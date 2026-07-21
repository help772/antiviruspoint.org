<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */


defined('ABSPATH') or exit;

/**
 * Handles scheduling and execution of the ELR Application Response
 * (inbound documents) sync via Action Scheduler.
 *
 * Drives a recurring background job whose cadence is controlled by the
 * `wc_avatax_elr_buyer_feedback_refresh_frequency` option. Each run pulls
 * inbound documents from the ELR `/documents` endpoint and writes the
 * buyer-feedback fields (`businessStatus`, `Request Action Code`,
 * `Status Reason Code`) onto the matching WooCommerce orders.
 *
 * Each iteration uses the window [previous run endDate, now]; on success the
 * cursor (`wc_avatax_elr_buyer_feedback_last_run_end_date`) is advanced to the
 * endDate of this run so consecutive runs cover the timeline with no gaps or
 * overlaps.
 *
 * @since 3.8.4
 */
class WC_AvaTax_Application_Response_Scheduler {

	/** @var string Scheduled (recurring) action hook name */
	const APPLICATION_RESPONSE_HOOK = 'wc_avatax_application_response_refresh';

	/** @var string Action Scheduler group */
	const GROUP = 'avatax';

	/** @var string User-controlled frequency option */
	const FREQUENCY_OPTION = 'wc_avatax_elr_buyer_feedback_refresh_frequency';

	/** @var string Internal cursor option (ISO 8601 endDate of last successful run) */
	const LAST_RUN_END_DATE_OPTION = 'wc_avatax_elr_buyer_feedback_last_run_end_date';

	/**
	 * Server-side filter for the documents endpoint. The Application Response
	 * use case only cares about CDAR (Cross-Domain Acknowledgement and
	 * Response) documents that have been fully processed.
	 *
	 * @var string
	 */
	const DOCUMENTS_FILTER = "documentType eq 'cdar-crossdomainacknowledgementandresponse' and status eq 'Complete'";

	/**
	 * Initialize the scheduler.
	 *
	 * @since 3.8.4
	 */
	public function __construct()
	{
		add_action(self::APPLICATION_RESPONSE_HOOK, array($this, 'runApplicationResponseSync'));

		// Use wp_loaded so the schedule is ensured in both admin and front-end
		// contexts without firing multiple times during cron processing.
		add_action('wp_loaded', array($this, 'scheduleApplicationResponse'));

		// Re-schedule whenever the user changes the frequency from settings
		add_action('update_option_' . self::FREQUENCY_OPTION, array($this, 'rescheduleOnOptionChange'), 10, 2);
		add_action('add_option_'    . self::FREQUENCY_OPTION, array($this, 'rescheduleOnOptionChange'), 10, 2);
	}

	/**
	 * Ensures the recurring action exists with the currently configured
	 * frequency. Does nothing if it's already scheduled.
	 *
	 * Called on every `wp_loaded`; Action Scheduler handles the actual
	 * execution independently of the current request context.
	 *
	 * @since 3.8.4
	 */
	public function scheduleApplicationResponse()
	{
		if (! function_exists('as_next_scheduled_action') || ! function_exists('as_schedule_recurring_action')) {
			return;
		}

		$frequency = get_option(self::FREQUENCY_OPTION, 'never');

		// "never" or unset → tear down any previously scheduled action
		if ('' === $frequency || 'never' === $frequency) {
			self::unscheduleApplicationResponse();
			return;
		}

		$interval = $this->getFrequencyIntervalSeconds($frequency);

		if ($interval <= 0) {
			return;
		}

		if (as_next_scheduled_action(self::APPLICATION_RESPONSE_HOOK, array(), self::GROUP)) {
			return;
		}

		as_schedule_recurring_action(
			time() + $interval,
			$interval,
			self::APPLICATION_RESPONSE_HOOK,
			array(),
			self::GROUP
		);

		if (wc_avatax()->elr_logging_enabled()) {
			wc_avatax()->log_elr(sprintf(
				'Application Response sync scheduled with frequency: %s (%ds)',
				$frequency,
				$interval
			));
		}
	}

	/**
	 * Drops the existing schedule and re-creates it with the new frequency.
	 * Bound to `update_option_*` / `add_option_*` for the frequency option.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $old_value previous option value (unused)
	 * @param mixed $new_value new option value     (unused)
	 */
	public function rescheduleOnOptionChange($old_value = null, $new_value = null)
	{
		self::unscheduleApplicationResponse();
		$this->scheduleApplicationResponse();
	}

	/**
	 * Action Scheduler callback. Performs one sync iteration.
	 *
	 * @since 3.8.4
	 */
	public function runApplicationResponseSync()
	{
		$start_date = '';

		try {
			if (! $this->isElrConfigured()) {

				if (wc_avatax()->elr_logging_enabled()) {
					wc_avatax()->log_elr('Application Response sync skipped: ELR is not configured');
				}
				return;
			}

			$now_ts   = time();
			$end_date = gmdate('Y-m-d\TH:i:s', $now_ts);

			$last_end_date = get_option(self::LAST_RUN_END_DATE_OPTION, '');

			if (empty($last_end_date)) {
				$frequency  = get_option(self::FREQUENCY_OPTION, '1day');
				$lookback   = $this->getFrequencyIntervalSeconds($frequency);
				$lookback   = $lookback > 0 ? $lookback : DAY_IN_SECONDS;
				$start_date = gmdate('Y-m-d\TH:i:s', $now_ts - $lookback);
			} else {
				$start_date = $last_end_date;
			}

			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'Application Response sync starting: startDate=%s endDate=%s',
					$start_date,
					$end_date
				));
			}

			$api = wc_avatax()->get_elr_api();

			if (! $api) {

				if (wc_avatax()->elr_logging_enabled()) {
					wc_avatax()->log_elr('Application Response sync aborted: ELR API unavailable');
				}
				return;
			}

			$filter = (string) apply_filters(
				'wc_avatax_elr_application_response_filter',
				self::DOCUMENTS_FILTER
			);

			$args = array(
				'startDate' => $start_date,
				'endDate'   => $end_date,
			);

			if ( '' !== $filter ) {
				$args['filter'] = $filter;
			}

			$summary = $api->sync_inbound_documents_status( $args );

			// Advance the cursor ONLY if the sync completed cleanly. If any
			// page-level API call failed, sync_inbound_documents_status sets
			// success=false; in that case we leave the cursor untouched so
			// the next scheduled run retries the same [startDate, endDate]
			// window — no data is silently skipped.
			if ( empty( $summary['success'] ) ) {

				// Sync failed: cursor was NOT advanced. Throw so Action Scheduler
				// records this run as "failed" and surfaces the error, and so the
				// same [startDate, endDate] window is retried on the next run.
				throw new Exception( sprintf(
					'Application Response sync failed (cursor preserved at %s): %s',
					$start_date,
					$summary['error'] ?? 'unknown error'
				) );
			}

			update_option(self::LAST_RUN_END_DATE_OPTION, $end_date, false);

			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'Application Response sync complete: pages=%d total=%d processed=%d skipped=%d',
					$summary['pages']     ?? 0,
					$summary['total']     ?? 0,
					$summary['processed'] ?? 0,
					$summary['skipped']   ?? 0
				));
			}
		} catch (\Throwable $e) {

			// Any error/exception during the sync leaves the cursor untouched
			// because we never reach the update_option call above.
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr(sprintf(
					'Application Response sync error (cursor preserved at %s): %s',
					$start_date,
					$e->getMessage()
				));
			}

			$logger = wc_avatax()->elr_logger();
			if ($logger && method_exists($logger, 'log_exception')) {
				$logger->log_exception(
					'ApplicationResponseSync',
					'runApplicationResponseSync',
					$e->getMessage(),
					$e->getTraceAsString()
				);
			}

			// Re-throw so Action Scheduler marks this scheduled action as failed
			// and shows the error in the Scheduled Actions log.
			throw $e;
		}
	}

	/**
	 * Maps a frequency key to its interval in seconds.
	 *
	 * @since 3.8.4
	 *
	 * @param string $frequency one of '15min'|'30min'|'1hr'|'6hr'|'12hr'|'1day'
	 * @return int seconds; 0 when the key is unknown
	 */
	private function getFrequencyIntervalSeconds(string $frequency): int
	{
		$map = array(
			'15min' => 15 * MINUTE_IN_SECONDS,
			'30min' => 30 * MINUTE_IN_SECONDS,
			'1hr'   => HOUR_IN_SECONDS,
			'6hr'   => 6 * HOUR_IN_SECONDS,
			'12hr'  => 12 * HOUR_IN_SECONDS,
			'1day'  => DAY_IN_SECONDS,
		);

		return $map[$frequency] ?? 0;
	}

	/**
	 * Check if ELR is properly configured.
	 *
	 * @since 3.8.4
	 * @return bool
	 */
	private function isElrConfigured(): bool
	{
		$clientId     = get_option('wc_avatax_elr_client_id');
		$clientSecret = get_option('wc_avatax_elr_client_secret');

		return ! empty($clientId) && ! empty($clientSecret);
	}

	/**
	 * Unschedule the Application Response sync when the plugin is deactivated
	 * or when the user turns the frequency off.
	 *
	 * @since 3.8.4
	 */
	public static function unscheduleApplicationResponse()
	{
		if (function_exists('as_unschedule_all_actions')) {
			as_unschedule_all_actions(self::APPLICATION_RESPONSE_HOOK, array(), self::GROUP);
		}
	}
}
