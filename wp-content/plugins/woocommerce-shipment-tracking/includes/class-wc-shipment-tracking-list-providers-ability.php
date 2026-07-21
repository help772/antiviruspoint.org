<?php
/**
 * WC_Shipment_Tracking_List_Providers_Ability class file.
 *
 * @package WC_Shipment_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipment Tracking provider list ability definition.
 */
class WC_Shipment_Tracking_List_Providers_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipment-tracking/list-providers';

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
			'label'               => __( 'List shipment tracking providers', 'woocommerce-shipment-tracking' ),
			'description'         => __( 'Retrieve configured shipment tracking providers, optionally filtered by country or search text.', 'woocommerce-shipment-tracking' ),
			'category'            => self::CATEGORY,
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read_providers' ),
			'meta'                => self::get_read_only_meta(),
		);
	}

	/**
	 * Check whether the current request can read provider definitions.
	 *
	 * Provider definitions are already exposed by the extension's public REST
	 * provider endpoint and do not include order/customer tracking data.
	 *
	 * @param mixed $input Ability input.
	 *
	 * @return bool|\WP_Error
	 */
	public static function can_read_providers( $input = null ) {
		return true;
	}

	/**
	 * Execute the provider list ability.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array
	 */
	public static function execute( array $input ) {
		$country_filter = isset( $input['country'] ) && is_scalar( $input['country'] ) ? wc_clean( (string) $input['country'] ) : '';
		$search_filter  = isset( $input['search'] ) && is_scalar( $input['search'] ) ? wc_clean( (string) $input['search'] ) : '';
		$providers      = WC_Shipment_Tracking_Actions::get_instance()->get_providers();
		$items          = array();

		foreach ( $providers as $country => $country_providers ) {
			if ( '' !== $country_filter && strtolower( $country_filter ) !== strtolower( (string) $country ) ) {
				continue;
			}

			foreach ( $country_providers as $provider_name => $tracking_url_template ) {
				$haystack = strtolower( (string) $country . ' ' . (string) $provider_name );

				if ( '' !== $search_filter && false === strpos( $haystack, strtolower( $search_filter ) ) ) {
					continue;
				}

				$items[] = array(
					'country'               => (string) $country,
					'name'                  => (string) $provider_name,
					'tracking_url_template' => (string) $tracking_url_template,
				);
			}
		}

		return array(
			'providers' => $items,
			'count'     => count( $items ),
		);
	}

	/**
	 * Read-only metadata for externally visible abilities.
	 *
	 * @return array
	 */
	private static function get_read_only_meta() {
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
	 * Get the input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'country' => array(
					'type'        => 'string',
					'description' => __( 'Exact provider country/group label to return.', 'woocommerce-shipment-tracking' ),
				),
				'search'  => array(
					'type'        => 'string',
					'description' => __( 'Search text matched against provider and country names.', 'woocommerce-shipment-tracking' ),
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
				'providers' => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'country'               => array( 'type' => 'string' ),
							'name'                  => array( 'type' => 'string' ),
							'tracking_url_template' => array(
								'type'        => 'string',
								'description' => __( 'Provider URL template with placeholders for tracking number and, when required, postcode.', 'woocommerce-shipment-tracking' ),
							),
						),
						'additionalProperties' => false,
					),
				),
				'count'     => array(
					'type'        => 'integer',
					'description' => __( 'Number of providers returned after applying country and search filters.', 'woocommerce-shipment-tracking' ),
				),
			),
			'additionalProperties' => false,
		);
	}
}
