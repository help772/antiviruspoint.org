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
 * Handle the Network Directory Interface functionality.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI {

	/** @var string NDI Portal URL for production environment */
	const NDI_PORTAL_URL_PRODUCTION = 'https://app.avalara.com/elr-ui/directory';

	/** @var string NDI Portal URL for development/sandbox environment */
	const NDI_PORTAL_URL_DEVELOPMENT = 'https://app.sbx.avalara.com/elr-ui/directory';

	/**
	 * Construct the class.
	 *
	 * @since 3.6.0
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Adds handler actions and filters.
	 *
	 * @since 3.6.0
	 * @codeCoverageIgnore
	 */
	private function add_hooks() {
		// AJAX hooks for Network Directory functionality
		add_action('wp_ajax_wc_avatax_network_directory_search', [ $this, 'network_directory_search' ]);
		add_action('wp_ajax_wc_avatax_ndi_batch_search_submit', [ $this, 'submit_batch_search' ]);
		add_action('wp_ajax_wc_avatax_ndi_batch_search_list', [ $this, 'list_batch_searches' ]);
	}

	/**
	 * Handles AJAX request for Network Directory search.
	 *
	 * @since 3.6.0
	 */
	public function network_directory_search() {
		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		try {
			// Get search parameters
			$search_term = sanitize_text_field($_POST['search_term'] ?? '');
			$page = absint($_POST['page'] ?? 1);
			$per_page = absint($_POST['per_page'] ?? 10);
			
			// Get filter parameters dynamically
			$filters = array();
			foreach ($_POST as $key => $value) {
				if (strpos($key, 'filter_') === 0) {
					$filter_key = str_replace('filter_', '', $key);
					$filters[ $filter_key ] = sanitize_text_field($value);
				}
			}

			// Validate search parameters
			if (empty($search_term)) {
				wp_send_json(array(
					'code'    => 400,
					'message' => __('Search term is required', 'woocommerce-avatax'),
					'data'    => array(),
					'pagination' => array()
				));
				return;
			}

			// Validate pagination parameters
			$page = max(1, $page);
			$per_page = min(max(1, $per_page), 100); // Limit max per_page to 100

			// Log the search request
			wc_avatax()->elr_logger()->log_event(
				"Network Directory Search",
				"network_directory_search",
				sprintf(
					"Search request - Term: %s, Filters: %s, Page: %d, Per Page: %d",
					$search_term,
                    wp_json_encode(array_filter($filters, function($value) { return !empty($value); })),
					$page,
					$per_page
				)
			);

			// Use actual NDI API
			$api_response = $this->call_ndi_search_api($search_term, $page, $per_page, $filters);
			
			if ($api_response && is_array($api_response) && $api_response['success']) {
				// API call successful - extract the data from the nested structure
				$response_data = $api_response['data']; // This contains searchResult, facetsResult
				
				// Handle new response structure with searchResult
				if (isset($response_data['searchResult'])) {
					$search_result = $response_data['searchResult'];
					$results = isset($search_result['value']) && is_array($search_result['value']) ? $search_result['value'] : array();
					$total_results = isset($search_result['recordSetCount']) ? $search_result['recordSetCount'] : 0;
					$next_link = isset($search_result['@nextLink']) ? $search_result['@nextLink'] : null;
				} else {
					// Fallback to old structure
					$results = isset($response_data['value']) && is_array($response_data['value']) ? $response_data['value'] : array();
					$total_results = isset($response_data['@recordSetCount']) ? $response_data['@recordSetCount'] : 0;
					$next_link = isset($response_data['@nextLink']) ? $response_data['@nextLink'] : null;
				}
				
				$facets = isset($api_response['facets']) ? $api_response['facets'] : array(); // Extract facets from API response
				
				$response = array(
					'code'    => 200,
					'message' => __('Search completed successfully', 'woocommerce-avatax'),
					'data'    => $results, // Send the actual results array
					'recordSetCount' => $total_results,
					'nextLink' => $next_link,
					'pagination' => array(
						'current_page' => $page,
						'per_page' => $per_page,
						'total_results' => $total_results,
						'total_pages' => ceil($total_results / $per_page),
						'has_prev' => $page > 1,
						'has_next' => $page < ceil($total_results / $per_page)
					)
				);
				
				// Include facets if available
				if (! empty($facets)) {
					$response['facets'] = $facets;
				}
				
				wp_send_json($response);
			} else {
				// API call failed, return empty results
				wc_avatax()->elr_logger()->log_event(
					"Network Directory Search",
					"network_directory_search_api_failed",
					sprintf(
						"API call failed, returning empty results. Response: %s",
						$api_response ? wp_json_encode($api_response) : 'false'
					)
				);
			
			wp_send_json(array(
				'code'    => 200,
					'message' => __('No results found', 'woocommerce-avatax'),
					'data'    => array(), // Empty results array
					'recordSetCount' => 0,
					'nextLink' => null,
					'pagination' => array(
						'current_page' => $page,
						'per_page' => $per_page,
						'total_results' => 0,
						'total_pages' => 0,
						'has_prev' => false,
						'has_next' => false
					)
				));
			}

		} catch (Exception $e) {
			wp_send_json(array(
				'code'    => 500,
				'message' => $e->getMessage(),
				'data'    => array(),
				'pagination' => array()
			));
		}
	}

	/**
	 * Calls the NDI API for search functionality.
	 *
	 * @since 3.6.0
	 *
	 * @param string $search_term The search term
	 * @param int    $page        Current page number
	 * @param int    $per_page    Results per page
	 * @param array  $filters     Filter parameters
	 *
	 * @return array|false API response or false on failure
	 */
	private function call_ndi_search_api($search_term, $page, $per_page, $filters = array()) {
		try {
			$ndi_api = wc_avatax()->get_ndi_api();

			// Only include facets on the first page to optimize subsequent calls
			$include_facets = ($page == 1);

			// Make the search API call
			$response = $ndi_api->search_directory($search_term, $filters, $page, $per_page, $include_facets);
			return $response;

		} catch (Exception $e) {
			// Log the error
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'NDI Search API Call Failed: %s',
					$e->getMessage()
				));
			}

			wc_avatax()->elr_logger()->log_exception(
							"NDI Search",
							"call_ndi_search_api",
							$e->getMessage(),
							$e->getTraceAsString()
						);

			return false;
		}
	}

	/**
	 * Handles AJAX request for batch search submission.
	 *
	 * @since 3.6.0
	 */
	public function submit_batch_search() {
		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		try {
			// Get batch name, notification email, and JSON data
			$batch_name = sanitize_text_field($_POST['batch_name'] ?? '');
			$notification_email = sanitize_email($_POST['notification_email'] ?? '');
			$batch_json = wc_clean(wp_unslash($_POST['batch_json'] ?? ''));

			if (empty($batch_name)) {
				wp_send_json_error('Batch name is required');
				return;
			}

			if (empty($notification_email)) {
				wp_send_json_error('Notification email is required');
				return;
			}

			if (empty($batch_json)) {
				wp_send_json_error('Batch JSON data is required');
				return;
			}

			// Validate JSON
			$batch_data = json_decode($batch_json, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				wp_send_json_error('Invalid JSON format: ' . json_last_error_msg());
				return;
			}

			// Validate that we have search queries in the expected format
			if (! isset($batch_data['value']) || ! is_array($batch_data['value'])) {
				wp_send_json_error('Invalid batch data format: missing value array');
				return;
			}

			// Convert the JSON data to the format expected by the API
			$search_queries = array();
			foreach ($batch_data['value'] as $query) {
				$search_query = array();
				
				// Extract search term (remove $ prefix for internal processing)
				if (isset($query['$search'])) {
					$search_query['search_term'] = $query['$search'];
				}
				
				// Extract filters (remove $ prefix for internal processing)
				if (isset($query['$filters'])) {
					$search_query['filters'] = $query['$filters'];
				}
				
				$search_queries[] = $search_query;
			}

			// Submit batch search to NDI API
			$api_response = wc_avatax()->get_ndi_api()->batch_search($notification_email, $batch_name, $search_queries);
			
			// Check if API call was successful
			if (isset($api_response['success']) && $api_response['success'] === false) {
				// Provide user-friendly error message
				$error_message = $this->get_friendly_error_message($api_response['message'] ?? 'Unknown error');
				wp_send_json_error($error_message);
				return;
			}
			
			// Log successful submission
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'NDI Batch Search Submitted Successfully - Batch ID: %s',
					$api_response['batch_id'] ?? 'unknown'
				));
			}

			wp_send_json_success(array(
				'message' => $api_response['message'] ?? 'Batch search submitted successfully',
				'batch_id' => $api_response['batch_id'] ?? null,
				'batch_name' => $batch_name, // Use original batch name from request
				'status' => $api_response['status'] ?? 'submitted',
				'notification_email' => $notification_email, // Use original email from request
				'query_count' => count($search_queries) // Use count from request
			));

		} catch (Exception $e) {
			// Log the error
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'NDI Batch Search Submission Error: %s',
					$e->getMessage()
				));
			}

			// Provide user-friendly error message
			$error_message = $this->get_friendly_error_message($e->getMessage());
			wp_send_json_error($error_message);
		}
	}

	/**
	 * Converts technical error messages to user-friendly messages.
	 *
	 * @since 3.6.0
	 *
	 * @param string $technical_message The technical error message from API
	 * @return string User-friendly error message
	 */
	private function get_friendly_error_message($technical_message) {
		$message = strtolower($technical_message);
		
		// Authentication/Authorization errors
		if (strpos($message, 'unauthorized') !== false || strpos($message, '401') !== false) {
			return __('Authentication failed. Please check your API credentials and try again.', 'woocommerce-avatax');
		}
		
		if (strpos($message, 'forbidden') !== false || strpos($message, '403') !== false) {
			return __('Access denied. Your account may not have permission to perform batch searches.', 'woocommerce-avatax');
		}
		
		// Validation errors
		if (strpos($message, 'bad request') !== false || strpos($message, '400') !== false) {
			return __('Invalid request data. Please check your search entries and try again.', 'woocommerce-avatax');
		}
		
		if (strpos($message, 'email') !== false && strpos($message, 'required') !== false) {
			return __('A valid notification email address is required for batch searches.', 'woocommerce-avatax');
		}
		
		if (strpos($message, 'name') !== false && strpos($message, 'required') !== false) {
			return __('A batch search name is required. Please provide a descriptive name.', 'woocommerce-avatax');
		}
		
		// Rate limiting
		if (strpos($message, 'rate limit') !== false || strpos($message, '429') !== false) {
			return __('Too many requests. Please wait a moment and try again.', 'woocommerce-avatax');
		}
		
		// Server errors
		if (strpos($message, 'internal server error') !== false || strpos($message, '500') !== false) {
			return __('The service is temporarily unavailable. Please try again in a few minutes.', 'woocommerce-avatax');
		}
		
		if (strpos($message, 'service unavailable') !== false || strpos($message, '503') !== false) {
			return __('The batch search service is currently under maintenance. Please try again later.', 'woocommerce-avatax');
		}
		
		// Network/timeout errors
		if (strpos($message, 'timeout') !== false || strpos($message, 'network') !== false) {
			return __('Network connection timeout. Please check your internet connection and try again.', 'woocommerce-avatax');
		}
		
		// Quota/limit errors
		if (strpos($message, 'quota') !== false || strpos($message, 'limit exceeded') !== false) {
			return __('Your batch search quota has been exceeded. Please contact support or try again later.', 'woocommerce-avatax');
		}
		
		// Default fallback for unknown errors
		return sprintf(
			__('Unable to submit batch search. Please try again or contact support if the problem persists. (Error: %s)', 'woocommerce-avatax'),
			$technical_message
		);
	}

	/**
	 * Gets the NDI Portal URL based on the current ELR environment.
	 *
	 * @since 3.6.0
	 *
	 * @return string The NDI Portal URL for the current environment
	 */
	public function get_ndi_portal_url() {
		$environment = wc_avatax()->get_elr_api_environment();
		
		return ('production' === $environment) ? self::NDI_PORTAL_URL_PRODUCTION : self::NDI_PORTAL_URL_DEVELOPMENT;
	}

	/**
	 * Handles AJAX request for listing batch searches.
	 *
	 * @since 3.6.0
	 */
	public function list_batch_searches() {
		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		try {
			// Get pagination parameters
			$page = intval($_POST['page'] ?? 1);
			$per_page = intval($_POST['per_page'] ?? 10);
			$filter = sanitize_text_field($_POST['filter'] ?? '');
			$order_by = sanitize_text_field($_POST['order_by'] ?? 'created DESC');

			// Calculate skip value for pagination
			$skip = ($page - 1) * $per_page;

			// Prepare API arguments
			$api_args = array(
				'top' => $per_page,
				'skip' => $skip,
				'orderBy' => $order_by,
				'current_page' => $page,
			);

			// Add filter if provided
			if (! empty($filter)) {
				$api_args['filter'] = $filter;
			}

			// Call the API
			$api_response = wc_avatax()->get_ndi_api()->list_batch_searches($api_args);

			// Check if API call was successful
			if (isset($api_response['success']) && $api_response['success'] === false) {
				wp_send_json_error(array(
					'message' => $api_response['message'] ?? 'Failed to fetch batch searches',
					'data' => array()
				));
				return;
			}


			wp_send_json_success(array(
				'data' => $api_response['data'] ?? array(),
				'pagination' => $api_response['pagination'] ?? array(),
				'message' => 'Batch searches loaded successfully'
			));

		} catch (Exception $e) {
			// Log the error
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'NDI Batch Search List Error: %s',
					$e->getMessage()
				));
			}

			wp_send_json_error(array(
				'message' => 'Failed to load batch searches. Please try again.',
				'data' => array()
			));
		}
	}

}
