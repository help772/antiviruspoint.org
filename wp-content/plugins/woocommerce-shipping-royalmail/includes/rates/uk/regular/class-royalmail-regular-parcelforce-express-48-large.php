<?php
/**
 * Parcelforce express48 Large rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Rates\Parcelforce_UK_Rate;

/**
 * RoyalMail_Regular_Parcelforce_Express_48_Large class.
 */
class RoyalMail_Regular_Parcelforce_Express_48_Large extends Parcelforce_UK_Rate {
	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug(): string {
		return Services::PARCELFORCE_EXPRESS_48_LARGE;
	}
}
