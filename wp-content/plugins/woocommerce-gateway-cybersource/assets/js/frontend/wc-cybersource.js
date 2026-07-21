/**
 * WooCommerce CyberSource Flex Microform handler.
 */
jQuery( document ).ready( ( $ ) => {

	'use strict';

	// the base non-flex form handler for eChecks or credit cards with Flex disabled
	window.WC_Cybersource_Payment_Form_Handler = window.SV_WC_Payment_Form_Handler_v5_15_11;

	// dispatch loaded event
	$( document.body ).trigger( 'wc_cybersource_payment_form_handler_loaded' );

	/**
	 * CyberSource Credit Card Flex Microform Handler class
	 *
	 * Loads CyberSource Flex Microform in an iframe and intercepts the form submission to inject the token returned by CyberSource.
	 *
	 * @since 2.0.0-dev-2
	 */
	window.WC_Cybersource_Flex_Payment_Form_Handler = class WC_Cybersource_Flex_Payment_Form_Handler extends SV_WC_Payment_Form_Handler_v5_15_11 {

		/**
		 * Instantiates Payment Form Handler.
		 *
		 * @since 2.0.0
		 */
		constructor( args ) {

			super( args );

			this.general_error      = args.general_error;
			this.capture_context    = args.capture_context;
			this.number_placeholder = args.number_placeholder;
			this.csc_placeholder    = args.csc_placeholder;
			this.styles             = args.styles;

			this.init_cybersource_microform();

			$( document.body ).on( 'sv_wc_payment_form_valid_payment_data', ( event, data ) => {

				if ( data.payment_form.id !== this.id ) {
					return data.passed_validation;
				}

				// if regular validation passed
				if ( data.passed_validation ) {

					// if a saved method is selected, we have a token
					if ( this.saved_payment_method_selected || this.get_flex_token() ) {

						let token_input = this.form.find( 'input#wc-cybersource-credit-card-payment-token-' + this.saved_payment_method_selected )

						// these values are accessed by 3DS handler later on
						if (token_input.length) {
							this.card_expiration_month = token_input.data( 'card-expiration-month' ).toString();
							this.card_expiration_year  = token_input.data( 'card-expiration-year' ).toString();
						}

						return $( document.body ).triggerHandler( 'wc_cybersource_flex_form_submitted', { payment_form: data.payment_form } ) !== false;
					}

					// block the UI
					this.form.block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );

					// generate a flex token
					this.create_token();

					// always return false to resubmit the form
					return false;
				}

			} );

			$( document.body ).on( 'checkout_error', () => {
				this.clear_flex_data();
			} );
		}


		/**
		 * Sets the form payment fields.
		 *
		 * Initializes the Flex microform.
		 *
		 * @since 2.0.0
		 */
		set_payment_fields() {

			super.set_payment_fields();

			delete this.initializing_microform_instance;

			if ( this.capture_context ) {
				this.init_cybersource_microform();
			}
		}


		/**
		 * Initializes CyberSource Flex Microform.
		 *
		 * @since 2.0.0
		 */
		init_cybersource_microform() {

			// avoid calling init_cybersource_microform() again before the request to initialize the microform instance finishes
			if ( this.initializing_microform_instance ) {
				return;
			}

			// bail if the hosted credit card form field is already part of the page
			if ( this.microform_instance && this.microform_instance.iframe && $( `#${this.microform_instance.iframe.id}` ).length ) {
				return;
			}

			// bail if the WooCommerce payment form is rendered
			if ( ! $( '#wc-cybersource-credit-card-account-number-hosted' ).length > 0 ) {
				return
			}

			this.initializing_microform_instance = true;

			this.clear_flex_data();

			let flex = new Flex( this.capture_context );

			this.microform_instance = flex.microform( 'card', { styles: this.styles } );

			delete this.initializing_microform_instance;

			let numberField = this.microform_instance.createField( 'number', { placeholder: this.number_placeholder } ),
				cscField       = this.microform_instance.createField( 'securityCode', { placeholder: this.csc_placeholder } );

			// handle card type changes
			numberField.on( 'change', function( data ) {

				let $card_number_field = $( '#wc-cybersource-credit-card-account-number-hosted' );

				// clear any previous card type classes
				$card_number_field.attr( 'class', ( i, c ) => {
					return c.replace( /(^|\s)card-type-\S+/g, '' );
				} );

				// set the card type class for display
				if ( data && data.card && data.card[0] && data.card[0].name ) {
					$card_number_field.addClass( 'card-type-' + data.card[0].name );
				}

			} );

			numberField.load( '#wc-cybersource-credit-card-account-number-hosted' );

			if ( $( '#wc-cybersource-credit-card-csc-hosted' ).length > 0 ) {
				cscField.load( '#wc-cybersource-credit-card-csc-hosted' );
			}
		}


		/**
		 * Validates remaining credit card data (expiry and optionally csc).
		 *
		 * @since 2.0.0
		 */
		validate_card_data() {

			let errors = [];

			// validate CC fields if necessary
			if ( ! this.saved_payment_method_selected ) {

				// validate expiration date
				const expiry = $.payment.cardExpiryVal( this.payment_fields.find( '.js-sv-wc-payment-gateway-credit-card-form-expiry' ).val() );

				// validates future date
				if ( ! $.payment.validateCardExpiry( expiry ) ) {
					errors.push( this.params.card_exp_date_invalid );
				}
			}

			if ( errors.length > 0 ) {
				this.render_errors( errors );
				return false;
			}

			return true;
		}


		/**
		 * Creates a new flex token.
		 *
		 * @since 2.0.0
		 */
		create_token() {

			const expiry = $.payment.cardExpiryVal( this.payment_fields.find( '.js-sv-wc-payment-gateway-credit-card-form-expiry' ).val() );

			let expiration_month = String( expiry.month );
			let expiration_year  = String( expiry.year );

			// Cybersource requires a leading zero
			if ( expiration_month.length === 1 ) {
				expiration_month = '0' + expiration_month;
			}

			const options = {
				expirationMonth: expiration_month,
				expirationYear:  expiration_year,
			};

			this.microform_instance.createToken( options, ( error, token ) => {

				if ( error ) {

					this.handle_token_errors( error );

				} else {

					let payload = JSON.parse( atob( token.split( '.' )[1] ) );
					let card    = payload.content.paymentInformation.card

					// these values are accessed by the 3DS handler later on
					if (card) {
						this.card_type             = card.number && card.number.detectedCardTypes && card.number.detectedCardTypes.length ? card.number.detectedCardTypes[0] : null
						this.card_bin              = card.number && card.number.bin ? card.number.bin : null
						this.card_expiration_month = card.expirationMonth ? card.expirationMonth.value : null
						this.card_expiration_year  = card.expirationYear ? card.expirationYear.value : null
					}

					if ( payload.jti ) {
						this.jti = payload.jti;
					}

					// at this point the token & key may be added to the form as hidden fields
					$( '[name=wc-cybersource-credit-card-flex-key]' ).val( this.capture_context );
					$( '[name=wc-cybersource-credit-card-flex-token]' ).val( token );

					this.form.submit();
				}

			} );
		}


		/**
		 * Handles flex tokenization errors.
		 *
		 * @since 2.0.0
		 *
		 * @param error
		 */
		handle_token_errors( error ) {

			let errors = [];

			console.log( error );

			if ( error.details && error.details.length > 0 && error.reason === 'CREATE_TOKEN_VALIDATION_FIELDS' ) {

				error.details.forEach( ( detail, index ) => {

					let message = '';

					switch ( detail.location ) {

						case 'number':
							message = this.params.card_number_invalid;
						break;

						case 'securityCode':
							message = this.params.cvv_length_invalid;
						break;
					}

					if ( message.length ) {
						errors.push( message );
					}

				} );

			} else {

				errors.push( this.general_error );
			}

			this.render_errors( errors );
		}


		/**
		 * Renders errors.
		 *
		 * @since 2.0.0
		 *
		 * @param errors
		 */
		render_errors( errors ) {

			this.clear_flex_data();

			super.render_errors( errors );
		}


		/**
		 * Gets the flex token if set in the form.
		 *
		 * @since 2.0.0
		 *
		 * @returns string
		 */
		get_flex_token() {

			return this.payment_fields.find( 'input[name=wc-cybersource-credit-card-flex-token]' ).val();
		}


		/**
		 * Clears any form flex data.
		 *
		 * @since 2.0.0
		 */
		clear_flex_data() {

			this.jti = null;

			$( '[name=wc-cybersource-credit-card-flex-key]' ).val( '' );
			$( '[name=wc-cybersource-credit-card-flex-token]' ).val( '' );
		}


	};


	// dispatch loaded event
	$( document.body ).trigger( 'wc_cybersource_flex_payment_form_handler_loaded' );

} );
