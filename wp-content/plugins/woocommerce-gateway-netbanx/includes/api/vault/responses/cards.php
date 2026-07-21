<?php

namespace WcPaysafe\Api\Vault\Responses;

use Paysafe\CustomerVault\Card;
use WcPaysafe\Api\Response_Abstract;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Cards extends Commons_Vault {
	
	protected static $fieldTypes = array(
		'id'                   => 'string',
		'nickName'             => 'string',
		'status'               => array(
			'INITIAL',
			'ACTIVE'
		),
		'merchantRefNum'       => 'string',
		'holderName'           => 'string',
		'cardNum'              => 'string',
		'cardBin'              => 'string',
		'lastDigits'           => 'string',
		'cardExpiry'           => '\Paysafe\CustomerVault\CardExpiry',
		'cardType'             => 'string',
		'billingAddressId'     => 'string',
		'defaultCardIndicator' => 'bool',
		'paymentToken'         => 'string',
		'error'                => '\Paysafe\Error',
		'links'                => 'array:\Paysafe\Link',
		'profileID'            => 'string',
		'singleUseToken'       => 'string',
		'billingAddress'       => '\Paysafe\CustomerVault\BillingAddress'
	);
	
	public function get_data_type() {
		return 'cards';
	}
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Card $response
	 */
	public function __construct( Card $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * @return string
	 */
	public function get_last_digits() {
		return str_pad( $this->data->lastDigits, 4, '*', STR_PAD_LEFT );
	}
	
	/**
	 * @return string
	 */
	public function get_card_type() {
		return isset( $this->data->cardType ) ? $this->data->cardType : 'Card';
	}
	
	/**
	 * @return string
	 */
	public function get_expiry_month() {
		return $this->data->cardExpiry->month;
	}
	
	/**
	 * @return string
	 */
	public function get_expiry_year() {
		return $this->data->cardExpiry->year;
	}
	
	/**
	 * @return string
	 */
	public function get_card_category() {
		return $this->data->cardCategory;
	}
	
	/**
	 * @return string
	 */
	public function get_risk_code() {
		return $this->data->riskReasonCode;
	}
	
	/**
	 * @return string
	 */
	public function get_account_holder_name() {
		return $this->data->holderName;
	}
	
	public function get_default_card_indicator() {
		return $this->data->defaultCardIndicator;
	}
}