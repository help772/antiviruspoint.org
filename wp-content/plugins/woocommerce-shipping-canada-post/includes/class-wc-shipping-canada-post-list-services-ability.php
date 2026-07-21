<?php
/**
 * Canada Post services ability definition.
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
 * Canada Post services ability definition.
 */
class WC_Shipping_Canada_Post_List_Services_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-canada-post/list-services';

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
			'label'               => __( 'List Canada Post services', 'woocommerce-shipping-canada-post' ),
			'description'         => __( 'Returns Canada Post service codes and per-instance enablement and rate adjustment settings.', 'woocommerce-shipping-canada-post' ),
			'category'            => self::CATEGORY,
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => self::get_meta(),
		);
	}

	/**
	 * Check whether the current user can read Canada Post service settings.
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
					'services' => $projection->get_service_configuration(),
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
					'description' => __( 'Return services only for this Canada Post shipping method instance ID.', 'woocommerce-shipping-canada-post' ),
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
							'zone_id'     => array( 'type' => 'integer' ),
							'zone_name'   => array( 'type' => 'string' ),
							'instance_id' => array( 'type' => 'integer' ),
							'services'    => array(
								'type'  => 'array',
								'items' => self::get_service_schema(),
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
	 * Get the service output schema.
	 *
	 * @return array
	 */
	private static function get_service_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'                         => array( 'type' => 'string' ),
				'name'                         => array( 'type' => 'string' ),
				'region'                       => array(
					'type' => 'string',
					'enum' => array( 'domestic', 'united_states', 'international', 'unknown' ),
				),
				'configured_name'              => array( 'type' => 'string' ),
				'effective_name'               => array( 'type' => 'string' ),
				'enabled'                      => array( 'type' => 'boolean' ),
				'sort_order'                   => array( 'type' => 'integer' ),
				'price_adjustment'             => array( 'type' => 'string' ),
				'price_adjustment_percent'     => array( 'type' => 'string' ),
				'has_price_adjustment'         => array( 'type' => 'boolean' ),
				'has_price_adjustment_percent' => array( 'type' => 'boolean' ),
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
