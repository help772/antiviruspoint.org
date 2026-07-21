<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hosted API payment requests.
 *
 * Will process normal requests for payment of orders containing only normal products.
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Request_Hosted extends Abstract_Request {
	
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
		 * @deprecated 'wc_netbanx_request_params' Will be removed soon, use 'wc_paysafe_request_params'
		 */
		$params = apply_filters( 'wc_netbanx_request_params', $params, $order );
		
		// Allow for parameters modification
		$params = apply_filters( 'wc_paysafe_request_params', $params, $order );
		
		$response = $this->attempt_to_process_order( $order, $params );
		
		return $response->getLink( 'hosted_payment' )->uri;
	}
	
	/**
	 * Builds the parameters and runs the process refund method.
	 *
	 * @since 2.0
	 *
	 * @param $transaction_id
	 * @param $amount
	 *
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Refund
	 */
	public function process_order_refund( $transaction_id, $amount ) {
		$params = array(
			'orderID' => $transaction_id,
			'amount'  => $this->format_amount( $amount ),
		);
		
		return $this->process_refund( $params );
	}
	
	/**
	 * Generates and runs settlement request
	 *
	 * @param \WC_Order $order
	 * @param int       $amount
	 *
	 * @return array
	 */
	public function prepare_settlement( \WC_Order $order, $amount = 0 ) {
		$ps_order = new Paysafe_Order( $order );
		
		// Get the saved card
		$transaction_id = $ps_order->get_payment_order_id();
		
		$params = array(
			'orderID'        => $transaction_id,
			'merchantRefNum' => 'capture-' . $ps_order->get_order_number() . '-' . $ps_order->get_attempts_suffix( 'order' ),
		);
		
		if ( 0 < $amount ) {
			$params['amount'] = $this->format_amount( $amount );
		}
		
		$params = apply_filters( 'wc_paysafe_settlement_parameters', $params, $order, $amount, $this->get_gateway() );
		
		return $params;
	}
}