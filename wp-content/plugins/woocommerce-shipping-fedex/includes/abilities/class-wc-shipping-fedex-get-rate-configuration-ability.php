<?php
/**
 * WC_Shipping_Fedex_Get_Rate_Configuration_Ability class.
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
 * Get FedEx rate configuration ability definition.
 */
class WC_Shipping_Fedex_Get_Rate_Configuration_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability category.
	 */
	const CATEGORY = 'woocommerce';

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-fedex/get-rate-configuration';

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
			'label'               => __( 'Get FedEx rate configuration', 'woocommerce-shipping-fedex' ),
			'description'         => __( 'Returns FedEx shipping method rate, service, and package configuration without exposing credentials or account identifiers.', 'woocommerce-shipping-fedex' ),
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
	 * Returns credential-safe FedEx rate configuration.
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
			'store'     => WC_Shipping_Fedex_Ability_Data::get_rate_store_environment(),
			'instances' => array_map( array( 'WC_Shipping_Fedex_Ability_Data', 'format_rate_configuration' ), $instances ),
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
				'store'     => WC_Shipping_Fedex_Ability_Data::get_rate_store_schema(),
				'instances' => array(
					'type'  => 'array',
					'items' => self::get_configuration_instance_schema(),
				),
			),
			'required'             => array( 'store', 'instances' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the configuration instance schema.
	 *
	 * @return array
	 */
	private static function get_configuration_instance_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_id'              => array( 'type' => 'integer' ),
				'zone_id'                  => array( 'type' => 'integer' ),
				'zone_name'                => array( 'type' => 'string' ),
				'enabled'                  => array( 'type' => 'boolean' ),
				'title'                    => array( 'type' => 'string' ),
				'api_type'                 => array(
					'type' => 'string',
					'enum' => array( 'rest', 'soap' ),
				),
				'api_mode'                 => array(
					'type' => 'string',
					'enum' => array( 'test', 'production' ),
				),
				'packing_method'           => array( 'type' => 'string' ),
				'box_packer_library'       => array( 'type' => 'string' ),
				'offer_rates'              => array( 'type' => 'string' ),
				'request_type'             => array( 'type' => 'string' ),
				'residential_default'      => array( 'type' => 'boolean' ),
				'insurance_enabled'        => array( 'type' => 'boolean' ),
				'fedex_one_rate_enabled'   => array( 'type' => 'boolean' ),
				'freight_enabled'          => array( 'type' => 'boolean' ),
				'smartpost_hub_configured' => array( 'type' => 'boolean' ),
				'services'                 => array(
					'type'  => 'array',
					'items' => self::get_service_schema(),
				),
				'packages'                 => self::get_package_summary_schema(),
			),
			'required'             => array( 'instance_id', 'zone_id', 'zone_name', 'enabled', 'title', 'api_type', 'api_mode', 'packing_method', 'box_packer_library', 'offer_rates', 'request_type', 'residential_default', 'insurance_enabled', 'fedex_one_rate_enabled', 'freight_enabled', 'smartpost_hub_configured', 'services', 'packages' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the service schema.
	 *
	 * @return array
	 */
	private static function get_service_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'               => array( 'type' => 'string' ),
				'default_name'       => array( 'type' => 'string' ),
				'display_name'       => array( 'type' => 'string' ),
				'enabled'            => array( 'type' => 'boolean' ),
				'adjustment'         => array( 'type' => 'string' ),
				'adjustment_percent' => array( 'type' => 'string' ),
				'groups'             => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
			),
			'required'             => array( 'code', 'default_name', 'display_name', 'enabled', 'adjustment', 'adjustment_percent', 'groups' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the package summary schema.
	 *
	 * @return array
	 */
	private static function get_package_summary_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'default_boxes_count'      => array( 'type' => 'integer' ),
				'enabled_default_box_ids'  => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'custom_boxes_count'       => array( 'type' => 'integer' ),
				'enabled_custom_box_count' => array( 'type' => 'integer' ),
			),
			'required'             => array( 'default_boxes_count', 'enabled_default_box_ids', 'custom_boxes_count', 'enabled_custom_box_count' ),
			'additionalProperties' => false,
		);
	}
}
