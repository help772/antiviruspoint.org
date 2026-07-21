<?php
/**
 * Admin utilities.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Utilities;

/**
 * Class Admin_Utils.
 */
class Admin_Utils {

	/**
	 * Gets the current screen ID.
	 *
	 * @since 1.9.0
	 *
	 * @return string|false The screen ID. False otherwise.
	 */
	public static function get_screen_id() {
		$screen_id = false;

		// It may not be available.
		if ( function_exists( 'get_current_screen' ) ) {
			$screen    = get_current_screen();
			$screen_id = isset( $screen, $screen->id ) ? $screen->id : false;
		}

		// Get the value from the request.
		if ( ! $screen_id && ! empty( $_REQUEST['screen'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$screen_id = wc_clean( wp_unslash( $_REQUEST['screen'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		return $screen_id;
	}

	/**
	 * Gets the screen ID for the 'License Keys' page.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public static function get_license_keys_screen_id() {
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) ); // phpcs:ignore WordPress.WP.I18n

		return "{$wc_screen_id}_page_wc_software_keys";
	}

	/**
	 * Gets the URL of the 'License Keys' page.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public static function get_license_keys_url() {
		return admin_url( 'admin.php?page=wc_software_keys' );
	}
}
