<?php

namespace WcPaysafe\Api\Alternate_Payments\Responses;

use Paysafe\AlternatePayments\Payment;

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
class Payments extends Commons {
	
	protected static $fieldTypes = array(
		'id'                      => 'string',
		'gatewayReconciliationId' => 'string',
		'merchantRefNum'          => 'string',
		'dupCheck'                => 'bool',
		'consumerIp'              => 'string',
		'settleWithAuth'          => 'bool',
		'amount'                  => 'int',
		'verificationId'          => 'string',
		'currencyCode'            => 'string',
		'availableToSettle'       => 'int',
		'availableToRefund'       => 'int',
		'profile'                 => '\Paysafe\AlternatePayments\Profile',
		'billingDetails'          => '\Paysafe\AlternatePayments\BillingDetails',
		'shippingDetails'         => '\Paysafe\AlternatePayments\ShippingDetails',
		'returnLinks'             => 'array:\Paysafe\Link',
		'links'                   => 'array:\Paysafe\Link',
		'txnTime'                 => 'string',
		'updatedTime'             => 'string',
		'statusTime'              => 'string',
		'error'                   => '\Paysafe\Error',
		'status'                  => array(
			'RECEIVED',
			'INITIATED',
			'PROCESSING',
			'COMPLETED',
			'CANCELLED',
			'FAILED',
			'ERROR',
			'EXPIRED',
		),
		'statusReason'            => array(
			'USER_CANCELLED',
			'MERCHANT_CANCELLED',
			'AUTH_VOIDED',
			'AUTH_EXPIRED',
		),
		'gatewayResponse'         => '\Paysafe\AlternatePayments\GatewayResponse',
		'paymentType'             => array(
			'BOKU',
			'GIROPAY',
			'INTERAC',
			'NETELLER',
			'PAYSAFECARD',
			'PAYOLUTION',
			'RAPIDTRANSFER',
			'SKRILL',
			'SOFORT',
		),
		'interac'                 => '\Paysafe\AlternatePayments\PaymentTypes\Interac',
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Payment $response
	 */
	public function __construct( Payment $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * Returns true of the response contains direct debit data
	 *
	 * @return bool
	 */
	public function is_alternate_payment() {
		return true;
	}
}