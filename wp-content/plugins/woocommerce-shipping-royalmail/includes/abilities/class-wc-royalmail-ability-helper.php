<?php
/**
 * Shared helpers for Royal Mail abilities.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Rate_Data_Manager;

/**
 * Helper methods used by Royal Mail ability definitions.
 */
class WC_RoyalMail_Ability_Helper {
	/**
	 * Shipping method ID.
	 *
	 * @var string
	 */
	const METHOD_ID = 'royal_mail';

	/**
	 * Ability namespace.
	 *
	 * @var string
	 */
	const ABILITY_NAMESPACE = 'woocommerce-shipping-royalmail';

	/**
	 * Check whether the current user can read Royal Mail store settings.
	 *
	 * @return bool
	 */
	public static function can_manage_woocommerce(): bool {
		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this store-management capability.
		return function_exists( 'current_user_can' ) && current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Get standard read-only ability metadata.
	 *
	 * @return array
	 */
	public static function get_read_only_meta(): array {
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

	/**
	 * Build a Royal Mail ability name.
	 *
	 * @param string $name Local ability name.
	 * @return string
	 */
	public static function ability_name( string $name ): string {
		return self::ABILITY_NAMESPACE . '/' . $name;
	}

	/**
	 * Get all supported rate types.
	 *
	 * @return string[]
	 */
	public static function get_rate_types(): array {
		return array( WC_RoyalMail::REGULAR_SERVICE, WC_RoyalMail::ONLINE_SERVICE );
	}

	/**
	 * Get all supported destination types.
	 *
	 * @return string[]
	 */
	public static function get_destination_types(): array {
		return array( WC_RoyalMail::UK_SERVICE, WC_RoyalMail::INTERNATIONAL_SERVICE );
	}

	/**
	 * Get service IDs for a rate type and destination country.
	 *
	 * @param string $rate_type Rate type.
	 * @param string $country_code Destination country code.
	 * @return string[]
	 */
	public static function get_services_for_country( string $rate_type, string $country_code ): array {
		$destination_type = in_array( strtoupper( $country_code ), array( 'GB', 'GG', 'IM', 'JE' ), true )
			? WC_RoyalMail::UK_SERVICE
			: WC_RoyalMail::INTERNATIONAL_SERVICE;
		$services         = WC_RoyalMail::get_all_services();

		return isset( $services[ $rate_type ][ $destination_type ] ) ? $services[ $rate_type ][ $destination_type ] : array();
	}

	/**
	 * Get service catalog entries.
	 *
	 * @param string $rate_type_filter Optional rate type filter.
	 * @param string $destination_filter Optional destination type filter.
	 * @return array
	 */
	public static function get_service_catalog( string $rate_type_filter = '', string $destination_filter = '' ): array {
		$catalog = array();

		foreach ( WC_RoyalMail::$service_names as $service_id => $service_name ) {
			$rate_types        = self::get_service_rate_types( $service_id );
			$destination_types = self::get_service_destination_types( $service_id );

			if ( '' !== $rate_type_filter && ! in_array( $rate_type_filter, $rate_types, true ) ) {
				continue;
			}

			if ( '' !== $destination_filter && ! in_array( $destination_filter, $destination_types, true ) ) {
				continue;
			}

			$filtered_rate_types = '' === $rate_type_filter ? $rate_types : array( $rate_type_filter );

			$catalog[] = array(
				'id'                   => $service_id,
				'name'                 => self::clean_label( $service_name ),
				'available_rate_types' => array_values( $rate_types ),
				'destination_types'    => array_values( $destination_types ),
				'rate_data'            => self::get_rate_data_summaries( $service_id, $filtered_rate_types ),
			);
		}

		return $catalog;
	}

	/**
	 * Get available rate types for a service.
	 *
	 * @param string $service_id Service ID.
	 * @return string[]
	 */
	public static function get_service_rate_types( string $service_id ): array {
		$rate_types = array();

		if ( self::service_exists_in_rate_type( $service_id, WC_RoyalMail::REGULAR_SERVICE ) ) {
			$rate_types[] = WC_RoyalMail::REGULAR_SERVICE;
		}

		if ( self::service_exists_in_rate_type( $service_id, WC_RoyalMail::ONLINE_SERVICE ) ) {
			$rate_types[] = WC_RoyalMail::ONLINE_SERVICE;
		}

		return $rate_types;
	}

	/**
	 * Get available destination types for a service.
	 *
	 * @param string $service_id Service ID.
	 * @return string[]
	 */
	public static function get_service_destination_types( string $service_id ): array {
		$destination_types = array();

		foreach ( self::get_destination_types() as $destination_type ) {
			if (
				self::service_exists_in_group( $service_id, WC_RoyalMail::REGULAR_SERVICE, $destination_type )
				|| self::service_exists_in_group( $service_id, WC_RoyalMail::ONLINE_SERVICE, $destination_type )
			) {
				$destination_types[] = $destination_type;
			}
		}

		return $destination_types;
	}

	/**
	 * Get effective non-secret settings for a Royal Mail instance.
	 *
	 * @param int $instance_id Shipping method instance ID.
	 * @return array
	 */
	public static function get_effective_settings( int $instance_id = 0 ): array {
		$defaults = array(
			'title'                     => 'Royal Mail',
			'tax_status'                => 'taxable',
			'packing_method'            => 'per_item',
			'rate_type'                 => WC_RoyalMail::REGULAR_SERVICE,
			'offer_rates'               => 'all',
			'box_packer_library'        => 'dvdoug',
			'compensation_optional'     => 'no',
			'ignore_max_total_cover'    => 'no',
			'enable_addit_compensation' => 'no',
			'debug_mode'                => 'no',
			'boxes'                     => array(),
			'services'                  => array(),
		);

		$general_settings  = self::get_option_array( 'woocommerce_royal_mail_settings' );
		$instance_settings = $instance_id > 0 ? self::get_option_array( self::get_instance_option_key( $instance_id ) ) : array();

		return array_merge( $defaults, $general_settings, $instance_settings );
	}

	/**
	 * Get sanitized non-secret settings output.
	 *
	 * @param array $settings Raw settings.
	 * @return array
	 */
	public static function format_settings( array $settings ): array {
		$formatted_services = self::format_custom_services( isset( $settings['services'] ) && is_array( $settings['services'] ) ? $settings['services'] : array() );

		return array(
			'title'                          => self::clean_text( $settings['title'] ?? 'Royal Mail' ),
			'tax_status'                     => self::clean_text( $settings['tax_status'] ?? 'taxable' ),
			'packing_method'                 => self::clean_text( $settings['packing_method'] ?? 'per_item' ),
			'rate_type'                      => self::clean_text( $settings['rate_type'] ?? WC_RoyalMail::REGULAR_SERVICE ),
			'offer_rates'                    => self::clean_text( $settings['offer_rates'] ?? 'all' ),
			'box_packer_library'             => self::clean_text( $settings['box_packer_library'] ?? 'dvdoug' ),
			'compensation_optional'          => self::is_yes( $settings['compensation_optional'] ?? 'no' ),
			'ignore_max_total_cover'         => self::is_yes( $settings['ignore_max_total_cover'] ?? 'no' ),
			'enable_additional_compensation' => self::is_yes( $settings['enable_addit_compensation'] ?? 'no' ),
			'debug_enabled'                  => self::is_yes( $settings['debug_mode'] ?? 'no' ),
			'has_credentials'                => false,
			'boxes'                          => self::format_boxes( isset( $settings['boxes'] ) && is_array( $settings['boxes'] ) ? $settings['boxes'] : array() ),
			'services'                       => $formatted_services,
			'enabled_services'               => self::get_enabled_service_ids( $formatted_services ),
		);
	}

	/**
	 * Get configured Royal Mail shipping method instances from WooCommerce zones.
	 *
	 * @param int $requested_instance_id Optional instance ID filter.
	 * @return array
	 */
	public static function get_shipping_method_instances( int $requested_instance_id = 0 ): array {
		$instances = array();

		if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
			if ( $requested_instance_id > 0 ) {
				$instances[] = array(
					'instance_id' => $requested_instance_id,
					'zone_id'     => 0,
					'zone_name'   => '',
					'enabled'     => true,
					'settings'    => self::format_settings( self::get_effective_settings( $requested_instance_id ) ),
				);
			}

			return $instances;
		}

		$zones = \WC_Shipping_Zones::get_zones();
		foreach ( $zones as $zone ) {
			self::append_zone_instances( $instances, (int) $zone['zone_id'], (string) $zone['zone_name'], $zone['shipping_methods'] ?? array(), $requested_instance_id );
		}

		$rest_of_world = \WC_Shipping_Zones::get_zone( 0 );
		if ( $rest_of_world && method_exists( $rest_of_world, 'get_shipping_methods' ) ) {
			self::append_zone_instances( $instances, 0, __( 'Rest of the world', 'woocommerce-shipping-royalmail' ), $rest_of_world->get_shipping_methods(), $requested_instance_id );
		}

		return $instances;
	}

	/**
	 * Create a WP_Error when available.
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 * @param array  $data Error data.
	 * @return WP_Error|array
	 */
	public static function error( string $code, string $message, array $data = array() ) {
		if ( class_exists( 'WP_Error' ) ) {
			return new WP_Error( $code, $message, $data );
		}

		return array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		);
	}

	/**
	 * Get a settings option as an array.
	 *
	 * @param string $option Option name.
	 * @return array
	 */
	private static function get_option_array( string $option ): array {
		$value = get_option( $option, array() );
		return is_array( $value ) ? $value : array();
	}

	/**
	 * Append Royal Mail instances from one zone.
	 *
	 * @param array  $instances Instances accumulator.
	 * @param int    $zone_id Zone ID.
	 * @param string $zone_name Zone name.
	 * @param array  $shipping_methods Shipping methods.
	 * @param int    $requested_instance_id Optional instance ID filter.
	 * @return void
	 */
	private static function append_zone_instances( array &$instances, int $zone_id, string $zone_name, array $shipping_methods, int $requested_instance_id = 0 ): void {
		foreach ( $shipping_methods as $shipping_method ) {
			if ( ! is_object( $shipping_method ) || self::METHOD_ID !== ( $shipping_method->id ?? '' ) ) {
				continue;
			}

			$instance_id = absint( $shipping_method->instance_id ?? 0 );
			if ( $requested_instance_id > 0 && $requested_instance_id !== $instance_id ) {
				continue;
			}

			$settings = isset( $shipping_method->instance_settings ) && is_array( $shipping_method->instance_settings )
				? array_merge( self::get_effective_settings( $instance_id ), $shipping_method->instance_settings )
				: self::get_effective_settings( $instance_id );

			$instances[] = array(
				'instance_id' => $instance_id,
				'zone_id'     => $zone_id,
				'zone_name'   => self::clean_text( $zone_name ),
				'enabled'     => self::is_yes( $shipping_method->enabled ?? 'yes' ),
				'settings'    => self::format_settings( $settings ),
			);
		}
	}

	/**
	 * Get option key for an instance.
	 *
	 * @param int $instance_id Instance ID.
	 * @return string
	 */
	private static function get_instance_option_key( int $instance_id ): string {
		return 'woocommerce_royal_mail_' . $instance_id . '_settings';
	}

	/**
	 * Get sanitized service rate summaries.
	 *
	 * @param string $service_id Service ID.
	 * @param array  $rate_types Rate types.
	 * @return array
	 */
	private static function get_rate_data_summaries( string $service_id, array $rate_types ): array {
		$summaries = array();

		foreach ( $rate_types as $rate_type ) {
			$data = self::get_local_rate_data( $service_id, $rate_type );

			if ( ! is_array( $data ) ) {
				$summaries[] = array(
					'rate_type'                   => $rate_type,
					'has_rate_data'               => false,
					'taxed'                       => false,
					'package_types'               => array(),
					'zone_codes'                  => array(),
					'supported_country_count'     => 0,
					'has_additional_rates'        => false,
					'compensation_included_value' => 0,
					'compensation_max_value'      => 0,
					'maximum_total_cover'         => 0,
				);
				continue;
			}

			$summaries[] = array(
				'rate_type'                   => $rate_type,
				'has_rate_data'               => true,
				'taxed'                       => Rate_Data_Manager::is_taxed( $data ),
				'package_types'               => array_values( array_keys( isset( $data['packages'] ) && is_array( $data['packages'] ) ? $data['packages'] : array() ) ),
				'zone_codes'                  => array_values( array_keys( isset( $data['zones'] ) && is_array( $data['zones'] ) ? $data['zones'] : array() ) ),
				'supported_country_count'     => count( Rate_Data_Manager::get_supported_countries( $data ) ),
				'has_additional_rates'        => ! empty( Rate_Data_Manager::get_additional_rates( $data ) ),
				'compensation_included_value' => Rate_Data_Manager::get_compensation_included_value( $data ) / 100,
				'compensation_max_value'      => Rate_Data_Manager::get_compensation_up_to_value( $data ) / 100,
				'maximum_total_cover'         => Rate_Data_Manager::get_maximum_total_cover( $data ) / 100,
			);
		}

		return $summaries;
	}

	/**
	 * Check whether a service exists in a rate type.
	 *
	 * @param string $service_id Service ID.
	 * @param string $rate_type Rate type.
	 * @return bool
	 */
	private static function service_exists_in_rate_type( string $service_id, string $rate_type ): bool {
		foreach ( self::get_destination_types() as $destination_type ) {
			if ( self::service_exists_in_group( $service_id, $rate_type, $destination_type ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether a service exists in one rate/destination group.
	 *
	 * @param string $service_id Service ID.
	 * @param string $rate_type Rate type.
	 * @param string $destination_type Destination type.
	 * @return bool
	 */
	private static function service_exists_in_group( string $service_id, string $rate_type, string $destination_type ): bool {
		$services = WC_RoyalMail::get_all_services();
		return isset( $services[ $rate_type ][ $destination_type ] ) && in_array( $service_id, $services[ $rate_type ][ $destination_type ], true );
	}

	/**
	 * Load rate data from local JSON files without database cache side effects.
	 *
	 * @param string $service_id Service ID.
	 * @param string $rate_type Rate type.
	 * @return array|false
	 */
	private static function get_local_rate_data( string $service_id, string $rate_type ) {
		$current_rate_date = self::get_current_local_rate_date();
		if ( '' === $current_rate_date ) {
			return false;
		}

		$file_path = WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'rate-files-json/' . $current_rate_date . '/' . sanitize_key( $rate_type ) . '/' . sanitize_key( $service_id ) . '.json';
		if ( ! is_readable( $file_path ) ) {
			return false;
		}

		$contents = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $contents ) {
			return false;
		}

		$data = json_decode( $contents, true );
		if ( ! is_array( $data ) ) {
			return false;
		}

		return $data;
	}

	/**
	 * Get current local rate date.
	 *
	 * @return string
	 */
	private static function get_current_local_rate_date(): string {
		$rates_path = WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'rate-files-json/';
		if ( ! is_dir( $rates_path ) ) {
			return '';
		}

		$directories = scandir( $rates_path );
		if ( false === $directories ) {
			return '';
		}

		$current_date    = gmdate( 'Y-m-d' );
		$available_dates = array();

		foreach ( $directories as $directory ) {
			if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $directory ) && is_dir( $rates_path . $directory ) && $directory <= $current_date ) {
				$available_dates[] = $directory;
			}
		}

		if ( empty( $available_dates ) ) {
			return '';
		}

		rsort( $available_dates );
		return $available_dates[0];
	}

	/**
	 * Format boxes for ability output.
	 *
	 * @param array $boxes Boxes settings.
	 * @return array
	 */
	private static function format_boxes( array $boxes ): array {
		$formatted = array();

		foreach ( $boxes as $box ) {
			if ( ! is_array( $box ) ) {
				continue;
			}

			$formatted[] = array(
				'name'         => self::clean_text( $box['name'] ?? '' ),
				'inner_length' => (float) ( $box['inner_length'] ?? 0 ),
				'inner_width'  => (float) ( $box['inner_width'] ?? 0 ),
				'inner_height' => (float) ( $box['inner_height'] ?? 0 ),
				'box_weight'   => (float) ( $box['box_weight'] ?? 0 ),
			);
		}

		return $formatted;
	}

	/**
	 * Format custom service settings.
	 *
	 * @param array $services Service settings.
	 * @return array
	 */
	private static function format_custom_services( array $services ): array {
		$formatted = array();

		foreach ( $services as $service_id => $settings ) {
			if ( ! is_array( $settings ) || ! isset( WC_RoyalMail::$service_names[ $service_id ] ) ) {
				continue;
			}

			$formatted[] = array(
				'id'                 => (string) $service_id,
				'name'               => self::clean_text( $settings['name'] ?? '' ),
				'enabled'            => ! empty( $settings['enabled'] ),
				'order'              => absint( $settings['order'] ?? 0 ),
				'adjustment'         => (float) ( $settings['adjustment'] ?? 0 ),
				'adjustment_percent' => (float) ( $settings['adjustment_percent'] ?? 0 ),
			);
		}

		return $formatted;
	}

	/**
	 * Get enabled service IDs from formatted custom services.
	 *
	 * @param array $formatted_services Formatted services.
	 * @return array
	 */
	private static function get_enabled_service_ids( array $formatted_services ): array {
		if ( empty( $formatted_services ) ) {
			return array_values( array_keys( WC_RoyalMail::$service_names ) );
		}

		$enabled = array();
		foreach ( $formatted_services as $service ) {
			if ( ! empty( $service['enabled'] ) ) {
				$enabled[] = $service['id'];
			}
		}

		return $enabled;
	}

	/**
	 * Normalize yes/no settings to bool.
	 *
	 * @param mixed $value Value.
	 * @return bool
	 */
	private static function is_yes( $value ): bool {
		return true === $value || 1 === $value || '1' === $value || 'yes' === $value;
	}

	/**
	 * Clean a service label.
	 *
	 * @param string $label Label.
	 * @return string
	 */
	private static function clean_label( string $label ): string {
		$label = html_entity_decode( $label, ENT_QUOTES, 'UTF-8' );
		return self::clean_text( wp_strip_all_tags( $label ) );
	}

	/**
	 * Clean a text field.
	 *
	 * @param mixed $value Text value.
	 * @return string
	 */
	private static function clean_text( $value ): string {
		$value = is_scalar( $value ) ? (string) $value : '';
		return function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : trim( $value );
	}
}
