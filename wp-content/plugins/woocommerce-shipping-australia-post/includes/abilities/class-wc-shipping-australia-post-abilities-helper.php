<?php
/**
 * Australia Post Abilities helpers.
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shared read-only helpers for Australia Post abilities.
 */
class WC_Shipping_Australia_Post_Abilities_Helper {
	/**
	 * Shipping method ID.
	 */
	private const METHOD_ID = 'australia_post';

	/**
	 * Sensitive setting key fragments that must never be returned by abilities.
	 */
	private const SENSITIVE_KEY_FRAGMENTS = array(
		'api_key',
		'apikey',
		'password',
		'token',
		'secret',
		'oauth',
		'credential',
		'account',
		'meter',
		'customer',
		'contract',
		'username',
	);

	/**
	 * Check whether the current user can read shipping configuration.
	 *
	 * @return bool
	 */
	public static function can_manage_woocommerce(): bool {
		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability.
		return current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Return non-secret Australia Post shipping method configuration.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array|WP_Error
	 */
	public static function get_rate_configuration( array $input = array() ) {
		$instance_id      = isset( $input['instance_id'] ) ? absint( $input['instance_id'] ) : 0;
		$include_services = ! isset( $input['include_services'] ) || wc_string_to_bool( $input['include_services'] );
		$include_boxes    = ! isset( $input['include_boxes'] ) || wc_string_to_bool( $input['include_boxes'] );
		$instances        = self::get_method_instances( $instance_id, $include_services, $include_boxes );

		if ( $instance_id && empty( $instances ) ) {
			return new WP_Error(
				'woocommerce_shipping_australia_post_instance_not_found',
				__( 'No Australia Post shipping method instance was found for the requested instance ID.', 'woocommerce-shipping-australia-post' ),
				array( 'status' => 404 )
			);
		}

		return array(
			'method_id'       => self::METHOD_ID,
			'method_title'    => __( 'Australia Post', 'woocommerce-shipping-australia-post' ),
			'global_settings' => self::get_global_settings(),
			'environment'     => self::get_environment_status(),
			'instances'       => $instances,
		);
	}

	/**
	 * Return Australia Post service and package catalog data.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array
	 */
	public static function get_service_catalog( array $input = array() ): array {
		$scope                   = isset( $input['scope'] ) ? sanitize_key( $input['scope'] ) : 'all';
		$include_package_catalog = ! isset( $input['include_package_catalog'] ) || wc_string_to_bool( $input['include_package_catalog'] );

		if ( ! in_array( $scope, array( 'all', 'domestic', 'international' ), true ) ) {
			$scope = 'all';
		}

		$output = array(
			'method_id'       => self::METHOD_ID,
			'method_title'    => __( 'Australia Post', 'woocommerce-shipping-australia-post' ),
			'scope'           => $scope,
			'services'        => self::get_services( $scope ),
			'optional_extras' => self::get_optional_extras(),
		);

		if ( $include_package_catalog ) {
			$output['default_boxes'] = self::get_default_boxes();
			$output['letter_sizes']  = self::get_letter_sizes();
		}

		return $output;
	}

	/**
	 * Return method instances.
	 *
	 * @param int  $filter_instance_id Optional instance ID filter.
	 * @param bool $include_services    Include service settings.
	 * @param bool $include_boxes       Include box settings.
	 *
	 * @return array
	 */
	private static function get_method_instances( int $filter_instance_id = 0, bool $include_services = true, bool $include_boxes = true ): array {
		$instances = array();
		foreach ( self::get_method_contexts( $filter_instance_id ) as $context ) {
			$shipping_method = $context['method'];
			$instance_id     = absint( $context['instance_id'] );
			$settings        = self::get_instance_settings( $instance_id, $shipping_method );
			$excluding_tax   = wc_string_to_bool( self::get_setting( $settings, 'excluding_tax', 'no' ) );

			$instance = array(
				'instance_id'     => $instance_id,
				'zone_id'         => absint( $context['zone_id'] ),
				'zone_name'       => (string) $context['zone_name'],
				'method_order'    => absint( $context['method_order'] ),
				'enabled'         => (bool) $context['enabled'],
				'title'           => self::get_setting( $settings, 'title', __( 'Australia Post', 'woocommerce-shipping-australia-post' ) ),
				'origin_postcode' => self::get_setting( $settings, 'origin', '' ),
				'excluding_tax'   => $excluding_tax,
				'tax_status'      => $excluding_tax ? 'taxable' : 'none',
				'packing_method'  => self::get_setting( $settings, 'packing_method', 'per_item' ),
				'max_weight_kg'   => (float) self::get_setting( $settings, 'max_weight', '20' ),
				'satchel_rates'   => self::get_setting( $settings, 'satchel_rates', 'off' ),
				'offer_rates'     => self::get_setting( $settings, 'offer_rates', 'all' ),
			);

			if ( $include_services ) {
				$raw_services         = self::get_setting( $settings, 'services', array() );
				$instance['services'] = self::get_configured_services( is_array( $raw_services ) ? $raw_services : array() );
			}

			if ( $include_boxes ) {
				$raw_boxes         = self::get_setting( $settings, 'boxes', array() );
				$instance['boxes'] = self::get_configured_boxes( is_array( $raw_boxes ) ? $raw_boxes : array() );
			}

			$instances[] = $instance;
		}

		usort( $instances, array( __CLASS__, 'sort_method_instances' ) );

		return $instances;
	}

	/**
	 * Return Australia Post shipping method contexts from WooCommerce zones.
	 *
	 * @param int $filter_instance_id Optional instance ID filter.
	 *
	 * @return array
	 */
	private static function get_method_contexts( int $filter_instance_id = 0 ): array {
		if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
			return array();
		}

		$contexts = array();

		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			if ( ! is_array( $zone ) ) {
				continue;
			}

			self::append_zone_method_contexts(
				$contexts,
				isset( $zone['zone_id'] ) ? absint( $zone['zone_id'] ) : 0,
				isset( $zone['zone_name'] ) ? (string) $zone['zone_name'] : '',
				isset( $zone['shipping_methods'] ) && is_array( $zone['shipping_methods'] ) ? $zone['shipping_methods'] : array(),
				$filter_instance_id
			);
		}

		$rest_of_world = WC_Shipping_Zones::get_zone( 0 );
		if ( $rest_of_world ) {
			self::append_zone_method_contexts(
				$contexts,
				0,
				(string) $rest_of_world->get_zone_name(),
				$rest_of_world->get_shipping_methods( false ),
				$filter_instance_id
			);
		}

		return $contexts;
	}

	/**
	 * Append Australia Post methods from a shipping zone.
	 *
	 * @param array  $contexts           Method context accumulator.
	 * @param int    $zone_id            Shipping zone ID.
	 * @param string $zone_name          Shipping zone name.
	 * @param array  $shipping_methods   Zone shipping methods.
	 * @param int    $filter_instance_id Optional instance ID filter.
	 *
	 * @return void
	 */
	private static function append_zone_method_contexts( array &$contexts, int $zone_id, string $zone_name, array $shipping_methods, int $filter_instance_id = 0 ): void {
		foreach ( $shipping_methods as $shipping_method ) {
			if ( ! is_object( $shipping_method ) || ! isset( $shipping_method->id ) || self::METHOD_ID !== $shipping_method->id ) {
				continue;
			}

			$instance_id = isset( $shipping_method->instance_id ) ? absint( $shipping_method->instance_id ) : 0;
			if ( ! $instance_id || ( $filter_instance_id && $filter_instance_id !== $instance_id ) ) {
				continue;
			}

			$contexts[] = array(
				'instance_id'  => $instance_id,
				'zone_id'      => $zone_id,
				'zone_name'    => $zone_name,
				'method_order' => isset( $shipping_method->method_order ) ? absint( $shipping_method->method_order ) : 0,
				'enabled'      => is_callable( array( $shipping_method, 'is_enabled' ) )
					? (bool) $shipping_method->is_enabled()
					: ( ! isset( $shipping_method->enabled ) || wc_string_to_bool( $shipping_method->enabled ) ),
				'method'       => $shipping_method,
			);
		}
	}

	/**
	 * Sort method instances by the same stable fields previously returned by direct SQL.
	 *
	 * @param array $a First method instance.
	 * @param array $b Second method instance.
	 *
	 * @return int
	 */
	private static function sort_method_instances( array $a, array $b ): int {
		$a_sort = array( absint( $a['zone_id'] ), absint( $a['method_order'] ), absint( $a['instance_id'] ) );
		$b_sort = array( absint( $b['zone_id'] ), absint( $b['method_order'] ), absint( $b['instance_id'] ) );

		if ( $a_sort === $b_sort ) {
			return 0;
		}

		return $a_sort < $b_sort ? -1 : 1;
	}

	/**
	 * Return non-secret global settings.
	 *
	 * @return array
	 */
	private static function get_global_settings(): array {
		$settings = get_option( 'woocommerce_' . self::METHOD_ID . '_settings', array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$has_custom_api_key = ! empty( $settings['api_key'] );

		return array(
			'has_custom_api_key' => $has_custom_api_key,
			'debug_mode'         => wc_string_to_bool( self::get_setting( $settings, 'debug_mode', 'no' ) ),
			'box_packer_library' => self::get_setting( $settings, 'box_packer_library', self::get_default_box_packer_library() ),
		);
	}

	/**
	 * Return environment status relevant to Australia Post.
	 *
	 * @return array
	 */
	private static function get_environment_status(): array {
		$base_country = '';

		if ( function_exists( 'WC' ) ) {
			$base_country = WC()->countries->get_base_country();
		}

		$currency = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : '';

		return array(
			'shipping_enabled'                    => function_exists( 'wc_shipping_enabled' ) ? wc_shipping_enabled() : false,
			'store_currency'                      => $currency,
			'store_base_country'                  => $base_country,
			'requires_store_currency'             => 'AUD',
			'requires_store_base_country'         => 'AU',
			'meets_currency_requirement'          => 'AUD' === $currency,
			'meets_base_country_requirement'      => 'AU' === $base_country,
			'supported_domestic_country_codes'    => array( 'AU', 'CC', 'CX', 'HM', 'NF' ),
			'supports_international_destinations' => true,
		);
	}

	/**
	 * Return instance settings.
	 *
	 * @param int         $instance_id     Instance ID.
	 * @param object|null $shipping_method Shipping method instance.
	 *
	 * @return array
	 */
	private static function get_instance_settings( int $instance_id, $shipping_method = null ): array {
		if ( is_object( $shipping_method ) && isset( $shipping_method->instance_settings ) && is_array( $shipping_method->instance_settings ) ) {
			return self::remove_sensitive_settings( $shipping_method->instance_settings );
		}

		$settings = get_option( 'woocommerce_' . self::METHOD_ID . '_' . $instance_id . '_settings', array() );

		if ( ! is_array( $settings ) ) {
			return array();
		}

		return self::remove_sensitive_settings( $settings );
	}

	/**
	 * Return a safe setting value.
	 *
	 * @param array  $settings Settings.
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Default value.
	 *
	 * @return mixed
	 */
	private static function get_setting( array $settings, string $key, $fallback ) {
		if ( self::is_sensitive_key( $key ) ) {
			return $fallback;
		}

		return array_key_exists( $key, $settings ) ? $settings[ $key ] : $fallback;
	}

	/**
	 * Return configured services.
	 *
	 * @param array $configured_services Configured service settings.
	 *
	 * @return array
	 */
	private static function get_configured_services( array $configured_services ): array {
		$services = array();

		foreach ( self::get_services( 'all' ) as $service ) {
			$code     = $service['code'];
			$settings = isset( $configured_services[ $code ] ) && is_array( $configured_services[ $code ] ) ? $configured_services[ $code ] : array();
			$name     = self::get_setting( $settings, 'name', '' );

			$services[] = array(
				'code'                           => $code,
				'default_name'                   => $service['name'],
				'name'                           => '' !== $name ? $name : $service['name'],
				'has_custom_name'                => '' !== $name,
				'enabled'                        => ! array_key_exists( 'enabled', $settings ) || wc_string_to_bool( $settings['enabled'] ),
				'order'                          => self::get_nullable_integer_setting( $settings, 'order' ),
				'adjustment'                     => self::get_nullable_float_setting( $settings, 'adjustment' ),
				'adjustment_percent'             => self::get_nullable_float_setting( $settings, 'adjustment_percent' ),
				'extra_cover_enabled'            => ! empty( $settings['extra_cover'] ),
				'delivery_confirmation_enabled'  => ! empty( $settings['delivery_confirmation'] ),
				'supports_extra_cover'           => $service['supports_extra_cover'],
				'supports_delivery_confirmation' => $service['supports_signature_on_delivery'],
				'alternate_service_codes'        => $service['alternate_service_codes'],
			);
		}

		return $services;
	}

	/**
	 * Return service catalog.
	 *
	 * @param string $scope Service scope.
	 *
	 * @return array
	 */
	private static function get_services( string $scope ): array {
		$service_definitions = self::include_data_file( 'data-services.php' );
		$extra_cover         = self::include_data_file( 'data-extra-cover.php' );
		$sod_services        = self::include_data_file( 'data-sod.php' );
		$services            = array();

		foreach ( $service_definitions as $code => $service ) {
			$service_scope = 0 === strpos( $code, 'AUS_' ) ? 'domestic' : 'international';

			if ( 'all' !== $scope && $scope !== $service_scope ) {
				continue;
			}

			$services[] = array(
				'code'                           => $code,
				'name'                           => isset( $service['name'] ) ? $service['name'] : $code,
				'scope'                          => $service_scope,
				'image_path'                     => isset( $service['image'] ) ? $service['image'] : '',
				'alternate_service_codes'        => isset( $service['alternate_services'] ) && is_array( $service['alternate_services'] ) ? array_values( $service['alternate_services'] ) : array(),
				'supports_extra_cover'           => array_key_exists( $code, $extra_cover ),
				'max_extra_cover'                => array_key_exists( $code, $extra_cover ) ? (float) $extra_cover[ $code ] : null,
				'supports_signature_on_delivery' => in_array( $code, $sod_services, true ),
			);
		}

		return $services;
	}

	/**
	 * Return optional extras catalog.
	 *
	 * @return array
	 */
	private static function get_optional_extras(): array {
		$extra_cover  = self::include_data_file( 'data-extra-cover.php' );
		$sod_services = self::include_data_file( 'data-sod.php' );

		return array(
			'extra_cover_service_limits'     => array_map( 'floatval', $extra_cover ),
			'signature_on_delivery_services' => array_values( $sod_services ),
		);
	}

	/**
	 * Return default configured boxes merged with saved box enablement and custom boxes.
	 *
	 * @param array $configured_boxes Configured boxes.
	 *
	 * @return array
	 */
	private static function get_configured_boxes( array $configured_boxes ): array {
		$enabled_default_boxes = array();
		$custom_boxes          = array();

		foreach ( $configured_boxes as $box ) {
			if ( ! is_array( $box ) ) {
				continue;
			}

			if ( isset( $box['id'] ) && isset( $box['enabled'] ) && count( $box ) <= 2 ) {
				$enabled_default_boxes[ $box['id'] ] = wc_string_to_bool( $box['enabled'] );
				continue;
			}

			$custom_boxes[] = self::format_box( $box, 'custom' );
		}

		$boxes = array();
		foreach ( self::get_default_boxes() as $box ) {
			if ( isset( $enabled_default_boxes[ $box['id'] ] ) ) {
				$box['enabled'] = $enabled_default_boxes[ $box['id'] ];
			} else {
				$box['enabled'] = true;
			}
			$boxes[] = $box;
		}

		return array_merge( $boxes, $custom_boxes );
	}

	/**
	 * Return default box catalog.
	 *
	 * @return array
	 */
	private static function get_default_boxes(): array {
		$boxes = array();

		foreach ( self::include_data_file( 'data-box-sizes.php' ) as $box ) {
			$boxes[] = self::format_box( $box, 'default' );
		}

		return $boxes;
	}

	/**
	 * Return letter size catalog.
	 *
	 * @return array
	 */
	private static function get_letter_sizes(): array {
		$letter_sizes = array();

		foreach ( self::include_data_file( 'data-letter-sizes.php' ) as $code => $letter_size ) {
			$letter_sizes[] = array(
				'code'         => $code,
				'name'         => isset( $letter_size['name'] ) ? $letter_size['name'] : $code,
				'width_mm'     => isset( $letter_size['width'] ) ? (float) $letter_size['width'] : null,
				'length_mm'    => isset( $letter_size['length'] ) ? (float) $letter_size['length'] : null,
				'thickness_mm' => isset( $letter_size['thickness'] ) ? (float) $letter_size['thickness'] : null,
			);
		}

		return $letter_sizes;
	}

	/**
	 * Format a box for ability output.
	 *
	 * @param array  $box    Box settings.
	 * @param string $source Box source.
	 *
	 * @return array
	 */
	private static function format_box( array $box, string $source ): array {
		return array(
			'id'              => isset( $box['id'] ) ? (string) $box['id'] : '',
			'name'            => isset( $box['name'] ) ? (string) $box['name'] : '',
			'source'          => $source,
			'type'            => isset( $box['type'] ) ? (string) $box['type'] : 'box',
			'enabled'         => ! array_key_exists( 'enabled', $box ) || wc_string_to_bool( $box['enabled'] ),
			'max_weight_kg'   => isset( $box['max_weight'] ) ? (float) $box['max_weight'] : null,
			'box_weight_kg'   => isset( $box['box_weight'] ) ? (float) $box['box_weight'] : null,
			'outer_length_cm' => isset( $box['outer_length'] ) ? (float) $box['outer_length'] : null,
			'outer_width_cm'  => isset( $box['outer_width'] ) ? (float) $box['outer_width'] : null,
			'outer_height_cm' => isset( $box['outer_height'] ) ? (float) $box['outer_height'] : null,
			'inner_length_cm' => isset( $box['inner_length'] ) ? (float) $box['inner_length'] : null,
			'inner_width_cm'  => isset( $box['inner_width'] ) ? (float) $box['inner_width'] : null,
			'inner_height_cm' => isset( $box['inner_height'] ) ? (float) $box['inner_height'] : null,
		);
	}

	/**
	 * Return nullable integer setting.
	 *
	 * @param array  $settings Settings.
	 * @param string $key      Setting key.
	 *
	 * @return int|null
	 */
	private static function get_nullable_integer_setting( array $settings, string $key ) {
		if ( ! isset( $settings[ $key ] ) || '' === $settings[ $key ] ) {
			return null;
		}

		return absint( $settings[ $key ] );
	}

	/**
	 * Return nullable float setting.
	 *
	 * @param array  $settings Settings.
	 * @param string $key      Setting key.
	 *
	 * @return float|null
	 */
	private static function get_nullable_float_setting( array $settings, string $key ) {
		if ( ! isset( $settings[ $key ] ) || '' === $settings[ $key ] ) {
			return null;
		}

		return (float) $settings[ $key ];
	}

	/**
	 * Return default box packer library.
	 *
	 * @return string
	 */
	private static function get_default_box_packer_library(): string {
		return ! empty( self::get_method_contexts() ) ? 'original' : 'dvdoug';
	}

	/**
	 * Include an Australia Post data file.
	 *
	 * @param string $file File name.
	 *
	 * @return array
	 */
	private static function include_data_file( string $file ): array {
		$data = include WC_SHIPPING_AUSTRALIA_POST_ABSPATH . 'includes/data/' . $file;

		return is_array( $data ) ? $data : array();
	}

	/**
	 * Remove sensitive settings recursively.
	 *
	 * @param array $settings Settings.
	 *
	 * @return array
	 */
	private static function remove_sensitive_settings( array $settings ): array {
		foreach ( $settings as $key => $value ) {
			if ( self::is_sensitive_key( (string) $key ) ) {
				unset( $settings[ $key ] );
				continue;
			}

			if ( is_array( $value ) ) {
				$settings[ $key ] = self::remove_sensitive_settings( $value );
			}
		}

		return $settings;
	}

	/**
	 * Check whether a setting key is sensitive.
	 *
	 * @param string $key Setting key.
	 *
	 * @return bool
	 */
	private static function is_sensitive_key( string $key ): bool {
		$normalized_key = strtolower( str_replace( array( '-', ' ' ), '_', $key ) );

		foreach ( self::SENSITIVE_KEY_FRAGMENTS as $fragment ) {
			if ( false !== strpos( $normalized_key, $fragment ) ) {
				return true;
			}
		}

		return false;
	}
}
