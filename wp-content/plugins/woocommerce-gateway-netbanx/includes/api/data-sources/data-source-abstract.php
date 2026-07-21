<?php

namespace WcPaysafe\Api\Data_Sources;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Data_Source_Abstract implements Data_Source_Interface {
	
	protected $source_type;
	/**
	 * @var \WC_Order|\WP_User
	 */
	protected $source;
	protected $save_to_account = false;
	protected $last_payment_response;
	protected $is_initial_payment = false;
	protected $using_saved_token = false;
	protected $cvv = '';
	
	/**
	 * A way to relay calls to the source properties.
	 * Since we cannot replicate all source properties in the classes,
	 * we want to make sure we try to get the props from the source object
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function __get( $name ) {
		if ( isset( $this->source->{$name} ) ) {
			return $this->source->{$name};
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
		if ( is_callable( array( $this->source, $name ) ) ) {
			return call_user_func_array( array( $this->source, $name ), $arguments );
		}
		
		throw new \BadMethodCallException( 'Call to undefined method ' . $name );
	}
	
	/**
	 * @return \WC_Order|\WP_User
	 */
	public function get_source() {
		return $this->source;
	}
	
	public function get_source_type() {
		return $this->source_type;
	}
	
	/**
	 * @param bool $bool
	 */
	public function set_save_to_account( $bool ) {
		$this->save_to_account = $bool;
	}
	
	/**
	 * @return bool
	 */
	public function get_save_to_account() {
		return $this->save_to_account;
	}
	
	/**
	 * @param object $object
	 */
	public function set_last_payment_response( $object ) {
		$this->last_payment_response = $object;
	}
	
	/**
	 * @return mixed
	 */
	public function get_last_payment_response() {
		return $this->last_payment_response;
	}
	
	/**
	 * @param bool $value
	 */
	public function set_is_initial_payment( $value ) {
		$this->is_initial_payment = $value;
	}
	
	/**
	 * @return mixed
	 */
	public function get_is_initial_payment() {
		return $this->is_initial_payment;
	}
	
	/**
	 * @param bool $value
	 */
	public function set_using_saved_token( $value ) {
		$this->using_saved_token = $value;
	}
	
	/**
	 * @return mixed
	 */
	public function get_using_saved_token() {
		return $this->using_saved_token;
	}
	
	public function set_cvv( $value ) {
		$this->cvv = $value;
	}
	
	public function get_cvv() {
		return $this->cvv;
	}
}