<?php

namespace WcPaysafe\Api\Cards\Responses;

use WcPaysafe\Api\Response_Abstract;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Commons extends Response_Abstract {
	
	public function get_available_to_settle() {
		return $this->get_data()->availableToSettle;
	}
	
	public function get_amount() {
		return $this->data->amount;
	}
	
	public function get_last_digits() {
		return isset( $this->data->card ) ? $this->data->card->lastDigits : '****';
	}
	
	public function get_expiry_month() {
		return isset( $this->data->card ) && isset( $this->data->card->expiry ) ? $this->data->card->expiry->month : '';
	}
	
	public function get_expiry_year() {
		return isset( $this->data->card ) && isset( $this->data->card->expiry ) ? $this->data->card->expiry->year : '';
	}
	
	public function get_card_type() {
		return isset( $this->data->card ) ? $this->data->card->type : 'Card';
	}
	
	public function get_currency_code() {
		return $this->data->currencyCode;
	}
	
	public function get_risk_code() {
		return $this->data->riskReasonCode;
	}
	
	public function get_settlement_id() {
		return '';
	}
	
	public function get_auth_code() {
		return $this->data->authCode;
	}
	
	public function get_settlements() {
		return array();
	}
}