<?php

namespace WcPaysafe\Api_Payments\Refunds;

use WcPaysafe\Api_Payments\Refunds\Requests\Refunds;
use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Card_Fields;
use WcPaysafe\Api_Payments\Service_Abstract;
use WcPaysafe\Api_Payments\Service_Interface;

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
	 * @return Refunds
	 */
	public function refunds_request() {
		return new Refunds( $this );
	}
}