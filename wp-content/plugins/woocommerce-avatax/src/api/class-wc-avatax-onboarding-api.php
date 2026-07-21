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


use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_Abstract_API;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined('ABSPATH') or exit;

/**
 * The AvaTax Onboarding API.
 *
 * Handles onboarding-specific API calls such as transaction data push.
 *
 * @since 3.6.4
 */
class WC_AvaTax_Onboarding_API extends WC_AvaTax_Abstract_API
{
	
	/** @var string Avalara account ID */
	protected $accountId;

	/** @var string Avalara license key */
	protected $licenseKey;

	/** @var string Avalara company ID */
	protected $companyId;

	/** @var string Avalara connector ID */
	protected $connectorId;

	/**
	 * Construct the API.
	 *
	 * @since 3.6.4
	 *
	 * @param string $accountId Avalara account ID
	 * @param string $licenseKey Avalara license key
	 * @param string $companyId Avalara company ID
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function __construct($accountId, $licenseKey, $companyId, $environment)
	{
		$this->accountId   = $accountId;
		$this->licenseKey  = $licenseKey;
		$this->companyId   = $companyId;
		$this->connectorId = wc_avatax()::CONNECTOR_ID;

		// Set the onboarding API base URI
		$this->request_uri = ('production' === $environment)
			? 'https://ai-onboarding-erp-data-extractor.avalara.com'
			: 'https://ai-onboarding-erp-data-extractor.sbx.avalara.com';

		// Set basic auth credentials
		$this->set_http_basic_auth($this->accountId, $this->licenseKey);

		parent::__construct();
	}

	/**
	 * Sends transaction data to onboarding API for one-time push.
	 *
	 * This method creates a JSON file with transaction data and sends it
	 * via multipart/form-data along with metadata.
	 *
	 * @since 3.6.4
	 *
	 * @param array $payload The transaction data payload
	 * @param bool $firstCall Whether this is the first call in a batch push
	 * @return object Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function send_transaction_data($payload, $firstCall = true)
	{
		
		$request = $this->get_new_request('transaction-push');
		$request->set_transaction_data($payload, $this->accountId, $this->companyId, $this->connectorId, $firstCall);

		// Set the proper Content-Type for multipart/form-data with boundary
		$this->set_request_content_type_header($request->get_content_type());
		
		$this->set_response_handler('WC_AvaTax_Onboarding_Transaction_Push_Response');

		return $this->perform_request($request);
	}

	/**
	 * Allow child classes to validate a response prior to instantiating the
	 * response object.
	 *
	 * @since 3.6.4
	 *
	 * @return bool
	 */
	protected function do_pre_parse_response_validation()
	{
		return true;
	}

	/**
	 * Validate the parsed response data.
	 *
	 * Primarily checks for errors returned by the Onboarding API.
	 *
	 * @since 3.6.4
	 *
	 * @throws Framework\SV_WC_API_Exception
	 * @return bool
	 */
    protected function do_post_parse_response_validation()
    {
        $response = $this->get_response();

        // Check HTTP status code directly from API base class first
        // This is necessary because response_code is stored in API class, not response object
        $isErrorStatusCode = isset($this->response_code) && in_array((int)$this->response_code, [401, 402, 422], true);

        if ($isErrorStatusCode || $response->has_errors()) {
            $errors = $response->get_errors();
            $messages = [];

            // Build error messages from response errors
            if (is_array($errors) && !empty($errors)) {
                $messages = $errors;
            } elseif (!empty($errors)) {
                // Fallback for WP_Error format (if needed)
                foreach ($errors->get_error_codes() as $code) {
                    $messages[] = '[' . $code . '] ' . $errors->get_error_message($code);
                }
            }

            // If no errors from response but we have error status code, use status code message
            if (empty($messages) && $isErrorStatusCode) {
                $statusMessages = [
                    401 => '[Unauthorized] Authentication failed',
                    402 => '[Payment Required] Payment is required',
                    422 => '[Validation Error] Invalid request data'
                ];
                $messages[] = $statusMessages[(int)$this->response_code] ?? 'HTTP ' . $this->response_code . ' error';
            }

            if (!empty($messages)) {
                throw new Framework\SV_WC_API_Exception(implode(' ', $messages));
            }
        }

        return true;
    }

	/**
	 * Builds and returns a new API request object.
	 *
	 * @see Framework\SV_WC_API_Base::get_new_request()
	 *
	 * @since 3.6.4
	 *
	 * @param string $type the desired request type
	 * @param mixed $args optional argument(s) to be passed to the request
	 * @return WC_AvaTax_Onboarding_Transaction_Push_Request
	 * @throws Framework\SV_WC_API_Exception for invalid request types
	 */
	protected function get_new_request($type = '', $args = null)
	{

		switch ($type) {
			case 'transaction-push':
				$this->set_response_handler('WC_AvaTax_Onboarding_Transaction_Push_Response');
				return new WC_AvaTax_Onboarding_Transaction_Push_Request();
				break;
			default:
				throw new Framework\SV_WC_API_Exception('Invalid request type');
				break;
		}
	}
}
