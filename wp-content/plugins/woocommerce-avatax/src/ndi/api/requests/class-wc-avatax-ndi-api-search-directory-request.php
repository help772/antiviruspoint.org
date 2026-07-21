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
 * The NDI API Search Directory request class.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API_Search_Directory_Request extends \WC_AvaTax_NDI_API_Request {

    /**
     * Constructs the search directory request.
     *
     * @since 3.6.0
     *
     * @param array $args Request arguments containing search parameters
     */
    public function __construct($args = null) {
        $this->path = '/trading-partners';
        $this->method = 'GET';
        
        if (is_array($args)) {
            $this->prepare_request($args);
        }
    }

    /**
     * Prepares the search directory request.
     *
     * @since 3.6.0
     *
     * @param array $args Request arguments
     */
    public function prepare_request($args) {
        $search_term = isset($args['search_term']) ? $args['search_term'] : '';
        $filters = isset($args['filters']) ? $args['filters'] : array();
        $page = isset($args['page']) ? (int) $args['page'] : 1;
        $per_page = isset($args['per_page']) ? (int) $args['per_page'] : 10;

        // Build OData query parameters
        $query_params = array();

        // Add count parameter
        $query_params['count'] = 'true';

        // Add pagination parameters
        $query_params['top'] = $per_page;
        $query_params['skip'] = ($page - 1) * $per_page;

        // Add ordering
        $query_params['orderBy'] = 'name ASC';

        // Add search parameter if provided
        if (! empty($search_term)) {
            $query_params['search'] = $search_term;
        }

        // Always include facets for single search, allow override for filter-only requests
        $include_facets = isset($args['include_facets']) ? $args['include_facets'] : true;
        $include_results = isset($args['include_results']) ? $args['include_results'] : true;
        
        // Add new API parameters
        $query_params['includeFacets'] = $include_facets ? 'true' : 'false';
        $query_params['includeResults'] = $include_results ? 'true' : 'false';

        // Add filter parameters dynamically
        $filter_conditions = array();
        
        // Map frontend filter names to API filter expressions
        $filter_mapping = array(
            'network' => function($value) { 
                return "network eq '" . esc_sql($value) . "'"; 
            },
            'country' => function($value) { 
                return "country eq '" . esc_sql($value) . "'"; 
            },
            'documentType' => function($value) { 
                return "documentType eq '" . esc_sql($value) . "'"; 
            },
            'idType' => function($value) { 
                return "idType eq '" . esc_sql($value) . "'"; 
            }
       );
        
        // Process all filters dynamically
        foreach ($filters as $filter_key => $filter_value) {
            if (! empty($filter_value) && isset($filter_mapping[ $filter_key ])) {
                $filter_conditions[] = $filter_mapping[ $filter_key ]($filter_value);
            }
        }
        
        if (! empty($filter_conditions)) {
            $query_params['filter'] = implode(' and ', $filter_conditions);
        }

        // Set the query string
        $this->path .= '?' . http_build_query($query_params);
    }
}
