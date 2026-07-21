<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase --- Ignore the class file name
/**
 * Base class Parcelforce UK rate.
 *
 * @package WC_RoyalMail/Rate
 */

namespace WooCommerce\RoyalMail\Rates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parcelforce UK rate.
 *
 * @since 4.0.0
 */
abstract class Parcelforce_UK_Rate extends RoyalMail_Rate {
	/**
	 * Get quotes for the rate.
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $country_code Address to ship to.
	 *
	 * @return array<string, float>
	 */
	public function get_quotes( array $items, string $packing_method, string $country_code ): array {
		$quote    = 0;
		$packages = $this->get_packages( $items, $packing_method );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				/**
				 * ToDO: This need to be reviewed
				 * https://www.parcelforce.com/help-and-advice/sending/under-declaration
				 * https://www.parcelforce.com/sites/default/files/3890_A4_Contract_Tariff_Packaging_Guidelines_AW_09-06-21.pdf
				 */
				if ( empty( $package->id ) ) {
					// Check if it is a Tube.
					$girth            = $package->length + ( $package->width * 2 );
					$length_limit_met = $package->length < 900;
					$girth_limit_met  = $girth < 1040;

					if ( ! ( $length_limit_met && $girth_limit_met ) ) {
						return array(); // Unpacked item.
					}
				}

				$bands   = $this->get_rate_bands();
				$matched = false;

				foreach ( $bands as $coverage => $weight_bands ) {
					foreach ( $weight_bands as $weight => $value ) {
						if ( is_numeric( $weight ) && $package->weight <= $weight ) {
							$quote  += $value;
							$matched = true;
							break 2;
						}
					}
				}

				if ( ! $matched ) {
					return array();
				}
			}
		}

		// If taxed, rate include 20% VAT.
		if ( $this->is_taxed ) {
			$quote = $quote / 1.2;
		}

		$quote                            = $quote / 100;
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $quote;

		return $quotes;
	}
}
