<?php

namespace WcPaysafe\Api_Payments\Customers;

use WcPaysafe\Api_Payments\Customers\Requests\Payment_Handles;
use WcPaysafe\Api_Payments\Customers\Requests\Customer_Single_Use_Tokens;
use WcPaysafe\Api_Payments\Customers\Requests\Customers;
use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Common_Fields;
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
	 * @return Common_Fields
	 */
	public function get_fields( $source ) {
		return new Common_Fields( $source );
	}
	
	/**
	 * Returns the Transaction API class
	 *
	 * @since 4.0.0
	 *
	 * @return Customer_Single_Use_Tokens
	 */
	public function single_use_tokens_request() {
		return new Customer_Single_Use_Tokens( $this );
	}
	
	public function customers_request() {
		return new Customers( $this );
	}
	
	public function payment_handles_request() {
		return new Payment_Handles( $this );
	}
}