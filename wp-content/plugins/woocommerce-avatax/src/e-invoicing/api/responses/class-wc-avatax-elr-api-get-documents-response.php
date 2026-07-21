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

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax ELR API Get Documents response class.
 *
 * Parses responses from the Studio Router `/documents` endpoint.
 *
 * Expected response shape:
 * {
 *     "@recordsetCount": 5,
 *     "@nextLink": null,
 *     "value": [ { ...document... }, ... ],
 *     "nextPageInfo": null
 * }
 *
 * @since 3.8.4
 */
class WC_AvaTax_Elr_API_Get_Documents_Response extends \WC_AvaTax_Elr_API_Response {


	/**
	 * Gets the raw list of documents from the `value` array.
	 *
	 * @since 3.8.4
	 *
	 * @return array list of document objects (stdClass), or empty array
	 */
	public function get_documents() : array {

		$documents = $this->value;

		if ( empty( $documents ) || ! is_array( $documents ) ) {
			return array();
		}

		return $documents;
	}


	/**
	 * Gets the total record count returned by the API.
	 *
	 * Maps to the `@recordsetCount` property in the response payload.
	 * The leading `@` makes the property inaccessible via the magic
	 * `__get`, so it is read directly off `response_data`.
	 *
	 * @since 3.8.4
	 *
	 * @return int
	 */
	public function get_records_count() : int {

		if ( isset( $this->response_data->{'@recordsetCount'} ) ) {
			return (int) $this->response_data->{'@recordsetCount'};
		}

		return 0;
	}


	/**
	 * Gets the `nextPageInfo` cursor object, if provided by the API.
	 *
	 * @since 3.8.4
	 *
	 * @return mixed null when not present
	 */
	public function get_next_page_info() {

		return $this->nextPageInfo;
	}


	/**
	 * Returns the query-string args required to fetch the next page.
	 *
	 * The Studio Router `/documents` endpoint returns its pagination cursor
	 * inline as a `nextPageInfo` object that already contains the exact
	 * query params for the next page, e.g.:
	 *
	 *   "nextPageInfo": {
	 *     "startDate": "2026-05-05T00:00:01",
	 *     "endDate":   "2026-05-18T23:59:59",
	 *     "$skip":     "50",
	 *     "$top":      "50",
	 *     "flow":      "in"
	 *   }
	 *
	 * The response may echo pagination keys as `$skip`/`$top`, but the endpoint
	 * itself does NOT accept `$`-prefixed params on the URL, so keys are
	 * normalized here by stripping the leading `$`.
	 *
	 * @since 3.8.4
	 *
	 * @return array|null next-page args, or null when there is no next page
	 */
	public function get_next_page_args() : ?array {

		$next_page_info = $this->get_next_page_info();

		if ( ! is_object( $next_page_info ) && ! is_array( $next_page_info ) ) {
			return null;
		}

		$normalized = $this->normalize_pagination_keys( (array) $next_page_info );

		return ! empty( $normalized ) ? $normalized : null;
	}


	/**
	 * Strips the leading `$` from any param key (e.g. `$skip` → `skip`,
	 * `$top` → `top`). The Studio Router `/documents` endpoint accepts
	 * non-OData parameter names, so `$`-prefixed keys must be normalized
	 * before being sent on the next request.
	 *
	 * @since 3.8.4
	 *
	 * @param array $args raw key/value map from the API response
	 * @return array same map with `$`-prefixed keys rewritten to plain keys
	 */
	private function normalize_pagination_keys( array $args ) : array {

		$normalized = array();

		foreach ( $args as $key => $value ) {
			$clean_key = ltrim( (string) $key, '$' );

			if ( '' === $clean_key ) {
				continue;
			}

			$normalized[ $clean_key ] = $value;
		}

		return $normalized;
	}


	/**
	 * Returns the document list as an array of normalized associative arrays.
	 *
	 * Useful for callers that prefer arrays over stdClass objects (e.g. for
	 * rendering in admin tables or persisting to options/transients).
	 *
	 * @since 3.8.4
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_documents_as_array() : array {

		$result = array();

		foreach ( $this->get_documents() as $document ) {

			$result[] = array(
				'id'              => $document->id              ?? '',
				'companyId'       => $document->companyId       ?? '',
				'processDateTime' => $document->processDateTime ?? '',
				'status'          => $document->status          ?? '',
				'documentNumber'  => $document->documentNumber  ?? '',
				'documentType'    => $document->documentType    ?? '',
				'documentVersion' => $document->documentVersion ?? '',
				'documentDate'    => $document->documentDate    ?? '',
				'flow'            => $document->flow            ?? '',
				'countryCode'     => $document->countryCode     ?? '',
				'countryMandate'  => $document->countryMandate  ?? '',
				'receiver'        => $document->receiver        ?? '',
				'createdAt'       => $document->createdAt       ?? '',
				'businessStatus'  => $document->businessStatus  ?? null,
				'supplierName'    => $document->supplierName    ?? '',
				'customerName'    => $document->customerName    ?? '',
				'interface'       => $document->interface       ?? '',
				'lastUpdatedAt'   => $document->lastUpdatedAt   ?? '',
			);
		}

		return $result;
	}
}
