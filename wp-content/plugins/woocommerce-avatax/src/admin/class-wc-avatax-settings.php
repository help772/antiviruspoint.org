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

use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined('ABSPATH') or exit;

/**
 * Set up the admin settings.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Settings {

	/** @var string $id The settings page ID used for hooks */
	protected $id = 'avatax';

	/** @var string $tab_id The tab ID displayed in URL and tabs array */
	protected $tab_id = 'avalara';


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->add_hooks();

		//wc_avatax()->logger()->test();
	}

	/**
	 * Adds action and filter hooks.
	 *
	 * @since 1.13.0
	 */
	private function add_hooks() {

		// Handle all settings redirects (Avalara tab when tax disabled + legacy Tax tab URLs)
		add_action('admin_init', [ $this, 'handle_settings_redirects' ]);

		// Add AvaTax as a separate top-level settings tab (only if WooCommerce tax is enabled)
		add_filter('woocommerce_settings_tabs_array', [ $this, 'add_settings_tab' ], 50);

		// Output sections for the Avalara tab (uses tab_id to match URL parameter)
		add_action('woocommerce_sections_' . $this->tab_id, [ $this, 'output_sections' ]);

		// Output the settings for Avalara tab (uses tab_id to match URL parameter)
		add_action('woocommerce_settings_' . $this->tab_id, [ $this, 'output_settings' ]);

		// Save the settings for Avalara tab (uses tab_id to match URL parameter)
		add_action('woocommerce_settings_save_' . $this->tab_id, [ $this, 'save_settings' ]);

		//display a custom license key field with landing message
		add_action('woocommerce_admin_field_wc_avatax_api_license_key_type', [ $this, 'display_api_license_key_field' ]);
		add_action('woocommerce_admin_field_wc_avatax_api_environment_type', [ $this, 'display_api_environment_field' ]);

		// clears the license key cache when the license key changes
		add_action('update_option_wc_avatax_api_license_key', [ $this, 'prune_account_number_cache' ], 9, 2);

		// clears the API account number cache when the account number changes
		add_action('update_option_wc_avatax_api_account_number', [ $this, 'prune_account_number_cache' ], 9, 2);
		add_action('update_option_wc_avatax_api_environment', [ $this, 'prune_account_number_cache' ], 9, 2);
	}

	/**
	 * Handles all redirects for Avalara settings.
	 *
	 * This method handles redirects:
	 * 1. Avalara tab to general settings when WooCommerce tax is disabled
	 * 2. Legacy Tax tab URLs to new Avalara tab (backward compatibility)
	 * 3. Reconciliation section to default Avalara tab when AvaTax is not connected
	 *
	 * Order matters: Check tax enabled status first to avoid double redirects
	 * (e.g., legacy URL → Avalara → general when tax disabled)
	 *
	 * @since 3.6.5
	 */
	public function handle_settings_redirects()
	{
		// Only redirect on WooCommerce settings page
		if (! isset($_GET['page']) || 'wc-settings' !== $_GET['page']) {
			return;
		}

		if (! isset($_GET['tab'])) {
			return;
		}

		$current_tab = sanitize_text_field(wp_unslash($_GET['tab']));
		$tax_enabled = wc_tax_enabled();

		// REDIRECT 1: Avalara tab to general settings if WooCommerce tax is disabled
		// This must be checked first to avoid double redirects for legacy URLs
		if ($this->tab_id === $current_tab && ! $tax_enabled) {
			$params = [
				'page' => 'wc-settings',
				'tab'  => 'general',
			];

			$redirect_url = add_query_arg($params, admin_url('admin.php'));
			wp_safe_redirect($redirect_url);
			exit;
		}

		// REDIRECT 2: Legacy Tax tab URLs to new Avalara tab (backward compatibility)
		// Only redirect to Avalara if tax is enabled; otherwise redirect to general
		if ('tax' === $current_tab && isset($_GET['section'])) {
			$section = sanitize_text_field(wp_unslash($_GET['section']));

			// Define sections that have moved from Tax tab to Avalara tab
			$movedSections = [
				'avatax',           // Main AvaTax settings
				'avatax-elr',      // E-Invoicing/ELR settings
				'avatax-business-license', // Business License/Tax Registrations settings
			];

			/**
			 * Filters the list of sections that have moved from Tax tab to Avalara tab.
			 *
			 * @since 3.6.4
			 * @param array $movedSections Array of section slugs that should be redirected.
			 */
			$movedSections = apply_filters('wc_avatax_legacy_tax_tab_sections', $movedSections);

			// Check if current section has moved to Avalara tab
			if (in_array($section, $movedSections, true)) {
				// If tax is disabled, redirect to general settings instead of Avalara
				if (! $tax_enabled) {
					$params = [
						'page' => 'wc-settings',
						'tab'  => 'general',
					];
				} else {
					// Build the new URL with Avalara tab
					$params = [
						'page' => 'wc-settings',
						'tab'  => $this->tab_id, // 'avalara'
					];

					// Keep the section parameter for subsections (e.g., e-invoicing, business-license)
					// For 'avatax' section, redirect to main Avalara tab (no section parameter)
					if ('avatax' !== $section) {
						$params['section'] = $section;
					}
				}

				$redirect_url = add_query_arg($params, admin_url('admin.php'));
				wp_safe_redirect($redirect_url);
				exit;
			}
		}

		// REDIRECT 3: Reconciliation requires AvaTax connected (direct URL when disconnected)
		if ($this->tab_id === $current_tab && isset($_GET['section'])) {
			$section = sanitize_text_field(wp_unslash($_GET['section']));
			if ('avatax-reconciliation' === $section && 'connected' !== get_transient('wc_avatax_connection_status')) {
				$params = [
					'page' => 'wc-settings',
					'tab'  => $this->tab_id,
				];
				$redirect_url = add_query_arg($params, admin_url('admin.php'));
				wp_safe_redirect($redirect_url);
				exit;
			}
		}
	}

	/**
	 * Add AvaTax as a separate top-level settings tab.
	 * Only displays when WooCommerce tax calculation is enabled.
	 *
	 * @since 3.6.4
	 * @param array $tabs The existing settings tabs.
	 * @return array The modified settings tabs.
	 */
	public function add_settings_tab($tabs) {

		// Only show Avalara tab if WooCommerce tax calculation is enabled
		if (! wc_tax_enabled()) {
			return $tabs;
		}

		// Insert Avalara tab right after the Tax tab
		$new_tabs = array();

		foreach ($tabs as $key => $label) {
			// Skip 'avalara' if it exists - we'll add it in the correct position
			if ($key === $this->tab_id) {
				continue;
			}

			$new_tabs[ $key ] = $label;
			if ('tax' === $key) {
				$new_tabs[ $this->tab_id ] = __('Avalara', 'woocommerce-avatax');
			}
		}

		return $new_tabs;
	}

	/**
	 * Get sections for the Avalara tab.
	 *
	 * @since 3.6.4
	 * @return array Sections array.
	 */
	public function get_sections() {

		$sections = [
			'' => __('AvaTax', 'woocommerce-avatax'),
		];

		/**
		 * Filter the sections for the Avalara tab.
		 *
		 * @since 3.6.4
		 * @param array $sections Sections array.
		 */
		return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
	}

	/**
	 * Output sections for the Avalara tab.
	 *
	 * @since 3.6.4
	 */
	public function output_sections() {

		global $current_section;

		$sections = $this->get_sections();

		if (empty($sections) || 1 === count($sections)) {
			return;
		}

		echo '<ul class="subsubsub avalara-subsubsub">';

		foreach ($sections as $id => $label) {
			echo '<li><a href="' .
				esc_url(
					admin_url('admin.php?page=wc-settings&tab=' .
						$this->tab_id . '&section=' . sanitize_title($id)
						)
				)
				. '" class="' . ($current_section == $id ? 'current' : '') . '">'
				. esc_html($label) . '</a></li>';
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 * Get the API settings.
	 *
	 * @since 1.0.0
	 * @return array $settings The API settings.
	 */
	public function get_api_settings() {

		$connection_status = get_transient('wc_avatax_connection_status');

		$settings = array(

			array(
				'name' => __('Connect to Avalara', 'woocommerce-avatax'),
				'type' => 'title'
			),
			array(
				'id'      => 'wc_avatax_api_environment',
				'name'    => __('Choose your account type', 'woocommerce-avatax'),
				'options' => array(
					'production'  => __('Production', 'woocommerce-avatax'),
					'development' => __('Development', 'woocommerce-avatax'),
				),
				'desc'=>'Select your Production or Development account.',
				'default' => 'production',
				'type'    => 'wc_avatax_api_environment_type',
			),

			array(
				'id'                => 'wc_avatax_api_account_number',
				'name'              => __('Account ID', 'woocommerce-avatax'),
				'type'              => 'text',
				'class'             => 'wc-avatax-connection-field',
				'css'               => 'min-width:300px;',
				'custom_attributes' => array(
					'data-wc-avatax-connection-status' => $connection_status,
				),
			),

			array(
				'id'                => 'wc_avatax_api_license_key',
				'name'              => __('License Key/Password', 'woocommerce-avatax'),
				'type'              => 'wc_avatax_api_license_key_type',
				'class'             => 'wc-avatax-connection-field',
				'css'               => 'min-width:300px;',
				'custom_attributes' => array(
					'data-wc-avatax-connection-status' => $connection_status,
				),
			),

			array(
				'type' => 'sectionend',
			),
		);

		/**
		 * Filter the API settings.
		 *
		 * @since 1.0.0
		 * @param array $settings The API settings.
		 */
		return (array) apply_filters('woocommerce_get_settings_' . $this->id . '_api', $settings);
	}

	/**
	 * Get all settings for the Avalara tab.
	 *
	 * @since 3.6.4
	 * @param string $current_section Current section ID.
	 * @return array Settings array.
	 */
	public function get_settings($current_section = '') {

		$settings = [];

		if ('' === $current_section) {
			// Main AvaTax settings (API credentials, etc.)
			$settings = $this->get_api_settings();
		}

		/**
		 * Filter the settings for the Avalara tab.
		 *
		 * @since 3.6.4
		 * @param array  $settings        Settings array.
		 * @param string $current_section Current section ID.
		 */
		return apply_filters('woocommerce_get_settings_' . $this->id, $settings, $current_section);
	}

	/**
	 * Output the settings for Avalara tab.
	 *
	 * @since 3.6.4
	 * @codeCoverageIgnore - Coverage tracking limitation: PHPUnit/Xdebug doesn't properly
	 *                       track coverage for methods called via partial mocks or child classes.
	 *                       Functionality is verified by tests, but coverage tool limitation
	 *                       prevents accurate tracking. Tests: test_output_settings,
	 *                       test_output_settings_with_section, test_output_settings_with_empty_settings
	 */
	public function output_settings() {

		global $current_section;

		// Get settings for current section
		$settings = $this->get_settings($current_section);

		// Output the settings
		WC_Admin_Settings::output_fields($settings);
	}

	/**
	 * Saves the settings for Avalara tab.
	 *
	 * @internal
	 *
	 * @since 3.6.4
	 *
	 * @global string $current_section The current settings section.
	 */
	public function save_settings() {
		global $current_section;

		// Only process for main AvaTax section (empty string)
		if ('' !== $current_section) {
			return;
		}

		$is_connecting = true;

		// Check if already connected
		if ('connected' === get_transient('wc_avatax_connection_status')) {
			$is_connecting = false;
		}

		// Save the settings
		$settings = $this->get_settings($current_section);
		$this->save_fields($settings);
		wc_avatax()->wc_avatax_utilities()->sync_origin_country_option();

		if ($is_connecting) {
			wc_avatax()->get_company_details('defaultCompany');
		}

		// Reset transients
		delete_transient('wc_avatax_connection_status');
		delete_transient('wc_avatax_subscribed');

		// Check API and trigger hooks
		if ($this->get_plugin()->check_api(false)) {
			if ($is_connecting) {
				add_action('woocommerce_settings_saved', [ $this, 'save_default_fields' ]);
			}
		} else {
			if ($is_connecting) {
				add_action('woocommerce_settings_saved', [ $this, 'clear_default_fields' ]);
			}
		}
	}
	
	/**
	 * Save default fields. Enables all visible settings when connecting to AvaTAx.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function save_default_fields() {
		wc_avatax()->wc_avatax_utilities()->save_default_fields();
		$this->send_avatax_settings_to_cup();

		/**
		 * Fires after AvaTax settings are sent to CCS and the API connection is established.
		 *
		 * @since 3.6.0
		 */
		do_action('wc_avatax_api_connected');

		//Refresh logger instance:
		if(wc_avatax()->refresh_logger()){
			//Logging Connect event
			wc_avatax()->logger()->log_event("Connect", "save_default_fields", "Connecting to AvaTax");
		}
		else
		{
			wc_avatax()->log("log_event - Connect, function name - save_default_fields, message - Connecting to AvaTax");
		}

		// Trigger transaction push to CCS after successful connection
		// First time: sends full data (firstCall=true)
		// Subsequent times: sends empty data (firstCall=false)
		if ($this->transaction_push_enabled() && wc_avatax()->get_transaction_push_handler()) {
			wc_avatax()->get_transaction_push_handler()->trigger_push();
		}

	}

	/**
	 * Clears default fields.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function clear_default_fields() {
		wc_avatax()->wc_avatax_utilities()->clear_default_fields();

		if(!$this->get_plugin()->has_api_credentials_set()){
			WC_Admin_Settings::add_error(__('Enter Account ID and License key.', 'woocommerce-avatax'));
		}
	}

	/**
	 * Save the settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields the settings fields to save
	 */
	private function save_fields($fields) {
		WC_Admin_Settings::save_fields($fields);
	}

	/**
	 * Displays the API License field with landing message and connect, disconnect, test connection button.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	public function display_api_license_key_field($options) {

		$id           = $options['id'];
		$label        = $options['title'];
		$value		  = $options['value'];
		$company_name = get_option('wc_avatax_company_name');
		$env 		  = get_option('wc_avatax_api_environment', 'production');
		$message = sprintf(__('If you are new to Avalara, %1$ssign up%2$s to generate your account information.', 'woocommerce-avatax'),
			'<a href="https://www.avalara.com/us/en/get-started.html" target="_blank">',
			'</a>'
		);
		$application_id = wc_avatax()::CONNECTOR_ID;
		$website_id = get_option("wc_avatax_website_id");
		$edit_config_link = ($env === 'production' ? ('https://integrations.avalara.com/#/advance-configuration-settings/a/' . $application_id . '/c/' . $website_id) : ('https://sandbox.integrations.avalara.com/#/advance-configuration-settings/a/' . $application_id . '/c/' . $website_id));

		$connected_message = __('<p>Your WooCommerce store is connected to Company <b>'
								.$company_name
								.'</b> in Avalara.<br /><br /> Go <a href="'
								. ($env == 'production' ? 'https://integrations.avalara.com/' : 'https://sandbox.integrations.avalara.com/') 
								. '" target="_blank"> back to Avalara</a> to finish setting up the tax profile for Company <b>'
								. $company_name
								.'</b>.</p><button type="button" class="notice-dismiss">'.
								'<span class="screen-reader-text">Dismiss this notice.</span></button>'.
								'<span class="info-icon dashicons"></span>',
								'woocommerce-avatax');
		require_once($this->get_plugin()->get_plugin_path() . '/src/admin/views/html-field-api-license-key.php');
	}

	/**	
	 * Displays the API environment field.
	 *
	 * @since 2.7.0
	 */
	public function display_api_environment_field($options) {
		$value		  = get_option("wc_avatax_api_environment");
		require_once($this->get_plugin()->get_plugin_path() . '/src/admin/views/html-field-api-environment.php');
	}

	/**	
	 * Gets the Nexus enabled countries.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	private function get_supported_countries() : array {
		
		$supported_countries = $this->get_plugin()->get_landed_cost_handler()->get_supported_countries();
		return $supported_countries;
	}

	/**	
	 * Fetches ECM enabled Countries from Nexus.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	public function get_ecm_enabled_countries(): array {
		$supported_countries = $this->get_supported_countries();
		$ecm_enabled_countries = [
			(in_array('US', $supported_countries) ? 'US' : ''),
			(in_array('CA', $supported_countries)  ? 'CA' : '')
		];
		array_filter($ecm_enabled_countries);
		return $ecm_enabled_countries;
	}

	/**
	 * Determines whether the API environment is set to production.
	 *
	 * @since 1.13.0
	 * @deprecated 1.16.0
	 *
	 * @return bool
	 */
	public function is_api_environment_production() : bool {

		wc_deprecated_function(__METHOD__, '1.16.0');

		return 'production' === get_option('wc_avatax_api_environment');
	}

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 1.13.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin() : WC_AvaTax {

		return wc_avatax();
	}

	/**
	 * Caches the company ID to prevent race conditions with other requests that depend on it.
	 *
	 * @since 1.13.0
	 */
	protected function cache_company_id() {

		wc_avatax()->get_company_id();
	}

	/**
	 * Save default fields. Enables all visible settings when connecting to AvaTAx.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function save_company_default_fields()
	{
		wc_avatax()->wc_avatax_utilities()->save_company_default_fields();
	}

	/**
	 * Clears the account number cache when updating the account number.
	 *
	 * Also stops sync in progress if the account number changes when saving settings.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 *
	 * @param string|mixed $old_account_number
	 * @param string|mixed $new_account_number
	 * @return void
	 */
	public function prune_account_number_cache($old_account_number, $new_account_number)
	{
	if ($old_account_number === $new_account_number) {
			return;
		}
		wc_avatax()->get_landed_cost_sync_handler()->stop_syncing();
		wc_avatax()->clear_account_number_cache();
	}

	/**
	 * Sends configuration setting to CUP
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	protected function send_avatax_settings_to_cup()
	{
		$integration_api = wc_avatax()->wc_avatax_utilities()->get_integration_api();
		$response = $integration_api->send_settings_to_cup('POST');
		if($response instanceof stdClass && empty(((array)$response))){
			WC_Admin_Settings::add_error(__('Unable to connect to Avalara API service, please try again after some time.', 'woocommerce-avatax'));
			wc_avatax()->wc_avatax_utilities()->disconnect_avatax();
			$set = $this->get_settings();
			set_transient("wc_avatax_ccs_error", 'yes', 60);
		}
	}

	/**
	 * Checks if transaction push is enabled.
	 *
	 * @since 3.6.4
	 *
	 * @return bool
	 */
	public function transaction_push_enabled()
	{
		return get_option('wc_avatax_transaction_push', 'no') === 'yes';
	}
}
