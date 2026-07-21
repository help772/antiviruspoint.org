<?php

namespace WcPaysafe\Api\Direct_Debit\Responses;

use WcPaysafe\Api\Response_Abstract;

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
class Commons extends Response_Abstract {
	
	/**
	 * @return \Paysafe\JSONObject
	 */
	public function bank() {
		return $this->get_data();
	}
	
	/**
	 * Returns the specific bank type of the API response.
	 * Values: 'ach', 'bacs', 'eft', 'sepa'
	 *
	 * @return string
	 */
	public function bank_type() {
		return '';
	}
	
	/**
	 * @return string
	 */
	public function get_last_digits() {
		return str_pad( $this->bank() ? $this->bank()->lastDigits : '****', 4, '*', STR_PAD_LEFT );
	}
	
	/**
	 * @return string
	 */
	public function get_account_type() {
		return $this->bank() ? $this->bank()->accountType : '';
	}
	
	/**
	 * @return string
	 */
	public function get_account_holder_name() {
		return $this->bank() ? $this->bank()->accountHolderName : '';
	}
	
	/**
	 * @return string
	 */
	public function get_account_number() {
		return $this->bank() ? $this->bank()->accountNumber : '';
	}
	
	/**
	 * @return string
	 */
	public function get_routing_number() {
		return $this->bank() ? $this->bank()->routingNumber : '';
	}
	
	/**
	 * @return string
	 */
	public function get_pay_method() {
		return $this->bank() ? $this->bank()->payMethod : '';
	}
	
	/**
	 * @return string
	 */
	public function get_payment_descriptor() {
		return $this->bank() ? $this->bank()->paymentDescriptor : '';
	}
	
	/**
	 * @return string
	 */
	public function get_merchant_reference_number() {
		return $this->data->merchantRefNum;
	}
	
	/**
	 * @return string
	 */
	public function get_institution_id() {
		return '';
	}
}