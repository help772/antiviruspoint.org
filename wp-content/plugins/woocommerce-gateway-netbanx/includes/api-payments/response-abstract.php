<?php

namespace WcPaysafe\Api_Payments;

use Paysafe\Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Response_Abstract {
	
	public $data;
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param string|\stdClass $data
	 */
	public function __construct( $data ) {
		if ( is_string( $data ) && $this->is_json( $data ) ) {
			$data = json_decode( $data );
		}
		
		if ( is_object( $data ) && is_callable( [ $data, 'get_data' ] ) ) {
			$data = $data->get_data();
		}
		
		$this->data = $data;
	}
	
	function is_json( $string ) {
		if ( ! is_string( $string ) ) {
			return false;
		}
		json_decode( $string );
		
		return json_last_error() === JSON_ERROR_NONE;
	}
	
	/**
	 * We want any call to a not declared prop to attempt to get the value from the response
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( isset( $this->data->{$name} ) ) {
			return $this->data->{$name};
		}
		
		return null;
	}
	
	public function get_data() {
		return $this->data;
	}
	
	/**
	 * The type of the API response.
	 * We have currently three(3) types: 'cards', 'dd' and 'vault'. Default: ''
	 *
	 * Extend this method to provide the correct type
	 *
	 * @return string
	 */
	public function get_data_type() {
		return '';
	}
	
	/**
	 * @return string
	 */
	public function get_id() {
		return $this->data->id;
	}
	
	/**
	 * @return string
	 */
	public function get_status() {
		return $this->data->status;
	}
	
	/**
	 * @return int
	 */
	public function get_amount() {
		return $this->data->amount;
	}
	
	public function get_currency_code() {
		return $this->data->currencyCode;
	}
	
	/**
	 * @return string
	 */
	public function get_payment_token() {
		return $this->data->paymentToken;
	}
	
	/**
	 * @return bool|Error
	 */
	public function get_error() {
		return ! empty( $this->data->error ) ? $this->data->error : false;
	}
	
	/**
	 * @return string
	 */
	public function get_error_code() {
		$error = $this->get_error();
		
		if ( ! $error ) {
			return '';
		}
		
		return $error->code;
	}
	
	/**
	 * @return string
	 */
	public function get_error_message() {
		$error = $this->get_error();
		
		if ( ! $error ) {
			return '';
		}
		
		$message = $error->message;
		
		if ( ! empty( $error->details ) ) {
			if ( is_array( $error->details ) ) {
				$message .= '.';
				foreach ( $error->details as $err_details ) {
					$message .= ' ' . $err_details;
				}
			} else {
				$message .= ' ' . $error->details;
			}
		}
		
		if ( ! empty( $error->fieldErrors ) ) {
			$message .= ' ';
			foreach ( $error->fieldErrors as $err_details ) {
				$message .= __( 'Field: ', 'wc_paysafe' ) . $err_details->field;
				$message .= ' ' . $err_details->error;
			}
		}
		
		return $message;
	}
	
	/**
	 * Gets potential error code and message from the payment response
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	function get_errors_from_response() {
		$error_code    = '';
		$error_message = '';
		if ( $this->get_error_code() ) {
			$error_code = ' Error Code: ' . $this->get_error_code();
		}
		
		if ( $this->get_error_message() ) {
			$error_message = ' Error Message: ' . $this->get_error_message();
		}
		
		return sprintf( '%s%s', $error_code, $error_message );
	}
	
	/**
	 * Is the transaction authorized and captured in the same request
	 * @return bool
	 */
	public function get_settle_with_auth() {
		return true;
	}
	
	/**
	 * Returns true of the response contains direct debit data
	 *
	 * @return bool
	 */
	public function is_direct_debit() {
		return false;
	}
}