<?php
/**
 * Royal Mail settings ability.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
	return;
}

/**
 * Retrieves sanitized Royal Mail shipping method configuration.
 */
class WC_RoyalMail_Get_Settings_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {
	/**
	 * Get ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return WC_RoyalMail_Ability_Helper::ability_name( 'get-settings' );
	}

	/**
	 * Get ability registration args.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get Royal Mail settings', 'woocommerce-shipping-royalmail' ),
			'description'         => __( 'Retrieve sanitized, non-secret Royal Mail shipping method settings and configured shipping zone instances.', 'woocommerce-shipping-royalmail' ),
			'category'            => 'woocommerce',
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( WC_RoyalMail_Ability_Helper::class, 'can_manage_woocommerce' ),
			'meta'                => WC_RoyalMail_Ability_Helper::get_read_only_meta(),
		);
	}

	/**
	 * Execute ability.
	 *
	 * @param array $input Ability input.
	 * @return array
	 */
	public static function execute( array $input = array() ): array {
		$instance_id = isset( $input['instance_id'] ) ? absint( $input['instance_id'] ) : 0;
		$instances   = WC_RoyalMail_Ability_Helper::get_shipping_method_instances( $instance_id );

		return array(
			'method_id'       => WC_RoyalMail_Ability_Helper::METHOD_ID,
			'has_credentials' => false,
			'global_settings' => WC_RoyalMail_Ability_Helper::format_settings( WC_RoyalMail_Ability_Helper::get_effective_settings() ),
			'instances'       => $instances,
		);
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
				'instance_id' => array(
					'type'    => 'integer',
					'minimum' => 1,
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
				'method_id'       => array( 'type' => 'string' ),
				'has_credentials' => array( 'type' => 'boolean' ),
				'global_settings' => self::get_settings_schema(),
				'instances'       => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'instance_id' => array( 'type' => 'integer' ),
							'zone_id'     => array( 'type' => 'integer' ),
							'zone_name'   => array( 'type' => 'string' ),
							'enabled'     => array( 'type' => 'boolean' ),
							'settings'    => self::get_settings_schema(),
						),
						'required'             => array( 'instance_id', 'zone_id', 'zone_name', 'enabled', 'settings' ),
						'additionalProperties' => false,
					),
				),
			),
			'required'             => array( 'method_id', 'has_credentials', 'global_settings', 'instances' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get settings schema.
	 *
	 * @return array
	 */
	private static function get_settings_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'title'                          => array( 'type' => 'string' ),
				'tax_status'                     => array( 'type' => 'string' ),
				'packing_method'                 => array( 'type' => 'string' ),
				'rate_type'                      => array( 'type' => 'string' ),
				'offer_rates'                    => array( 'type' => 'string' ),
				'box_packer_library'             => array( 'type' => 'string' ),
				'compensation_optional'          => array( 'type' => 'boolean' ),
				'ignore_max_total_cover'         => array( 'type' => 'boolean' ),
				'enable_additional_compensation' => array( 'type' => 'boolean' ),
				'debug_enabled'                  => array( 'type' => 'boolean' ),
				'has_credentials'                => array( 'type' => 'boolean' ),
				'boxes'                          => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'name'         => array( 'type' => 'string' ),
							'inner_length' => array( 'type' => 'number' ),
							'inner_width'  => array( 'type' => 'number' ),
							'inner_height' => array( 'type' => 'number' ),
							'box_weight'   => array( 'type' => 'number' ),
						),
						'required'             => array( 'name', 'inner_length', 'inner_width', 'inner_height', 'box_weight' ),
						'additionalProperties' => false,
					),
				),
				'services'                       => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'id'                 => array( 'type' => 'string' ),
							'name'               => array( 'type' => 'string' ),
							'enabled'            => array( 'type' => 'boolean' ),
							'order'              => array( 'type' => 'integer' ),
							'adjustment'         => array( 'type' => 'number' ),
							'adjustment_percent' => array( 'type' => 'number' ),
						),
						'required'             => array( 'id', 'name', 'enabled', 'order', 'adjustment', 'adjustment_percent' ),
						'additionalProperties' => false,
					),
				),
				'enabled_services'               => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
			),
			'required'             => array( 'title', 'tax_status', 'packing_method', 'rate_type', 'offer_rates', 'box_packer_library', 'compensation_optional', 'ignore_max_total_cover', 'enable_additional_compensation', 'debug_enabled', 'has_credentials', 'boxes', 'services', 'enabled_services' ),
			'additionalProperties' => false,
		);
	}
}
