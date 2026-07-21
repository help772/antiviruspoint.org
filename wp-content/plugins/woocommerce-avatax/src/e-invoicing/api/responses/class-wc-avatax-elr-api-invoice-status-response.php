<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API utility response class.
 *
 * @since 3.0.0
 */
class WC_AvaTax_Elr_API_Invoice_Status_Response extends \WC_AvaTax_Elr_API_Response {

	/**
	 * Gets the status.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function get_status() {
		return $this->status;
	}


	/**
	 * Gets the business status of the invoice.
	 *
	 * @since 3.8.4
	 *
	 * @return string
	 */
	public function get_business_status() {
		return (string) ( $this->businessStatus ?? '' );
	}


	/**
	 * Gets the status messages.
	 *
	 * @since 3.0.0
	 *
	 */
	public function get_status_messages() {
		return ((array) $this->events);
	}
	
	/**
	 * Gets AvailableMediaType
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_available_media_types() {

		return ((array) $this->AvailableMediaType);
	}

	/**
	 * Extracts responseKey => responseValue map from events.
	 *
	 * @since 3.4.0
	 *
	 * @return string JSON-encoded map of responseKey => responseValue pairs, or '{}' if no keys found.
	 */
	public function get_response_key_map() {
		$response_keys = [];

		foreach ((array) $this->events as $event) {
			if (!empty($event->responseKey) && !empty($event->responseValue)) {
				$response_keys[$event->responseKey] = $event->responseValue;
			}
		}

		return json_encode($response_keys);
	}

	public function get_downloadable_media_types() {
		$downloadable_media_types = array();
		$items = $this->response_data->downloadableMediaTypes ?? array();
		if ( ! is_array( $items ) ) {
			return $downloadable_media_types;
		}
		foreach ( $items as $item ) {
			if ( is_object( $item ) && ! empty( $item->mediaType ) ) {
				$downloadable_media_types[] = (string) $item->mediaType;
			} elseif ( is_array( $item ) && ! empty( $item['mediaType'] ) ) {
				$downloadable_media_types[] = (string) $item['mediaType'];
			}
		}
		return array_values( array_unique( $downloadable_media_types ) );
	}
}