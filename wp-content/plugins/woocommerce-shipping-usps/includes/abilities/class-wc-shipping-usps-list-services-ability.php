<?php
/**
 * List USPS services ability.
 *
 * @package WC_Shipping_USPS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
	return;
}

/**
 * Ability definition for reading USPS service configuration.
 */
class WC_Shipping_USPS_List_Services_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-usps/list-services';

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
			'label'               => __( 'List USPS services', 'woocommerce-shipping-usps' ),
			'description'         => __( 'Retrieve the USPS service catalog with enabled status and non-secret rate adjustment settings.', 'woocommerce-shipping-usps' ),
			'category'            => WC_Shipping_USPS_Ability_Output::CATEGORY,
			'input_schema'        => WC_Shipping_USPS_Ability_Output::get_instance_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( WC_Shipping_USPS_Ability_Output::class, 'can_read' ),
			'meta'                => WC_Shipping_USPS_Ability_Output::get_read_only_meta(),
		);
	}

	/**
	 * Execute the USPS services read ability.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( array $input = array() ) {
		$shipping_method = WC_Shipping_USPS_Ability_Output::get_shipping_method( $input );

		if ( is_wp_error( $shipping_method ) ) {
			return $shipping_method;
		}

		$services = array();

		foreach ( $shipping_method->services as $service_code => $service ) {
			$service_settings = isset( $shipping_method->custom_services[ $service_code ] ) && is_array( $shipping_method->custom_services[ $service_code ] )
				? $shipping_method->custom_services[ $service_code ]
				: array();
			$subservices      = self::format_subservices( $service['services'], $service_settings );
			$enabled_count    = count( array_filter( wp_list_pluck( $subservices, 'enabled' ) ) );

			$services[] = array(
				'code'                       => (string) $service_code,
				'name'                       => WC_Shipping_USPS_Ability_Output::clean_label( $service['name'] ),
				'configured_name_overridden' => WC_Shipping_USPS_Ability_Output::has_value( $service_settings['name'] ?? '' ),
				'order'                      => WC_Shipping_USPS_Ability_Output::string_value( $service_settings['order'] ?? '' ),
				'enabled_subservice_count'   => $enabled_count,
				'subservice_count'           => count( $subservices ),
				'commercial_service_ids'     => array_values( array_map( 'strval', $service['commercial'] ?? array() ) ),
				'subservices'                => $subservices,
			);
		}

		return array(
			'method_id'                 => 'usps',
			'instance_id'               => absint( $shipping_method->get_instance_id() ),
			'standard_services_enabled' => (bool) $shipping_method->enable_standard_services,
			'service_count'             => count( $services ),
			'services'                  => $services,
			'sensitive_values_omitted'  => true,
		);
	}

	/**
	 * Format configured USPS subservices.
	 *
	 * @param array $subservices      USPS service catalog subservices.
	 * @param array $service_settings Merchant service settings.
	 * @return array
	 */
	private static function format_subservices( array $subservices, array $service_settings ): array {
		$formatted = array();

		foreach ( $subservices as $subservice_code => $subservice_name ) {
			if ( 0 === $subservice_code && is_array( $subservice_name ) ) {
				foreach ( $subservice_name as $nested_code => $nested_name ) {
					$settings    = isset( $service_settings[ $subservice_code ][ $nested_code ] ) && is_array( $service_settings[ $subservice_code ][ $nested_code ] )
						? $service_settings[ $subservice_code ][ $nested_code ]
						: array();
					$formatted[] = self::format_subservice( (string) $nested_code, $nested_name, $settings );
				}

				continue;
			}

			$settings    = isset( $service_settings[ $subservice_code ] ) && is_array( $service_settings[ $subservice_code ] )
				? $service_settings[ $subservice_code ]
				: array();
			$formatted[] = self::format_subservice( (string) $subservice_code, $subservice_name, $settings );
		}

		return $formatted;
	}

	/**
	 * Format one USPS subservice.
	 *
	 * @param string $code     Subservice code.
	 * @param string $name     Subservice name.
	 * @param array  $settings Merchant service settings.
	 * @return array
	 */
	private static function format_subservice( string $code, string $name, array $settings ): array {
		return array(
			'code' => $code,
			'name' => WC_Shipping_USPS_Ability_Output::clean_label( $name ),
		) + WC_Shipping_USPS_Ability_Output::format_service_settings( $settings );
	}

	/**
	 * Get output schema for the services ability.
	 *
	 * @return array
	 */
	private static function get_output_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'method_id'                 => array( 'type' => 'string' ),
				'instance_id'               => array( 'type' => 'integer' ),
				'standard_services_enabled' => array( 'type' => 'boolean' ),
				'service_count'             => array( 'type' => 'integer' ),
				'services'                  => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'code'                       => array( 'type' => 'string' ),
							'name'                       => array( 'type' => 'string' ),
							'configured_name_overridden' => array( 'type' => 'boolean' ),
							'order'                      => array( 'type' => 'string' ),
							'enabled_subservice_count'   => array( 'type' => 'integer' ),
							'subservice_count'           => array( 'type' => 'integer' ),
							'commercial_service_ids'     => array(
								'type'  => 'array',
								'items' => array( 'type' => 'string' ),
							),
							'subservices'                => array(
								'type'  => 'array',
								'items' => self::get_subservice_schema(),
							),
						),
						'additionalProperties' => false,
					),
				),
				'sensitive_values_omitted'  => array( 'type' => 'boolean' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for one USPS subservice.
	 *
	 * @return array
	 */
	private static function get_subservice_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'               => array( 'type' => 'string' ),
				'name'               => array( 'type' => 'string' ),
				'enabled'            => array( 'type' => 'boolean' ),
				'adjustment'         => array( 'type' => 'string' ),
				'adjustment_percent' => array( 'type' => 'number' ),
			),
			'additionalProperties' => false,
		);
	}
}
