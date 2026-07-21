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

namespace SkyVerge\WooCommerce\AvaTax\API\Requests;

use WC_AvaTax_API_Request;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API transactions request class.
 *
 * Fetches transactions for a company, with optional OData $filter (e.g. date range and type).
 *
 * @since 2.x.x
 */
class Transactions_Request extends WC_AvaTax_API_Request {

	/**
	 * The Transactions request constructor.
	 *
	 * @since 2.x.x
	 *
	 * @param string $company_code  Company code (defaults to option wc_avatax_company_code when empty).
	 * @param string $filter        Optional OData $filter (e.g. date between and type eq 'SalesInvoice').
	 * @param string $paginated_url Optional full path for next page (from response @nextLink);
	 *                              when set, path and filter are ignored.
	 */
	public function __construct( $company_code = '', $filter = '', $paginated_url = '' ) {

		if ( ! empty( $paginated_url ) ) {
			$this->path = $paginated_url;
		} else {
			$company_code = $company_code ?: get_option( 'wc_avatax_company_code', '' );
			$this->path   = '/companies/' . $company_code . '/transactions';
			if ( ! empty( $filter ) ) {
				$this->path .= '?$filter=' . rawurlencode( $filter );
			}
		}

		$this->method = 'GET';
	}
}
