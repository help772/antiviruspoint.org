<?php

namespace WcPaysafe\Api\Cards\Responses;

use Paysafe\CardPayments\Authorization;

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
class Authorizations extends Commons {
	
	protected static $fieldTypes = array(
		'id'                     => 'string',
		'merchantRefNum'         => 'string',
		'amount'                 => 'int',
		'settleWithAuth'         => 'bool',
		'availableToSettle'      => 'int',
		'childAccountNum'        => 'string',
		'card'                   => '\Paysafe\CardPayments\Card',
		'authentication'         => '\Paysafe\CardPayments\Authentication',
		'authCode'               => 'string',
		'profile'                => '\Paysafe\CardPayments\Profile',
		'billingDetails'         => '\Paysafe\CardPayments\BillingDetails',
		'shippingDetails'        => '\Paysafe\CardPayments\ShippingDetails',
		'storedCredential'       => '\Paysafe\CardPayments\StoredCredential',
		'recurring'              => array(
			'INITIAL',
			'RECURRING'
		),
		'customerIp'             => 'string',
		'dupCheck'               => 'bool',
		'keywords'               => 'array:string',
		'merchantDescriptor'     => '\Paysafe\CardPayments\MerchantDescriptor',
		'accordD'                => '\Paysafe\CardPayments\AccordD',
		'description'            => 'string',
		'masterPass'             => '\Paysafe\CardPayments\MasterPass',
		'txnTime'                => 'string',
		'currencyCode'           => 'string',
		'avsResponse'            => array(
			'MATCH',
			'MATCH_ADDRESS_ONLY',
			'MATCH_ZIP_ONLY',
			'NO_MATCH',
			'NOT_PROCESSED',
			'UNKNOWN'
		),
		'cvvVerification'        => array(
			'MATCH',
			'NO_MATCH',
			'NOT_PROCESSED',
			'UNKNOWN'
		),
		'status'                 => array(
			'RECEIVED',
			'COMPLETED',
			'HELD',
			'FAILED',
			'CANCELLED'
		),
		'riskReasonCode'         => 'array:int',
		'acquirerResponse'       => '\Paysafe\CardPayments\AcquirerResponse',
		'visaAdditionalAuthData' => '\Paysafe\CardPayments\VisaAdditionalAuthData',
		'settlements'            => 'array:\Paysafe\CardPayments\Settlement',
		'error'                  => '\Paysafe\Error',
		'links'                  => 'array:\Paysafe\Link',
		'splitpay'               => 'array:\Paysafe\CardPayments\SplitPay',
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Authorization $response
	 */
	public function __construct( Authorization $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * Is the transaction authorized and captured in the same request
	 * @return bool
	 */
	public function get_settle_with_auth() {
		return isset( $this->data->settleWithAuth ) ? $this->data->settleWithAuth : false;
	}
	
	public function get_settlements() {
		return isset( $this->get_data()->settlements ) ? $this->get_data()->settlements : array();
	}
}