<?php
/**
 * List USPS packages ability.
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
 * Ability definition for reading USPS package and box configuration.
 */
class WC_Shipping_USPS_List_Packages_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipping-usps/list-packages';

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
			'label'               => __( 'List USPS packages', 'woocommerce-shipping-usps' ),
			'description'         => __( 'Retrieve USPS flat-rate package catalogs and non-secret merchant packing configuration.', 'woocommerce-shipping-usps' ),
			'category'            => WC_Shipping_USPS_Ability_Output::CATEGORY,
			'input_schema'        => WC_Shipping_USPS_Ability_Output::get_instance_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( WC_Shipping_USPS_Ability_Output::class, 'can_read' ),
			'meta'                => WC_Shipping_USPS_Ability_Output::get_read_only_meta(),
		);
	}

	/**
	 * Execute the USPS packages read ability.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( array $input = array() ) {
		$shipping_method = WC_Shipping_USPS_Ability_Output::get_shipping_method( $input );

		if ( is_wp_error( $shipping_method ) ) {
			return $shipping_method;
		}

		$flat_rate_boxes        = self::format_flat_rate_boxes( $shipping_method );
		$box_packing_boxes      = self::format_box_packing_boxes( (array) $shipping_method->boxes );
		$custom_flat_rate_boxes = self::format_custom_flat_rate_boxes( (array) $shipping_method->custom_flat_rate_boxes );

		return array(
			'method_id'                      => 'usps',
			'instance_id'                    => absint( $shipping_method->get_instance_id() ),
			'packing_method'                 => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->packing_method ),
			'packing_method_label'           => WC_Shipping_USPS_Ability_Output::clean_label( $shipping_method->get_packing_method_label() ),
			'box_packer_library'             => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->box_packer_library ),
			'flat_rate_boxes_mode'           => WC_Shipping_USPS_Ability_Output::string_value( $shipping_method->enable_flat_rate_boxes ),
			'flat_rate_box_weights_enabled'  => (bool) $shipping_method->enable_flat_rate_box_weights,
			'custom_flat_rate_boxes_enabled' => (bool) $shipping_method->enable_custom_flat_rate_boxes,
			'flat_rate_box_count'            => count( $flat_rate_boxes ),
			'box_packing_box_count'          => count( $box_packing_boxes ),
			'custom_flat_rate_box_count'     => count( $custom_flat_rate_boxes ),
			'flat_rate_boxes'                => $flat_rate_boxes,
			'box_packing_boxes'              => $box_packing_boxes,
			'custom_flat_rate_boxes'         => $custom_flat_rate_boxes,
			'sensitive_values_omitted'       => true,
		);
	}

	/**
	 * Format USPS flat-rate boxes.
	 *
	 * @param WC_Shipping_USPS $shipping_method USPS shipping method.
	 * @return array
	 */
	private static function format_flat_rate_boxes( WC_Shipping_USPS $shipping_method ): array {
		$boxes = array();

		foreach ( $shipping_method->flat_rate_boxes as $code => $box ) {
			$default_weight = (float) ( $box['weight'] ?? 0 );

			$boxes[] = array(
				'code'                    => (string) $code,
				'usps_id'                 => WC_Shipping_USPS_Ability_Output::string_value( $box['id'] ?? '' ),
				'name'                    => WC_Shipping_USPS_Ability_Output::clean_label( WC_Shipping_USPS_Ability_Output::string_value( $box['name'] ?? '' ) ),
				'service'                 => WC_Shipping_USPS_Ability_Output::string_value( $box['service'] ?? '' ),
				'type'                    => WC_Shipping_USPS_Ability_Output::string_value( $box['type'] ?? '' ),
				'rate_indicator'          => WC_Shipping_USPS_Ability_Output::string_value( $box['rate_indicator'] ?? '' ),
				'domestic'                => 0 === strpos( (string) $code, 'd' ),
				'length'                  => (float) ( $box['length'] ?? 0 ),
				'width'                   => (float) ( $box['width'] ?? 0 ),
				'height'                  => (float) ( $box['height'] ?? 0 ),
				'default_empty_weight'    => $default_weight,
				'effective_empty_weight'  => (float) $shipping_method->get_empty_box_weight( (string) $code, $default_weight ),
				'max_weight'              => (float) ( $box['max_weight'] ?? 0 ),
				'empty_weight_overridden' => ! empty( $shipping_method->flat_rate_box_weights[ $code ] ),
			);
		}

		return $boxes;
	}

	/**
	 * Format merchant box-packing boxes without exposing free-form names.
	 *
	 * @param array $boxes Merchant box packing boxes.
	 * @return array
	 */
	private static function format_box_packing_boxes( array $boxes ): array {
		$formatted = array();

		foreach ( $boxes as $box ) {
			if ( ! is_array( $box ) ) {
				continue;
			}

			$formatted[] = array(
				'name_configured' => WC_Shipping_USPS_Ability_Output::has_value( $box['name'] ?? '' ),
				'outer_length'    => (float) ( $box['outer_length'] ?? 0 ),
				'outer_width'     => (float) ( $box['outer_width'] ?? 0 ),
				'outer_height'    => (float) ( $box['outer_height'] ?? 0 ),
				'inner_length'    => (float) ( $box['inner_length'] ?? 0 ),
				'inner_width'     => (float) ( $box['inner_width'] ?? 0 ),
				'inner_height'    => (float) ( $box['inner_height'] ?? 0 ),
				'box_weight'      => (float) ( $box['box_weight'] ?? 0 ),
				'max_weight'      => (float) ( $box['max_weight'] ?? 0 ),
				'is_letter'       => ! empty( $box['is_letter'] ),
			);
		}

		return $formatted;
	}

	/**
	 * Format merchant custom flat-rate boxes without exposing free-form names.
	 *
	 * @param array $boxes Merchant custom flat-rate boxes.
	 * @return array
	 */
	private static function format_custom_flat_rate_boxes( array $boxes ): array {
		$formatted = array();

		foreach ( $boxes as $box ) {
			if ( ! is_array( $box ) ) {
				continue;
			}

			$formatted[] = array(
				'name_configured' => WC_Shipping_USPS_Ability_Output::has_value( $box['name'] ?? '' ),
				'length'          => (float) ( $box['length'] ?? 0 ),
				'width'           => (float) ( $box['width'] ?? 0 ),
				'height'          => (float) ( $box['height'] ?? 0 ),
				'box_weight'      => (float) ( $box['box_weight'] ?? 0 ),
				'max_weight'      => (float) ( $box['max_weight'] ?? 0 ),
				'flat_rate_type'  => WC_Shipping_USPS_Ability_Output::string_value( $box['flat_rate_type'] ?? '' ),
			);
		}

		return $formatted;
	}

	/**
	 * Get output schema for the packages ability.
	 *
	 * @return array
	 */
	private static function get_output_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'method_id'                      => array( 'type' => 'string' ),
				'instance_id'                    => array( 'type' => 'integer' ),
				'packing_method'                 => array( 'type' => 'string' ),
				'packing_method_label'           => array( 'type' => 'string' ),
				'box_packer_library'             => array( 'type' => 'string' ),
				'flat_rate_boxes_mode'           => array( 'type' => 'string' ),
				'flat_rate_box_weights_enabled'  => array( 'type' => 'boolean' ),
				'custom_flat_rate_boxes_enabled' => array( 'type' => 'boolean' ),
				'flat_rate_box_count'            => array( 'type' => 'integer' ),
				'box_packing_box_count'          => array( 'type' => 'integer' ),
				'custom_flat_rate_box_count'     => array( 'type' => 'integer' ),
				'flat_rate_boxes'                => array(
					'type'  => 'array',
					'items' => self::get_flat_rate_box_schema(),
				),
				'box_packing_boxes'              => array(
					'type'  => 'array',
					'items' => self::get_box_packing_box_schema(),
				),
				'custom_flat_rate_boxes'         => array(
					'type'  => 'array',
					'items' => self::get_custom_flat_rate_box_schema(),
				),
				'sensitive_values_omitted'       => array( 'type' => 'boolean' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for one USPS flat-rate box.
	 *
	 * @return array
	 */
	private static function get_flat_rate_box_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'code'                    => array( 'type' => 'string' ),
				'usps_id'                 => array( 'type' => 'string' ),
				'name'                    => array( 'type' => 'string' ),
				'service'                 => array( 'type' => 'string' ),
				'type'                    => array( 'type' => 'string' ),
				'rate_indicator'          => array( 'type' => 'string' ),
				'domestic'                => array( 'type' => 'boolean' ),
				'length'                  => array( 'type' => 'number' ),
				'width'                   => array( 'type' => 'number' ),
				'height'                  => array( 'type' => 'number' ),
				'default_empty_weight'    => array( 'type' => 'number' ),
				'effective_empty_weight'  => array( 'type' => 'number' ),
				'max_weight'              => array( 'type' => 'number' ),
				'empty_weight_overridden' => array( 'type' => 'boolean' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for one merchant box-packing box.
	 *
	 * @return array
	 */
	private static function get_box_packing_box_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'name_configured' => array( 'type' => 'boolean' ),
				'outer_length'    => array( 'type' => 'number' ),
				'outer_width'     => array( 'type' => 'number' ),
				'outer_height'    => array( 'type' => 'number' ),
				'inner_length'    => array( 'type' => 'number' ),
				'inner_width'     => array( 'type' => 'number' ),
				'inner_height'    => array( 'type' => 'number' ),
				'box_weight'      => array( 'type' => 'number' ),
				'max_weight'      => array( 'type' => 'number' ),
				'is_letter'       => array( 'type' => 'boolean' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for one merchant custom flat-rate box.
	 *
	 * @return array
	 */
	private static function get_custom_flat_rate_box_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'name_configured' => array( 'type' => 'boolean' ),
				'length'          => array( 'type' => 'number' ),
				'width'           => array( 'type' => 'number' ),
				'height'          => array( 'type' => 'number' ),
				'box_weight'      => array( 'type' => 'number' ),
				'max_weight'      => array( 'type' => 'number' ),
				'flat_rate_type'  => array( 'type' => 'string' ),
			),
			'additionalProperties' => false,
		);
	}
}
