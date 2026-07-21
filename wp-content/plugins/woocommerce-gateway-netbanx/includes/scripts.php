<?php

namespace WcPaysafe;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Helpers\Formatting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles script loading
 *
 * @since  3.2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Scripts {
	
	public $suffix;
	public $version;
	
	public function __construct() {
		$this->suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->version = $this->suffix ? WC_PAYSAFE_PLUGIN_VERSION : rand( 1, 999 );
	}
	
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
	}
	
	/**
	 * Adds admin scripts
	 *
	 * @since 3.2.0
	 */
	public function admin_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		
		wp_register_script( 'paysafe-admin', Paysafe::plugin_url() . '/assets/js/admin' . $this->suffix . '.js', array( 'jquery' ), $this->version, true );
		
		if ( in_array( str_replace( 'edit-', '', $screen_id ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
			wp_enqueue_script( 'paysafe-admin' );
		}
		
		wp_localize_script( 'paysafe-admin', 'wc_paysafe_params', array(
			'i18n_capture_payment'      => _x( 'Are you sure you want to capture the payment?', 'capture payment', 'wc_paysafe' ),
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'capture_payment'           => wp_create_nonce( 'capture-payment' ),
			'il8n_integration_changed'  => __( '<span>Integration Type changed!</span> <span>Save the change before you continue.</span >', 'wc_paysafe' ),
			'il8n_dd_refund_action'     => __( 'To refund a Direct Debit purchase we will issue the customer a Standalone Credit against their Vault payment method. Are you sure you want to proceed?', 'wc_paysafe' ),
			'il8n_confirm_pair_removal' => __( 'Are you sure you want to remove the account pair?', 'wc_paysafe' ),
		) );
	}
	
	public function frontend_scripts() {
		$this->checkoutjs_scripts();
		$this->checkoutjs_v2_scripts();
		$this->checkoutjs_google_pay_scripts();
	}
	
	public function checkoutjs_scripts() {
		if ( ! is_cart()
		     && ! is_checkout()
		     && ! isset( $_GET['pay_for_order'] )
		     && ! is_add_payment_method_page()
		     && ! wc_paysafe_is_update_payment_method_page()
		     && ! isset( $_GET['change_payment_method'] )
		) {
			return;
		}
		
		$class = wc_paysafe_instance()->get_gateway_class( 'hosted' );
		/**
		 * @var Gateway $gateway
		 */
		$gateway = new $class();
		
		if ( 'no' === $gateway->enabled ) {
			return;
		}
		
		// Make sure the gateway will be showing up on the checkout page
		if ( ! $gateway->is_available() ) {
			wc_paysafe_add_debug_log( 'Gateway is not setup correctly.', Formatting::get_log_id( $gateway->id ) );
			
			return;
		}
		
		// Check the integration, since not all integrations need scripts
		if ( 'checkoutjs' != $gateway->integration ) {
			return;
		}
		
		wp_enqueue_script( 'paysafe-checkout-js', Paysafe::plugin_url() . '/assets/js/paysafe/paysafe.checkout.min.js', array( 'jquery' ), $this->version, true );
		
		wp_enqueue_script( 'paysafe-checkout', Paysafe::plugin_url() . '/assets/js/paysafe-checkout' . $this->suffix . '.js', array(
			'jquery',
			'paysafe-checkout-js',
		), $this->version, true );
		
		$checkout_data = apply_filters( 'wc_paysafe_checkout_js_variables', array(
			'ajaxUrl'                   => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'gatewayId'                 => $gateway->id,
			'isWc3_0'                   => WC_Compatibility::is_wc_3_0(),
			'scriptDev'                 => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
			'isAddPaymentMethodPage'    => is_add_payment_method_page(),
			'isUpdatePaymentMethodPage' => wc_paysafe_is_update_payment_method_page(),
			'isPayForOrderPage'         => wc_paysafe_is_pay_for_order_page(),
			'isCheckoutPayPage'         => wc_paysafe_is_checkout_pay_page(),
			'isCheckoutPage'            => is_checkout() && ! is_checkout_pay_page(),
			'isChangePaymentMethodPage' => wc_paysafe_is_change_method_page(),
			'isCvvRequiredField'        => $gateway->is_cvv_required() ? 1 : 0,
			'nonce'                     => array(),
			'il8n'                      => array(
				'cardNumberNotValid'  => __( 'Card Number is invalid', 'wc_paysafe' ),
				'cardExpiryNotValid'  => __( 'Expiry date is invalid', 'wc_paysafe' ),
				'cardCvcNotValid'     => __( 'Card Code is invalid', 'wc_paysafe' ),
				'publicKeyLoadFailed' => __( 'Failed to load the public key', 'wc_paysafe' ),
				'publicKeyNotLoaded'  => __( 'No encryption public key was provided', 'wc_paysafe' ),
				'missingCVV'          => __( 'Missing CVV field', 'wc_paysafe' ),
				'emptyCVV'            => __( 'The CVV field is required, please fill it in and try again.', 'wc_paysafe' ),
				'invalidCVV'          => __( 'The CVV field not the correct length. Please correct it and try again.', 'wc_paysafe' ),
			),
			'publicKey'                 => base64_encode( $gateway->get_option( 'single_use_token_user_name' ) . ':' . $gateway->get_option( 'single_use_token_password' ) ),
			'errors'                    => array(
				'9012' => esc_js( __( 'Invalid setup. The supplied number of arguments is neither 3 nor 4.', 'wc_paysafe' ) ),
				'9062' => esc_js( __( 'Invalid setup. Setup function has been invoked and Paysafe Checkout is already opened or is loading at the moment.', 'wc_paysafe' ) ),
				'9013' => esc_js( __( 'The supplied apiKey parameter is not a string, is in invalid format or is not configured for Paysafe Checkout', 'wc_paysafe' ) ),
			),
		) );
		
		// Add the nonces only for the page they are going to be used
		if ( wc_paysafe_is_pay_for_order_page() ) {
			$checkout_data['nonce']['process_payment'] = wp_create_nonce( 'wc-paysafe-process-payment' );
		}
		if ( wc_paysafe_is_update_payment_method_page() ) {
			$checkout_data['nonce']['update_payment_method'] = wp_create_nonce( 'wc-paysafe-update-payment-method' );
		}
		if ( is_add_payment_method_page() ) {
			$checkout_data['nonce']['add_payment_method'] = wp_create_nonce( 'wc-paysafe-add-payment-method' );
		}
		if ( wc_paysafe_is_change_method_page() ) {
			$checkout_data['nonce']['change_payment_method'] = wp_create_nonce( 'wc-paysafe-change-payment-method' );
		}
		
		wp_localize_script( 'paysafe-checkout', 'paysafe_checkoutjs_params', $checkout_data );
	}
	
	public function checkoutjs_v2_scripts() {
		if ( ! is_cart()
		     && ! is_checkout()
		     && ! isset( $_GET['pay_for_order'] )
		     && ! is_add_payment_method_page()
		     && ! wc_paysafe_is_update_payment_method_page()
		     && ! isset( $_GET['change_payment_method'] )
		) {
			return;
		}
		
		$class = wc_paysafe_instance()->get_gateway_class( 'checkout_payments' );
		/**
		 * @var \WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway $gateway
		 */
		$gateway = new $class();
		
		if ( 'no' === $gateway->enabled ) {
			return;
		}
		
		// Make sure the gateway will be showing up on the checkout page
		if ( ! $gateway->is_available() ) {
			wc_paysafe_add_debug_log( 'Gateway is not setup correctly.', Formatting::get_log_id( $gateway->id ) );
			
			return;
		}
		
		wp_enqueue_script( 'paysafe-checkout-js-v2', 'https://hosted.paysafe.com/checkout/v2/paysafe.checkout.min.js', array( 'jquery' ), $this->version, true );
		
		wp_enqueue_script( 'paysafe-checkout-v2', Paysafe::plugin_url() . '/assets/js/paysafe-checkout-payments' . $this->suffix . '.js', array(
			'jquery',
			'paysafe-checkout-js-v2',
		), $this->version, true );
		
		$checkout_data = apply_filters( 'wc_paysafe_checkout_js_v2_variables', array(
			'ajaxUrl'                   => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'gatewayId'                 => $gateway->id,
			'isWc3_0'                   => WC_Compatibility::is_wc_3_0(),
			'scriptDev'                 => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
			'isAddPaymentMethodPage'    => is_add_payment_method_page(),
			'isUpdatePaymentMethodPage' => wc_paysafe_is_update_payment_method_page(),
			'isPayForOrderPage'         => wc_paysafe_is_pay_for_order_page(),
			'isCheckoutPayPage'         => wc_paysafe_is_checkout_pay_page(),
			'isCheckoutPage'            => is_checkout() && ! is_checkout_pay_page(),
			'isChangePaymentMethodPage' => wc_paysafe_is_change_method_page(),
			'isCvvRequiredField'        => $gateway->is_cvv_required() ? 1 : 0,
			'nonce'                     => array(),
			'il8n'                      => array(
				'cardNumberNotValid'  => __( 'Card Number is invalid', 'wc_paysafe' ),
				'cardExpiryNotValid'  => __( 'Expiry date is invalid', 'wc_paysafe' ),
				'cardCvcNotValid'     => __( 'Card Code is invalid', 'wc_paysafe' ),
				'publicKeyLoadFailed' => __( 'Failed to load the public key', 'wc_paysafe' ),
				'publicKeyNotLoaded'  => __( 'No encryption public key was provided', 'wc_paysafe' ),
				'missingCVV'          => __( 'Missing CVV field', 'wc_paysafe' ),
				'emptyCVV'            => __( 'The CVV field is required, please fill it in and try again.', 'wc_paysafe' ),
				'invalidCVV'          => __( 'The CVV field not the correct length. Please correct it and try again.', 'wc_paysafe' ),
				'error'               => __( 'Error:', 'wc_paysafe' ),
				'correlation'         => __( 'Correlation:', 'wc_paysafe' ),
			),
			'publicKey'                 => base64_encode( $gateway->get_option( 'single_use_token_user_name' ) . ':' . $gateway->get_option( 'single_use_token_password' ) ),
			'errors'                    => array(
				'9012' => esc_js( __( 'Invalid setup. The supplied number of arguments is neither 3 nor 4.', 'wc_paysafe' ) ),
				'9062' => esc_js( __( 'Invalid setup. Setup function has been invoked and Paysafe Checkout is already opened or is loading at the moment.', 'wc_paysafe' ) ),
				'9013' => esc_js( __( 'The supplied apiKey parameter is not a string, is in invalid format or is not configured for Paysafe Checkout', 'wc_paysafe' ) ),
			),
		) );
		
		// Add the nonces only for the page they are going to be used
		if ( wc_paysafe_is_pay_for_order_page() ) {
			$checkout_data['nonce']['process_payment'] = wp_create_nonce( 'paysafe_checkout_process-payment' );
		}
		if ( wc_paysafe_is_update_payment_method_page() ) {
			$checkout_data['nonce']['update_payment_method'] = wp_create_nonce( 'paysafe_checkout_update-payment-method' );
		}
		if ( is_add_payment_method_page() ) {
			$checkout_data['nonce']['add_payment_method'] = wp_create_nonce( 'paysafe_checkout_add-payment-method' );
		}
		if ( wc_paysafe_is_change_method_page() ) {
			$checkout_data['nonce']['change_payment_method'] = wp_create_nonce( 'paysafe_checkout_change-payment-method' );
		}
		
		wp_localize_script( 'paysafe-checkout-v2', 'paysafe_checkoutjs_payments_params', $checkout_data );
	}
	
	public function checkoutjs_google_pay_scripts() {
		if ( ! is_cart()
		     && ! is_checkout()
		     && ! isset( $_GET['pay_for_order'] )
		     && ! is_add_payment_method_page()
		     && ! wc_paysafe_is_update_payment_method_page()
		     && ! isset( $_GET['change_payment_method'] )
		) {
			return;
		}
		
		/**
		 * @var \WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway $gateway
		 */
		$gateway = Factories::get_gateway( 'paysafe_checkout_payments' );
		
		if ( 'no' === $gateway->enabled ) {
			return;
		}
		
		wp_enqueue_script( 'paysafe-gpay-script', 'https://pay.google.com/gp/p/js/pay.js', array(), $this->version, true );
		
		wp_enqueue_script( 'paysafe-checkout-gpay', Paysafe::plugin_url() . '/assets/js/paysafe-google-pay' . $this->suffix . '.js', array(
			'jquery',
			'paysafe-gpay-script',
			'paysafe-checkout-v2',
		), $this->version, true );
		
		$checkout_data = apply_filters( 'wc_paysafe_google_pay_variables', array(
			'ajaxUrl'                => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'gatewayId'              => $gateway->id,
			'isWc3_0'                => WC_Compatibility::is_wc_3_0(),
			'isTestmode'             => 'yes' == $gateway->testmode,
			'scriptDev'              => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
			'currency'               => get_woocommerce_currency(),
			'currencySymbol'         => get_woocommerce_currency_symbol(),
			'merchantName'           => $gateway->get_option( 'google_pay_merchant_name' ),
			'merchantId'             => $gateway->get_option( 'google_pay_merchant_id' ),
			'gatewayMerchantId'      => $gateway->get_option( 'single_use_token_user_name' ),
			'countryCode'            => substr( get_option( 'woocommerce_default_country' ), 0, 2 ),
			'is_product_page'        => false,
			'allowedCardNetworks'    => [ "AMEX", "DISCOVER", "JCB", "MASTERCARD", "VISA" ],
			'allowedCardAuthMethods' => [ "PAN_ONLY" ],
			'baseApiVersion'         => [
				'apiVersion'      => 2,
				'apiVersionMinor' => 0,
			],
			'nonce'                  => array(
				'process_payment'        => wp_create_nonce( 'paysafe_checkout_process-payment' ),
				'update_shipping_method' => wp_create_nonce( 'paysafe_checkout_payments-update_shipping_method' ),
				'get_shipping_options'   => wp_create_nonce( 'paysafe_checkout_payments-get_shipping_options' ),
				'cart_details'           => wp_create_nonce( 'paysafe_checkout_payments-cart_details' ),
				'checkout'               => wp_create_nonce( 'woocommerce-process_checkout' ),
			),
			'il8n'                   => array(
				'shippingLabel' => __( 'Shipping', 'woocommerce' ),
				'error'         => __( 'Error:', 'wc_paysafe' ),
				'total'         => __( 'Total', 'wc_paysafe' ),
			),
			'errors'                 => array(),
		) );
		
		if ( 'specific' === get_option( 'woocommerce_ship_to_countries' ) ) {
			$raw_countries                        = get_option( 'woocommerce_specific_ship_to_countries', array() );
			$checkout_data['allowedCountryCodes'] = $raw_countries;
		}
		
		wp_localize_script( 'paysafe-checkout-gpay', 'paysafe_google_pay_params', $checkout_data );
	}
}