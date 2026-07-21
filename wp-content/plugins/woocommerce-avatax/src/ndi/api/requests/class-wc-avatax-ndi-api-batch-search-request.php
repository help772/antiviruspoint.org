<?php
/**
 * WooCommerce AvaTax NDI API Batch Search Request
 *
 * Handles batch search requests to the Network Directory Interface API.
 *
 * @since 3.6.0
 */

defined('ABSPATH') || exit;

/**
 * NDI API Batch Search Request class.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API_Batch_Search_Request extends WC_AvaTax_NDI_API_Request {

	/**
	 * Constructs the batch search request.
	 *
	 * @since 3.6.0
	 *
	 * @param array $args Request arguments containing batch search parameters
	 */
	public function __construct($args = null) {
		$this->path = '/trading-partners/batch-searches';
		$this->method = 'POST';
		
		if (is_array($args)) {
			$this->prepare_request($args);
		}
	}

	/**
	 * Prepares the request for the batch search API.
	 *
	 * @since 3.6.0
	 *
	 * @param array $args Request arguments containing search queries, filters, email, and name
	 * @return void
	 */
	protected function prepare_request($args = array()) {
		// Extract required parameters
		$notification_email = isset($args['notification_email']) ? sanitize_email($args['notification_email']) : '';
		$batch_name = isset($args['batch_name']) ? sanitize_text_field($args['batch_name']) : '';
		$search_queries = isset($args['search_queries']) && is_array($args['search_queries']) ? $args['search_queries'] : array();
		
		// Validate required parameters
		if (empty($notification_email)) {
			throw new Exception('Notification email is required for batch search');
		}
		
		if (empty($batch_name)) {
			throw new Exception('Batch name is required for batch search');
		}
		
		if (empty($search_queries)) {
			throw new Exception('At least one search query is required for batch search');
		}
		
		// Set query parameters
		$query_params = array(
			'notificationEmail' => $notification_email,
			'name' => $batch_name,
		);
		
		// Set the query string (same pattern as search directory request)
		$this->path .= '?' . http_build_query($query_params);
		
		// Prepare the request body
		$body_data = array(
			'value' => array()
		);
		
		// Process each search query
		foreach ($search_queries as $query) {
			$search_item = array();
			
			// Add search term (using $search as per API spec)
			if (! empty($query['search_term'])) {
				$search_item['$search'] = sanitize_text_field($query['search_term']);
			}
			
			// Add filters if provided (using $filters as per API spec)
			// Filters should be in OData format: "network eq 'Peppol' and country eq 'Poland'"
			if (isset($query['filters']) && ! empty($query['filters'])) {
				$filter_string = sanitize_text_field($query['filters']);
				
				// Validate that the filter string looks like OData format
				// Basic validation: should contain 'eq' and quotes for proper OData syntax
				if ($this->is_valid_odata_filter($filter_string)) {
					$search_item['$filters'] = $filter_string;
				} else {
					// Log warning but don't fail - let API handle invalid filters
					if (wc_avatax()->elr_logging_enabled()) {
						wc_avatax()->log_elr(sprintf(
							'NDI Batch Search Warning: Invalid OData filter format: %s', 
							$filter_string
						));
					}
					$search_item['$filters'] = $filter_string; // Still send it, let API validate
				}
			} else {
				// Include empty filters field as shown in the example
				$search_item['$filters'] = '';
			}
			
			$body_data['value'][] = $search_item;
		}
		
		// Set the request body as JSON
		$this->data = wp_json_encode($body_data);
	}

	/**
	 * Validates if a filter string is in proper OData format.
	 *
	 * @since 3.6.0
	 *
	 * @param string $filter_string The filter string to validate
	 * @return bool True if the filter appears to be valid OData format
	 */
	private function is_valid_odata_filter($filter_string) {
		// Basic validation for OData filter format
		// Should contain 'eq' operator and quoted values
		// Examples: "network eq 'Peppol'", "country eq 'Poland' and network eq 'Turkey'"
		
		if (empty($filter_string)) {
			return true; // Empty filters are valid
		}
		
		// Check for basic OData patterns
		// Should contain 'eq' and single quotes around values
		$has_eq_operator = strpos($filter_string, ' eq ') !== false;
		$has_quoted_values = preg_match("/eq\s+'[^']+'/", $filter_string);
		
		// Check for valid logical operators if multiple conditions
		$valid_logical_ops = true;
		if (strpos($filter_string, ' and ') !== false || strpos($filter_string, ' or ') !== false) {
			// If it has logical operators, make sure they're properly formatted
			$valid_logical_ops = preg_match("/(\w+\s+eq\s+'[^']+')(\s+(and|or)\s+\w+\s+eq\s+'[^']+')*$/", $filter_string);
		}
		
		return $has_eq_operator && $has_quoted_values && $valid_logical_ops;
	}
}
