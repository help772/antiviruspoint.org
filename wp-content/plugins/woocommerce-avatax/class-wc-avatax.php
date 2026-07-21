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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_HS_API;
use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

/**
 * WooCommerce AvaTax main plugin class.
 *
 * @since 1.0.0
 */
class WC_AvaTax extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '3.8.5';

	/** relative path to main admin CSS */
	const ADMIN_CSS_PATH = '/assets/css/admin/wc-avatax-admin.min.css';

	/** relative path to ELR admin CSS */
	const ADMIN_CSS_ELR_PATH = '/assets/css/admin/wc-avatax-admin-elr.min.css';

	/** connector id string */
	const CONNECTOR_ID = 'a0n3300000FSK2zAAH';

	/** e-invoice connector id string */
	const ELR_CONNECTOR_ID = 'a0nUz00000KQeSvIAL';

	/** client version string */
	const CLIENT_STRING = 'a0oUz00000BFvUHIA1';
	const ELR_CLIENT_STRING = 'a0oUz00000BFvVtIAL';

	/** client version string */
	const SCHEMA_ID = 'avatax';

	/** client version string */
	const LAYOUT_ID = 'avaportal';

	/** @var WC_AvaTax_Logger_API the api class */
	protected $logger_api;

	/** @var WC_AvaTax_Logger_API the api class for ELR */
	protected $elr_logger_api;

	/** @var WC_AvaTax_Logger instance */
	public $logger;

	/** @var WC_AvaTax_Logger instance for ELR */
	public $elr_logger;

	/** plugin id */
	const PLUGIN_ID = 'avatax';
	const method = 'aes-256-cbc';

	/** @var WC_AvaTax single instance of this plugin */
	protected static $instance;

	/** @var WC_AvaTax_API the api class */
	protected $api;

	/** @var WC_AvaTax_Integration_API the api class */
	protected $integration_api;

	/** @var WC_AvaTax_Onboarding_API the onboarding api class */
	protected $onboarding_api;
	
	/** @var WC_AvaTax_Elr_API the api class */
	protected $elr_api;
	
	/** @var WC_AvaTax_NDI_API the NDI api class */
	protected $ndi_api;
	

	/** @var WC_AvaTax_HS_API the HS Classification API class */
	protected $hs_api;

	/** @var \WC_AvaTax_REST_API instance */
	protected $rest_api;

	/** @var \WC_AvaTax_Tax_Handler instance */
	protected $tax_handler;

	/** @var \WC_AvaTax_Order_Handler instance */
	protected $order_handler;

	/** @var \WC_AvaTax_Elr instance */
	protected $elr_handler;

	/** @var \WC_AvaTax_Business_License instance */
	protected $business_license_handler;


	/** @var \WC_AvaTax_NDI instance */
	protected $ndi_handler;

	/** @var \WC_AvaTax_Checkout_Handler instance */
	protected $checkout_handler;

	/** @var WC_AvaTax_Landed_Cost_Handler instance */
	protected $landed_cost_handler;

	/** @var \WC_AvaTax_Integrations instance */
	protected $integrations;

	/** @var \WC_AvaTax_Admin instance */
	protected $admin;

	/** @var \WC_AvaTax_Frontend instance */
	protected $frontend;

	/** @var \WC_AvaTax_AJAX instance */
	protected $ajax;

	/** @var \WC_AvaTax_Import_Export_Handler instance, adds support for import/export functionality */
	protected $import_export_handler;

	/** @var bool $logging_enabled Whether debug logging is enabled */
	private $logging_enabled;

	/** @var Landed_Cost_Sync_Handler instance, The handler for the synchronization process */
	protected $landed_cost_sync_handler;

	/** @var \WC_AvaTax_Utilities instance */
	protected $wc_avatax_utilites;

	/** @var \WC_AvaTax_Elr_Utilities instance */
	protected $wc_avatax_elr_utilities;

	/** @var \WC_AvaTax_Heartbeat_Scheduler instance */
	protected $heartbeat_scheduler;

	/** @var \WC_AvaTax_Reconciliation_Scheduler instance */
	protected $reconciliation_scheduler;

	/** @var \WC_AvaTax_Application_Response_Scheduler instance */
	protected $application_response_scheduler;

	/** @var \WC_AvaTax_Transaction_Push_Handler instance */
	protected $transaction_push_handler;

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct( self::PLUGIN_ID, self::VERSION, array(
			'text_domain' => 'woocommerce-avatax',
		) );

		// Turn off API request logging unless specified in the settings
		if (! $this->logging_enabled()) {
			remove_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ) );
		}

		// Turn off API request logging unless specified in the settings
		if (! $this->elr_logging_enabled()) {
			remove_action( 'wc_' . $this->get_id() . '_elr_api_request_performed', array( $this, 'log_elr_api_request' ) );
		}

		$this->create_website_id();
		$this->set_confic_sync_cron();

	}


	/**
	 * Flags TLS 1.2 as required for AvaTax API calls.
	 *
	 * @since 1.16.0
	 *
	 * @return bool
	 */
	public function require_tls_1_2() {

		return true;
	}

	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 1.7.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\AvaTax\Lifecycle( $this );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.12.0
	 */
	public function init_plugin() {

		parent::init_plugin();

		// General includes
		require_once( $this->get_plugin_path() . '/src/Traits/Resolves_Product_Item_Code.php' );

		// Set up the base tax handler
		$this->tax_handler = $this->load_class( '/src/class-wc-avatax-tax-handler.php', 'WC_AvaTax_Tax_Handler' );

		// set up the order handler
		$this->order_handler = $this->load_class( '/src/class-wc-avatax-order-handler.php', 'WC_AvaTax_Order_Handler' );

		// set up the elr handler
		$this->elr_handler = $this->load_class( '/src/e-invoicing/class-wc-avatax-elr.php', 'WC_AvaTax_Elr' );

		// set up the business license handler
		$this->business_license_handler = $this->load_class( '/src/business_license/class-wc-avatax-business-license.php', 'WC_AvaTax_Business_License' );

		// set up the ndi handler
		$this->ndi_handler = $this->load_class( '/src/ndi/class-wc-avatax-ndi.php', 'WC_AvaTax_NDI' );

		// set up the checkout handler
		$this->checkout_handler = $this->load_class( '/src/class-wc-avatax-checkout-handler.php', 'WC_AvaTax_Checkout_Handler' );

		// set up the integrations handler
		$this->integrations = $this->load_class( '/src/integrations/class-wc-avatax-integrations.php', 'WC_AvaTax_Integrations' );

		// Frontend includes
		if ( ! is_admin() ) {
			$this->frontend = $this->load_class( '/src/frontend/class-wc-avatax-frontend.php', 'WC_AvaTax_Frontend' );
		}

		// Admin includes
		if ( is_admin() ) { //&& ! wp_doing_ajax() ) {
			$this->admin = $this->load_class( '/src/admin/class-wc-avatax-admin.php', 'WC_AvaTax_Admin' );
			$this->load_api_classes();
		}

		/**
		 * Admin shipping transport field includes.
		 *
		 * @since 2.4.0
		 */
		if ( is_admin()) {
			$this->admin = $this->load_class( '/src/admin/class-wc-avatax-shipping-transport.php', 'WC_AvaTax_Shipping_Transport' );
		}

		// Import / Export handler needs to be available in admin over ajax
		if ( is_admin() ) {
			$this->import_export_handler = $this->load_class( '/src/integrations/class-wc-avatax-import-export-handler.php', 'WC_AvaTax_Import_Export_Handler' );
		}

		// AJAX includes
		if ( wp_doing_ajax() ) {
			$this->ajax = $this->load_class( '/src/class-wc-avatax-ajax.php', 'WC_AvaTax_AJAX' );
		}

		// REST API handler
		$this->rest_api = $this->get_rest_api_instance();

		$this->landed_cost_sync_handler = $this->get_landed_cost_sync_handler();

		//AvaTax Logger Instance
		$this->logger = $this->logger();
		$this->elr_logger = $this->elr_logger();

		// Initialize heartbeat scheduler (needs to run in all contexts)
		$this->heartbeat_scheduler = $this->load_class('/src/class-wc-avatax-heartbeat-scheduler.php', 'WC_AvaTax_Heartbeat_Scheduler');

		// Initialize reconciliation scheduler (Action Scheduler; Run from UI schedules a single action)
		$this->reconciliation_scheduler = $this->load_class('/src/class-wc-avatax-reconciliation-scheduler.php', 'WC_AvaTax_Reconciliation_Scheduler');

		// Initialize ELR Application Response (inbound docs) scheduler (Action Scheduler; recurring per user-set frequency)
		$this->application_response_scheduler = $this->load_class('/src/class-wc-avatax-application-response-scheduler.php', 'WC_AvaTax_Application_Response_Scheduler');

		// Register transaction push cron hook ONLY if event is scheduled (doesn't load handler class)
		if ( wp_next_scheduled( 'wc_avatax_process_transaction_push' ) ) {
			add_action( 'wc_avatax_process_transaction_push', array( $this, 'run_transaction_push' ) );
		}

	}


	/**
	 * Returns the base tax handler.
	 *
	 * @since 1.5.0
	 *
	 * @return \WC_AvaTax_Tax_Handler
	 */
	public function get_tax_handler() {

		return $this->tax_handler;
	}
	/**
	 * Sets the base tax handler.
	 *
	 * @since 2.4.0
	 *
	 * @return \WC_AvaTax_Tax_Handler
	 */
	public function set_tax_handler() {

		return $this->tax_handler = $this->load_class( '/src/class-wc-avatax-tax-handler.php', 'WC_AvaTax_Tax_Handler' );
	}

	/**
	 * Returns the admin class instance.
	 *
	 * @since 1.2.0
	 *
	 * @return \WC_AvaTax_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the frontend class instance.
	 *
	 * @since 1.2.0
	 *
	 * @return \WC_AvaTax_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Returns the ajax handler.
	 *
	 * @since 1.2.0
	 *
	 * @return \WC_AvaTax_AJAX
	 */
	public function get_ajax_handler() {

		return $this->ajax;
	}


	/**
	 * Returns the import/export handler class instance.
	 *
	 * @since 1.3.0
	 *
	 * @return \WC_AvaTax_Import_Export_Handler
	 */
	public function get_import_export_handler_instance() {

		return $this->import_export_handler;
	}

	/**
	 * Returns the reconciliation scheduler instance (Action Scheduler).
	 *
	 * @since 0.0.0
	 * @return \WC_AvaTax_Reconciliation_Scheduler|null
	 */
	public function get_reconciliation_scheduler() {

		return $this->reconciliation_scheduler;
	}

	/**
	 * Returns the order handler.
	 *
	 * @since 1.2.0
	 *
	 * @return \WC_AvaTax_Order_Handler The order handler object
	 */
	public function get_order_handler() {

		return $this->order_handler;
	}

	/**
	 * Returns the elr handler.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_AvaTax_Elr The order handler object
	 */
	public function get_elr_handler() {

		return $this->elr_handler;
	}

	/**
	 * Returns the Application Response (inbound documents) scheduler.
	 *
	 * @since 0.0.0
	 *
	 * @return \WC_AvaTax_Application_Response_Scheduler|null
	 */
	public function get_application_response_scheduler() {

		return $this->application_response_scheduler;
	}

	/**
	 * Returns the ndi handler.
	 *
	 * @since 3.0.0
	 *
	 * @return \WC_AvaTax_NDI The ndi handler object
	 */
	public function get_ndi_handler() {

		return $this->ndi_handler;
	}

	/**
	 * Returns the checkout handler.
	 *
	 * @since 1.2.0
	 *
	 * @return \WC_AvaTax_Checkout_Handler The checkout handler object
	 */
	public function get_checkout_handler() {

		return $this->checkout_handler;
	}


	/**
	 * Returns the integrations handler.
	 *
	 * @since 1.2.0
	 *
	 * @return \WC_AvaTax_Integrations integrations handler object
	 */
	public function get_integrations() {

		return $this->integrations;
	}


	/**
	 * Returns the landed cost handler.
	 *
	 * @since 1.5.0
	 *
	 * @return WC_AvaTax_Landed_Cost_Handler landed cost handler instance
	 */
	public function get_landed_cost_handler() : WC_AvaTax_Landed_Cost_Handler {

		if ( ! $this->landed_cost_handler instanceof WC_AvaTax_Landed_Cost_Handler ) {

			$this->landed_cost_handler = $this->load_class( '/src/class-wc-avatax-landed-cost-handler.php', 'WC_AvaTax_Landed_Cost_Handler' );

			$this->landed_cost_handler->add_hooks();
		}

		return $this->landed_cost_handler;
	}


	/**
	 * Returns the WP REST API handler instance.
	 *
	 * @since 1.7.0
	 *
	 * @return \WC_AvaTax_REST_API
	 */
	public function get_rest_api_instance() {

		if ( null === $this->rest_api ) {

			require_once( $this->get_plugin_path() . '/src/api/class-wc-avatax-rest-api.php' );

			$this->rest_api = new WC_AvaTax_REST_API( $this );
		}

		return $this->rest_api;
	}


	/**
	 * Returns the deprecated/removed hooks.
	 *
	 * @since 1.5.0
	 *
	 * @see Framework\SV_WC_Plugin::get_deprecated_hooks()
	 * @return array
	 */
	protected function get_deprecated_hooks() {

		$hooks = array(
			'wc_avatax_calculate_taxes' => array(
				'version'     => '1.5.0',
				'removed'     => true,
				'replacement' => 'wc_avatax_is_enabled',
				'map'         => true,
			),
		);

		return $hooks;
	}


	/** Admin methods ******************************************************/


	/**
	 * Renders a notice for the user to read the docs before adding add-ons.
	 *
	 * @since 1.0.0
	 *
	 * @see Framework\SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$screen = get_current_screen();

		if ( 'wc-settings' === Framework\SV_WC_Helper::get_requested_value( 'page' ) || 'plugins' === $screen->id ) {

			// if the API is not connected, display a persistent notice throughout WC settings screens
			if ( ! $this->check_api()) {
				if (!$this->is_plugin_settings() ) {
					// TODO: consider showing a similar notice for cross-border sync {IT 2022-01-12}
					$this->get_admin_notice_handler()->add_admin_notice( sprintf(
						/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - </a> tag */
						__( '%1$sWooCommerce AvaTax is almost ready!%2$s To get started, please ​%3$sconnect to AvaTax%4$s.', 'woocommerce-avatax' ),
						'<strong>',
						'</strong>',
						'<a href="' . esc_url( $this->get_settings_url() ) . '">',
						'</a>'
					), 'wc-avatax-welcome', array(
						'always_show_on_settings' => false,
						'dismissible'             => false,
					) );
				}
				else{
					//if api credentials are not valid, display a persistent notice throughout WC settings screens.
					if($this->has_api_credentials_set()){
						WC_Admin_Settings::add_error( __( 'Either your Account ID or License key is incorrect. Incase you have just generated a new license key, try again after few minutes.', 'woocommerce-avatax' ) );
					}
				}
			// otherwise, display various other config notices
			} else {

				/**
				 * Display a notice when the plugin has new update available.
				 *
				 * @since 3.6.4
				 */
				if($this->is_plugin_tab()){
					$this->avatax_update_notice();
				}

				// dismissable welcome notice
				$this->get_admin_notice_handler()->add_admin_notice( sprintf(
					/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - </a> tag */
					__( '%1$sThanks for installing WooCommerce AvaTax!%2$s Need help? %3$sRead the documentation%4$s.', 'woocommerce-avatax' ),
					'<strong>',
					'</strong>',
					'<a href="' . esc_url( $this->get_documentation_url() ) . '" target="_blank">',
					'</a>'
				), 'wc-avatax-welcome', array(
					'always_show_on_settings' => false,
					'dismissible'             => true,
					'notice_class'            => 'updated'
				) );

				// AvaTax calculation is enabled but global WC taxes are disabled
				if ( $this->get_tax_handler()->is_enabled() && ! wc_tax_enabled() ) {

					$this->get_admin_notice_handler()->add_admin_notice( sprintf(
						/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, , %3$s - <a> tag, %4$s - </a> tag */
						__( '%1$sWooCommerce taxes are disabled.%2$s To see tax rates from AvaTax at checkout, please %3$senable taxes%4$s for your store.', 'woocommerce-avatax' ),
						'<strong>', '</strong>',
						'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings' ) ) . '">', '</a>'
					), 'wc-taxes-deactivated-notice', array(
						'notice_class' => 'error',
					) );
				}

				// only display these on the plugin settings page
				if ( $this->is_plugin_settings() ) {

					// the origin address is not configured properly
					if ( ! $this->get_tax_handler()->is_origin_address_complete() ) {

						if($this->has_api_credentials_set() && $this->check_api()){
							wc_avatax()->wc_avatax_utilities()->update_origin_address();
						}

						if ( ! $this->get_tax_handler()->is_origin_address_complete() ) {
							$this->get_admin_notice_handler()->add_admin_notice( sprintf(
								/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
								__( '%1$sWooCommerce AvaTax%2$s calculation is disabled. Please configure your full Origin Address.', 'woocommerce-avatax' ),
								'<strong>', '</strong>'
							), 'wc-avatax-origin-notice', array(
								'notice_class' => 'error',
							) );
						}
					}
				}

				/**
				 * Fetches the tax codes from AvaTax and saves in db.
				 *
				 * @since 2.6.1
				 */
				try {
					global $wpdb;
					
					$table_name = 'wp_wc_avatax_tax_codes';
			
					if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) !== $table_name ) {
						$this->get_api()->get_tax_codes();
					}
				} catch ( Framework\SV_WC_API_Exception $e ) {

					if ( $this->logging_enabled() ) {
						$this->log( $e->getCode() . ' - ' . $e->getMessage() );
					}

					//Logging error
					wc_avatax()->logger()->log_exception("Backend", "add_admin_notices", $e->getMessage(), $e->getTraceAsString());
				}

			}
		}

		// display a notice when the legacy extension is deactivated
		if ( 'plugins' === $screen->id && 'yes' === get_option( 'wc_avatax_legacy_deactivated' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice( __( 'The legacy version of the WooCommerce AvaTax exension was deactivated.', 'woocommerce-avatax' ), 'legacy-deactivated-notice', array(
				'always_show_on_settings' => false,
				'notice_class'            => 'updated'
			) );

			delete_option( 'wc_avatax_legacy_deactivated' );
		}

		$this->maybe_add_product_sync_error_notice();
		$this->maybe_add_sync_weight_hint_notice();
	}


	/**
	 * May add an admin notice if the sync process had error responses.
	 *
	 * @since 1.13.0
	 */
	private function maybe_add_product_sync_error_notice() {

		if ( empty( get_option( 'wc_avatax_landed_cost_products_with_sync_errors' ) ) &&
		     empty( get_option( 'wc_avatax_landed_cost_products_with_sync_resolutions' ) ) ) {
			return;
		}

		$resync_link = wp_nonce_url( admin_url( 'admin-ajax.php?action=wc_avatax_resync_error_products' ), 'wc_avatax_resync_error_products', 'nonce' );

		$this->get_admin_notice_handler()->add_admin_notice(
			sprintf(
				/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - </a> tag, %5$s - <a> tag, %6$s - </a> tag */
				__( '<p>%1$sHeads up!%2$s Cross-border product sync encountered an error while syncing your products to AvaTax. Please try again or contact support if you\'re continuing to encounter this message.</p>%3$sResync products%4$s %5$sContact support%6$s', 'woocommerce-avatax' ),
				'<strong>', '</strong>',
				'<a href="' . esc_url( $resync_link ) . '" class="button button-primary">', '</a>',
				'<a href="https://woocommerce.com/my-account/create-a-ticket/" target="_blank" class="button">', '</a>'
			),
			'wc-avatax-sync-error-notice',
			[
				'notice_class'            => 'error',
				'always_show_on_settings' => false,
				'dismissible'             => true,
			] );
	}


	/**
	 * May add a hint admin notice about adding weights to products.
	 *
	 * @since 1.13.0
	 */
	private function maybe_add_sync_weight_hint_notice() {

		if ( ! $this->get_landed_cost_sync_handler()->is_syncing_active() ) {
			return;
		}

		$this->get_admin_notice_handler()->add_admin_notice( sprintf(
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			__( 'For best results in calculating cross-border duties, ensure weight is entered for your products in Product data > Shipping. %1$sLearn more%2$s.', 'woocommerce-avatax' ),
			'<a href="https://help.avalara.com/Avalara_Item_Classification_and_Cross-border/Understand_customs_duty_calculations_in_AvaTax" target="_blank">', '</a>'
		), 'wc-avatax-sync-weight-hint',
		[
			'always_show_on_settings' => false,
			'dismissible'             => true,
		] );
	}


	/** Helper methods ******************************************************/


	/**
	 * Main WC_AvaTax Instance, ensures only one instance is/can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @see wc_avatax()
	 * @return WC_AvaTax
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the plugin documentation URL.
	 *
	 * @since 1.0.0
	 *
	 * @see Framework\SV_WC_Plugin::get_documentation_url()
	 * @return string
	 */
	public function get_documentation_url() {

		return 'http://docs.woocommerce.com/document/woocommerce-avatax/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 1.7.1
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-avatax/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 1.0.0
	 *
	 * @see Framework\SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/tickets/';
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @see Framework\SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return 'WooCommerce AvaTax';
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.0.0
	 *
	 * @see Framework\SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns true if on the Avalara settings tab.
	 *
	 * Checks if the current page is the WooCommerce settings page
	 * with the Avalara tab selected (any section).
	 *
	 * @since 3.6.4
	 *
	 * @return boolean true if on the Avalara tab
	 */
	public function is_plugin_tab() {

		return isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] &&
			isset( $_GET['tab'] ) && 'avalara' === $_GET['tab'];
	}

	/**
	 * Returns true if on the plugin's settings page.
	 *
	 * @since 1.0.0
	 *
	 * @see Framework\SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {

		return ( $this->is_plugin_tab() && ( ! isset( $_GET['section'] ) || '' === $_GET['section'] ) ) ||
			( isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] && isset( $_GET['tab'] ) && 'avatax-landed-cost' === $_GET['tab'] );
	}

	/**
	 * Returns true if on the plugin's ELR settings page.
	 *
	 * @since 3.0.0
	 *
	 * @see Framework\SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the elr settings page
	 */
	public function is_plugin_elr_settings() {

		return $this->is_plugin_tab() && isset( $_GET['section'] ) && 'avatax-elr' === $_GET['section'];
	}

	/**
	 * Returns the plugin configuration URL.
	 *
	 * @since 1.0.0
	 *
	 * @see Framework\SV_WC_Plugin::get_settings_link()
	 * @param string $plugin_id optional plugin identifier.  Note that this can be a
	 *        sub-identifier for plugins with multiple parallel settings pages
	 *        (ie a gateway that supports both credit cards and echecks)
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=avalara' );
	}


	/**
	 * Determines if debug logging is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool $logging_enabled Whether debug logging is enabled.
	 */
	public function logging_enabled() {

		$this->logging_enabled = ( 'yes' === get_option( 'wc_avatax_debug' ) );

		/**
		 * Filter whether debug logging is enabled.
		 *
		 * @since 1.0.0
		 * @param bool $logging_enabled Whether debug logging is enabled.
		 */
		return apply_filters( 'wc_avatax_logging_enabled', $this->logging_enabled );
	}

	/**
	 * Determines if elr debug logging is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool $logging_enabled Whether debug logging is enabled.
	 */
	public function elr_logging_enabled() {
		$this->logging_enabled = ( 'yes' === get_option( 'wc_elr_debug' ) );
		/**
		 * Filter whether debug logging is enabled.
		 *
		 * @since 1.0.0
		 * @param bool $logging_enabled Whether debug logging is enabled.
		 */
		return apply_filters( 'wc_avatax_elr_logging_enabled', $this->logging_enabled);
	}


	/**
	 * Returns the API class instance.
	 *
	 * @since 2.8.0
	 *
	 * @return WC_AvaTax_Integration_API
	 */
	public function get_integration_api($account_id, $license_key, $environment, $generateElrToken = false) : WC_AvaTax_Integration_API {

		$this->load_api_classes();

		//Instantiate the API
		return $this->integration_api = new WC_AvaTax_Integration_API( $account_id, $license_key, $environment, $generateElrToken);
	}

	/**
	 * Gets the Onboarding API instance.
	 *
	 * @since 3.6.4
	 *
	 * @param string $account_id Avalara account ID
	 * @param string $license_key Avalara license key
	 * @param string $company_id Avalara company ID
	 * @param string $environment The current API environment
	 * @return WC_AvaTax_Onboarding_API
	 */
	public function get_onboarding_api($account_id, $license_key, $company_id, $environment) : WC_AvaTax_Onboarding_API {

		$this->load_api_classes();

		//Instantiate the API
		return $this->onboarding_api = new WC_AvaTax_Onboarding_API( $account_id, $license_key, $company_id, $environment);
	}

	/**
	 * Gets the configured API environment.
	 *
	 * @since 1.16.0
	 *
	 * @return string
	 */
	public function get_api_environment() : string {

		return (string) get_option( 'wc_avatax_api_environment', 'production' );
	}

	/** Gets the configured ELR API environment.
     *
     * @since 3.0.0
     *
     * @return string
     */
	public function get_elr_api_environment() : string {
        return (string) get_option( 'wc_avatax_elr_environment', 'production' );
    }	/**
	 * Returns the logger class instance.
	 *
	 * @since 2.8.1
	 *
	 * @return WC_AvaTax_Logger
	 */
	public function logger() : WC_AvaTax_Logger {

		// Return the logger object if already instantiated
		if ( is_object( $this->logger ) ) {
			return $this->logger;
		}

		if (!class_exists('WC_AvaTax_Logger')) {
			require_once( $this->get_plugin_path() . '/src/logger/class-wc-avatax-logger.php');
		}
		
		// Instantiate the API
		return $this->logger = new WC_AvaTax_Logger();
	}

	/* Returns the logger class instance for ELR.
	*
	* @since 3.0.0
	*
	* @return WC_AvaTax_Logger
	*/
   public function elr_logger() : WC_AvaTax_Logger {

	   // Return the logger object if already instantiated
	   if ( is_object( $this->elr_logger ) ) {
		   return $this->elr_logger;
	   }

	   if (!class_exists('WC_AvaTax_Logger')) {
		require_once( $this->get_plugin_path() . '/src/logger/class-wc-avatax-logger.php');
	   }
	   
	   // Instantiate the API
	   return $this->elr_logger = new WC_AvaTax_Logger(true);
   }

	/**
	 * Returns the API class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_AvaTax_API
	 */
	public function get_api() : WC_AvaTax_API {

		// Return the API object if already instantiated
		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		$this->load_api_classes();

		// Get the API token & secret
		$account_number = get_option( 'wc_avatax_api_account_number' );
		// $license_key    = get_option( 'wc_avatax_api_license_key' );
		$license_key    = get_option( 'wc_avatax_api_license_key' );
		$company_code   = get_option( 'wc_avatax_company_code' ); // TODO: set this on the request level?
		$environment    = $this->get_api_environment();

		// Instantiate the API
		return $this->api = new WC_AvaTax_API( $account_number, $license_key, $company_code, $environment );
	}

	/**
	 * Returns the ELT API class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_AvaTax_Elr_API
	 */
	public function get_elr_api() : WC_AvaTax_Elr_API {

		// Return the API object if already instantiated
		if ( is_object( $this->elr_api ) ) {
			return $this->elr_api;
		}

		$this->load_elr_api_classes();

		// Get the API token & secret
		$client_id = get_option( 'wc_avatax_elr_client_id' );
		$client_secret    = get_option( 'wc_avatax_elr_client_secret' );
		$environment    = $this->get_elr_api_environment();

		// Instantiate the API
		return $this->elr_api = new WC_AvaTax_Elr_API( $client_id, $client_secret, $environment );
	}

	/**
	 * Returns the NDI API class instance.
	 *
	 * @since 3.6.0
	 *
	 * @return WC_AvaTax_NDI_API
	 */
	public function get_ndi_api() : WC_AvaTax_NDI_API {

		// Return the API object if already instantiated
		if ( is_object( $this->ndi_api ) ) {
			return $this->ndi_api;
		}

		$this->load_ndi_api_classes();

		// Get the API token & secret (NDI uses same credentials as ELR)
		$client_id = get_option( 'wc_avatax_elr_client_id' );
		$client_secret    = get_option( 'wc_avatax_elr_client_secret' );
		$environment    = $this->get_elr_api_environment();

		// Instantiate the NDI API
		return $this->ndi_api = new WC_AvaTax_NDI_API( $client_id, $client_secret, $environment );
	}

	/**
	 * Refreshes the API class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_AvaTax_API
	 */
	public function refresh_elr_api(){
		$this->elr_api = null;
	}

	/**
	 * Refreshes the NDI API class instance.
	 *
	 * @since 3.6.0
	 *
	 * @return void
	 */
	public function refresh_ndi_api(){
		$this->ndi_api = null;
	}
	

	/**
	 * Refreshes the API class instance.
	 *
	 * @since 2.7.0
	 *
	 * @return WC_AvaTax_API
	 */
	public function refresh_api(){
		$this->api = null;
	}

	/**
	 * Returns the logger API class instance.
	 *
	 * @since 2.8.1
	 *
	 * @return WC_AvaTax_Logger_API
	 */
	public function get_logger_api() : WC_AvaTax_Logger_API {

		$this->load_api_classes();

		// Get the API token & secret
		$account_number = get_option( 'wc_avatax_api_account_number' );
		$license_key    = get_option( 'wc_avatax_api_license_key' );
		$environment    = $this->get_api_environment();

		// Return the API object if already instantiated
		if ($account_number && $license_key && is_object( $this->logger_api ) ) {
			return $this->logger_api;
		}

		// Instantiate the API
		return $this->logger_api = new WC_AvaTax_Logger_API( $account_number, $license_key, $environment);
	}

	/**
	 * Returns the logger API class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_AvaTax_Logger_API
	 */
	public function get_elr_logger_api() : WC_AvaTax_Logger_API {

		$this->load_api_classes();

		// Get the API token & secret
		$environment    = $this->get_elr_api_environment();
		$token = get_transient( "wc_avatax_elr_token" );

		// Return the API object if already instantiated
		if ($token && is_object( $this->elr_logger_api ) ) {
			return $this->elr_logger_api;
		}
		
		// Instantiate the API
		return $this->elr_logger_api = new WC_AvaTax_Logger_API( '', '', $environment, true);
	}

	/**
	 * Refreshes the logger class instance.
	 *
	 * @since 2.8.1
	 *
	 * @return WC_AvaTax_API
	 */
	public function refresh_logger(){
		$this->logger_api = null;
		$this->logger = null;

		if(get_transient( 'wc_avatax_connection_status', '' ) === 'connected'){
			//AvaTax Logger Instance
			$this->logger = $this->logger();
			return true;
		}
		return false;
	}

	/**
	 * Refreshes the logger class instance.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_AvaTax_API
	 */
	public function refresh_elr_logger(){
		$this->elr_logger_api = null;
		$this->elr_logger = null;

		if(get_transient( 'wc_avatax_elr_connection_status', '' ) === 'connected'){
			//AvaTax Logger Instance
			$this->logger = $this->elr_logger();
			return true;
		}
		return false;
	}

	/**
	 * Returns the HS Classification API class instance.
	 *
	 * @since 1.13.0
	 *
	 * @return WC_AvaTax_HS_API
	 */
	public function get_hs_api() {

		if ( is_object( $this->hs_api ) ) {
			return $this->hs_api;
		}

		$this->load_api_classes();

		$username    = get_option( 'wc_avatax_hs_api_username' );
		$password    = get_option( 'wc_avatax_hs_api_password' );
		$environment = $this->get_api_environment();

		return $this->hs_api = new WC_AvaTax_HS_API( $username, $password, $environment );
	}


	/**
	 * Determines if the connector is connected to AvaTax.
	 *
	 * @since 3.3.1
	 *
	 * @return bool Whether the connector is connected to AvaTax.
	 */
	public function is_connected() : bool {
		return 'connected' === get_transient( 'wc_avatax_connection_status' );
	}

	/**
	 * Determines if API credentials exist and are valid.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $check_cache Whether to check the cached result first.
	 * @return bool Whether the API credentials exist and are valid.
	 */
	public function check_api( $check_cache = true ) : bool {
		if ( $check_cache && ( $cache = get_transient( 'wc_avatax_connection_status' ) ) ) {

			if ( 'connected' === $cache ) {
				return true;
			}

			if ( 'not-connected' === $cache ) {
				return false;
			}
		}

		/**
		 * Filter the amount of time to keep the connection status cache.
		 *
		 * @since 1.0.0
		 * @param int $expiration The cache expiration, in seconds.
		 */
		$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );

		try {
			
			if(trim(get_option( 'wc_avatax_api_account_number', '' )) == '' || trim(get_option( 'wc_avatax_api_license_key', '' )) == '') {
				return false;
			}

			$response = $this->get_api()->test();
			if ( ! $response->is_authenticated() ) {
				throw new Framework\SV_WC_API_Exception( 'Not authenticated' );
			}

			set_transient( 'wc_avatax_connection_status', 'connected', $cache_expiration );

			return true;

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( $e->getCode() . ' - ' . $e->getMessage() );
			}

			set_transient( 'wc_avatax_connection_status', 'not-connected', $cache_expiration );

			wc_avatax()->log("Error: " . $e->getMessage() ."\n Stack trace: ". $e->getTraceAsString());

			return false;
		}
	}

/**
	 * Determines if ELR API credentials exist and are valid.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $check_cache Whether to check the cached result first.
	 * @return bool Whether the API credentials exist and are valid.
	 */
	public function check_elr_api( $check_cache = true ) : bool {
		if ( $check_cache && ( $cache = get_transient( 'wc_avatax_elr_connection_status' ) ) ) {

			if ( 'connected' === $cache ) {
				return true;
			}

			if ( 'not-connected' === $cache ) {
				return false;
			}
		}

		$cache_expiration = apply_filters( 'wc_avatax_elr_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );

		try {
			
			if(trim(get_option( 'wc_avatax_elr_client_id', '' )) == '' )
			{
				return false;
			}

			$response = $this->get_elr_api()->test();
			if ( ! $response ) {
				throw new Framework\SV_WC_API_Exception( 'Not authenticated' );
			}

			set_transient( 'wc_avatax_elr_connection_status', 'connected', $cache_expiration );

			return true;

		} 
		catch ( Framework\SV_WC_API_Exception $e ) {

			if ( $this->elr_logging_enabled() ) {
				$this->log_elr( $e->getCode() . ' - ' . $e->getMessage() );
			}

			set_transient( 'wc_avatax_elr_connection_status', 'not-connected', $cache_expiration );

			return false;
		}
	}
	
	/**
	 * Gets the AvaTax company ID.
	 *
	 * @since 1.13.0
	 *
	 * @return string
	 */
	public function get_company_id() : string {
		return get_option('wc_avatax_company_id', '');
	}

	/**
	 * Get the company details by filter method
	 *
	 * @since 2.10.0
	 *
	 * @return void
	 */
	public function get_company_details(string $type) : void {
		$company_code = get_option( 'wc_avatax_company_code' );
		try {
			if ( $this->has_api_credentials_set() && $this->check_api() && $api = $this->get_api() ) {
				if($type === 'companyByCode')
				{
					$response = $api->get_company_by_companyCode();
					update_option( 'wc_avatax_company_name', $response->get_company_name( $company_code ));
					update_option( 'wc_avatax_company_id', $response->get_company_id( $company_code ) );
				}
				if($type === 'defaultCompany')
				{
					$response_default = $api->get_default_company();
					$company_code_default = $response_default->get_default_company_code();
					update_option('wc_avatax_company_code', $response_default->get_default_company_code());
					update_option( 'wc_avatax_company_name', $response_default->get_company_name( $company_code_default ));
					update_option( 'wc_avatax_company_id', $response_default->get_company_id( $company_code_default ));
				}

			}

		} catch ( \Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Backend", "get_company_details", $e->getMessage(), $e->getTraceAsString());
		}

	}



	/**
	 * Get user email
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	public function get_user_email() : string {
		try {

			$user_info = $this->get_user_data();
			if(empty($user_info)){
				return '';
			}
			return $user_info['emailAddress'];
		} catch ( \Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Backend", "get_user_email", $e->getMessage(), $e->getTraceAsString());
		}
		return '';
	}
	/**
	 * Add customer to Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return bool
	 */
	public function add_customer_to_avatax($userId):bool {
		try {
			if(empty($userId)){
				return false;
			}
			else{
				if($this->has_api_credentials_set() && $api = $this->get_api()){
					$user = $api->add_customer_to_avatax($userId);
					if(empty($user)){
						return false;
					}
					else{
						return true;
					}
				}
			}
		} catch ( \Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}
			
			//Logging error
			wc_avatax()->logger()->log_exception("Backend", "add_customer_to_avatax", $e->getMessage(), $e->getTraceAsString());

			return false;
		}
		return false;
	}

	/**
	 * Get user data
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function get_user_data($userId='') : array {
		try {
			if (!empty($userId)){
				$user_id = $userId;
			}
			elseif (! empty($_GET['user_id']) && is_numeric($_GET['user_id'])){
				$user_id = absint( $_GET['user_id'] );
			}
			else{
				$user_id = get_current_user_id();
			}
			$user_info = get_userdata($user_id);
			$user_meta_data = get_metadata_raw('user',$user_id);
			if(!empty($user_info) && !empty($user_meta_data)){
					$filtered_user_data = array(
						'id' => $user_info->ID,
						'companyId' => $this->get_company_id(),
						'customerCode' => empty($user_meta_data['billing_email'])?"":$user_meta_data['billing_email'][0],
						'name' =>  (empty($user_meta_data['first_name'])?'':$user_meta_data['first_name'][0]). " ". (empty($user_meta_data['last_name'])?'':$user_meta_data['last_name'][0]),
						'line1' =>  empty($user_meta_data['billing_address_1'])?'':$user_meta_data['billing_address_1'][0],
						'line2' =>  empty($user_meta_data['billing_address_2'])?'':$user_meta_data['billing_address_2'][0],
						'city' =>  empty($user_meta_data['billing_city'])?'':$user_meta_data['billing_city'][0],
						'postalCode' =>  empty($user_meta_data['billing_postcode'])?'':$user_meta_data['billing_postcode'][0],
						'country' =>  empty($user_meta_data['billing_country'])?'':$user_meta_data['billing_country'][0],
						'emailAddress' =>$user_info->data->user_email,
						'alternateId'  =>$user_info->data->user_email."_".$user_id,
						'region'=> empty($user_meta_data['billing_state'])?'':$user_meta_data['billing_state'][0]
					);
					return $filtered_user_data;
			}
			else{
				return array();
			}
		} catch ( \Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Backend", "get_user_data", $e->getMessage(), $e->getTraceAsString());
		}
		return array();
	}
	/**
	 * Validate the customer data
	 *
	 * @since 2.6.0
	 *
	 * @return bool
	 */
	public function check_if_all_customer_is_valid($user_info):bool{
		try {

			if(empty($user_info['emailAddress']) || empty($user_info['name']) ||
			empty($user_info['line1']) || empty($user_info['city']) 
			|| empty($user_info['postalCode']) || empty($user_info['country'])|| empty($user_info['customerCode'])
			|| empty($user_info['alternateId']))
			{
				wc_avatax()->log("some user details are missing");
				return false;
			}
			else{
				return true;
			}
		} catch ( \Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Backend", "check_if_all_customer_is_valid", $e->getMessage(), $e->getTraceAsString());

			return false;
		}
	}
	/**
	 * Check if customer exists in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return object
	 */
	public function check_if_customer_exists_return($customerCode) {
		try {

			if($this->has_api_credentials_set() && $api = $this->get_api())
		{
			$customer = $api->get_customer_details($customerCode);
			if(empty((array) $customer )){
				return new stdClass();
			}
			else{
				return $customer;
			}
		}

		} catch ( \Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Backend", "check_if_customer_exists_return", $e->getMessage(), $e->getTraceAsString());
			
			return ;
		}
		return ;
	}
	/**
	 * Get Certificates
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function get_certificate_options($user_data)
	{
		try {
			
				if($this->has_api_credentials_set() && $api = $this->get_api())
				{
					
					$certificateList = [] ;
					if(!empty($user_data)){
					$options = $api->get_certificate_list(strval($user_data['customerCode']), strval($user_data['alternateId']));
					if(!empty( $options))
						{
							foreach($options as $customer)
							{
								$dataArr = (array) $customer->certificates;
								foreach($dataArr as $opt)
								{
									$filteredCertificateList = [] ;
									$filteredCertificateList[] = array(
										'id' => $opt->id,
										'state' => $opt-> exposureZone-> name,
										'signedDate' => empty($opt->signedDate)?"":$opt->signedDate,
										'expirationDate'      => empty($opt->expirationDate)?"":$opt->expirationDate,
										'status'     => $opt->status,
										'ecmStatus'  => isset($opt->ecmStatus) ? $opt->ecmStatus : '',
										'exemptionNumber'     => $opt->exemptionNumber,
										'companyId'     => $this->get_company_id(),
										'userId'		=> $user_data['id'],
										'customerCode'		=> $customer->customerCode
									);
									$certificateList = array_merge($certificateList,$filteredCertificateList);
								}
							}
						}
						return $certificateList;
				}
			}
		} catch ( \Exception $e ) {

			if ( $this->logging_enabled() ) {
				$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Backend", "get_certificate_options", $e->getMessage(), $e->getTraceAsString());

			return array();
		}
		return array();
	}
	/**
	 * Get Exposure Zones
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function get_exposure_zones(){
		try {

			if($this->has_api_credentials_set() && $api = $this->get_api())
				{
					$zones = $api->get_exposure_zones();
					$zone_html = "";
					$filtered_zones = array_filter($zones, [ $this, 'is_zone_applicable' ] );
					foreach ($filtered_zones as $key => $value) {
						$zone_html = $zone_html . "<option value = '" . $value->id . "'>"  . $value->name . "</option>";
					}
					return $zone_html;
				}
			} catch ( \Exception $e ) {

				if ( $this->logging_enabled() ) {
					$this->log( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
				}

				//Logging error
				wc_avatax()->logger()->log_exception("Backend", "get_exposure_zones", $e->getMessage(), $e->getTraceAsString());

				return array();
			}
	}
	/**
	 * Checks if the zone is applicable for ECM
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function is_zone_applicable($zone)
  	{
		$class_WC_AvaTax_Settings = new WC_AvaTax_Settings();
		$countries = $class_WC_AvaTax_Settings->get_ecm_enabled_countries();
  		return in_array($zone->country, $countries);
	}
	/**
	 * Clears the AvaTax company ID from the cache.
	 *
	 * @since 1.17.0
	 *
	 * @return void
	 */
	public function clear_company_id_cache() : void {

		delete_transient( 'wc_avatax_company_id' );
		delete_transient( 'wc_avatax_company_code' );
		delete_transient( 'wc_avatax_company_response' );

		/**
		 * Deleted below transients to get updated nexus on company change 
		 *
		 * @since 2.7.0
		 */
		
		delete_option( 'wc_avatax_landed_cost_supported_countries' );
		delete_option( 'wc_avatax_full_nexus_details');
		delete_option('wc_avatax_company_id');
		delete_option('wc_avatax_company_name');
		delete_option( 'wc_avatax_enable_vat' );
		delete_option( 'wc_avatax_enable_cross_border_classification' );
		delete_option( 'wc_avatax_api_product_countries_sync' );
		
	}

	/**
	 * Clears the AvaTax account number and license key from the cache.
	 *
	 * @since 2.3.0
	 *
	 * @return void
	 */
	public function clear_account_number_cache() : void {

		delete_transient( 'wc_avatax_api_account_number' );
		delete_transient( 'wc_avatax_api_license_key' );
		delete_transient( 'wc_avatax_api_environment' );
		delete_transient( 'wc_avatax_connection_status' );
		$this->api = null;
	}

	/**
	 * Gets the landed cost sync handler.
	 *
	 * @since 1.13.0
	 *
	 * @return Landed_Cost_Sync_Handler
	 */
	public function get_landed_cost_sync_handler() : Landed_Cost_Sync_Handler {

		if ( ! $this->landed_cost_sync_handler instanceof Landed_Cost_Sync_Handler ) {

			require_once( $this->get_plugin_path() . '/src/api/Models/HS_Classification_Model.php' );
			require_once( $this->get_plugin_path() . '/src/api/Models/HS_Item_Model.php' );
			require_once( $this->get_plugin_path() . '/src/Landed_Cost_Sync_Enqueued_Product.php' );

			$this->landed_cost_sync_handler = $this->load_class( '/src/Landed_Cost_Sync_Handler.php', Landed_Cost_Sync_Handler::class );
		}

		return $this->landed_cost_sync_handler;
	}

	/**
	 * Returns the transaction push handler.
	 *
	 * Lazy-loads the handler only when needed to save memory.
	 *
	 * @since 3.6.4
	 *
	 * @return WC_AvaTax_Transaction_Push_Handler
	 */
	public function get_transaction_push_handler() {

		if ( ! isset( $this->transaction_push_handler ) ) {
			$this->transaction_push_handler = $this->load_class( '/src/class-wc-avatax-transaction-push-handler.php', 'WC_AvaTax_Transaction_Push_Handler' );
		}

		return $this->transaction_push_handler;
	}

	/**
	 * Runs the transaction push process.
	 *
	 * This is the callback for the WordPress cron event.
	 * Lazy-loads the handler only when the cron event actually fires.
	 *
	 * @since 3.6.4
	 */
	public function run_transaction_push() {
		$this->get_transaction_push_handler()->handle_transaction_push();
	}

	/**
	 * Determines whether the account number and license key are set.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public function has_api_credentials_set() : bool {

		return ! empty ( get_option( 'wc_avatax_api_account_number' ) ) && ! empty ( get_option( 'wc_avatax_api_license_key' ) );
	}


    /**
     * Determines whether the HS API login and password are set.
     *
     * @since 1.13.0
     *
     * @return bool
     */
	public function has_hs_api_credentials_set() : bool {

		return ! empty ( get_option( 'wc_avatax_hs_api_username' ) ) && ! empty ( get_option( 'wc_avatax_hs_api_password' ) );
	}

	/**
     * Determines whether the elr API login and password are set.
     *
     * @since 3.0.0
     *
     * @return bool
     */
	public function has_elr_api_credentials_set() : bool {

		return ! empty ( get_option( 'wc_avatax_elr_client_id' ) ) && ! empty ( get_option( 'wc_avatax_elr_client_secret' ) );
	}

	/**
	 * Loads all the API classes.
	 *
	 * @since 1.13.0
	 */
	private function load_api_classes() {

		// loads the abstract API class
		require_once( $this->get_plugin_path() . '/src/api/WC_AvaTax_Abstract_API.php' );

		// loads the main API classes
		require_once( $this->get_plugin_path() . '/src/api/class-wc-avatax-api.php' );
		require_once( $this->get_plugin_path() . '/src/api/class-wc-avatax-api-tax-rate.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-utility-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-subscriptions-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-entity-use-code-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-rate-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-company-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-tax-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-void-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-address-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-transport-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-tax-code-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-company-tax-code-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/Companies_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-company-filter-company-code-request.php');
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-company-filter-default-request.php');
		require_once( $this->get_plugin_path() . '/src/api/requests/Nexus_List_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/Product_Classification_Systems_List_By_Company_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/Query_Items_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/Transactions_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-get-certificates-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-get-customer-details-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-invite-customers-to-add-certificate-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-get-ecommerce-token-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-get-certificates-exposure-zones-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-add-customer-to-avatax-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-unlink-certificate-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-update-customer-to-avatax-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-company-location-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-utility-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-subscriptions-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-entity-use-code-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-rate-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-tax-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-address-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-transport-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/Companies_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/Nexus_List_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/Product_Classification_Systems_List_By_Company_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/Query_Items_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/Transactions_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-tax-code-response-base.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-tax-code-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-company-tax-code-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-get-certificates-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-get-customer-details-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-invite-customer-to-add-certificate-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-get-ecommerce-token-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-get-exposure-zones-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-add-customer-to-avatax-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-unlink-certificate-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-update-customer-to-avatax-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-company-location-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-product-classification-systems-response.php' );

		// loads the HS Classification API classes
		require_once( $this->get_plugin_path() . '/src/api/WC_AvaTax_HS_API.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/Abstract_HS_Classification_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/HS_Classification_Create_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/HS_Classification_Update_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/HS_Classification_Get_Request.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/Abstract_HS_Classification_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/HS_Classification_Create_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/HS_Classification_Update_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/HS_Classification_Get_Response.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-product-classification-systems-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-item-sync-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-item-sync-response.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-get-items-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-get-items-response.php' );
		// loads the integration API classes
		require_once( $this->get_plugin_path() . '/src/api/class-wc-avatax-integration-api.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-put-settings-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-get-settings-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-post-tenant-app-config.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-delete-setting-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-delete-tenant-app-config.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-css-heartbeat-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-get-settings-response.php' );

		//Load the logger API classes
		require_once( $this->get_plugin_path() . '/src/api/class-wc-avatax-logger-api.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-logger-api-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-logger-api-response.php' );

		// loads the onboarding API classes
		require_once( $this->get_plugin_path() . '/src/api/class-wc-avatax-onboarding-api.php' );
		// Load the transaction push API classes (uses Onboarding API)
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-onboarding-transaction-push-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-onboarding-transaction-push-response.php' );

	}

	/**
	 * Loads all the NDI API classes.
	 *
	 * @since 3.6.0
	 */
	private function load_ndi_api_classes() {
		// NDI API classes are loaded as part of ELR API classes
		// since NDI extends ELR API
		$this->load_elr_api_classes();
	}

	/**
	 * Loads all the API classes.
	 *
	 * @since 1.13.0
	 */
	private function load_elr_api_classes() {

		// First load the base API classes
		$this->load_api_classes();

		//loads the ELR classes
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/class-wc-avatax-elr-api.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/class-wc-avatax-elr.php' );

		//loads the ELR request classes
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class-wc-avatax-elr-api-request.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class-wc-avatax-elr-api-submit-invoice-request.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class-wc-avatax-elr-api-invoice-status-request.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class-wc-avatax-elr-api-get-companies-request.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class_wc_avatax_elr_api_condition_payload_request.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class-wc-avatax-elr-api-download-invoice-request.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class-wc-avatax-elr-api-inbound-mapper-download-request.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/requests/class-wc-avatax-elr-api-get-documents-request.php' );

		//loads the ELR response classes
		require_once( $this->get_plugin_path() . '/src/api/responses/class-wc-avatax-api-response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class-wc-avatax-elr-api-response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class-wc-avatax-elr-api-submit-invoice-response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class-wc-avatax-elr-api-invoice-status-response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class-wc-avatax-elr-api-get-companies-response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class_wc_avatax_elr_api_condition_payload_response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class-wc-avatax-elr-api-download-invoice-response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class-wc-avatax-elr-api-inbound-mapper-download-response.php' );
		require_once( $this->get_plugin_path() . '/src/e-invoicing/api/responses/class-wc-avatax-elr-api-get-documents-response.php' );

		//loads the post payload schema request classes
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-post-payload-schema-request.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-post-elr-app-config.php' );
		require_once( $this->get_plugin_path() . '/src/api/requests/class-wc-avatax-api-delete-elr-app-config.php' );

		//loads the NDI API classes (after ELR classes to ensure proper loading order)
		require_once( $this->get_plugin_path() . '/src/ndi/api/class-wc-avatax-ndi-api.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/requests/class-wc-avatax-ndi-api-request.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/requests/class-wc-avatax-ndi-api-search-directory-request.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/requests/class-wc-avatax-ndi-api-batch-search-request.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/requests/class-wc-avatax-ndi-api-list-batch-search-request.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/responses/class-wc-avatax-ndi-api-response.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/responses/class-wc-avatax-ndi-api-search-directory-response.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/responses/class-wc-avatax-ndi-api-batch-search-response.php' );
		require_once( $this->get_plugin_path() . '/src/ndi/api/responses/class-wc-avatax-ndi-api-list-batch-search-response.php' );
	}

	/**
     * Returns the One True Instance of WC_AvaTax.
     *
     * @since 2.7.0
     *
     * @return WC_AvaTax_Utilities
     */
    function wc_avatax_utilities() {
        if ( is_object( $this->wc_avatax_utilites ) ) 
        {
			return $this->wc_avatax_utilites;
		}
        else
        {
            return $this->wc_avatax_utilites =  $this->load_class( '/src/utilities/class-wc-avatax-utilities.php', 'WC_AvaTax_Utilities' );
        }
    }

	/**
     * Returns the One True Instance of WC_AvaTax.
     *
     * @since 3.0.0
     *
     * @return wc_avatax_elr_utilities
     */
    function wc_avatax_elr_utilities() {
        if ( is_object( $this->wc_avatax_elr_utilities ) ) 
        {
			return $this->wc_avatax_elr_utilities;
		}
        else
        {
            return $this->wc_avatax_elr_utilities =  $this->load_class( '/src/utilities/class-wc-avatax-elr-utilities.php', 'WC_AvaTax_Elr_Utilities' );
        }
    }

	/**
	 * Creates a cron job that will run after every 15 minutes to check for AvaTax configuration settings.
	 *
	 * @since 2.8.0
	 *
	 */
	public function set_confic_sync_cron(){
		// Add a new interval of 180 seconds
		// See http://codex.wordpress.org/Plugin_API/Filter_Reference/cron_schedules
		add_filter( 'cron_schedules', 'isa_add_every_fifteen_minutes' );
		function isa_add_every_fifteen_minutes( $schedules ) {
			$schedules['every_fifteen_minutes'] = array(
					'interval'  => 900,
					'display'   => __( 'Every 15 Minutes', 'textdomain' )
			);
			return $schedules;
		}

		// Schedule an action if it's not already scheduled
		if ( ! wp_next_scheduled( 'isa_add_every_fifteen_minutes' ) ) {
			wp_schedule_event( time(), 'every_fifteen_minutes', 'isa_add_every_fifteen_minutes' );
		}

		// Hook into that action that'll fire every fifteen minutes
		add_action( 'isa_add_every_fifteen_minutes', 'every_fifteen_minutes_event_func' );
		function every_fifteen_minutes_event_func() {
			wc_avatax()->log("called");
			wc_avatax()->wc_avatax_utilities()->sync_confic_settings();
		}
	}

	/**
	 * Creates a unique id for current website.
	 *
	 * @since 2.8.0
	 *
	 */
	public function create_website_id(){
		if(!get_option('wc_avatax_website_id')){
			update_option('wc_avatax_website_id', wp_generate_uuid4());
		}
	}

	/**
	 * Get Connector ID
	 *
	 * @since 2.9.0
	 *
	 */
	public function get_elr_connector_id(){
		return self::ELR_CONNECTOR_ID;
	}

	/**
	 * Displays an admin notice when a plugin update is available.
	 *
	 * Uses WordPress's cached update transient instead of forcing a refresh,
	 * which is more efficient and doesn't slow down page loads.
	 *
	 * @since 3.6.4
	 */
	protected function avatax_update_notice() {

		// Plugin basename for update check
		$plugin_slug = 'woocommerce-avatax/woocommerce-avatax.php';

		// Get cached plugin update data (WordPress refreshes this automatically every 12 hours)
		$updates = get_site_transient( 'update_plugins' );

		// No update available
		if ( empty( $updates->response[ $plugin_slug ] ) ) {
			return;
		}

		$new_version = $updates->response[ $plugin_slug ]->new_version;

		$this->get_admin_notice_handler()->add_admin_notice( sprintf(
			/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - current version, %4$s - new version, %5$s - <a> tag, %6$s - </a> tag */
			__( 'There is a new version of %1$sAvalara AvaTax%2$s available. %3$sUpdate Now%4$s', 'woocommerce-avatax' ),
			'<strong>',
			'</strong>',
			'<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">',
			'</a>'
		), 'wc-avatax-update-notice', array(
			'always_show_on_settings' => false,
			'dismissible'             => true,
			'notice_class'            => 'update-message notice-warning',
		) );
	}
}


/**
 * Returns the One True Instance of WC_AvaTax.
 *
 * @since 1.0.0
 *
 * @return WC_AvaTax
 */
function wc_avatax() {

	return WC_AvaTax::instance();
}
