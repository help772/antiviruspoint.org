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
 * The AvaTax ELR API Get Documents request class.
 *
 * Fetches a list of e-invoicing documents from the Studio Router
 * `/documents` endpoint, filtered by date range and optional query
 * parameters (flow, countryCode, status, pagination, etc.).
 *
 * Example endpoint:
 *  GET /documents?startDate=2026-05-15T00:00:01&endDate=2026-05-18T23:59:59
 *
 * @since 3.8.4
 */
class WC_AvaTax_Elr_API_Get_Documents_Request extends \WC_AvaTax_Elr_API_Request {


	/**
	 * Constructs the request.
	 *
	 * @since 3.8.4
	 *
	 * @param array $args {
	 *     Query parameters for the documents endpoint.
	 *
	 *     @type string $startDate    Required. ISO 8601 start date, e.g. `2026-05-15T00:00:01`.
	 *     @type string $endDate      Required. ISO 8601 end date,   e.g. `2026-05-18T23:59:59`.
	 *     @type string $flow         Optional. `in` or `out`.
	 *     @type string $countryCode  Optional. ISO country code, e.g. `FR`, `RO`.
	 *     @type string $status       Optional. Document status, e.g. `Complete`.
	 *     @type string $documentType Optional. Document type filter.
	 *     @type int    $top          Optional. Page size.
	 *     @type int    $skip         Optional. Skip count for pagination.
	 * }
	 */
	public function __construct( $args = array() ) {

		$this->path   = '/documents';
		$this->method = 'GET';

		$this->prepare_request( (array) $args );
	}


	/**
	 * Prepares the query string parameters for the request.
	 *
	 * The framework's API base will append these as a query string
	 * automatically for GET requests via `http_build_query`.
	 *
	 * @since 3.8.4
	 *
	 * @param array $args query parameters
	 */
	public function prepare_request( array $args ) {

		// The Studio Router /documents endpoint uses plain (non-OData) query
		// parameter names — no `$` prefix anywhere, including pagination.
		$allowed_keys = array(
			'startDate',
			'endDate',
			'flow',
			'countryCode',
			'countryMandate',
			'status',
			'documentType',
			'documentNumber',
			'companyId',
			'receiver',
			'interface',
			'top',
			'skip',
			'filter',
			'orderby',
			'nextPageInfo',
		);

		$params = array();

		foreach ( $allowed_keys as $key ) {
			if ( isset( $args[ $key ] ) && '' !== $args[ $key ] ) {
				$params[ $key ] = $args[ $key ];
			}
		}

		$this->params = $params;
	}
}
