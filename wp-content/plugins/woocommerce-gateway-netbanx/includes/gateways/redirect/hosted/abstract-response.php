<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Exceptions\Validation_Exception;
use WcPaysafe\Paysafe;
use WcPaysafe\Exceptions\Order_Processed_Exception;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Paysafe Response abstract class.
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Abstract_Response extends Abstract_Hosted {
	
	/**
	 * Process Hosted API payment response
	 *
	 * @since 1.1
	 * @throws \InvalidArgumentException|\Exception
	 **/
	public function process_response() {
		try {
			$response = $this->lookup_the_response_and_return_the_result();
			
			$order = $this->get_wc_order_from_response_object( $response );
			
			do_action( 'wc_paysafe_redirect_hosted_response_processed', $response, $this, $order );
		}
		catch ( Order_Processed_Exception $ex ) {
			if ( '' == Paysafe::get_field( 'paysafe-redirect', $_GET, '' ) ) {
				throw new \Exception( $ex->getMessage(), $ex->getCode() );
			}
			
			if ( 0 == $ex->get_order_id() ) {
				throw new \Exception( $ex->getMessage(), $ex->getCode() );
			}
			
			// Debug log
			wc_paysafe_add_debug_log( $ex->getMessage() );
			
			$order = wc_get_order( $ex->get_order_id() );
		}
		
		// Reached the end, so if this is a redirect and the gateway uses iframe
		// lets redirect the customer and break the iframe
		$this->end_redirect_response( $order, wc_clean( wp_unslash( Paysafe::get_field( 'paysafe-redirect', $_GET ) ) ) );
	}
	
	/**
	 * Follows the main procedure of validating and processing a payment response.
	 *
	 * 1. Look up the order in Paysafe system.
	 * 2. Validate the Look up response against the WC order.
	 * 3. Process the WC order based on the Look up response.
	 *
	 * @since 2.0
	 * @throws \Exception
	 * @return \Paysafe\HostedPayment\Order The Paysafe Look up response object.
	 */
	public function lookup_the_response_and_return_the_result() {
		// Debug log
		wc_paysafe_add_debug_log( 'Payment response received. Response POST is: ' . print_r( $_POST, true ) );
		wc_paysafe_add_debug_log( 'Payment response received. Response GET is: ' . print_r( $_GET, true ) );
		
		if ( '' != Paysafe::get_field( 'id', $_POST, '' ) ) {
			$id = wc_clean( wp_unslash( Paysafe::get_field( 'id', $_POST, '' ) ) );
		} else {
			$id = wc_clean( wp_unslash( Paysafe::get_field( 'id', $_GET, '' ) ) );
		}
		
		// We got a response for a payment, query back the payment
		// to get all information and ensure everything is correct.
		$response = $this->response_order_lookup( $id );
		
		// Get the order ID from the lookup(re-query) object
		$order = $this->get_wc_order_from_response_object( $response );
		
		// Validate the response
		$this->validate_payment_response( $order, $response );
		
		// Process the order based on the response received from Paysafe.
		$this->process_order_based_on_response( $response, $order );
		
		return $response;
	}
	
	/**
	 * Performs an order look up
	 *
	 * @since 2.0
	 *
	 * @param string $id Paysafe order ID
	 *
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Order
	 */
	public function response_order_lookup( $id ) {
		$request  = new Request_Hosted( $this->get_gateway() );
		$response = $request->process_order_lookup( $id );
		
		wc_paysafe_add_debug_log( 'Payment Lookup response: ' . print_r( $response, true ) );
		
		return $response;
	}
	
	/**
	 * Validates a response from Paysafe
	 *
	 * @since 2.0
	 *
	 * @throws \Exception
	 *
	 * @param \WC_Order $order
	 * @param           $response
	 */
	public function validate_payment_response( \WC_Order $order, $response ) {
		// Since this check can only be done at a post request,
		// but there are many more times we can validate a response,
		// we will perform it only when applicable
		if ( null !== wc_clean( wp_unslash( Paysafe::get_field( 'order_id', $_POST, null ) ) ) ) {
			$this->validate_order_id( wc_clean( wp_unslash( \WcPaysafe\Paysafe::get_field( 'order_id', $_POST, '0' ) ) ), $response->id );
		}
		
		$paysafe_order = new Paysafe_Order( $order );
		
		$this->validate_currency( WC_Compatibility::get_order_currency( $order ), $response->currencyCode, $order );
		if ( ! $paysafe_order->is_subscription() ) {
			$this->validate_amount( $this->format_amount( $order->get_total() ), $response->totalAmount, $order );
			$this->validate_order_status( $order );
		}
	}
	
	/**
	 * Gets the WC Order from the Lookup order object.
	 *
	 * Inside the addendumData.
	 *
	 * @since 2.0
	 *
	 * @param object $response
	 *
	 * @return \WC_Order
	 * @throws \InvalidArgumentException
	 */
	public function get_wc_order_from_response_object( $response ) {
		if ( isset( $response->addendumData ) ) {
			foreach ( $response->addendumData as $value ) {
				if ( 'order_id' == $value->key ) {
					$order = wc_get_order( (int) $value->value );
					if ( false !== $order ) {
						return $order;
					}
				}
			}
		}
		throw new \InvalidArgumentException( __( 'The order ID in the response and the re-query ID do not match.', 'wc_paysafe' ) );
	}
	
	/**
	 * Adds an order note with most of the transaction information.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param object    $response
	 */
	public function add_payment_details_note( \WC_Order $order, $response ) {
		
		$update_details = sprintf(
			__(
				'Paysafe Payment Details:
Status: %s.
Transaction Confirmation Number: %s,
Card type: %s,
Last four digits: %s', 'wc_paysafe'
			),
			$response->transaction->status,
			$response->transaction->confirmationNumber,
			$response->transaction->card->brand,
			$response->transaction->card->lastDigits
		);
		
		wc_paysafe_add_debug_log( 'Paysafe Payment Details: ' . $update_details );
		
		// Update order
		$order->add_order_note( $update_details );
	}
	
	/**
	 * Gets potential error code and message from the payment response
	 *
	 * @since 2.0
	 *
	 * @param object $response
	 *
	 * @return string
	 */
	function get_errors_from_response( $response ) {
		$error_code    = '';
		$error_message = '';
		if ( isset( $response->transaction->errorCode ) ) {
			$error_code = ' Error Code: ' . $response->transaction->errorCode;
		}
		
		if ( isset( $response->transaction->errorMessage ) ) {
			$error_message = ' Error Message: ' . $response->transaction->errorMessage;
		}
		
		return sprintf( '%s%s', $error_code, $error_message );
	}
	
	/**
	 * Saves the transaction details to the given order.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param           $response
	 */
	public function save_transaction_details_to_order( \WC_Order $order, $response ) {
		$ps_order = new Paysafe_Order( $order );
		$ps_order->save_payment_order_id( $response->id );
		$ps_order->save_payment_type_details( array(
			'type'   => $response->transaction->card->type,
			'brand'  => $response->transaction->card->brand,
			'last4'  => $response->transaction->card->lastDigits,
			'expiry' => $response->transaction->card->expiry,
		) );
	}
	
	/**
	 * Saves the transaction capture status to the order meta
	 *
	 * @since 3.2.0
	 *
	 * @param \WC_Order $order
	 * @param           $response
	 */
	public function save_transaction_capture_status( \WC_Order $order, $response ) {
		$ps_order = new Paysafe_Order( $order );
		
		// Format the amount because it is send as an integer
		$amount = wc_format_decimal( $response->transaction->amount / 100 );
		
		if ( 'purchase' == $response->transaction->authType ) {
			$ps_order->save_is_payment_captured( true );
			$ps_order->save_order_amount_captured( $amount );
		} elseif ( 'auth' == $response->transaction->authType ) {
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
		if ( 'complete' == $order->get_status() || 'processing' == $order->get_status() ) {
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
	 * @param $response
	 * @param $order
	 */
	public function process_order_based_on_response( $response, $order ) {
		// Check the payment status
		switch ( $response->transaction->status ) :
			case 'success' :
				$this->process_response_status_success( $order, $response );
				break;
			case 'declined' :
				$this->process_response_status_declined( $response, $order );
				break;
			case 'abandoned' :
				$this->process_response_status_abandoned( $response, $order );
				break;
			case 'held' :
				$this->process_response_status_held( $response, $order );
				break;
			case 'cancelled' :
				$this->process_response_status_cancelled( $order );
				break;
			case 'pending' :
				$this->process_response_status_cancelled( $order );
				break;
			case 'errored' :
				$this->process_response_status_declined( $response, $order );
				break;
			default :
				$this->process_response_status_default( $response, $order );
				break;
		endswitch;
		
		/**
		 * @deprecated wc_netbanx_payment_response_processed is deprecated use the action below
		 */
		do_action( 'wc_netbanx_payment_response_processed', $order, $response );
		
		// Allow external system to utilize the response data
		do_action( 'wc_paysafe_payment_response_processed', $order, $response );
	}
	
	/**
	 * Processes the order for successful payment
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param           $response
	 */
	public function process_response_status_success( $order, $response ) {
		$this->add_payment_details_note( $order, $response );
		
		// Add transaction details to order
		$this->save_transaction_details_to_order( $order, $response );
		
		// Save capture status
		$this->save_transaction_capture_status( $order, $response );
		
		// Debug log
		wc_paysafe_add_debug_log( 'Payment completed.' );
		
		$order->payment_complete( $response->id );
		
		// Save the payment profile to the customer meta
		$this->save_customer_payment_profile( $order, $response );
		$this->save_profile_to_order( $order, $response );
		
		do_action( 'wc_paysafe_redirect_hosted_successful_response_processed', $response, $this, $order );
	}
	
	/**
	 * Processes the order for declined payment
	 *
	 * @since 2.0
	 *
	 * @param           $response
	 * @param \WC_Order $order
	 */
	public function process_response_status_declined( $response, $order ) {
		$error_details = $this->get_errors_from_response( $response );
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'Payment declined. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		// Update order
		$order->add_order_note( sprintf( __( 'Paysafe Payment Declined.', 'wc_paysafe' ) ) );
		
		do_action( 'wc_paysafe_redirect_hosted_declined_response_processed', $response, $this, $order );
	}
	
	/**
	 * Processes the order for abandoned payment
	 *
	 * @since 2.0
	 *
	 * @param object    $response
	 * @param \WC_Order $order
	 */
	public function process_response_status_abandoned( $response, $order ) {
		$error_details = $this->get_errors_from_response( $response );
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'Payment was abandoned part way through. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		// Update order
		$order->add_order_note( sprintf( __( 'Payment was abandoned part way through.', 'wc_paysafe' ) ) );
		
		do_action( 'wc_paysafe_redirect_hosted_abandoned_response_processed', $response, $this, $order );
	}
	
	/**
	 * Processes the order for held payment.
	 *
	 * @since 2.0
	 *
	 * @param object    $response
	 * @param \WC_Order $order
	 */
	public function process_response_status_held( $response, $order ) {
		// Debug log
		wc_paysafe_add_debug_log(
			sprintf(
				'Transaction has been placed on hold due to risk rules results.
						Risk Reason Code: %s.',
				$response->transaction->riskReasonCode
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
				$response->transaction->riskReasonCode
			)
		);
		
		// Add transaction details to order
		$this->save_transaction_details_to_order( $order, $response );
		
		// Save capture status
		$this->save_transaction_capture_status( $order, $response );
		
		// Save the payment profile to the customer meta
		$this->save_customer_payment_profile( $order, $response );
		$this->save_profile_to_order( $order, $response );
		
		do_action( 'wc_paysafe_redirect_hosted_help_response_processed', $response, $this, $order );
	}
	
	/**
	 * Processes the order for cancelled payment.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_response_status_cancelled( $order ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Transaction has been cancelled.' );
		
		$order->update_status( 'cancelled' );
		
		$order->add_order_note( __( 'Transaction has been cancelled.', 'wc_paysafe' ) );
		
		do_action( 'wc_paysafe_redirect_hosted_cancelled_response_processed', $this, $order );
	}
	
	/**
	 * Processes the order for declined payment
	 *
	 * @since 3.0
	 *
	 * @param           $response
	 * @param \WC_Order $order
	 */
	public function process_response_status_errored( $response, $order ) {
		$error_details = $this->get_errors_from_response( $response );
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'Payment error. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		// Update order
		$order->add_order_note( sprintf( __( 'Paysafe Payment Error.', 'wc_paysafe' ) ) );
		
		do_action( 'wc_paysafe_redirect_hosted_errored_response_processed', $this, $order );
	}
	
	/**
	 * Processes the order for default payment status(any status not already handled).
	 *
	 * @since 2.0
	 *
	 * @param object    $response
	 * @param \WC_Order $order
	 */
	public function process_response_status_pending( $response, $order ) {
		$error_details = $this->get_errors_from_response( $response );
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'The payment is still pending. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		$order->add_order_note( __( 'The payment is still pending.', 'wc_paysafe' ) );
		
		do_action( 'wc_paysafe_redirect_hosted_pending_response_processed', $this, $order );
	}
	
	/**
	 * Processes the order for default payment status(any status not already handled).
	 *
	 * @since 2.0
	 *
	 * @param object    $response
	 * @param \WC_Order $order
	 */
	public function process_response_status_default( $response, $order ) {
		$error_details = $this->get_errors_from_response( $response );
		
		// Debug log
		wc_paysafe_add_debug_log( sprintf( 'Unrecognized payment status was received. %s', $error_details ) );
		
		$order->update_status( 'failed' );
		
		$order->add_order_note( __( 'Unrecognized payment status was received.', 'wc_paysafe' ) );
	}
	
	public function end_redirect_response( $order, $status = '' ) {
		if ( '' != wc_clean( wp_unslash( Paysafe::get_field( 'paysafe-redirect', $_GET, '' ) ) ) ) {
			/**
			 * @deprecated 'wc_netbanx_payment_customer_redirect_url' Will be removed soon, use 'wc_paysafe_payment_customer_redirect_url'
			 */
			$redirect_url = apply_filters(
				'wc_netbanx_payment_customer_redirect_url',
				add_query_arg( 'paysafe-payment-status', $status, $this->get_gateway()->get_return_url( $order ) ),
				$status,
				WC_Compatibility::get_order_id( $order )
			);
			
			// Allow for custom final redirection link.
			$redirect_url = apply_filters(
				'wc_paysafe_payment_customer_redirect_url',
				$redirect_url,
				$status,
				WC_Compatibility::get_order_id( $order )
			);
			
			wc_get_template(
				'paysafe/iframe-break.php',
				array(
					'redirect_url' => $redirect_url,
				),
				'',
				Paysafe::plugin_path() . '/templates/'
			);
			exit;
		}
	}
}