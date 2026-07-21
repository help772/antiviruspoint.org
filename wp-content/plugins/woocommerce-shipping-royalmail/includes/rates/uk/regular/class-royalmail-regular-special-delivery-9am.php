<?php
/**
 * Special Delivery Guaranteed by 9am rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Rates\RoyalMail_Rate;

/**
 * RoyalMail_Regular_Special_Delivery_9am class.
 */
class RoyalMail_Regular_Special_Delivery_9am extends RoyalMail_Rate {
	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug(): string {
		return Services::SPECIAL_DELIVERY_9AM;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $country_code Address to ship to.
	 *
	 * @return array{ 'special-delivery-9am': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $country_code ) {
		$quote    = false;
		$packages = $this->get_packages( $items, $packing_method );

		// Service not available for the following destinations.
		$excluded_destinations = array(
			'GG', // Guernsey.
			'IM', // Isle of Man.
			'JE', // Jersey.
		);

		if ( in_array( $country_code, $excluded_destinations, true ) ) {
			return false;
		}

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( empty( $package->id ) ) {
					// Try a tube or fail.
					if ( $package->length < 900 && $package->length + ( $package->width * 2 ) < 1040 ) {
						$package->id = 'packet';
					} else {
						return false; // Unpacked item.
					}
				}

				$bands = $this->get_rate_bands();

				// Check if rate data is available.
				if ( false === $bands ) {
					return false;
				}

				$matched = false;

				foreach ( $bands as $coverage => $weight_bands ) {
					if ( is_numeric( $coverage ) && $package->value > $coverage ) {
						continue;
					}
					foreach ( $weight_bands as $weight => $value ) {

						if ( is_numeric( $weight ) && $package->weight <= $weight ) {
							$quote  += $value;
							$matched = true;
							break 2;
						}
					}
				}

				if ( ! $matched ) {
					return null;
				}
			}
		}

		// Rates include 20% VAT.
		$quote = $quote / 1.2;
		$quote = $quote / 100;

		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $quote;

		return $quotes;
	}
}
