<?php

namespace WcPaysafe\Api\Cards\Responses;

use Paysafe\CardPayments\Refund;

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
class Refunds extends Commons {
	
	protected static $fieldTypes = array(
		'id'               => 'string',
		'merchantRefNum'   => 'string',
		'amount'           => 'int',
		'childAccountNum'  => 'string',
		'dupCheck'         => 'bool',
		'txnTime'          => 'string',
		'status'           => 'string',
		'riskReasonCode'   => 'array:int',
		'acquirerResponse' => '\Paysafe\CardPayments\AcquirerResponse',
		'error'            => '\Paysafe\Error',
		'links'            => 'array:\Paysafe\Link',
		'settlementID'     => 'string',
		'splitpay'         => 'array:\Paysafe\CardPayments\SplitPay',
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Refund $response
	 */
	public function __construct( Refund $response ) {
		parent::__construct( $response );
	}
	
	public function get_settlement_id() {
		return $this->data->settlementID;
	}
}