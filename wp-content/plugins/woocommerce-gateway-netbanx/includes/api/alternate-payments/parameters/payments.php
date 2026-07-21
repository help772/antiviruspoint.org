<?php

namespace WcPaysafe\Api\Alternate_Payments\Parameters;

use WcPaysafe\Api\Config\Redirect;
use WcPaysafe\Api\Data_Sources\Order_Source as Data_Source_Order;
use WcPaysafe\Api\Parameters_Abstract;
use WcPaysafe\Api\Request_Fields\Alternate_Payments_Fields;
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
class Payments extends Parameters_Abstract {
	
	/**
	 * Generate transaction parameters for transaction using a token
	 *
	 * @since 3.3.0
	 *
	 * @param string $token
	 * @param float  $amount
	 *
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function get_token_transaction_parameters( $token, $amount = null ) {
		/**
		 * @var Data_Source_Order         $source
		 * @var \WC_Order                 $order
		 * @var Alternate_Payments_Fields $fields
		 * @var Redirect                  $configuration
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
		
		$params = array(
			'merchantRefNum'  => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ),
			'settleWithAuth'  => 'sale' == $configuration->get_authorization_type(),
			'amount'          => Formatting::format_amount( $amount, $order->get_currency() ),
			'dupCheck'        => true,
			'paymentType'     => "INTERAC",
			'paymentToken'    => $token,
			'profile'         => $fields->get_profile_fields(),
			'billingDetails'  => $fields->get_billing_fields(),
			'shippingDetails' => $fields->get_shipping_fields(),
			'returnLinks'     => $fields->get_return_links(),
		);
		
		// No capture if total is 0
		if ( 0 == $amount ) {
			$params['settleWithAuth'] = false;
			$params['amount']         = 1;
		}
		
		$params = apply_filters( 'wc_paysafe_token_transaction_parameters', $params, $order, $token, 'interac' );
		
		wc_paysafe_add_debug_log( 'Interac Payment parameters: ' . print_r( $params, true ) );
		
		return $params;
	}
}