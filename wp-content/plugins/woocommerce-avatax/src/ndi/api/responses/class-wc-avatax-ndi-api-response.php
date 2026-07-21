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
 * The NDI API base response class.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API_Response extends \WC_AvaTax_API_Response {

    /**
     * Checks if the response contains an error.
     * NDI API specific error handling.
     *
     * @since 3.6.0
     *
     * @return bool Returns true if an error exists, false otherwise.
     */
    public function has_ndi_error() {
        // Check for various error indicators in NDI API responses
        if (isset($this->response_data->error) || 
            isset($this->response_data->ErrorCode) ||
            isset($this->response_data->error_description) ||
            $this->has_errors()) {
            return true;
        }
        
        // Check for error messages that indicate API failures
        if (isset($this->response_data->Message) && 
            (strpos(strtolower($this->response_data->Message), 'required field') !== false ||
             strpos(strtolower($this->response_data->Message), 'missing') !== false ||
             strpos(strtolower($this->response_data->Message), 'error') !== false)) {
            return true;
        }
        
        // Check for missing required fields in success responses
        if (isset($this->response_data->Message) && 
            !isset($this->response_data->id) && 
            !isset($this->response_data->status)) {
            return true;
        }
        
        return false;
    }

    /**
     * Gets the NDI error message from the response.
     *
     * @since 3.6.0
     *
     * @return string The error message if it exists, empty string otherwise.
     */
    public function get_ndi_error_message() {
        // Check for structured error objects first
        if (isset($this->response_data->error->message)) {
            return $this->response_data->error->message;
        }
        
        // Check for NDI API specific error messages
        if (isset($this->response_data->Message)) {
            return $this->response_data->Message;
        }
        
        // Check for error descriptions
        if (isset($this->response_data->error_description)) {
            return $this->response_data->error_description;
        }
        
        // Check for simple error strings
        if (isset($this->response_data->error)) {
            return is_string($this->response_data->error) ? $this->response_data->error : 'API Error';
        }
        
        // Check for error codes
        if (isset($this->response_data->ErrorCode)) {
            $error_msg = 'Error Code: ' . $this->response_data->ErrorCode;
            if (isset($this->response_data->Message)) {
                $error_msg .= ' - ' . $this->response_data->Message;
            }
            return $error_msg;
        }
        
        // Fall back to framework error handling
        if ($this->has_errors()) {
            $errors = $this->get_errors();
            if ($errors && method_exists($errors, 'get_error_message')) {
                return $errors->get_error_message();
            }
        }
        
        return 'Unknown API Error';
    }

    /**
     * Gets the record set count from OData response.
     *
     * @since 3.6.0
     *
     * @return int The total record count, 0 if not available.
     */
    public function get_record_set_count() {
        return isset($this->response_data->{'@recordSetCount'}) ? 
               (int) $this->response_data->{'@recordSetCount'} : 0;
    }

    /**
     * Gets the next link from OData response for pagination.
     *
     * @since 3.6.0
     *
     * @return string The next link URL, empty string if not available.
     */
    public function get_next_link() {
        return isset($this->response_data->{'@nextLink'}) ? 
               $this->response_data->{'@nextLink'} : '';
    }

    /**
     * Gets the value array from OData response.
     *
     * @since 3.6.0
     *
     * @return array The value array, empty array if not available.
     */
    public function get_value() {
        return isset($this->response_data->value) && is_array($this->response_data->value) ? 
               $this->response_data->value : [];
    }
}

