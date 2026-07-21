<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Wp_Temporary_Login_Without_Password_Pro' ) ) {
/**
 * Main Pro class file
 *
 * @package Temporary Login Without Password
 */

/**
 * Class Wp_Temporary_Login_Without_Password_Pro
 *
 * @package Temporary Login Without Password
 */
	class Wp_Temporary_Login_Without_Password_Pro {

		 /**
		 * Wp_Temporary_Login_Without_Password constructor.
		 */
		public function __construct() {
			$this->load_dependencies();
		}

		/** 
		 * Load dependencies.
		 *
		 * @since 1.8.6
		 */
		private function load_dependencies() {
			$files = array(
			'/pro/admin/pro-class-wp-temporary-login-without-password-admin.php' => 'Wp_Temporary_Login_Without_Password_Admin_Pro',
			'/pro/class-wp-temporary-login-without-password-utils.php' => 'Wp_Temporary_Login_Without_Password_Utils',
			'/pro/admin/pro-class-wp-temporary-login-without-password-activity-log.php' => 'Wp_Temporary_Login_Without_Password_Activity_Log',  
			);
	
			foreach ($files as $relative_path => $class_name) {
				$file_path = WTLWP_PLUGIN_DIR . $relative_path;
	
				if (file_exists($file_path)) {
					require_once $file_path;
					if (class_exists($class_name)) {
						new $class_name();
					} else {
						error_log(sprintf(__( 'Class does not exist: %s', 'temporary-login-without-password'), $class_name)); //phpcs:ignore
					}
				} else {
					error_log(sprintf(__( 'File does not exist: %s', 'temporary-login-without-password'), $file_path)); //phpcs:ignore
				}
			}
		}	

	}	

}
