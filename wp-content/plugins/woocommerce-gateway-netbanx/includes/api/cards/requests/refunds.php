<?php

namespace WcPaysafe\Api\Cards\Requests;

use Paysafe\CardPayments\Refund;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Request_Abstract;
use WcPaysafe\Api\Request_Fields\Card_Fields;

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
class Refunds extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return bool|\WcPaysafe\Api\Cards\Parameters\Refunds
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api\Cards\Parameters\Refunds( new Card_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Cards\Responses\Refunds
	 * @throws \Paysafe\PaysafeException
	 */
	public function process( $params ) {
		$result = new \WcPaysafe\Api\Cards\Responses\Refunds( $this->service->sdk()->cardPaymentService()->refund(
			new Refund( $params )
		) );
		
		return $result;
	}
}