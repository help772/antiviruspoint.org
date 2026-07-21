<?php

namespace WcPaysafe\Api_Payments\Data_Sources;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implementation of the Order data source
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Order_Source extends Data_Source_Abstract {
	
	public $source_type = 'order';
	
	public function __construct( \WC_Order $order ) {
		$this->source = $order;
	}
	
	public function get_address_field( $name, $type = 'billing' ) {
		return WC_Compatibility::get_order_prop( $this->get_source(), $type . '_' . $name );
	}
	
	/**
	 * @return mixed
	 */
	public function get_description() {
		return apply_filters( 'wc_paysafe_request_description', sprintf( __( 'Paying for order #%s', 'wc_paysafe' ), $this->get_source()->get_order_number() ), $this->get_source(), $this->get_source_type() );
	}
	
	public function return_url() {
		return $this->get_source()->get_checkout_order_received_url();
	}
	
	public function get_cancel_url() {
		return $this->get_source()->get_cancel_order_url();
	}
	
	public function get_initial_transaction_id() {
		/**
		 * 1. Get all subscription orders
		 * 2. Go through each order from first to last placed
		 *      2.1. Get the transaction ID of the first paid with Paysafe
		 * 3. If there is no order paid with Paysafe (customer changed payment method to Paysafe)
		 *      3.1. On the change payment method add the transaction ID for the change
		 */
		
		$subscriptions = array( $this->get_source() );
		if ( ! wcs_is_subscription( $this->get_source()->get_id() ) ) {
			$subscriptions = wcs_get_subscriptions_for_order( $this->get_source(), array( 'order_type' => 'any' ) );
		}
		
		// If there is no subscription, we will treat the order as the initial order
		if ( ! $subscriptions ) {
			$paysafe_order = new Paysafe_Order( $this->get_source() );
			
			return $paysafe_order->get_authorization_id();
		}
		
		foreach ( $subscriptions as $subscription ) {
			
			$ps_sub = new Paysafe_Order( $subscription );
			
			// If the subscription has an initial ID, then use it
			if ( '' != $ps_sub->get_authorization_id() ) {
				return $ps_sub->get_authorization_id();
			}
			
			$related_orders = array_reverse( $subscription->get_related_orders() );
			
			foreach ( $related_orders as $order_id ) {
				$order = new \WC_Order( $order_id );
				
				if ( 'netbanx' != $order->get_payment_method()
				     && 'paysafe_checkout_payments' != $order->get_payment_method() ) {
					continue;
				}
				
				$paysafe_order = new Paysafe_Order( $order );
				$initial_id    = $paysafe_order->get_authorization_id();
				
				if ( ! $initial_id ) {
					continue;
				}
				
				// Set the initial ID to the subscription for future use
				$ps_sub->save_authorization_id( $initial_id );
				
				return $initial_id;
			}
		}
		
		return false;
	}
}