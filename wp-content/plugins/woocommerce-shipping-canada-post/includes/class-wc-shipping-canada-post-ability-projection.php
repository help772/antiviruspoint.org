<?php
/**
 * Canada Post ability output projection.
 *
 * @package woocommerce-shipping-canada-post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Projects Canada Post shipping method configuration into ability responses.
 */
class WC_Shipping_Canada_Post_Ability_Projection {

	/**
	 * Non-secret configuration snapshot from the shipping method.
	 *
	 * @var array
	 */
	private $snapshot;

	/**
	 * Constructor.
	 *
	 * @param WC_Shipping_Canada_Post $method Canada Post shipping method.
	 */
	public function __construct( WC_Shipping_Canada_Post $method ) {
		$this->snapshot = $method->get_configuration_snapshot();
	}

	/**
	 * Get non-secret Canada Post settings for ability output.
	 *
	 * @return array
	 */
	public function get_safe_settings() {
		$settings = self::get_array_value( $this->snapshot, 'settings' );

		return array(
			'method_id'                    => isset( $settings['method_id'] ) ? (string) $settings['method_id'] : '',
			'title'                        => isset( $settings['title'] ) ? (string) $settings['title'] : '',
			'enabled'                      => isset( $settings['enabled'] ) ? wc_string_to_bool( $settings['enabled'] ) : true,
			'has_origin'                   => ! empty( $settings['has_origin'] ),
			'api_mode'                     => isset( $settings['api_mode'] ) ? (string) $settings['api_mode'] : 'production',
			'quote_type'                   => isset( $settings['quote_type'] ) ? (string) $settings['quote_type'] : 'commercial',
			'cost_basis'                   => isset( $settings['cost_basis'] ) ? (string) $settings['cost_basis'] : 'due',
			'packing_method'               => isset( $settings['packing_method'] ) ? (string) $settings['packing_method'] : 'per_item',
			'max_weight_kg'                => isset( $settings['max_weight_kg'] ) && is_numeric( $settings['max_weight_kg'] ) ? (float) $settings['max_weight_kg'] : 0.0,
			'offer_rates'                  => isset( $settings['offer_rates'] ) ? (string) $settings['offer_rates'] : 'all',
			'box_packer_library'           => isset( $settings['box_packer_library'] ) ? (string) $settings['box_packer_library'] : 'original',
			'flat_rates_enabled'           => ! empty( $settings['flat_rates_enabled'] ),
			'lettermail_services'          => self::get_string_list( isset( $settings['lettermail'] ) ? $settings['lettermail'] : array() ),
			'additional_options'           => self::get_string_list( isset( $settings['options'] ) ? $settings['options'] : array() ),
			'show_delivery_time'           => ! empty( $settings['show_delivery_time'] ),
			'delivery_time_delay_days'     => isset( $settings['delivery_time_delay_days'] ) ? absint( $settings['delivery_time_delay_days'] ) : 0,
			'debug_enabled'                => ! empty( $settings['debug_enabled'] ),
			'has_api_credentials'          => ! empty( $settings['has_api_credentials'] ),
			'has_commercial_account'       => ! empty( $settings['has_commercial_account'] ),
			'has_contract'                 => ! empty( $settings['has_contract'] ),
			'has_complete_commercial_auth' => ! empty( $settings['has_complete_commercial_auth'] ),
		);
	}

	/**
	 * Get Canada Post service configuration for ability output.
	 *
	 * @return array
	 */
	public function get_service_configuration() {
		$services           = array();
		$service_snapshot   = self::get_array_value( $this->snapshot, 'services' );
		$available_services = self::get_array_value( $service_snapshot, 'available' );
		$configured         = self::get_array_value( $service_snapshot, 'configured' );

		foreach ( $available_services as $code => $name ) {
			$settings                 = isset( $configured[ $code ] ) && is_array( $configured[ $code ] ) ? $configured[ $code ] : array();
			$configured_name          = isset( $settings['name'] ) && is_scalar( $settings['name'] ) ? (string) $settings['name'] : '';
			$price_adjustment         = isset( $settings['adjustment'] ) && is_scalar( $settings['adjustment'] ) ? (string) $settings['adjustment'] : '';
			$price_adjustment_percent = isset( $settings['adjustment_percent'] ) && is_scalar( $settings['adjustment_percent'] ) ? (string) $settings['adjustment_percent'] : '';
			$services[]               = array(
				'code'                         => (string) $code,
				'name'                         => (string) $name,
				'region'                       => self::get_service_region( (string) $code ),
				'configured_name'              => $configured_name,
				'effective_name'               => '' !== $configured_name ? $configured_name : (string) $name,
				'enabled'                      => ! isset( $settings['enabled'] ) || wc_string_to_bool( $settings['enabled'] ),
				'sort_order'                   => isset( $settings['order'] ) ? absint( $settings['order'] ) : 999,
				'price_adjustment'             => $price_adjustment,
				'price_adjustment_percent'     => $price_adjustment_percent,
				'has_price_adjustment'         => '' !== $price_adjustment,
				'has_price_adjustment_percent' => '' !== $price_adjustment_percent,
			);
		}

		return $services;
	}

	/**
	 * Get Canada Post package, flat-rate box, and lettermail catalogs for ability output.
	 *
	 * @return array
	 */
	public function get_package_options() {
		$settings = self::get_array_value( $this->snapshot, 'settings' );
		$packages = self::get_array_value( $this->snapshot, 'packages' );

		return array(
			'packing_method'                   => isset( $settings['packing_method'] ) ? (string) $settings['packing_method'] : 'per_item',
			'custom_boxes'                     => $this->get_custom_boxes( self::get_array_value( $packages, 'custom_boxes' ) ),
			'flat_rate_boxes'                  => $this->get_flat_rate_boxes(
				self::get_array_value( $packages, 'flat_rate_boxes' ),
				self::get_array_value( $packages, 'flat_rate_boxes_enabled' )
			),
			'lettermail_catalogs'              => $this->get_lettermail_catalogs( self::get_array_value( $packages, 'lettermail_catalogs' ) ),
			'registered_lettermail_surcharges' => self::get_array_value( $packages, 'registered_lettermail_surcharges' ),
			'additional_liability_coverage'    => self::get_array_value( $packages, 'additional_liability_coverage' ),
		);
	}

	/**
	 * Get configured custom boxes for ability output.
	 *
	 * @param array $configured_boxes Configured custom boxes.
	 * @return array
	 */
	private function get_custom_boxes( array $configured_boxes ) {
		$boxes = array();

		foreach ( $configured_boxes as $box ) {
			if ( ! is_array( $box ) ) {
				continue;
			}

			$boxes[] = array(
				'name'                => isset( $box['name'] ) && is_scalar( $box['name'] ) ? (string) $box['name'] : '',
				'enabled'             => ! isset( $box['enabled'] ) || wc_string_to_bool( $box['enabled'] ),
				'outer_dimensions_cm' => array(
					'length' => self::get_float_value( $box, 'outer_length' ),
					'width'  => self::get_float_value( $box, 'outer_width' ),
					'height' => self::get_float_value( $box, 'outer_height' ),
				),
				'inner_dimensions_cm' => array(
					'length' => self::get_float_value( $box, 'inner_length' ),
					'width'  => self::get_float_value( $box, 'inner_width' ),
					'height' => self::get_float_value( $box, 'inner_height' ),
				),
				'box_weight_kg'       => self::get_float_value( $box, 'box_weight' ),
				'max_weight_kg'       => self::get_float_value( $box, 'max_weight' ),
			);
		}

		return $boxes;
	}

	/**
	 * Get flat-rate box catalog for ability output.
	 *
	 * @param array $flat_rate_boxes         Flat-rate box catalog.
	 * @param array $flat_rate_boxes_enabled Enabled-state map.
	 * @return array
	 */
	private function get_flat_rate_boxes( array $flat_rate_boxes, array $flat_rate_boxes_enabled ) {
		$boxes = array();

		foreach ( $flat_rate_boxes as $code => $box ) {
			if ( ! is_array( $box ) ) {
				continue;
			}

			$boxes[] = array(
				'code'          => (string) $code,
				'name'          => isset( $box['name'] ) ? (string) $box['name'] : '',
				'dimensions_cm' => array(
					'length' => self::get_float_value( $box, 'length' ),
					'width'  => self::get_float_value( $box, 'width' ),
					'height' => self::get_float_value( $box, 'height' ),
				),
				'max_weight_kg' => self::get_float_value( $box, 'max_weight' ),
				'cost'          => isset( $box['cost'] ) && is_scalar( $box['cost'] ) ? (string) $box['cost'] : '',
				'enabled'       => ! isset( $flat_rate_boxes_enabled[ $code ] ) || wc_string_to_bool( $flat_rate_boxes_enabled[ $code ] ),
			);
		}

		return $boxes;
	}

	/**
	 * Get lettermail catalogs for ability output.
	 *
	 * @param array $catalogs Lettermail catalogs keyed by scope.
	 * @return array
	 */
	private function get_lettermail_catalogs( array $catalogs ) {
		$output = array();

		foreach ( $catalogs as $scope => $boxes ) {
			$catalog_boxes = array();
			$boxes         = is_array( $boxes ) ? $boxes : array();

			foreach ( $boxes as $code => $box ) {
				if ( ! is_array( $box ) ) {
					continue;
				}

				$rate_tiers = array();
				$costs      = isset( $box['costs'] ) && is_array( $box['costs'] ) ? $box['costs'] : array();

				foreach ( $costs as $weight => $cost ) {
					$rate_tiers[] = array(
						'max_weight_kg' => is_numeric( $weight ) ? (float) $weight : 0.0,
						'cost'          => is_scalar( $cost ) ? (string) $cost : '',
					);
				}

				$catalog_boxes[] = array(
					'code'          => (string) $code,
					'name'          => isset( $box['name'] ) ? (string) $box['name'] : '',
					'dimensions_cm' => array(
						'length' => self::get_float_value( $box, 'length' ),
						'width'  => self::get_float_value( $box, 'width' ),
						'height' => self::get_float_value( $box, 'height' ),
					),
					'max_weight_kg' => self::get_float_value( $box, 'weight' ),
					'rate_tiers'    => $rate_tiers,
				);
			}

			$output[] = array(
				'scope' => (string) $scope,
				'boxes' => $catalog_boxes,
			);
		}

		return $output;
	}

	/**
	 * Normalize an option value to a string list.
	 *
	 * @param mixed $value Option value.
	 * @return array
	 */
	private static function get_string_list( $value ) {
		if ( ! is_array( $value ) ) {
			$value = empty( $value ) ? array() : array( $value );
		}

		return array_values(
			array_map(
				'strval',
				array_filter(
					$value,
					'is_scalar'
				)
			)
		);
	}

	/**
	 * Get an array value from an array.
	 *
	 * @param array  $values Values.
	 * @param string $key Value key.
	 * @return array
	 */
	private static function get_array_value( array $values, $key ) {
		return isset( $values[ $key ] ) && is_array( $values[ $key ] ) ? $values[ $key ] : array();
	}

	/**
	 * Get a float value from an array.
	 *
	 * @param array  $values Values.
	 * @param string $key Value key.
	 * @return float
	 */
	private static function get_float_value( array $values, $key ) {
		return isset( $values[ $key ] ) && is_numeric( $values[ $key ] ) ? (float) $values[ $key ] : 0.0;
	}

	/**
	 * Classify a Canada Post service code by destination region.
	 *
	 * @param string $code Service code.
	 * @return string
	 */
	private static function get_service_region( $code ) {
		if ( 0 === strpos( $code, 'DOM.' ) ) {
			return 'domestic';
		}

		if ( 0 === strpos( $code, 'USA.' ) ) {
			return 'united_states';
		}

		if ( 0 === strpos( $code, 'INT.' ) ) {
			return 'international';
		}

		return 'unknown';
	}
}
