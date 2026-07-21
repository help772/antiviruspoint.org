<?php

namespace WcPaysafe\Api\Alternate_Payments\Responses;

use Paysafe\AlternatePayments\Refund;

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
		'id'                      => 'string',
		'gatewayReconciliationId' => 'string',
		'merchantRefNum'          => 'string',
		'amount'                  => 'int',
		'currencyCode'            => 'string',
		'paymentType'             => 'string',
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
		'gatewayResponse'         => '\Paysafe\AlternatePayments\GatewayResponse',
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
		return $this->data->gatewayResponse->id;
	}
}