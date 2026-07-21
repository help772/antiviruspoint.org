<?php
/**
 * Get USPS settings ability.
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
 * Ability definition for reading non-secret USPS settings.
 */
class WC_Shipping_USPS_Get_Settings_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-usps/get-settings';

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
			'label'               => __( 'Get USPS settings', 'woocommerce-shipping-usps' ),
			'description'         => __( 'Retrieve a non-secret summary of WooCommerce USPS shipping configuration and environment requirements.', 'woocommerce-shipping-usps' ),
			'category'            => WC_Shipping_USPS_Ability_Output::CATEGORY,
			'input_schema'        => WC_Shipping_USPS_Ability_Output::get_instance_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( WC_Shipping_USPS_Ability_Output::class, 'can_read' ),
			'meta'                => WC_Shipping_USPS_Ability_Output::get_read_only_meta(),
		);
	}

	/**
	 * Execute the USPS settings read ability.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( array $input = array() ) {
		$shipping_method = WC_Shipping_USPS_Ability_Output::get_shipping_method( $input );

		if ( is_wp_error( $shipping_method ) ) {
			return $shipping_method;
		}

		$base_country = '';
		if ( WC()->countries ) {
			$base_country = WC()->countries->get_base_country();
		}

		$api_mode              = WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->api_type );
		$has_rest_credentials  = WC_Shipping_USPS_Ability_Output::has_value( $shipping_method->client_id ) && WC_Shipping_USPS_Ability_Output::has_value( $shipping_method->client_secret );
		$has_legacy_credential = WC_Shipping_USPS_Ability_Output::has_value( $shipping_method->user_id );

		return array(
			'method_id'                             => 'usps',
			'method_title'                          => WC_Shipping_USPS_Ability_Output::clean_label( $shipping_method->method_title ),
			'instance_id'                           => absint( $shipping_method->get_instance_id() ),
			'api_mode'                              => $api_mode,
			'has_credentials'                       => 'rest' === $api_mode ? $has_rest_credentials : $has_legacy_credential,
			'has_rest_credentials'                  => $has_rest_credentials,
			'has_legacy_credential'                 => $has_legacy_credential,
			'has_origin_postcode'                   => WC_Shipping_USPS_Ability_Output::has_value( $shipping_method->origin ),
			'rate_type'                             => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->shippingrates ),
			'offer_rates'                           => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->offer_rates ),
			'fallback_rate_enabled'                 => WC_Shipping_USPS_Ability_Output::has_value( $shipping_method->fallback ),
			'flat_rate_boxes_mode'                  => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->enable_flat_rate_boxes ),
			'standard_services_enabled'             => (bool) $shipping_method->enable_standard_services,
			'sort_by_price'                         => (bool) $shipping_method->sort_by_price,
			'display_delivery_time'                 => 'yes' === $shipping_method->get_option( 'show_delivery_time', 'yes' ),
			'packing_method'                        => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->packing_method ),
			'packing_method_label'                  => WC_Shipping_USPS_Ability_Output::clean_label( $shipping_method->get_packing_method_label() ),
			'unpacked_item_handling'                => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->unpacked_item_handling ),
			'box_packer_library'                    => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->box_packer_library ),
			'flat_rate_box_weights_enabled'         => (bool) $shipping_method->enable_flat_rate_box_weights,
			'custom_flat_rate_boxes_enabled'        => (bool) $shipping_method->enable_custom_flat_rate_boxes,
			'default_product_weight_configured'     => WC_Shipping_USPS_Ability_Output::has_value( $shipping_method->product_weight ),
			'default_product_dimensions_configured' => count( array_filter( (array) $shipping_method->product_dimensions ) ) > 0,
			'environment'                           => array(
				'currency'               => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : '',
				'currency_supported'     => function_exists( 'get_woocommerce_currency' ) && 'USD' === get_woocommerce_currency(),
				'base_country'           => WC_Shipping_USPS_Ability_Output::string_value( $base_country ),
				'base_country_supported' => in_array( $base_country, array( 'US', 'PR', 'VI', 'MH', 'FM' ), true ),
				'simplexml_available'    => function_exists( 'simplexml_load_string' ),
			),
			'sensitive_values_omitted'              => true,
		);
	}

	/**
	 * Get output schema for the settings ability.
	 *
	 * @return array
	 */
	private static function get_output_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'method_id'                             => array( 'type' => 'string' ),
				'method_title'                          => array( 'type' => 'string' ),
				'instance_id'                           => array( 'type' => 'integer' ),
				'api_mode'                              => array( 'type' => 'string' ),
				'has_credentials'                       => array( 'type' => 'boolean' ),
				'has_rest_credentials'                  => array( 'type' => 'boolean' ),
				'has_legacy_credential'                 => array( 'type' => 'boolean' ),
				'has_origin_postcode'                   => array( 'type' => 'boolean' ),
				'rate_type'                             => array( 'type' => 'string' ),
				'offer_rates'                           => array( 'type' => 'string' ),
				'fallback_rate_enabled'                 => array( 'type' => 'boolean' ),
				'flat_rate_boxes_mode'                  => array( 'type' => 'string' ),
				'standard_services_enabled'             => array( 'type' => 'boolean' ),
				'sort_by_price'                         => array( 'type' => 'boolean' ),
				'display_delivery_time'                 => array( 'type' => 'boolean' ),
				'packing_method'                        => array( 'type' => 'string' ),
				'packing_method_label'                  => array( 'type' => 'string' ),
				'unpacked_item_handling'                => array( 'type' => 'string' ),
				'box_packer_library'                    => array( 'type' => 'string' ),
				'flat_rate_box_weights_enabled'         => array( 'type' => 'boolean' ),
				'custom_flat_rate_boxes_enabled'        => array( 'type' => 'boolean' ),
				'default_product_weight_configured'     => array( 'type' => 'boolean' ),
				'default_product_dimensions_configured' => array( 'type' => 'boolean' ),
				'environment'                           => array(
					'type'                 => 'object',
					'properties'           => array(
						'currency'               => array( 'type' => 'string' ),
						'currency_supported'     => array( 'type' => 'boolean' ),
						'base_country'           => array( 'type' => 'string' ),
						'base_country_supported' => array( 'type' => 'boolean' ),
						'simplexml_available'    => array( 'type' => 'boolean' ),
					),
					'additionalProperties' => false,
				),
				'sensitive_values_omitted'              => array( 'type' => 'boolean' ),
			),
			'additionalProperties' => false,
		);
	}
}
