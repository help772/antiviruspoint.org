<?php

namespace WcPaysafe\Api_Payments\Customers\Parameters;

use WcPaysafe\Api_Payments\Config\Redirect;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Parameters_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Card_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Payment_Handles extends Parameters_Abstract {
	
	// Empty as we don't need it for the implemented methods.
	
	public function get_create_payment_handle_for_customer_params( $token ) {
		/**
		 * @var Order_Source $source
		 * @var \WC_Order    $order
		 * @var Card_Fields  $fields
		 * @var Redirect     $configuration
		 */
		$fields        = $this->get_fields();
		$configuration = $this->get_configuration();
		$source        = $fields->get_source();
		$order         = $source->get_source();
		
		$params = array(
			'merchantRefNum'         => uniqid( 'handle-ref-' ),
			'paymentType'            => 'CARD',
			'paymentHandleTokenFrom' => $token,
		);
		
		if ( $configuration->send_customer_ip() ) {
			$params['customerIp'] = $configuration->get_user_ip_addr();
		}
		
		$params = apply_filters( 'wc_paysafe_payments_create_handle_for_customer_parameters', $params, $order, $token );
		
		return $params;
	}
}