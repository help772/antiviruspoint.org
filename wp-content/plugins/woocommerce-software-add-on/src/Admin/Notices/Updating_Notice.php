<?php
/**
 * Notice - Updating.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Admin\Notices;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Utilities\Admin_Utils;

/**
 * Updating notice.
 */
class Updating_Notice {

	/**
	 * Outputs the notice content.
	 *
	 * @since 1.9.0
	 */
	public static function output() {
		$force_update_url = wp_nonce_url(
			add_query_arg( 'force_update_wc_software_addon', 'true', Admin_Utils::get_license_keys_url() ),
			'wc_software_addon_force_db_update',
			'wc_software_addon_force_db_update_nonce'
		);

		include dirname( __FILE__ ) . '/views/html-notice-updating.php';
	}
}
