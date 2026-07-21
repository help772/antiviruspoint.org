<?php

namespace WcPaysafe\Api_Payments\Payments;

use WcPaysafe\Api_Payments\Payments\Requests\Authorizations;
use WcPaysafe\Api_Payments\Payments\Requests\Monitor;
use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Card_Fields;
use WcPaysafe\Api_Payments\Service_Abstract;
use WcPaysafe\Api_Payments\Service_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides setup and routing for the specific service actions card/check payments, refunds, captures etc.
 * Will load the integration credentials and pass the request props to the specific service class.
 *
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Service extends Service_Abstract implements Service_Interface {
	
	/**
	 * @param Data_Source_Abstract $source
	 *
	 * @return Card_Fields
	 */
	public function get_fields( $source ) {
		return new Card_Fields( $source );
	}
	
	/**
	 * Returns the Transaction API class
	 *
	 * @since 4.0.0
	 *
	 * @return \WcPaysafe\Api_Payments\Payments\Requests\Authorizations
	 */
	public function authorizations_request() {
		return new Authorizations( $this );
	}
	
	/**
	 * @return Monitor
	 * @throws \Exception
	 */
	public function monitor_request() {
		return new Monitor( $this );
	}
}