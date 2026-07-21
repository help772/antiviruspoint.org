<?php
/**
 * International Economy rate.
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
 * RoyalMail_Online_International_Economy class.
 */
class RoyalMail_Online_International_Economy extends International_Rate {
	/**
	 * Slug of the rate (e.g. 'international-tracked').
	 *
	 * @return string
	 */
	public function get_rate_slug(): string {
		return Services::INTERNATIONAL_ECONOMY;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $country_code Address to ship to.
	 * @param  array  $boxes User-defined boxes.
	 * @param  int    $instance_id Instance ID.
	 *
	 * @return array{ 'rate-slug': float }
	 */
	public function get_quotes( array $items, string $packing_method, string $country_code, array $boxes = array(), int $instance_id = 0 ): array {
		/**
		 * Letters may be sent by Economy outside Europe only.
		 * See Page 19 https://www.royalmail.com/sites/royalmail.com/files/2025-07/online-price-guide-july-2025-v1.pdf .
		 */
		if ( in_array( $country_code, $this->get_all_european_countries(), true ) ) {
			unset( $this->bands[ Packaging::LETTER ], $this->boxes[ Packaging::LETTER ] );
		}

		return parent::get_quotes( $items, $packing_method, $country_code, $boxes, $instance_id );
	}
}
