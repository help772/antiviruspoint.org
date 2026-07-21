<?php
/**
 * International-Tracked-and-Signed rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Rates\International_Rate;

/**
 * RoyalMail_Online_International_Tracked_Signed class.
 */
class RoyalMail_Online_International_Tracked_Signed extends International_Rate {
	/**
	 * Slug of the rate (e.g. 'international-tracked').
	 *
	 * @return string
	 */
	public function get_rate_slug(): string {
		return Services::INTERNATIONAL_TRACKED_SIGNED;
	}
}
