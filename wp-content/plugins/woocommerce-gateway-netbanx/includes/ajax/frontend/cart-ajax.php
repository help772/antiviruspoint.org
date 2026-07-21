<?php

namespace WcPaysafe\Ajax\Frontend;

use WcPaysafe\Ajax\Ajax_Abstract;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Checkout\Processes;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Cart_Checkout_Helpers;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Customer;
use WcPaysafe\Paysafe_Order;
use WcPaysafe\Tokens\Customer_Tokens;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Cart_Ajax extends Ajax_Abstract {
	
	public function hooks() {
		$map = array(
			'paysafe_payments_update_shipping_method' => [ $this, 'update_shipping_method' ],
			'paysafe_payments_get_shipping_options'   => [ $this, 'get_shipping_options' ],
			'paysafe_payments_get_cart_details'       => [ $this, 'get_cart_details' ],
			'paysafe_payments_create_order'           => [ $this, 'create_order' ],
			'paysafe_payments_pay_for_order'          => [ $this, 'pay_for_order' ],
		);
		
		foreach ( $map as $hook => $callback ) {
			add_action( 'wc_ajax_' . $hook, $callback );
		}
	}
	
	/**
	 * Process an order payment
	 */
	public function get_cart_details() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'paysafe_checkout_payments-cart_details' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		$from_cart = Paysafe::get_field( 'from_cart', $_POST, true );
		$order_id  = Paysafe::get_field( 'order_id', $_POST, 0 );
		
		if ( ! $from_cart ) {
			$order = wc_get_order( $order_id );
			$data  = [
				'shipping_required' => count( $order->get_shipping_methods() ),
				'order_data'        => [
					'currency'     => strtolower( $order->get_currency() ),
					'country_code' => substr( $order->get_billing_country(), 0, 2 ),
				],
			
			];
			
			$shipping_options = $order->get_shipping_methods();
			if ( $shipping_options ) {
				foreach ( $shipping_options as $shipping_option ) {
					$shipping_methods[]       = $shipping_option->get_method_id();
					$shipping_methods_names[] = $shipping_option->get_method_title();
				}
				
				$shipping_method       = implode( ', ', $shipping_methods );
				$shipping_methods_name = implode( ', ', $shipping_methods_names );
				
				$data['shippingOptions'] = [
					'defaultSelectedOptionId' => $shipping_method,
					'shippingOptions'         => [
						[
							"id"    => $shipping_method,
							"label" => get_woocommerce_currency_symbol( $order->get_currency() ) . $order->get_shipping_total() . ': ' . $shipping_methods_name,
						],
					],
				];
			}
			
			$data['order_data'] += Cart_Checkout_Helpers::build_order_display_items( $order );
		} else {
			WC()->cart->calculate_totals();
			
			$currency = get_woocommerce_currency();
			
			// Set mandatory payment details.
			$data = [
				'shipping_required' => WC()->cart->needs_shipping(),
				'order_data'        => [
					'currency'     => strtolower( $currency ),
					'country_code' => substr( get_option( 'woocommerce_default_country' ), 0, 2 ),
				],
			];
			
			$data['order_data'] += Cart_Checkout_Helpers::build_display_items();
		}
		
		wp_send_json( $data );
	}
	
	public function get_shipping_options() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'paysafe_checkout_payments-get_shipping_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		$shipping_address          = filter_input_array(
			INPUT_POST,
			[
				'country'   => FILTER_SANITIZE_SPECIAL_CHARS,
				'state'     => FILTER_SANITIZE_SPECIAL_CHARS,
				'postcode'  => FILTER_SANITIZE_SPECIAL_CHARS,
				'city'      => FILTER_SANITIZE_SPECIAL_CHARS,
				'address'   => FILTER_SANITIZE_SPECIAL_CHARS,
				'address_2' => FILTER_SANITIZE_SPECIAL_CHARS,
			]
		);
		$product_view_options      = filter_input_array( INPUT_POST, [ 'is_product_page' => FILTER_SANITIZE_SPECIAL_CHARS ] );
		$should_show_itemized_view = ! isset( $product_view_options['is_product_page'] ) ? true : filter_var( $product_view_options['is_product_page'], FILTER_VALIDATE_BOOLEAN );
		
		$data = Cart_Checkout_Helpers::get_shipping_options( $shipping_address, $should_show_itemized_view );
		wp_send_json( $data );
	}
	
	public function update_shipping_method() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'paysafe_checkout_payments-update_shipping_method' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		$shipping_methods = filter_input( INPUT_POST, 'shipping_method', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		Cart_Checkout_Helpers::update_shipping_method( $shipping_methods );
		
		WC()->cart->calculate_totals();
		
		$product_view_options      = filter_input_array( INPUT_POST, [ 'is_product_page' => FILTER_SANITIZE_SPECIAL_CHARS ] );
		$should_show_itemized_view = ! isset( $product_view_options['is_product_page'] ) ? true : filter_var( $product_view_options['is_product_page'], FILTER_VALIDATE_BOOLEAN );
		
		$data               = [];
		$data['order_data'] = Cart_Checkout_Helpers::build_display_items( $should_show_itemized_view );
		$data['result']     = 'success';
		
		wp_send_json( $data );
	}
	
	public function pay_for_order() {
		try {
			$nonce_value = wc_get_var( $_REQUEST['_wpnonce'], '' ); // phpcs:ignore
			if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
				throw new \Exception( __( 'We were unable to process your payment, please try again.', 'woocommerce' ) );
			}
			
			/**
			 * @var \WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway $gateway
			 */
			$gateway  = Factories::get_gateway( 'paysafe_checkout_payments' );
			$order_id = Paysafe::get_field( 'orderId', $_POST, 0 );
			
			$return = $gateway->process_payment( $order_id );
			
			wp_send_json( $return );
		}
		catch ( \Exception $e ) {
			$response = array(
				'result'   => 'failure',
				'messages' => $e->getMessage(),
			);
			
			wp_send_json( $response );
		}
	}
	
	public function create_order() {
		if ( WC()->cart->is_empty() ) {
			wp_send_json_error( __( 'Empty cart', 'wc_paysafe' ), 400 );
		}
		
		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}
		
		// Normalizes billing and shipping state values.
		Cart_Checkout_Helpers::normalize_state();
		
		// In case the state is required, but is missing, add a more descriptive error notice.
		Cart_Checkout_Helpers::validate_state();
		
		WC()->checkout()->process_checkout();
		
		die( 0 );
	}
}