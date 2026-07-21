<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Wp_Temporary_Login_Without_Password_Admin_Pro' ) ) {
/**
 * Public face of Temporary Login Without Password Pro
 *
 * @package Temporary Login Without Password
 */

/**
 * Class Wp_Temporary_Login_Without_Password_Admin_Pro
 *
 * @package Temporary Login Without Password
 */
	class Wp_Temporary_Login_Without_Password_Admin_Pro {

		/**
		 * Wp_Temporary_Login_Without_Password_Admin_Pro constructor.
		 *
		 */
		public function __construct() {
			add_filter( 'wtlwp_login_pre_check', array( $this, 'limit_login_attempts' ), 10, 2 );
			add_action( 'wtlwp_after_login_success', array( $this, 'notify_admin_on_temp_login' ), 10, 1 );
		}

		/**
		 * Limit number of login attempts
		 *
		 * @param bool $do_login Flag to determine if login should proceed.
		 * @param int|null $temporary_user_id The ID of the temporary user.
		 *
		 * @return boolean
		 *
		 * @since 1.8.6
		 */
		public function limit_login_attempts( $do_login = true, $temporary_user_id = null ) {
	
			if ( empty( $temporary_user_id ) || !is_numeric( $temporary_user_id ) ) {
				return false; 
			}

			if ( !Wp_Temporary_Login_Without_Password::is_pro() ) {
				return true;
			}

			// Fetch the user's specific login limit.
			$max_login_limit = get_user_meta( $temporary_user_id, '_wtlwp_max_login_limit', true );

			// Set the default max login limit from settings or fallback to 99 if it's not available.
		   $default_max_login_limit = Wp_Temporary_Login_Without_Password_Common::get_default_max_login_limit();
			if ( empty( $max_login_limit ) || $max_login_limit == WTLWP_DEFAULT_MAX_LOGIN_LIMIT ) {
				$max_login_limit = $default_max_login_limit;
			}

			$login_count_key   = '_wtlwp_login_count';
			$login_count       = (int) get_user_meta($temporary_user_id, $login_count_key, true);

			// Allow login if login count is below the maximum limit.
			return $login_count < $max_login_limit;
		}
	
		/**
		 * Notify admin on temporary login creation
		 *
		 * @param int|null $temporary_user_id The ID of the temporary user.
		 *
		 * @return void
		 * @since 1.8.6
		 */
	
		public function notify_admin_on_temp_login( $temporary_user_id = null) {
		
			if ( empty( $temporary_user_id ) || !is_numeric( $temporary_user_id ) ) {
				return;
			}

			$admin_email = get_bloginfo('admin_email');
			if ( empty( $admin_email ) || !is_email( $admin_email ) ) {
				return; 
			}
			$admin_name = get_bloginfo('name') ? get_bloginfo('name') : 'Admin'; 
			$subject    =  __('🔔 Important: Recent Login Activity on Your Account', 'temporary-login-without-password');
			$body       =  self::generate_temporary_login_notification_html($temporary_user_id);
		
			$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $admin_name . ' <' . esc_html($admin_email) . '>',
			'Reply-To: "' . $admin_name . '" <' . $admin_email . '>',
			'MIME-Version: 1.0',
			);
		
			// Send the email
			$mail_sent = wp_mail($admin_email, $subject, $body, $headers);
			if (!$mail_sent) {
				error_log(__('Failed to send temporary login notification to admin.', 'temporary-login-without-password'));

			}
		
		}

		public static function generate_temporary_login_notification_html( $temporary_user_id = null) {

			$wtlwp_user          = get_userdata($temporary_user_id);
			$tlwp_settings       = maybe_unserialize( get_option( 'tlwp_settings', array() ) );
			$expire              = get_user_meta( $temporary_user_id, '_wtlwp_expire', true );
			if ( ! empty( $expire ) ) {
				$expiry_time = Wp_Temporary_Login_Without_Password_Common::time_elapsed_string( $expire );
			}

			ob_start();
			$temporary_login_notification = WTLWP_PLUGIN_DIR . '/templates/temporary-login-notification.php';
			if ( file_exists( $temporary_login_notification ) ) {
				require_once $temporary_login_notification;
			}

			$wtlwp_notification_html = ob_get_clean();

			return $wtlwp_notification_html;
		}

	}

}
