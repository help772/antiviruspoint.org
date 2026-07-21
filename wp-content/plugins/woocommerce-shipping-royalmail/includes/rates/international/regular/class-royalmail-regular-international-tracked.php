<?php
/**
 * International-Tracked rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Packaging;
use WooCommerce\RoyalMail\Rates\International_Rate;

/**
 * RoyalMail_Regular_International_Tracked class.
 */
class RoyalMail_Regular_International_Tracked extends International_Rate {
	/**
	 * Slug of the rate (e.g. 'international-tracked').
	 *
	 * @return string
	 */
	public function get_rate_slug(): string {
		return Services::INTERNATIONAL_TRACKED;
	}

	/**
	 * Keep the built-in MEDIUM_PARCEL box available when merchants configure
	 * custom international parcel boxes, so BoxPacker can route packages above
	 * the PACKET 2 kg limit to the MEDIUM_PARCEL rate bands (up to 20 kg).
	 *
	 * @return array<int, string>
	 */
	protected function get_custom_box_additions(): array {
		return array( Packaging::MEDIUM_PARCEL );
	}
}
