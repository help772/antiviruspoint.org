<?php
/**
 * WooCommerce CyberSource
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce CyberSource to newer
 * versions in the future. If you wish to customize WooCommerce CyberSource for your
 * needs please refer to http://docs.woocommerce.com/document/cybersource-payment-gateway/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Cybersource\API\Responses\Reporting\Conversion_Detail;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * Orders handler class.
 *
 * @since 2.3.0
 */
class Orders {


	/** @var string action for updating orders */
	const ACTION_UPDATE_ORDERS = 'wc_cybersource_update_orders';

	/** @var string option where the last order update timestamp is stored */
	const OPTION_LAST_ORDER_UPDATE = 'wc_cybersource_last_order_update';


	/**
	 * Orders constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {

		// schedule the order update routines
		add_action( 'init', [ $this, 'schedule_order_updates' ] );

		// handle the order update routine
		add_action( self::ACTION_UPDATE_ORDERS, [ $this, 'handle_order_updates' ] );
	}


	/**
	 * Schedules the order update routines.
	 *
	 * Only schedule a routine for a gateway if it's
	 * - Not inheriting another's settings
	 * - Configured and Decision Manager is enabled
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 */
	public function schedule_order_updates() {

		foreach ( $this->get_unique_gateways() as $gateway ) {

			$args = [ $gateway->get_id() ];

			if ( false === as_next_scheduled_action( self::ACTION_UPDATE_ORDERS, $args ) ) {
				as_schedule_recurring_action( time() + $this->get_order_update_interval(), $this->get_order_update_interval(), self::ACTION_UPDATE_ORDERS, $args, Plugin::PLUGIN_ID );
			}
		}
	}


	/**
	 * Handles the order update routine.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 *
	 * @param string $gateway_id gateway ID
	 */
	public function handle_order_updates( $gateway_id ) {
		global $wpdb;

		/** @var Gateway $gateway */
		$gateway = wc_cybersource()->get_gateway( $gateway_id );

		if ( ! $gateway ) {
			return;
		}

		try {

			$last_order_update = $this->get_last_order_update( $gateway->get_id() );

			// the limit is 24 hours, so check the last order update in case the plugin was disabled for a period of time
			if ( time() - $last_order_update > 24 * HOUR_IN_SECONDS ) {
				$last_order_update = time() - $this->get_order_update_interval();
			}

			$response = $gateway->get_api()->get_conversion_details( $gateway->get_merchant_id(), $last_order_update );

			// no API errors, so set now as the last query time in case new decisions are posted during this cycle's processing
			$this->set_last_order_update( $gateway->get_id(), time() );

			$orders_meta_table = Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ? $wpdb->prefix . 'wc_orders_meta' : $wpdb->postmeta;

			// find an associated order for each case decision and update it accordingly
			foreach ( $response->get_conversion_details() as $conversion_detail ) {

				// find the order with the given transaction ID
				$order_id = $wpdb->get_var( $wpdb->prepare(
					"SELECT post_id FROM $orders_meta_table WHERE meta_key = '_transaction_id' AND meta_value = %s",
					$conversion_detail->get_request_id()
				) );

				$order = wc_get_order( $order_id );

				if ( ! $order instanceof \WC_Order ) {
					continue;
				}

				$this->handle_order_decision( $order, $conversion_detail );

				// if the order was settled remotely, mark it as captured
				if ( $conversion_detail->is_settled() ) {

					$order->payment_complete();

					$gateway->update_order_meta( $order, 'capture_total',   Framework\SV_WC_Helper::number_format( $order->get_total() ) );
					$gateway->update_order_meta( $order, 'charge_captured', 'yes' );
				}
			}

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			// 404 responses indicate no orders to process, not an API problem
			if ( 404 === (int) $exception->getCode() ) {

				$this->set_last_order_update( $gateway->get_id(), time() );

			} elseif ( $gateway->debug_log() ) {

				wc_cybersource()->log( 'Could not retrieve latest case details. ' . $exception->getMessage(), $gateway->get_id() );
			}
		}
	}


	/**
	 * Handles an order based on the conversion detail's decision.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param Conversion_Detail $conversion_detail single conversion detail that corresponds to $order
	 */
	private function handle_order_decision( \WC_Order $order, Conversion_Detail $conversion_detail ) {

		$conversion_message = sprintf(
			/* translators: Placeholders: %1$s - order fraud decision, such as ACCEPT or REJECT, %2$s - order fraud decision, such as ACCEPT or REJECT */
			__( 'Order changed from %1$s to %2$s in decision manager.', 'woocommerce-gateway-cybersource' ),
			$conversion_detail->get_original_decision(),
			$conversion_detail->get_new_decision()
		);

		switch ( $conversion_detail->get_new_decision() ) {

			case Conversion_Detail::DECISION_ACCEPT:

				// don't transition status, as it may not be captured
				$order->add_order_note( $conversion_message );

			break;

			case Conversion_Detail::DECISION_REVIEW:

				if ( ! $order->has_status( 'on-hold' ) ) {
					$order->update_status( 'on-hold', $conversion_message );
				}

			break;

			case Conversion_Detail::DECISION_REJECT:

				if ( ! $order->has_status( 'failed' ) ) {
					$order->update_status( 'failed', $conversion_message );
				}

			break;
		}
	}


	/**
	 * Gets the last order update timestamp.
	 *
	 * @since 2.3.0
	 *
	 * @param string $gateway_id gateway ID
	 * @return int
	 */
	private function get_last_order_update( $gateway_id ) {

		return get_option( self::OPTION_LAST_ORDER_UPDATE . '_' . $gateway_id, time() - $this->get_order_update_interval() );
	}


	/**
	 * Sets the last order update timestamp.
	 *
	 * @since 2.3.0
	 *
	 * @param string $gateway_id gateway ID
	 * @param int $timestamp timestamp of the last order update
	 */
	private function set_last_order_update( $gateway_id, $timestamp ) {

		update_option( self::OPTION_LAST_ORDER_UPDATE . '_' . $gateway_id, $timestamp );
	}


	/**
	 * Gets the order update interval.
	 *
	 * @since 2.3.0
	 *
	 * @return int
	 */
	private function get_order_update_interval() {

		/**
		 * Filters the frequency with with orders should be updated from CyberSource data.
		 *
		 * @since 2.3.0
		 *
		 * @param int $interval order update interval
		 */
		return max( 5, (int) apply_filters( 'wc_cybersource_order_update_interval', 15 * MINUTE_IN_SECONDS ) );
	}


	/**
	 * Gets the gateways that should have unique order update actions scheduled.
	 *
	 * Unique gateways are those that aren't inheriting another gateway's credentials.
	 *
	 * @since 2.3.0
	 *
	 * @return Gateway[]
	 */
	private function get_unique_gateways() {

		$unique_gateways = [];

		/** @var Gateway $gateway */
		foreach ( wc_cybersource()->get_gateways() as $gateway ) {

			if ( ! $gateway instanceof Gateway ) {
				continue;
			}

			if ( ! $gateway->inherit_settings() && $gateway->is_available() && $gateway->is_decision_manager_enabled() ) {
				$unique_gateways[] = $gateway;
			}
		}

		return $unique_gateways;
	}


}
