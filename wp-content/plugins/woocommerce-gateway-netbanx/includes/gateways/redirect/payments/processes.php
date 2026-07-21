<?php

namespace WcPaysafe\Gateways\Redirect\Payments;

use WcPaysafe\Api_Payments\Client;
use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source as Sources_Order;
use WcPaysafe\Api_Payments\Data_Sources\User_Source;
use WcPaysafe\Api_Payments\Payments\Responses\Payments_Card_Token_Wrapper;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Abstracted_Gateway;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Cart_Checkout_Helpers;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Payment_Form;
use WcPaysafe\Paysafe_Customer;
use WcPaysafe\Paysafe_Order;
use WcPaysafe\Tokens\Customer_Tokens;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
// TODO: Call it Process, like checkout->process->transaction, checkout->process->create_profile
// This class is way too complicated than it should be. It basically has a hand in most of the processes of the Gateway
// Can we refactor this to split the processes into more manageable/testable classes?
class Processes {
	
	/**
	 * @var Gateway
	 */
	private $gateway = null;
	private $api_client = null;
	
	/**
	 * Constructor of the class
	 *
	 * @since 2.0
	 *
	 * @param Gateway|Abstracted_Gateway $gateway
	 */
	public function __construct( Abstracted_Gateway $gateway ) {
		$this->set_gateway( $gateway );
	}
	
	/**
	 * Sets the gateway class to a class variable
	 *
	 * @since 2.0
	 *
	 * @param Gateway $gateway
	 */
	private function set_gateway( Abstracted_Gateway $gateway ) {
		$this->gateway = $gateway;
	}
	
	/**
	 * Returns the variable with the gateway class
	 *
	 * @since 2.0
	 *
	 * @return Gateway
	 * @throws \InvalidArgumentException
	 */
	public function get_gateway() {
		return $this->gateway;
	}
	
	/**
	 * Returns the settings for this integration
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function get_settings() {
		$obj = new Settings( $this->get_gateway() );
		
		return $obj->get_settings();
	}
	
	/**
	 * Checks to see if we have the integration set with the minimum required information for operations
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( '' == $this->get_gateway()->get_option( 'api_user_name' )
		     || '' == $this->get_gateway()->get_option( 'api_password' )
		     || '' == $this->get_gateway()->get_option( 'single_use_token_user_name' )
		     || '' == $this->get_gateway()->get_option( 'single_use_token_password' )
		     || ( '' == $this->get_gateway()->get_account_id()
		          && '' == $this->get_gateway()->get_account_id( null, 'directdebit' ) )
		) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param Sources_Order|User_Source $sourse
	 * @param string                    $payment_type card|applePay
	 *
	 * @throws \Exception
	 * @return null|Client
	 */
	public function get_api_client( $sourse = null, $payment_type = 'card' ) {
		if ( null == $this->api_client ) {
			
			$this->api_client = Factories::get_api_client( $this->get_gateway(), $sourse, $payment_type );
		}
		
		return $this->api_client;
	}
	
	/**
	 * Process Payment of this integration
	 *
	 * @since   3.3.0
	 * @version 3.7.0
	 *
	 * @param $order_id
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function process_payment( $order_id ) {
		if ( $this->is_using_saved_payment_method() ) {
			return $this->process_payment_with_saved_payment_method( $order_id );
		}
		
		return $this->process_payment_with_new_payment_method( $order_id );
	}
	
	/**
	 * Runs the process when the customer used a saved payment method.
	 *
	 * @since 3.7.0
	 *
	 * @param $order_id
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function process_payment_with_saved_payment_method( $order_id ) {
		$order = wc_get_order( (int) $order_id );
		
		if ( wc_paysafe_is_change_method_page() ) {
			// We are changing the payment method and customer is using a saved token
			// just change the method and direct the customer to the Subs view page
			$this->handle_change_payment_method( $order );
			
			return array(
				'result'   => 'success',
				'redirect' => $order->get_view_order_url(),
			);
		}
		
		$payment_method = $this->get_gateway()->id;
		$wc_token_id    = wc_clean( wp_unslash( $_POST[ 'wc-' . $payment_method . '-payment-token' ] ) );
		$wc_token       = \WC_Payment_Tokens::get( $wc_token_id );
		
		wc_paysafe_payments_add_debug_log( 'Paying for order: ' . $order_id );
		wc_paysafe_payments_add_debug_log( 'Paying with saved token' );
		
		// We require token to process a payment
		if ( ! $wc_token || $wc_token->get_user_id() !== get_current_user_id() ) {
			WC()->session->set( 'refresh_totals', true );
			throw new \Exception( __( 'Invalid payment method. Please try again or use a new card number.', 'wc_paysafe' ) );
		}
		
		$paysafe_order = new Paysafe_Order( $order );
		
		if ( 0 < $order->get_total() || ! $paysafe_order->is_pre_order_with_tokenization() ) {
			
			wc_paysafe_payments_add_debug_log( 'Processing token transaction' );
			
			$cvv = wc_clean( wp_unslash( $_POST[ $payment_method . '-card-cvv' ] ) );
			
			$data_source = new Sources_Order( $order );
			$data_source->set_is_initial_payment( true );
			$data_source->set_using_saved_token( true );
			$data_source->set_cvv( $cvv );
			
			$customer = new Paysafe_Customer( $order->get_user() );
			
			// 1. Get the customer payment handles
			$api_client               = $this->get_api_client( $data_source, 'card' );
			$customer_service         = $api_client->get_customers_service()->customers_request();
			$customer_profile_handles = $customer_service->get_customer_by_customer_id( $customer->get_payments_customer_id(), [ 'fields' => 'paymenthandles' ] );
			
			// 2. Find the matching payment handle
			$match_handle = $this->match_payment_handle_to_token( $customer_profile_handles->get_payment_handles(), $wc_token );
			
			if ( ! $match_handle ) {
				throw new \Exception( __( "Can't find the payment token in your Paysafe saved tokens.", 'wc_paysafe' ) );
			}
			
			// 3. Create a Single Use Payment Handle Token - Pass the payment handle as paymentHandleTokenFrom and the CVV
			try {
				$payment_handles_service   = $api_client->get_payment_handles_service();
				$single_use_handle_request = $payment_handles_service->payment_handles_request();
				$single_use_handle         = $single_use_handle_request->create_single_use_payment_handle(
					$single_use_handle_request->get_request_builder( $data_source )->single_use_handle_from_parameters( $match_handle->paymentHandleToken, $order->get_total(), $match_handle->paymentType )
				
				);
				
				if ( 'REDIRECT' == $single_use_handle->get_action() ) {
					$links = $single_use_handle->get_links();
					if ( $links ) {
						$redirect_url = '';
						
						foreach ( $links as $link ) {
							if ( 'redirect_payment' == $link->rel ) {
								$redirect_url = $link->href;
								break;
							}
						}
						
						wc_paysafe_payments_add_debug_log( 'GooglePay payment handle: $redirect_url: ' . print_r( $redirect_url, true ) );
						
						if ( ! $redirect_url ) {
							throw new \Exception( __( 'There was no 3DS authentication link provided. Please refresh the page and try again.', 'wc_paysafe' ) );
						}
						
						$paysafe_order->save_single_use_payment_handle_id( $single_use_handle->get_id() );
						
						return array(
							'result'   => 'success',
							'redirect' => $redirect_url,
						);
					}
				}
			}
			catch ( \Exception $e ) {
				throw new \Exception( sprintf( __( "Error occurred while trying to get your payment token. Error: %s", 'wc_paysafe' ), $e->getMessage() ) );
			}
			
			// 4. Take the payment with the single-use handle token
			$payment = $this->process_token_transaction( $data_source, $single_use_handle->get_payment_token(), 'card' );
			
			$response_processor = Factories::load_response_processor( $payment, 'checkout_payments' );
			$response_processor->process_payment_response( $order, $wc_token );
		}
		
		// Save the token details to the Order and any subscriptions in it.
		// Since this is a customer initiated payment the transaction ID is also saved to the subscription for future use
		$paysafe_order->save_token( $wc_token, $payment->get_id() );
		
		// Debug log
		wc_paysafe_payments_add_debug_log( 'Payment completed' );
		
		Cart_Checkout_Helpers::empty_cart();
		
		return array(
			'result'   => 'success',
			'redirect' => $this->get_gateway()->get_return_url( $order ),
		);
	}
	
	/**
	 * Runs the process when the customer is using a new method and the layover needs to be displayed
	 *
	 * @since 3.7.0
	 *
	 * @param $order_id
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function process_payment_with_new_payment_method( $order_id ) {
		
		
		// TODO: If Google Pay payment
		// 1. Generate Payment Handle with Google data
		// 2. Get the result and redirect if needs 3DS
		// 3. Process the payment, if it does not need 3DS
		
		if ( ! empty( $_POST['payment_request_type'] ) ) {
			return $this->process_google_payment( $order_id );
		}
		
		$order = wc_get_order( (int) $order_id );
		
		// Return the layover params unless directed otherwise
		if ( apply_filters( 'wc_paysafe_layover_on_checkout', true, $order ) && ( is_checkout() && ! is_checkout_pay_page() ) ) {
			$api_client         = $this->get_api_client( $order, 'card' );
			$checkoutjs_service = $api_client->get_checkoutjs_service();
			$params             = $checkoutjs_service->get_iframe_order_parameters( $order );
			
			// Add the process action
			$params['processAction'] = 'process_payment';
			
			return array(
				'result'                => 'success',
				'paymentData'           => $params,
				'process_payment_nonce' => wp_create_nonce( 'paysafe_checkout_process-payment' ),
			);
		}
		
		// We are directing to the pay page only when we don't want to directly display the layover
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}
	
	public function process_google_payment( $order_id ) {
		$order         = wc_get_order( (int) $order_id );
		$paysafe_order = new Paysafe_Order( $order );
		
		$payment_type = 'CARD';
		$data_source  = new Sources_Order( $order );
		$api_client   = $this->get_api_client( $data_source, $payment_type );
		$token_data   = wc_clean( ! empty( $_POST['google_pay_payment_data'] ) ? $_POST['google_pay_payment_data'] : [] );
		
		$payment_handles = $api_client->get_payment_handles_service()->payment_handles_request();
		
		try {
			$single_use_handle = $payment_handles->create_single_use_payment_handle(
				$payment_handles->get_request_builder( $data_source )->google_payment_parameters( $token_data, $order->get_total() )
			);
			
			wc_paysafe_payments_add_debug_log( 'GooglePay payment handle: $single_use_handle: ' . print_r( $single_use_handle, true ) );
			
			if ( 'REDIRECT' == $single_use_handle->get_action() && 'INITIATED' == $single_use_handle->get_status() ) {
				$links = $single_use_handle->get_links();
				if ( $links ) {
					$redirect_url = '';
					
					foreach ( $links as $link ) {
						if ( 'redirect_payment' == $link->rel ) {
							$redirect_url = $link->href;
							break;
						}
					}
					
					wc_paysafe_payments_add_debug_log( 'GooglePay payment handle: $redirect_url: ' . print_r( $redirect_url, true ) );
					
					if ( ! $redirect_url ) {
						throw new \Exception( __( 'There was no 3DS authentication link provided. Please refresh the page and try again.', 'wc_paysafe' ) );
					}
					
					$paysafe_order->save_google_pay_payment_handle_id( $single_use_handle->get_id() );
					
					return array(
						'result'   => 'success',
						'redirect' => $redirect_url,
					);
				}
			} elseif ( 'PAYABLE' == $single_use_handle->get_status() ) {
				if ( 0 < $order->get_total() ) {
					$data_source->set_is_initial_payment( true );
					$data_source->set_using_saved_token( false );
					
					$token = $single_use_handle->get_payment_token();
					
					// Take the payment
					$payment = $this->process_token_transaction( $data_source, $token, $payment_type );
					
					wc_paysafe_payments_add_debug_log( 'Payment response ' . print_r( $payment, true ) );
					
					$response_processor = Factories::load_response_processor( $payment, 'checkout_payments' );
					$response_processor->process_payment_response( $order, $token );
					
					// We can set the last payment response here.
					// The response contains payment info, so we may need it further down
					$data_source->set_last_payment_response( $payment );
					
					// The transaction is successful, so any errors from here on should only be displayed as notices
					try {
						$save_to_account = false;
						// Save the tokens
						if ( ( 0 < $order->get_customer_id() && true == $save_to_account )
						     // TODO: We need a way to save a token without the customer selecting it in the payment screen
						     || $paysafe_order->contains_subscription()
						) {
							// Debug log
							wc_paysafe_payments_add_debug_log( 'Saving the payment as a token' );
							
							// Get or create a Vault profile for the customer
							$paysafe_customer = new Paysafe_Customer( $order->get_user() );
							
							// Convert the single-use token to permanent one
							$card_token_wrapper = new Payments_Card_Token_Wrapper( $payment );
							
							$customer_tokens = new Customer_Tokens( $order->get_user_id(), 'paysafe_checkout_payments' );
							
							// Save the customer_id to the customer itself. It will be used to recover the customer details
							$paysafe_customer->save_payments_customer_id( $card_token_wrapper->get_customer_id() );
							
							// If we have a multiUseToken and we don't have it saved already
							if ( $card_token_wrapper->get_payment_token()
							     && ! $customer_tokens->get_token_from_value( $card_token_wrapper->get_payment_token() ) ) {
								$wc_token = $customer_tokens->create_wc_token( $card_token_wrapper );
								
								// Save the token to the order and possibly subscription
								$paysafe_order->save_token( $wc_token );
							}
						}
					}
					catch ( \Exception $e ) {
						wc_paysafe_payments_add_debug_log( 'Google Pay: Error converting the payment method to a token: ' . $e->getMessage() );
						
						$notice = $e->getMessage();
					}
				}
				
				// Debug log
				wc_paysafe_payments_add_debug_log( 'Payment completed' );
				
				// Empty cart
				Cart_Checkout_Helpers::empty_cart();
				
				return array(
					'result'   => 'success',
					'redirect' => $this->gateway->get_return_url( $order ),
				);
			}
		}
		catch ( \Exception $e ) {
			if ( $order ) {
				// Add note to the order, so the merchant knows what happened.
				$order->update_status( 'failed' );
				$order->add_order_note( sprintf( __( 'Google Pay: Paysafe failed to process your payment. Result: %s %s', 'wc_paysafe' ), $e->getCode(), $e->getMessage() ) );
			}
			
			wc_paysafe_payments_add_debug_log( sprintf( 'Google Pay: Error processing Ajax payment: %s %s', $e->getCode(), $e->getMessage() ) );
			
			$error_message = $e->getMessage();
			
			if ( $e->getCode() ) {
				$error_message .= ' ' . sprintf( __( 'Error code: %s', 'wc_paysafe' ) . $e->getCode() );
			}
			
			wc_add_notice( $error_message, 'error' );
			
			return array(
				'result'   => 'failure',
				'messages' => $error_message,
			);
		}
	}
	
	/**
	 * @param \WC_Subscription|\WC_Order $subscription
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function handle_change_payment_method( $subscription ) {
		$payment_method = $this->get_gateway()->id;
		$wc_token_id    = wc_clean( wp_unslash( $_POST[ 'wc-' . $payment_method . '-payment-token' ] ) );
		$wc_token       = \WC_Payment_Tokens::get( $wc_token_id );
		
		wc_paysafe_payments_add_debug_log( 'Changing payment method for order order: ' . WC_Compatibility::get_order_id( $subscription ) );
		
		// We require token to process a payment
		if ( ! $wc_token || $wc_token->get_user_id() !== get_current_user_id() ) {
			WC()->session->set( 'refresh_totals', true );
			throw new \Exception( __( 'Invalid payment method. Please try again or use a new payment method.', 'wc_paysafe' ) );
		}
		
		// Complete the order
		$paysafe_order = new Paysafe_Order( $subscription );
		$paysafe_order->save_token( $wc_token );
		
		// Debug log
		wc_paysafe_payments_add_debug_log( 'Payment Method was changed successfully' );
		
		return true;
	}
	
	/**
	 * Receipt Page output for this integration
	 *
	 * @param $order_id
	 */
	public function receipt_page( $order_id ) {
		try {
			$order = wc_get_order( $order_id );
			
			echo Formatting::kses_form_html( $this->string_pay_with_form_below() );
			echo $this->load_pay_page_payment_form( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		catch ( \Exception $e ) {
			$this->error_notification_message( $e->getMessage() );
		}
	}
	
	/**
	 * Process a token transaction
	 *
	 * @since 3.3.0
	 *
	 * @param Sources_Order|User_Source|Data_Source_Interface $data_source
	 * @param string                                          $token
	 * @param string                                          $payment_type card
	 * @param float                                           $amount
	 *
	 * @throws \Exception
	 * @return \WcPaysafe\Api_Payments\Payments\Responses\Authorizations
	 */
	public function process_token_transaction( $data_source, $token, $payment_type, $amount = null, $force_auth_only = false ) {
		$payment_type = strtolower( $payment_type );
		$api_client   = $this->get_api_client( $data_source, $payment_type );
		
		$transaction_service = $api_client->get_payments_service()->authorizations_request();
		
		$payment = $transaction_service->process(
			$transaction_service->get_request_builder( $data_source )->get_token_transaction_parameters( $token, $amount, $force_auth_only )
		);
		
		// Remove?
		wc_paysafe_payments_add_debug_log( 'process_token_transaction: Payment response passed ' );
		
		return $payment;
	}
	
	/**
	 * Checks if payment is done by saved card
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function is_using_saved_payment_method() {
		$payment_method = $this->get_gateway()->id;
		
		return ( isset( $_POST[ 'wc-' . $payment_method . '-payment-token' ] ) && 'new' !== $_POST[ 'wc-' . $payment_method . '-payment-token' ] );
	}
	
	/**
	 * TODO: Needs attention. Save to account moved to layover
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function load_pay_page_payment_form( $order ) {
		$api_client         = $this->get_api_client( $order, 'card' );
		$checkoutjs_service = $api_client->get_checkoutjs_service();
		
		$paysafe_args = $checkoutjs_service->get_iframe_order_parameters( $order );
		
		// Add the process action
		$paysafe_args['processAction'] = 'process_payment';
		
		wp_localize_script( 'paysafe-checkout-v2', 'paysafe_layover_params', $paysafe_args );
		
		$submit_button_text = apply_filters( 'wc_paysafe_checkoutjs_pay_button_text', __( 'Pay the order', 'wc_paysafe' ) );
		
		$payment_form = new Payment_Form( $this->get_gateway() );
		$payment_form->output_checkoutjs_iframe_payment_block( $submit_button_text );
	}
	
	/**
	 * Adds the iframe error notification
	 *
	 * @since 3.3.0
	 *
	 * @param $error_message
	 */
	public function error_notification_message( $error_message ) {
		// Any exception is logged and flags a notice
		wc_paysafe_payments_add_debug_log( 'Paysafe-checkout error: ' . $error_message );
		
		$message = Formatting::kses_form_html( sprintf(
			__( 'Error generating the payment form. Please refresh the page and try again.
			 		If error persists, please contact the administrator. Error message: %s ', 'wc_paysafe' ),
			$error_message
		) );
		
		echo '<p class="paysafe-iframe-error">' . $message . '</p>'; // WPCS: XSS ok.
	}
	
	/**
	 * The string on Pay page, prompting user to pay with the form below
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function string_pay_with_form_below() {
		return apply_filters( 'wc_paysafe_checkout_js_before_iframe', __( 'Thank you for your order. Please click on the button below to pay for your order.', 'wc_paysafe' ) );
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 * @param null      $amount
	 * @param string    $reason
	 *
	 * @throws \Exception
	 * @return \WcPaysafe\Api_Payments\Refunds\Responses\Refunds
	 */
	public function process_refund( $order, $amount = null, $reason = '' ) {
		$data_source = new Sources_Order( $order );
		$amount      = wc_format_decimal( $amount );
		
		$paysafe_order = new Paysafe_Order( $order );
		$payment_type  = $paysafe_order->get_payment_type();
		
		// TODO: Only card refunds are processed for now. We can check to see other types as well.
		
		// We need to have at least one settlement
		$settlement_ids = $paysafe_order->get_settlement_ids();
		$transaction_id = array_pop( $settlement_ids );
		if ( empty( $transaction_id ) ) {
			throw new \Exception( sprintf( __( "We can't refund this transaction because we don't have settlement ID.", 'wc_paysafe' ) ) );
		}
		
		$client_api = $this->get_api_client( $data_source, 'card' );
		
		$authorization        = $client_api->get_payments_service()->authorizations_request()->get( array( 'id' => $paysafe_order->get_authorization_id() ) );
		$authorization_amount = wc_format_decimal( $authorization->get_amount() );
		$available_to_settle  = wc_format_decimal( $authorization->get_available_to_settle() );
		
		if ( 'completed' != strtolower( $authorization->get_status() ) ) {
			throw new \Exception( sprintf( __( "We can only refund transactions with 'COMPLETED' status. This transaction status is: %s", 'wc_paysafe' ), $authorization->get_status() ) );
		}
		
		if ( $authorization_amount == $available_to_settle ) {
			throw new \Exception( __( "We can only refund transactions with settled amount. There are no settlements for processed for this transaction.", 'wc_paysafe' ) );
		}
		
		$available_to_refund = wc_format_decimal( Formatting::format_amount_from_cent( $authorization_amount - $available_to_settle ) );
		
		if ( $available_to_refund < $amount ) {
			throw new \Exception( sprintf( __( "The amount to refund is more than the amount allowed to be refunded for this transaction. You are allowed to refund up to %s%s", 'wc_paysafe' ), $available_to_refund ) );
		}
		
		$refund_service = $client_api->get_refunds_service()->refunds_request();
		$refund         = $refund_service->process( $refund_service->get_request_builder( $data_source )->refund_parameters( $amount, $reason ) );
		
		$response_processor = Factories::load_response_processor( $refund, 'checkout_payments' );
		$response_processor->process_refund( $order, $amount );
		
		return $refund;
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 * @param float     $amount
	 *
	 * @throws \Exception
	 */
	public function process_capture( $order, $amount ) {
		$data_source = new Sources_Order( $order );
		$client_api  = $this->get_api_client( $data_source, 'card' );
		
		// Check that the captured amount is
		$settlement_service = $client_api->get_settlements_service()->settlements_request();
		$settlement         = $settlement_service->process( $settlement_service->get_request_builder( $data_source )->settlement_parameters( $amount ) );
		
		$response_processor = Factories::load_response_processor( $settlement, 'checkout_payments' );
		$response_processor->process_settlement( $data_source->get_source(), $amount );
	}
	
	/**
	 * Transfers the token from failed sub to a renewal
	 *
	 * @since 3.3.0
	 *
	 * @param $subscription
	 * @param $renewal_order
	 */
	public function changed_failing_payment_method( $subscription, $renewal_order ) {
		$ps_subscription = new Paysafe_Order( $subscription );
		$ps_renewal      = new Paysafe_Order( $renewal_order );
		$ps_subscription->save_order_profile_token( $ps_renewal->get_order_profile_token() );
		$ps_subscription->save_payments_merchant_customer_id( $ps_renewal->get_payments_merchant_customer_id() );
		$ps_subscription->save_payments_customer_id( $ps_renewal->get_payments_customer_id() );
	}
	
	/**
	 * Don't transfer Paysafe meta to resubscribe orders.
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $resubscribe_order The order created for the customer to resubscribe to the old expired/cancelled subscription
	 *
	 * @return void
	 */
	public function remove_renewal_order_meta( $resubscribe_order ) {
		$paysafe_order = new Paysafe_Order( $resubscribe_order );
		$paysafe_order->delete_order_profile_token();
		$paysafe_order->delete_payments_merchant_customer_id();
		$paysafe_order->delete_payments_customer_id();
	}
	
	/**
	 * Processes a schedules payment
	 *
	 * @since 3.3.0
	 *
	 * @param           $amount_to_charge
	 * @param \WC_Order $renewal_order
	 */
	public function scheduled_subscription_payment_request( $amount_to_charge, $renewal_order ) {
		try {
			wc_paysafe_payments_add_debug_log( 'Scheduled payment: ' . print_r( WC_Compatibility::get_order_id( $renewal_order ), true ) );
			
			$paysafe_order   = new Paysafe_Order( $renewal_order );
			$customer_tokens = new Customer_Tokens( $renewal_order->get_customer_id(), 'paysafe_checkout_payments' );
			
			// Get the token used
			$wc_token = $customer_tokens->get_token_from_value( $paysafe_order->get_order_profile_token() );
			
			if ( ! $wc_token ) {
				// Still no token? Bail
				throw new \Exception( __( 'Payment token is missing. The subscription order cannot be charged.', 'wc_paysafe' ) );
			}
			
			$data_source = new Sources_Order( $renewal_order );
			$data_source->set_is_initial_payment( false );
			$data_source->set_using_saved_token( true );
			
			$payment = $this->process_token_transaction(
				$data_source,
				$wc_token->get_token(),
				'Paysafe_Payments_Card' == $wc_token->get_type() ? 'card' : '',
				$amount_to_charge
			);
			
			wc_paysafe_payments_add_debug_log( 'Scheduled payment response: ' . print_r( $payment->get_data(), true ) );
			
			// Process the order from the response
			$response_processor = Factories::load_response_processor( $payment, 'checkout_payments' );
			$response_processor->process_payment_response( $renewal_order );
		}
		catch ( \Exception $e ) {
			$renewal_order->update_status( 'failed', $e->getMessage() );
			
			// Debug log
			wc_paysafe_payments_add_debug_log( $e->getMessage() );
		}
	}
	
	/**
	 * Charge the payment on order release
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_pre_order_release_payment( \WC_Order $order ) {
		try {
			$paysafe_order   = new Paysafe_Order( $order );
			$customer_tokens = new Customer_Tokens( $order->get_customer_id(), 'paysafe_checkout_payments' );
			
			$wc_token = $customer_tokens->get_token_from_value( $paysafe_order->get_order_profile_token() );
			
			if ( ! $wc_token ) {
				throw new \Exception( __( 'Payment token is missing. The Pre-order cannot be charged.', 'wc_paysafe' ) );
			}
			
			$data_source = new Sources_Order( $order );
			$data_source->set_is_initial_payment( false );
			$data_source->set_using_saved_token( true );
			
			$payment = $this->process_token_transaction(
				new Sources_Order( $order ),
				$wc_token->get_token(),
				'Paysafe_Payments_Card' == $wc_token->get_type() ? 'card' : ''
			);
			
			$response_processor = Factories::load_response_processor( $payment, 'checkout_payments' );
			$response_processor->process_payment_response( $order );
		}
		catch ( \Exception $e ) {
			$order->add_order_note( $e->getMessage(), 'error' );
		}
	}
	
	/**
	 * Delete a customer profile from both PayTrace and store systems.
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Payment_Token $wc_token The ID of the save, in the database, profile
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	function delete_profile_token( \WC_Payment_Token $wc_token ) {
		
		$customers_service = $this->get_api_client()->get_customers_service();
		
		/**
		 * @var \WC_Payment_Token_Paysafe_Payments_Card $wc_token
		 */
		if ( ! $wc_token instanceof \WC_Payment_Token_Paysafe_Payments_Card ) {
			return;
		}
		
		$method_service = $customers_service->payment_handles_request();
		
		$customer_id = $wc_token->get_customer_id();
		
		$single_use_token = false;
		// Create a single-use token to get the customer payment handles
		try {
			$single_use_token_request = $customers_service->single_use_tokens_request();
			$single_use_token         = $single_use_token_request->create( $customer_id,
				$single_use_token_request->get_request_builder( new User_Source( new \WP_User( $wc_token->get_user_id() ) ) )->get_single_use_token_parameters()
			);
		}
		catch ( \Exception $e ) {
		
		}
		
		if ( ! $single_use_token || empty( $single_use_token->get_payment_handles() ) ) {
			return false;
		}
		
		// Looks through the Payment Handles of the customer and find the card with the matching type, last4, expiry date
		$match_payment_handle = $this->match_payment_handle_to_token( $single_use_token->get_payment_handles(), $wc_token );
		$payment_handle_id    = $match_payment_handle ? $match_payment_handle->id : false;
		
		if ( ! $payment_handle_id ) {
			return false;
		}
		
		try {
			// Delete the matched card
			$method_service->delete( $customer_id, $payment_handle_id );
		}
		catch ( \Exception $e ) {
			return false;
		}
		
		return true;
	}
	
	public function match_payment_handle_to_token( $payment_handles, $token ) {
		$matched_handle = false;
		
		// Match them by token
		foreach ( $payment_handles as $payment_handle ) {
			if ( $payment_handle->paymentHandleToken == $token->get_token() ) {
				$matched_handle = $payment_handle;
				break;
			}
		}
		
		if ( ! $matched_handle ) {
			// Match them by card info
			foreach ( $payment_handles as $payment_handle ) {
				if ( $payment_handle->card->lastDigits == $token->get_last4()
				     && $payment_handle->card->cardExpiry->month == $token->get_expiry_month()
				     && $payment_handle->card->cardExpiry->year == $token->get_expiry_year()
				     && $payment_handle->card->cardType == $token->get_card_type()
				) {
					$matched_handle = $payment_handle;
					break;
				}
			}
		}
		
		return $matched_handle;
	}
	
	/**
	 * Matches the payment handles to the provided payment handle value or tries to match the card to one of the handle cards
	 *
	 * The payment handle we receive during payment does not match the multi_use payment handle value even if a saved card is use
	 * so we need a way to match the card even if the handle token does not match. This is why we try to match all card info to one
	 * of the handles
	 *
	 * @param array     $payment_handles
	 * @param string    $handle_token_value
	 * @param \stdClass $card_value
	 *
	 * @return false|mixed
	 */
	public function match_payment_handle_to_temp_handle( $payment_handles, $handle_token_value, $card_value = null ) {
		$matched_handle = false;
		
		// Match them by token
		foreach ( $payment_handles as $payment_handle ) {
			if ( $payment_handle->paymentHandleToken == $handle_token_value ) {
				$matched_handle = $payment_handle;
				break;
			}
		}
		
		if ( ! $matched_handle && $card_value ) {
			// Match them by card info
			foreach ( $payment_handles as $payment_handle ) {
				if ( $payment_handle->card->lastDigits == $card_value->lastDigits
				     && $payment_handle->card->cardExpiry->month == $card_value->cardExpiry->month
				     && $payment_handle->card->cardExpiry->year == $card_value->cardExpiry->year
				     && $payment_handle->card->cardType == $card_value->cardType
				) {
					$matched_handle = $payment_handle;
					break;
				}
			}
		}
		
		return $matched_handle;
	}
	
	/**
	 * @param Payments_Card_Token_Wrapper $card_token_wrapper
	 * @param \WC_Order                   $order
	 *
	 * @return Payments_Card_Token_Wrapper
	 * @throws \Exception
	 */
	public function maybe_convert_card_to_multi_use_token( $card_token_wrapper, $order ) {
		$data_source = new Order_Source( $order );
		$token       = $card_token_wrapper->get_payment_token();
		$gateway     = Factories::get_gateway( 'paysafe_checkout_payments' );
		$integration = new Processes( $gateway );
		
		if ( $token ) {
			return $card_token_wrapper;
		}
		
		$customer_id = $card_token_wrapper->get_customer_id();
		if ( ! $customer_id ) {
			// Check if we have a customer ID in the profile, this would mean that the customer already has a
			// profile in Paysafe and we can just use it
			$customer_id = isset( $card_token_wrapper->profile->id ) ? $card_token_wrapper->profile->id : '';
			
			// Set the customerId for consistency
			$card_token_wrapper->set_customer_id( $customer_id );
		}
		
		if ( ! $customer_id ) {
			// Create customer using
			$api_client = $integration->get_api_client( $data_source, 'card' );
			
			$customers_request = $api_client->get_customers_service()->customers_request();
			
			$customer_response = $customers_request->create_customer(
				$customers_request->get_request_builder( $data_source )->get_create_customer_from_handle_params( $card_token_wrapper->get_payment_handle_token() )
			);
			
			foreach ( $customer_response->get_payment_handles() as $payment_handle ) {
				if ( 'MULTI_USE' !== $payment_handle->usage ) {
					continue;
				}
				
				$card_token_wrapper->set_multi_use_payment_handle_token( $payment_handle->paymentHandleToken );
				$card_token_wrapper->set_customer_id( $customer_response->get_id() );
				
				if ( ! $card_token_wrapper->get_card() ) {
					$card_token_wrapper->set_card( $payment_handle->card );
				}
				break;
			}
			
			return $card_token_wrapper;
		}
		
		$api_client = $integration->get_api_client( $data_source, 'card' );
		
		$payment_handles_request = $api_client->get_customers_service()->payment_handles_request();
		
		// Get all payment handles and try to match one of them to the used payment method
		$customer_with_handles = $api_client->get_customers_service()->customers_request()->get_customer_by_customer_id( $customer_id, [ 'fields' => 'paymenthandles' ] );
		$matched_handle        = $this->match_payment_handle_to_temp_handle( $customer_with_handles->get_payment_handles(), $card_token_wrapper->get_payment_handle_token(), $card_token_wrapper->get_card() );
		
		if ( $matched_handle ) {
			// Found a match use the handle token
			$card_token_wrapper->set_multi_use_payment_handle_token( $matched_handle->paymentHandleToken );
		} else {
			// Create a multi_use payment handle from the payment
			$payment_handle_response = $payment_handles_request->create_payment_handle( $customer_id,
				$payment_handles_request->get_request_builder( $data_source )->get_create_payment_handle_for_customer_params( $card_token_wrapper->get_payment_handle_token() )
			);
			
			if ( 'MULTI_USE' === $payment_handle_response->get_usage() ) {
				// Set the merchant customer ID since it is not in the handle response
				$card_token_wrapper->set_multi_use_payment_handle_token( $card_token_wrapper->get_payment_handle_token() );
			}
		}
		
		return $card_token_wrapper;
	}
}