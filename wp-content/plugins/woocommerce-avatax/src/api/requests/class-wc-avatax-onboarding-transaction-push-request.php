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
 * Onboarding Transaction Push API Request.
 *
 * Handles the request for pushing transaction data to the onboarding API
 * using multipart/form-data with file upload.
 *
 * @since 3.6.4
 */
class WC_AvaTax_Onboarding_Transaction_Push_Request extends WC_AvaTax_API_Request
{
	/** @var array Transaction data */
	protected $transactionData;

	/** @var string Account ID */
	protected $accountId;

	/** @var string Company ID */
	protected $companyId;

	/** @var string Connector ID */
	protected $connectorId;

	/** @var bool First call flag */
	protected $firstCall;

	/** @var string Temporary file path */
	protected $tempFilePath;

	/** @var string Multipart boundary */
	protected $boundary;


	/**
	 * Constructs the class.
	 *
	 * @since 3.6.4
	 */
	public function __construct()
	{
		$this->method = 'POST';
		$this->path   = '/data/ssgl'; // Onboarding API endpoint
		$this->boundary = '----WebKitFormBoundary' . md5(time());
	}


	/**
	 * Sets the transaction data for the request.
	 *
	 * @since 3.6.4
	 *
	 * @param array $data Transaction data array
	 * @param string $accountId Avalara account ID
	 * @param string $companyId Avalara company ID
	 * @param string $connectorId Avalara connector ID
	 * @param bool $firstCall Whether this is the first call
	 */
	public function set_transaction_data($data, $accountId, $companyId, $connectorId, $firstCall = true)
	{
		$this->transactionData = $data;
		$this->accountId = $accountId;
		$this->companyId = $companyId;
		$this->connectorId = $connectorId;
		$this->firstCall = $firstCall;

		$this->data = $this->prepare_data();
	}


	/**
	 * Gets the request data as multipart form data.
	 *
	 * For first call: Creates temp file with transaction data and includes in upload
	 * For subsequent calls: Skips file creation, sends only metadata parameters
	 *
	 * @since 3.6.4
	 *
	 * @return string Multipart form data body
	 */
	public function prepare_data()
	{
		$formFields = [
			'connectorId' => $this->connectorId,
			'accountId' => $this->accountId,
			'companyId' => $this->companyId,
			'firstCall' => $this->firstCall ? 'true' : 'false'
		];

		// Build multipart body
		$body = '';

		// Add form fields
		foreach ($formFields as $name => $value) {
			$body .= "--{$this->boundary}\r\n";
			$body .= "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n";
			$body .= "{$value}\r\n";
		}

		// Only create and include file for first call or when there's actual data
		if ($this->firstCall && !empty($this->transactionData)) {
			// Check if transactionData is already a file path (from handler's file-based storage)
			if (is_string($this->transactionData) && file_exists($this->transactionData)) {
				// Use the existing file directly - it was already created with proper JSON formatting
				$this->tempFilePath = $this->transactionData;
				$filename = basename($this->tempFilePath);
				$fileContents = file_get_contents($this->tempFilePath);
			} else {
				// transactionData is an array - create temp file (legacy path)
				$this->tempFilePath = $this->create_temp_file();
				$filename = basename($this->tempFilePath);
				$fileContents = file_get_contents($this->tempFilePath);
			}

			$body .= "--{$this->boundary}\r\n";
			$body .= "Content-Disposition: form-data; name=\"file\"; filename=\"{$filename}\"\r\n";
			$body .= "Content-Type: application/json\r\n\r\n";
			$body .= "{$fileContents}\r\n";
		}

		// Close the multipart body
		$body .= "--{$this->boundary}--\r\n";

		return $body;
	}


	/**
	 * Creates a temporary JSON file with transaction data.
	 *
	 * @since 3.6.4
	 *
	 * @return string Path to the temporary file
	 * @throws Exception If file creation fails or data is empty
	 */
	protected function create_temp_file()
	{
		// Safety check: don't create file for empty data
		if (empty($this->transactionData)) {
			throw new Exception('Cannot create file: transaction data is empty');
		}

		$uploadDir = wp_upload_dir();
		$tempDir = $uploadDir['basedir'] . '/wc-avatax-temp';

		// Create temp directory if it doesn't exist
		if (! file_exists($tempDir)) {
			wp_mkdir_p($tempDir);
		}

		// Create temp file with timestamp
		$timestamp = date('dmYHis');
		$filename = sprintf('transaction_push_%s.json', $timestamp);
		$filepath = $tempDir . '/' . $filename;

		// Write JSON data to file (compact format - no whitespace)
		// Set serialize_precision to -1 to prevent floating-point precision issues in JSON
		// JSON_UNESCAPED_SLASHES: Prevents escaping "/" as "\/" (saves bytes)
		// JSON_UNESCAPED_UNICODE: Outputs UTF-8 directly instead of \uXXXX escapes (saves bytes)
		$oldPrecision = ini_get('serialize_precision');
		ini_set('serialize_precision', -1);
		$jsonData = json_encode($this->transactionData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		ini_set('serialize_precision', $oldPrecision);
		
		if (false === file_put_contents($filepath, $jsonData)) {
			throw new Exception('Failed to create temporary transaction data file');
		}

		return $filepath;
	}


	/**
	 * Cleans up the temporary file after request is complete.
	 *
	 * @since 3.6.4
	 */
	public function cleanup_temp_file()
	{
		if (! empty($this->tempFilePath) && file_exists($this->tempFilePath)) {
			@unlink($this->tempFilePath);
		}
	}


	/**
	 * Gets the request body as a string.
	 *
	 * Overrides parent to handle multipart form data properly.
	 * Returns the multipart body string.
	 *
	 * @since 3.6.4
	 *
	 * @return string The multipart form data body
	 */
	public function to_string()
	{
		return is_string($this->data) ? $this->data : '';
	}


	/**
	 * Gets the Content-Type header for this request.
	 *
	 * @since 3.6.4
	 *
	 * @return string The Content-Type with boundary
	 */
	public function get_content_type()
	{
		return 'multipart/form-data; boundary=' . $this->boundary;
	}


	/**
	 * Gets the request data as a safe string for logging.
	 *
	 * @since 3.6.4
	 *
	 * @return string JSON encoded request data (metadata only, not file contents)
	 */
	public function to_string_safe()
	{

		$data = [
			'connectorId' => $this->connectorId,
			'accountId' => $this->accountId,
			'companyId' => $this->companyId,
			'firstCall' => $this->firstCall ? 'true' : 'false'
		];

		return json_encode($data);
	}


	/**
	 * Destructor to ensure temp file is cleaned up.
	 *
	 * @since 3.6.4
	 */
	public function __destruct()
	{
		$this->cleanup_temp_file();
	}
}


