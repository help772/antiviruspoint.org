<?php

namespace WcPaysafe\Api\Vault\Responses;

use Paysafe\CustomerVault\EFTBankaccounts;
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
class Eft extends Commons_Bank {
	
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
		'transitNumber'     => 'string',
		'institutionId'     => 'string',
		'lastDigits'        => 'string',
		'billingAddressId'  => 'string',
		'paymentToken'      => 'string',
		'profileID'         => 'string',
		'singleUseToken'    => 'string',
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param EFTBankaccounts $response
	 */
	public function __construct( EFTBankaccounts $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * @return string
	 */
	public function get_institution_id() {
		return $this->data->institutionId;
	}
	
	public function bank_type() {
		return 'eft';
	}
	
	/**
	 * @return string
	 */
	public function get_routing_number() {
		return $this->data->transitNumber;
	}
}