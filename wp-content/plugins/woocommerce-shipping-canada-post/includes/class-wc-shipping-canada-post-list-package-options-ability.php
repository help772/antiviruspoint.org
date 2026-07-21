<?php
/**
 * Canada Post package options ability definition.
 *
 * @package woocommerce-shipping-canada-post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
	return;
}

/**
 * Canada Post package options ability definition.
 */
class WC_Shipping_Canada_Post_List_Package_Options_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-canada-post/list-package-options';

	/**
	 * Ability category.
	 */
	const CATEGORY = 'woocommerce';

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
			'label'               => __( 'List Canada Post package options', 'woocommerce-shipping-canada-post' ),
			'description'         => __( 'Returns configured package boxes, flat-rate boxes, and lettermail catalogs for Canada Post shipping.', 'woocommerce-shipping-canada-post' ),
			'category'            => self::CATEGORY,
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => self::get_meta(),
		);
	}

	/**
	 * Check whether the current user can read Canada Post package settings.
	 *
	 * @param array $input Ability input.
	 * @return bool
	 */
	public static function can_read( $input = array() ) {
		return WC_Shipping_Canada_Post_Ability_Helper::can_read();
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $input Ability input.
	 * @return array
	 */
	public static function execute( $input = array() ) {
		$input     = is_array( $input ) ? $input : array();
		$contexts  = WC_Shipping_Canada_Post_Ability_Helper::get_method_contexts( WC_Shipping_Canada_Post_Ability_Helper::get_input_instance_id( $input ) );
		$instances = array();

		foreach ( $contexts as $context ) {
			$projection = new WC_Shipping_Canada_Post_Ability_Projection( $context['method'] );

			$instances[] = array_merge(
				WC_Shipping_Canada_Post_Ability_Helper::get_context_summary( $context ),
				array(
					'package_options' => $projection->get_package_options(),
				)
			);
		}

		return array(
			'instance_count' => count( $instances ),
			'instances'      => $instances,
		);
	}

	/**
	 * Get the input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Return package options only for this Canada Post shipping method instance ID.', 'woocommerce-shipping-canada-post' ),
				),
			),
			'additionalProperties' => false,
			'default'              => array(),
		);
	}

	/**
	 * Get the output schema.
	 *
	 * @return array
	 */
	private static function get_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_count' => array( 'type' => 'integer' ),
				'instances'      => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'zone_id'         => array( 'type' => 'integer' ),
							'zone_name'       => array( 'type' => 'string' ),
							'instance_id'     => array( 'type' => 'integer' ),
							'package_options' => self::get_package_options_schema(),
						),
						'additionalProperties' => false,
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get the package options schema.
	 *
	 * @return array
	 */
	private static function get_package_options_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'packing_method'                   => array(
					'type' => 'string',
					'enum' => array( 'per_item', 'weight', 'box_packing' ),
				),
				'custom_boxes'                     => array(
					'type'  => 'array',
					'items' => self::get_custom_box_schema(),
				),
				'flat_rate_boxes'                  => array(
					'type'  => 'array',
					'items' => self::get_flat_rate_box_schema(),
				),
				'lettermail_catalogs'              => array(
					'type'  => 'array',
					'items' => self::get_lettermail_catalog_schema(),
				),
				'registered_lettermail_surcharges' => array(
					'type'                 => 'object',
					'properties'           => array(
						'domestic'      => array( 'type' => 'string' ),
						'international' => array( 'type' => 'string' ),
					),
					'additionalProperties' => false,
				),
				'additional_liability_coverage'    => array(
					'type'                 => 'object',
					'properties'           => array(
						'domestic_cost_per_100_cad' => array( 'type' => 'string' ),
					),
					'additionalProperties' => false,
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get the dimensions schema.
	 *
	 * @return array
	 */
	private static function get_dimensions_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'length' => array( 'type' => 'number' ),
				'width'  => array( 'type' => 'number' ),
				'height' => array( 'type' => 'number' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get the custom box schema.
	 *
	 * @return array
	 */
	private static function get_custom_box_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'name'                => array( 'type' => 'string' ),
				'enabled'             => array( 'type' => 'boolean' ),
				'outer_dimensions_cm' => self::get_dimensions_schema(),
				'inner_dimensions_cm' => self::get_dimensions_schema(),
				'box_weight_kg'       => array( 'type' => 'number' ),
				'max_weight_kg'       => array( 'type' => 'number' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get the flat-rate box schema.
	 *
	 * @return array
	 */
	private static function get_flat_rate_box_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'          => array( 'type' => 'string' ),
				'name'          => array( 'type' => 'string' ),
				'dimensions_cm' => self::get_dimensions_schema(),
				'max_weight_kg' => array( 'type' => 'number' ),
				'cost'          => array( 'type' => 'string' ),
				'enabled'       => array( 'type' => 'boolean' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get the lettermail catalog schema.
	 *
	 * @return array
	 */
	private static function get_lettermail_catalog_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'scope' => array(
					'type' => 'string',
					'enum' => array( 'domestic', 'united_states', 'international' ),
				),
				'boxes' => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'code'          => array( 'type' => 'string' ),
							'name'          => array( 'type' => 'string' ),
							'dimensions_cm' => self::get_dimensions_schema(),
							'max_weight_kg' => array( 'type' => 'number' ),
							'rate_tiers'    => array(
								'type'  => 'array',
								'items' => array(
									'type'                 => 'object',
									'properties'           => array(
										'max_weight_kg' => array( 'type' => 'number' ),
										'cost'          => array( 'type' => 'string' ),
									),
									'additionalProperties' => false,
								),
							),
						),
						'additionalProperties' => false,
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get metadata for a read-only, externally visible ability.
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
}
