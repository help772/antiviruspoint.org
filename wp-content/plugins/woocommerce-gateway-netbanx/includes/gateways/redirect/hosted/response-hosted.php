<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the Hosted Response operations
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Response_Hosted extends Abstract_Response {
	
	/**
	 * Validates and processes the refund response
	 *
	 * @since 2.0
	 *
	 * @param \Paysafe\HostedPayment\Refund $response              Response of the refund request
	 * @param \WC_Order                     $order
	 * @param double                        $amount                Amount to be refunded.
	 * @param string                        $refund_transaction_id Original Paysafe Transaction Order ID
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function process_refund_response( \Paysafe\HostedPayment\Refund $response, \WC_Order $order, $amount, $refund_transaction_id ) {
		$this->validate_currency( $response->currencyCode, WC_Compatibility::get_order_currency( $order ), $order );
		$this->validate_amount( $response->amount, $this->format_amount( $amount ), $order );
		
		if ( isset( $response->confirmationNumber ) && '' != $response->confirmationNumber ) {
			do_action( 'wc_paysafe_redirect_hosted_refund_response_processed', $response, $this, $order );
			
			return true;
		} else {
			throw new \Exception(
				__(
					'Refund was not approved. Please try again or
					 try manual refund through your Paysafe dashboard.', 'wc_paysafe'
				)
			);
		}
	}
	
	/**
	 * Processes a settlement response
	 *
	 * @since 3.2.0
	 *
	 * @param \Paysafe\HostedPayment\Settlement $response
	 * @param \WC_Order                         $order
	 * @param float|int                         $amount
	 *
	 * @return bool
	 * @throws \Exception
	 * @throws \WC_Paysafe_Validation_Exception
	 */
	public function process_settlement_response( \Paysafe\HostedPayment\Settlement $response, \WC_Order $order, $amount = 0 ) {
		$ps_order = new Paysafe_Order( $order );
		
		// Only settlement types allowed
		if ( 'settlement' != $response->authType ) {
			wc_paysafe_add_debug_log( 'Settlement response did not have the proper authType. authType: ' . $response->authType );
			throw new \Exception(
				sprintf( __( 'Settlement response did not have the proper authType. authType: %s', 'wc_paysafe' ), $response->authType )
			);
		}
		
		if ( isset( $response->confirmationNumber ) && '' != $response->confirmationNumber ) {
			$this->validate_currency( $response->currencyCode, WC_Compatibility::get_order_currency( $order ), $order );
			
			// We will validate amount in case we sent a specific amount
			if ( 0 < $amount ) {
				$this->validate_amount( $response->amount, $this->format_amount( $amount ), $order );
			}
			
			// Debug log
			\wc_paysafe_add_debug_log( 'Capture completed.' );
			
			$response_amount = $response->amount / 100;
			
			// Add order note
			$order->add_order_note( sprintf( __( 'Amount captured %s. Transaction ID: %s.', 'wc_paysafe' ),
				get_woocommerce_currency_symbol() . wc_format_decimal( $response_amount ),
				$response->id
			) );
			
			$amount_authorized     = wc_format_decimal( $ps_order->get_order_amount_authorized(), 2 );
			$amount_captured       = wc_format_decimal( $ps_order->get_order_amount_captured(), 2 );
			$total_captured_amount = wc_format_decimal( $response_amount + $amount_captured );
			
			$is_captured = false;
			if ( $amount_authorized <= $total_captured_amount ) {
				$is_captured = true;
			}
			
			$ps_order->save_is_payment_captured( $is_captured );
			$ps_order->save_order_amount_captured( $total_captured_amount );
			
			do_action( 'wc_paysafe_redirect_hosted_settlement_response_processed', $response, $this, $order );
			
			return true;
		} else {
			throw new \Exception(
				__(
					'Capture was not approved. Please try again or
					 try manual capture through your Paysafe dashboard.', 'wc_paysafe'
				)
			);
		}
	}
}