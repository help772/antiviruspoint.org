<?php

namespace WcPaysafe\Api_Payments\Customers\Responses;

use WcPaysafe\Api_Payments\Payments\Responses\Commons;

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
class Customers extends Commons {
	
	public function get_single_use_token() {
		return $this->data->singleUseCustomerToken;
	}
	
	public function get_payment_handles() {
		return $this->data->paymentHandles;
	}
	
	public function get_merchant_customer_id() {
		return $this->data->merchantCustomerId;
	}
}