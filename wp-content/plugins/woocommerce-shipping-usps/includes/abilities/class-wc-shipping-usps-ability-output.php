<?php
/**
 * Shared output helpers for WooCommerce USPS abilities.
 *
 * @package WC_Shipping_USPS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formats USPS ability output without exposing carrier credentials.
 */
class WC_Shipping_USPS_Ability_Output {

	/**
	 * Ability category.
	 */
	const CATEGORY = 'woocommerce';

	/**
	 * Permission check for USPS configuration reads.
	 *
	 * @return bool
	 */
	public static function can_read() {
		// phpcs:ignore WordPress.WP.Capabilities.Unknown --- WooCommerce capability.
		return current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Read-only metadata for externally visible USPS abilities.
	 *
	 * @return array
	 */
	public static function get_read_only_meta() {
		return array(
			'show_in_rest' => true,
			'mcp'          => array(
				'public' => true,
				'type'   => 'tool',
			),
			'annotations'  => array(
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			),
		);
	}

	/**
	 * Input schema shared by USPS settings read abilities.
	 *
	 * @return array
	 */
	public static function get_instance_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_id' => array(
					'type'        => 'integer',
					'description' => __( 'Optional USPS shipping method instance ID. When omitted or zero, global USPS settings are returned.', 'woocommerce-shipping-usps' ),
					'default'     => 0,
					'minimum'     => 0,
				),
			),
			'additionalProperties' => false,
			'default'              => array(),
		);
	}

	/**
	 * Resolve the USPS shipping method for ability input.
	 *
	 * @param array $input Ability input.
	 * @return WC_Shipping_USPS|WP_Error
	 */
	public static function get_shipping_method( array $input = array() ) {
		if ( ! class_exists( 'WC_Shipping_USPS' ) ) {
			require_once WC_USPS_ABSPATH . 'includes/class-wc-shipping-usps.php';
		}

		$instance_id = self::get_instance_id( $input );

		if ( $instance_id > 0 ) {
			$shipping_method = class_exists( 'WC_Shipping_Zones' ) ? WC_Shipping_Zones::get_shipping_method( $instance_id ) : false;

			if ( ! $shipping_method instanceof WC_Shipping_USPS ) {
				return new WP_Error(
					'woocommerce_shipping_usps_ability_invalid_instance',
					__( 'USPS shipping method instance not found.', 'woocommerce-shipping-usps' ),
					array( 'status' => 404 )
				);
			}

			return $shipping_method;
		}

		return new WC_Shipping_USPS();
	}

	/**
	 * Get the requested USPS shipping method instance ID.
	 *
	 * @param array $input Ability input.
	 * @return int
	 */
	public static function get_instance_id( array $input = array() ): int {
		return isset( $input['instance_id'] ) ? absint( $input['instance_id'] ) : 0;
	}

	/**
	 * Format a USPS service name for plain-text clients.
	 *
	 * @param string $value Service name.
	 * @return string
	 */
	public static function clean_label( string $value ): string {
		return wp_strip_all_tags( html_entity_decode( $value, ENT_QUOTES, get_bloginfo( 'charset' ) ) );
	}

	/**
	 * Cast a value to a string for stable schema output.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function string_value( $value ): string {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}

	/**
	 * Format a merchant-entered service setting.
	 *
	 * @param array $settings Saved service settings.
	 * @return array
	 */
	public static function format_service_settings( array $settings = array() ): array {
		return array(
			'enabled'            => ! array_key_exists( 'enabled', $settings ) || self::bool_value( $settings['enabled'] ),
			'adjustment'         => self::string_value( $settings['adjustment'] ?? '' ),
			'adjustment_percent' => isset( $settings['adjustment_percent'] ) ? (float) $settings['adjustment_percent'] : 0.0,
		);
	}

	/**
	 * Cast WooCommerce setting values to booleans.
	 *
	 * @param mixed $value Raw setting value.
	 * @return bool
	 */
	public static function bool_value( $value ): bool {
		if ( function_exists( 'wc_string_to_bool' ) ) {
			return wc_string_to_bool( $value );
		}

		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Get whether a scalar setting has a non-empty value.
	 *
	 * @param mixed $value Setting value.
	 * @return bool
	 */
	public static function has_value( $value ): bool {
		return '' !== trim( self::string_value( $value ) );
	}
}
