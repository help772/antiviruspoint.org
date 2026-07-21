<?php
/**
 * Parcelforce globalpriority rates.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Rates\Parcelforce_Rate;

/**
 * Rates for Parcelforce globalpriority.
 */
class RoyalMail_Regular_Parcelforce_Globalpriority extends Parcelforce_Rate {
	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug(): string {
		return Services::PARCELFORCE_GLOBALPRIORITY;
	}
}
