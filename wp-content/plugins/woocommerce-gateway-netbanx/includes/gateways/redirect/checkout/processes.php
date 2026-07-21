<?php

namespace WcPaysafe\Gateways\Redirect\Checkout;

use Paysafe\CustomerVault\Card;
use WcPaysafe\Api\Cards\Requests\Refunds;
use WcPaysafe\Api\Cards\Responses\Authorizations;
use WcPaysafe\Api\Cards\Responses\Verifications;
use WcPaysafe\Api\Client;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source as Sources_Order;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Direct_Debit\Requests\Standalone_Credits;
use WcPaysafe\Api\Direct_Debit\Responses\Purchases;
use WcPaysafe\Api\Vault\Requests\Ach;
use WcPaysafe\Api\Vault\Requests\Bacs;
use WcPaysafe\Api\Vault\Requests\Cards;
use WcPaysafe\Api\Vault\Requests\Eft;
use WcPaysafe\Api\Vault\Requests\Sepa;
use WcPaysafe\Api\Vault\Responses\Commons_Bank;
use WcPaysafe\Api\Vault\Responses\Commons_Vault;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Abstracted_Gateway;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Cart_Checkout_Helpers;
use WcPaysafe\Helpers\Currency;
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
	public function __construct( Gateway $gateway ) {
		$this->set_gateway( $gateway );
	}
	
	/**
	 * Sets the gateway class to a class variable
	 *
	 * @since 2.0
	 *
	 * @param Gateway $gateway
	 */
	private function set_gateway( Gateway $gateway ) {
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
		
		if ( is_checkout()
		     || wc_paysafe_is_change_method_page()
		     || wc_paysafe_is_pay_for_order_page()
		     || wc_paysafe_is_checkout_pay_page() ) {
			return ! $this->get_gateway()->hide_for_payments;
		}
		
		return true;
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param Sources_Order|User_Source $sourse
	 * @param string                    $payment_type cards|directdebit|interac
	 *
	 * @throws \Exception
	 * @return null|Client
	 */
	public function get_api_client( $sourse = null, $payment_type = 'cards' ) {
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
		
		wc_paysafe_add_debug_log( 'Paying for order: ' . $order_id );
		wc_paysafe_add_debug_log( 'Paying with saved token' );
		
		// We require token to process a payment
		if ( ! $wc_token || $wc_token->get_user_id() !== get_current_user_id() ) {
			WC()->session->set( 'refresh_totals', true );
			throw new \Exception( __( 'Invalid payment method. Please try again or use a new card number.', 'wc_paysafe' ) );
		}
		
		$paysafe_order = new Paysafe_Order( $order );
		
		if ( 0 < $order->get_total() || ! $paysafe_order->is_pre_order_with_tokenization() ) {
			
			wc_paysafe_add_debug_log( 'Processing token transaction' );
			
			$cvv = wc_clean( wp_unslash( $_POST[ $payment_method . '-card-cvv' ] ) );
			
			$data_source = new Sources_Order( $order );
			$data_source->set_is_initial_payment( true );
			$data_source->set_using_saved_token( true );
			$data_source->set_cvv( $cvv );
			
			// Take the payment
			$payment = $this->process_token_transaction( $data_source, $wc_token->get_token(), 'Paysafe_CC' == $wc_token->get_type() ? 'Cards' : 'DirectDebit' );
			
			$response_processor = Factories::load_response_processor( $payment, 'checkoutjs' );
			$response_processor->process_payment_response( $order, $wc_token );
		}
		
		// Save the token details to the Order and any subscriptions in it.
		// Since this is a customer initiated payment the transaction ID is also saved to the subscription for future use
		$paysafe_order->save_token( $wc_token, $payment->get_id() );
		
		// Debug log
		wc_paysafe_add_debug_log( 'Payment completed' );
		
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
		$order = wc_get_order( (int) $order_id );
		
		// Return the layover params unless directed otherwise
		if ( apply_filters( 'wc_paysafe_layover_on_checkout', true, $order ) && ( is_checkout() && ! is_checkout_pay_page() ) ) {
			$api_client         = $this->get_api_client( $order, 'cards' );
			$checkoutjs_service = $api_client->get_checkoutjs_service();
			$params             = $checkoutjs_service->get_iframe_order_parameters( $order );
			
			// Add the process action
			$params['processAction'] = 'process_payment';
			
			$paysafe_order = new Paysafe_Order( $order );
			
			if ( $this->get_gateway()->use_layover_3ds2 && $paysafe_order->is_pre_order_with_tokenization() ) {
				// For a pre-ordered purchase, this is the date that the merchandise is expected to be available.
				// The ISO 8601 date format is expected, i.e., YYYY-MM-DD.
				// TODO: Add to pre-orders with later date release only
				
				$release_date = \WC_Pre_Orders_Product::get_localized_availability_date( \WC_Pre_Orders_Order::get_pre_order_product( $order ) );
				
				$params['options']['threeDS']['orderItemDetails']['preOrderItemAvailabilityDate'] = date( 'Y-m-d', strtotime( $release_date ) );
				$params['options']['threeDS']['orderItemDetails']['preOrderPurchaseIndicator']    = 'FUTURE_AVAILABILITY';
			}
			
			return array(
				'result'                => 'success',
				'paymentData'           => $params,
				'process_payment_nonce' => wp_create_nonce( 'wc-paysafe-process-payment' ),
			);
		}
		
		// We are directing to the pay page only when we don't want to directly display the layover
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
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
		
		wc_paysafe_add_debug_log( 'Changing payment method for order order: ' . WC_Compatibility::get_order_id( $subscription ) );
		
		// We require token to process a payment
		if ( ! $wc_token || $wc_token->get_user_id() !== get_current_user_id() ) {
			WC()->session->set( 'refresh_totals', true );
			throw new \Exception( __( 'Invalid payment method. Please try again or use a new payment method.', 'wc_paysafe' ) );
		}
		
		// Complete the order
		$paysafe_order = new Paysafe_Order( $subscription );
		$paysafe_order->save_token( $wc_token );
		
		// Debug log
		wc_paysafe_add_debug_log( 'Payment Method was changed successfully' );
		
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
	 * @param string                                          $payment_type cards|directdebit|interac
	 * @param float                                           $amount
	 *
	 * @throws \Exception
	 * @return Authorizations|Purchases
	 */
	public function process_token_transaction( $data_source, $token, $payment_type, $amount = null ) {
		$payment_type = strtolower( $payment_type );
		$api_client   = $this->get_api_client( $data_source, $payment_type );
		
		if ( 'directdebit' == $payment_type ) {
			wc_paysafe_add_debug_log( 'process_token_transaction: running direct debit purchase' );
			
			$transaction_service = $api_client->get_direct_debit_service()->purchases();
		} elseif ( 'interac' == $payment_type ) {
			wc_paysafe_add_debug_log( 'process_token_transaction: running interac purchase' );
			
			$transaction_service = $api_client->get_alternate_payment_service()->payments();
		} else {
			wc_paysafe_add_debug_log( 'process_token_transaction: running card authorization' );
			
			$transaction_service = $api_client->get_cards_service()->authorizations();
		}
		
		$payment = $transaction_service->process(
			$transaction_service->get_request_builder( $data_source )->get_token_transaction_parameters( $token, $amount )
		);
		
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
		$api_client         = $this->get_api_client( $order, 'cards' );
		$checkoutjs_service = $api_client->get_checkoutjs_service();
		
		$paysafe_args = $checkoutjs_service->get_iframe_order_parameters( $order );
		
		// Add the process action
		$paysafe_args['processAction'] = 'process_payment';
		
		wp_localize_script( 'paysafe-checkout-js', 'paysafe_iframe_params', $paysafe_args );
		
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
		wc_paysafe_add_debug_log( 'Paysafe-checkout error: ' . $error_message );
		
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
	 * @param Data_Source_Interface $data_source
	 * @param string|array          $data_to_verify
	 * @param string                $verification_type
	 *
	 * @return Verifications
	 * @throws \Exception
	 */
	public function process_verification( Data_Source_Interface $data_source, $data_to_verify, $verification_type = 'card' ) {
		$api_client           = $this->get_api_client( $data_source, 'cards' );
		$cards_authorizations = $api_client->get_cards_service()->authorizations();
		$response             = $cards_authorizations->verify(
			$cards_authorizations->get_request_builder( $data_source )->get_verification_parameters( $data_to_verify, $verification_type )
		);
		
		if ( 'FAILED' == $response->status ) {
			throw new \Exception( sprintf( __( 'The %s provided is not valid.', 'wc_paysafe' ), $verification_type ) );
		} elseif ( 'RECEIVED' == $response->status ) {
			throw new \Exception( sprintf( __( 'Can not verify the %s provided. Please try again later.', 'wc_paysafe' ), $verification_type ) );
		}
		
		return $response;
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param Sources_Order|User_Source $data_source
	 * @param Paysafe_Customer          $paysafe_customer
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function create_vault_profile( $data_source, $paysafe_customer ) {
		$api_client    = $this->get_api_client( $data_source, 'cards' );
		$vault_service = $api_client->get_vault_service();
		
		$create_profile_response = $api_client->get_vault_service()->profile()->create(
			$vault_service->profile()->get_request_builder( $data_source )->get_create_profile_parameters()
		);
		
		// Save the profile ID to the user meta
		if ( $create_profile_response->get_error() ) {
			wc_paysafe_add_debug_log( 'Profile creation failed. ' . print_r( $create_profile_response->get_errors_from_response() ) );
			
			throw new \Exception( sprintf( __( 'Unable to create vault profile. %s', 'wc_paysafe' ), $create_profile_response->get_errors_from_response() ) );
		}
		
		$paysafe_customer->save_vault_profile_id( $create_profile_response->get_id() );
		
		wc_paysafe_add_debug_log( 'Profile create. ID: ' . $create_profile_response->get_id() );
		
		return $create_profile_response->get_id();
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param User_Source|Sources_Order|Data_Source_Interface $data_source
	 * @param                                                 $token
	 * @param string                                          $payment_type "directdebit" or "cards". Case-insensitive !!!
	 *
	 * @return \WcPaysafe\Api\Response_Abstract
	 * @throws \Exception
	 */
	public function convert_single_use_token( Data_Source_Interface $data_source, $token, $payment_type = 'cards' ) {
		$paysafe_customer = new Paysafe_Customer( $data_source->get_user() );
		$profile_id       = $paysafe_customer->get_vault_profile_id();
		$payment_type     = strtolower( $payment_type );
		
		$api_client    = $this->get_api_client( $data_source, $payment_type );
		$vault_service = $api_client->get_vault_service();
		$currency      = $data_source->get_currency();
		
		wc_paysafe_add_debug_log( 'Convert single-use token' );
		
		if ( 'cards' == $payment_type ) {
			$method_service = $vault_service->card();
		} else {
			if ( Currency::is_ach_currency( $currency ) ) {
				$method_service = $vault_service->ach();
			} elseif ( Currency::is_bacs_currency( $currency ) ) {
				$method_service = $vault_service->bacs();
			} elseif ( Currency::is_sepa_currency( $currency ) ) {
				$method_service = $vault_service->sepa();
			} else {
				$method_service = $vault_service->eft();
			}
		}
		
		wc_paysafe_add_debug_log( 'Service method used is: ' . get_class( $vault_service ) );
		
		try {
			/**
			 * @var \WcPaysafe\Api\Response_Abstract
			 */
			$permanent_token = $method_service->create_from_single_use_token(
				$method_service->get_request_builder( $data_source )->create_from_single_use_token_parameters( $token, $profile_id )
			);
			
			if ( 'ACTIVE' != $permanent_token->get_status() ) {
				throw new \Exception( __( 'Unable to convert single use token to permanent token. Please try again', 'wc_paysafe' ) );
			}
		}
		catch ( \Exception $e ) {
			
			wc_paysafe_add_debug_log( 'Single-use token conversion not successful. Error code: ' . print_r( $e->getCode(), true ) );
			
			// Error 7503: Card already exists in the Vault Profile
			if ( '7503' == $e->getCode() ) {
				
				wc_paysafe_add_debug_log( "Card already exists in the Vault. Trying to find it..." . print_r( $e->getCode(), true ) );
				
				// TODO: Make sure we detect the existing card and update it before we proceed.
				// TODO: Match a card by its last digits and type
				$verification = $this->process_verification( $data_source, $token, 'token' );
				
				$profile = $api_client->get_vault_service()->profile()->get(
					array( 'id' => $profile_id ),
					true,
					true
				);
				
				/**
				 * @var \WcPaysafe\Api\Vault\Responses\Cards $card
				 */
				foreach ( $profile->get_cards() as $card ) {
					
					if ( $verification->get_last_digits() == $card->get_last_digits()
					     && $verification->get_card_type() == $card->get_card_type()
					) {
						wc_paysafe_add_debug_log( 'Found the Vault card.' );
						$permanent_token = $card;
						break;
					}
				}
				
				if ( ! empty( $permanent_token ) ) {
					wc_paysafe_add_debug_log( 'Update the card since token was already present in the profile ... ' );
					
					try {
						$customer_tokens = new Customer_Tokens( $paysafe_customer->get_id() );
						
						// Get the matching WC Token to be updated
						$update_wc_token = $customer_tokens->get_token_from_value( $permanent_token->get_payment_token() );
						if ( ! $update_wc_token ) {
							throw new \Exception( __( 'Could not find the wc token to update', 'wc_paysafe' ) );
						}
						
						// Update the Paysafe Profile using the single use token
						$permanent_token = $this->update_profile_with_token( $data_source, $token, $update_wc_token, 'cards' );
						
						// Use the updated vault token to update the WC Token
						$customer_tokens->update_wc_token( $update_wc_token, $permanent_token );
					}
					catch ( \Exception $e ) {
						wc_paysafe_add_debug_log( 'Error occurred: ' . $e->getMessage() );
						
						$permanent_token = false;
					}
				}
			} elseif ( '7506' == $e->getCode() ) {
				// TODO: Make sure we detect the existing card and update it before we proceed.
				// TODO: Match a card by its last digits and type
				
				wc_paysafe_add_debug_log( "Bank account already exists in the Vault. Trying to find it..." . print_r( $e->getCode(), true ) );
				
				// Get the payment account used from the last payment response.
				// - We do process the payment before we save the account to the profile,
				//      so we are able to see the information of the account used.
				// - We don't have this info originally since we are using a single-use token
				
				/**
				 * @var Purchases $last_payment Since we are trying to save
				 */
				$last_payment = $data_source->get_last_payment_response();
				if ( empty( $last_payment ) ) {
					throw new \Exception( $e->getMessage(), $e->getCode() );
				}
				
				$include_cards = false;
				$include_ach   = false;
				$include_eft   = false;
				$include_bacs  = false;
				$include_sepa  = false;
				if ( Currency::is_ach_currency( $currency ) ) {
					$include_ach = true;
				} elseif ( Currency::is_bacs_currency( $currency ) ) {
					$include_bacs = true;
				} elseif ( Currency::is_sepa_currency( $currency ) ) {
					$include_sepa = true;
				} else {
					$include_eft = true;
				}
				
				/**
				 * @var \WcPaysafe\Api\Direct_Debit\Responses\Commons $bank_account
				 */
				$bank_account       = $last_payment->bank();
				$accounts_to_search = $last_payment->bank_type() . 'bankaccounts';
				
				$profile = $api_client->get_vault_service()->profile()->get(
					array( 'id' => $profile_id ),
					false,
					$include_cards,
					$include_ach,
					$include_eft,
					$include_bacs,
					$include_sepa
				);
				
				// Look through the saved accounts and match the lastDigits
				if ( '' != $profile->{$accounts_to_search} ) {
					/**
					 * @var Commons_Bank $account
					 */
					foreach ( $profile->{$accounts_to_search} as $account ) {
						if ( $bank_account->get_last_digits() == $account->get_last_digits() ) {
							wc_paysafe_add_debug_log( "Found the bank account" );
							
							$permanent_token = $account;
							break;
						}
					}
				}
			}
			
			if ( ! isset( $permanent_token ) || ! $permanent_token ) {
				
				wc_paysafe_add_debug_log( "No token was matched. Error returned: " . $e->getCode() . ':' . $e->getMessage() );
				
				throw new \Exception( $e->getCode() . ':' . $e->getMessage() );
			}
		}
		
		return $permanent_token;
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param Data_Source_Interface|User_Source|Sources_Order $data_source
	 * @param                                                 $single_use_token
	 * @param \WC_Payment_Token_Paysafe_CC|\WC_Payment_Token  $wc_token
	 * @param string                                          $payment_type cards|directdebit|interac
	 *
	 * @return \WcPaysafe\Api\Response_Abstract
	 * @throws \Exception
	 */
	public function update_profile_with_token( Data_Source_Interface $data_source, $single_use_token, $wc_token, $payment_type ) {
		$payment_type  = strtolower( $payment_type );
		$api_client    = $this->get_api_client( $data_source, $payment_type );
		$vault_service = $api_client->get_vault_service();
		
		wc_paysafe_add_debug_log( 'Updating a profile with a token' );
		
		$vault_source_id = $wc_token->get_source_id();
		if ( ! $vault_source_id ) {
			$vault_object    = $this->get_vault_token_from_wc_token( $wc_token );
			$vault_source_id = $vault_object->id;
		}
		
		if ( 'cards' == $payment_type ) {
			$method_service = $vault_service->card();
		} else {
			$currency = $data_source->get_currency();
			if ( Currency::is_ach_currency( $currency ) ) {
				$method_service = $vault_service->ach();
			} elseif ( Currency::is_bacs_currency( $currency ) ) {
				$method_service = $vault_service->bacs();
			} elseif ( Currency::is_sepa_currency( $currency ) ) {
				$method_service = $vault_service->sepa();
			} else {
				$method_service = $vault_service->eft();
			}
		}
		
		$updated_vault_token = $method_service->update(
			$method_service->get_request_builder( $data_source )->update_from_single_use_token_parameters( $wc_token->get_profile_id(), $vault_source_id, $single_use_token )
		);
		
		if ( 'ACTIVE' != $updated_vault_token->get_status() ) {
			wc_paysafe_add_debug_log( 'Update from single-use token failed. Status: ' . print_r( $updated_vault_token->get_status(), true ) );
			
			throw new \Exception( __( 'Unable update the payment method. Please refresh the page and try again', 'wc_paysafe' ) );
		}
		
		wc_paysafe_add_debug_log( 'Update from single-use token was successful' );
		
		return $updated_vault_token;
	}
	
	/**
	 * @param \WC_Payment_Token|\WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD $wc_token
	 *
	 * @return bool|Card
	 * @throws \Exception
	 */
	public function get_vault_token_from_wc_token( $wc_token ) {
		$matched_token = false;
		$include_cards = false;
		$include_ach   = false;
		$include_eft   = false;
		$include_bacs  = false;
		$include_sepa  = false;
		
		if ( $wc_token instanceof \WC_Payment_Token_Paysafe_CC ) {
			$include_cards = true;
		} else {
			if ( 'sepa' == $wc_token->get_bank_account_type() ) {
				$include_sepa = true;
			} else if ( 'bacs' == $wc_token->get_bank_account_type() ) {
				$include_bacs = true;
			} else if ( 'eft' == $wc_token->get_bank_account_type() ) {
				$include_eft = true;
			} else if ( 'ach' == $wc_token->get_bank_account_type() ) {
				$include_ach = true;
			}
		}
		
		$api_client = $this->get_api_client();
		$profile    = $api_client->get_vault_service()->profile()->get(
			array( 'id' => $wc_token->get_profile_id() ),
			true,
			$include_cards,
			$include_ach,
			$include_eft,
			$include_bacs,
			$include_sepa
		);
		
		$tokens = array();
		if ( $include_cards ) {
			$tokens = $profile->get_cards();
		} else {
			if ( 'sepa' == $wc_token->get_bank_account_type() ) {
				$tokens = $profile->sepaBankAccounts;
			} else if ( 'bacs' == $wc_token->get_bank_account_type() ) {
				$tokens = $profile->bacsBankAccounts;
			} else if ( 'eft' == $wc_token->get_bank_account_type() ) {
				$tokens = $profile->eftBankAccounts;
			} else if ( 'ach' == $wc_token->get_bank_account_type() ) {
				$tokens = $profile->achBankAccounts;
			}
		}
		
		/**
		 * @var Commons_Vault $vault_token
		 */
		foreach ( $tokens as $vault_token ) {
			// Match the cards to the wc_token
			if ( $vault_token->get_payment_token() == $wc_token->get_token() ) {
				$matched_token = $vault_token;
				break;
			}
		}
		
		return $matched_token;
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 * @param null      $amount
	 * @param string    $reason
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function process_refund( $order, $amount = null, $reason = '' ) {
		$data_source = new Sources_Order( $order );
		$amount      = wc_format_decimal( $amount );
		
		$paysafe_order = new Paysafe_Order( $order );
		$payment_type  = $paysafe_order->get_payment_type();
		
		/**
		 * @var Refunds|Standalone_Credits $refund_service
		 */
		if ( 'card' != $payment_type ) {
			$client_api     = $this->get_api_client( $data_source, 'directdebit' );
			$refund_service = $client_api->get_direct_debit_service()->standalone_credits();
		} else {
			
			// We need to have at least one settlement
			$settlement_ids = $paysafe_order->get_settlement_ids();
			$transaction_id = array_pop( $settlement_ids );
			if ( empty( $transaction_id ) ) {
				throw new \Exception( sprintf( __( "We can't refund this transaction because we don't have settlement ID.", 'wc_paysafe' ) ) );
			}
			
			$client_api = $this->get_api_client( $data_source, 'cards' );
			
			$authorization        = $client_api->get_cards_service()->authorizations()->get( array( 'id' => $paysafe_order->get_authorization_id() ) );
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
			
			$refund_service = $client_api->get_cards_service()->refunds();
		}
		
		$refund = $refund_service->process( $refund_service->get_request_builder( $data_source )->refund_parameters( $amount, $reason ) );
		
		$response_processor = Factories::load_response_processor( $refund, 'checkoutjs' );
		$response_processor->process_refund( $order, $amount );
		
		return $refund->get_id();
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
		
		$client_api = $this->get_api_client( $data_source, 'cards' );
		
		// Check that the captured amount is
		$settlement_service = $client_api->get_cards_service()->settlements();
		$settlement         = $settlement_service->process( $settlement_service->get_request_builder( $data_source )->settlement_parameters( $amount ) );
		
		$response_processor = Factories::load_response_processor( $settlement, 'checkoutjs' );
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
		$ps_subscription->save_order_profile_id( $ps_renewal->get_order_profile_id() );
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
		$paysafe_order->delete_order_profile_id();
	}
	
	/**
	 * Attempts to retrieve the payment token from a past transaction.
	 * If successful, a WC Token will be created and the token will be saved to the passed order and any subscriptions attached to it.
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return \WC_Payment_Token|\WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD|false
	 */
	public function get_token_from_past_transaction( $order ) {
		$wc_token = false;
		try {
			// May be the subscription originally used the Hosted API.
			// Look to get the order ID and convert it to a Vault token
			
			$customer          = new Paysafe_Customer( $order->get_user() );
			$legacy_profile_id = $customer->get_legacy_profile_id();
			$profile_token     = $customer->get_legacy_profile_token();
			
			wc_paysafe_add_debug_log( 'Create a token from legacy profile.' );
			wc_paysafe_add_debug_log( 'Profile: ' . $legacy_profile_id );
			wc_paysafe_add_debug_log( 'Token: ' . $profile_token );
			
			// Bail, on no profile
			if ( '' == $legacy_profile_id ) {
				return false;
			}
			
			$client          = $this->get_api_client();
			$profile_service = $client->get_vault_service()->profile();
			
			// Get the profile addresses and cards
			$response = $profile_service->get( array( 'id' => $legacy_profile_id ), true, true );
			
			$card_to_add = false;
			if ( ! empty( $response->get_cards() ) ) {
				/**
				 * @var \WcPaysafe\Api\Vault\Responses\Cards $card
				 */
				foreach ( $response->get_cards() as $card ) {
					if ( $card->get_payment_token() == $profile_token ) {
						$card_to_add = $card;
						break;
					}
				}
				
				if ( ! $card_to_add ) {
					throw new \Exception();
				}
				
				wc_paysafe_add_debug_log( 'Found a matching token. Creating a WC Token...' );
				
				// Save or Create a Paysafe Vault ID for the customer
				$vault_profile_id = $customer->get_vault_profile_id();
				if ( ! $vault_profile_id ) {
					wc_paysafe_add_debug_log( 'Customer has no profile. We\'ll create one for them.' );
					// Save the legacy profile ID, it will be the customer profile from now on
					$customer->save_vault_profile_id( $legacy_profile_id );
				}
				
				$manage_tokens = new Customer_Tokens( $customer->get_id() );
				$wc_token      = $manage_tokens->create_wc_token( $card_to_add );
				
				$paysafe_order = new Paysafe_Order( $order );
				$paysafe_order->save_token( $wc_token );
				
				wc_paysafe_add_debug_log( 'Created token ID is: ' . $wc_token->get_id() );
			}
		}
		catch ( \Exception $e ) {
			wc_paysafe_add_debug_log( 'Legacy token was not converted. From order/subscription: ' . WC_Compatibility::get_order_id( $order ) );
		}
		
		return $wc_token;
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
			wc_paysafe_add_debug_log( 'Scheduled payment: ' . print_r( WC_Compatibility::get_order_id( $renewal_order ), true ) );
			
			$paysafe_order   = new Paysafe_Order( $renewal_order );
			$customer_tokens = new Customer_Tokens( $renewal_order->get_customer_id() );
			
			// Get the token used
			$wc_token = $customer_tokens->get_token_from_value( $paysafe_order->get_order_profile_token() );
			if ( ! $wc_token ) {
				// Get the token from the order transaction
				$wc_token = $this->get_token_from_past_transaction( $renewal_order );
			}
			
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
				'Paysafe_CC' == $wc_token->get_type() ? 'cards' : 'directdebit',
				$amount_to_charge
			);
			
			wc_paysafe_add_debug_log( 'Scheduled payment response: ' . print_r( $payment->get_data(), true ) );
			
			// Process the order from the response
			$response_processor = Factories::load_response_processor( $payment );
			$response_processor->process_payment_response( $renewal_order );
		}
		catch ( \Exception $e ) {
			$renewal_order->update_status( 'failed', $e->getMessage() );
			
			// Debug log
			wc_paysafe_add_debug_log( $e->getMessage() );
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
			$customer_tokens = new Customer_Tokens( $order->get_customer_id() );
			
			$wc_token = $customer_tokens->get_token_from_value( $paysafe_order->get_order_profile_token() );
			if ( ! $wc_token ) {
				// Get the token from the order transaction
				$wc_token = $this->get_token_from_past_transaction( $order );
			}
			
			if ( ! $wc_token ) {
				throw new \Exception( __( 'Payment token is missing. The Pre-order cannot be charged.', 'wc_paysafe' ) );
			}
			
			$data_source = new Sources_Order( $order );
			$data_source->set_is_initial_payment( false );
			$data_source->set_using_saved_token( true );
			
			$payment = $this->process_token_transaction(
				new Sources_Order( $order ),
				$wc_token->get_token(),
				'Paysafe_CC' == $wc_token->get_type() ? 'Cards' : 'DirectDebit'
			);
			
			$response_processor = Factories::load_response_processor( $payment );
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
		
		$vault_service = $this->get_api_client()->get_vault_service();
		
		if ( $wc_token instanceof \WC_Payment_Token_Paysafe_DD ) {
			$type = $wc_token->get_bank_account_type();
			/**
			 * @var Eft|Sepa|Bacs|Ach $method_service
			 */
			$method_service = $vault_service->{$type}();
		} else {
			/**
			 * @var Cards $method_service
			 */
			$method_service = $vault_service->card();
		}
		
		return $method_service->delete(
			$method_service->get_request_builder( new User_Source( new \WP_User( $wc_token->get_user_id() ) ) )->delete_method_parameters( $wc_token )
		);
	}
}