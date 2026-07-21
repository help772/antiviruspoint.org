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

defined('ABSPATH') or exit;

/**
 * The NDI API List Batch Search request class.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API_List_Batch_Search_Request extends \WC_AvaTax_NDI_API_Request {

	/**
	 * Constructs the list batch search request.
	 *
	 * @since 3.6.0
	 *
	 * @param array $args Request arguments containing search parameters
	 */
	public function __construct($args = null) {
		$this->path = '/trading-partners/batch-searches';
		$this->method = 'GET';
		
		if (is_array($args)) {
			$this->prepare_request($args);
		}
	}

	/**
	 * Prepares the request.
	 *
	 * @since 3.6.0
	 *
	 * @param array $args Request arguments
	 */
	protected function prepare_request($args) {
		$query_params = array();

		// Handle filter parameter
		if (isset($args['filter']) && ! empty($args['filter'])) {
			$query_params['filter'] = sanitize_text_field($args['filter']);
		}

		// Handle orderBy parameter
		if (isset($args['orderBy']) && ! empty($args['orderBy'])) {
			$query_params['orderBy'] = sanitize_text_field($args['orderBy']);
		} else {
			// Default ordering
			$query_params['orderBy'] = 'created DESC';
		}

		// Handle pagination parameters
		if (isset($args['top']) && is_numeric($args['top'])) {
			$query_params['top'] = intval($args['top']);
		} else {
			$query_params['top'] = 10; // Default page size
		}

		if (isset($args['skip']) && is_numeric($args['skip'])) {
			$query_params['skip'] = intval($args['skip']);
		} else {
			$query_params['skip'] = 0; // Default offset
		}

		// Add query parameters to the path
		if (! empty($query_params)) {
			$this->path .= '?' . http_build_query($query_params);
		}


	}
}
