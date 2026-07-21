<?php

namespace WcPaysafe\Gateways\Redirect\Checkout\Response_Processors;

use WcPaysafe\Api\Cards\Responses\Authorizations;
use WcPaysafe\Api\Direct_Debit\Responses\Eft;
use WcPaysafe\Api\Direct_Debit\Responses\Purchases;
use WcPaysafe\Api\Direct_Debit\Responses\Sepa;
use WcPaysafe\Api\Response_Abstract;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Exceptions\Validation_Exception;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Exceptions\Order_Processed_Exception;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Response abstract class.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Abstract_Processor {
	
	public $response;
	
	/**
	 * Abstract_Processor constructor.
	 *
	 * @param Response_Abstract|Authorizations|Sepa $response
	 */
	public function __construct( Response_Abstract $response ) {
		$this->response = $response;
	}
	
	/**
	 * We want any call to a not declared prop to attempt to get the value from the response
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( isset( $this->get_response()->{$name} ) ) {
			return $this->get_response()->{$name};
		}
		
		return null;
	}
	
	/**
	 * @return Authorizations|Purchases|Sepa|Eft|Response_Abstract
	 */
	public function get_response() {
		return $this->response;
	}
	
	/**
	 * Validates a response from Paysafe
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 */
	public function validate_payment_response( \WC_Order $order ) {
		$this->validate_currency( WC_Compatibility::get_order_currency( $order ), $this->get_response()->get_currency_code(), $order );
		$this->validate_amount( Formatting::format_amount( $order->get_total(), $order->get_currency() ), $this->get_response()->get_amount(), $order );
		$this->validate_order_status( $order );
	}
	
	/**
	 * Adds an order note with most of the transaction information.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function add_payment_details_note( \WC_Order $order ) {
		// Extend the method to properly add details
	}
	
	/**
	 * Gets potential error code and message from the payment response
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	function get_errors_from_response() {
		$error_code    = '';
		$error_message = '';
		if ( $this->get_response()->get_error_code() ) {
			$error_code = ' Error Code: ' . $this->get_response()->get_error_code();
		}
		
		if ( $this->get_response()->get_error_message() ) {
			$error_message = ' Error Message: ' . $this->get_response()->get_error_message();
		}
		
		return sprintf( '%s%s', $error_code, $error_message );
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
		$ps_order->add_settlement_id( $this->get_response()->get_id() );
	}
	
	/**
	 * Saves the transaction capture status to the order meta
	 *
	 * @since 3.2.0
	 *
	 * @param \WC_Order $order
	 */
	public function save_transaction_capture_status( \WC_Order $order ) {
		$ps_order = new Paysafe_Order( $order );
		
		// Format the amount because it is send as an integer
		$amount = wc_format_decimal( Formatting::format_amount_from_cent( $this->get_response()->get_amount() ) );
		
		// Add check for CC|DD|is_charge
		if ( $this->get_response()->get_settle_with_auth() ) {
			$ps_order->save_is_payment_captured( true );
			$ps_order->save_order_amount_captured( $amount );
			$ps_order->save_order_amount_authorized( 0 );
		} else {
			$ps_order->save_is_payment_captured( false );
			$ps_order->save_order_amount_captured( 0 );
			$ps_order->save_order_amount_authorized( $amount );
		}
	}
	
	/**
	 * Validates the order ID against the WC Order.
	 *
	 * @since 2.0
	 *
	 * @param $order_id
	 * @param $response_id
	 *
	 * @throws \InvalidArgumentException
	 */
	public function validate_order_id( $order_id, $response_id ) {
		// Those IDs should always match
		if ( $order_id == $response_id ) {
			throw new \InvalidArgumentException( __( 'The order ID in the response and security check order ID do not match.', 'wc_paysafe' ) );
		}
	}
	
	/**
	 * Validates the currency against the store currency.
	 *
	 * @since 2.0
	 *
	 * @param string    $order_currency
	 * @param string    $response_currency
	 * @param \WC_Order $order
	 *
	 * @throws Validation_Exception
	 */
	public function validate_currency( $order_currency, $response_currency, $order ) {
		// Check order currency
		if ( $order_currency != $response_currency ) {
			$message = __( 'Currency of the payment did not match the currency the order.', 'wc_paysafe' );
			$order->add_order_note( $message );
			
			throw new Validation_Exception( $message, 8001, WC_Compatibility::get_order_id( $order ) );
		}
	}
	
	/**
	 * Validates the amount paid against the store order amount.
	 *
	 * @since 2.0
	 *
	 * @param float     $order_total
	 * @param float     $response_total
	 * @param \WC_Order $order
	 *
	 * @throws Validation_Exception
	 */
	public function validate_amount( $order_total, $response_total, $order ) {
		// Check the amount
		if ( $order_total != $response_total ) {
			$message = __( 'Response amount did not match the given order amount.', 'wc_paysafe' );
			$order->add_order_note( $message );
			
			throw new Validation_Exception( $message, 8002, WC_Compatibility::get_order_id( $order ) );
		}
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
		if ( 'completed' == $order->get_status() || 'processing' == $order->get_status() ) {
			$message = __( 'Paysafe: Received response, but order was already paid for.', 'wc_paysafe' );
			$order->add_order_note( $message );
			
			throw new Order_Processed_Exception( $message, 8003, WC_Compatibility::get_order_id( $order ) );
		}
	}
	
	/**
	 * Processes the order based on the payment status.
	 * Will complete or fail an order based on the payment status.
	 * Adds debug logs and the appropriate order notes.
	 *
	 * @since 2.0
	 *
	 * @param $order
	 *
	 * @throws \Exception
	 */
	public function process_order_by_status( $order ) {
		wc_paysafe_add_debug_log( 'Processing response for order: ' . print_r( WC_Compatibility::get_order_id( $order ), true ) );
		
		// Check the payment status
		switch ( strtolower( $this->get_response()->get_status() ) ) :
			case 'completed' :
				$this->process_response_status_success( $order );
				break;
			case 'failed' :
				$this->process_response_status_declined( $order );
				break;
			case 'pending' :
			case 'held' :
				$this->process_response_status_held( $order );
				break;
			case 'received' :
				$this->process_response_status_held( $order );
				break;
			case 'cancelled' :
				$this->process_response_status_cancelled( $order );
				break;
			default :
				$this->process_response_status_default( $order );
				break;
		endswitch;
		
		// Allow external system to utilize the response data
		do_action( 'wc_paysafe_payment_response_processed', $order, $this->get_response() );
	}
	
	/**
	 * Processes the order for successful payment
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_response_status_success( $order ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Payment successful. Adding notes and details.' );
		
		$this->add_payment_details_note( $order );
		
		// Save capture status
		$this->save_transaction_capture_status( $order );
		$this->save_transaction_details_to_order( $order );
		
		$paysafe_order = new Paysafe_Order( $order );
		$paysafe_order->complete_order( $this->get_response()->get_id() );
	}
	
	/**
	 * Processes the order for declined payment
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 */
	public function process_response_status_declined( $order ) {
		$error_details = $this->get_errors_from_response();
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'Payment declined. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		// Update order
		$order->add_order_note( sprintf( __( 'Paysafe Payment Declined.', 'wc_paysafe' ) ) );
		
		// Failed
		throw new \Exception( sprintf( __( 'Paysafe Payment Declined. Payment status: %s', 'wc_paysafe' ), $this->get_status() ) );
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
				'Transaction has been placed on hold due to risk rules results.
						Risk Reason Code: %s.',
				$this->get_response()->get_risk_code()
			)
		);
		
		$order->update_status( 'on-hold' );
		
		// Update order
		$order->add_order_note(
			sprintf(
				__(
					'Transaction has been placed on hold due to risk rules results.
							Risk Reason Code: %s.', 'wc_paysafe'
				),
				$this->get_response()->get_risk_code()
			)
		);
		
		// Save capture status
		$this->save_transaction_capture_status( $order );
		$this->save_transaction_details_to_order( $order );
	}
	
	/**
	 * @param \WC_Order $order
	 */
	public function process_response_status_received( $order ) {
		
		$message = __( "Transaction has been placed on hold because we are still waiting on the processor response. We'll process the order as soon as we get a decision back.", 'wc_paysafe' );
		
		// Debug log
		wc_paysafe_add_debug_log( $message );
		
		$order->update_status( 'on-hold' );
		
		// Update order
		$order->add_order_note( $message );
		
		// Save capture status
		$this->save_transaction_capture_status( $order );
		$this->save_transaction_details_to_order( $order );
	}
	
	/**
	 * Processes the order for cancelled payment.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 */
	public function process_response_status_cancelled( $order ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Transaction has been cancelled.' );
		
		$order->update_status( 'cancelled' );
		
		$order->add_order_note( __( 'Transaction has been cancelled.', 'wc_paysafe' ) );
		
		// Failed
		throw new \Exception( __( 'Transaction has been cancelled.', 'wc_paysafe' ) );
	}
	
	/**
	 * Processes the order for declined payment
	 *
	 * @since 3.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_response_status_errored( $order ) {
		$error_details = $this->get_errors_from_response();
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'Payment error. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		// Update order
		$order->add_order_note( sprintf( __( 'Paysafe Payment Error.', 'wc_paysafe' ) ) );
	}
	
	/**
	 * Processes the order for default payment status(any status not already handled).
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_response_status_pending( $order ) {
		$error_details = $this->get_errors_from_response();
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'The payment is still pending. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		$order->add_order_note( __( 'The payment is still pending.', 'wc_paysafe' ) );
	}
	
	/**
	 * Processes the order for default payment status(any status not already handled).
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 */
	public function process_response_status_default( $order ) {
		$error_details = $this->get_errors_from_response();
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'Unrecognized payment status was received. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		$order->add_order_note( __( 'Unrecognized payment status was received.', 'wc_paysafe' ) );
		
		// Failed
		throw new \Exception( sprintf( __( 'Payment Failed. Unrecognized payment status was received. Payment status: %s', 'wc_paysafe' ), $this->get_response()->get_status() ) );
	}
}