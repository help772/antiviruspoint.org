<?php

namespace WcPaysafe\Api;

use WcPaysafe\Api\Config\Configuration_Abstract;
use WcPaysafe\Api\Config\Redirect;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Order;

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
class Complex_Fields {
	
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
	 * @param Data_Source_Interface|Order_Source|User_Source $data_source
	 * @param Configuration_Abstract|Redirect                $configuration
	 */
	public function __construct( $data_source, $configuration ) {
		$this->gateway_config = $configuration;
		$this->data_source    = $data_source;
	}
	
	public function get_source() {
		return $this->data_source;
	}
	
	/**
	 * Returns the profile fields
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function get_profile_fields() {
		return array(
			'firstName' => $this->get_source()->get_billing_first_name(),
			'lastName'  => $this->get_source()->get_billing_last_name(),
			'email'     => $this->get_source()->get_billing_email(),
		);
	}
	
	public function get_description() {
		return Formatting::format_string( $this->get_source()->get_description(), 255, true, '...' );
	}
	
	/**
	 * Returns order suffix, to prevent duplicate order reference numbers
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param string    $type
	 *
	 * @return int
	 */
	public function get_attempts_suffix( \WC_Order $order, $type = 'order' ) {
		// Add a retry count suffix to the orderID.
		$ps_order        = new Paysafe_Order( $order );
		$attempts        = $ps_order->get_order_payment_attempts( $type );
		$attempts_suffix = 0;
		
		if ( is_numeric( $attempts ) ) {
			$attempts_suffix = $attempts;
			
			$attempts_suffix ++;
		}
		
		// Save the incremented attempts
		$ps_order->save_order_payment_attempts( $attempts_suffix, $type );
		
		return $attempts_suffix;
	}
	
	/**
	 * Returns the payment request billing fields
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_billing_fields() {
		
		$billing = array(
			'city'    => Formatting::format_string( $this->get_source()->get_billing_city(), 40 ),
			'country' => Formatting::format_string( $this->get_source()->get_billing_country(), 2 ),
			'street'  => Formatting::format_string( $this->get_source()->get_billing_address_1(), 50 ),
			'street2' => Formatting::format_string( $this->get_source()->get_billing_address_2(), 50 ),
			'zip'     => Formatting::format_string( $this->get_source()->get_billing_postcode(), 10 ),
			'state'   => '' == $this->get_source()->get_billing_state() ? Formatting::format_string( $this->get_source()->get_billing_city(), 40 ) : Formatting::format_string( $this->get_source()->get_billing_state(), 40 ),
			
			'phone' => Formatting::format_string( $this->get_source()->get_billing_phone(), 40 ),
		);
		
		// Remove empty elements
		$billing = array_filter( $billing );
		
		return $billing;
	}
	
	/**
	 * Returns the payment request shipping fields
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function get_shipping_fields() {
		
		$shipping = array(
			'recipientName' => Formatting::format_string( $this->get_source()->get_shipping_full_name(), 255 ),
			'city'          => Formatting::format_string( $this->get_source()->get_shipping_city(), 40 ),
			'country'       => Formatting::format_string( $this->get_source()->get_shipping_country(), 2 ),
			'street'        => Formatting::format_string( $this->get_source()->get_shipping_address_1(), 50 ),
			'zip'           => Formatting::format_string( $this->get_source()->get_shipping_postcode(), 10 ),
			'state'         => '' == $this->get_source()->get_shipping_state() ? Formatting::format_string( $this->get_source()->get_shipping_city(), 40 ) : Formatting::format_string( $this->get_source()->get_shipping_state(), 40 ),
		);
		
		// Remove empty elements
		$shipping = array_filter( $shipping );
		
		return $shipping;
	}
}