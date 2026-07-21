/**
 * WooCommerce CyberSource 3D Secure handler.
 */
jQuery( document ).ready( ( $ ) => {

	'use strict';

	/**
	 * CyberSource Credit Card 3D Secure handler.
	 *
	 * @since 2.3.0
	 */
	window.WC_Cybersource_ThreeD_Secure_Handler = class WC_Cybersource_ThreeD_Secure_Handler {


		/**
		 * Instantiates 3D Secure handler.
		 *
		 * @since 2.3.0
		 *
		 * @param {Object} args
		 */
		constructor( args ) {

			this.order_id                = args.order_id;
			this.ajax_url                = args.ajax_url;
			this.logging_enabled         = args.logging_enabled;
			this.setup_action            = args.setup_action;
			this.setup_nonce             = args.setup_nonce;
			this.check_enrollment_action = args.check_enrollment_action;
			this.check_enrollment_nonce  = args.check_enrollment_nonce;
			this.enabled_card_types      = args.enabled_card_types;
			this.enabled_card_type_names = args.enabled_card_type_names;
			this.i18n                    = args.i18n;
			this.has_validated           = false;

			// intercept the form submission, trigger 3DSecure processing if necessary
			$( document.body ).on( 'wc_cybersource_flex_form_submitted', ( event, data ) => {

				this.handler = data.payment_form;

				// assume new card
				let cardType         = this.handler.card_type;
				let enabledCardTypes = this.enabled_card_types;

				// tokenized payment method handling
				if ( this.handler.saved_payment_method_selected ) {
					cardType         = this.handler.form.find( 'input#wc-cybersource-credit-card-payment-token-' + this.handler.saved_payment_method_selected ).data( 'card-type' );
					enabledCardTypes = this.enabled_card_type_names;
				}

				// if the card type is not enabled for 3D Secure, bail for regular processing
				if ( ! enabledCardTypes.includes( cardType ) ) {
					return true;
				}

				// if already validated, proceed with form submission
				if ( this.has_validated ) {
					return true;
				}

				this.token = this.handler.jti;

				if ( this.handler.saved_payment_method_selected ) {
					this.setup( this.handler.saved_payment_method_selected, false );
				} else {
					this.setup( this.token, true );
				}

				return false;

			} );
		}


		/**
		 * Start Device Data Collection
		 *
		 * @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-ddc-intro.html
		 *
		 * @param collection_url
		 * @param access_token
		 */
		collect_device_information(collection_url, access_token) {

			const parsedUrl = new URL( collection_url )

			this.log( 'Collecting Device Data' )

			const handleDeviceDataCollection = (result = null) => {

				window.removeEventListener( 'message', collectionListener )

				if (timeout) {
					clearTimeout( timeout )
				}

				// note: we don't need to do anything with the result, it's just for logging purposes
				if (result) {
					this.log( result )
				}

				this.check_enrollment()
			}

			const collectionListener = (event) => {
				if (event.origin === parsedUrl.origin && event.source === iframeWindow) {
					this.log( 'Device Data collection complete' )
					handleDeviceDataCollection( event.data )
				}
			}

			window.addEventListener(
				'message',
				collectionListener,
				false
			)

			let $deviceDataCollectionComponents = $( '#cardinal_device_data_collection' )

			// Insert the iframe and form if not already present
			if ( ! $deviceDataCollectionComponents.length ) {
				$deviceDataCollectionComponents = $(`
					<div id="cardinal_device_data_collection">
						<iframe id="cardinal_collection_iframe" name="collectionIframe" height="10" width="10" style="display:none;"></iframe>
						<form id="cardinal_collection_form" method="POST" target="collectionIframe" action="">
							<input id="cardinal_collection_form_input" type="hidden" name="JWT" />
						</form>
					</div>
				`)
				$( document.body ).append( $deviceDataCollectionComponents )
			}

			$deviceDataCollectionComponents.find( '#cardinal_collection_form_input' ).val( access_token )
			$deviceDataCollectionComponents.find( '#cardinal_collection_form' ).prop( 'action', collection_url ).submit()

			const iframeWindow = $deviceDataCollectionComponents.find( '#cardinal_collection_iframe' )[0].contentWindow;

			// time out DDC after 10 seconds
			const timeout = setTimeout( () => {
				this.log( 'Device Data collection timed out' )
				handleDeviceDataCollection();
			}, 10000 )
		}


		/**
		 * Sets up the 3D Secure JS.
		 *
		 * @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-setup-intro.html
		 *
		 * @since 2.3.0
		 *
		 * @param {String} token
		 * @param {Boolean} isTransient
		 */
		setup( token, isTransient ) {

			this.log( 'Setting up Payer Authentication' )

			$.post( this.ajax_url, {
				order_id:     this.order_id,
				action:       this.setup_action,
				nonce:        this.setup_nonce,
				token:        token,
				is_transient: isTransient
			}, ( response ) => {

				if ( response.success && response.data && response.data.jwt ) {

					this.log( 'Payer Authentication setup complete' )

					this.reference_id = response.data.reference_id;

					this.collect_device_information( response.data.device_data_collection_url, response.data.jwt )

				} else {

					this.log( 'JWT is missing', 'error' );

					this.handler.render_errors( [ this.i18n.error_general ] );
				}

			} ).fail( ( response ) => {

				this.handle_ajax_error( response );

			} );
		}


		/**
		 * Checks the given token for 3D Secure enrollment.
		 *
		 * @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-enroll-intro.html
		 *
		 * @since 2.3.0
		 */
		check_enrollment() {

			this.log( 'Checking enrollment' )

			const token       = this.handler.saved_payment_method_selected || this.token;
			const isTransient = ! this.handler.saved_payment_method_selected;

			$.post( this.ajax_url, {
				order_id:         this.order_id,
				reference_id:     this.reference_id,
				action:           this.check_enrollment_action,
				nonce:            this.check_enrollment_nonce,
				token:            token,
				expiration_month: this.handler.card_expiration_month,
				expiration_year:  this.handler.card_expiration_year,
				is_transient:     isTransient,
				device_data:      this.collect_backup_device_data(),
			}, ( response ) => {

				if ( response.success && response.data && response.data.consumerAuthenticationInformation ) {

					const consumerAuthenticationInformation = response.data.consumerAuthenticationInformation

					// @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-stepup-frame-intro.html
					if ( consumerAuthenticationInformation.stepUpUrl ) {

						this.log( 'Step-up URL provided, present challenge' );

						this.present_challenge( consumerAuthenticationInformation )

					} else {

						this.log( 'No Step-Up URL, submitting form' );

						this.submit()
					}

				} else {

					this.log( response );

					const errors = response.data.length ? response.data.map( error => error.message ) : [ this.i18n.error_general ]

					this.handler.render_errors( errors );
				}

			} ).fail( ( response ) => {

				this.handle_ajax_error( response );

			} );
		}


		/**
		 * Presents the 3DS challenge.
		 *
		 * @since 2.8.0
		 *
		 * @param {Object} consumerAuthenticationInformation
		 */
		present_challenge( consumerAuthenticationInformation ) {

			this.log( 'Presenting 3DS challenge' )

			// @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-stepup-frame-intro/pa2-ccdc-stepup-frame-building-iframe-parameters.html
			const pareq      = JSON.parse( atob( consumerAuthenticationInformation.pareq ) );
			const stepUpUrl  = consumerAuthenticationInformation.stepUpUrl;
			const token      = consumerAuthenticationInformation.accessToken;
			const dimensions = this.get_challenge_window_dimensions( pareq.challengeWindowSize || '05' );

			const stepUpMessageListener = (event) => {
				// ensure origin and source are correct
				if (event.origin === window.location.origin && event.source === iframeWindow) {

					this.log( 'Step-up challenge complete' )

					$( document.body ).css( "overflow", "auto" );

					console.log( event.data )

					challengeWindowComponents.hide() // hide the modal as soon as we have a response

					this.submit()
				}
			}

			window.addEventListener(
				'message',
				stepUpMessageListener,
				false
			)

			let challengeWindowComponents = $( '#wc-cybersource-3ds-challenge' )

			// Insert the iframe and form if not already present
			if ( ! challengeWindowComponents.length ) {
				challengeWindowComponents = $(`
					<div id="wc-cybersource-3ds-challenge">
						<div class="modal">
							<iframe name="step-up-iframe" id="step-up-iframe"></iframe>
						</div>
						<form id="step-up-form" target="step-up-iframe" method="post">
							<input type="hidden" name="JWT" id="step-up-token" />
						</form>
					</div>
				`)
				$( document.body ).append( challengeWindowComponents )
			}

			$( document.body ).css( "overflow", "hidden" );

			challengeWindowComponents.find( '#step-up-iframe' )
				.width( dimensions.width )
				.height( dimensions.height )

			challengeWindowComponents.find( '#step-up-token' ).val( token )

			challengeWindowComponents.find( '#step-up-form' )
				.prop( 'action', stepUpUrl )
				.submit()

			challengeWindowComponents.show() // ensure modal is visible

			const iframeWindow = challengeWindowComponents.find( '#step-up-iframe' )[0].contentWindow;
		}

		/**
		 * Submits the form for payment authorization.
		 *
		 * This is where 3DSecure is completed, the backend still needs to pass the payment authorization data to CyberSource.
		 *
		 * @since 2.8.0
		 */
		submit() {

			this.log( 'Payer Authentication complete, submitting form for payment authorization' )

			this.has_validated = true;

			this.handler.form.submit();
		}


		/**
		 * Handles an AJAX error response.
		 *
		 * @since 2.3.0
		 *
		 * @param {Object} response
		 */
		handle_ajax_error( response ) {

			this.log( response.responseJSON.data ? response.responseJSON.data : 'Unknown error', 'error' );

			this.handler.render_errors( [ this.i18n.error_general ] );
		}


		/**
		 * Logs a message if enabled.
		 *
		 * @since 2.3.0
		 *
		 * @param message
		 * @param type
		 */
		log( message, type = '' ) {

			if ( ! this.logging_enabled ) {
				return;
			}

			if ( 'error' === type ) {
				console.error( message );
			} else {
				console.log( message );
			}
		}


		/**
		 * Gets backup details for device data collection.
		 *
		 * @ince 2.8.0
		 *
		 * @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-enroll-intro.html
		 */
		collect_backup_device_data() {
			return {
				httpBrowserColorDepth: window.screen.colorDepth,
				httpBrowserJavaEnabled: window.navigator.javaEnabled(),
				httpBrowserJavaScriptEnabled: true, // If this script is running, JS is enabled
				httpBrowserLanguage: window.navigator.language,
				httpBrowserScreenHeight: window.screen.height,
				httpBrowserScreenWidth: window.screen.width,
				httpBrowserTimeDifference: new Date().getTimezoneOffset()
			};
		}


		/**
		 * Returns the challenge window dimensions.
		 *
		 * @since 2.8.0
		 *
		 * @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-stepup-frame-intro/pa2-ccdc-stepup-frame-building-iframe-parameters.html
		 *
		 * @param {String} windowSize
		 */
		get_challenge_window_dimensions(windowSize) {
			return {
				'01': { width: 250, height: 400 },
				'02': { width: 390, height: 400 },
				'03': { width: 500, height: 600 },
				'04': { width: 600, height: 400 },
				'05': { width: 1000, height: 600 }
			}[windowSize]
		}


	}

} );
