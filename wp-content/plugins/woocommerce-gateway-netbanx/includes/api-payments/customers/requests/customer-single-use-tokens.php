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
class Customer_Single_Use_Tokens extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return bool|\WcPaysafe\Api_Payments\Customers\Parameters\Customers_Single_Use_Tokens
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api_Payments\Customers\Parameters\Customers_Single_Use_Tokens( new Common_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/customers/{customerId}/singleusecustomertokens
	 *
	 * @param $customer_id
	 * @param $parameters
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Responses\Customers_Single_Use_Tokens
	 * @throws \Exception
	 */
	public function create( $customer_id, $parameters ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/customers/' . $customer_id . '/singleusecustomertokens', 'POST', $parameters );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Customers_Single_Use_Tokens( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * https://api.test.paysafe.com/paymenthub/v1/singleusecustomertokens/{singleusecustomertokenId}
	 *
	 * @param $single_user_token_id
	 *
	 * @return \WcPaysafe\Api_Payments\Customers\Responses\Customers_Single_Use_Tokens
	 * @throws \Exception
	 */
	public function get( $single_user_token_id ) {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/singleusecustomertokens/' . $single_user_token_id, 'GET', [] );
		
		$result = new \WcPaysafe\Api_Payments\Customers\Responses\Customers_Single_Use_Tokens( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result;
	}
}