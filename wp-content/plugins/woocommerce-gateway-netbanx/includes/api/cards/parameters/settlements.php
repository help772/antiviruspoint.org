<?php

namespace WcPaysafe\Api\Cards\Parameters;

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
class Settlements extends Parameters_Abstract {
	
	public function settlement_parameters( $amount ) {
		$order    = $this->get_fields()->get_source()->get_source();
		$ps_order = new Paysafe_Order( $order );
		
		// Get the saved card
		$authorization_id = $ps_order->get_authorization_id();
		
		$params = array(
			'authorizationID' => $authorization_id,
			'merchantRefNum'  => 'capture-' . $ps_order->get_order_number() . '-' . $ps_order->get_attempts_suffix( 'capture' ),
		);
		
		if ( 0 < $amount ) {
			$params['amount'] = Formatting::format_amount( $amount, $order->get_currency() );
		}
		
		$params = apply_filters( 'wc_paysafe_settlement_parameters', $params, $order, $amount, $this->get_configuration()->get_gateway() );
		
		return $params;
	}
}