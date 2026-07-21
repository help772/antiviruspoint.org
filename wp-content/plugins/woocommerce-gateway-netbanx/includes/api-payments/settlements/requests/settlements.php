<?php

namespace WcPaysafe\Api_Payments\Settlements\Requests;

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
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Settlements extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return \WcPaysafe\Api_Payments\Settlements\Parameters\Settlements
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api_Payments\Settlements\Parameters\Settlements( new Card_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/payments/{paymentId}/settlements
	 *
	 * @param $params
	 *
	 * @return \WcPaysafe\Api_Payments\Settlements\Responses\Settlements
	 * @throws \Exception
	 */
	public function process( $params ) {
		$this->api_credentials_type = 'private';
		
		$payment_id = Paysafe::get_field( 'payment_id', $params, '' );
		unset( $params['payment_id'] );
		
		$response = $this->send_request( '/payments/' . $payment_id . '/settlements', 'POST', $params );
		
		$result = new \WcPaysafe\Api_Payments\Settlements\Responses\Settlements( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/settlements/{settlementId}
	 *
	 * @param string $payment_id
	 *
	 * @return \WcPaysafe\Api_Payments\Settlements\Responses\Settlements
	 * @throws \Exception
	 */
	public function get( $payment_id ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/payments/' . $payment_id . '/settlements', 'GET', [] );
		
		$result = new \WcPaysafe\Api_Payments\Settlements\Responses\Settlements( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
}