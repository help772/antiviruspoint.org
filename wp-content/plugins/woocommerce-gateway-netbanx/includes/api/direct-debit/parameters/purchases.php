<?php

namespace WcPaysafe\Api\Direct_Debit\Parameters;

use WcPaysafe\Api\Config\Redirect;
use WcPaysafe\Api\Data_Sources\Order_Source as Data_Source_Order;
use WcPaysafe\Api\Parameters_Abstract;
use WcPaysafe\Api\Request_Fields\Card_Fields;
use WcPaysafe\Helpers\Currency;
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
class Purchases extends Parameters_Abstract {
	
	/**
	 * Generate transaction parameters for transaction using a token
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 * @param string    $token
	 * @param float     $amount
	 *
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function get_token_transaction_parameters( $token, $amount = null ) {
		/**
		 * @var Data_Source_Order $source
		 * @var \WC_Order         $order
		 * @var Card_Fields       $fields
		 * @var Redirect          $configuration
		 */
		$fields        = $this->get_fields();
		$configuration = $this->get_configuration();
		$source        = $fields->get_source();
		$order         = $source->get_source();
		$paysafe_order = new Paysafe_Order( $order );
		
		// If no amount is provided, charge the order total
		if ( null === $amount ) {
			$amount = $order->get_total();
		}
		
		if ( 0 === $amount ) {
			throw new \InvalidArgumentException( "Can't process direct debit payment with total equal to 0" );
		}
		
		// Determine the type of debit we are processing based on the currency
		$currency = $source->get_currency();
		
		if ( ! Currency::is_allowed_direct_debit_currency( $currency ) ) {
			throw new \InvalidArgumentException( sprintf( __( 'Invalid currency. We do not support direct debit payments with your currency "%s"', 'wc_paysafe' ), $currency ) );
		}
		
		$params = array(
			'amount'          => Formatting::format_amount( $amount, $order->get_currency() ),
			'merchantRefNum'  => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ),
			'profile'         => $fields->get_profile_fields(),
			'dupCheck'        => false,
			'billingDetails'  => $fields->get_billing_fields(),
			'shippingDetails' => $fields->get_shipping_fields(),
		);
		
		if ( Currency::is_ach_currency( $currency ) ) {
			$params['ach'] = array(
				'paymentToken' => $token,
				'payMethod'    => 'WEB'
			);
		} elseif ( Currency::is_eft_currency( $currency ) ) {
			$params['eft'] = array(
				'paymentToken' => $token,
			);
		} elseif ( Currency::is_bacs_currency( $currency ) ) {
			$params['bacs'] = array(
				'paymentToken' => $token,
			);
		} elseif ( Currency::is_sepa_currency( $currency ) ) {
			$params['sepa'] = array(
				'paymentToken' => $token,
			);
		}
		
		if ( $configuration->send_customer_ip() ) {
			$params['customerIp'] = $configuration->get_user_ip_addr();
		}
		
		$params = apply_filters( 'wc_paysafe_token_transaction_parameters', $params, $order, $token, 'directdebit' );
		
		wc_paysafe_add_debug_log( 'DD purchase parameters: ' . print_r( $params, true ) );
		
		return $params;
	}
}