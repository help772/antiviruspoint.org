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
 * The AvaTax Network Directory Interface API.
 *
 * Extends the ELR API class to inherit authorization and base functionality.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API extends WC_AvaTax_Elr_API {

	/**
	 * Construct the NDI API.
	 *
	 * @since 3.6.0
	 *
	 * @param string $client_id     the API client ID
	 * @param string $client_secret the API client secret
	 * @param string $environment   the API environment, either `production` or `development`
	 */
	public function __construct($client_id, $client_secret, $environment) {
		// Call parent constructor to initialize ELR API with authorization
		// Pass ELR_CONNECTOR_ID to ensure X-Avalara-Client header is set correctly
		parent::__construct($client_id, $client_secret, $environment);
		
		// NDI uses the same base URLs as ELR but different endpoints
		
		// Set NDI-specific headers
		$this->set_request_header('avalara-version', '1.0.0');
		$this->set_request_content_type_header('application/json');
		$this->set_request_accept_header('application/json');
	}

	/**
	 * Builds and returns a new API request object.
	 *
	 * @since 3.6.0
	 *
	 * @param string $type the desired request type
	 * @param mixed  $args optional argument(s) to be passed to the request
	 * @return mixed Request object or null for direct HTTP calls
	 * @throws Framework\SV_WC_API_Exception for invalid request types
	 */
	protected function get_new_request($type = '', $args = null) {
		
		// Get access token first (inherited from ELR API)
		$access_token = $this->get_elr_auth_token();
		if (!$access_token) {
			throw new Exception('Failed to obtain access token for NDI API');
		}
		
		// Ensure X-Avalara-Client header is set (inherited from parent)
		// This header is set in the parent constructor and should be available
		$headers = $this->get_request_headers();
		
		if (!isset($headers['X-Avalara-Client'])) {
			// If header is missing, set it manually
			$avalara_client = sprintf('ELR For WooCommerce || %sv2;%s;;;;', 
				wc_avatax()::VERSION,
				wc_avatax()::ELR_CLIENT_STRING
			);
			$this->set_request_header('X-Avalara-Client', $avalara_client);
		}
		
		// Set required headers for each request
		$this->set_request_header('avalara-version', '1.0.0');
		$this->set_request_header('Authorization', 'Bearer ' . $access_token);
		
		switch ($type) {
			case 'search_directory':
				$this->set_response_handler(WC_AvaTax_NDI_API_Search_Directory_Response::class);
				return new WC_AvaTax_NDI_API_Search_Directory_Request($args);
				
			case 'batch_search':
				$this->set_response_handler(WC_AvaTax_NDI_API_Batch_Search_Response::class);
				return new WC_AvaTax_NDI_API_Batch_Search_Request($args);
				
			case 'list_batch_search':
				$this->set_response_handler(WC_AvaTax_NDI_API_List_Batch_Search_Response::class);
				$request = new WC_AvaTax_NDI_API_List_Batch_Search_Request($args);
				return $request;
				
				
			default:
				throw new Framework\SV_WC_API_Exception('Invalid NDI request type: ' . $type);
		}
	}

	/**
	 * Searches the Network Directory for entities.
	 *
	 * @since 3.6.0
	 *
	 * @param string $search_term the search term
	 * @param array  $filters     optional filters (network, country, document_type, etc.)
	 * @param int    $page        page number for pagination
	 * @param int    $per_page    results per page
	 * 
	 * @return array search results
	 */
	public function search_directory($search_term, $filters = array(), $page = 1, $per_page = 10, $include_facets = true) {
		try {
			$request_args = array(
				'search_term' => $search_term,
				'filters' => $filters,
				'page' => $page,
				'per_page' => $per_page,
				'include_facets' => $include_facets,
				'include_results' => true,
			);

			$request = $this->get_new_request('search_directory', $request_args);
			$response = $this->perform_request($request);

			if ($response && !$response->has_ndi_error()) {
				return $response->get_formatted_response($page, $per_page);
			} else {
				$error_message = $response ? $response->get_ndi_error_message() : 'Unknown error';
				throw new Exception('NDI Search API Error: ' . $error_message);
			}

		} catch (Exception $e) {
			// Log the error if logging is enabled
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'NDI Search API Error: %s',
					$e->getMessage()
				));
			}

			// Return empty results on API failure
			return array(
				'success' => false,
				'data' => array(
					'@recordSetCount' => 0,
					'@nextLink' => null,
					'value' => array(),
					'pagination' => array(
						'current_page' => $page,
						'per_page' => $per_page,
						'total_results' => 0,
						'total_pages' => 0
					)
				),
				'message' => 'API call failed'
			);
		}
	}

	/**
	 * Submits a batch search to the Network Directory.
	 *
	 * @since 3.6.0
	 *
	 * @param string $notification_email Email address for notifications
	 * @param string $batch_name Name of the batch search
	 * @param array  $search_queries Array of search queries with search_term and filters
	 * 
	 * @return array batch search response
	 */
	public function batch_search($notification_email, $batch_name, $search_queries) {
		try {
			$request_args = array(
				'notification_email' => $notification_email,
				'batch_name' => $batch_name,
				'search_queries' => $search_queries,
			);

			$request = $this->get_new_request('batch_search', $request_args);
			$response = $this->perform_request($request);

			if ($response && !$response->has_ndi_error()) {
				return $response->get_formatted_response();
			} else {
				$error_message = $response ? $response->get_ndi_error_message() : 'Unknown error';
				throw new Exception('NDI Batch Search API Error: ' . $error_message);
			}

		} catch (Exception $e) {
			// Log the error if logging is enabled
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'NDI Batch Search API Error: %s', 
					$e->getMessage() 
				));
			}

			// Return error response
			return array(
				'success' => false,
				'message' => $e->getMessage(),
				'batch_id' => null,
			);
		}
	}

	/**
	 * Get list of batch searches.
	 *
	 * @since 3.6.0
	 *
	 * @param array $args Optional arguments for filtering and pagination
	 * @return array The batch search list response
	 * @throws Exception If the API request fails
	 */
	public function list_batch_searches($args = array()) {
		try {
			$request_args = array(
				'filter' => isset($args['filter']) ? $args['filter'] : '',
				'orderBy' => isset($args['orderBy']) ? $args['orderBy'] : 'created DESC',
				'top' => isset($args['top']) ? intval($args['top']) : 10,
				'skip' => isset($args['skip']) ? intval($args['skip']) : 0,
				'current_page' => isset($args['current_page']) ? intval($args['current_page']) : 1,
			);

			$request = $this->get_new_request('list_batch_search', $request_args);
			
			$response = $this->perform_request($request);

			if ($response && !$response->has_ndi_error()) {
				// Pass both current page and per_page to the response method
				$formatted_response = $response->get_formatted_response($request_args['current_page'], $request_args['top']);
				
				return $formatted_response;
			} else {
				$error_message = $response ? $response->get_ndi_error_message() : 'Unknown error';
				
				// Get HTTP status code for more specific error handling
				$http_code = method_exists($this, 'get_response_code') ? $this->get_response_code() : 'Unknown';
				
				// Log detailed error information
				if (wc_avatax()->elr_logging_enabled()) {
					wc_avatax()->log_elr(sprintf(
						'NDI List Batch Search API Response Error - HTTP Code: %s, Error: %s',
						$http_code,
						$error_message
					));
					

				}
				
				throw new Exception(sprintf('NDI List Batch Search API Error (HTTP %s): %s', $http_code, $error_message));
			}

		} catch (Exception $e) {
			// Log the error with more details
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'NDI List Batch Search API Error: %s (Code: %s)', 
					$e->getMessage(),
					$e->getCode()
				));
				
				// Also log the last response if available
				if (method_exists($this, 'get_response_code')) {
					$response_code = $this->get_response_code();
					$response_body = method_exists($this, 'get_response_body') ? $this->get_response_body() : 'N/A';
					wc_avatax()->log_elr(sprintf(
						'NDI List Batch Search HTTP Response - Code: %s, Body: %s',
						$response_code,
						substr($response_body, 0, 500) // Limit body to 500 chars
					));
				}
			}
			return array('success' => false, 'message' => $e->getMessage(), 'data' => array());
		}
	}



	/**
	 * Pings the NDI API to test connectivity.
	 *
	 * @since 3.6.0
	 *
	 * @return bool True if connection successful, false otherwise
	 */
	public function test_connection() {
		try {
			// Use the inherited ELR authentication
			$token = $this->get_elr_auth_token();
			return !empty($token);
		} catch (Exception $e) {
			return false;
		}
	}
}
