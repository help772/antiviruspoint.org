<?php

namespace WcPaysafe\Api_Payments\Payments\Requests;

use WcPaysafe\Api_Payments\Request_Abstract;

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
class Monitor extends Request_Abstract {
	
	/**
	 * @return string
	 * @throws \Exception
	 */
	public function get_status() {
		$this->api_credentials_type = 'private';
		
		$response = $this->send_request( '/monitor', 'GET', [] );
		
		$result = new \WcPaysafe\Api_Payments\Payments\Responses\Monitor( $response );
		
		if ( $result->get_error() ) {
			throw new \Exception( $result->get_error_message() );
		}
		
		return $result->get_status();
	}
}