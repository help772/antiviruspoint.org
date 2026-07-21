<?php
/**
 * UK Signed For First class rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;

/**
 * RoyalMail_Online_First_Class_Signed class.
 */
class RoyalMail_Online_First_Class_Signed extends RoyalMail_Online_First_Class {
	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug(): string {
		return Services::FIRST_CLASS_SIGNED;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $country_code Address to ship to.
	 * @param array  $boxes Boxes used.
	 * @param int    $instance_id Instance ID.
	 *
	 * @return array{ 'first-class-signed': float }|false|null
	 */
	public function get_quotes( array $items, string $packing_method, string $country_code, array $boxes = array(), int $instance_id = 0 ) {
		$class_quote             = 0;
		$packages                = $this->get_packages( $items, $packing_method );
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > $this->get_compensation_up_to_value() && ! $ignore_max_compensation ) {
					return false; // Max. compensation is 50.
				}

				$quote = 0;

				if ( ! $this->get_rate_bands( $package->id ) ) {
					return false; // Unpacked item.
				}

				$bands = $this->get_rate_bands( $package->id );

				$matched = false;

				foreach ( $bands as $band => $value ) {
					if ( is_numeric( $band ) && $package->weight <= $band ) {
						$quote  += $value;
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					return null;
				}

				$class_quote += $quote;
			}
		}

		// Return pounds.
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $class_quote / 100;

		return $quotes;
	}
}
