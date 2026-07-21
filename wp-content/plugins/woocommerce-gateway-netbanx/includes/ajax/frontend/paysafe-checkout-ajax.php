<?php

namespace WcPaysafe\Ajax\Frontend;

use WcPaysafe\Ajax\Ajax_Abstract;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Checkout\Processes;
use WcPaysafe\Gateways\Redirect\Gateway;
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
class Paysafe_Checkout_Ajax extends Ajax_Abstract {
	
	public function hooks() {
		$map = array(
			'paysafe_process_payment'       => [ $this, 'process_payment' ],
			'paysafe_update_payment_method' => [ $this, 'update_payment_method' ],
			'paysafe_add_payment_method'    => [ $this, 'add_payment_method' ],
			'paysafe_change_payment_method' => [ $this, 'change_payment_method' ],
		);
		
		foreach ( $map as $hook => $callback ) {
			add_action( 'wc_ajax_' . $hook, $callback );
		}
		
		add_action( 'template_redirect', array( $this, 'set_session' ) );
	}
	
	/**
	 * Sets the WC customer session if one is not set.
	 * This is needed so nonces can be verify by AJAX Request.
	 *
	 * @since 3.3.0
	 */
	public function set_session() {
		if ( ! is_product() || ( isset( WC()->session ) && WC()->session->has_session() ) ) {
			return;
		}
		
		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
		/**
		 * @var \WC_Session_Handler $wc_session
		 */
		$wc_session = new $session_class();
		
		if ( version_compare( WC_VERSION, '3.3', '>=' ) ) {
			$wc_session->init();
		}
		
		$wc_session->set_customer_session_cookie( true );
	}
	
	/**
	 * Process an order payment
	 */
	public function process_payment() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'wc-paysafe-process-payment' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		/**
		 * @var Gateway|\WC_Payment_Gateway $gateway
		 */
		$gateway              = Factories::get_gateway( 'netbanx' );
		$order_id             = wc_clean( wp_unslash( Paysafe::get_field( 'order_id', $_POST, 0 ) ) );
		$token                = wc_clean( wp_unslash( Paysafe::get_field( 'token', $_POST, '' ) ) );
		$payment_method       = wc_clean( wp_unslash( strtolower( Paysafe::get_field( 'payment_method', $_POST, '' ) ) ) );
		$save_to_account      = wc_clean( wp_unslash( Paysafe::get_field( 'save_to_account', $_POST, false ) ) );
		$set_token_as_default = wc_clean( wp_unslash( Paysafe::get_field( 'set_as_default', $_POST, false ) ) );
		
		$order         = wc_get_order( $order_id );
		$paysafe_order = new Paysafe_Order( $order );
		
		try {
			if ( false == $order ) {
				throw new \Exception( __( "Can't retrieve the order. Please refresh the page and try again.", 'wc_paysafe' ) );
			}
			
			if ( $order->is_paid() ) {
				throw new \Exception( __( "Can't process the payment. Please refresh the page and try again.", 'wc_paysafe' ) );
			}
			
			if ( 'interac' == $payment_method ) {
				throw new \Exception( __( "We don't support interac payments. Please try again using another payment type.", 'wc_paysafe' ) );
			}
			
			$integration = new Processes( $gateway );
			
			// Allow additional actions before payment is processed
			do_action( 'wc_paysafe_before_payment_process', $order_id );
			
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
			
			wc_paysafe_add_debug_log( 'Ajax: Init payment for order ' . print_r( WC_Compatibility::get_order_id( $order ), true ) );
			
			// Do we need to charge the order now?
			if ( 0 < $order->get_total() || ! $paysafe_order->is_pre_order_with_tokenization() ) {
				$data_source->set_is_initial_payment( true );
				$data_source->set_using_saved_token( false );
				
				// Take the payment
				$payment = $integration->process_token_transaction( $data_source, $token, $payment_method );
				
				wc_paysafe_add_debug_log( 'Payment response ' . print_r( $payment, true ) );
				
				$response_processor = Factories::load_response_processor( $payment, 'checkoutjs' );
				$response_processor->process_payment_response( $order, $token );
				
				// We can set the last payment response here.
				// The response contains payment info, so we may need it further down
				$data_source->set_last_payment_response( $payment );
			}
			
			// The transaction is successful, so any errors from here on should only be displayed as notices
			try {
				// Save the tokens
				// Convert the bank account to a token
				
				if ( ( 0 < $order->get_customer_id() && true == $save_to_account )
				     || $paysafe_order->contains_subscription()
				     || $paysafe_order->is_pre_order_with_tokenization()
				) {
					
					// Debug log
					wc_paysafe_add_debug_log( 'Saving the payment as a token' );
					
					// Get or create a Vault profile for the customer
					$paysafe_customer = new Paysafe_Customer( $order->get_user() );
					$profile_id       = $paysafe_customer->get_vault_profile_id();
					if ( false === $profile_id ) {
						wc_paysafe_add_debug_log( 'Customer has no profile. We will create one.' );
						
						// If the customer does not have a Paysafe profile to hold their tokens, lets create one
						$integration->create_vault_profile( $data_source, $paysafe_customer );
					}
					
					// Convert the single-use token to permanent one
					$permanent_token = $integration->convert_single_use_token( $data_source, $token, $payment_method );
					$customer_tokens = new Customer_Tokens( $order->get_user_id() );
					$wc_token        = $customer_tokens->create_wc_token( $permanent_token );
					
					// Set the token as default if it is selected
					if ( true == $set_token_as_default ) {
						$wc_token->set_default( true );
						$wc_token->save();
					}
					
					// Save the token to the order and possibly subscription
					$paysafe_order->save_token( $wc_token );
				}
			}
			catch ( \Exception $e ) {
				wc_paysafe_add_debug_log( 'Error converting a single-use token: ' . $e->getMessage() );
				
				$notice = $e->getMessage();
			}
			
			// Debug log
			wc_paysafe_add_debug_log( 'Payment completed' );
			
			// Empty cart
			Cart_Checkout_Helpers::empty_cart();
			
			$return_message = __( 'Payment completed successfully.', 'wc_paysafe' );
			if ( '' != $notice ) {
				$return_message = sprintf( __( 'Payment was completed, but with notices: %s', 'wc_paysafe' ), $notice );
				wc_paysafe_add_debug_log( 'Completed with notice ' . $notice );
			}
			
			wp_send_json_success(
				array( 'message' => $return_message )
			);
		}
		catch ( \Exception $e ) {
			
			if ( $order ) {
				// Add note to the order, so the merchant knows what happened.
				$order->update_status( 'failed' );
				$order->add_order_note( sprintf( __( 'Paysafe failed to process your payment. Result: %s %s', 'wc_paysafe' ), $e->getCode(), $e->getMessage() ) );
			}
			
			wc_paysafe_add_debug_log( sprintf( 'Error processing Ajax payment: %s %s', $e->getCode(), $e->getMessage() ) );
			
			wp_send_json_error(
				array( 'message' => $e->getMessage() )
			);
		}
	}
	
	/**
	 * Runs the update payment method procedure
	 */
	public function update_payment_method() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'wc-paysafe-update-payment-method' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		/**
		 * @var Gateway|\WC_Payment_Gateway $gateway
		 */
		$gateway         = Factories::get_gateway( 'netbanx' );
		$user_id         = (int) wc_clean( wp_unslash( Paysafe::get_field( 'user_id', $_POST, 0 ) ) );
		$update_token_id = (int) wc_clean( wp_unslash( Paysafe::get_field( 'update_token_id', $_POST, 0 ) ) );
		$token           = wc_clean( wp_unslash( Paysafe::get_field( 'token', $_POST, '' ) ) );
		$payment_method  = wc_clean( wp_unslash( strtolower( Paysafe::get_field( 'payment_method', $_POST, '' ) ) ) );
		
		try {
			$user = new \WP_User( $user_id );
			
			wc_paysafe_add_debug_log( 'Updating payment method...' );
			
			if ( 'interac' == $payment_method ) {
				wc_paysafe_add_debug_log( 'Do not support interac payments.' );
				
				throw new \Exception( __( "We don't support interac payments. Please try again using another payment type.", 'wc_paysafe' ) );
			}
			
			if ( false == $user ) {
				wc_paysafe_add_debug_log( 'Could not retrieve the token user. User ID: ' . print_r( $user_id, true ) );
				
				throw new \Exception( __( "Could not retrieve the token user. Please refresh the page and try again.", 'wc_paysafe' ) );
			}
			
			$integration = new Processes( $gateway );
			
			/**
			 * @var \WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD $wc_token
			 */
			$wc_token = \WC_Payment_Tokens::get( $update_token_id );
			
			// Do we have a token to update
			if ( ! $wc_token ) {
				wc_paysafe_add_debug_log( 'Could not retrieve the token to update: Token ID: ' . print_r( $update_token_id, true ) );
				throw new \Exception( __( "Could not retrieve the token to update. Please refresh the page and try again.", 'wc_paysafe' ) );
			}
			
			// Only the owner of a token can update it
			if ( $wc_token->get_user_id() != $user_id ) {
				wc_paysafe_add_debug_log( sprintf( 'The token does not belong to the user. Token User ID: %s, User ID: %s' . $wc_token->get_user_id(), $user_id ) );
				
				throw new \Exception( __( "This token does not belong to you.", 'wc_paysafe' ) );
			}
			
			/**
			 * 1. Convert the token to permanent token
			 * 2. Update the Old Vault profile using the information from the new vault profile, or simple do Card|DD update using a token
			 * 3. Update the WC_Token using the information form the new token
			 * 4. Send the result
			 */
			$data_source = new User_Source( $user );
			
			if ( 'cards' == $payment_method ) {
				// Verify the token
				$integration->process_verification( $data_source, $token, 'token' );
			}
			
			$paysafe_token = $integration->update_profile_with_token( $data_source, $token, $wc_token, $payment_method );
			
			$customer_tokens = new Customer_Tokens( $user->ID );
			$customer_tokens->update_wc_token( $wc_token, $paysafe_token );
			
			wp_send_json_success(
				array( 'message' => __( 'Payment method successfully updated.' ) )
			);
		}
		catch ( \Exception $e ) {
			wp_send_json_error(
				array( 'message' => $e->getMessage() )
			);
		}
	}
	
	/**
	 * Runs the add payment method procedure
	 */
	public function add_payment_method() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'wc-paysafe-add-payment-method' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		/**
		 * @var Gateway|\WC_Payment_Gateway $gateway
		 */
		$gateway        = Factories::get_gateway( 'netbanx' );
		$user_id        = (int) wc_clean( wp_unslash( Paysafe::get_field( 'user_id', $_POST, 0 ) ) );
		$token          = wc_clean( wp_unslash( Paysafe::get_field( 'token', $_POST, '' ) ) );
		$payment_method = wc_clean( wp_unslash( strtolower( Paysafe::get_field( 'payment_method', $_POST, '' ) ) ) );
		
		try {
			wc_paysafe_add_debug_log( 'Adding payment method...' );
			
			$user = new \WP_User( $user_id );
			
			if ( 'interac' == $payment_method ) {
				wc_paysafe_add_debug_log( 'Do not support interac payments.' );
				
				throw new \Exception( __( "We don't support interac payments. Please try again using another payment type.", 'wc_paysafe' ) );
			}
			
			$integration = $gateway->get_integration_object();
			
			// The data we will pull information from
			$data_source = new User_Source( $user );
			
			if ( 'cards' == $payment_method ) {
				// Verify the token
				$integration->process_verification( $data_source, $token, 'token' );
			}
			
			// Get Profile
			$paysafe_customer = new Paysafe_Customer( $user );
			$profile_id       = $paysafe_customer->get_vault_profile_id();
			if ( false === $profile_id ) {
				wc_paysafe_add_debug_log( 'No Vault profile found. We will create one.' );
				
				// If the customer does not have a Paysafe profile to hold their tokens, lets create one
				$integration->create_vault_profile( $data_source, $paysafe_customer );
			}
			
			// Convert the single-use token to permanent one
			$permanent_token = $integration->convert_single_use_token( $data_source, $token, $payment_method );
			
			// Create new WC token
			$customer_tokens = new Customer_Tokens( $user->ID );
			$customer_tokens->create_wc_token( $permanent_token );
			
			wp_send_json_success(
				array( 'message' => __( 'Payment method successfully added.' ) )
			);
		}
		catch ( \Exception $e ) {
			wp_send_json_error(
				array( 'message' => $e->getMessage() )
			);
		}
	}
	
	/**
	 * Runs subscriptions change payment method request
	 */
	public function change_payment_method() {
		if ( ! $this->verify_request( wc_clean( wp_unslash( Paysafe::get_field( 'security', $_POST, '' ) ) ), 'wc-paysafe-change-payment-method' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The could not verify the payment collection request. Please refresh the page and try again.', 'wc_paysafe' ) )
			);
		}
		
		/**
		 * @var Gateway|\WC_Payment_Gateway $gateway
		 */
		$gateway         = Factories::get_gateway( 'netbanx' );
		$subscription_id = (int) wc_clean( wp_unslash( Paysafe::get_field( 'order_id', $_POST, 0 ) ) );
		$token           = wc_clean( wp_unslash( Paysafe::get_field( 'token', $_POST, '' ) ) );
		$payment_method  = wc_clean( wp_unslash( strtolower( Paysafe::get_field( 'payment_method', $_POST, '' ) ) ) );
		
		try {
			wc_paysafe_add_debug_log( 'Changing payment method...' );
			
			if ( 'interac' == $payment_method ) {
				wc_paysafe_add_debug_log( 'Do not support interac payments.' );
				
				throw new \Exception( __( "We don't support interac payments. Please try again using another payment type.", 'wc_paysafe' ) );
			}
			
			$subscription = wcs_get_subscription( $subscription_id );
			
			if ( false === $subscription ) {
				throw new \Exception( __( 'The subscription ID passed to the request was not found. Please refresh the page and try again.', 'wc_paysafe' ) );
			}
			
			$user        = $subscription->get_user();
			$integration = $gateway->get_integration_object();
			
			// The data we will pull information from
			$data_source = new Order_Source( $subscription );
			
			if ( 'cards' == $payment_method ) {
				// Verify the token
				$integration->process_verification( $data_source, $token, 'token' );
			}
			
			// Get Profile
			$paysafe_customer = new Paysafe_Customer( $user );
			$profile_id       = $paysafe_customer->get_vault_profile_id();
			if ( false === $profile_id ) {
				wc_paysafe_add_debug_log( 'No Vault profile found. We will create one.' );
				
				// If the customer does not have a Paysafe profile to hold their tokens, lets create one
				$integration->create_vault_profile( $data_source, $paysafe_customer );
			}
			
			// Convert the single-use token to permanent one
			$permanent_token = $integration->convert_single_use_token( $data_source, $token, $payment_method );
			
			// Create new WC token
			$customer_tokens = new Customer_Tokens( $user->ID );
			$wc_token        = $customer_tokens->create_wc_token( $permanent_token );
			
			// Complete the order
			$paysafe_order = new Paysafe_Order( $subscription );
			$paysafe_order->save_token( $wc_token );
			
			wp_send_json_success(
				array( 'message' => __( 'Payment method successfully changed.' ) )
			);
		}
		catch ( \Exception $e ) {
			
			wc_paysafe_add_debug_log( 'Error message in ajax change_payment_method: ' . $e->getMessage() );
			
			wp_send_json_error(
				array( 'message' => $e->getMessage() )
			);
		}
	}
}