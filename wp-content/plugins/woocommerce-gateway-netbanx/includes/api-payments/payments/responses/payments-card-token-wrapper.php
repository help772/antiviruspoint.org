<?php

namespace WcPaysafe\Api_Payments\Payments\Responses;

use WcPaysafe\Api_Payments\Response_Abstract;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Payments_Card_Token_Wrapper extends Response_Abstract {
	
	public function get_data_type() {
		return 'payments_card';
	}
	
	/**
	 * @return string
	 */
	public function get_last_digits() {
		return str_pad( $this->data->card->lastDigits, 4, '*', STR_PAD_LEFT );
	}
	
	/**
	 * @return string
	 */
	public function get_card_type() {
		return isset( $this->data->card->cardType ) ? $this->data->card->cardType : 'Card';
	}
	
	/**
	 * @return string
	 */
	public function get_expiry_month() {
		return $this->data->card->cardExpiry->month;
	}
	
	/**
	 * @return string
	 */
	public function get_expiry_year() {
		return $this->data->card->cardExpiry->year;
	}
	
	/**
	 * @return string
	 */
	public function get_card_category() {
		return $this->data->card->cardCategory;
	}
	
	/**
	 * @return string
	 */
	public function get_account_holder_name() {
		return $this->data->card->holderName;
	}
	
	/**
	 * @return string
	 */
	public function get_payment_handle_token() {
		return $this->data->paymentHandleToken;
	}
	
	public function get_payment_token() {
		return isset( $this->data->multiUsePaymentHandleToken ) ? $this->data->multiUsePaymentHandleToken : '';
	}
	
	public function get_customer_id() {
		return isset( $this->data->customerId ) ? $this->data->customerId : '';
	}
	
	public function get_merchant_customer_id() {
		$value = $this->data->profile->merchantCustomerId;
		
		if ( ! $value ) {
			$value = $this->data->merchantCustomerId;
		}
		
		return $value;
	}
	
	public function get_card() {
		return isset( $this->data->card ) ? $this->data->card : false;
	}
	
	public function set_card( $value ) {
		$this->data->card = $value;
	}
	
	public function set_multi_use_payment_handle_token( $value ) {
		$this->data->multiUsePaymentHandleToken = $value;
	}
	
	public function set_customer_id( $value ) {
		$this->data->customerId = $value;
	}
}