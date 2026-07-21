<?php

namespace WcPaysafe\Gateways\Redirect\Payments\Response_Processors;

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
class Cards_Processor extends Checkoutjs_Processor {
	
	/**
	 * Adds an order note with most of the transaction information.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function add_payment_details_note( \WC_Order $order ) {
		$update_details = sprintf(
			__(
				'Paysafe Payment Details:
Status: %s.
Transaction Confirmation Number: %s,
Card type: %s,
Authorization ID: %s,
Last four digits: %s', 'wc_paysafe'
			),
			$this->get_response()->get_status(),
			$this->get_response()->get_auth_code(),
			$this->get_response()->get_card_type(),
			$this->get_response()->get_id(),
			$this->get_response()->get_last_digits()
		);
		
		$settlements = $this->get_response()->get_settlements();
		
		if ( ! empty( $settlements ) ) {
			$settlements = array_shift( $settlements );
			$update_details .= "\n" . sprintf( __( 'Settlement ID: %s', 'wc_paysafe' ), $settlements->id );
		}
		
		wc_paysafe_payments_add_debug_log( 'Paysafe Payment Details: ' . $update_details );
		
		// Update order
		$order->add_order_note( $update_details );
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
		$ps_order->save_payment_type( 'card' );
		
		// Only backward compatible
		$ps_order->save_payment_order_id( $this->get_response()->get_id() );
		
		$ps_order->save_authorization_id( $this->get_response()->get_id() );
		$ps_order->save_merchant_reference_number( $this->get_response()->merchantRefNum );
		
		// Save any settlement IDs
		$settlements = $this->get_response()->get_settlements();
		if ( $settlements ) {
			foreach ( $settlements as $settlement ) {
				$ps_order->add_settlement_id( $settlement->id );
			}
		}
	}
}