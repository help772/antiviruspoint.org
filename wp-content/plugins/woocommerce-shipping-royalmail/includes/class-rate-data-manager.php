<?php
/**
 * Rate Data Manager.
 *
 * Handles transformation of JSON rate data to the format expected by rate classes.
 *
 * @since 4.0.0
 * @package WC_Shipping_Royalmail
 */

namespace WooCommerce\RoyalMail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rate Data Manager class.
 *
 * Provides methods to transform JSON rate data from local JSON files into the format
 * expected by Royal Mail rate classes. Handles various rate structures
 * including zone-based, package-based, and direct rates.
 */
class Rate_Data_Manager {
	/**
	 * Transform JSON rate data to bands format.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return array Transformed bands data.
	 */
	public static function transform_to_bands( array $json_data ): array {
		if ( empty( $json_data ) ) {
			return array();
		}

		if ( isset( $json_data['compensation'] ) && is_array( $json_data['compensation'] ) ) {
			return $json_data['compensation'];
		}

		if ( isset( $json_data['zones'] ) && is_array( $json_data['zones'] ) ) {
			return $json_data['zones'];
		}

		if ( isset( $json_data['packages'] ) && is_array( $json_data['packages'] ) ) {
			return $json_data['packages'];
		}

		return array();
	}

	/**
	 * Check if service is taxed.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return bool Whether service is taxed.
	 */
	public static function is_taxed( array $json_data ): bool {
		return (bool) ( $json_data['taxed'] ?? false );
	}

	/**
	 * Get compensation up to value from JSON.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return int Compensation up to value in pence.
	 */
	public static function get_compensation_up_to_value( array $json_data ): int {
		return isset( $json_data['addons']['compensation']['max'] ) ? absint( $json_data['addons']['compensation']['max'] ) : 0;
	}

	/**
	 * Get compensation included value from JSON.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return int Compensation included value in pence.
	 */
	public static function get_compensation_included_value( array $json_data ): int {
		return isset( $json_data['addons']['compensation']['included'] ) ? absint( $json_data['addons']['compensation']['included'] ) : 0;
	}

	/**
	 * Get additional compensation fees from JSON.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return int Additional compensation fees in pence.
	 */
	public static function get_compensation_fees( array $json_data ): int {
		return isset( $json_data['addons']['compensation']['fees'] ) ? absint( $json_data['addons']['compensation']['fees'] ) : 0;
	}

	/**
	 * Get maximum inclusive compensation from JSON.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return int Maximum inclusive compensation in pence.
	 */
	public static function get_maximum_inclusive_compensation( array $json_data ): int {
		return isset( $json_data['limits']['maximum_inclusive_compensation'] ) ? absint( $json_data['limits']['maximum_inclusive_compensation'] ) : 0;
	}

	/**
	 * Get maximum total cover from JSON.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return int Maximum total cover in pence.
	 */
	public static function get_maximum_total_cover( array $json_data ): int {
		return isset( $json_data['limits']['maximum_total_cover'] ) ? absint( $json_data['limits']['maximum_total_cover'] ) : 0;
	}

	/**
	 * Get supported countries from JSON.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return array Supported countries.
	 */
	public static function get_supported_countries( array $json_data ): array {
		return $json_data['restrictions']['supported_countries'] ?? array();
	}

	/**
	 * Get additional rates from JSON.
	 *
	 * @param array $json_data JSON rate data.
	 *
	 * @return array Additional rates.
	 */
	public static function get_additional_rates( array $json_data ): array {
		return $json_data['additional_rates'] ?? array();
	}
}
