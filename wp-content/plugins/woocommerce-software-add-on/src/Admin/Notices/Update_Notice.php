<?php
/**
 * Notice - Update.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Admin\Notices;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Utilities\Admin_Utils;

/**
 * Update notice.
 */
class Update_Notice {

	/**
	 * Outputs the notice content.
	 *
	 * @since 1.9.0
	 */
	public static function output() {
		$update_url = wp_nonce_url(
			add_query_arg( 'do_update_wc_software_addon', 'true', Admin_Utils::get_license_keys_url() ),
			'wc_software_addon_db_update',
			'wc_software_addon_db_update_nonce'
		);

		include dirname( __FILE__ ) . '/views/html-notice-update.php';
	}
}
