<?php
/**
 * Ability helpers for WooCommerce Canada Post Shipping.
 *
 * @package woocommerce-shipping-canada-post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared helper methods for Canada Post ability execution.
 */
class WC_Shipping_Canada_Post_Ability_Helper {

	/**
	 * Check whether the current user can read Canada Post shipping configuration.
	 *
	 * @return bool
	 */
	public static function can_read() {
		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers the manage_woocommerce capability.
		return current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Get non-secret environment status for Canada Post shipping.
	 *
	 * @return array
	 */
	public static function get_environment() {
		return array(
			'simplexml_loaded'       => function_exists( 'simplexml_load_string' ),
			'currency_is_cad'        => function_exists( 'get_woocommerce_currency' ) && 'CAD' === get_woocommerce_currency(),
			'base_country_is_canada' => function_exists( 'WC' ) && WC() && WC()->countries && 'CA' === WC()->countries->get_base_country(),
		);
	}

	/**
	 * Get Canada Post shipping method contexts, optionally filtered by instance ID.
	 *
	 * @param int $instance_id Shipping method instance ID. Zero means all instances.
	 * @return array
	 */
	public static function get_method_contexts( $instance_id = 0 ) {
		if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
			return array();
		}

		$instance_id = absint( $instance_id );
		$contexts    = array();
		$zone_ids    = array( 0 );

		foreach ( WC_Shipping_Zones::get_zones() as $zone_data ) {
			if ( isset( $zone_data['zone_id'] ) ) {
				$zone_ids[] = absint( $zone_data['zone_id'] );
			}
		}

		foreach ( array_unique( $zone_ids ) as $zone_id ) {
			$zone = WC_Shipping_Zones::get_zone( $zone_id );

			if ( ! $zone ) {
				continue;
			}

			foreach ( $zone->get_shipping_methods( false ) as $method ) {
				if ( ! $method instanceof WC_Shipping_Canada_Post ) {
					continue;
				}

				$method_instance_id = isset( $method->instance_id ) ? absint( $method->instance_id ) : 0;

				if ( $instance_id && $method_instance_id !== $instance_id ) {
					continue;
				}

				$contexts[] = array(
					'zone_id'   => absint( $zone_id ),
					'zone_name' => (string) $zone->get_zone_name(),
					'method'    => $method,
				);
			}
		}

		return $contexts;
	}

	/**
	 * Format common shipping method context data.
	 *
	 * @param array $context Method context.
	 * @return array
	 */
	public static function get_context_summary( array $context ) {
		$method = isset( $context['method'] ) ? $context['method'] : null;

		return array(
			'zone_id'     => isset( $context['zone_id'] ) ? absint( $context['zone_id'] ) : 0,
			'zone_name'   => isset( $context['zone_name'] ) ? (string) $context['zone_name'] : '',
			'instance_id' => $method instanceof WC_Shipping_Canada_Post && isset( $method->instance_id ) ? absint( $method->instance_id ) : 0,
		);
	}

	/**
	 * Get an optional instance ID from ability input.
	 *
	 * @param array $input Ability input.
	 * @return int
	 */
	public static function get_input_instance_id( array $input ) {
		return isset( $input['instance_id'] ) ? absint( $input['instance_id'] ) : 0;
	}
}
