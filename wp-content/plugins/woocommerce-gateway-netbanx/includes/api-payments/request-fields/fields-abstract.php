<?php

namespace WcPaysafe\Api_Payments\Request_Fields;

use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Data_Sources\User_Source;

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
abstract class Fields_Abstract {
	
	protected $gateway_config;
	protected $data_source;
	protected $parameters = array();
	
	/**
	 * Complex_Fields constructor.
	 *
	 * TODO:
	 *      1. Should receive the (gateway) $configuration, (order or user) $data_object
	 *      2. Should load all possible fields for the Cards, DirectDebit, Vault and Alternative APIs.
	 *              2.1. An abstract class should cover all overlapping fields between the APIs and a loaded concrete class should cover the rest
	 *      3. Fields should be generated based on the provided $data_object, so we can always cover orders and user data with the same fields
	 *
	 * @param Order_Source|User_Source|Data_Source_Interface $data_source
	 */
	public function __construct( $data_source ) {
		$this->data_source = $data_source;
	}
	
	/**
	 * @return Order_Source|User_Source|Data_Source_Interface
	 */
	public function get_source() {
		return $this->data_source;
	}
	
	public function get_billing_first_name() {
		return $this->get_source()->get_address_field( 'first_name', 'billing' );
	}
	
	public function get_billing_last_name() {
		return $this->get_source()->get_address_field( 'last_name', 'billing' );
	}
	
	public function get_billing_full_name() {
		return $this->get_billing_first_name() . ' ' . $this->get_billing_last_name();
	}
	
	public function get_billing_city() {
		return $this->get_source()->get_address_field( 'city', 'billing' );
	}
	
	public function get_billing_state() {
		return $this->get_source()->get_address_field( 'state', 'billing' );
	}
	
	public function get_billing_country() {
		return $this->get_source()->get_address_field( 'country', 'billing' );
	}
	
	public function get_billing_postcode() {
		return $this->get_source()->get_address_field( 'postcode', 'billing' );
	}
	
	public function get_billing_address_1() {
		return $this->get_source()->get_address_field( 'address_1', 'billing' );
	}
	
	public function get_billing_address_2() {
		return $this->get_source()->get_address_field( 'address_2', 'billing' );
	}
	
	public function get_billing_phone() {
		return $this->get_source()->get_address_field( 'phone', 'billing' );
	}
	
	public function get_billing_email() {
		return $this->get_source()->get_address_field( 'email', 'billing' );
	}
	
	public function get_shipping_first_name() {
		return $this->get_source()->get_address_field( 'first_name', 'shipping' );
	}
	
	public function get_shipping_last_name() {
		return $this->get_source()->get_address_field( 'last_name', 'shipping' );
	}
	
	public function get_shipping_full_name() {
		return $this->get_shipping_first_name() . ' ' . $this->get_shipping_last_name();
	}
	
	public function get_shipping_city() {
		return $this->get_source()->get_address_field( 'city', 'shipping' );
	}
	
	public function get_shipping_state() {
		return $this->get_source()->get_address_field( 'state', 'shipping' );
	}
	
	public function get_shipping_country() {
		return $this->get_source()->get_address_field( 'country', 'shipping' );
	}
	
	public function get_shipping_postcode() {
		return $this->get_source()->get_address_field( 'postcode', 'shipping' );
	}
	
	public function get_shipping_address_1() {
		return $this->get_source()->get_address_field( 'address_1', 'shipping' );
	}
	
	public function get_shipping_address_2() {
		return $this->get_source()->get_address_field( 'address_2', 'shipping' );
	}
}