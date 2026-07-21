<?php
/**
 * Get Australia Post service catalog ability.
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Ability definition for reading Australia Post service and package catalogs.
 */
class WC_Shipping_Australia_Post_Get_Service_Catalog_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {
	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-shipping-australia-post/get-service-catalog';
	}

	/**
	 * Get ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get Australia Post service catalog', 'woocommerce-shipping-australia-post' ),
			'description'         => __( 'Retrieve deterministic Australia Post service, optional extra, box, and letter package catalogs used by the shipping method.', 'woocommerce-shipping-australia-post' ),
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
				__( 'You do not have permission to read Australia Post service catalog data.', 'woocommerce-shipping-australia-post' ),
				array( 'status' => 403 )
			);
		}

		return WC_Shipping_Australia_Post_Abilities_Helper::get_service_catalog( is_array( $input ) ? $input : array() );
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
				'scope'                   => array(
					'type'        => 'string',
					'enum'        => array( 'all', 'domestic', 'international' ),
					'default'     => 'all',
					'description' => __( 'Limit returned services by destination scope.', 'woocommerce-shipping-australia-post' ),
				),
				'include_package_catalog' => array(
					'type'        => 'boolean',
					'default'     => true,
					'description' => __( 'Whether to include default box and letter package catalogs.', 'woocommerce-shipping-australia-post' ),
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
				'scope'           => array(
					'type' => 'string',
					'enum' => array( 'all', 'domestic', 'international' ),
				),
				'services'        => array(
					'type'  => 'array',
					'items' => self::get_service_schema(),
				),
				'optional_extras' => array(
					'type'                 => 'object',
					'properties'           => array(
						'extra_cover_service_limits'     => array(
							'type'                 => 'object',
							'additionalProperties' => array( 'type' => 'number' ),
						),
						'signature_on_delivery_services' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
					),
					'additionalProperties' => false,
				),
				'default_boxes'   => array(
					'type'  => 'array',
					'items' => self::get_box_schema(),
				),
				'letter_sizes'    => array(
					'type'  => 'array',
					'items' => self::get_letter_size_schema(),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for a catalog service.
	 *
	 * @return array
	 */
	private static function get_service_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'                           => array( 'type' => 'string' ),
				'name'                           => array( 'type' => 'string' ),
				'scope'                          => array(
					'type' => 'string',
					'enum' => array( 'domestic', 'international' ),
				),
				'image_path'                     => array( 'type' => 'string' ),
				'alternate_service_codes'        => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'supports_extra_cover'           => array( 'type' => 'boolean' ),
				'max_extra_cover'                => array( 'type' => array( 'number', 'null' ) ),
				'supports_signature_on_delivery' => array( 'type' => 'boolean' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for a package box.
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

	/**
	 * Get schema for a letter-size package catalog entry.
	 *
	 * @return array
	 */
	private static function get_letter_size_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'         => array( 'type' => 'string' ),
				'name'         => array( 'type' => 'string' ),
				'width_mm'     => array( 'type' => array( 'number', 'null' ) ),
				'length_mm'    => array( 'type' => array( 'number', 'null' ) ),
				'thickness_mm' => array( 'type' => array( 'number', 'null' ) ),
			),
			'additionalProperties' => false,
		);
	}
}
