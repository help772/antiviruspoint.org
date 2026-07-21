<?php

namespace WcPaysafe\Api\Vault\Responses;

use Paysafe\CustomerVault\BACSBankaccounts;
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
class Bacs extends Commons_Bank {
	
	protected static $fieldTypes = array(
		'id'                => 'string',
		'nickName'          => 'string',
		'merchantRefNum'    => 'string',
		'status'            => array(
			'ACTIVE',
			'INVALID',
			'INACTIVE'
		),
		'statusReason'      => 'string',
		'accountNumber'     => 'string',
		'accountHolderName' => 'string',
		'sortCode'          => 'string',
		'lastDigits'        => 'string',
		'billingAddressId'  => 'string',
		'paymentToken'      => 'string',
		'profileID'         => 'string',
		'singleUseToken'    => 'string',
		'mandates'          => 'array:\Paysafe\CustomerVault\Mandates'
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param BACSBankaccounts $response
	 */
	public function __construct( BACSBankaccounts $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * @return \Paysafe\CustomerVault\Mandates|array
	 */
	public function get_mandates() {
		return $this->data->mandates;
	}
	
	/**
	 * @return string
	 */
	public function get_routing_number() {
		return $this->data->sortCode;
	}
	
	public function bank_type() {
		return 'bacs';
	}
}