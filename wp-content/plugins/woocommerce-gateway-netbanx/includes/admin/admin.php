<?php

namespace WcPaysafe\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class to handle any additions for the plugin on the WC Admin
 *
 * @since  3.2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Admin {
	
	/**
	 * @var Admin_Notices
	 */
	public $notices;
	/**
	 * @var Capture
	 */
	public $capture;
	/**
	 * @var Refund
	 */
	public $refund;
	
	public function __construct() {
		$this->load_admin_notices();
		$this->load_capture();
		$this->load_refunds();
		$this->load_system_status();
	}
	
	/**
	 * Loads the admin notices
	 *
	 * @since 3.3.0
	 */
	public function load_admin_notices() {
		// Load admin notices
		$this->notices = new Admin_Notices();
		$this->notices->hooks();
	}
	
	/**
	 * Loads the Admin Capture procedure setup
	 *
	 * @since 3.3.0
	 */
	public function load_capture() {
		$this->capture = new Capture();
		$this->capture->hooks();
	}
	
	/**
	 * Loads the Admin Refund manipulation
	 *
	 * @since 3.3.0
	 */
	public function load_refunds() {
		$this->refund = new Refund();
		$this->refund->hooks();
	}
	
	public function load_system_status() {
		$status = new System_Status( 'netbanx' );
		$status->hooks();
	}
}