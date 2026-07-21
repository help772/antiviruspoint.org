<?php
/**
 * WC_Shipping_Fedex_Get_Connection_Status_Ability class.
 *
 * @package WC_Shipping_Fedex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
	return;
}

/**
 * Get FedEx connection status ability definition.
 */
class WC_Shipping_Fedex_Get_Connection_Status_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability category.
	 */
	const CATEGORY = 'woocommerce';

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-fedex/get-connection-status';

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return self::NAME;
	}

	/**
	 * Get the ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get FedEx connection status', 'woocommerce-shipping-fedex' ),
			'description'         => __( 'Returns credential-safe FedEx API mode, environment, and setup status for configured FedEx shipping methods.', 'woocommerce-shipping-fedex' ),
			'category'            => self::CATEGORY,
			'input_schema'        => WC_Shipping_Fedex_Ability_Data::get_instance_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => WC_Shipping_Fedex_Ability_Data::get_read_only_meta(),
		);
	}

	/**
	 * Checks whether the current user can read FedEx settings.
	 *
	 * @param array $input Ability input.
	 * @return bool
	 */
	public static function can_read( $input = array() ) {
		return WC_Shipping_Fedex_Ability_Data::can_read_settings();
	}

	/**
	 * Returns credential-safe FedEx connection status.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( $input = array() ) {
		$instances = WC_Shipping_Fedex_Ability_Data::get_shipping_method_instances_for_input( $input );

		if ( is_wp_error( $instances ) ) {
			return $instances;
		}

		return array(
			'store'     => WC_Shipping_Fedex_Ability_Data::get_connection_store_environment(),
			'instances' => array_map( array( 'WC_Shipping_Fedex_Ability_Data', 'format_connection_status' ), $instances ),
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
				'store'     => WC_Shipping_Fedex_Ability_Data::get_connection_store_schema(),
				'instances' => array(
					'type'  => 'array',
					'items' => self::get_connection_instance_schema(),
				),
			),
			'required'             => array( 'store', 'instances' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the connection instance schema.
	 *
	 * @return array
	 */
	private static function get_connection_instance_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_id'           => array( 'type' => 'integer' ),
				'zone_id'               => array( 'type' => 'integer' ),
				'zone_name'             => array( 'type' => 'string' ),
				'enabled'               => array( 'type' => 'boolean' ),
				'title'                 => array( 'type' => 'string' ),
				'api_type'              => array(
					'type' => 'string',
					'enum' => array( 'rest', 'soap' ),
				),
				'api_mode'              => array(
					'type' => 'string',
					'enum' => array( 'test', 'production' ),
				),
				'has_credentials'       => array( 'type' => 'boolean' ),
				'origin_configured'     => array( 'type' => 'boolean' ),
				'missing_requirements'  => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'freight_enabled'       => array( 'type' => 'boolean' ),
				'soap_client_available' => array( 'type' => 'boolean' ),
			),
			'required'             => array( 'instance_id', 'zone_id', 'zone_name', 'enabled', 'title', 'api_type', 'api_mode', 'has_credentials', 'origin_configured', 'missing_requirements', 'freight_enabled', 'soap_client_available' ),
			'additionalProperties' => false,
		);
	}
}
