<?php

namespace WcPaysafe\Gateways\Redirect;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Compatibility\WC_Subscriptions_Compatibility;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015-2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Gateway_Addons extends Gateway {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		
		// Load hooks
		$this->hooks();
	}
	
	public function hooks() {
		
		if ( true === self::$loaded ) {
			return;
		}
		
		if ( Paysafe::is_subscriptions_active() && WC_Subscriptions_Compatibility::is_equal_or_gtr( '2.0.0' ) ) {
			// Scheduled payment
			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array(
				$this,
				'scheduled_subscription_payment_request',
			), 10, 2 );
			
			// Meta data renewal remove
			add_action( 'wcs_resubscribe_order_created', array( $this, 'remove_renewal_order_meta' ), 10 );
			
			// Update change payment method
			add_action( 'woocommerce_subscription_failing_payment_method_updated_' . $this->id, array(
				$this,
				'changed_failing_payment_method',
			), 10, 2 );
			
			// Display card used details
			add_filter( 'woocommerce_my_subscriptions_payment_method', array(
				$this,
				'maybe_render_subscription_payment_method',
			), 10, 2 );
			
			// Handle display of the Admin facing payment method change
			add_filter( 'woocommerce_subscription_payment_meta', array(
				$this,
				'add_subscription_payment_meta',
			), 10, 2 );
			// Handle validation of the Admin facing payment method change
			add_filter( 'woocommerce_subscription_validate_payment_meta', array(
				$this,
				'validate_subscription_payment_meta',
			), 10, 2 );
		}
		
		// Add support for Pre-Orders
		if ( Paysafe::is_pre_orders_active() ) {
			add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array(
				$this,
				'process_pre_order_release_payment',
			) );
		}
		
		// Load the parent hooks
		parent::hooks();
	}
	
	/**
	 * Don't transfer Paysafe meta to resubscribe orders.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $resubscribe_order The order created for the customer to resubscribe to the old expired/cancelled subscription
	 *
	 * @return void
	 */
	public function remove_renewal_order_meta( $resubscribe_order ) {
		$this->get_integration_object()->remove_renewal_order_meta( $resubscribe_order );
	}
	
	/**
	 * Perform a subscription scheduled payment
	 *
	 * @since 2.0
	 *
	 * @param           $amount_to_charge
	 * @param \WC_Order $renewal_order
	 */
	public function scheduled_subscription_payment_request( $amount_to_charge, $renewal_order ) {
		$this->get_integration_object()->scheduled_subscription_payment_request( $amount_to_charge, $renewal_order );
	}
	
	/**
	 * Check the payment response and process the order
	 *
	 * @since 2.0
	 */
	public function process_server_to_server_response() {
		if ( 'checkoutjs' == $this->integration ) {
			return;
		}
		
		$this->get_integration_object()->process_server_to_server_response( 'hosted_addons' );
	}
	
	/**
	 * Add the Transaction ID to a changed failing payment method
	 *
	 * @since 2.0
	 *
	 * @param $subscription
	 * @param $renewal_order
	 */
	public function changed_failing_payment_method( $subscription, $renewal_order ) {
		$this->get_integration_object()->changed_failing_payment_method( $subscription, $renewal_order );
	}
	
	/**
	 * Display the payment method info to the customer.
	 *
	 * @since 2.0
	 *
	 * @param                  $payment_method_to_display
	 * @param \WC_Subscription $subscription
	 *
	 * @return string
	 */
	public function maybe_render_subscription_payment_method( $payment_method_to_display, $subscription ) {
		if ( $this->id !== WC_Compatibility::get_order_prop( $subscription, 'payment_method' ) ) {
			return $payment_method_to_display;
		}
		
		$ps_subscription = new Paysafe_Order( $subscription );
		
		// TODO: Together with the "get_payment_type_details",
		//      we should just call the "get_payment_type_details" and return its string value
		
		$payment_type = $ps_subscription->get_payment_type_details();
		$last4        = $payment_type['last4'];
		$type         = $payment_type['type'];
		$brand        = $payment_type['brand'];
		
		// If could not find any card info on the subscription try the order.
		if ( '' == $last4 ) {
			$ps_subscription = new Paysafe_Order( WC_Subscriptions_Compatibility::get_parent( $subscription ) );
			
			$payment_type = $ps_subscription->get_payment_type_details();
			$last4        = $payment_type['last4'];
			$type         = $payment_type['type'];
			$brand        = $payment_type['brand'];
		}
		
		// If we found at least the last four digits and either the type or brand, display the data.
		if ( '' == $last4 || ( '' == $type && '' == $brand ) ) {
			return $payment_method_to_display;
		}
		
		if ( 'card' != $type ) {
			$payment_method_to_display = sprintf( __( 'Via %1$s with account ending in %2$s', 'wc_paysafe' ), $type, $last4 );
		} else {
			$payment_method_to_display = sprintf( __( 'Via %1$s ending in %2$s', 'wc_paysafe' ), '' != $brand ? $brand : $type, $last4 );
		}
		
		return $payment_method_to_display;
	}
	
	/**
	 * Add payment method change fields
	 *
	 * @since   2.0
	 * @version 3.3.0
	 *
	 * @param $payment_meta
	 * @param $subscription
	 *
	 * @return mixed
	 */
	public function add_subscription_payment_meta( $payment_meta, $subscription ) {
		// Make sure we create a new subscription because the passed one has old data
		$subscription_new = wcs_get_subscription( $subscription->get_id() );
		
		$ps_subscription = new Paysafe_Order( $subscription_new );
		$ps_order_id     = $ps_subscription->get_payment_order_id();
		$ps_token        = $ps_subscription->get_order_profile_token();
		
		if ( 'hosted' == $this->integration ) {
			$payment_meta[ $this->id ] = array(
				'post_meta' => array(
					'_netbanx_payment_order_id' => array(
						'value' => $ps_order_id,
						'label' => 'Paysafe Payment Transaction ID (required)',
					),
				),
			);
		} else {
			if ( $ps_order_id ) {
				$payment_meta[ $this->id ] = array(
					'post_meta' => array(
						'_netbanx_payment_order_id' => array(
							'value' => $ps_order_id,
							'label' => 'Paysafe Payment Transaction ID (legacy)',
						),
					),
				);
			}
			
			$payment_meta[ $this->id ] = array(
				'post_meta' => array(
					'_netbanx_hosted_order_profile_token' => array(
						'value' => $ps_token,
						'label' => 'Paysafe Payment Token (required)',
					),
				),
			);
		}
		
		return $payment_meta;
	}
	
	/**
	 * Validate Payment method change
	 *
	 * @since   2.0
	 * @version 3.3.0
	 *
	 * @param $payment_method_id
	 * @param $payment_meta
	 *
	 * @throws \Exception
	 */
	public function validate_subscription_payment_meta( $payment_method_id, $payment_meta ) {
		if ( $this->id === $payment_method_id ) {
			if ( 'hosted' == $this->integration ) {
				if ( ! isset( $payment_meta['post_meta']['_netbanx_payment_order_id']['value'] )
				     || empty( $payment_meta['post_meta']['_netbanx_payment_order_id']['value'] )
				) {
					throw new \Exception( 'A Paysafe Payment Transaction ID value is required.' );
				}
			} else {
				
				if ( empty( $payment_meta['post_meta']['_netbanx_hosted_order_profile_token']['value'] )
				     && empty( $payment_meta['post_meta']['_netbanx_payment_order_id']['value'] )
				) {
					throw new \Exception( 'Oops, we need at Paysafe Transaction ID or Paysafe Payment Token to be populated.' );
				}
			}
		}
	}
	
	/**
	 * Charge the payment on order release
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_pre_order_release_payment( \WC_Order $order ) {
		$this->get_integration_object()->process_pre_order_release_payment( $order );
	}
	
	/**
	 * Returns true, if order contains Subscription
	 *
	 * @since      2.0
	 *
	 * @deprecated 3.3.0 Use the Paysafe_Order::order_contains_subscription instead
	 *
	 * @param \WC_Order $order
	 *
	 * @return bool
	 */
	public function order_contains_subscription( \WC_Order $order ) {
		$paysafe_order = new Paysafe_Order( $order );
		
		return $paysafe_order->contains_subscription();
	}
	
	/**
	 * Returns true, if order contains Pre-Order
	 *
	 * @since      2.0
	 *
	 * @deprecated 3.3.0 Use the Paysafe_Order::contains_pre_order instead
	 *
	 * @param \WC_Order $order
	 *
	 * @return bool
	 */
	public function order_contains_pre_order( \WC_Order $order ) {
		$paysafe_order = new Paysafe_Order( $order );
		
		return $paysafe_order->contains_pre_order();
	}
}