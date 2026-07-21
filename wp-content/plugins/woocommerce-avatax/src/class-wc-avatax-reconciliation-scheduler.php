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
 * Handles scheduling and execution of reconciliation runs via Action Scheduler.
 *
 * When the user clicks Run in the reconciliation UI, a single action is scheduled
 * with the session_id; the actual batch work runs when the cron job fires.
 *
 * @since 3.8.0
 */
class WC_AvaTax_Reconciliation_Scheduler {

	/** @var string The scheduled action hook name */
	const RECONCILIATION_HOOK = 'wc_avatax_run_reconciliation';

	/** @var string Action Scheduler group */
	const GROUP = 'avatax';

	/**
	 * Initialize the scheduler.
	 *
	 * @since 3.8.0
	 */
	public function __construct()
	{
		add_action(self::RECONCILIATION_HOOK, array($this, 'runReconciliation'));
	}

	/**
	 * Schedule a single reconciliation run for the given session.
	 * Call this after creating the session and setting job status to pending.
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID (job status and filters already stored in transient).
	 */
	public function scheduleReconciliationRun($session_id)
	{
		$session_id = is_array($session_id) ? (isset($session_id[0]) ? $session_id[0] : '') : (string) $session_id;
		if ('' === trim($session_id)) {
			return;
		}
		if (!function_exists('as_schedule_single_action')) {
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log('Reconciliation: Action Scheduler not available, cannot schedule run');
			}
			return;
		}
		// Schedule for a few seconds from now so the Run AJAX response returns immediately
		// (if the queue runs at end of request, it won't run this action yet)
		as_schedule_single_action(time() + 5, self::RECONCILIATION_HOOK, array($session_id), self::GROUP);
		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log(sprintf('Reconciliation scheduled for session %s', $session_id));
		}
	}

	/**
	 * Run reconciliation for a session (callback when the scheduled action runs).
	 *
	 * Reads filters from the DB row (persistent) instead of transients, which
	 * can be evicted by external object caches on production.
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID.
	 */
	public function runReconciliation($session_id)
	{
		$session_id = is_array($session_id) ? (isset($session_id[0]) ? $session_id[0] : '') : (string) $session_id;
		if ('' === trim($session_id)) {
			return;
		}
		require_once wc_avatax()->get_plugin_path() . '/src/reconciliation/class-wc-avatax-reconciliation-handler.php';
		$handler = new WC_AvaTax_Reconciliation_Handler();

		$db_status = $handler->get_reconciliation_session_status_from_db($session_id);
		if (null === $db_status) {
			wc_avatax()->log(sprintf('Reconciliation skipped for session %s: no DB row found', $session_id));
			return;
		}
		if ('pending' !== $db_status) {
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log(sprintf('Reconciliation skipped for session %s (DB status: %s)', $session_id, $db_status));
			}
			return;
		}

		$filters = $handler->get_reconciliation_session_filters_from_db($session_id);
		if (empty($filters)) {
			wc_avatax()->log(sprintf('Reconciliation failed for session %s: filters not found in DB', $session_id));
			$handler->update_reconciliation_session_status($session_id, 'failed');
			return;
		}

		try {
			$handler->run_reconciliation_batches($session_id, $filters);
			$handler->set_reconciliation_job_status($session_id, 'completed', array(), '');
			$handler->update_reconciliation_session_status($session_id, 'completed');
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log(sprintf('Reconciliation completed for session %s', $session_id));
			}
		} catch (Exception $e) {
			wc_avatax()->log(sprintf('Reconciliation background error: %s', $e->getMessage()));
			$handler->set_reconciliation_job_status($session_id, 'failed', array(), $e->getMessage());
			$handler->update_reconciliation_session_status($session_id, 'failed');
		}
	}

	/**
	 * Unschedule all reconciliation actions when plugin is deactivated.
	 *
	 * @since 3.8.0
	 */
	public static function unscheduleReconciliation()
	{
		if (function_exists('as_unschedule_all_actions')) {
			as_unschedule_all_actions(self::RECONCILIATION_HOOK, array(), self::GROUP);
		}
	}
}
