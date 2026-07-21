<?php
/**
 * WC_PRL_AS_Generator class
 *
 * @package  WooCommerce Product Recommendations
 * @since    4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Generator Action Scheduler recurring task.
 *
 * @class    WC_PRL_AS_Generator
 * @version  4.3.3
 */
final class WC_PRL_AS_Generator {

	/**
	 * Unique AS job name.
	 *
	 * @var string
	 */
	public static $cron_hook_identifier = 'wc_prl_as_generator';

	/**
	 * Cron healthcheck every 1 minute.
	 *
	 * @var int Number of minutes.
	 */
	protected $cron_interval = 1;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$is_handled_via_wp_cli = self::is_handled_via_wp_cli();

		// Only init cron if not handled via WP-CLI.
		if ( ! $is_handled_via_wp_cli ) {
			add_action( self::$cron_hook_identifier, array( $this, 'handle' ) );
			add_action(
				'init',
				function () {
					$this->schedule_event();
				}
			);
		} else {

			// Clean up the cron event if handled via WP-CLI.
			add_action(
				'init',
				function () {
					as_unschedule_action( self::$cron_hook_identifier );
				}
			);
		}

		// Cleanup crontasks prior v4.0.0.
		if ( wp_next_scheduled( 'wp_' . get_current_blog_id() . '_wc_prl_generator_cron' ) ) {
			wp_clear_scheduled_hook( 'wp_' . get_current_blog_id() . '_wc_prl_generator_cron' );
		}
	}

	/**
	 * Is queue processing handled via wp-cli.
	 */
	public static function is_handled_via_wp_cli() {
		return (bool) apply_filters( 'woocommerce_prl_queue_via_wp_cli', false );
	}

	/*
	|--------------------------------------------------------------------------
	| Cron Management.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Schedule event
	 */
	protected function schedule_event() {

		/**
		 * `wc_prl_enable_cron_generation` filter.
		 *
		 * Use this to disable the cron generation.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $enable_cron_generation Enable the cron generation.
		 * @return bool
		 */
		if ( ! (bool) apply_filters( 'wc_prl_enable_cron_generation', true ) ) {
			// If the cron is disabled, remove the event.
			as_unschedule_action( self::$cron_hook_identifier );
			return;
		}

		// Strictly rely on the lock to prevent race condition.
		if ( ! class_exists( 'ActionScheduler_Lock' ) || ! method_exists( 'ActionScheduler_Lock', 'instance' ) ) {
			return;
		}

		// Use Action Scheduler lock to prevent race condition.
		// Lock automatically expires after 60 seconds (MINUTE_IN_SECONDS).
		$locker = ActionScheduler_Lock::instance();
		if ( $locker->is_locked( 'wc_prl_schedule' ) ) {
			return;
		}

		if ( ! $locker->set( 'wc_prl_schedule' ) ) {
			return;
		}

		if ( function_exists( 'as_next_scheduled_action' ) && false === as_next_scheduled_action( self::$cron_hook_identifier ) ) {

			/**
			 * `wc_prl_as_generator_interval` filter.
			 *
			 * Used for controlling the main job's interval.
			 *
			 * @since 4.0.0
			 *
			 * @param int $interval Number of minutes.
			 * @return int
			 */
			$interval = (int) apply_filters( 'wc_prl_as_generator_interval', $this->cron_interval );
			as_schedule_recurring_action( time() + 10, $interval * MINUTE_IN_SECONDS, self::$cron_hook_identifier, array(), 'wc_prl_generator', true );
		}
	}

	/**
	 * Handle Action Scheduler recurring action.
	 *
	 * Acts as a cron job.
	 * Splits the work between the amplifier and the classic generator.
	 */
	public function handle() {

		$is_block_theme = wp_is_block_theme();

		if ( $is_block_theme ) {
			$amp_queue = new WC_PRL_Amplifier_Generator_Queue();
			$amp_queue->handle_request();
		}

		$queue = new WC_PRL_Generator_Queue();
		$queue->handle_request();
	}
}
new WC_PRL_AS_Generator();
