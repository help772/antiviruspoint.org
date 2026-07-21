<?php

namespace WcPaysafe\Ajax\Frontend;

use WcPaysafe\Ajax\Ajax_Abstract;
use WcPaysafe\Api_Payments\Payments\Responses\Payments_Card_Token_Wrapper;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Payments\Processes;
use WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway;
use WcPaysafe\Helpers\Cart_Checkout_Helpers;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Customer;
use WcPaysafe\Paysafe_Order;
use WcPaysafe\Tokens\Customer_Tokens;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Paysafe_Checkout_Payments_Ajax extends Ajax_Abstract {
	
	public function hooks() {
		$map = array(
			'paysafe_payments_process_payment'       => [ $this, 'process_payment' ],
			'paysafe_payments_change_payment_method' => [ $this, 'change_payment_method' ],
		);
		
		foreach ( $map as $hook => $callback ) {
			add_action( 'wc_ajax_' . $hook, $callback );
		}
	}
	
	/**
	 * Process an order payment
	 */
	public function process_payment() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'paysafe_checkout_process-payment' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		// TODO: remove don't need it
		wc_paysafe_payments_add_debug_log( 'Payments checkout process_payment: $POST ' . print_r( $_POST, true ) );
		
		ob_start();
		
		/**
		 * @var Payments_Gateway|\WC_Payment_Gateway $gateway
		 */
		$gateway         = Factories::get_gateway( 'paysafe_checkout_payments' );
		$order_id        = wc_clean( wp_unslash( Paysafe::get_field( 'order_id', $_POST, 0 ) ) );
		$token           = wc_clean( wp_unslash( Paysafe::get_field( 'token', $_POST, '' ) ) );
		$payment_method  = wc_clean( wp_unslash( strtolower( Paysafe::get_field( 'payment_method', $_POST, '' ) ) ) );
		$save_to_account = (bool) wc_clean( wp_unslash( Paysafe::get_field( 'save_to_account', $_POST, false ) ) );
		
		$order         = wc_get_order( $order_id );
		$paysafe_order = new Paysafe_Order( $order );
		
		$available_methods = $gateway->get_available_payment_methods();
		$available_methods = array_map( 'strtolower', $available_methods );
		
		try {
			if ( false == $order ) {
				throw new \Exception( __( "Can't retrieve the order. Please refresh the page and try again.", 'wc_paysafe' ) );
			}
			
			if ( $order->is_paid() ) {
				throw new \Exception( __( "Can't process the payment. Please refresh the page and try again.", 'wc_paysafe' ) );
			}
			
			if ( ! in_array( strtolower( $payment_method ), $available_methods ) ) {
				throw new \Exception( __( "We don't support the requested payment method. Please try again using another payment method.", 'wc_paysafe' ) );
			}
			
			$integration = new Processes( $gateway );
			
			// Allow additional actions before payment is processed
			do_action( 'wc_paysafe_payments_before_payment_process', $order_id );
			
			/**
			 * 1. Charge the single token.
			 * 2. If successful, save the token to the profile. Process order
			 * 3. Save the WC Token information to the order/subscription
			 * 4. Return
			 *
			 * - No need for a payment
			 * 1. Turn the token to a permanent token, save it to the WC token
			 * 2. Save the WC Token to the order/subscription
			 */
			$notice      = '';
			$data_source = new Order_Source( $order );
			
			wc_paysafe_payments_add_debug_log( 'Ajax: Init payment for order ' . print_r( WC_Compatibility::get_order_id( $order ), true ) );
			
			// Do we need to charge the order now?
			if ( 0 < $order->get_total() ) {
				$data_source->set_is_initial_payment( true );
				$data_source->set_using_saved_token( false );
				
				// Take the payment
				$payment = $integration->process_token_transaction( $data_source, $token, $payment_method );
				
				$response_processor = Factories::load_response_processor( $payment, 'checkout_payments' );
				$response_processor->process_payment_response( $order, $token );
				
				// We can set the last payment response here.
				// The response contains payment info, so we may need it further down
				$data_source->set_last_payment_response( $payment );
				
				// The transaction is successful, so any errors from here on should only be displayed as notices
				try {
					// Save the tokens
					if ( ( 0 < $order->get_customer_id() && true == $save_to_account )
					     // TODO: We need a way to save a token without the customer selecting it in the payment screen
					     || $paysafe_order->contains_subscription()
					) {
						// Debug log
						wc_paysafe_payments_add_debug_log( 'Saving the payment as a token' );
						
						// Get or create a Vault profile for the customer
						$paysafe_customer = new Paysafe_Customer( $order->get_user() );
						
						$card_token_wrapper = $integration->maybe_convert_card_to_multi_use_token( new Payments_Card_Token_Wrapper( $payment ), $order );
						$token              = $card_token_wrapper->get_payment_token();
						
						// Save the customer_id to the customer itself. It will be used to recover the customer details
						$paysafe_customer->save_payments_customer_id( $card_token_wrapper->get_customer_id() );
						
						$customer_tokens = new Customer_Tokens( $order->get_user_id(), 'paysafe_checkout_payments' );
						
						// If we have a multiUseToken and we don't have it saved already
						if ( $token ) {
							$wc_token = $customer_tokens->get_token_from_value( $token );
							if (! $wc_token) {
								$wc_token = $customer_tokens->create_wc_token( $card_token_wrapper );
							}
							
							// Save the token to the order and possibly subscription
							$paysafe_order->save_token( $wc_token );
						}
					}
				}
				catch ( \Exception $e ) {
					wc_paysafe_payments_add_debug_log( 'Error converting the payment method to a token: ' . $e->getMessage() );
					
					$notice = $e->getMessage();
				}
			}
			
			// Debug log
			wc_paysafe_payments_add_debug_log( 'Payment completed' );
			
			// Empty cart
			Cart_Checkout_Helpers::empty_cart();
			
			$return_message = __( 'Payment completed successfully.', 'wc_paysafe' );
			if ( '' != $notice ) {
				$return_message = sprintf( __( 'Payment was completed, but with notices: %s', 'wc_paysafe' ), $notice );
				wc_paysafe_payments_add_debug_log( 'Completed with notice ' . $notice );
			}
			
			ob_clean();
			
			wp_send_json_success(
				array( 'message' => $return_message )
			);
		}
		catch ( \Exception $e ) {
			
			if ( $order ) {
				// Add note to the order, so the merchant knows what happened.
				$order->update_status( 'failed' );
				$order->add_order_note( sprintf( __( 'Paysafe failed to process your payment. Result: %s', 'wc_paysafe' ), $e->getMessage() ) );
			}
			
			wc_paysafe_payments_add_debug_log( sprintf( 'Error processing Ajax payment: %s', $e->getMessage() ) );
			
			ob_clean();
			
			wp_send_json_error(
				array( 'message' => $e->getMessage() )
			);
		}
	}
	
	/**
	 * Runs subscriptions change payment method request
	 */
	public function change_payment_method() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'paysafe_checkout_change-payment-method' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		/**
		 * @var Payments_Gateway|\WC_Payment_Gateway $gateway
		 */
		$gateway         = Factories::get_gateway( 'paysafe_checkout_payments' );
		$subscription_id = (int) wc_clean( wp_unslash( Paysafe::get_field( 'order_id', $_POST, 0 ) ) );
		$token           = wc_clean( wp_unslash( Paysafe::get_field( 'token', $_POST, '' ) ) );
		$payment_method  = wc_clean( wp_unslash( strtolower( Paysafe::get_field( 'payment_method', $_POST, '' ) ) ) );
		
		try {
			wc_paysafe_payments_add_debug_log( 'Changing payment method...' );
			
			$subscription = wcs_get_subscription( $subscription_id );
			
			if ( false === $subscription ) {
				throw new \Exception( __( 'The subscription ID passed to the request was not found. Please refresh the page and try again.', 'wc_paysafe' ) );
			}
			
			if ( 'card' != $payment_method ) {
				throw new \Exception( __( 'Not allowed payment type. Please refresh the page and try again.', 'wc_paysafe' ) );
			}
			
			$user        = $subscription->get_user();
			$integration = $gateway->get_integration_object();
			
			// The data we will pull information from
			$data_source = new Order_Source( $subscription );
			
			// Verify the token
			$payment = $integration->process_token_transaction( $data_source, $token, 'card', null, true );
			
			// Get Profile
			$paysafe_customer = new Paysafe_Customer( $user );
			$paysafe_order    = new Paysafe_Order( $subscription );
			
			// Convert the single-use token to permanent one
			$card_token_wrapper = new Payments_Card_Token_Wrapper( $payment );
			
			$customer_tokens = new Customer_Tokens( $data_source->get_user_id(), 'paysafe_checkout_payments' );
			
			// Save the customer_id to the customer itself. It will be used to recover the customer details
			$paysafe_customer->save_payments_customer_id( $card_token_wrapper->get_customer_id() );
			
			// If we have a multiUseToken and we don't have it saved already
			if ( ! $card_token_wrapper->get_payment_token() ) {
				throw new \Exception( __( 'A token was not created from the verification. Please refresh the page and try again.', 'wc_paysafe' ) );
			}
			
			$existing_token = $customer_tokens->get_token_from_value( $card_token_wrapper->get_payment_token() );
			
			if ( $existing_token ) {
				$wc_token = $existing_token;
			} else {
				$wc_token = $customer_tokens->create_wc_token( $card_token_wrapper );
			}
			
			// Save the token to the order and possibly subscription
			$paysafe_order->save_token( $wc_token );
			
			wp_send_json_success(
				array( 'message' => __( 'Payment method successfully changed.' ) )
			);
		}
		catch ( \Exception $e ) {
			
			wc_paysafe_payments_add_debug_log( 'Error message in ajax change_payment_method: ' . $e->getMessage() );
			
			wp_send_json_error(
				array( 'message' => $e->getMessage() )
			);
		}
	}
}