<?php

namespace WcPaysafe\Gateways\Redirect\Payments\Response_Processors;

use WcPaysafe\Helpers\Formatting;
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
class Checkoutjs_Processor extends Abstract_Processor {
	
	/**
	 * @since 3.3.0
	 *
	 * @param \WC_Order                                                 $order
	 * @param \WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD $token
	 *
	 * @throws \Exception
	 */
	public function process_payment_response( $order, $token = null ) {
		$this->process_order_by_status( $order );
	}
	
	/**
	 * Validates and processes the refund response
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 * @param double    $amount Amount to be refunded.
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function process_refund( \WC_Order $order, $amount ) {
		$this->validate_amount( $this->get_response()->get_amount(), Formatting::format_amount( $amount, $order->get_currency() ), $order );
		$status = strtolower( $this->get_response()->get_status() );
		
		// TODO: Need to make add a note for the merchant when the status is pending or received, so the merchant knows to check later
		if ( 'completed' != $status && 'pending' != $status && 'received' != $status ) {
			throw new \Exception(
				__(
					'Refund was not approved. Please try again or
					 try manual refund through your Paysafe dashboard.', 'wc_paysafe'
				)
			);
		}
		
		if ( 'pending' == $status || 'received' == $status ) {
			$order->add_order_note( sprintf( __( 'Important: The refund is in "%s" status and it should be processed in the next batch. Refund ID: %s', 'wc_paysafe' ), $status, $this->get_response()->get_id() ) );
		}
		
		return true;
	}
	
	/**
	 * Processes a settlement response
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 * @param float|int $amount
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function process_settlement( \WC_Order $order, $amount = 0 ) {
		$ps_order = new Paysafe_Order( $order );
		$status   = strtolower( $this->get_response()->get_status() );
		
		if ( 'completed' != $status && 'pending' != $status && 'received' != $status ) {
			throw new \Exception(
				__( 'Capture was not approved. Please try again or try manual capture through your Paysafe dashboard.', 'wc_paysafe' )
			);
		}
		
		if ( 'pending' == $status || 'received' == $status ) {
			$order->add_order_note( sprintf( __( 'Important: The settlement is in "%s" status and it should be processed in the next batch. Settlement ID: %s', 'wc_paysafe' ), $status, $this->get_response()->get_id() ) );
		}
		
		// We will validate amount in case we sent a specific amount
		if ( 0 < $amount ) {
			$this->validate_amount( $this->get_response()->get_amount(), Formatting::format_amount( $amount, $order->get_currency() ), $order );
		}
		
		// Debug log
		wc_paysafe_payments_add_debug_log( 'Capture completed.' );
		
		$response_amount = Formatting::format_amount_from_cent( $this->get_response()->get_amount() );
		
		// Add order note
		$order->add_order_note( sprintf( __( 'Amount captured %s. Settlement ID: %s.', 'wc_paysafe' ),
			get_woocommerce_currency_symbol() . wc_format_decimal( $response_amount ),
			$this->get_response()->get_id()
		) );
		
		$total_captured_amount = wc_format_decimal( $response_amount );
		
		// We only allow 1 capture per order, so if we are here then the order is considered captured
		$ps_order->save_is_payment_captured( true );
		$ps_order->add_settlement_id( $this->get_response()->get_id() );
		$ps_order->save_order_amount_captured( $total_captured_amount );
		
		return true;
	}
}