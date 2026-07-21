<?php

namespace WcPaysafe\Api\Cards\Responses;

use Paysafe\CardPayments\Verification;

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
class Verifications extends Commons {
	
	protected static $fieldTypes = array(
		'id'                 => 'string',
		'merchantRefNum'     => 'string',
		'childAccountNum'    => 'string',
		'card'               => '\Paysafe\CardPayments\Card',
		'authCode'           => 'string',
		'profile'            => '\Paysafe\CardPayments\Profile',
		'billingDetails'     => '\Paysafe\CardPayments\BillingDetails',
		'customerIp'         => 'string',
		'dupCheck'           => 'bool',
		'merchantDescriptor' => '\Paysafe\CardPayments\MerchantDescriptor',
		'description'        => 'string',
		'txnTime'            => 'string',
		'currencyCode'       => 'string',
		'avsResponse'        => array(
			'MATCH',
			'PARTIAL_MATCH_ADDRESS',
			'PARTIAL_MATCH_ZIP',
			'NO_MATCH',
			'NOT_PROCESSED',
			'UNKNOWN'
		),
		'cvvVerification'    => array(
			'MATCH',
			'NO_MATCH',
			'NOT_PROCESSED',
			'UNKNOWN'
		),
		'status'             => 'string',
		'riskReasonCode'     => 'array:int',
		'acquirerResponse'   => '\Paysafe\CardPayments\AcquirerResponse',
		'error'              => '\Paysafe\Error',
		'links'              => 'array:\Paysafe\Link'
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Verification $response
	 */
	public function __construct( Verification $response ) {
		parent::__construct( $response );
	}
}