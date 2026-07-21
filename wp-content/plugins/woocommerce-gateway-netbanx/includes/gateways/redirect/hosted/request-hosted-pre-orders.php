<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  1.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Request_Hosted_Pre_Orders extends Abstract_Request {
	
	/**
	 * Returns a payment request URL link.
	 *
	 * It initialize the process of payment request.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 *
	 * @return string
	 */
	public function get_payment_url( \WC_Order $order ) {
		// Build
		$params = $this->build_payment_request( $order );
		
		/**
		 * @deprecated 'wc_netbanx_addons_request_params' Will be removed soon, use 'wc_paysafe_addons_request_params'
		 */
		$params = apply_filters( 'wc_netbanx_addons_request_params', $params, $order );
		
		// Allow for parameters modification
		$params = apply_filters( 'wc_paysafe_addons_request_params', $params, $order );
		
		$response = $this->attempt_to_process_order( $order, $params );
		
		return $response->getLink( 'hosted_payment' )->uri;
	}
	
	/**
	 * @param \WC_Order $order
	 *
	 * @return array|string
	 * @throws \Exception
	 */
	public function build_payment_request( \WC_Order $order ) {
		if ( \WC_Pre_Orders_Order::order_requires_payment_tokenization( $order ) ) {
			$params = $this->build_authorization_only_request( $order );
		} else {
			$params = parent::build_payment_request( $order );
		}
		
		return $params;
	}
	
	/**
	 * Setup the Paysafe payment request
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function build_authorization_only_request( \WC_Order $order ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Generating authorization request for Pre-Order #' . WC_Compatibility::get_order_id( $order ) );
		
		$amount        = 0;
		$paysafe_order = new Paysafe_Order( $order );
		
		$request_params = array(
			'merchantRefNum'            => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ),
			'totalAmount'               => $amount,
			'currencyCode'              => WC_Compatibility::get_order_currency( $order ),
			'customerNotificationEmail' => WC_Compatibility::get_order_billing_email( $order ),
			'locale'                    => $this->get_locale( $order ),
			'billingDetails'            => $this->get_billing_fields( $order ),
			'callback'                  => $this->get_callback_fields(),
			'redirect'                  => $this->get_redirect_fields(),
			'addendumData'              => $this->get_addendum_data_fields( $order ),
			'link'                      => $this->get_link_fields( $order ),
			'extendedOptions'           => array(
				array(
					'key'   => 'authType',
					'value' => 'auth',
				)
			)
		);
		
		// Add the merchant notifications to the request
		if ( '' != $this->get_gateway()->get_option( 'merchant_email_address' ) ) {
			$request_params['merchantNotificationEmail'] = $this->get_gateway()->get_option( 'merchant_email_address' );
		}
		
		// Add shipping fields
		if ( '' !== WC_Compatibility::get_order_shipping_address_1( $order ) ) {
			$request_params['shippingDetails'] = $this->get_shipping_fields( $order );
		}
		
		// Add order details
		if ( $this->maybe_send_order_details() ) {
			$request_params['shoppingCart'] = $this->get_pre_orders_cart_fields( $order );
		}
		
		// Add the customer profile node
		$request_params['profile'] = $this->add_customer_profile_fields( $order );
		
		// Are we sending the customer IP with the request
		if ( $this->send_customer_ip() ) {
			$request_params['customerIp'] = $this->get_user_ip_addr();
		}
		
		return $request_params;
	}
	
	/**
	 * Add the Pre-Orders cart details, so user knows what they are authorizing for.
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public function get_pre_orders_cart_fields( $order ) {
		$paysafe_order = new Paysafe_Order( $order );
		// Cart items
		$shopping_cart[] = array(
			'amount'      => '0',
			'description' => $this->format_string( sprintf( __( 'Pre-Order #%s' ), $paysafe_order->get_order_number() ), 50 ),
			'quantity'    => 1,
		);
		
		return $shopping_cart;
	}
	
	/**
	 * Process a rebill for a Pre-Order
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Order
	 */
	public function process_pre_orders_rebill( \WC_Order $order ) {
		$ps_order       = new Paysafe_Order( $order );
		$transaction_id = $ps_order->get_payment_order_id();
		
		$amount = $order->get_total();
		
		$params = array(
			'id'             => $transaction_id,
			'totalAmount'    => $this->format_amount( $amount ),
			'currencyCode'   => WC_Compatibility::get_order_currency( $order ),
			'merchantRefNum' => $ps_order->get_order_number() . '_' . $ps_order->get_attempts_suffix( 'order' ),
		);
		
		$params['shoppingCart'] = $this->get_shopping_cart_fields( $order, 'including' );
		
		$params = apply_filters( 'wc_paysafe_pre_order_rebill_parameters', $params, $order, $this->get_gateway() );
		
		return $this->process_rebill( $params );
	}
}