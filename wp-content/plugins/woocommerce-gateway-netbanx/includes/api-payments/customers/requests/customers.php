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
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Customers extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Parameters\Customers
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api_Payments\Customers\Parameters\Customers( new Common_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/customers/{customerId}
	 *
	 * @param string $customer_id
	 * @param array  $params
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Responses\Customers
	 * @throws \Exception
	 */
	public function get_customer_by_customer_id( $customer_id, $params = [] ) {
		
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/customers/' . $customer_id, 'GET', $params );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Customers( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/customers
	 *
	 * @param $customer_id
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Responses\Customers
	 * @throws \Exception
	 */
	public function get_customer_by_merchant_customer_id( $customer_id ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/customers/', 'GET', [ 'merchantCustomerId' => $customer_id ] );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Customers( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	public function create_customer( $params = [] ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/customers/', 'POST', $params );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Customers( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
}