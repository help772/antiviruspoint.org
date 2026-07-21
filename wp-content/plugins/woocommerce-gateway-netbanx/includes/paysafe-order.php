<?php

namespace WcPaysafe;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Tokens\Customer_Tokens;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order wrapper for gateway order data
 *
 * @since  3.2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018-2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Paysafe_Order {
	
	/**
	 * @var \WC_Order
	 */
	public $order;
	
	/**
	 * @param $order
	 */
	public function __construct( \WC_Order $order ) {
		$this->order = $order;
	}
	
	/**---------------------------------
	 * GETTERS
	 * -----------------------------------*/
	
	/**
	 * Return the order number with stripped # or n° ( french translations )
	 *
	 * @param \WC_Order $order
	 *
	 * @return string
	 */
	public function get_order_number() {
		return str_replace( array( '#', 'n°' ), '', $this->order->get_order_number() );
	}
	
	/**
	 * Returns the Paysafe billing id(Hosted API), authorization ID(Cards API)
	 *
	 * The meta is used as the backwards compatible meta field to get the charged authorization
	 *
	 * @since 3.2.0
	 *
	 * @return mixed
	 */
	public function get_payment_order_id() {
		$id = WC_Compatibility::get_meta( $this->order, '_netbanx_payment_order_id' );
		
		return $id;
	}
	
	/**
	 * Returns the order authorization ID
	 *
	 * @since 3.3.0
	 *
	 * @return mixed
	 */
	public function get_authorization_id() {
		$auth_id = WC_Compatibility::get_meta( $this->order, '_paysafe_authorization_id' );
		
		return $auth_id;
	}
	
	/**
	 * Returns the payment captured data
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	public function get_is_payment_captured() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			return $this->order->get_meta( '_paysafe_is_payment_captured' );
		}
		
		return get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_paysafe_is_payment_captured', true );
	}
	
	/**
	 * Returns the amount captured
	 *
	 * @since 3.2.0
	 *
	 * @return float
	 */
	public function get_order_amount_captured() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			return $this->order->get_meta( '_paysafe_order_amount_captured' );
		}
		
		return get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_paysafe_order_amount_captured', true );
	}
	
	/**
	 * Returns the amount authorized in the transaction
	 *
	 * @since 3.2.0
	 *
	 * @return float
	 */
	public function get_order_amount_authorized() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			return $this->order->get_meta( '_paysafe_order_amount_authorized' );
		}
		
		return get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_paysafe_order_amount_authorized', true );
	}
	
	/**
	 * Returns the payment type details: brand, type, last4, expiry.
	 *
	 * @since 3.2.0
	 *
	 * TODO: This should be replaced with the token "get_display_name" method to return the formatted string
	 *      It is still in this state and returns an array to keep backward compat functionality with the Legacy API(Hosted Payments)
	 *
	 * @return array
	 */
	public function get_payment_type_details() {
		
		$customer_tokens = new Customer_Tokens( $this->order->get_user_id() );
		$token           = $customer_tokens->get_token_from_value( $this->get_order_profile_token() );
		
		if ( $token ) {
			if ( 'Paysafe_CC' == $token->get_type() ) {
				return array(
					'type'   => _x( 'card', 'payment type', 'wc_paysafe' ),
					'brand'  => $token->get_card_type_label( $token->get_card_type() ),
					'last4'  => $token->get_last4(),
					'expiry' => $token->get_expiry_month() . '/' . $token->get_expiry_year(),
				);
			}
			
			return array(
				'type'   => _x( 'Direct Debit', 'payment type', 'wc_paysafe' ),
				'brand'  => $token->get_bank_account_type(),
				'last4'  => $token->get_last4(),
				'expiry' => '',
			);
		}
		
		if ( WC_Compatibility::is_wc_3_0() ) {
			return array(
				'type'   => $this->order->get_meta( '_netbanx_transaction_payment_type' ),
				'brand'  => $this->order->get_meta( '_netbanx_transaction_card_brand' ),
				'last4'  => $this->order->get_meta( '_netbanx_transaction_card_last_four' ),
				'expiry' => $this->order->get_meta( '_netbanx_transaction_card_expiration' ),
			
			);
		}
		
		return array(
			'type'   => get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_payment_type', true ),
			'brand'  => get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_card_brand', true ),
			'last4'  => get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_card_last_four', true ),
			'expiry' => get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_card_expiration', true ),
		
		);
	}
	
	/**
	 * Saves the payment type to the order.
	 *
	 * Payment type: card, eft, ach, bacs, sepa ... Any type of payment, we want to add it here so we know the context of the payment
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_payment_type() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			$result = $this->order->get_meta( '_paysafe_payment_type' );
		} else {
			$result = get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_paysafe_payment_type', true );
		}
		
		if ( '' == $result ) {
			$result = 'card';
		}
		
		return $result;
	}
	
	public function get_order_profile_id() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			return $this->order->get_meta( '_netbanx_hosted_order_profile_id' );
		}
		
		return get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_hosted_order_profile_id', true );
	}
	
	public function get_order_profile_token() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			return $this->order->get_meta( '_netbanx_hosted_order_profile_token' );
		}
		
		return get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_hosted_order_profile_token', true );
	}
	
	/**
	 * Source ID is the payment method ID. We can use this ID to retrieve the payment method details and charge it.
	 * @return mixed
	 */
	public function get_source_id() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			return $this->order->get_meta( '_paysafe_source_id' );
		}
		
		return get_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_paysafe_source_id', true );
	}
	
	/**
	 * Returns the attempt count for the request type
	 *
	 * @param string $type
	 *
	 * @return mixed
	 */
	public function get_order_payment_attempts( $type = 'order' ) {
		if ( 'order' == $type ) {
			return WC_Compatibility::get_meta( $this->order, '_netbanx_order_payment_attempts' );
		}
		
		return WC_Compatibility::get_meta( $this->order, '_paysafe_attempts_counter_for_' . $type );
	}
	
	/**
	 * Iterates and saves an order request suffix. Used to remove duplication of requests
	 *
	 * @param string $type Example: order, refund, capture
	 *
	 * @return int|string
	 */
	public function get_attempts_suffix( $type = 'order' ) {
		$attempts        = $this->get_order_payment_attempts( $type );
		$attempts_suffix = 0;
		
		if ( is_numeric( $attempts ) ) {
			$attempts_suffix = $attempts;
			
			$attempts_suffix ++;
		}
		
		// Save the incremented attempts
		$this->save_order_payment_attempts( $attempts_suffix, $type );
		
		return $attempts_suffix;
	}
	
	/**
	 * Returns the settlement ID for an order.
	 *
	 * @see   "add_settlement_id" method for explanation
	 *
	 * @since 3.3.0
	 *
	 * @return mixed
	 */
	public function get_settlement_ids() {
		$ids = WC_Compatibility::get_meta( $this->order, '_paysafe_settlement_ids', true );
		
		if ( ! is_array( $ids ) ) {
			return array();
		}
		
		$key = key( $ids );
		if ( $ids[ $key ] instanceof \WC_Meta_Data ) {
			unset( $ids[ $key ] );
		}
		
		return $ids;
	}
	
	
	/**---------------------------------------------------
	 * CREATE
	 * ---------------------------------------------------*/
	
	/**
	 * Saves Paysafe payment order id to the order
	 *
	 * This is the original transaction_id/settlement_id for the payment
	 *
	 * @since 3.2.0
	 *
	 * @param string $value
	 */
	public function save_payment_order_id( $value ) {
		WC_Compatibility::update_meta( $this->order, '_netbanx_payment_order_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * Returns the order authorization ID
	 *
	 * @since 3.3.0
	 *
	 * @param string $value
	 *
	 * @return mixed
	 */
	public function save_authorization_id( $value ) {
		return WC_Compatibility::update_meta( $this->order, '_paysafe_authorization_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * TODO: Payments: We may need this number to reference the processed transaction for the order.
	 *
	 * @param $value
	 *
	 * @return bool|int
	 */
	public function save_merchant_reference_number( $value ) {
		return WC_Compatibility::update_meta( $this->order, '_paysafe_merchant_reference_number', wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * Adds the value to the settlement IDs.
	 *
	 * Since we can authorize a payment and then capture the payment a little bit at a time,
	 * we want to keep all settlement IDs for all those captures, for our records.
	 * This meta should contain all settlements done against the order, including the original authorization
	 *
	 * @since 3.3.0
	 *
	 * @param string $value
	 */
	public function add_settlement_id( $value ) {
		$ids = $this->get_settlement_ids();
		
		// Only add the value if it is not in the array
		if ( ! in_array( $value, $ids ) ) {
			$ids[] = $value;
			
			WC_Compatibility::update_meta( $this->order, '_paysafe_settlement_ids', wc_clean( wp_unslash( $ids ) ) );
		}
	}
	
	/**
	 * Marks the order the amount captured
	 *
	 * @param bool $is_captured
	 *
	 * @since 3.2.0
	 */
	public function save_is_payment_captured( $is_captured = false ) {
		WC_Compatibility::update_meta( $this->order, '_paysafe_is_payment_captured', wc_clean( wp_unslash( $is_captured ) ) );
	}
	
	/**
	 * Marks the order payment as captured or not
	 *
	 * @since 3.2.0
	 *
	 * @param bool $amount (optional) If not present the order total will be saved
	 */
	public function save_order_amount_captured( $amount = false ) {
		if ( false === $amount ) {
			$amount = $this->order->get_total();
		}
		
		WC_Compatibility::update_meta( $this->order, '_paysafe_order_amount_captured', wc_clean( wp_unslash( $amount ) ) );
	}
	
	/**
	 * Saves the amount authorized in the transaction
	 *
	 * @since 3.2.0
	 *
	 * @param bool $amount
	 */
	public function save_order_amount_authorized( $amount = false ) {
		if ( false === $amount ) {
			$amount = $this->order->get_total();
		}
		
		WC_Compatibility::update_meta( $this->order, '_paysafe_order_amount_authorized', wc_clean( wp_unslash( $amount ) ) );
	}
	
	/**
	 * Saves the payment method details to the order.
	 *
	 * The method is only used in the legacy API (Hosted Payments) and should not be used by new integrations.
	 * Use the token object to save payment data or just add the data as an order note for the merchant to see.
	 *
	 * @since 3.2.0
	 *
	 * @param $data
	 */
	public function save_payment_type_details( $data ) {
		$defaults = array(
			'type'   => '',
			'brand'  => '',
			'last4'  => '****',
			'expiry' => '**/**',
		);
		
		$data = wp_parse_args( $data, $defaults );
		
		if ( WC_Compatibility::is_wc_3_0() ) {
			$this->order->add_meta_data( '_netbanx_transaction_payment_type', $data['type'], true );
			$this->order->add_meta_data( '_netbanx_transaction_card_brand', $data['brand'], true );
			$this->order->add_meta_data( '_netbanx_transaction_card_last_four', $data['last4'], true );
			$this->order->add_meta_data( '_netbanx_transaction_card_expiration', $data['expiry'], true );
			$this->order->save();
		} else {
			update_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_payment_type', $data['type'] );
			update_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_card_brand', $data['brand'] );
			update_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_card_last_four', $data['last4'] );
			update_post_meta( WC_Compatibility::get_prop( $this->order, 'id' ), '_netbanx_transaction_card_expiration', $data['expiry'] );
		}
	}
	
	/**
	 * Saves the payment type to the order.
	 *
	 * Payment type: card, eft, ach, bacs, sepa ... Any type of payment, we want to add it here so we know the context of the payment
	 *
	 * @param $value
	 */
	public function save_payment_type( $value ) {
		WC_Compatibility::update_meta( $this->order, '_paysafe_payment_type', wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * Saves the profile id
	 *
	 * @param $value
	 */
	public function save_order_profile_id( $value ) {
		WC_Compatibility::update_meta( $this->order, '_netbanx_hosted_order_profile_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * Saves the profile token
	 *
	 * @param $value
	 */
	public function save_order_profile_token( $value ) {
		WC_Compatibility::update_meta( $this->order, '_netbanx_hosted_order_profile_token', wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * Source ID is the payment method ID. We can use this ID to retrieve the payment method details and charge it.
	 * @since 3.3.0
	 *
	 * @param $value
	 */
	public function save_source_id( $value ) {
		WC_Compatibility::update_meta( $this->order, '_paysafe_source_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * Saves the order payment attempts
	 *
	 * @param int    $value
	 * @param string $type
	 *
	 * @return int
	 */
	public function save_order_payment_attempts( $value, $type = 'order' ) {
		if ( 'order' == $type ) {
			return WC_Compatibility::update_meta( $this->order, '_netbanx_order_payment_attempts', wc_clean( wp_unslash( $value ) ) );
		}
		
		return WC_Compatibility::update_meta( $this->order, '_paysafe_attempts_counter_for_' . $type, wc_clean( wp_unslash( $value ) ) );
	}
	
	/**
	 * Save token and profile id to the orders and subscriptions
	 *
	 * @param \WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD|\WC_Payment_Token_Paysafe_Payments_Card|\WC_Payment_Token $token
	 */
	public function save_token( $token, $initial_transaction_id = '' ) {
		if ( $token instanceof \WC_Payment_Token_Paysafe_Payments_Card ) {
			$this->save_payments_merchant_customer_id( $token->get_merchant_customer_id() );
			$this->save_payments_customer_id( $token->get_customer_id() );
			$this->save_order_profile_token( $token->get_token() );
			
			$subscriptions = $this->get_subscriptions();
			
			/**
			 * @var \WC_Subscription $subscription
			 */
			foreach ( $subscriptions as $subscription ) {
				
				$ps_subscription = new Paysafe_Order( $subscription );
				
				// Debug log
				wc_paysafe_add_debug_log( 'Saving details to subscription: ' . print_r( WC_Compatibility::get_order_id( $subscription ), true ), Formatting::get_log_id($subscription->get_payment_method()) );
				
				$ps_subscription->save_payments_customer_id( $token->get_customer_id() );
				$ps_subscription->save_order_profile_token( $token->get_token() );
				$ps_subscription->save_payments_merchant_customer_id( $token->get_merchant_customer_id() );
				
				if ( '' !== $initial_transaction_id ) {
					$ps_subscription->save_authorization_id( $initial_transaction_id );
				}
			}
		} else {
			$this->save_order_profile_id( $token->get_profile_id() );
			$this->save_order_profile_token( $token->get_token() );
			$this->save_source_id( $token->get_source_id() );
			
			$subscriptions = $this->get_subscriptions();
			
			/**
			 * @var \WC_Subscription $subscription
			 */
			foreach ( $subscriptions as $subscription ) {
				
				$ps_subscription = new Paysafe_Order( $subscription );
				
				// Debug log
				wc_paysafe_add_debug_log( 'Saving details to subscription: ' . print_r( WC_Compatibility::get_order_id( $subscription ), true ), Formatting::get_log_id($subscription->get_payment_method()) );
				
				$ps_subscription->save_order_profile_id( $token->get_profile_id() );
				$ps_subscription->save_order_profile_token( $token->get_token() );
				$ps_subscription->save_source_id( $token->get_source_id() );
				
				if ( '' !== $initial_transaction_id ) {
					$ps_subscription->save_authorization_id( $initial_transaction_id );
				}
			}
		}
	}
	
	/**
	 * Returns all subscriptions for the order
	 * @return array
	 */
	public function get_subscriptions() {
		$subscriptions = array();
		
		if ( ! Paysafe::is_subscriptions_active() ) {
			return $subscriptions;
		}
		
		// Also store it on the subscriptions being purchased or paid for in the order
		if ( wcs_order_contains_subscription( $this->order ) ) {
			$subscriptions = wcs_get_subscriptions_for_order( $this->order );
		} elseif ( wcs_order_contains_renewal( $this->order ) ) {
			$subscriptions = wcs_get_subscriptions_for_renewal_order( $this->order );
		}
		
		return $subscriptions;
	}
	
	/**
	 * @param string $transaction_id
	 */
	public function complete_order( $transaction_id ) {
		if ( $this->is_pre_order_with_tokenization() ) {
			// Now that we have the info need for future payment, mark the order pre-ordered
			\WC_Pre_Orders_Order::mark_order_as_pre_ordered( $this->order );
		} else {
			$this->order->payment_complete( $transaction_id );
		}
	}
	
	/**---------------------------------------------------
	 * DELETE
	 * ---------------------------------------------------*/
	
	/**
	 * Deletes the billing ID from the order
	 *
	 * @since 3.2.0
	 *
	 * @return bool|int
	 */
	public function delete_payment_order_id() {
		return WC_Compatibility::delete_meta( $this->order, '_netbanx_payment_order_id' );
	}
	
	/**
	 * Returns the order authorization ID
	 *
	 * @since 3.3.0
	 *
	 * @return mixed
	 */
	public function delete_authorization_id() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_authorization_id' );
	}
	
	/**
	 * Deletes the is payment captured mark
	 *
	 * @since 3.2.0
	 */
	public function delete_is_payment_captured() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_is_payment_captured' );
	}
	
	/**
	 * Deletes the amount captured value
	 *
	 * @since 3.2.0
	 */
	public function delete_order_amount_captured() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_order_amount_captured' );
	}
	
	/**
	 * Deletes the amount authorized meta
	 *
	 * @since 3.2.0
	 *
	 * @return bool|int
	 */
	public function delete_order_amount_authorized() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_order_amount_authorized' );
	}
	
	/**
	 * Deletes the payment type details
	 *
	 * @since 3.2.0
	 *
	 * @return bool|int
	 */
	public function delete_payment_type_detials() {
		if ( WC_Compatibility::is_wc_3_0() ) {
			$this->order->delete_meta_data( '_netbanx_transaction_payment_type' );
			$this->order->delete_meta_data( '_netbanx_transaction_card_brand' );
			$this->order->delete_meta_data( '_netbanx_transaction_card_last_four' );
			$this->order->delete_meta_data( '_netbanx_transaction_card_expiration' );
			
			return $this->order->save();
		}
		
		delete_post_meta( WC_Compatibility::get_order_id( $this->order ), '_netbanx_transaction_payment_type' );
		delete_post_meta( WC_Compatibility::get_order_id( $this->order ), '_netbanx_transaction_card_brand' );
		delete_post_meta( WC_Compatibility::get_order_id( $this->order ), '_netbanx_transaction_card_last_four' );
		delete_post_meta( WC_Compatibility::get_order_id( $this->order ), '_netbanx_transaction_card_expiration' );
		
		return true;
	}
	
	public function delete_order_profile_id() {
		return WC_Compatibility::delete_meta( $this->order, '_netbanx_hosted_order_profile_id' );
	}
	
	public function delete_order_profile_token() {
		return WC_Compatibility::delete_meta( $this->order, '_netbanx_hosted_order_profile_token' );
	}
	
	public function delete_source_id() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_source_id' );
	}
	
	public function delete_order_payment_attempts() {
		return WC_Compatibility::delete_meta( $this->order, '_netbanx_order_payment_attempts' );
	}
	
	/**---------------------------------------------------
	 * Payments API Fields
	 * ---------------------------------------------------*/
	
	public function get_payments_customer_id() {
		$customer_id = WC_Compatibility::get_meta( $this->order, '_paysafe_payments_customer_id' );
		
		return $customer_id;
	}
	
	public function save_payments_customer_id( $value ) {
		WC_Compatibility::update_meta( $this->order, '_paysafe_payments_customer_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	public function delete_payments_customer_id() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_payments_customer_id' );
	}
	
	public function get_payments_merchant_customer_id() {
		$customer_id = WC_Compatibility::get_meta( $this->order, '_paysafe_payments_merchant_customer_id' );
		
		return $customer_id;
	}
	
	public function save_payments_merchant_customer_id( $value ) {
		WC_Compatibility::update_meta( $this->order, '_paysafe_payments_merchant_customer_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	public function delete_payments_merchant_customer_id() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_payments_merchant_customer_id' );
	}
	
	public function get_google_pay_payment_handle_id() {
		$id = WC_Compatibility::get_meta( $this->order, '_paysafe_google_pay_payment_handle_id' );
		
		return $id;
	}
	
	public function save_google_pay_payment_handle_id( $value ) {
		WC_Compatibility::update_meta( $this->order, '_paysafe_google_pay_payment_handle_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	public function delete_google_pay_payment_handle_id() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_google_pay_payment_handle_id' );
	}
	
	public function get_single_use_payment_handle_id() {
		$id = WC_Compatibility::get_meta( $this->order, '_paysafe_single_use_payment_handle_id' );
		
		return $id;
	}
	
	public function save_single_use_payment_handle_id( $value ) {
		WC_Compatibility::update_meta( $this->order, '_paysafe_single_use_payment_handle_id', wc_clean( wp_unslash( $value ) ) );
	}
	
	public function delete_single_use_payment_handle_id() {
		return WC_Compatibility::delete_meta( $this->order, '_paysafe_single_use_payment_handle_id' );
	}
	
	
	
	/**---------------------------------------------------
	 * Functional Checks
	 * ---------------------------------------------------*/
	
	/**
	 * Returns true, if order contains Subscription
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function contains_subscription() {
		if ( ! Paysafe::is_subscriptions_active() ) {
			return false;
		}
		
		if ( wcs_order_contains_subscription( $this->order )
		     || wcs_order_contains_renewal( $this->order ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns whether or not the order is a WC_Subscription
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function is_subscription() {
		if ( ! Paysafe::is_subscriptions_active() ) {
			return false;
		}
		
		return wcs_is_subscription( $this->order );
	}
	
	/**
	 * Returns true, if order contains Pre-Order
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function contains_pre_order() {
		if ( ! Paysafe::is_pre_orders_active() ) {
			return false;
		}
		
		return \WC_Pre_Orders_Order::order_contains_pre_order( $this->order );
	}
	
	/**
	 * Returns true if the order is a pre-order and it requires tokenization(charged at release)
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function is_pre_order_with_tokenization() {
		if ( ! Paysafe::is_pre_orders_active() ) {
			return false;
		}
		
		return \WC_Pre_Orders_Order::order_contains_pre_order( $this->order )
		       && \WC_Pre_Orders_Order::order_requires_payment_tokenization( $this->order );
	}
}