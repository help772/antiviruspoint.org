<?php

namespace WcPaysafe\Api\Cards;

use WcPaysafe\Api\Cards\Requests\Authorizations;
use WcPaysafe\Api\Cards\Requests\Refunds;
use WcPaysafe\Api\Cards\Requests\Settlements;
use WcPaysafe\Api\Data_Sources\Data_Source_Abstract;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Request_Fields\Card_Fields;
use WcPaysafe\Api\Service_Abstract;
use WcPaysafe\Api\Service_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides setup and routing for the specific service actions card/check payments, refunds, captures etc.
 * Will load the integration credentials and pass the request props to the specific service class.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
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
	 * @since 3.3.0
	 *
	 * @return \WcPaysafe\Api\Cards\Requests\Authorizations
	 */
	public function authorizations() {
		return new Authorizations( $this );
	}
	
	/**
	 * Returns the Transaction API class
	 *
	 * @since 3.3.0
	 *
	 * @return \WcPaysafe\Api\Cards\Requests\Authorizations
	 */
	public function authorizations_request() {
		return new Authorizations( $this );
	}
	
	/**
	 * @return \WcPaysafe\Api\Cards\Requests\Settlements
	 */
	public function settlements() {
		return new Settlements( $this );
	}
	
	/**
	 * @return Refunds
	 */
	public function refunds() {
		return new Refunds( $this );
	}
	
	/**
	 * @return bool
	 * @throws \Paysafe\PaysafeException
	 */
	public function monitor() {
		return $this->sdk()->cardPaymentService()->monitor();
	}
}