<?php

namespace WcPaysafe\Api\Direct_Debit;

use WcPaysafe\Api\Direct_Debit\Requests\Purchases;
use WcPaysafe\Api\Direct_Debit\Requests\Standalone_Credits;
use WcPaysafe\Api\Request_Fields\Direct_Debit_Fields;
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
	 * @param $source
	 *
	 * @return Direct_Debit_Fields
	 */
	public function get_fields( $source ) {
		return new Direct_Debit_Fields( $source );
	}
	
	/**
	 * Returns the Transaction API class
	 *
	 * @since 3.3.0
	 *
	 * @return \WcPaysafe\Api\Direct_Debit\Requests\Purchases
	 */
	public function purchases() {
		return new Purchases( $this );
	}
	
	/**
	 * Returns the Standalone Credits API class
	 *
	 * @since 3.3.0
	 *
	 * @return \WcPaysafe\Api\Direct_Debit\Requests\Standalone_Credits
	 */
	public function standalone_credits() {
		return new Standalone_Credits( $this );
	}
}