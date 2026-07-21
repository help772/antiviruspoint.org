<?php

namespace WcPaysafe\Api\Vault\Responses;

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
class Commons_Vault extends Response_Abstract {
	
	/**
	 * @return string
	 */
	public function get_last_digits() {
		$last_digits = $this->data->lastDigits;
		
		if ( ! $last_digits ) {
			$last_digits = $this->get_account_number() ? $this->get_account_number() : '****';
		}
		
		return str_pad( substr( $last_digits, - 4 ), 4, '*', STR_PAD_LEFT );
	}
	
	public function get_account_number() {
		return $this->get_data()->accountNumber;
	}
	
	/**
	 * @return string
	 */
	public function get_payment_token() {
		return $this->data->paymentToken;
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
	public function get_nick_name() {
		return $this->data->nickName;
	}
	
	/**
	 * @return string
	 */
	public function get_account_holder_name() {
		return $this->data->accountHolderName;
	}
	
	/**
	 * @return string
	 */
	public function get_billing_address_id() {
		return $this->data->billingAddressId;
	}
	
	/**
	 * @return string
	 */
	public function get_single_use_token() {
		return $this->data->singleUseToken;
	}
	
	/**
	 * @return string
	 */
	public function get_profile_id() {
		return $this->data->profileID;
	}
}