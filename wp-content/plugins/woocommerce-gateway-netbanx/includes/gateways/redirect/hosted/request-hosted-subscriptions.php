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
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Request_Hosted_Subscriptions extends Abstract_Request {
	
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
	public function build_payment_request( \WC_Order $order ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Generating Subscription payment form for order #' . WC_Compatibility::get_order_id( $order ) );
		
		$amount        = $this->format_amount( $order->get_total() );
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
			if ( $paysafe_order->is_subscription() ) {
				$request_params['shoppingCart'] = $this->get_change_method_shopping_cart_fields( $order );
			} elseif ( 0 == $amount ) {
				$request_params['shoppingCart'] = $this->get_free_subscription_shopping_cart_fields( $order );
			} else {
				$request_params['shoppingCart'] = $this->get_shopping_cart_fields( $order, 'including' );
			}
		}
		
		// If we don't have to charge a payment. Total 0
		if ( 0 == $amount || 'auth' == $this->get_gateway()->authorization_type ) {
			$request_params['extendedOptions'][] = array(
				'key'   => 'authType',
				'value' => 'auth',
			);
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
	 * @param \WC_Order $order
	 * @param double    $amount
	 *
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Order
	 */
	public function process_subscription_rebill( \WC_Order $order, $amount = null ) {
		$ps_order       = new Paysafe_Order( $order );
		$transaction_id = $ps_order->get_payment_order_id();
		
		// Amount is the order total, if not given any different
		if ( null === $amount ) {
			$amount = $order->get_total();
		}
		
		$params = array(
			'id'             => $transaction_id,
			'totalAmount'    => $this->format_amount( $amount ),
			'currencyCode'   => WC_Compatibility::get_order_currency( $order ),
			'merchantRefNum' => $ps_order->get_order_number() . '_' . $ps_order->get_attempts_suffix( 'order' ),
		);
		
		$params['shoppingCart'][] = array(
			'amount'      => $this->format_amount( $amount ),
			'description' => $this->format_string( sprintf( __( '%s - Order %s', 'wc_paysafe' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $ps_order->get_order_number() ), 50 ),
			'quantity'    => 1,
		);
		
		$params = apply_filters( 'wc_paysafe_subscription_rebill_parameters', $params, $order, $this->get_gateway() );
		
		return $this->process_rebill( $params );
	}
	
	/**
	 * Free Subscription order description
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	private function get_free_subscription_shopping_cart_fields( \WC_Order $order ) {
		$paysafe_order = new Paysafe_Order($order);
		return array(
			array(
				'amount'      => 0,
				'description' => $this->format_string(
					sprintf(
						__( 'Payment for Order %s.', 'wc_paysafe' ),
						$paysafe_order->get_order_number()
					),
					50
				),
				'quantity'    => 1,
			)
		);
	}
	
	public function get_change_method_shopping_cart_fields( \WC_Order $order ) {
		$paysafe_order = new Paysafe_Order($order);
		return array(
			array(
				'amount'      => 0,
				'description' => $this->format_string(
					sprintf(
						__( 'Change payment method request on order %s.', 'wc_paysafe' ),
						$paysafe_order->get_order_number()
					),
					50
				),
				'quantity'    => 1,
			)
		);
	}
}

