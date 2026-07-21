<?php

namespace WcPaysafe\Api_Payments\Payments\Responses;

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
class Authorizations extends Commons {
	/**
	 * Response_Abstract constructor.
	 *
	 * @param string|\stdClass $response
	 */
	public function __construct( $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * Is the transaction authorized and captured in the same request
	 * @return bool
	 */
	public function get_settle_with_auth() {
		return isset( $this->data->settleWithAuth ) ? $this->data->settleWithAuth : false;
	}
	
	public function get_settlements() {
		return isset( $this->get_data()->settlements ) ? $this->get_data()->settlements : array();
	}
}