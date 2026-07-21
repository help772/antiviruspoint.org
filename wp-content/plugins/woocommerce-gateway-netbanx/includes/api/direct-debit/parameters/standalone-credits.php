<?php

namespace WcPaysafe\Api\Direct_Debit\Parameters;

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
class Standalone_Credits extends Parameters_Abstract {
	
	/**
	 * Returns the refund parameters for an order.
	 * The DD API uses a standalone credit to refund an amount to the customer.
	 *
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
		
		/**
		 * We need a Bank Account Token representation, so
		 *      1. Attempt to get that from a saved token in the store
		 *
		 * - No: If we don't have a token, fail the process
		 * - Yes: If we have a token
		 *      1. Add the appropriate refund method by using the saved Bank Type to the order (eft, bacs)
		 *      2. Add the token to the request
		 *
		 */
		
		$paysafe_order = new Paysafe_Order( $order );
		$token         = $paysafe_order->get_order_profile_token();
		
		if ( ! $token ) {
			throw new \Exception( __( 'To refund a Direct Debit payment we need the customer to have saved their payment method to the Paysafe Vault. This transaction was not saved to the Paysafe Vault, so we cannot refund it.' ) );
		}
		
		$parameters = array(
			$paysafe_order->get_payment_type() => array(
				'paymentToken' => $token,
			),
			
			'merchantRefNum' => 'refund-' . $paysafe_order->get_order_number() . '-' . $paysafe_order->get_attempts_suffix( 'refund' ),
			'amount'         => Formatting::format_amount( $amount, $order->get_currency() ),
			'dupCheck'       => true,
			'billingDetails' => $this->get_fields()->get_billing_fields()
		);
		
		$parameters = apply_filters( 'wc_paysafe_standalone_credit_parameters', $parameters, $order, $amount, $reason, $this->get_configuration()->get_gateway() );
		
		return $parameters;
	}
}