<?php

namespace WcPaysafe\Gateways\Redirect\Payments\Google_Pay;

use WcPaysafe\Helpers\Factories;
use WcPaysafe\Paysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Google_Pay_Button {
	
	public function hooks() {
		add_action( 'woocommerce_checkout_before_customer_details', [
			$this,
			'display_payment_request_button_html',
		], 1 );
		
		add_action( 'woocommerce_pay_order_before_payment', [
			$this,
			'display_payment_request_button_html',
		], 1 );
	}
	
	public function display_payment_request_button_html() {
		/**
		 * @var \WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway $gateway
		 */
		$gateway = Factories::get_gateway( 'paysafe_checkout_payments' );
		
		if ( 'yes' !== $gateway->enabled ) {
			return;
		}
		
		if ( ! in_array( 'googlePay', $gateway->get_available_payment_methods() ) ) {
			return;
		}
		
		// We don't support subscription in Google Pay yet
		if ( class_exists( 'WC_Subscriptions_Cart' ) && \WC_Subscriptions_Cart::cart_contains_subscription() ) {
			return;
		}
		
		if ( apply_filters( 'wc_paysafe_payments_hide_google_pay_button', false ) ) {
			return;
		}
		
		wc_get_template(
			'paysafe/checkoutjs/google-pay/button.php',
			array(
				'gateway' => $gateway,
			),
			'',
			Paysafe::plugin_path() . '/templates/'
		);
	}
}