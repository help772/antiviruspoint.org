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

defined('ABSPATH') or exit;

/**
 * Onboarding Transaction Push API Response.
 *
 * Handles the response from pushing transaction data to the Onboarding API.
 *
 * Response structure:
 * {
 *   "status": "success",
 *   "message": "File uploaded successfully",
 *   "data": {
 *     "fileName": "...",
 *     "firstCall": true,
 *     "s3Bucket": "...",
 *     "s3Url": "...",
 *     "connectorId": "...",
 *     "accountId": "...",
 *     "companyId": "...",
 *     "erpCompanyId": null,
 *     "timeTakenSeconds": 1.56,
 *     "timeTakenMs": 1560,
 *     "fileSizeMB": 0,
 *     "s3Key": "...",
 *     "kafkaIngestionTime": "..."
 *   }
 * }
 *
 * @since 3.6.4
 */
class WC_AvaTax_Onboarding_Transaction_Push_Response extends WC_AvaTax_API_Response {


	/**
	 * Checks if the push was successful.
	 *
	 * @since 3.6.4
	 *
	 * @return bool True if successful
	 */
	public function is_successful()
	{
		return $this->response_data
			&& isset($this->response_data->status)
			&& 'success' === $this->response_data->status;
	}


	/**
	 * Gets the response message.
	 *
	 * @since 3.6.4
	 *
	 * @return string Response message
	 */
	public function get_message()
	{
		return isset($this->response_data->message) ? $this->response_data->message : '';
	}


	/**
	 * Gets the response data object.
	 *
	 * @since 3.6.4
	 *
	 * @return object|null Response data object
	 */
	public function get_data()
	{
		return isset($this->response_data->data) ? $this->response_data->data : null;
	}


	/**
	 * Gets the HTTP status code for the response.
	 *
	 * @since 3.6.4
	 *
	 * @return int HTTP status code
	 */
	protected function get_http_status_code()
	{
		return isset($this->response_code) ? (int) $this->response_code : 200;
	}

	/**
	 * Checks if the response has errors.
	 *
	 * Overrides parent method to work with array-based errors.
	 *
	 * @since 3.6.4
	 *
	 * @return bool True if there are errors
	 */
	public function has_errors()
	{
		$errors = $this->get_errors();
		return ! empty($errors);
	}

	/**
	 * Gets error codes from the response.
	 *
	 * This method provides compatibility with parent class expectations
	 * while working with the array-based error format.
	 *
	 * @since 3.6.4
	 *
	 * @return array Array of error codes (numeric indices)
	 */
	public function get_error_codes()
	{
		$errors = $this->get_errors();
		return ! empty($errors) ? array_keys($errors) : [];
	}

	/**
	 * Gets the errors from the response.
	 *
	 * Handles multiple error formats:
	 * - FastAPI validation errors (detail array with type, loc, msg)
	 * - Simple errors array
	 * - HTTP status code errors (401, 402, 422)
	 *
	 * @since 3.6.4
	 *
	 * @return array Array of error messages
	 */
	public function get_errors()
	{
		$errors = [];

		// Handle HTTP status code errors
		$statusCcode = $this->get_http_status_code();
		if (in_array($statusCcode, [ 401, 402, 422 ], true)) {
			$errorPrefix = $this->get_status_code_error_prefix($statusCcode);

            // Check for FastAPI validation errors (detail array)
            if (isset($this->response_data->detail) && is_array($this->response_data->detail)) {
                foreach ($this->response_data->detail as $detail) {
                    $errors[] = $this->format_validation_error($detail, $errorPrefix);
                }
            } elseif (isset($this->response_data->detail) && is_string($this->response_data->detail)) {
                // Handle string detail (e.g., {"detail": "Authentication failed"})
                $errors[] = $errorPrefix . $this->response_data->detail;
            } elseif (isset($this->response_data->message)) { // Check for simple message
                $errors[] = $errorPrefix . $this->response_data->message;
            } else {
                // Generic status code error
                $errors[] = $errorPrefix . $this->get_default_status_message($statusCcode);
            }
		} elseif (isset($this->response_data->errors) && is_array($this->response_data->errors)) {
			// Handle errors array (standard format)
			$errors = $this->response_data->errors;
		} elseif (isset($this->response_data->message) && ! $this->is_successful()) {
			// Handle single error message
			$errors[] = $this->response_data->message;
		}

		return $errors;
	}


	/**
	 * Formats a validation error from FastAPI/Pydantic format.
	 *
	 * @since 3.6.4
	 *
	 * @param object $detail Validation error detail object
	 * @param string $prefix Optional prefix for the error message
	 * @return string Formatted error message
	 */
	private function format_validation_error($detail, $prefix = '')
	{
		if (! is_object($detail)) {
			return $prefix . 'Invalid error format';
		}

		$field = '';
		if (isset($detail->loc) && is_array($detail->loc)) {
			// Extract field name from location (e.g., ["body", "file"] -> "file")
			$field = end($detail->loc);
		}

		$message = isset($detail->msg) ? $detail->msg : 'Validation error';

		// Build error message
		if ($field) {
			return sprintf('%s%s: %s', $prefix, ucfirst($field), $message);
		}

		return $prefix . $message;
	}


	/**
	 * Gets the error prefix for a given HTTP status code.
	 *
	 * @since 3.6.4
	 *
	 * @param int $statusCcode HTTP status code
	 * @return string Error prefix
	 */
	private function get_status_code_error_prefix($statusCcode)
	{
		$errorPrefix = '';
		switch ($statusCcode) {
			case 401:
				$errorPrefix = '[Unauthorized] ';
				break;
			case 402:
				$errorPrefix = '[Payment Required] ';
				break;
			case 422:
				$errorPrefix = '[Validation Error] ';
				break;
			default:
				$errorPrefix = '[Error ' . $statusCcode . '] ';
				break;
		}
		return $errorPrefix;
	}


	/**
	 * Gets the default error message for a given HTTP status code.
	 *
	 * @since 3.6.4
	 *
	 * @param int $statusCcode HTTP status code
	 * @return string Default error message
	 */
	private function get_default_status_message($statusCcode)
	{
		$defaultErrorMessage = '';
		switch ($statusCcode) {
			case 401:
				$defaultErrorMessage = 'Authentication failed. Please check your API credentials.';
				break;
			case 402:
				$defaultErrorMessage = 'Payment is required to access this resource.';
				break;
			case 422:
				$defaultErrorMessage = 'The request data is invalid or incomplete.';
				break;
			default:
				$defaultErrorMessage = 'An error occurred while processing the request.';
				break;
		}
		return $defaultErrorMessage;
	}
}


