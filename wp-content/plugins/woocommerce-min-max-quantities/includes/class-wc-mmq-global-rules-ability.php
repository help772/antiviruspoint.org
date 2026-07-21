<?php
/**
 * WC_MMQ_Global_Rules_Ability class
 *
 * @package WooCommerce Min/Max Quantities
 * @since   5.2.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MMQ_Global_Rules_Ability ability definition.
 *
 * @version 5.2.9
 */
class WC_MMQ_Global_Rules_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-min-max-quantities/get-global-rules';
	}

	/**
	 * Get the ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get global Min/Max Quantities rules', 'woocommerce-min-max-quantities' ),
			'description'         => __( 'Returns the store-level order quantity and order value rules configured by Min/Max Quantities.', 'woocommerce-min-max-quantities' ),
			'category'            => 'woocommerce',
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => self::get_meta(),
		);
	}

	/**
	 * Checks whether the current user can read global rules.
	 *
	 * @param array $input Ability input.
	 * @return bool
	 */
	public static function can_read( $input = array() ) {
		return current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Returns global Min/Max Quantities rules.
	 *
	 * @param array $input Ability input.
	 * @return array
	 */
	public static function execute( $input = array() ) {
		return array(
			'minimum_order_quantity' => absint( get_option( 'woocommerce_minimum_order_quantity' ) ),
			'maximum_order_quantity' => absint( get_option( 'woocommerce_maximum_order_quantity' ) ),
			'minimum_order_value'    => self::get_decimal_option( 'woocommerce_minimum_order_value' ),
			'maximum_order_value'    => self::get_decimal_option( 'woocommerce_maximum_order_value' ),
		);
	}

	/**
	 * Returns the input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(),
			'additionalProperties' => false,
			'default'              => array(),
		);
	}

	/**
	 * Returns the output schema.
	 *
	 * @return array
	 */
	private static function get_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'minimum_order_quantity' => array( 'type' => 'integer' ),
				'maximum_order_quantity' => array( 'type' => 'integer' ),
				'minimum_order_value'    => array( 'type' => 'number' ),
				'maximum_order_value'    => array( 'type' => 'number' ),
			),
			'required'             => array( 'minimum_order_quantity', 'maximum_order_quantity', 'minimum_order_value', 'maximum_order_value' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns metadata for a read-only, externally visible ability.
	 *
	 * @return array
	 */
	private static function get_meta() {
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
	 * Reads a decimal option as a positive float.
	 *
	 * @param string $option_name Option name.
	 * @return float
	 */
	private static function get_decimal_option( $option_name ) {
		$value = get_option( $option_name, '' );

		if ( '' === $value ) {
			return 0.0;
		}

		return abs( (float) wc_format_decimal( $value ) );
	}
}
