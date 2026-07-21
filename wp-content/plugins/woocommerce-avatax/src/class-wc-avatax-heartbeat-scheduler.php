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
 * Handles scheduling and execution of CSS heartbeat API calls.
 *
 * @since 3.6.2
 */
class WC_AvaTax_Heartbeat_Scheduler {

	/** @var string The scheduled action hook name */
	const HEARTBEAT_HOOK = 'wc_avatax_css_heartbeat';
	
	/** @var int Minimum time between heartbeat calls (in seconds) to prevent duplicates */
	const HEARTBEAT_COOLDOWN = 300; // 5 minutes

	/**
	 * Initialize the scheduler.
	 *
	 * @since 3.6.2
	 */
	public function __construct()
	{
		add_action(self::HEARTBEAT_HOOK, array($this, 'sendHeartbeat'));
		// Use wp_loaded instead of init to avoid multiple executions during cron
		add_action('wp_loaded', array($this, 'scheduleHeartbeat'));
		add_action('wc_avatax_api_connected', array($this, 'sendImmediateAvaTaxHeartbeat'));
		add_action('wc_avatax_elr_api_connected', array($this, 'sendImmediateElrHeartbeat'));
	}

	/**
	 * Schedule the daily heartbeat if not already scheduled.
	 *
	 * This method is called during wp_loaded to ensure heartbeat is scheduled
	 * regardless of admin/frontend context. Action Scheduler handles the actual
	 * execution independently of the current request context.
	 *
	 * @since 3.6.2
	 */
	public function scheduleHeartbeat()
	{
		// Check if heartbeat is already scheduled
		$nextScheduled = as_next_scheduled_action(self::HEARTBEAT_HOOK);
		
		// If not scheduled, schedule it
		if (! $nextScheduled) {
			// Schedule new recurring heartbeat
			as_schedule_recurring_action(time(), DAY_IN_SECONDS, self::HEARTBEAT_HOOK, array(), 'avatax');
			
			// Log the scheduling for debugging
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log('Heartbeat scheduled successfully');
			}
		}
	}

	/**
	 * Send immediate AvaTax heartbeat when AvaTax API is connected.
	 *
	 * This bypasses the cooldown mechanism because it's critical for CCS
	 * to receive the connection notification immediately to create the
	 * integration record and display the tile in AvaTax portal.
	 *
	 * @since 3.6.2
	 */
	public function sendImmediateAvaTaxHeartbeat()
	{
		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log('Sending immediate AvaTax heartbeat due to API connection');
		}
		
		// Send AvaTax heartbeat immediately without cooldown check
		$this->sendHeartbeatForAvaTax();
		
		// Update the last attempt timestamp to prevent rapid subsequent calls
		update_option('wc_avatax_last_heartbeat_attempt', time());
	}

	/**
	 * Send immediate ELR heartbeat when ELR API is connected.
	 *
	 * This bypasses the cooldown mechanism because it's critical for CCS
	 * to receive the connection notification immediately to create the
	 * integration record and display the tile in AvaTax portal.
	 *
	 * @since 3.6.2
	 */
	public function sendImmediateElrHeartbeat()
	{
		if (wc_avatax()->elr_logging_enabled()) {
			wc_avatax()->log_elr('Sending immediate ELR heartbeat due to API connection');
		}
		
		// Send ELR heartbeat immediately without cooldown check
		$this->sendHeartbeatForElr();
		
		// Update the last attempt timestamp to prevent rapid subsequent calls
		update_option('wc_avatax_last_heartbeat_attempt', time());
	}

	/**
	 * Send the CSS heartbeat API call for AvaTax and ELR.
	 *
	 * @since 3.6.2
	 */
	public function sendHeartbeat()
	{
		// Check if we're in cooldown period to prevent duplicate calls
		$lastHeartbeat = get_option('wc_avatax_last_heartbeat_attempt', 0);
		$currentTime = time();
		
		// Allow filtering of cooldown period
		$cooldownPeriod = apply_filters('wc_avatax_heartbeat_cooldown', self::HEARTBEAT_COOLDOWN);
		
		if (($currentTime - $lastHeartbeat) < $cooldownPeriod) {
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log("Heartbeat skipped - within cooldown period ({$cooldownPeriod}s)");
			}
			return;
		}
		
		// Update the last attempt timestamp before sending
		update_option('wc_avatax_last_heartbeat_attempt', $currentTime);
		
		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log('Sending heartbeat for configured services');
		}
		
		$this->sendHeartbeatForAvaTax();
		$this->sendHeartbeatForElr();
	}

	/**
	 * Send the CSS heartbeat API call.
	 *
	 * @since 3.6.2
	 */
	public function sendHeartbeatForAvaTax()
	{
		// Only send heartbeat if AvaTax is properly configured
		if (! $this->isAvaTaxConfigured()) {
			return;
		}

		try {
			$accountId = get_option('wc_avatax_api_account_number');
			$licenseKey = get_option('wc_avatax_api_license_key');
			$environment = wc_avatax()->get_api_environment();

			$integrationApi = wc_avatax()->get_integration_api($accountId, $licenseKey, $environment);
			$integrationApi->sendCssHeartbeat('AvaTax');
			
			// Store the timestamp of the last successful heartbeat
			update_option('wc_avatax_last_heartbeat_attempt', time());
		} catch (Exception $e) {
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log('Heartbeat scheduler error: ' . $e->getMessage());
			}
		}
	}

	/**
	 * Send the CSS heartbeat API call for ELR.
	 *
	 * @since 3.6.2
	 */
	public function sendHeartbeatForElr()
	{
		// Only send heartbeat if ELR is properly configured
		if (! $this->isElrConfigured()) {
			return;
		}

		try {
			$clientId = get_option('wc_avatax_elr_client_id');
			$clientSecret = get_option('wc_avatax_elr_client_secret');
			$environment = wc_avatax()->get_api_environment();

			$integrationApi = wc_avatax()->get_integration_api($clientId, $clientSecret, $environment, true);
			$integrationApi->sendCssHeartbeat('ELR');
			
			// Store the timestamp of the last successful ELR heartbeat
			update_option('wc_avatax_last_heartbeat_attempt', time());
		} catch (Exception $e) {
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr('ELR Heartbeat scheduler error: ' . $e->getMessage());
			}
		}
	}

	/**
	 * Check if AvaTax is properly configured.
	 *
	 * @since 3.6.2
	 * @return bool
	 */
	private function isAvaTaxConfigured()
	{
		$accountId = get_option('wc_avatax_api_account_number');
		$licenseKey = get_option('wc_avatax_api_license_key');
		$websiteId = get_option('wc_avatax_website_id');

		return ! empty($accountId) && ! empty($licenseKey) && ! empty($websiteId);
	}

	/**
	 * Check if AvaTax ELR is properly configured.
	 *
	 * @since 3.6.2
	 * @return bool
	 */
	private function isElrConfigured()
	{
		$accountId = get_option('wc_avatax_elr_client_id');
		$licenseKey = get_option('wc_avatax_elr_client_secret');
		$websiteId = get_option('wc_avatax_website_id');

		return ! empty($accountId) && ! empty($licenseKey) && ! empty($websiteId);
	}

	/**
	 * Unschedule the heartbeat when plugin is deactivated.
	 *
	 * @since 3.6.2
	 */
	public static function unscheduleHeartbeat()
	{
		as_unschedule_all_actions(self::HEARTBEAT_HOOK);
	}
}