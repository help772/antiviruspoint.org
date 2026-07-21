<?php
/**
 * Handles the plugin database migrations.
 *
 * @version 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Database;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Internal\Abstracts\Background_Process;
use Themesquad\WC_Software_Addon\Internal\Abstracts\Update;
use Themesquad\WC_Software_Addon\Logger;

/**
 * Class Updater.
 */
class Updater extends Background_Process {

	/**
	 * Init updater.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {
		$this->action = 'updater';

		parent::__construct();
	}

	/**
	 * Dispatch updater.
	 *
	 * Updater will still run via cron job if this fails for any reason.
	 *
	 * @since 1.9.0
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			Logger::error( sprintf( 'Unable to dispatch the updater: %s', $dispatched->get_error_message() ), 'db_updates' );
		}
	}

	/**
	 * Handle cron healthcheck.
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 *
	 * @since 1.9.0
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running.
			return;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			$this->clear_scheduled_event();
			return;
		}

		$this->handle();
	}

	/**
	 * Schedule fallback event.
	 *
	 * @since 1.9.0
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Is the updater running?
	 *
	 * @since 1.9.0
	 *
	 * @return boolean
	 */
	public function is_updating() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Task.
	 *
	 * @since 1.9.0
	 *
	 * @param array $item Queue item to iterate over.
	 * @return array|false
	 */
	protected function task( $item ) {
		$item = wp_parse_args(
			$item,
			array(
				'update' => '',
				'args'   => array(),
			)
		);

		if ( empty( $item['update'] ) || ! is_a( $item['update'], Update::class, true ) ) {
			Logger::notice( sprintf( 'Could not find the update: %s', wc_print_r( $item, true ) ), 'db_updates' );
			return false;
		}

		$update    = new $item['update']( $item['args'] );
		$update_id = $update->get_id();

		Logger::info( sprintf( "Running the update '%s'.", $update_id ), 'db_updates' );

		if ( $update->run() ) {
			Logger::info( sprintf( "The update '%s' needs to run again.", $update_id ), 'db_updates' );
			return $item;
		}

		Logger::info( sprintf( "Finished running the update '%s'.", $update_id ), 'db_updates' );

		return false;
	}

	/**
	 * Complete.
	 *
	 * @since 1.9.0
	 */
	protected function complete() {
		Logger::info( 'Database update completed.', 'db_updates' );

		parent::complete();

		/**
		 * Fires when the plugin updater finished.
		 *
		 * @since 1.9.0
		 */
		do_action( 'wc_software_addon_updater_complete' );
	}
}
