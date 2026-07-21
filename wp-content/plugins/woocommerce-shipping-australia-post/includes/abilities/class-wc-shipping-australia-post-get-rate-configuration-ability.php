<?php
/**
 * Get Australia Post rate configuration ability.
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Ability definition for reading Australia Post rate configuration.
 */
class WC_Shipping_Australia_Post_Get_Rate_Configuration_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {
	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-shipping-australia-post/get-rate-configuration';
	}

	/**
	 * Get ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get Australia Post rate configuration', 'woocommerce-shipping-australia-post' ),
			'description'         => __( 'Retrieve non-secret Australia Post shipping method configuration, configured services, package settings, and store environment status.', 'woocommerce-shipping-australia-post' ),
			'category'            => 'woocommerce',
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( 'WC_Shipping_Australia_Post_Abilities_Helper', 'can_manage_woocommerce' ),
			'meta'                => array(
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
			),
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array|WP_Error
	 */
	public static function execute( $input = array() ) {
		if ( ! WC_Shipping_Australia_Post_Abilities_Helper::can_manage_woocommerce() ) {
			return new WP_Error(
				'woocommerce_shipping_australia_post_cannot_read',
				__( 'You do not have permission to read Australia Post shipping configuration.', 'woocommerce-shipping-australia-post' ),
				array( 'status' => 403 )
			);
		}

		return WC_Shipping_Australia_Post_Abilities_Helper::get_rate_configuration( is_array( $input ) ? $input : array() );
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_id'      => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Limit the response to a specific Australia Post shipping method instance ID.', 'woocommerce-shipping-australia-post' ),
				),
				'include_services' => array(
					'type'        => 'boolean',
					'default'     => true,
					'description' => __( 'Whether to include configured service names, enablement, and rate adjustments.', 'woocommerce-shipping-australia-post' ),
				),
				'include_boxes'    => array(
					'type'        => 'boolean',
					'default'     => true,
					'description' => __( 'Whether to include configured package and box settings.', 'woocommerce-shipping-australia-post' ),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array
	 */
	private static function get_output_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'method_id'       => array(
					'type' => 'string',
				),
				'method_title'    => array(
					'type' => 'string',
				),
				'global_settings' => array(
					'type'                 => 'object',
					'properties'           => array(
						'has_custom_api_key' => array( 'type' => 'boolean' ),
						'debug_mode'         => array( 'type' => 'boolean' ),
						'box_packer_library' => array( 'type' => 'string' ),
					),
					'additionalProperties' => false,
				),
				'environment'     => array(
					'type'                 => 'object',
					'properties'           => array(
						'shipping_enabled'                 => array( 'type' => 'boolean' ),
						'store_currency'                   => array( 'type' => 'string' ),
						'store_base_country'               => array( 'type' => 'string' ),
						'requires_store_currency'          => array( 'type' => 'string' ),
						'requires_store_base_country'      => array( 'type' => 'string' ),
						'meets_currency_requirement'       => array( 'type' => 'boolean' ),
						'meets_base_country_requirement'   => array( 'type' => 'boolean' ),
						'supported_domestic_country_codes' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'supports_international_destinations' => array( 'type' => 'boolean' ),
					),
					'additionalProperties' => false,
				),
				'instances'       => array(
					'type'  => 'array',
					'items' => self::get_instance_schema(),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for an Australia Post shipping method instance.
	 *
	 * @return array
	 */
	private static function get_instance_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_id'     => array( 'type' => 'integer' ),
				'zone_id'         => array( 'type' => 'integer' ),
				'zone_name'       => array( 'type' => 'string' ),
				'method_order'    => array( 'type' => 'integer' ),
				'enabled'         => array( 'type' => 'boolean' ),
				'title'           => array( 'type' => 'string' ),
				'origin_postcode' => array( 'type' => 'string' ),
				'excluding_tax'   => array( 'type' => 'boolean' ),
				'tax_status'      => array(
					'type' => 'string',
					'enum' => array( 'taxable', 'none' ),
				),
				'packing_method'  => array( 'type' => 'string' ),
				'max_weight_kg'   => array( 'type' => 'number' ),
				'satchel_rates'   => array( 'type' => 'string' ),
				'offer_rates'     => array(
					'type' => 'string',
					'enum' => array( 'all', 'cheapest' ),
				),
				'services'        => array(
					'type'  => 'array',
					'items' => self::get_configured_service_schema(),
				),
				'boxes'           => array(
					'type'  => 'array',
					'items' => self::get_box_schema(),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for a configured Australia Post service.
	 *
	 * @return array
	 */
	private static function get_configured_service_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'                           => array( 'type' => 'string' ),
				'default_name'                   => array( 'type' => 'string' ),
				'name'                           => array( 'type' => 'string' ),
				'has_custom_name'                => array( 'type' => 'boolean' ),
				'enabled'                        => array( 'type' => 'boolean' ),
				'order'                          => array( 'type' => array( 'integer', 'null' ) ),
				'adjustment'                     => array( 'type' => array( 'number', 'null' ) ),
				'adjustment_percent'             => array( 'type' => array( 'number', 'null' ) ),
				'extra_cover_enabled'            => array( 'type' => 'boolean' ),
				'delivery_confirmation_enabled'  => array( 'type' => 'boolean' ),
				'supports_extra_cover'           => array( 'type' => 'boolean' ),
				'supports_delivery_confirmation' => array( 'type' => 'boolean' ),
				'alternate_service_codes'        => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for an Australia Post package box.
	 *
	 * @return array
	 */
	private static function get_box_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'              => array( 'type' => 'string' ),
				'name'            => array( 'type' => 'string' ),
				'source'          => array(
					'type' => 'string',
					'enum' => array( 'default', 'custom' ),
				),
				'type'            => array( 'type' => 'string' ),
				'enabled'         => array( 'type' => 'boolean' ),
				'max_weight_kg'   => array( 'type' => array( 'number', 'null' ) ),
				'box_weight_kg'   => array( 'type' => array( 'number', 'null' ) ),
				'outer_length_cm' => array( 'type' => array( 'number', 'null' ) ),
				'outer_width_cm'  => array( 'type' => array( 'number', 'null' ) ),
				'outer_height_cm' => array( 'type' => array( 'number', 'null' ) ),
				'inner_length_cm' => array( 'type' => array( 'number', 'null' ) ),
				'inner_width_cm'  => array( 'type' => array( 'number', 'null' ) ),
				'inner_height_cm' => array( 'type' => array( 'number', 'null' ) ),
			),
			'additionalProperties' => false,
		);
	}
}
