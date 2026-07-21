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
class Update_DB_Version extends Update {

	/**
	 * Constructor.
	 *
	 * @since 1.9.0
	 *
	 * @param array $args Update args.
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'version' => WC_SOFTWARE_ADDON_VERSION,
			)
		);

		parent::__construct( $args );
	}

	/**
	 * Generates the update ID.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function generate_id() {
		return sprintf(
			'update_%d_db_version',
			str_replace( '.', '', sanitize_text_field( $this->args['version'] ) )
		);
	}

	/**
	 * Runs the update.
	 *
	 * @since 1.9.0
	 *
	 * @return false
	 */
	public function run() {
		Versions::update_version( $this->args['version'], 'db' );

		return false;
	}
}
