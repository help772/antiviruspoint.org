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

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined('ABSPATH') or exit;

/**
 * Reconciliation section manager.
 *
 * Manages the reconciliation utility section within the Avalara settings tab.
 * Handles display of the reconciliation interface, tab navigation, and prepares
 * the infrastructure for data fetching and comparison.
 *
 * @since 3.8.0
 */
class WC_AvaTax_Reconciliation {

	/** @var string $id The settings page ID */
	protected $id = 'avatax-reconciliation';

	/** @var string $tabId The tab ID displayed in URL */
	protected $tabId = 'avalara';

	/**
	 * Constructs the class.
	 *
	 * @since 3.8.0
	 */
	public function __construct()
	{
		$this->add_hooks();
	}

	/**
	 * Adds action and filter hooks.
	 *
	 * @since 3.8.0
	 */
	private function add_hooks()
	{
		// Add the settings section to the AvaTax tab
		add_filter('woocommerce_get_sections_avatax', [ $this, 'add_settings_section' ]);

		// Output the settings
		add_action('woocommerce_settings_avalara', [ $this, 'output_settings' ]);

		// Display custom reconciliation content
		add_action(
			'woocommerce_admin_field_wc_avatax_reconciliation_content_type',
			[ $this, 'display_reconciliation_content' ]
		);

		// Save the settings (no actual settings to save, but hook is required)
		add_action('woocommerce_settings_save_avalara', [ $this, 'save_settings' ]);

		// Hide the save button on reconciliation page
		add_action('admin_head', [ $this, 'hide_save_button_css' ]);

		// Enqueue reconciliation-specific scripts and styles
		add_action('admin_enqueue_scripts', [ $this, 'enqueue_scripts_styles' ]);

		// AJAX handler for fetching reconciliation data
		add_action('wp_ajax_wc_avatax_get_reconciliation_data', [ $this, 'ajax_get_reconciliation_data' ]);
		// AJAX handlers for paginated missing orders and mismatches (batched display)
		add_action('wp_ajax_wc_avatax_get_reconciliation_missing_orders', [ $this, 'ajax_get_reconciliation_missing_orders' ]);
		add_action('wp_ajax_wc_avatax_get_reconciliation_mismatches', [ $this, 'ajax_get_reconciliation_mismatches' ]);
		// Cleanup batch data after UI rendered or user left
		add_action('wp_ajax_wc_avatax_reconciliation_cleanup_batches', [ $this, 'ajax_cleanup_reconciliation_batches' ]);
		// Background job status (polling)
		add_action('wp_ajax_wc_avatax_get_reconciliation_job_status', [ $this, 'ajax_get_reconciliation_job_status' ]);
		// Previous runs history
		add_action('wp_ajax_wc_avatax_get_reconciliation_runs', [ $this, 'ajax_get_reconciliation_runs' ]);
	}

	/**
	 * Add the Reconciliation section to the AvaTax tab.
	 *
	 * @since 3.8.0
	 * @param array $sections The existing AvaTax sections.
	 * @return array The modified AvaTax sections.
	 */
	public function add_settings_section($sections)
	{
		// Only show reconciliation section if AvaTax is connected
		if ('connected' !== get_transient('wc_avatax_connection_status')) {
			return $sections;
		}

		$sections[ $this->id ] = __('Reconciliation', 'woocommerce-avatax');

		return $sections;
	}

	/**
	 * Get Reconciliation settings.
	 *
	 * @since 3.8.0
	 * @return array $settings Reconciliation settings.
	 */
	public function get_reconciliation_settings()
	{
		$settings = array(
			array(
				'type' => 'wc_avatax_reconciliation_content_type',
			),
			array(
				'type' => 'sectionend',
			),
		);
			
		return (array) apply_filters('woocommerce_get_settings_' . $this->id, $settings);
	}

	/**
	 * Get all of the settings.
	 *
	 * @since 3.8.0
	 * @return array $settings The settings.
	 */
	public function get_settings()
	{
		$settings = $this->get_reconciliation_settings();
		
		return apply_filters('woocommerce_get_settings_' . $this->id, $settings);
	}

	/**
	 * Output the settings for Reconciliation section within AvaTax tab.
	 *
	 * @since 3.8.0
	 */
	public function output_settings()
	{
		global $current_section;

		// Only output for Reconciliation section
		if ($this->id !== $current_section) {
			return;
		}

		$settings = $this->get_reconciliation_settings();

		// Output the settings
		WC_Admin_Settings::output_fields($settings);
	}

	/**
	 * Displays the Reconciliation content with tabs.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function display_reconciliation_content()
	{
		// Include the main reconciliation view template
		$templatePath = $this->get_plugin()->get_plugin_path() .
			'/src/reconciliation/views/html-reconciliation-main.php';
		
		if (file_exists($templatePath)) {
			require_once($templatePath);
		}
	}

	/**
	 * Enqueue reconciliation-specific scripts and styles.
	 *
	 * @since 3.8.0
	 * @param string $hookSuffix The current screen suffix
	 */
	public function enqueue_scripts_styles($hookSuffix)
	{
		// Only enqueue on WooCommerce settings page
		if ('woocommerce_page_wc-settings' !== $hookSuffix) {
			return;
		}

		// Check if we're on the reconciliation section
		if (!isset($_GET['tab']) || 'avalara' !== $_GET['tab']) {
			return;
		}

		if (!isset($_GET['section']) || $this->id !== $_GET['section']) {
			return;
		}

		// Enqueue reconciliation styles
		wp_enqueue_style(
			'wc-avatax-reconciliation',
			wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-reconciliation.css',
			[],
			WC_AvaTax::VERSION
		);

		// Enqueue reconciliation scripts
		wp_enqueue_script(
			'wc-avatax-reconciliation',
			wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-reconciliation.js',
			['jquery'],
			WC_AvaTax::VERSION,
			true
		);

		// Localize script with data
		wp_localize_script('wc-avatax-reconciliation', 'wcAvataxReconciliation', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wc_avatax_reconciliation'),
			'strings' => [
				'loading' => __('Loading...', 'woocommerce-avatax'),
				'error' => __('An error occurred. Please try again.', 'woocommerce-avatax'),
				'waitingForResults' => __('Waiting for results. Run a search to see data.', 'woocommerce-avatax'),
			],
		]);
	}

	/**
	 * Saves the settings.
	 *
	 * @internal
	 *
	 * @since 3.8.0
	 *
	 * @global string $current_section The current settings section.
	 */
	public function save_settings()
	{
		global $current_section;
		
		if ($this->id === $current_section) {
			// No settings to save - Reconciliation is read-only
		}
	}

	/**
	 * Hide save button on Reconciliation settings page.
	 *
	 * @since 3.8.0
	 */
	public function hide_save_button_css()
	{
		global $current_section;
		
		if (isset($_GET['section']) && $_GET['section'] === $this->id) {
			echo '<style>.woocommerce-save-button, '.
			'input[name="save"], '.
			'.submit input[type="submit"], '.
			'.woocommerce-settings-save { display: none !important; }</style>';
		}
	}

	/**
	 * AJAX handler for fetching reconciliation data.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function ajax_get_reconciliation_data()
	{
		// Verify nonce
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!$nonce || !wp_verify_nonce($nonce, 'wc_avatax_reconciliation')) {
			wp_send_json_error(array(
				'message' => __('Security check failed.', 'woocommerce-avatax'),
			));
		}

		// Check user capabilities
		if (!current_user_can('manage_woocommerce')) {
			wp_send_json_error(array(
				'message' => __('You do not have permission to access this data.', 'woocommerce-avatax'),
			));
		}

		// Get and sanitize filters from request
		$filters = array(
			'from_date'      => isset($_POST['filters']['from_date']) ? sanitize_text_field(wp_unslash($_POST['filters']['from_date'])) : '',
			'to_date'        => isset($_POST['filters']['to_date']) ? sanitize_text_field(wp_unslash($_POST['filters']['to_date'])) : '',
			'document_type'  => isset($_POST['filters']['document_type']) ? sanitize_text_field(wp_unslash($_POST['filters']['document_type'])) : 'SalesInvoice',
			'search'         => isset($_POST['filters']['search']) ? sanitize_text_field(wp_unslash($_POST['filters']['search'])) : '',
		);

		// Validate date range is at most 3 months
		$from_date = $filters['from_date'] ?: gmdate('Y-m-01');
		$to_date = $filters['to_date'] ?: gmdate('Y-m-d');
		$from_dt = DateTime::createFromFormat('Y-m-d', $from_date);
		$to_dt = DateTime::createFromFormat('Y-m-d', $to_date);
		if ($from_dt && $to_dt) {
			$max_to = (clone $from_dt)->modify('+3 months');
			if ($to_dt > $max_to) {
				wp_send_json_error(array(
					'message' => __('Date range cannot exceed 3 months. Please adjust From or To date.', 'woocommerce-avatax'),
				));
			}
		}

		try {
			require_once($this->get_plugin()->get_plugin_path() . '/src/reconciliation/class-wc-avatax-reconciliation-handler.php');
			$handler = new WC_AvaTax_Reconciliation_Handler();

			// Run via Action Scheduler (cron): set pending, schedule single action, return immediately
			$session_id = function_exists('wp_generate_uuid') ? wp_generate_uuid() : uniqid('recon_', true);
			$handler->set_reconciliation_job_status($session_id, 'pending', $filters, '');
			$from_date = !empty($filters['from_date']) ? $filters['from_date'] : gmdate('Y-m-01');
			$to_date = !empty($filters['to_date']) ? $filters['to_date'] : gmdate('Y-m-d');
			$document_type = !empty($filters['document_type']) ? $filters['document_type'] : 'SalesInvoice';
			$handler->ensure_initial_reconciliation_session_row($session_id, $from_date, $to_date, $document_type);

			$scheduler = $this->get_plugin()->get_reconciliation_scheduler();
			if ($scheduler) {
				$scheduler->scheduleReconciliationRun($session_id);
			}

			wp_send_json_success(array(
				'session_id' => $session_id,
				'status'     => 'pending',
			));
		} catch (Exception $e) {
			wc_avatax()->log(sprintf('Reconciliation AJAX error: %s', $e->getMessage()));
			wp_send_json_error(array(
				'message' => sprintf(
					/* translators: %s: error message */
					__('Failed to start reconciliation: %s', 'woocommerce-avatax'),
					$e->getMessage()
				),
			));
		}
	}

	/**
	 * AJAX handler for reconciliation job status (polling when running in background).
	 *
	 * @since 3.8.0
	 * @return void
	 */
	public function ajax_get_reconciliation_job_status()
	{
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!$nonce || !wp_verify_nonce($nonce, 'wc_avatax_reconciliation')) {
			wp_send_json_error(array('message' => __('Security check failed.', 'woocommerce-avatax')));
		}
		if (!current_user_can('manage_woocommerce')) {
			wp_send_json_error(array('message' => __('You do not have permission.', 'woocommerce-avatax')));
		}
		$session_id = isset($_POST['session_id']) ? trim(sanitize_text_field(wp_unslash($_POST['session_id']))) : '';
		if (empty($session_id)) {
			wp_send_json_error(array('message' => __('Missing session ID.', 'woocommerce-avatax')));
		}
		require_once($this->get_plugin()->get_plugin_path() . '/src/reconciliation/class-wc-avatax-reconciliation-handler.php');
		$handler = new WC_AvaTax_Reconciliation_Handler();

		$db_status = $handler->get_reconciliation_session_status_from_db($session_id);

		if ($db_status === 'completed') {
			$overview = $handler->get_reconciliation_overview_from_batches($session_id);
			wp_send_json_success(array(
				'status' => 'completed',
				'session_id' => $session_id,
				'overview' => $overview,
				'missing_orders' => array('count' => isset($overview['missing_in_avalara']) ? $overview['missing_in_avalara'] : 0),
				'mismatches' => array('count' => isset($overview['mismatches']) ? $overview['mismatches'] : 0),
			));
		}

		if ($db_status === 'failed') {
			$status = $handler->get_reconciliation_job_status($session_id);
			wp_send_json_success(array(
				'status' => 'failed',
				'error' => !empty($status['error']) ? $status['error'] : __('Reconciliation failed.', 'woocommerce-avatax'),
			));
		}

		if ($db_status === 'pending') {
			wp_send_json_success(array(
				'status' => 'pending',
			));
		}

		$status = $handler->get_reconciliation_job_status($session_id);
		if ($status['status'] === 'completed' || ($status['status'] === 'unknown' && $handler->has_reconciliation_batches($session_id))) {
			$overview = $handler->get_reconciliation_overview_from_batches($session_id);
			wp_send_json_success(array(
				'status' => 'completed',
				'session_id' => $session_id,
				'overview' => $overview,
				'missing_orders' => array('count' => isset($overview['missing_in_avalara']) ? $overview['missing_in_avalara'] : 0),
				'mismatches' => array('count' => isset($overview['mismatches']) ? $overview['mismatches'] : 0),
			));
		}

		wp_send_json_success(array(
			'status' => $db_status ?: $status['status'],
		));
	}

	/**
	 * AJAX handler for paginated missing orders (batched display).
	 *
	 * @since 3.8.0
	 * @return void
	 */
	public function ajax_get_reconciliation_missing_orders()
	{
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!$nonce || !wp_verify_nonce($nonce, 'wc_avatax_reconciliation')) {
			wp_send_json_error(array('message' => __('Security check failed.', 'woocommerce-avatax')));
		}
		if (!current_user_can('manage_woocommerce')) {
			wp_send_json_error(array('message' => __('You do not have permission to access this data.', 'woocommerce-avatax')));
		}
		$session_id = isset($_POST['session_id']) ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';
		$page = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
		$per_page = isset($_POST['per_page']) ? max(1, min(100, (int) $_POST['per_page'])) : 20;
		if (empty($session_id)) {
			wp_send_json_error(array('message' => __('Missing session ID.', 'woocommerce-avatax')));
		}
		require_once($this->get_plugin()->get_plugin_path() . '/src/reconciliation/class-wc-avatax-reconciliation-handler.php');
		$handler = new WC_AvaTax_Reconciliation_Handler();
		$data = $handler->get_reconciliation_missing_orders_page($session_id, $page, $per_page);
		wp_send_json_success($data);
	}

	/**
	 * AJAX handler for paginated mismatches (batched display).
	 *
	 * @since 3.8.0
	 * @return void
	 */
	public function ajax_get_reconciliation_mismatches()
	{
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!$nonce || !wp_verify_nonce($nonce, 'wc_avatax_reconciliation')) {
			wp_send_json_error(array('message' => __('Security check failed.', 'woocommerce-avatax')));
		}
		if (!current_user_can('manage_woocommerce')) {
			wp_send_json_error(array('message' => __('You do not have permission to access this data.', 'woocommerce-avatax')));
		}
		$session_id = isset($_POST['session_id']) ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';
		$page = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
		$per_page = isset($_POST['per_page']) ? max(1, min(100, (int) $_POST['per_page'])) : 20;
		if (empty($session_id)) {
			wp_send_json_error(array('message' => __('Missing session ID.', 'woocommerce-avatax')));
		}
		require_once($this->get_plugin()->get_plugin_path() . '/src/reconciliation/class-wc-avatax-reconciliation-handler.php');
		$handler = new WC_AvaTax_Reconciliation_Handler();
		$data = $handler->get_reconciliation_mismatches_page($session_id, $page, $per_page);
		wp_send_json_success($data);
	}

	/**
	 * AJAX handler to delete reconciliation batch data for a session (after UI rendered or user left).
	 *
	 * @since 3.8.0
	 * @return void
	 */
	public function ajax_cleanup_reconciliation_batches()
	{
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!$nonce || !wp_verify_nonce($nonce, 'wc_avatax_reconciliation')) {
			wp_send_json_error(array('message' => __('Security check failed.', 'woocommerce-avatax')));
		}
		if (!current_user_can('manage_woocommerce')) {
			wp_send_json_error(array('message' => __('You do not have permission.', 'woocommerce-avatax')));
		}
		$session_id = isset($_POST['session_id']) ? trim(sanitize_text_field(wp_unslash($_POST['session_id']))) : '';
		if (empty($session_id)) {
			wp_send_json_success(array('deleted' => 0));
		}
		require_once($this->get_plugin()->get_plugin_path() . '/src/reconciliation/class-wc-avatax-reconciliation-handler.php');
		$handler = new WC_AvaTax_Reconciliation_Handler();
		$deleted = $handler->delete_reconciliation_batches_for_session($session_id);
		wp_send_json_success(array('deleted' => is_numeric($deleted) ? (int) $deleted : 0));
	}

	/**
	 * AJAX handler for fetching previous reconciliation runs.
	 *
	 * @since 3.8.0
	 * @return void
	 */
	public function ajax_get_reconciliation_runs()
	{
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (!$nonce || !wp_verify_nonce($nonce, 'wc_avatax_reconciliation')) {
			wp_send_json_error(array('message' => __('Security check failed.', 'woocommerce-avatax')));
		}
		if (!current_user_can('manage_woocommerce')) {
			wp_send_json_error(array('message' => __('You do not have permission.', 'woocommerce-avatax')));
		}
		require_once($this->get_plugin()->get_plugin_path() . '/src/reconciliation/class-wc-avatax-reconciliation-handler.php');
		$handler = new WC_AvaTax_Reconciliation_Handler();
		$runs = $handler->get_reconciliation_runs();
		wp_send_json_success(array('runs' => $runs));
	}

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 3.8.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin() : WC_AvaTax
	{
		return wc_avatax();
	}
}
