<?php
/**
 * Update: DB Version.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Database\Updates;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Database\Versions;
use Themesquad\WC_Software_Addon\Internal\Abstracts\Update;

/**
 * Class Update_DB_Version.
 */
class Update_190_Migrate_Options extends Update {


	/**
	 * Runs the update.
	 *
	 * @since 1.9.0
	 *
	 * @return false
	 */
	public function run() {
		// Rename the version option.
		$version = get_option( 'woocommerce_software_version' );

		if ( $version ) {
			Versions::update_version( $version );
		}

		delete_option( 'woocommerce_software_version' );

		return false;
	}
}
