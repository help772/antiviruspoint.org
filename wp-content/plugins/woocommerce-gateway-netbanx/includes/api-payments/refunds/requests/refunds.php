<?php

namespace WcPaysafe\Api_Payments\Refunds\Requests;

use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Data_Sources\User_Source;
use WcPaysafe\Api_Payments\Request_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Card_Fields;
use WcPaysafe\Paysafe;

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
	 * @return bool|\WcPaysafe\Api_Payments\Refunds\Parameters\Refunds
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api_Payments\Refunds\Parameters\Refunds( new Card_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/settlements/{settlementId}/refunds
	 *
	 * @param $params
	 *
	 * @return \WcPaysafe\Api_Payments\Refunds\Responses\Refunds
	 * @throws \Exception
	 */
	public function process( $params ) {
		$this->api_credentials_type = 'private';
		
		$settlement_id = Paysafe::get_field( 'settlement_id', $params, '' );
		unset( $params['settlement_id'] );
		
		$response = $this->send_request( '/settlements/' . $settlement_id . '/refunds', 'POST', $params );
		
		$result = new \WcPaysafe\Api_Payments\Refunds\Responses\Refunds( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
}