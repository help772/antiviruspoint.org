<?php
/**
 * Abstract Background Process class.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Internal\Abstracts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/class-wc-background-process.php';
}

/**
 * Class Background_Process.
 */
abstract class Background_Process extends \WC_Background_Process {

	/**
	 * Initiates new background process.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id() . '_wc_software_addon';

		parent::__construct();
	}

	/**
	 * Forces the process execution.
	 *
	 * @since 1.9.0
	 */
	public function force_process() {
		do_action( $this->cron_hook_identifier ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
	}
}
