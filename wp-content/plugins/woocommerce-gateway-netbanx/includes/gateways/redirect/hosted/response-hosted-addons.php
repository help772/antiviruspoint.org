<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Paysafe;
use WcPaysafe\Exceptions\Order_Processed_Exception;
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
class Response_Hosted_Addons extends Abstract_Response {
	
	/**
	 * Follows the main procedure of validating and processing a payment response.
	 *
	 * 1. Look up the order in Paysafe system.
	 * 2. Validate the Look up response against the WC order.
	 * 3. Process the WC order based on the Look up response.
	 *
	 * @since 2.0
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Order The Paysafe Look up response object.
	 */
	public function lookup_the_response_and_return_the_result() {
		// Debug log
		wc_paysafe_add_debug_log( 'Payment response received (addons). Response POST is: ' . print_r( $_POST, true ) );
		wc_paysafe_add_debug_log( 'Payment response received (addons). Response GET is: ' . print_r( $_GET, true ) );
		
		if ( '' != wc_clean( wp_unslash( Paysafe::get_field( 'id', $_POST, '' ) ) ) ) {
			$id = wc_clean( wp_unslash( Paysafe::get_field( 'id', $_POST, '' ) ) );
		} else {
			$id = wc_clean( wp_unslash( Paysafe::get_field( 'id', $_GET, '' ) ) );
		}
		
		// We got a response for a payment, query back the payment
		// to get all information and ensure everything is correct.
		$response = $this->response_order_lookup( $id );
		
		// Get the order ID from the lookup(requery) object
		$order         = $this->get_wc_order_from_response_object( $response );
		$paysafe_order = new Paysafe_Order( $order );
		
		// Contains subscription
		if ( $paysafe_order->is_subscription() ) {
			wc_paysafe_add_debug_log( 'Processing change method response.' );
			
			// Validate the response
			$this->validate_payment_response( $order, $response );
			
			// Only save the customer payment profile and the order id for future recurring payments
			if ( 'success' == $response->transaction->status ) {
				$this->save_customer_payment_profile( $order, $response );
				$this->save_transaction_details_to_order( $order, $response );
			}
			
			$order->add_order_note( sprintf( __( 'Customer changed their payment method payment to %s', 'wc_paysafe' ), $this->get_gateway()->method_title ) );
		} elseif ( $paysafe_order->contains_subscription() ) {
			wc_paysafe_add_debug_log( 'Processing Subscription order.' );
			
			// Validate the response
			$this->validate_payment_response( $order, $response );
			
			// Process the order based on the response received from Paysafe.
			$this->process_order_based_on_response( $response, $order );
			
			if ( 'success' == $response->transaction->status ) {
				$this->save_meta_data_to_subscription( $order, $response );
			}
		} elseif ( $paysafe_order->is_pre_order_with_tokenization() ) {
			// Contains Pre Order
			wc_paysafe_add_debug_log( 'Processing Pre order. Order: ' . $paysafe_order->get_order_number() );
			wc_paysafe_add_debug_log( 'Response ID: ' . $response->id );
			
			if ( null !== wc_clean( wp_unslash( Paysafe::get_field( 'order_id', $_POST, null ) ) ) ) {
				$this->validate_order_id( wc_clean( wp_unslash( Paysafe::get_field( 'order_id', $_POST, '0' ) ) ), $response->id );
			}
			
			$this->validate_currency( WC_Compatibility::get_order_currency( $order ), $response->currencyCode, $order );
			$this->validate_order_status( $order );
			
			if ( 'success' == $response->transaction->status ) {
				
				wc_paysafe_add_debug_log( 'Save Transaction Details' );
				
				// Add transaction details to order
				$this->save_transaction_details_to_order( $order, $response );
				
				wc_paysafe_add_debug_log( 'Mark As Pre Ordered' );
				
				// Now that we have the info need for future payment, mark the order pre-ordered
				\WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );
			}
		} else {
			// Normal Order
			wc_paysafe_add_debug_log( 'Processing normal order.' );
			
			// Validate the response
			$this->validate_payment_response( $order, $response );
			
			// Process the order based on the response received from Paysafe.
			$this->process_order_based_on_response( $response, $order );
		}
		
		do_action( 'wc_paysafe_redirect_hosted_addons_response_processed', $response, $this, $order );
		
		return $response;
	}
	
	/**
	 * Checks that the order is not already processed(Processing or Completed status).
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws Order_Processed_Exception
	 */
	public function validate_order_status( $order ) {
		// Check if order was processed already
		if ( 'complete' == $order->get_status()
		     || 'processing' == $order->get_status()
		     || 'pre-ordered' == $order->get_status()
		) {
			$message = __( 'Paysafe: Received response, but order was already paid for.', 'wc_paysafe' );
			$order->add_order_note( $message );
			
			throw new Order_Processed_Exception( $message, 8003, WC_Compatibility::get_order_id( $order ) );
		}
	}
	
	/**
	 * Save the transaction details to the Subscription
	 *
	 * @since 2.0
	 *
	 * @param $order
	 * @param $response
	 */
	public function save_meta_data_to_subscription( $order, $response ) {
		// Also store it on the subscriptions being purchased or paid for in the order
		if ( wcs_order_contains_subscription( $order ) ) {
			$subscriptions = wcs_get_subscriptions_for_order( $order );
		} elseif ( wcs_order_contains_renewal( $order ) ) {
			$subscriptions = wcs_get_subscriptions_for_renewal_order( $order );
		} else {
			$subscriptions = array();
		}
		
		foreach ( $subscriptions as $subscription ) {
			
			// Debug log
			wc_paysafe_add_debug_log( 'Saving details to subscription: ' . print_r( WC_Compatibility::get_order_id( $subscription ), true ) );
			
			$this->save_transaction_details_to_order( $subscription, $response );
		}
	}
}