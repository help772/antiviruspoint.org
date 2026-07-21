<?php

namespace WcPaysafe\Api\Vault\Responses;

use Paysafe\CustomerVault\ACHBankaccounts;

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
class Ach extends Commons_Bank {
	
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
		'routingNumber'     => 'string',
		'accountType'       => array(
			'CHECKING',
			'LOAN',
			'SAVINGS'
		),
		'lastDigits'        => 'string',
		'billingAddressId'  => 'string',
		'paymentToken'      => 'string',
		'singleUseToken'    => 'string',
		'profileID'         => 'string',
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param ACHBankaccounts $response
	 */
	public function __construct( ACHBankaccounts $response ) {
		parent::__construct( $response );
	}
	
	public function bank_type() {
		return 'ach';
	}
}