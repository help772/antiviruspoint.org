<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wp_Temporary_Login_Without_Password_Utils' ) ) {
	class Wp_Temporary_Login_Without_Password_Utils {

		public function __construct() {
			add_action( 'admin_init', array( $this, 'ob_start' ) );
			// Start-TLWP-Code.
			// Include load file.
			if ( ! class_exists( 'Icegram_Upgrade_4_1' ) ) {
				require_once WTLWP_PLUGIN_DIR . '/inc/ig-upgrade-v-4.1.php';
			}
			
			$file                 = WTLWP_PLUGIN_FILE;
			$sku                  = 'IGA-TEMPORARY-LOGIN-WITHOUT-PASSWORD';
			$prefix               = 'wtlwp';
			$plugin_data 		  = get_plugin_data( $file, true, false );
			$plugin_name          = isset( $plugin_data['Name'] ) ? $plugin_data['Name'] : 'Temporary Login Without Password Pro';
			$text_domain          = 'temporary-login-without-password';
			$documentation_link   = 'https://www.icegram.com/knowledgebase_category/';
			$pricing_link         = 'https://www.icegram.com/?buy-now=445245&qty=1&coupon=tlwp-pro-20&with-cart=1';
			$plugin_dashboard_url = admin_url( 'admin.php?page=wp-temporary-login-without-password' );
			
			new Icegram_Upgrade_4_1( $file, $sku, $prefix, $plugin_name, $text_domain, $documentation_link, $pricing_link, $plugin_dashboard_url );
			// End-TLWP-Code.
			add_filter( 'wtlwp_is_page_for_notifications', array( $this, 'wtlwp_show_notification') );
			
		}
		
		function wtlwp_show_notification() {
			$screen = get_current_screen();
			if ( in_array( $screen->id, array( 'users_page_wp-temporary-login-without-password') ) ) {
				return true;		
			} 
			return false;
			
		}

		/**
		 * Method to start output buffering to allows admin screens to make redirects later on.
		 *
		 * @since 4.5.2
		 */
		public function ob_start() {
			ob_start();
		}

	}

	 
}
