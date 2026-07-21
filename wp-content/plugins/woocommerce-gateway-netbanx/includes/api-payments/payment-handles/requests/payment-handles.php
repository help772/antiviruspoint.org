<?php

namespace WcPaysafe\Api_Payments\Payment_Handles\Requests;

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
class Payment_Handles extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return bool|\WcPaysafe\Api_Payments\Payment_Handles\Parameters\Payment_Handles
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api_Payments\Payment_Handles\Parameters\Payment_Handles( new Card_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/paymenthandles/{paymentHandleId}
	 *
	 * @param $payment_handle_id
	 *
	 * @return \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles
	 * @throws \Exception
	 */
	public function get( $payment_handle_id ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/paymenthandles/' . $payment_handle_id, 'GET', [] );
		
		$result = new \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/paymenthandles
	 *
	 * @param $params
	 *
	 * @return \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles
	 * @throws \Exception
	 */
	public function get_by_merchant_reference_number( $params = [] ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/paymenthandles', 'GET', $params );
		
		$result = new \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/singleusepaymenthandles
	 *
	 * @param $params
	 *
	 * @return \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles
	 * @throws \Exception
	 */
	public function create_single_use_payment_handle( $params = [] ) {
		$this->api_credentials_type = 'public';
		
		$response = $this->send_request( '/singleusepaymenthandles', 'POST', $params );
		
		$result = new \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/paymenthandles
	 *
	 * @param $params
	 *
	 * @return \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles
	 * @throws \Exception
	 */
	public function create_multi_use_payment_handle( $params = [] ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/paymenthandles', 'POST', $params );
		
		$result = new \WcPaysafe\Api_Payments\Payment_Handles\Responses\Payment_Handles( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
}