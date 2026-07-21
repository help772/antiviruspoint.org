<?php

namespace WcPaysafe\Api_Payments\Customers\Responses;

use WcPaysafe\Api_Payments\Response_Abstract;

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
class Payment_Handles extends Response_Abstract {
	
	public function get_usage() {
		return $this->data->usage;
	}
	
	public function get_customer_id() {
		return $this->data->customerId;
	}
	
	public function get_payment_token() {
		return $this->data->paymentHandleToken;
	}
}