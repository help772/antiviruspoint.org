<?php

namespace WcPaysafe\Api_Payments\Config;

use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * An adapter class that receives the WC gateways and provides methods to access their properties
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Configuration_Abstract {
	
	protected $gateway;
	
	/**
	 * @param \WC_Payment_Gateway|\WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway $gateway
	 */
	public function __construct( \WC_Payment_Gateway $gateway ) {
		$this->gateway = $gateway;
	}
	
	public function get_gateway() {
		return $this->gateway;
	}
	
	/**
	 * @param      $name
	 * @param null $default
	 *
	 * @return string
	 */
	public function get_option( $name, $default = null ) {
		return $this->get_gateway()->get_option( $name, $default );
	}
	
	/**
	 * Relay any direct calls to gateway props
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function __get( $name ) {
		if ( isset( $this->get_gateway()->{$name} ) ) {
			return $this->get_gateway()->{$name};
		}
		
		return false;
	}
	
	/**
	 * A way for us to relay method calls to the source, i.e. WC_Order object
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		if ( is_callable( array( $this->get_gateway(), $name ) ) ) {
			return call_user_func_array( array( $this->get_gateway(), $name ), $arguments );
		}
		
		throw new \BadMethodCallException( 'Call to undefined method ' . $name );
	}
	
	/**
	 * @param null|Data_Source_Interface $data_source
	 * @param string                     $payment_type cards|directdebit|interac
	 *
	 * @return int
	 */
	public function get_account_id( $data_source = null, $payment_type = null ) {
		return $this->get_gateway()->get_account_id( $data_source, $payment_type );
	}
}