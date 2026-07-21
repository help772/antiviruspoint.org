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

namespace SkyVerge\WooCommerce\AvaTax\API\Responses;

use WC_AvaTax_API_Response;
use WC_AvaTax;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API transactions response class.
 *
 * Response for GET /companies/{companyCode}/transactions with value (list of transactions),
 * @recordsetCount, pageKey, and optional @nextLink for pagination.
 *
 * @since 2.x.x
 */
class Transactions_Response extends WC_AvaTax_API_Response {

	/**
	 * Gets a value from the response data (handles both object and array).
	 *
	 * @since 2.x.x
	 *
	 * @param string $key     Key or property name.
	 * @param mixed  $default Default if not set.
	 * @return mixed
	 */
	private function get_response_value( $key, $default = null ) {

		$data = $this->response_data;
		if ( is_object( $data ) && isset( $data->$key ) ) {
			return $data->$key;
		}
		if ( is_array( $data ) && array_key_exists( $key, $data ) ) {
			return $data[ $key ];
		}
		return $default;
	}

	/**
	 * Gets the list of transactions from the response body.
	 *
	 * @since 2.x.x
	 *
	 * @return object[] Array of transaction objects.
	 */
	public function get_transactions() {

		$value = $this->get_response_value( 'value' );
		return is_array( $value ) ? $value : [];
	}

	/**
	 * Gets the total recordset count from the response (@recordsetCount).
	 *
	 * @since 2.x.x
	 *
	 * @return int
	 */
	public function get_recordset_count() {

		$count = $this->get_response_value( '@recordsetCount' );
		return $count !== null ? (int) $count : 0;
	}

	/**
	 * Gets the page key from the response (for pagination).
	 *
	 * @since 2.x.x
	 *
	 * @return string
	 */
	public function get_page_key() {

		$key = $this->get_response_value( 'pageKey' );
		return $key !== null ? (string) $key : '';
	}

	/**
	 * Gets the next link URL if available (for pagination).
	 *
	 * @since 2.x.x
	 *
	 * @return string
	 */
	public function get_next_link() {

		$link = $this->get_response_value( '@nextLink' );
		return $link !== null ? (string) $link : '';
	}

	/**
	 * Gets all transactions, following @nextLink until no more pages.
	 *
	 * @since 2.x.x
	 *
	 * @return object[] All transaction objects across pages.
	 */
	public function get_all_transactions() {

		$transactions = $this->get_transactions();
		$next_link   = $this->get_next_link();

		while ( ! empty( $next_link ) ) {
			$token_array   = explode( '/v2', $next_link );
			$paginated_url = end( $token_array );
			$response     = WC_AvaTax::instance()->get_api()->get_transactions( '', '', $paginated_url );
			$transactions = array_merge( $transactions, $response->get_transactions() );
			$next_link    = $response->get_next_link();
		}

		return $transactions;
	}
}
