<?php

namespace WcPaysafe\Gateways\Redirect\Checkout\Response_Processors;

use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Direct_Debit_Processor extends Checkoutjs_Processor {
	
	public function is_direct_debit_response() {
		return $this->response instanceof \Paysafe\DirectDebit\Purchase;
	}
	
	/**
	 * Adds an order note with most of the transaction information.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function add_payment_details_note( \WC_Order $order ) {
		$bank_account = $this->get_response()->get_last_digits();
		
		$update_details = sprintf(
			__(
				'Paysafe Direct Debit Details:
Status: %s.
Account Last Digits: %s', 'wc_paysafe'
			),
			$this->get_response()->get_status(),
			$bank_account
		);
		
		wc_paysafe_add_debug_log( 'Paysafe checkoutjs DD Details: ' . $update_details );
		
		// Update order
		$order->add_order_note( $update_details );
	}
	
	/**
	 * Processes the order for held payment.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_response_status_held( $order ) {
		// Debug log
		wc_paysafe_add_debug_log(
			sprintf(
				'Transaction has been placed on hold because it was not processed yet. Transaction ID: %s',
				$this->get_response()->get_id()
			)
		
		);
		
		$order->update_status( 'on-hold' );
		
		// Update order
		$order->add_order_note(
			sprintf(
				__( 'The order has been placed on hold because it was not processed yet. Please manually check the transaction to complete the order. Transaction ID: %s', 'wc_paysafe' ),
				$this->get_response()->get_id()
			)
		);
		
		// Save capture status
		$this->save_transaction_capture_status( $order );
		$this->save_transaction_details_to_order( $order );
	}
	
	/**
	 * Saves the transaction details to the given order.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function save_transaction_details_to_order( \WC_Order $order ) {
		$ps_order = new Paysafe_Order( $order );
		$ps_order->save_payment_order_id( $this->get_response()->get_id() );
		$ps_order->save_payment_type( $this->get_response()->bank_type() );
	}
}