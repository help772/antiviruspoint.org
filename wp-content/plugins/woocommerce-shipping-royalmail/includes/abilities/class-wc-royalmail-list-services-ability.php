<?php
/**
 * Royal Mail service catalog ability.
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
 * Lists Royal Mail services and non-secret rate metadata.
 */
class WC_RoyalMail_List_Services_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {
	/**
	 * Get ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return WC_RoyalMail_Ability_Helper::ability_name( 'list-services' );
	}

	/**
	 * Get ability registration args.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'List Royal Mail services', 'woocommerce-shipping-royalmail' ),
			'description'         => __( 'Retrieve the Royal Mail and Parcelforce service catalog with supported rate types, destinations, and non-secret rate metadata.', 'woocommerce-shipping-royalmail' ),
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
		$rate_type        = isset( $input['rate_type'] ) ? (string) $input['rate_type'] : '';
		$destination_type = isset( $input['destination_type'] ) ? (string) $input['destination_type'] : '';

		return array(
			'method_id'         => WC_RoyalMail_Ability_Helper::METHOD_ID,
			'rate_types'        => WC_RoyalMail_Ability_Helper::get_rate_types(),
			'destination_types' => WC_RoyalMail_Ability_Helper::get_destination_types(),
			'services'          => WC_RoyalMail_Ability_Helper::get_service_catalog( $rate_type, $destination_type ),
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
				'rate_type'        => array(
					'type' => 'string',
					'enum' => WC_RoyalMail_Ability_Helper::get_rate_types(),
				),
				'destination_type' => array(
					'type' => 'string',
					'enum' => WC_RoyalMail_Ability_Helper::get_destination_types(),
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
				'method_id'         => array( 'type' => 'string' ),
				'rate_types'        => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'destination_types' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'services'          => array(
					'type'  => 'array',
					'items' => self::get_service_schema(),
				),
			),
			'required'             => array( 'method_id', 'rate_types', 'destination_types', 'services' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get service output schema.
	 *
	 * @return array
	 */
	private static function get_service_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                   => array( 'type' => 'string' ),
				'name'                 => array( 'type' => 'string' ),
				'available_rate_types' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'destination_types'    => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'rate_data'            => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'rate_type'                   => array( 'type' => 'string' ),
							'has_rate_data'               => array( 'type' => 'boolean' ),
							'taxed'                       => array( 'type' => 'boolean' ),
							'package_types'               => array(
								'type'  => 'array',
								'items' => array( 'type' => 'string' ),
							),
							'zone_codes'                  => array(
								'type'  => 'array',
								'items' => array( 'type' => 'string' ),
							),
							'supported_country_count'     => array( 'type' => 'integer' ),
							'has_additional_rates'        => array( 'type' => 'boolean' ),
							'compensation_included_value' => array( 'type' => 'number' ),
							'compensation_max_value'      => array( 'type' => 'number' ),
							'maximum_total_cover'         => array( 'type' => 'number' ),
						),
						'required'             => array( 'rate_type', 'has_rate_data', 'taxed', 'package_types', 'zone_codes', 'supported_country_count', 'has_additional_rates', 'compensation_included_value', 'compensation_max_value', 'maximum_total_cover' ),
						'additionalProperties' => false,
					),
				),
			),
			'required'             => array( 'id', 'name', 'available_rate_types', 'destination_types', 'rate_data' ),
			'additionalProperties' => false,
		);
	}
}
