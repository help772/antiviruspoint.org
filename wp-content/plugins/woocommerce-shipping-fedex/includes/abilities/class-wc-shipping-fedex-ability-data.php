<?php
/**
 * Shared data helpers for FedEx abilities.
 *
 * @package WC_Shipping_Fedex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds credential-safe FedEx ability responses from WooCommerce settings.
 */
class WC_Shipping_Fedex_Ability_Data {

	/**
	 * Checks whether the current user can read FedEx settings.
	 *
	 * @return bool
	 */
	public static function can_read_settings() {
		if ( function_exists( 'wc_rest_check_manager_permissions' ) ) {
			return wc_rest_check_manager_permissions( 'settings', 'read' );
		}

		return current_user_can( 'manage_woocommerce' ); // phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability.
	}

	/**
	 * Returns FedEx method instances matching ability input.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function get_shipping_method_instances_for_input( $input ) {
		$input       = is_array( $input ) ? $input : array();
		$instance_id = ! empty( $input['instance_id'] ) ? absint( $input['instance_id'] ) : 0;

		if ( $instance_id ) {
			$instance = self::get_shipping_method_instance( $instance_id );

			if ( ! $instance ) {
				return new WP_Error(
					'woocommerce_shipping_fedex_invalid_instance',
					__( 'FedEx shipping method instance not found.', 'woocommerce-shipping-fedex' ),
					array( 'status' => 404 )
				);
			}

			return array( $instance );
		}

		return self::get_shipping_method_instances();
	}

	/**
	 * Returns the shared instance filter input schema.
	 *
	 * @return array
	 */
	public static function get_instance_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'instance_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Optional FedEx shipping method instance ID. Omit to return all FedEx instances.', 'woocommerce-shipping-fedex' ),
				),
			),
			'additionalProperties' => false,
			'default'              => array(),
		);
	}

	/**
	 * Returns the connection status store environment schema.
	 *
	 * @return array
	 */
	public static function get_connection_store_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'currency'                        => array( 'type' => 'string' ),
				'base_country'                    => array( 'type' => 'string' ),
				'currency_supported'              => array( 'type' => 'boolean' ),
				'base_country_supported'          => array( 'type' => 'boolean' ),
				'soap_client_available'           => array( 'type' => 'boolean' ),
				'fedex_shipping_instances_count'  => array( 'type' => 'integer' ),
				'legacy_global_settings_detected' => array( 'type' => 'boolean' ),
			),
			'required'             => array( 'currency', 'base_country', 'currency_supported', 'base_country_supported', 'soap_client_available', 'fedex_shipping_instances_count', 'legacy_global_settings_detected' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the rate configuration store environment schema.
	 *
	 * @return array
	 */
	public static function get_rate_store_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'currency'               => array( 'type' => 'string' ),
				'base_country'           => array( 'type' => 'string' ),
				'currency_supported'     => array( 'type' => 'boolean' ),
				'base_country_supported' => array( 'type' => 'boolean' ),
			),
			'required'             => array( 'currency', 'base_country', 'currency_supported', 'base_country_supported' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns metadata for read-only, externally visible FedEx abilities.
	 *
	 * @return array
	 */
	public static function get_read_only_meta() {
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
	 * Returns store-level FedEx connection environment information.
	 *
	 * @return array
	 */
	public static function get_connection_store_environment() {
		$base_country = self::get_base_country();
		$currency     = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : '';

		return array(
			'currency'                        => (string) $currency,
			'base_country'                    => (string) $base_country,
			'currency_supported'              => in_array( $currency, array( 'USD', 'CAD' ), true ),
			'base_country_supported'          => in_array( $base_country, array( 'US', 'CA' ), true ),
			'soap_client_available'           => class_exists( 'SoapClient' ),
			'fedex_shipping_instances_count'  => count( self::get_shipping_method_instances() ),
			'legacy_global_settings_detected' => is_array( get_option( 'woocommerce_fedex_settings', false ) ),
		);
	}

	/**
	 * Returns store-level FedEx rate environment information.
	 *
	 * @return array
	 */
	public static function get_rate_store_environment() {
		$base_country = self::get_base_country();
		$currency     = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : '';

		return array(
			'currency'               => (string) $currency,
			'base_country'           => (string) $base_country,
			'currency_supported'     => in_array( $currency, array( 'USD', 'CAD' ), true ),
			'base_country_supported' => in_array( $base_country, array( 'US', 'CA' ), true ),
		);
	}

	/**
	 * Returns FedEx method instance rows.
	 *
	 * @return array
	 */
	public static function get_shipping_method_instances() {
		if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
			return array();
		}

		$instances = array();
		$zones     = WC_Shipping_Zones::get_zones();

		foreach ( $zones as $zone ) {
			self::add_zone_instances(
				$instances,
				isset( $zone['zone_id'] ) ? absint( $zone['zone_id'] ) : 0,
				isset( $zone['zone_name'] ) ? (string) $zone['zone_name'] : '',
				isset( $zone['shipping_methods'] ) && is_array( $zone['shipping_methods'] ) ? $zone['shipping_methods'] : array()
			);
		}

		$rest_of_world = WC_Shipping_Zones::get_zone( 0 );

		if ( $rest_of_world && is_callable( array( $rest_of_world, 'get_shipping_methods' ) ) ) {
			self::add_zone_instances(
				$instances,
				0,
				is_callable( array( $rest_of_world, 'get_zone_name' ) ) ? (string) $rest_of_world->get_zone_name() : __( 'Locations not covered by your other zones', 'woocommerce' ),
				$rest_of_world->get_shipping_methods( false )
			);
		}

		usort( $instances, array( __CLASS__, 'sort_instances' ) );

		return $instances;
	}

	/**
	 * Adds FedEx method instances from a shipping zone.
	 *
	 * @param array  $instances        Instance list.
	 * @param int    $zone_id          Shipping zone ID.
	 * @param string $zone_name        Shipping zone name.
	 * @param array  $shipping_methods Zone shipping methods.
	 * @return void
	 */
	private static function add_zone_instances( &$instances, $zone_id, $zone_name, $shipping_methods ) {
		foreach ( $shipping_methods as $method ) {
			if ( ! is_object( $method ) || ! isset( $method->id ) || 'fedex' !== $method->id ) {
				continue;
			}

			$instance_id = isset( $method->instance_id ) ? absint( $method->instance_id ) : 0;

			if ( ! $instance_id ) {
				continue;
			}

			$instances[] = array(
				'instance_id'  => $instance_id,
				'zone_id'      => absint( $zone_id ),
				'zone_name'    => (string) $zone_name,
				'method_order' => isset( $method->method_order ) ? absint( $method->method_order ) : 0,
				'enabled'      => is_callable( array( $method, 'is_enabled' ) ) ? (bool) $method->is_enabled() : ( isset( $method->enabled ) && 'yes' === $method->enabled ),
				'settings'     => self::get_instance_settings( $instance_id ),
			);
		}
	}

	/**
	 * Sorts FedEx method instances by zone, method order, and instance ID.
	 *
	 * @param array $a First instance.
	 * @param array $b Second instance.
	 * @return int
	 */
	private static function sort_instances( $a, $b ) {
		$a_sort = array( absint( $a['zone_id'] ), absint( $a['method_order'] ), absint( $a['instance_id'] ) );
		$b_sort = array( absint( $b['zone_id'] ), absint( $b['method_order'] ), absint( $b['instance_id'] ) );

		if ( $a_sort === $b_sort ) {
			return 0;
		}

		return $a_sort < $b_sort ? -1 : 1;
	}

	/**
	 * Returns one FedEx method instance row.
	 *
	 * @param int $instance_id Shipping method instance ID.
	 * @return array|false
	 */
	public static function get_shipping_method_instance( $instance_id ) {
		foreach ( self::get_shipping_method_instances() as $instance ) {
			if ( absint( $instance['instance_id'] ) === absint( $instance_id ) ) {
				return $instance;
			}
		}

		return false;
	}

	/**
	 * Returns connection status for one method instance.
	 *
	 * @param array $instance FedEx method instance data.
	 * @return array
	 */
	public static function format_connection_status( $instance ) {
		$settings             = isset( $instance['settings'] ) && is_array( $instance['settings'] ) ? $instance['settings'] : array();
		$api_type             = self::get_api_type( $settings );
		$has_rest_credentials = self::has_all_settings( $settings, array( 'client_id', 'client_secret', 'account_number' ) );
		$has_soap_credentials = self::has_all_settings( $settings, array( 'api_key', 'api_pass', 'account_number', 'meter_number' ) );
		$has_credentials      = 'soap' === $api_type ? $has_soap_credentials : $has_rest_credentials;

		return array(
			'instance_id'           => absint( $instance['instance_id'] ),
			'zone_id'               => absint( $instance['zone_id'] ),
			'zone_name'             => (string) $instance['zone_name'],
			'enabled'               => (bool) $instance['enabled'],
			'title'                 => self::get_setting_string( $settings, 'title', 'FedEx' ),
			'api_type'              => $api_type,
			'api_mode'              => self::get_api_mode( $settings ),
			'has_credentials'       => $has_credentials,
			'origin_configured'     => self::has_setting( $settings, 'origin' ),
			'missing_requirements'  => self::get_missing_connection_requirements( $settings, $api_type ),
			'freight_enabled'       => self::get_setting_bool( $settings, 'freight_enabled', false ),
			'soap_client_available' => class_exists( 'SoapClient' ),
		);
	}

	/**
	 * Returns rate configuration for one method instance.
	 *
	 * @param array $instance FedEx method instance data.
	 * @return array
	 */
	public static function format_rate_configuration( $instance ) {
		$settings = isset( $instance['settings'] ) && is_array( $instance['settings'] ) ? $instance['settings'] : array();

		return array(
			'instance_id'              => absint( $instance['instance_id'] ),
			'zone_id'                  => absint( $instance['zone_id'] ),
			'zone_name'                => (string) $instance['zone_name'],
			'enabled'                  => (bool) $instance['enabled'],
			'title'                    => self::get_setting_string( $settings, 'title', 'FedEx' ),
			'api_type'                 => self::get_api_type( $settings ),
			'api_mode'                 => self::get_api_mode( $settings ),
			'packing_method'           => self::get_setting_string( $settings, 'packing_method', 'per_item' ),
			'box_packer_library'       => self::get_setting_string( $settings, 'box_packer_library', self::get_default_box_packer_library() ),
			'offer_rates'              => self::get_setting_string( $settings, 'offer_rates', 'all' ),
			'request_type'             => self::get_setting_string( $settings, 'request_type', 'LIST' ),
			'residential_default'      => self::get_setting_bool( $settings, 'residential', true ),
			'insurance_enabled'        => self::get_setting_bool( $settings, 'insure_contents', true ),
			'fedex_one_rate_enabled'   => self::get_setting_bool( $settings, 'fedex_one_rate', false ) && 'US' === self::get_base_country(),
			'freight_enabled'          => self::get_setting_bool( $settings, 'freight_enabled', false ),
			'smartpost_hub_configured' => self::has_setting( $settings, 'smartpost_hub' ),
			'services'                 => self::get_services( $settings ),
			'packages'                 => self::get_package_summary( $settings ),
		);
	}

	/**
	 * Returns service configuration.
	 *
	 * @param array $settings Method settings.
	 * @return array
	 */
	private static function get_services( $settings ) {
		$service_labels     = self::get_service_labels();
		$ground_services    = include WC_SHIPPING_FEDEX_ABSPATH . 'includes/data/data-ground-services.php';
		$smartpost_services = include WC_SHIPPING_FEDEX_ABSPATH . 'includes/data/data-smartpost-services.php';
		$freight_services   = include WC_SHIPPING_FEDEX_ABSPATH . 'includes/data/data-freight-services.php';
		$custom_services    = isset( $settings['services'] ) && is_array( $settings['services'] ) ? $settings['services'] : array();
		$ordered_services   = array();
		$sort               = 0;

		foreach ( $service_labels as $code => $label ) {
			if ( isset( $custom_services[ $code ]['order'] ) && is_numeric( $custom_services[ $code ]['order'] ) ) {
				$sort = absint( $custom_services[ $code ]['order'] );
			}

			while ( isset( $ordered_services[ $sort ] ) ) {
				++$sort;
			}

			$ordered_services[ $sort ] = array(
				'code'  => (string) $code,
				'label' => (string) $label,
			);

			++$sort;
		}

		ksort( $ordered_services );

		$services = array();

		foreach ( $ordered_services as $order => $service ) {
			$code             = $service['code'];
			$custom           = isset( $custom_services[ $code ] ) && is_array( $custom_services[ $code ] ) ? $custom_services[ $code ] : array();
			$custom_name      = isset( $custom['name'] ) ? wc_clean( $custom['name'] ) : '';
			$enabled          = ! isset( $custom['enabled'] ) || ! empty( $custom['enabled'] );
			$service_groups   = array();
			$service_groups[] = in_array( $code, $ground_services, true ) ? 'ground' : '';
			$service_groups[] = in_array( $code, $smartpost_services, true ) ? 'smartpost' : '';
			$service_groups[] = in_array( $code, $freight_services, true ) ? 'freight' : '';
			$service_groups[] = false !== strpos( $code, 'INTERNATIONAL' ) ? 'international' : '';

			$services[] = array(
				'code'               => $code,
				'default_name'       => wp_specialchars_decode( (string) $service['label'], ENT_QUOTES ),
				'display_name'       => '' !== $custom_name ? $custom_name : wp_specialchars_decode( (string) $service['label'], ENT_QUOTES ),
				'enabled'            => (bool) $enabled,
				'adjustment'         => isset( $custom['adjustment'] ) ? wc_clean( $custom['adjustment'] ) : '',
				'adjustment_percent' => isset( $custom['adjustment_percent'] ) ? wc_clean( $custom['adjustment_percent'] ) : '',
				'groups'             => array_values( array_filter( $service_groups ) ),
			);
		}

		return $services;
	}

	/**
	 * Returns FedEx service labels with the same country-specific naming as the shipping method.
	 *
	 * @return array
	 */
	private static function get_service_labels() {
		$service_label_context = new class( self::get_base_country() ) {
			/**
			 * Store base country code.
			 *
			 * @var string
			 */
			private $base_country;

			/**
			 * Constructor.
			 *
			 * @param string $base_country Store base country code.
			 */
			public function __construct( $base_country ) {
				$this->base_country = (string) $base_country;
			}

			/**
			 * Returns the store base country.
			 *
			 * @return string
			 */
			public function get_base_country() {
				return $this->base_country;
			}

			/**
			 * Includes the FedEx service label source with a shipping-method-like context.
			 *
			 * @return array
			 */
			public function get_service_labels() {
				$service_labels = include WC_SHIPPING_FEDEX_ABSPATH . 'includes/data/data-service-codes.php';

				return is_array( $service_labels ) ? $service_labels : array();
			}
		};

		$service_labels = $service_label_context->get_service_labels();

		return is_array( $service_labels ) ? $service_labels : array();
	}

	/**
	 * Returns package configuration summary without exposing full box dimensions.
	 *
	 * @param array $settings Method settings.
	 * @return array
	 */
	private static function get_package_summary( $settings ) {
		$default_boxes           = include WC_SHIPPING_FEDEX_ABSPATH . 'includes/data/data-box-sizes.php';
		$saved_boxes             = isset( $settings['boxes'] ) && is_array( $settings['boxes'] ) ? $settings['boxes'] : array();
		$enabled_default_box_ids = array();
		$custom_boxes_count      = 0;
		$enabled_custom_count    = 0;

		foreach ( $default_boxes as $box ) {
			$box_id = isset( $box['id'] ) ? (string) $box['id'] : '';

			if ( '' === $box_id ) {
				continue;
			}

			if ( ! isset( $saved_boxes[ $box_id ]['enabled'] ) || ! empty( $saved_boxes[ $box_id ]['enabled'] ) ) {
				$enabled_default_box_ids[] = $box_id;
			}
		}

		foreach ( $saved_boxes as $key => $box ) {
			if ( ! is_numeric( $key ) || ! is_array( $box ) ) {
				continue;
			}

			++$custom_boxes_count;

			if ( ! empty( $box['enabled'] ) ) {
				++$enabled_custom_count;
			}
		}

		return array(
			'default_boxes_count'      => count( $default_boxes ),
			'enabled_default_box_ids'  => $enabled_default_box_ids,
			'custom_boxes_count'       => $custom_boxes_count,
			'enabled_custom_box_count' => $enabled_custom_count,
		);
	}

	/**
	 * Returns the settings array for a method instance.
	 *
	 * @param int $instance_id Shipping method instance ID.
	 * @return array
	 */
	private static function get_instance_settings( $instance_id ) {
		$settings = get_option( 'woocommerce_fedex_' . absint( $instance_id ) . '_settings', array() );

		return is_array( $settings ) ? $settings : array();
	}

	/**
	 * Returns the store base country.
	 *
	 * @return string
	 */
	private static function get_base_country() {
		if ( function_exists( 'WC' ) && WC() && isset( WC()->countries ) && is_callable( array( WC()->countries, 'get_base_country' ) ) ) {
			return (string) WC()->countries->get_base_country();
		}

		return '';
	}

	/**
	 * Returns the configured API type.
	 *
	 * @param array $settings Method settings.
	 * @return string
	 */
	private static function get_api_type( $settings ) {
		if ( isset( $settings['api_type'] ) && in_array( $settings['api_type'], array( 'rest', 'soap' ), true ) ) {
			return $settings['api_type'];
		}

		return self::has_setting( $settings, 'api_key' ) ? 'soap' : 'rest';
	}

	/**
	 * Returns the configured API mode.
	 *
	 * @param array $settings Method settings.
	 * @return string
	 */
	private static function get_api_mode( $settings ) {
		return self::get_setting_bool( $settings, 'production', false ) ? 'production' : 'test';
	}

	/**
	 * Returns the default box packer library.
	 *
	 * @return string
	 */
	private static function get_default_box_packer_library() {
		return count( self::get_shipping_method_instances() ) > 0 ? 'original' : 'dvdoug';
	}

	/**
	 * Returns missing setup requirements for the active FedEx API type.
	 *
	 * @param array  $settings Method settings.
	 * @param string $api_type Active API type.
	 * @return array
	 */
	private static function get_missing_connection_requirements( $settings, $api_type ) {
		$requirements = 'soap' === $api_type
			? array(
				'api_key'        => 'web_services_key',
				'api_pass'       => 'web_services_password',
				'account_number' => 'shipping_account_number',
				'meter_number'   => 'meter_number',
			)
			: array(
				'client_id'      => 'rest_api_key',
				'client_secret'  => 'rest_api_secret',
				'account_number' => 'shipping_account_number',
			);
		$missing      = array();

		foreach ( $requirements as $setting_key => $requirement ) {
			if ( ! self::has_setting( $settings, $setting_key ) ) {
				$missing[] = $requirement;
			}
		}

		if ( ! self::has_setting( $settings, 'origin' ) ) {
			$missing[] = 'origin_postcode';
		}

		if ( 'soap' === $api_type && ! class_exists( 'SoapClient' ) ) {
			$missing[] = 'soap_client';
		}

		return $missing;
	}

	/**
	 * Checks whether all settings are populated.
	 *
	 * @param array $settings Method settings.
	 * @param array $keys     Setting keys.
	 * @return bool
	 */
	private static function has_all_settings( $settings, $keys ) {
		foreach ( $keys as $key ) {
			if ( ! self::has_setting( $settings, $key ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks whether a setting is populated.
	 *
	 * @param array  $settings Method settings.
	 * @param string $key      Setting key.
	 * @return bool
	 */
	private static function has_setting( $settings, $key ) {
		return isset( $settings[ $key ] ) && '' !== trim( (string) $settings[ $key ] );
	}

	/**
	 * Reads a boolean yes/no setting.
	 *
	 * @param array  $settings      Method settings.
	 * @param string $key           Setting key.
	 * @param bool   $default_value Default value.
	 * @return bool
	 */
	private static function get_setting_bool( $settings, $key, $default_value ) {
		if ( ! isset( $settings[ $key ] ) || '' === $settings[ $key ] ) {
			return (bool) $default_value;
		}

		return 'yes' === $settings[ $key ] || true === $settings[ $key ];
	}

	/**
	 * Reads a string setting.
	 *
	 * @param array  $settings      Method settings.
	 * @param string $key           Setting key.
	 * @param string $default_value Default value.
	 * @return string
	 */
	private static function get_setting_string( $settings, $key, $default_value ) {
		if ( ! isset( $settings[ $key ] ) || '' === $settings[ $key ] ) {
			return (string) $default_value;
		}

		return wc_clean( $settings[ $key ] );
	}
}
