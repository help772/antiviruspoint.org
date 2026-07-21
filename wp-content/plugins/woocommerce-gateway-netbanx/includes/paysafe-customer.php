<?php

namespace WcPaysafe;

use WcPaysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order wrapper for gateway order data
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Paysafe_Customer {
	
	/**
	 * @var \WC_Customer
	 */
	public $customer;
	public $user;
	
	/**
	 * Paysafe_Order constructor.
	 *
	 * TODO: Why not make the user WC_Customer instead of WP_User. It will allow be much easier method transitions
	 *
	 * @param \WP_User $user
	 *
	 * @throws \Exception
	 */
	public function __construct( $user ) {
		$this->user     = $user;
		$this->customer = new \WC_Customer( (int) $this->get_id() );
	}
	
	/**---------------------------------
	 * GETTERS
	 * -----------------------------------*/
	
	/**
	 * Returns the user ID
	 * @return int
	 */
	public function get_id() {
		return $this->user->ID;
	}
	
	public function get_customer() {
		return $this->customer;
	}
	
	public function get_user() {
		return $this->user;
	}
	
	public function get_meta_prop( $key, $single = true ) {
		return get_user_meta( $this->get_id(), $key, $single );
	}
	
	/**
	 * Generates a profile ID for the customer
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function generate_merchant_customer_id( $prefix = 'wc-paysafe-' ) {
		$id = uniqid( apply_filters( 'wc_paysafe_merchant_customer_id_prefix', $prefix, $this->get_id() ) ) . '-' . $this->get_id();
		
		$this->save_merchant_customer_id( $id );
		
		return $id;
	}
	
	/**
	 * Returns the customer unique merchant customer ID
	 * @since 3.3.0
	 *
	 * @param string $prefix
	 *
	 * @return mixed
	 */
	public function get_merchant_customer_id( $prefix = 'wc-paysafe-' ) {
		$id = $this->get_meta_prop( '_paysafe_merchant_customer_id' );
		if ( empty( $id ) ) {
			return $this->generate_merchant_customer_id( $prefix );
		}
		
		return $id;
	}
	
	/**
	 * Returns the customer vault profile id
	 *
	 * @since 3.3.0
	 *
	 * @return bool|mixed
	 */
	public function get_vault_profile_id() {
		$id = $this->get_meta_prop( $this->get_vault_profile_id_field_name() );
		if ( empty( $id ) ) {
			return false;
		}
		
		return $id;
	}
	
	/**
	 * The customer date created
	 * @since 3.7.0
	 * @return \WC_DateTime|null
	 */
	public function get_date_created() {
		return $this->get_customer()->get_date_created();
	}
	
	/**
	 * The customer list date updated
	 * @since 3.7.0
	 * @return \WC_DateTime|null
	 */
	public function get_last_updated() {
		return $this->get_customer()->get_date_modified();
	}
	
	/**
	 * Get DateTime for first shipping address usage
	 *
	 * @since 3.7.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 * @return \WC_DateTime|\DateTime
	 */
	public function get_shipping_address_first_use( $order ) {
		$arguments = array(
			'customer'           => $this->get_customer()->get_id(),
			'limit'              => 1,
			'orderby'            => 'date',
			'order'              => 'ASC',
			'ids'                => true,
			'shipping_address_1' => $order->get_shipping_address_1(),
		);
		
		/** @var array $orders */
		$orders         = $this->get_orders( $arguments );
		$first_order_id = reset( $orders );
		
		/** @var \WC_Order $first_order */
		$first_order    = wc_get_order( $first_order_id );
		$first_use_date = new \DateTime();
		if ( $first_order ) {
			$first_use_date = $first_order->get_date_created();
		}
		
		return $first_use_date;
	}
	
	/**
	 * Get successful orders within last six months
	 * Successful order status includes:
	 *  - processing (purchase/debit) - standard WC Order State
	 *  - completed - standard WC Order State for shipped orders
	 *  - refunded - standard WC Order State for refunded orders (still successful)
	 *  - cancelled - standard WC Order State for cancelled order (still successful)
	 *
	 * @since 3.7.0
	 *
	 * @return int
	 */
	public function get_successful_orders_last_six_months() {
		$arguments = array(
			'customer'   => $this->get_customer()->get_id(),
			'limit'      => - 1,
			'ids'        => true,
			'status'     => apply_filters( 'wc_paysafe_successful_order_statuses', array(
				'processing',
				'completed',
				'refunded',
				'cancelled',
			) ),
			'date_after' => '6 months ago',
		);
		$orders    = $this->get_orders( $arguments );
		
		return count( $orders );
	}
	
	/**
	 * Get array with wc order data according to arguments
	 * Override paginate = false to avoid stdClass
	 *
	 * @since 3.7.0
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private function get_orders( $args ) {
		$no_paginate = array(
			'paginate' => false,
		);
		$args        = array_merge( $no_paginate, $args );
		$orders      = wc_get_orders( $args );
		
		return $orders;
	}
	
	/**
	 * Checks if the customer is re-ordering at least one of the items
	 *
	 * TODO: Not sure if we need to check if the customer has a previous order or if they are re-ordering at least one of the items
	 *
	 * @param \WC_Order $order
	 *
	 * @return string
	 * @since 2.1.0
	 */
	public function did_customer_ordered_before( $order ) {
		$result = 'FIRST_TIME_ORDER';
		
		/** @var \WC_Order_Item_Product $item */
		foreach ( $order->get_items() as $item ) {
			if ( $item->is_type( 'line_item' )
			     && wc_customer_bought_product( $this->get_user()->user_email, $this->get_id(), $item->get_product_id() )
			) {
				$result = 'REORDER';
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * Retrieves all profile vault sources
	 *
	 * @since 3.3.0
	 *
	 * @param string $gateway_id
	 * @param array  $args
	 *
	 * @throws \Exception
	 * @return WcPaysafe\Api\Vault\Responses\Profiles
	 */
	public function get_vault_sources( $gateway_id, $args = array() ) {
		$gateway = WcPaysafe\Helpers\Factories::get_gateway( $gateway_id );
		
		$api_client = WcPaysafe\Helpers\Factories::get_api_client( $gateway, 'directdebit' );
		
		$defaults = array(
			'addresses' => false,
			'cards'     => false,
			'ach'       => false,
			'eft'       => false,
			'bacs'      => false,
			'sepa'      => false,
		);
		
		$params = wp_parse_args( $args, $defaults );
		
		$profile = $api_client->get_vault_service()->profile()->get(
			array( 'id' => $this->get_vault_profile_id() ),
			$params['addresses'],
			$params['cards'],
			$params['ach'],
			$params['eft'],
			$params['bacs'],
			$params['sepa']
		);
		
		return $profile;
	}
	
	/**
	 * Returns the meta field name of the vault profile ID.
	 * We need this because a merchant can have two or more completely separate accounts for testing and live transactions.
	 * Profiles on the testing account will not work on the live account because the system sees them as separate merchants.
	 * To make it easier for the merchant to work with those accounts, we will use separate profile account id meta names.
	 * This is not a foolproof way to handle the separate accounts, but it will help.
	 *
	 * @since 3.3.0
	 *
	 * @return mixed
	 */
	public function get_vault_profile_id_field_name() {
		$gateway = WcPaysafe\Helpers\Factories::get_gateway( 'netbanx' );
		
		if ( 'yes' == $gateway->testmode ) {
			$field_name = '_paysafe_testmode_vault_profile_id';
		} else {
			$field_name = '_paysafe_vault_profile_id';
		}
		
		return apply_filters( 'wc_paysafe_vault_profile_field_name', $field_name, $gateway );
	}
	
	/**---------------------------------------------------
	 * CREATE
	 * ---------------------------------------------------*/
	
	/**
	 * @since 3.3.0
	 *
	 * @param        $key
	 * @param        $value
	 * @param bool   $unique (False)
	 *
	 * @return bool|int
	 */
	public function add_meta_prop( $key, $value, $unique = false ) {
		return add_user_meta( $this->get_id(), $key, wc_clean( wp_unslash( $value ) ), $unique );
	}
	
	/**---------------------------------------------------
	 * UPDATE
	 * ---------------------------------------------------*/
	
	/**
	 * @since 3.3.0
	 *
	 * @param        $key
	 * @param        $value
	 * @param string $prev_value
	 *
	 * @return bool|int
	 */
	public function update_meta_prop( $key, $value, $prev_value = '' ) {
		return update_user_meta( $this->get_id(), $key, wc_clean( wp_unslash( $value ) ), $prev_value );
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param string $value
	 * @param string $prev_value
	 */
	public function save_merchant_customer_id( $value, $prev_value = '' ) {
		$this->update_meta_prop( '_paysafe_merchant_customer_id', $value, $prev_value );
	}
	
	/**
	 * TODO: IMPORTANT: Make a note to the Admin in the admin settings that changing the live account can result in failure all saved tokens!!!
	 *
	 * @since 3.3.0
	 *
	 * @param        $value
	 * @param string $prev_value
	 */
	public function save_vault_profile_id( $value, $prev_value = '' ) {
		$this->update_meta_prop( $this->get_vault_profile_id_field_name(), $value, $prev_value );
	}
	
	/**---------------------------------------------------
	 * DELETE
	 * ---------------------------------------------------*/
	
	/**
	 * Deletes user meta value
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return bool
	 */
	public function delete_meta_prop( $key, $value = '' ) {
		return delete_user_meta( $this->get_id(), $key, $value );
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param $value
	 *
	 * @return bool|int
	 */
	public function delete_merchant_customer_id( $value = '' ) {
		return $this->delete_meta_prop( '_paysafe_merchant_customer_id', $value );
	}
	
	/**
	 * @param string $value
	 *
	 * @return bool
	 */
	public function delete_vault_profile_id( $value = '' ) {
		return $this->delete_meta_prop( $this->get_vault_profile_id_field_name(), $value );
	}
	
	/**---------------------------------------------------
	 * Legacy profile methods
	 * ---------------------------------------------------*/
	
	/**
	 * Returns profile ID saved to the user meta
	 *
	 * @since 3.3.0
	 *
	 * @return mixed
	 */
	public function get_legacy_profile_id() {
		return get_user_meta( $this->get_id(), '_netbanx_hosted_customer_profile_id', true );
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param $value
	 */
	public function save_legacy_profile_id( $value ) {
		update_user_meta( $this->get_id(), '_netbanx_hosted_customer_profile_id', $value );
	}
	
	/**
	 * Returns profile token saved to the user meta
	 *
	 * @since 3.3.0
	 *
	 * @return mixed
	 */
	public function get_legacy_profile_token() {
		return get_user_meta( $this->get_id(), '_netbanx_hosted_customer_profile_token', true );
	}
	
	/**
	 * @since 3.3.0
	 *
	 * @param $value
	 */
	public function save_legacy_profile_token( $value ) {
		update_user_meta( $this->get_id(), '_netbanx_hosted_customer_profile_token', $value );
	}
	
	/**=================================
	 * Payments API / Gateway fields
	 * ===================================*/
	
	public function get_payments_merchant_customer_id() {
		$id = $this->get_meta_prop( $this->get_payments_merchant_customer_id_field_name() );
		if ( empty( $id ) ) {
			return false;
		}
		
		return $id;
	}
	
	public function save_payments_merchant_customer_id( $value, $prev_value = '' ) {
		$this->update_meta_prop( $this->get_payments_merchant_customer_id_field_name(), $value, $prev_value );
	}
	
	public function get_payments_merchant_customer_id_field_name() {
		$gateway = WcPaysafe\Helpers\Factories::get_gateway( 'paysafe_checkout_payments' );
		
		if ( 'yes' == $gateway->testmode ) {
			$field_name = '_paysafe_testmode_payments_merchant_customer_id';
		} else {
			$field_name = '_paysafe_payments_merchant_customer_id';
		}
		
		return apply_filters( 'wc_paysafe_payments_merchant_customer_id_field_name', $field_name, $gateway );
	}
	
	public function get_payments_customer_id() {
		$id = $this->get_meta_prop( $this->get_payments_customer_id_field_name() );
		if ( empty( $id ) ) {
			return false;
		}
		
		return $id;
	}
	
	public function save_payments_customer_id( $value, $prev_value = '' ) {
		$prev_id = $this->get_payments_customer_id();
		
		// TODO: Should we be able to overwrite it? Once a customer has an ID, they should keep it for their lifetime on the store
		if ( $prev_id ) {
			return;
		}
		
		$this->update_meta_prop( $this->get_payments_customer_id_field_name(), $value, $prev_value );
	}
	
	public function get_payments_customer_id_field_name() {
		$gateway = WcPaysafe\Helpers\Factories::get_gateway( 'paysafe_checkout_payments' );
		
		if ( 'yes' == $gateway->testmode ) {
			$field_name = '_paysafe_testmode_payments_customer_id';
		} else {
			$field_name = '_paysafe_payments_customer_id';
		}
		
		return apply_filters( 'wc_paysafe_payments_customer_id_field_name', $field_name, $gateway );
	}
}