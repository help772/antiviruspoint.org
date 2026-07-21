<?php

namespace WcPaysafe\Gateways\Redirect\Payments;

use WcPaysafe\Api_Payments\Data_Sources\Order_Source as Sources_Order;
use WcPaysafe\Api_Payments\Payments\Responses\Payments_Card_Token_Wrapper;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Customer;
use WcPaysafe\Paysafe_Order;
use WcPaysafe\Tokens\Customer_Tokens;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  1.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Return_Links {
	
	public function hooks() {
		add_action( 'woocommerce_api_wc_paysafe_return_default', [ $this, 'process_return_completed' ], 25 );
		add_action( 'woocommerce_api_wc_paysafe_return_completed', [ $this, 'process_return_completed' ], 25 );
		add_action( 'woocommerce_api_wc_paysafe_return_cancelled', [ $this, 'process_return_cancelled' ], 25 );
		add_action( 'woocommerce_api_wc_paysafe_return_failed', [ $this, 'process_return_failed' ], 25 );
		
		// TODO: Check if we need them. For now we are just logging them
		// TODO: Both pairs are using pretty much the same code, small differences. Refactor when needed
		add_action( 'woocommerce_api_wc_paysafe_single_use_handle_return_default', [
			$this,
			'process_single_use_handle_return_completed',
		], 25 );
		add_action( 'woocommerce_api_wc_paysafe_single_use_handle_return_completed', [
			$this,
			'process_single_use_handle_return_completed',
		], 25 );
		add_action( 'woocommerce_api_wc_paysafe_single_use_handle_return_cancelled', [
			$this,
			'process_single_use_handle_return_cancelled',
		], 25 );
		add_action( 'woocommerce_api_wc_paysafe_single_use_handle_return_failed', [
			$this,
			'process_single_use_handle_return_failed',
		], 25 );
	}
	
	public function process_return_completed() {
		wc_paysafe_payments_add_debug_log( 'process_return_completed POST: ' . print_r( $_POST, true ) );
		wc_paysafe_payments_add_debug_log( 'process_return_completed GET: ' . print_r( $_GET, true ) );
		
		$this->process_success_return_link( 'google_pay' );
	}
	
	public function process_return_cancelled() {
		wc_paysafe_payments_add_debug_log( 'process_return_cancelled POST: ' . print_r( $_POST, true ) );
		wc_paysafe_payments_add_debug_log( 'process_return_cancelled GET: ' . print_r( $_GET, true ) );
		
		$this->process_failed_return_link( 'google_pay' );
	}
	
	public function process_return_failed() {
		wc_paysafe_payments_add_debug_log( 'process_return_failed POST: ' . print_r( $_POST, true ) );
		wc_paysafe_payments_add_debug_log( 'process_return_failed GET: ' . print_r( $_GET, true ) );
		
		$this->process_failed_return_link( 'google_pay' );
	}
	
	public function process_single_use_handle_return_completed() {
		wc_paysafe_payments_add_debug_log( 'process_single_use_handle_return_completed POST: ' . print_r( $_POST, true ) );
		wc_paysafe_payments_add_debug_log( 'process_single_use_handle_return_completed GET: ' . print_r( $_GET, true ) );
		
		$this->process_success_return_link( 'single_use_payment' );
	}
	
	public function process_single_use_handle_return_cancelled() {
		wc_paysafe_payments_add_debug_log( 'process_single_use_handle_return_cancelled POST: ' . print_r( $_POST, true ) );
		wc_paysafe_payments_add_debug_log( 'process_single_use_handle_return_cancelled GET: ' . print_r( $_GET, true ) );
		
		$this->process_failed_return_link( 'single_use_payment' );
	}
	
	public function process_single_use_handle_return_failed() {
		wc_paysafe_payments_add_debug_log( 'process_single_use_handle_return_failed POST: ' . print_r( $_POST, true ) );
		wc_paysafe_payments_add_debug_log( 'process_single_use_handle_return_failed GET: ' . print_r( $_GET, true ) );
		
		$this->process_failed_return_link( 'single_use_payment' );
	}
	
	/**
	 * @param string $payment_handle_type values: 'google_pay', 'single_use_payment'
	 *
	 * @return void
	 */
	public function process_success_return_link( $payment_handle_type = 'google_pay' ) {
		$order_id = wc_clean( Paysafe::get_field( 'paysafe_order', $_GET, '' ) );
		
		if ( ! $order_id ) {
			wc_add_notice( __( 'Incorrectly formatted return URL. Please try again.', 'wc_paysafe' ) );
			
			wp_redirect( wc_get_cart_url() );
			exit;
		}
		
		wc_paysafe_payments_add_debug_log( 'return_link: success for ' . $payment_handle_type );
		
		$order = wc_get_order( (int) $order_id );
		
		if ( ! $order ) {
			wc_add_notice( __( 'Incorrectly formatted return URL. No order provided. Please try again.', 'wc_paysafe' ) );
			
			wp_redirect( wc_get_cart_url() );
			exit;
		}
		
		$payment_handle_id = $this->get_order_payment_handle_id( $order, $payment_handle_type );
		
		if ( ! $payment_handle_id ) {
			wc_add_notice( __( 'Could not complete the payment process. Missing payment token', 'wc_paysafe' ), 'error' );
			
			wp_redirect( wc_get_cart_url() );
			exit;
		}
		
		// The transaction is successful, so any errors from here on should only be displayed as notices
		try {
			$gateway = Factories::get_gateway( 'paysafe_checkout_payments' );
			
			/**
			 * @var \WcPaysafe\Gateways\Redirect\Payments\Processes $processes
			 */
			$processes = $gateway->get_integration_object();
			
			$payment_type = 'CARD';
			$data_source  = new Sources_Order( $order );
			$data_source->set_is_initial_payment( true );
			$data_source->set_using_saved_token( false );
			
			$payment_handle = $this->get_payment_handle( $order, $payment_handle_id );
			
			wc_paysafe_payments_add_debug_log( 'return_link: $payment_handle ' . print_r( $payment_handle->get_data(), true ) );
			
			if ( ! $payment_handle->get_id() || 'PAYABLE' != strtoupper( $payment_handle->get_status() ) ) {
				throw new \Exception( __( 'Could not complete the payment process. Invalid payment token', 'wc_paysafe' ) );
			}
			
			$token = $payment_handle->get_payment_token();
			
			// Take the payment
			$payment = $processes->process_token_transaction( $data_source, $token, $payment_type );
			
			wc_paysafe_payments_add_debug_log( 'return_link: Payment response ' . print_r( $payment, true ) );
			
			$response_processor = Factories::load_response_processor( $payment, 'checkout_payments' );
			$response_processor->process_payment_response( $order, $token );
			
			// We can set the last payment response here.
			// The response contains payment info, so we may need it further down
			$data_source->set_last_payment_response( $payment );
			
			// Delete the payment handle ID
			$this->delete_order_payment_handle_id( $order, $payment_handle_type );
			
			if ( $order->is_paid() || 'on-hold' == $order->get_status() ) {
				wp_redirect( $gateway->get_return_url( $order ) );
				exit;
			}
		}
		catch ( \Exception $e ) {
			wc_paysafe_payments_add_debug_log( 'return_link: The order payment failed. Message: ' . $e->getMessage() );
			
			$notice = sprintf( __( 'The order payment failed. Message: %s' ), $e->getMessage() );
			
			wc_add_notice( $notice, 'error' );
			
			wp_redirect( wc_get_cart_url() );
			exit;
		}
	}
	
	/**
	 * @param string $payment_handle_type values: 'google_pay', 'single_use_payment'
	 *
	 * @return void
	 */
	public function process_failed_return_link( $payment_handle_type = 'google_pay' ) {
		$order_id = wc_clean( Paysafe::get_field( 'paysafe_order', $_GET, '' ) );
		
		if ( ! $order_id ) {
			wc_add_notice( __( 'Incorrectly formatted return URL. Please try again.', 'wc_paysafe' ) );
			
			wp_redirect( wc_get_cart_url() );
			exit;
		}
		
		wc_paysafe_payments_add_debug_log( 'process_return_failed: failed for ' . $payment_handle_type );
		
		$order = wc_get_order( (int) $order_id );
		if ( ! $order ) {
			wc_add_notice( __( 'Incorrectly formatted return URL. No order found. Please try again.', 'wc_paysafe' ) );
			
			wp_redirect( wc_get_cart_url() );
			exit;
		}
		
		$payment_handle_id = $this->get_order_payment_handle_id( $order, $payment_handle_type );
		
		try {
			$payment_handle = $this->get_payment_handle( $order, $payment_handle_id );
			
			wc_paysafe_payments_add_debug_log( 'process_return_failed: $payment_handle ' . print_r( $payment_handle->get_data(), true ) );
			$message = 'AUTH_FAILED' == $payment_handle->statusReason ? __( 'Authentication Failed', 'wc_paysafe' ) : __( 'The payment failed. Please try again.', 'wc_paysafe' );
			
			if ( ! empty( $payment_handle->get_authentication() ) && ! empty( $payment_handle->authentication->error ) ) {
				$error_message = $payment_handle->authentication->error->code . ': ' . $payment_handle->authentication->error->message;
				
				if ( $payment_handle->authentication->error->message != $payment_handle->authentication->error->description ) {
					$error_message .= '. ' . $payment_handle->authentication->error->description;
				}
				
				$message = sprintf( __( 'The payment failed. Error: %s' ), $error_message );
			}
			
			wc_add_notice( $message, 'error' );
		}
		catch ( \Exception $e ) {
			wc_paysafe_payments_add_debug_log( 'process_return_failed: The order payment failed. Message: ' . $e->getMessage() );
			
			$message = sprintf( __( 'The payment failed. We could not retrieve the exact reason for the failure. Problem message: %s', 'wc_paysafe' ), $e->getMessage() );
			
			wc_add_notice( $message, 'error' );
		}
		
		if ( $payment_handle_id ) {
			$this->delete_order_payment_handle_id( $order, $payment_handle_type );
		}
		
		$order->update_status( 'failed' );
		
		wp_redirect( wc_get_cart_url() );
		exit;
	}
	
	public function get_order_payment_handle_id( $order, $payment_handle_type ) {
		$paysafe_order     = new Paysafe_Order( $order );
		$payment_handle_id = '';
		
		if ( 'google_pay' == $payment_handle_type ) {
			$payment_handle_id = $paysafe_order->get_google_pay_payment_handle_id();
		} elseif ( 'single_use_payment' == $payment_handle_type ) {
			$payment_handle_id = $paysafe_order->get_single_use_payment_handle_id();
		}
		
		return $payment_handle_id;
	}
	
	public function delete_order_payment_handle_id( $order, $payment_handle_type ) {
		$paysafe_order     = new Paysafe_Order( $order );
		$payment_handle_id = '';
		
		if ( 'google_pay' == $payment_handle_type ) {
			$payment_handle_id = $paysafe_order->delete_google_pay_payment_handle_id();
		} elseif ( 'single_use_payment' == $payment_handle_type ) {
			$payment_handle_id = $paysafe_order->delete_single_use_payment_handle_id();
		}
		
		return $payment_handle_id;
	}
	
	public function get_payment_handle( $order, $payment_handle_id ) {
		$gateway = Factories::get_gateway( 'paysafe_checkout_payments' );
		
		$processes = $gateway->get_integration_object();
		
		$payment_type = 'CARD';
		$data_source  = new Sources_Order( $order );
		$data_source->set_is_initial_payment( true );
		$data_source->set_using_saved_token( false );
		
		/**
		 * @var \WcPaysafe\Gateways\Redirect\Payments\Processes $processes
		 */
		$api_client              = $processes->get_api_client( $data_source, $payment_type );
		$payment_handles_request = $api_client->get_payment_handles_service()->payment_handles_request();
		$payment_handle          = $payment_handles_request->get( $payment_handle_id );
		
		return $payment_handle;
	}
}