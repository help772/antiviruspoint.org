<?php

namespace WcPaysafe\Api_Payments\Payment_Handles\Responses;

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
class Payment_Handles extends Response_Abstract {
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param string $response
	 */
	public function __construct( $response ) {
		parent::__construct( $response );
	}
	
	public function get_payment_handles() {
		return $this->data->paymentHandles;
	}
	
	public function get_links() {
		return $this->data->links;
	}
	
	public function get_action() {
		return $this->data->action;
	}
	
	public function get_payment_token() {
		return $this->data->paymentHandleToken;
	}
	
	public function get_authentication() {
		return $this->data->authentication;
	}
}