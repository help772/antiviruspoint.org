<?php

namespace WcPaysafe\Api_Payments;

use Paysafe\PaysafeApiClient;
use WcPaysafe\Api_Payments\Config\Redirect;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Data_Sources\User_Source;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to bootstrap the requests. It should only format the data presented to the requests.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Request_Abstract {
	
	/**
	 * @var
	 */
	protected $sdk;
	protected $service;
	protected $base_url;
	/**
	 * @var Redirect
	 */
	protected $configuration;
	protected $api_credentials_type;
	
	/**
	 * Authorizations constructor.
	 *
	 * @param \WcPaysafe\Api_Payments\Service_Interface $service
	 */
	public function __construct( Service_Interface $service ) {
		$this->service       = $service;
		$this->configuration = $this->service->get_configuration();
		
		$this->base_url = 'https://api.paysafe.com/paymenthub/v1';
		if ( $this->configuration->is_testmode() ) {
			$this->base_url = 'https://api.test.paysafe.com/paymenthub/v1';
		}
		
		$this->api_credentials_type = 'private';
	}
	
	/**
	 * Method needs to be extended by the child classes
	 *
	 * @param Order_Source|User_Source $source
	 *
	 * @return bool
	 */
	public function get_request_builder( $source ) {
		return false;
	}
	
	public function service() {
		return $this->service;
	}
	
	public function get_configuration() {
		return $this->configuration;
	}
	
	public function get_api_username() {
		if ( 'public' == $this->api_credentials_type ) {
			return $this->configuration->get_option( 'single_use_token_user_name' );
		}
		
		return $this->configuration->get_option( 'api_user_name' );
	}
	
	public function get_api_password() {
		if ( 'public' == $this->api_credentials_type ) {
			return $this->configuration->get_option( 'single_use_token_password' );
		}
		
		return $this->configuration->get_option( 'api_password' );
	}
	
	/**
	 *
	 * @param \Paysafe\Request $request
	 *
	 * @return array|bool
	 * @throws \Exception
	 */
	public function send_request( $path, $method, $params ) {
		
		wc_paysafe_payments_add_debug_log( 'send_request: $path: ' . print_r( $path, true ) );
		
		$curl = curl_init();
		
		$url = $this->base_url . $path;
		if ( 'GET' == $method && $params ) {
			$query = '';
			foreach ( $params as $key => $value ) {
				$query .= $key . '=' . $value . '&';
			}
			
			$query = substr( $query, 0, - 1 );
			$url   .= '?' . $query;
			
			wc_paysafe_payments_add_debug_log( 'send_request: $query: ' . print_r( $query, true ) );
		}
		
		$headers = array(
			'Authorization: Basic ' . base64_encode( $this->get_api_username() . ':' . $this->get_api_password() ),
			'Content-Type: application/json; charset=utf-8',
		);
		
		
		
		$opts = array(
			CURLOPT_URL            => $url,
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0,
		);
		if ( ( $cert = PaysafeApiClient::getCACertPath() ) ) {
			$opts[ CURLOPT_CAINFO ] = $cert;
		} elseif ( ( $cert = getenv( 'SSL_CERT_FILE' ) ) ) {
			$opts[ CURLOPT_CAINFO ] = $cert;
		}
		if ( 'GET' != $method ) {
			$json_data = ( $params ? json_encode( $params ) : "" );
			
			$log_params = $params;
			if ( isset( $log_params['card'] ) && isset( $log_params['card']['cvv'] ) ) {
				$log_params['card']['cvv'] = '***';
			}
			$log_json_data = ( $log_params ? json_encode( $log_params ) : "" );
			
			wc_paysafe_payments_add_debug_log( 'send_request: $json_data: ' . print_r( $log_json_data, true ) );
			
			$opts[ CURLOPT_CUSTOMREQUEST ] = $method;
			$opts[ CURLOPT_POSTFIELDS ]    = $json_data;
			$opts[ CURLOPT_HTTPHEADER ][]  = 'Content-Length: ' . strlen( $json_data );
		}
		curl_setopt_array( $curl, $opts );
		$response_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		$response      = curl_exec( $curl );
		
		wc_paysafe_payments_add_debug_log( 'send_request: $response: ' . print_r( $response, true ) );
		
		return $response;
	}
}