<?php
/**
 * WooCommerce AvaTax NDI API Batch Search Response
 *
 * Handles batch search responses from the Network Directory Interface API.
 *
 * @since 3.6.0
 */

defined('ABSPATH') || exit;

/**
 * NDI API Batch Search Response class.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API_Batch_Search_Response extends WC_AvaTax_NDI_API_Response {

	/**
	 * Gets the batch search ID from the response.
	 *
	 * @since 3.6.0
	 *
	 * @return string|null Batch search ID or null if not found
	 */
	public function get_batch_id() {
		if (is_object($this->response_data)) {
			return isset($this->response_data->id) ? $this->response_data->id : null;
		} elseif (is_array($this->response_data)) {
			return isset($this->response_data['id']) ? $this->response_data['id'] : null;
		}
		
		return null;
	}

	/**
	 * Gets the batch search status from the response.
	 *
	 * @since 3.6.0
	 *
	 * @return string|null Batch search status or null if not found
	 */
	public function get_batch_status() {
		if (is_object($this->response_data)) {
			return isset($this->response_data->status) ? $this->response_data->status : null;
		} elseif (is_array($this->response_data)) {
			return isset($this->response_data['status']) ? $this->response_data['status'] : null;
		}
		
		return null;
	}

	/**
	 * Gets the batch search message from the response.
	 *
	 * @since 3.6.0
	 *
	 * @return string|null Batch search message or null if not found
	 */
	public function get_batch_message() {
		if (is_object($this->response_data)) {
			return isset($this->response_data->message) ? $this->response_data->message : null;
		} elseif (is_array($this->response_data)) {
			return isset($this->response_data['message']) ? $this->response_data['message'] : null;
		}
		
		return null;
	}

	/**
	 * Gets a formatted response array for the frontend.
	 *
	 * @since 3.6.0
	 *
	 * @return array Formatted response data
	 */
	public function get_formatted_response() {
		return array(
			'batch_id' => $this->get_batch_id(),
			'status' => $this->get_batch_status(),
			'message' => $this->get_batch_message(),
		);
	}

	/**
	 * Checks if the batch search was successfully created.
	 *
	 * @since 3.6.0
	 *
	 * @return bool True if batch search was created successfully
	 */
	public function is_batch_created() {
		$batch_id = $this->get_batch_id();
		$status = $this->get_batch_status();
		
		// Check for successful status indicators
		$has_valid_id = ! empty($batch_id) && $batch_id !== 'null';
		$has_valid_status = ! empty($status) && $status !== 'null';
		$has_success_status = $has_valid_status && 
			(strpos(strtolower($status), 'accepted') !== false || 
			  strpos(strtolower($status), 'processing') !== false);
		
		// Additional check: ensure we don't have error messages
		$has_error_message = $this->has_ndi_error();
		
		return $has_valid_id && $has_valid_status && $has_success_status && !$has_error_message;
	}
}

