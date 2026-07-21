<?php

namespace WcPaysafe\Api\Alternate_Payments;

use WcPaysafe\Api\Alternate_Payments\Requests\Payments;
use WcPaysafe\Api\Alternate_Payments\Requests\Refunds;
use WcPaysafe\Api\Alternate_Payments\Requests\Settlements;
use WcPaysafe\Api\Request_Fields\Alternate_Payments_Fields;
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
	 * @return Alternate_Payments_Fields
	 */
	public function get_fields( $source ) {
		return new Alternate_Payments_Fields( $source );
	}
	
	/**
	 * Returns the Transaction API class
	 *
	 * @since 3.3.0
	 *
	 * @return \WcPaysafe\Api\Alternate_Payments\Requests\Payments
	 */
	public function payments() {
		return new Payments( $this );
	}
	
	/**
	 * @return \WcPaysafe\Api\Alternate_Payments\Requests\Settlements
	 */
	public function settlements() {
		return new Settlements( $this );
	}
	
	/**
	 * @return \WcPaysafe\Api\Alternate_Payments\Requests\Refunds
	 */
	public function refunds() {
		return new Refunds( $this );
	}
}