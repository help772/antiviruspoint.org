<?php

namespace WcPaysafe\Api\Vault\Responses;

use Paysafe\CustomerVault\SEPABankaccounts;
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
class Sepa extends Commons_Bank {
	
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
		'iban'              => 'string',
		'bic'               => 'string',
		'accountHolderName' => 'string',
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
	 * @param SEPABankaccounts $response
	 */
	public function __construct( SEPABankaccounts $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * @return \Paysafe\CustomerVault\Mandates|array
	 */
	public function get_mandates() {
		return $this->get_data()->mandates;
	}
	
	/**
	 * Returns the account number for the Sepa ... the iban
	 * @return bool|int|mixed|string|null
	 */
	public function get_account_number() {
		return $this->get_data()->iban;
	}
	
	/**
	 * @return string
	 */
	public function get_routing_number() {
		return $this->get_data()->bic;
	}
	
	public function bank_type() {
		return 'sepa';
	}
}