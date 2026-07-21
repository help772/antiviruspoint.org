<?php

namespace WcPaysafe\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ajax Loader Class
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Ajax_Loader {
	
	public $admin_ajax;
	/**
	 * @var \WcPaysafe\Ajax\Frontend\Paysafe_Checkout_Ajax
	 */
	public $frontend_checkout;
	/**
	 * @var \WcPaysafe\Ajax\Frontend\Paysafe_Checkout_Payments_Ajax
	 */
	public $frontend_checkout_payments;
	public $cart;
	
	/**
	 * Registers all Ajax classes and initializes them.
	 */
	public function register() {
		$classes = array(
			'admin_ajax'                 => '\\WcPaysafe\\Ajax\\Admin\\Ajax',
			'frontend_checkout'          => '\\WcPaysafe\\Ajax\\Frontend\\Paysafe_Checkout_Ajax',
			'frontend_checkout_payments' => '\\WcPaysafe\\Ajax\\Frontend\\Paysafe_Checkout_Payments_Ajax',
			'cart'                       => '\\WcPaysafe\\Ajax\\Frontend\\Cart_Ajax',
		);
		
		foreach ( $classes as $prop => $class ) {
			$this->{$prop} = new $class;
			$this->{$prop}->hooks();
		}
	}
}