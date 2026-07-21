<?php

namespace WcPaysafe\Api\Cards\Responses;

use Paysafe\CardPayments\Settlement;
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
class Settlements extends Commons {
	
	protected static $fieldTypes = array(
		'id'                => 'string',
		'merchantRefNum'    => 'string',
		'amount'            => 'int',
		'availableToRefund' => 'int',
		'childAccountNum'   => 'string',
		'txnTime'           => 'string',
		'dupCheck'          => 'bool',
		'status'            => array(
			'RECEIVED',
			'PENDING',
			'PROCESSING',
			'COMPLETED',
			'FAILED',
			'CANCELLED'
		),
		'riskReasonCode'    => 'array:int',
		'acquirerResponse'  => '\Paysafe\CardPayments\AcquirerResponse',
		'error'             => '\Paysafe\Error',
		'links'             => 'array:\Paysafe\Link',
		'authorizationID'   => 'string',
		'splitpay'          => 'array:\Paysafe\CardPayments\SplitPay',
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Settlement $response
	 */
	public function __construct( Settlement $response ) {
		parent::__construct( $response );
	}
	
	public function available_to_refund() {
		return $this->data->availableToRefund;
	}
	
	public function get_authorization_id() {
		return $this->data->authorizationID;
	}
}