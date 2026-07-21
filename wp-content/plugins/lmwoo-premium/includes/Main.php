<?php
/**
 * Main plugin file.
 * PHP Version: 5.6
 *
 * @package  LicenseManagerForWooCommerce
 * @link     https://www.licensemanager.at/
 */

namespace LicenseManagerForWooCommerce;

use LicenseManagerForWooCommerce\Abstracts\Singleton;
use LicenseManagerForWooCommerce\Integrations\WooCommerce\Controller;
use LicenseManagerForWooCommerce\Controllers\ApiKey as ApiKeyController;
use LicenseManagerForWooCommerce\Controllers\Generator as GeneratorController;
use LicenseManagerForWooCommerce\Controllers\License as LicenseController;
use LicenseManagerForWooCommerce\Controllers\Dropdowns as DropdownsController;
use LicenseManagerForWooCommerce\Controllers\Application as ApplicationController;
use LicenseManagerForWooCommerce\Reports\AdminReports;

use LicenseManagerForWooCommerce\Enums\LicenseStatus;

defined('ABSPATH') || exit;

/**
 * LicenseManagerForWooCommerce
 *
 * @package  LicenseManagerForWooCommerce
 * @version  Release: <2.2.0>
 * @link     https://www.licensemanager.at/
 */
final class Main extends Singleton {

	/**
	 * Main constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_defineConstants();
		$this->_initHooks();

		add_action('init', array( $this, 'init' ));
	   
		new Api\Authentication();
	}

	/**
	 * Define plugin constants.
	 *
	 * @return void
	 */
	private function _defineConstants() {
		if (!defined('ABSPATH_LENGTH')) {
			define('ABSPATH_LENGTH', strlen(ABSPATH));
		}

		define('LMFWC_ABSPATH', dirname(LMFWC_PLUGIN_FILE) . '/');
		define('LMFWC_PLUGIN_BASENAME', plugin_basename(LMFWC_PLUGIN_FILE));

		// Directories
		define('LMFWC_ASSETS_DIR', LMFWC_ABSPATH . 'assets/');
		define('LMFWC_LOG_DIR', LMFWC_ABSPATH . 'logs/');
		define('LMFWC_TEMPLATES_DIR', LMFWC_ABSPATH . 'templates/');
		define('LMFWC_MIGRATIONS_DIR', LMFWC_ABSPATH . 'vender/migrations/');
		define('LMFWC_CSS_DIR', LMFWC_ASSETS_DIR . 'css/');

		// URL's
		define('LMFWC_ASSETS_URL', LMFWC_PLUGIN_URL . 'assets/');
		define('LMFWC_ETC_URL', LMFWC_ASSETS_URL . 'etc/');
		define('LMFWC_CSS_URL', LMFWC_ASSETS_URL . 'css/');
		define('LMFWC_JS_URL', LMFWC_ASSETS_URL . 'js/');
		define('LMFWC_IMG_URL', LMFWC_ASSETS_URL . 'img/');
	}
	/**
	 * Include JS and CSS files.
	 *
	 * @param string $hook
	 *
	 * @return void
	 */
	public function adminEnqueueScripts( $hook ) {
		// Select2
		wp_register_style(
			'lmfwc_select2_cdn',
			'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css'
		);
		wp_register_script(
			'lmfwc_select2_cdn',
			'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js'
		);
		wp_register_style(
			'lmfwc_select2',
			LMFWC_CSS_URL . 'select2.css'
		);

		// CSS
		wp_enqueue_style(
			'lmfwc_admin_css',
			LMFWC_CSS_URL . 'main.css',
			array(),
			LMFWC_VERSION
		);

		// JavaScript
		wp_enqueue_script(
			'lmfwc_admin_js',
			LMFWC_JS_URL . 'script.js',
			array(),
			LMFWC_VERSION
		);
		$inc = require LMFWC_ABSPATH. 'build/index.asset.php';
		// JavaScript
		wp_enqueue_script(
			'lmfwc_analytics_js',
			LMFWC_PLUGIN_URL . '/build/index.js',
			$inc['dependencies'],
			LMFWC_VERSION,
			true
		);
		// jQuery UI
		wp_register_style(
			'lmfwc-jquery-ui-datepicker',
			'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
			array(),
			'1.12.1'
		);
		$data = $_REQUEST;
		if ( 'woocommerce_page_wc-settings' === $hook   && isset( $data['tab'] ) && 'lmfwc_settings' === $data['tab'] ) {
			$extra_css = 'p.submit:not(.wrap.lmfwc p.submit){display:none;}';
			wp_add_inline_style('lmfwc_admin_css', $extra_css);
		}
		if ( 'product_page_lmfwc_licenses'   === $hook ||'product_page_lmfwc_generators'  ===  $hook || 'product_page_lmfwc_activations' === $hook  || ( 'woocommerce_page_wc-settings' === $hook   && isset( $data['tab'] ) && 'lmfwc_settings' === $data['tab'] ) ) {
			wp_enqueue_script('lmfwc_select2_cdn');
			wp_enqueue_style('lmfwc_select2_cdn');
			wp_enqueue_style('lmfwc_select2');
			 wp_enqueue_script('select2');
		}

		// Licenses page
		if ( 'product_page_lmfwc_licenses' === $hook ) {
			wp_enqueue_script('lmfwc_licenses_page_js', LMFWC_JS_URL . 'licenses_page.js');

			wp_localize_script(
				'lmfwc_licenses_page_js',
				'i18n',
				array(
					'placeholderSearchOrders'    => __('Search by order ID or customer email', 'license-manager-for-woocommerce'),
					'placeholderSearchProducts'  => __('Search by product ID or product name', 'license-manager-for-woocommerce'),
					'placeholderSearchUsers'     => __('Search by user login, name or email', 'license-manager-for-woocommerce'),
				)
			);

			wp_localize_script(
				'lmfwc_licenses_page_js',
				'security',
				array(
					'dropdownSearch' => wp_create_nonce('lmfwc_dropdown_search'),
				)
			);
		}

		// Generators page
		if ( 'product_page_lmfwc_generators' === $hook ) {
			wp_enqueue_script('lmfwc_generators_page_js', LMFWC_JS_URL . 'generators_page.js');

			wp_localize_script(
				'lmfwc_generators_page_js',
				'i18n',
				array(
					'placeholderSearchOrders'   => __('Search by order ID or customer email', 'license-manager-for-woocommerce'),
					'placeholderSearchProducts' => __('Search by product ID or product name', 'license-manager-for-woocommerce'),
				)
			);

			wp_localize_script(
				'lmfwc_generators_page_js',
				'security',
				array(
					'dropdownSearch' => wp_create_nonce('lmfwc_dropdown_search'),
				)
			);
		}


		// Activations page
		if ( 'product_page_lmfwc_activations' === $hook  ) {
			wp_enqueue_script('lmfwc_activations_page_js', LMFWC_JS_URL . 'activations_page.js');

			wp_localize_script(
				'lmfwc_activations_page_js',
				'i18n',
				array(
					'placeholderSearchLicenses' => __( 'Search by license ID', 'license-manager-for-woocommerce' ),
					'placeholderSearchSources'  => __( 'Search by source', 'license-manager-for-woocommerce' ),
				)
			);

			wp_localize_script(
				'lmfwc_activations_page_js',
				'security',
				array(
					'dropdownSearch' => wp_create_nonce('lmfwc_dropdown_search'),
				)
			);
		}

		
		// Edit Post
		if ( 'post.php' === $hook ) {
			// WooCommerce Product Page
			if ( isset( $data['action'] ) && 'edit' === $data['action'] && isset( $data['post'] ) && wc_get_product( $data['post'] ) ) {
				wp_enqueue_script( 'lmfwc_edit_product', LMFWC_JS_URL . 'edit_product.js', array( 'jquery' ), '1.0.0' );
			}
		}

		// Settings page
		if ( 'woocommerce_page_wc-settings' === $hook && isset( $data['tab'] ) && 'lmfwc_settings' === $data['tab'] ) {
			wp_enqueue_media();
			wp_enqueue_script('lmfwc_select2_cdn');
			wp_enqueue_style('lmfwc_select2_cdn');
			wp_enqueue_script('select2');
			wp_enqueue_script('lmfwc_settings_page_js', LMFWC_JS_URL . 'settings_page.js');

		}

		wp_localize_script(
				'lmfwc_settings_page_js',
				'security',
				array(
					'dropdownSearch' => wp_create_nonce('lmfwc_dropdown_search'),
					'ajaxurl' => admin_url('admin-ajax.php'),
				)
			);

		// Script localization
		wp_localize_script(
			'lmfwc_admin_js',
			'license',
			array(
				'show'     => wp_create_nonce('lmfwc_show_license_key'),
				'show_all' => wp_create_nonce('lmfwc_show_all_license_keys'),
			)
		);
	}

	public function frontEnqueueScripts() {
		$extra_js = "jQuery( window ).on('load',function(){jQuery('#tab-title-screenshots a').click(function() {setTimeout(function() {jQuery(window).trigger('resize'); }, 0); setTimeout(function() {jQuery(window).trigger('resize'); }, 2) }); var interval = 4000; var autoslide = true; interval = jQuery(this).data('interval'); if(interval == 0){autoslide = false; } else {autoslide = true; } jQuery('.woocommerce-product-gallery').flexslider({selector: '.woocommerce-product-gallery__wrapper > .woocommerce-product-gallery__image', animation: 'slide', prevText: ', nextText: ', pauseOnHover: true, animationLoop: true, animationSpeed: 300, slideshowSpeed: interval, directionNav : true, itemWidth:543, slideshow: autoslide, controlNav: false, start: function(){jQuery(window).resize(); }, }); jQuery(window).resize();}); ";
		if ( is_product() ) {
			wp_add_inline_script('woocommerce', $extra_js );
		}	
		
	}

	/**
	 * Add additional links to the plugin row meta.
	 *
	 * @param array  $links Array of already present links
	 * @param string $file  File name
	 *
	 * @return array
	 */
	public function pluginRowMeta( $links, $file ) {
		if (strpos($file, 'license-manager-woocommerce.php') !== false ) {
			$newLinks = array(
				// 'github' => sprintf(
				//  '<a href="%s" target="_blank">%s</a>',
				//  'https://github.com/wpexpertsio/license-manager-woocommerce',
				//  'GitHub'
				// ),
				'docs' => sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://woo.com/document/license-manager-woo-store-owner-guide/',
					__('Documentation', 'license-manager-for-woocommerce')
				),
				// 'donate' => sprintf(
				//  '<a href="%s" target="_blank">%s</a>',
				//  'https://www.licensemanager.at/donate/',
				//  __('Donate', 'license-manager-for-woocommerce')
				// ),
			);
			unset( $links[2] );
			$links = array_merge($links, $newLinks);
		}

		return $links;
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @return void
	 */
	private function _initHooks() {
		register_activation_hook(
			LMFWC_PLUGIN_FILE,
			array( '\LicenseManagerForWooCommerce\Setup', 'install' )
		);
		register_deactivation_hook(
			LMFWC_PLUGIN_FILE,
			array( '\LicenseManagerForWooCommerce\Setup', 'deactivate' )
		);
		register_uninstall_hook(
			LMFWC_PLUGIN_FILE,
			array( '\LicenseManagerForWooCommerce\Setup', 'uninstall' )
		);

		add_action('admin_enqueue_scripts', array( $this, 'adminEnqueueScripts' ));
		add_action('wp_enqueue_scripts', array( $this, 'frontEnqueueScripts' ), 99);
		add_filter('plugin_row_meta', array( $this, 'pluginRowMeta' ), 10, 2);
		add_action('wp_error_added', array($this, 'apiLogErrors'), 10, 4);
		add_filter('lmfwc_rest_api_pre_response', array($this, 'apiLogOutput'), 20, 3);
		add_filter('lmfwc_rest_check_permissions', array($this, 'apiLogActivity'), 10);
	}
	
	public function apiLogActivity($permission) {
		if ( Settings::get('lmfwc_api_output_log') ) {
			$server = $_SERVER;
			$method = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD']: '';
			$route = isset($server['REQUEST_URI']) ? $server['REQUEST_URI']: '';
			$remote_addr = isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR']: '';
			$logger = wc_get_logger();
			// LOG THE FAILED API REQUEST TO "lmfwc-api-exceptions" LOG
			$logger->info( sprintf( esc_html__( 'API activity on route "%s" using "%s" method having IP "%s"', 'license-manager-for-woocommerce' ), $route, $method, $remote_addr ), array( 'source' => 'lmfwc-api-activity' ) );
		}
		return $permission;
	}

	public function apiLogErrors($code, $message, $data, $class) {
		$lmfwc_codes = array('lmfwc_rest_data_error', 'lmfwc_rest_license_expired', 'lmfwc_rest_cannot_view', 'lmfwc_rest_route_disabled_error', 'lmfwc_rest_no_ssl_error', 'lmfwc_rest_authentication_error', 'lmfwc_rest_cannot_create', 'lmfwc_rest_cannot_edit', 'lmfwc_rest_cannot_download', 'lmfwc_rest_cannot_delete');
		if ( in_array($code, $lmfwc_codes) && Settings::get('lmfwc_api_exception_log') ) {
			$error_array = array(
				'code' => $code,
				'message' => $message,
				'data' => $data
			);
			$logger = wc_get_logger();
			// LOG THE FAILED API REQUEST TO "lmfwc-api-exceptions" LOG
			$logger->error( sprintf( esc_html__( 'Error in API request. Error message is "%s". Complete error details are below: %s', 'license-manager-for-woocommerce' ), $message, PHP_EOL . wc_print_r( $error_array, true ) ), array( 'source' => 'lmfwc-api-exceptions' ) );
		}
	}

	public function apiLogOutput($data, $method, $route) {
		
		if ( Settings::get('lmfwc_api_output_log') ) {
			
			$logger = wc_get_logger();
			// LOG THE FAILED API REQUEST TO "lmfwc-api-exceptions" LOG
			$logger->debug( sprintf( esc_html__( 'API output. The output for route "%s" is below: %s', 'license-manager-for-woocommerce' ), $route, PHP_EOL . wc_print_r( $data, true ) ), array( 'source' => 'lmfwc-api-output' ) );
		}
		return $data;
	}

	/**
	 * Init LicenseManagerForWooCommerce when WordPress Initialises.
	 *
	 * @return void
	 */
	public function init() {
		// flush_rewrite_rules();
		Setup::migrate();

		$this->publicHooks();

		new Crypto();
		new Import();
		new Export();
		new AdminMenus();
		new AdminNotice();
		new Generator();
		new LocalAdapter();
		new ApplicationManager();
		new Repositories\PostMeta();
		new Repositories\Users();
		new LicenseController();
		new GeneratorController();
		new DropdownsController();
		new ApiKeyController();
		new ApplicationController();
		new Api\Setup();
		new Reports\AdminReports();

		if ($this->isPluginActive('woocommerce/woocommerce.php')) {
			new Integrations\WooCommerce\Controller();
		}

		if ( $this->isPluginActive( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			new Integrations\WooCommerceSubscriptions\Controller();
		}

		if (Settings::get('lmfwc_allow_duplicates')) {
			add_filter('lmfwc_duplicate', '__return_false', PHP_INT_MAX);
		}
	}

	/**
	 * Defines all public hooks
	 *
	 * @return void
	 */
	protected function publicHooks() {
		add_filter(
			'lmfwc_license_keys_table_heading',
			function ( $text ) {
				$default = __('Your license key(s)', 'license-manager-for-woocommerce');

				if (!$text) {
					return $default;
				}

				return sanitize_text_field($text);
			},
			10,
			1
		);

		add_filter(
			'lmfwc_license_keys_table_valid_until',
			function ( $text ) {
				$default = __('Valid until', 'license-manager-for-woocommerce');

				if (!$text) {
					return $default;
				}

				return sanitize_text_field($text);
			},
			10,
			1
		);
	}

	/**
	 * Checks if a plugin is active.
	 *
	 * @param string $pluginName
	 *
	 * @return bool
	 */
	private function isPluginActive( $pluginName ) {
		/**
		* Filter active_plugins
		* 
		* @since 1.0
		**/
		return in_array($pluginName, apply_filters('active_plugins', get_option('active_plugins')));
	}
}
