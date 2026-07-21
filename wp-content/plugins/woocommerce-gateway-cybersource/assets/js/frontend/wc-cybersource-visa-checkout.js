/**
 * WooCommerce Visa Checkout handler.
 */
jQuery( ( $ ) => {

	"use strict"

	window.WC_Cybersource_Visa_Checkout_Payment_Form_Handler = class WC_Cybersource_Visa_Checkout_Payment_Form_Handler extends SV_WC_Payment_Form_Handler_v5_15_11 {


		/**
		 * Constructor
		 *
		 * @since 2.3.0
		 *
		 * @param {Array} args JS handler arguments
		 */
		constructor( args ) {

			super( args );

			this.ready = false;

			this.api_key  = args.api_key;
			this.settings = args.settings;
			this.i18n     = args.i18n;

			this.init();
		}


		/**
		 * Sets up the JS handler.
		 *
		 * This method is called when the handler is instantiated.
		 *
		 * It returns early if the Visa Checkout JS SDK is not available and it's called
		 * again when onVisaCheckoutReady() is executed.
		 *
		 * @see onVisaCheckoutReady()
		 *
		 * @since 2.3.0
		 */
		init() {

			if ( this.ready ) {
				return;
			}

			if ( 'undefined' === typeof window.V || 'undefined' === typeof window.V.init ) {
				return;
			}

			if ( 0 === $(  `#wc-${this.id_dasherized}-container .v-button` ).length ) {
				return;
			}

			this.ready = true;

			this.add_event_handlers();
			this.set_payment_fields();
		}


		/**
		 * Sets references to the button and container elements.
		 *
		 * Also initializes the Visa Checkout JS integration..
		 *
		 * This method is called when the handler is ready, on updated_checkout, and when the Pay page loads.
		 *
		 * @since 2.3.0
		 */
		set_payment_fields() {

			super.set_payment_fields();

			if ( ! this.ready ) {
				return;
			}

			this.$container = $( `#wc-${this.id_dasherized}-container` );
			this.$button    = this.$container.find( '.v-button' );

			this.show_button();
			this.init_visa_checkout();
			this.toggle_order_button();
		}


		/**
		 * Verifies that a payment was authorized on Visa Checkout before submitting the form.
		 *
		 * @since 2.3.0
		 */
		validate_payment_data() {

			if ( 0 === this.get_payment_response().length ) {

				this.render_errors( [ this.i18n.missing_payment_response_message ] );

				return false;
			}

			return true;
		}


		/**
		 * Gets the Visa Checkout payment response stored in a hidden field.
		 *
		 * @since 2.3.0
		 *
		 * @return {string}
		 */
		get_payment_response() {

			return $( `#wc-${this.id_dasherized}-payment-response` ).val();
		}


		/**
		 * Initializes Visa Checkout JS integration.
		 *
		 * @since 2.3.0
		 */
		init_visa_checkout() {

			// remove previous errors
			this.$container.find( `.wc-${this.id_dasherized}-error` ).remove();

			try {

				V.init( {
					apikey:         this.api_key,
					settings:       this.settings,
					paymentRequest: this.get_payment_request(),
				} );

			} catch ( e ) {

				this.$container.before( `<p class="wc-${this.id_dasherized}-error">${this.i18n.initialization_error_message}</p>` );

				this.hide_button();

				console.error( '[Visa Checkout] An error occurred trying to initialize Visa Checkout' );
				console.error( e );
			}
		}


		/**
		 * Gets the Visa Checkout payment request stored in a hidden field.
		 *
		 * The payment request is updated every time the payment fields are refreshed.
		 *
		 * @since 2.3.0
		 *
		 * @throws Error
		 */
		get_payment_request() {

			let request = $( `#wc-${this.id_dasherized}-payment-request` ).val();

			if ( ! request ) {
				throw new Error( 'Visa Checkout payment request is empty.' );
			}

			try {
				return $.parseJSON( request );
			} catch ( e ) {
				throw e;
			}
		}


		/**
		 * Sets up event handlers for various Visa Checkout and browser events.
		 *
		 * @since 2.3.0
		 */
		add_event_handlers() {

			$( document.body ).on( 'click', 'input[name="payment_method"]', this.toggle_order_button.bind( this ) );
			$( document.body ).on( 'payment_method_selected', this.toggle_order_button.bind( this ) );

			this.form.on( 'click', '.v-button', this.block_ui.bind( this ) );

			V.on( 'payment.success', this.on_payment_success.bind( this ) );
			V.on( 'payment.cancel', this.on_payment_cancel.bind( this ) );
			V.on( 'payment.error', this.on_payment_error.bind( this ) );
		}


		/**
		 * Shows the Visa Checkout button.
		 *
		 * @since 2.3.0
		 */
		show_button() {

			this.$container.show();
		}


		/**
		 * Hides the Visa Checkout button.
		 *
		 * @since 2.3.0
		 */
		hide_button() {

			this.$container.hide();
		}


		/*
		 * Toggles the "Place Order" button based on whether Visa Checkout is selected.
		 *
		 * If Visa Checkout is the selected payment method, customers can't place an order
		 * until they've clicked the Visa Checkout button and authorized the payment.
		 *
		 * We hide the Place Order button in this case because clicking it would
		 * always render an error.
		 *
		 * We use a CSS class instead of hide() and show() because Braintree (PayPal) tries
		 * to show/hide the button based on whether PayPal is the selected method too, causing
		 * the Place Order button to be always visible.
		 *
		 * @since 2.3.0
		 */
		toggle_order_button() {

			if ( this.is_selected() ) {
				$( '#place_order' ).addClass( 'wc-visa-checkout-hidden-place-order-button' );
			} else {
				$( '#place_order' ).removeClass( 'wc-visa-checkout-hidden-place-order-button' );
			}
		}


		/**
		 * Determines whether Visa Checkout is the selected payment method.
		 *
		 * @since 2.3.0
		 *
		 * @return {boolean}
		 */
		is_selected() {

			return this.id === this.get_selected_gateway_id();
		}


		/**
		 * Gets the ID of the currently selected payment method.
		 *
		 * @since 2.3.0
		 *
		 * @return {string}
		 */
		get_selected_gateway_id() {

			return this.form.find( 'input[name="payment_method"]:checked' ).val();
		}


		/**
		 * Handles a successful payment authorization.
		 *
		 * @since 2.3.0
		 *
		 * @param {Object} payment payment data returned by Visa Checkout
		 */
		on_payment_success( payment ) {

			// we don't need to send the Visa Checkout configuration used to initialize the integration
			if ( payment.vInitRequest ) {
				delete payment.vInitRequest;
			}

			$( `#wc-${this.id_dasherized}-payment-response` ).val( JSON.stringify( payment ) );

			this.form.submit();
		}


		/**
		 * Handles the case in which the customer decided to cancel the payment.
		 *
		 * @since 2.3.0
		 *
		 * @param {Object} request cancelled payment response returned by Visa Checkout.
		 */
		on_payment_cancel( request ) {

			this.unblock_ui();

			console.log( '[Visa Checkout] Payment cancelled.', request );
		}


		/**
		 * Handles the case in which an error occurs while trying to setup or use Visa Checkout.
		 *
		 * @since 2.3.0
		 *
		 * @param {Object} payment
		 * @param {Object} error
		 */
		on_payment_error( payment, error ) {

			this.render_errors( [ this.params.visa_checkout_general_error ] );

			console.error( '[Visa Checkout] ' + error.code + ': ' + error.message );
		}


	}

	window.onVisaCheckoutReady = function() {

		if ( 'undefined' !== typeof window.wc_cybersource_credit_card_visa_checkout_handler ) {
			window.wc_cybersource_credit_card_visa_checkout_handler.init();
		}
	}

	// dispatch loaded event
	$( document.body ).trigger( "wc_cybersource_visa_checkout_payment_form_handler_loaded" );

} );
