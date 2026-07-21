<?php

namespace WcPaysafe\Api_Payments\Request_Fields;

use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Customer;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Common_Fields extends Fields_Abstract {
	
	/**
	 * Returns the profile fields
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function get_profile_fields() {
		$profile = array(
			'firstName' => $this->get_billing_first_name(),
			'lastName'  => $this->get_billing_last_name(),
			'email'     => $this->get_billing_email(),
		);
		
		$profile_id = $this->get_customer_unique_merchant_customer_id();
		if ( $profile_id ) {
			$profile['merchantCustomerId'] = $profile_id;
		}
		
		return $profile;
	}
	
	public function get_customer_unique_merchant_customer_id() {
		if ( $this->get_source()->get_user_id() ) {
			$paysafe_customer = new Paysafe_Customer( $this->get_source()->get_user() );
			$profile_id       = $paysafe_customer->get_payments_merchant_customer_id();
			if ( ! $profile_id ) {
				$profile_id = $this->get_source()->get_user_id() . '_';
				$profile_id .= substr( uniqid(), 0, 10 - strlen( $profile_id ) );
				
				$paysafe_customer->save_payments_merchant_customer_id( $profile_id );
			}
			
			return $profile_id;
		}
		
		return false;
	}
	
	public function get_description() {
		// TODO: make this of the order details
		return Formatting::format_string( $this->get_source()->get_description(), 255, true, '...' );
	}
	
	/**
	 * Returns order suffix, to prevent duplicate order reference numbers
	 * Wrapper of the Paysafe_Order::get_attempts_suffix();
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param string    $type
	 *
	 * @return string
	 */
	public function get_attempts_suffix( \WC_Order $order, $type = 'order' ) {
		// Add a retry count suffix to the orderID.
		$ps_order = new Paysafe_Order( $order );
		
		return $ps_order->get_attempts_suffix( $type );
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
			'city'    => Formatting::format_string( $this->get_billing_city(), 40 ),
			'country' => Formatting::format_string( $this->get_billing_country(), 2 ),
			'street'  => Formatting::format_string( $this->get_billing_address_1(), 50 ),
			'street2' => Formatting::format_string( $this->get_billing_address_2(), 50 ),
			'zip'     => Formatting::format_string( $this->get_billing_postcode(), 10 ),
			'state'   => Formatting::format_string( $this->get_billing_state(), 40 ),
			'phone'   => Formatting::format_string( $this->get_billing_phone(), 40 ),
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
			'recipientName' => Formatting::format_string( $this->get_shipping_full_name(), 255 ),
			'city'          => Formatting::format_string( $this->get_shipping_city(), 40 ),
			'country'       => Formatting::format_string( $this->get_shipping_country(), 2 ),
			'street'        => Formatting::format_string( $this->get_shipping_address_1(), 50 ),
			'zip'           => Formatting::format_string( $this->get_shipping_postcode(), 10 ),
			'state'         => Formatting::format_string( $this->get_shipping_state(), 40 ),
		);
		
		// Remove empty elements
		$shipping = array_filter( $shipping );
		
		return $shipping;
	}
}