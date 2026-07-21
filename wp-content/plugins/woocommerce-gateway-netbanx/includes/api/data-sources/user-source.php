<?php

namespace WcPaysafe\Api\Data_Sources;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implementation of the User data source
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class User_Source extends Data_Source_Abstract {
	
	public $source_type = 'user';
	
	/**
	 * User constructor.
	 *
	 * @param \WP_User $user
	 */
	public function __construct( \WP_User $user ) {
		$this->source = $user;
	}
	
	public function get_address_field( $name, $type = 'billing', $single_value = true ) {
		$value = get_user_meta( $this->get_source()->ID, $type . '_' . $name, $single_value );
		
		// In case the billing email is missing
		if ( '' == $value ) {
			$this->get_source()->user_email;
		}
		
		return $value;
	}
	
	public function get_id() {
		return $this->source->ID;
	}
	
	/**
	 * @return int
	 */
	public function get_user_id() {
		return $this->get_id();
	}
	
	/**
	 * @return \WP_User
	 */
	public function get_user() {
		return $this->source;
	}
	
	public function get_billing_email() {
		$value = $this->get_address_field( 'email', 'billing' );
		
		if ( ! $value ) {
			$value = $this->get_source()->user_email;
		}
		
		return $value;
	}
	
	public function get_billing_country() {
		$value = $this->get_address_field( 'country', 'billing' );
		
		if ( ! $value ) {
			$value = WC()->countries->get_base_country();
		}
		
		return $value;
	}
	
	public function get_billing_state() {
		$value = $this->get_address_field( 'state', 'billing' );
		
		if ( ! $value ) {
			$value = WC()->countries->get_base_state();
		}
		
		return $value;
	}
	
	public function get_shipping_email() {
		$value = $this->get_address_field( 'email', 'shipping' );
		
		if ( ! $value ) {
			$value = $this->get_source()->user_email;
		}
		
		return $value;
	}
	
	public function get_shipping_country() {
		$value = $this->get_address_field( 'country', 'shipping' );
		
		if ( ! $value ) {
			$value = WC()->countries->get_base_country();
		}
		
		return $value;
	}
	
	public function get_shipping_state() {
		$value = $this->get_address_field( 'state', 'shipping' );
		
		if ( ! $value ) {
			$value = WC()->countries->get_base_state();
		}
		
		return $value;
	}
	
	public function get_description() {
		return apply_filters( 'wc_paysafe_request_description', '', $this->get_source(), $this->get_source_type() );
	}
	
	/**
	 * Returns the store currency
	 * @return string
	 */
	public function get_currency() {
		return get_woocommerce_currency();
	}
	
	public function return_url() {
		return wc_get_page_permalink( 'myaccount' );
	}
	
	public function get_cancel_url() {
		return wc_get_page_permalink( 'myaccount' );
	}
}