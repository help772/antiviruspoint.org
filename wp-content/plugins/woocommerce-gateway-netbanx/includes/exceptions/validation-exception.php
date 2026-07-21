<?php

namespace WcPaysafe\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2016 - 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Validation_Exception extends \Exception {
	
	public $order_id;
	
	/**
	 * Inits the Paysafe Validation exception.
	 *
	 * @param string $message  Message of the exception
	 * @param int    $code     Error code
	 * @param int    $order_id The Order ID, which we performed a validation to
	 */
	public function __construct( $message, $code = 0, $order_id = 0 ) {
		
		$this->order_id = $order_id;
		
		// make sure everything is assigned properly
		parent::__construct( $message, $code, $this->getPrevious() );
	}
	
	public function get_order_id() {
		return (int) $this->order_id;
	}
}