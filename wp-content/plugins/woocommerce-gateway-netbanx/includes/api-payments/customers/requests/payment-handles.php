<?php

namespace WcPaysafe\Api_Payments\Customers\Requests;

use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Data_Sources\User_Source;
use WcPaysafe\Api_Payments\Request_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Common_Fields;

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
class Payment_Handles extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Parameters\Payment_Handles
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api_Payments\Customers\Parameters\Payment_Handles( new Common_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/customers/{customerId}/paymenthandles/{paymenthandleId}
	 *
	 * @param $customer_id
	 * @param $handle_id
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Responses\Payment_Handles
	 * @throws \Exception
	 */
	public function delete( $customer_id, $handle_id ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/customers/' . $customer_id . '/paymenthandles/' . $handle_id, 'DELETE', [] );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Payment_Handles( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/customers/{customerId}/paymenthandles/{paymenthandleId}
	 *
	 * @param $customer_id
	 * @param $handle_id
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Responses\Payment_Handles
	 * @throws \Exception
	 */
	public function get( $customer_id, $handle_id ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/customers/' . $customer_id . '/paymenthandles/' . $handle_id, 'GET', [] );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Payment_Handles( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/customers/{customerId}/paymenthandles
	 *
	 * @param $customer_id
	 * @param $handle_id
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Responses\Payment_Handles
	 * @throws \Exception
	 */
	public function create_payment_handle( $customer_id, $params = [] ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/customers/' . $customer_id . '/paymenthandles/', 'POST', $params );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Payment_Handles( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
}