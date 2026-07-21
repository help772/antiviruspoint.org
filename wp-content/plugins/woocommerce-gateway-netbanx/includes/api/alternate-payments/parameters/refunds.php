<?php

namespace WcPaysafe\Api\Alternate_Payments\Parameters;

use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Parameters_Abstract;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Refunds extends Parameters_Abstract {
	
	/**
	 * @param null   $amount
	 * @param string $reason
	 *
	 * @throws \Exception
	 *
	 * @return array|mixed
	 */
	public function refund_parameters( $amount = null, $reason = '' ) {
		$data_source = $this->get_fields()->get_source();
		
		if ( ! $data_source instanceof Order_Source ) {
			throw new \Exception( __( 'Refunds are only processed against an existing order. Please pass an order source to be able to process the refund', 'wc_paysafe' ) );
		}
		
		$order = $data_source->get_source();
		
		$paysafe_order = new Paysafe_Order( $order );
		$settlement_id = $paysafe_order->get_payment_order_id();
		
		$parameters = array(
			'settlementID'   => $settlement_id,
			'merchantRefNum' => 'refund-' . $paysafe_order->get_order_number() . '-' . $paysafe_order->get_attempts_suffix( 'refund' ),
			'amount'         => Formatting::format_amount( $amount, $order->get_currency() ),
		);
		
		$parameters = apply_filters( 'wc_paysafe_refund_parameters', $parameters, $order, $amount, $reason, $this->get_configuration()->get_gateway() );
		
		return $parameters;
	}
}