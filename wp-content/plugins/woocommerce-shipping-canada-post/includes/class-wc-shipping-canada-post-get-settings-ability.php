<?php
/**
 * Canada Post settings ability definition.
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
 * Canada Post safe settings ability definition.
 */
class WC_Shipping_Canada_Post_Get_Settings_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-canada-post/get-settings';

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
			'label'               => __( 'Get Canada Post shipping settings', 'woocommerce-shipping-canada-post' ),
			'description'         => __( 'Returns non-secret Canada Post shipping method configuration and environment readiness.', 'woocommerce-shipping-canada-post' ),
			'category'            => self::CATEGORY,
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => self::get_meta(),
		);
	}

	/**
	 * Check whether the current user can read Canada Post settings.
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
		$input       = is_array( $input ) ? $input : array();
		$contexts    = WC_Shipping_Canada_Post_Ability_Helper::get_method_contexts( WC_Shipping_Canada_Post_Ability_Helper::get_input_instance_id( $input ) );
		$instances   = array();
		$environment = WC_Shipping_Canada_Post_Ability_Helper::get_environment();

		foreach ( $contexts as $context ) {
			$projection = new WC_Shipping_Canada_Post_Ability_Projection( $context['method'] );

			$instances[] = array_merge(
				WC_Shipping_Canada_Post_Ability_Helper::get_context_summary( $context ),
				$projection->get_safe_settings()
			);
		}

		return array(
			'environment'    => $environment,
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
					'description' => __( 'Return settings only for this Canada Post shipping method instance ID.', 'woocommerce-shipping-canada-post' ),
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
				'environment'    => array(
					'type'                 => 'object',
					'properties'           => array(
						'simplexml_loaded'       => array( 'type' => 'boolean' ),
						'currency_is_cad'        => array( 'type' => 'boolean' ),
						'base_country_is_canada' => array( 'type' => 'boolean' ),
					),
					'additionalProperties' => false,
				),
				'instance_count' => array( 'type' => 'integer' ),
				'instances'      => array(
					'type'  => 'array',
					'items' => self::get_instance_schema(),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get the settings instance output schema.
	 *
	 * @return array
	 */
	private static function get_instance_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'zone_id'                      => array( 'type' => 'integer' ),
				'zone_name'                    => array( 'type' => 'string' ),
				'instance_id'                  => array( 'type' => 'integer' ),
				'method_id'                    => array( 'type' => 'string' ),
				'title'                        => array( 'type' => 'string' ),
				'enabled'                      => array( 'type' => 'boolean' ),
				'has_origin'                   => array( 'type' => 'boolean' ),
				'api_mode'                     => array(
					'type' => 'string',
					'enum' => array( 'development', 'production' ),
				),
				'quote_type'                   => array(
					'type' => 'string',
					'enum' => array( 'commercial', 'counter' ),
				),
				'cost_basis'                   => array(
					'type' => 'string',
					'enum' => array( 'due', 'base' ),
				),
				'packing_method'               => array(
					'type' => 'string',
					'enum' => array( 'per_item', 'weight', 'box_packing' ),
				),
				'max_weight_kg'                => array( 'type' => 'number' ),
				'offer_rates'                  => array(
					'type' => 'string',
					'enum' => array( 'all', 'cheapest' ),
				),
				'box_packer_library'           => array(
					'type' => 'string',
					'enum' => array( 'original', 'dvdoug' ),
				),
				'flat_rates_enabled'           => array( 'type' => 'boolean' ),
				'lettermail_services'          => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'additional_options'           => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'show_delivery_time'           => array( 'type' => 'boolean' ),
				'delivery_time_delay_days'     => array( 'type' => 'integer' ),
				'debug_enabled'                => array( 'type' => 'boolean' ),
				'has_api_credentials'          => array( 'type' => 'boolean' ),
				'has_commercial_account'       => array( 'type' => 'boolean' ),
				'has_contract'                 => array( 'type' => 'boolean' ),
				'has_complete_commercial_auth' => array( 'type' => 'boolean' ),
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
