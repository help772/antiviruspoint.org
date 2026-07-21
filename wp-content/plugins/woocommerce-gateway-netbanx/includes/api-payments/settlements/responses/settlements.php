<?php

namespace WcPaysafe\Api_Payments\Settlements\Responses;

use WcPaysafe\Api_Payments\Response_Abstract;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Settlements extends Response_Abstract {
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param string $response
	 */
	public function __construct( $response ) {
		parent::__construct( $response );
	}
	
	public function available_to_refund() {
		return $this->data->availableToRefund;
	}
	
	public function get_merchant_reference_number() {
		return $this->data->merchantRefNum;
	}
	
	public function get_time_date() {
		return $this->data->txnTime;
	}
}