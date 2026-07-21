<?php

namespace WcPaysafe;

use WcPaysafe\Admin\Admin;
use WcPaysafe\Ajax\Ajax_Loader;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Tokens\Manage_Tokens;
use WcPaysafe\Tokens\Update_Payment_Method;

class Paysafe {
	
	/**
	 * The plugin version
	 */
	const VERSION = WC_PAYSAFE_PLUGIN_VERSION;
	/**
	 * The files and folders version
	 * Should be changes every time there is a new class file added or one deleted
	 * @since 3.2.0
	 */
	const FILES_VERSION = WC_PAYSAFE_PLUGIN_FILES_VERSION;
	/**
	 * Minimal PHP version supported
	 * @since 3.2.0
	 */
	const MIN_PHP_VERSION = '5.3.0';
	/**
	 * Minimal WC version supported
	 * @since 3.2.0
	 */
	const MIN_WC_VERSION = '3.0.0';
	/**
	 * Plugin URL
	 * @var string
	 */
	private static $plugin_url;
	/**
	 * Plugin Path
	 * @var string
	 */
	private static $plugin_path;
	/**
	 * Is WC Subscriptions active
	 * @since 2.0
	 * @var bool
	 */
	private static $is_subscriptions_active;
	/**
	 * WC Subscriptions version
	 * @since 2.0
	 * @var bool
	 */
	private static $is_subscriptions_version;
	/**
	 * Is WC Pre-Orders active
	 * @since 2.0
	 * @var bool
	 */
	private static $is_pre_orders_active;
	/**
	 * @var Scripts
	 */
	public $scripts;
	/**
	 * @var \WcPaysafe\Admin\Admin
	 */
	public $admin;
	/**
	 * @var \WcPaysafe\Ajax\Ajax_Loader
	 */
	public $ajax;
	/**
	 * The single instance of the class.
	 *
	 * @var \WcPaysafe\Paysafe
	 * @since 3.3.0
	 */
	protected static $_instance = null;
	
	/**
	 * Returns main instance of the class
	 * @since 3.3.0
	 * @return \WcPaysafe\Paysafe
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * @since 3.3.0
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Not allowed!', 'wc_paysafe' ), '3.3.0' );
	}
	
	/**
	 * @since 3.3.0
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Not allowed!', 'wc_paysafe' ), '3.3.0' );
	}
	
	protected function __construct() {
		$this->update_procedure();
		$this->load_gateway_files();
	}
	
	public function hooks() {
		// Add a 'Settings' link to the plugin action links
		add_filter( 'plugin_action_links_' . plugin_basename( WC_PAYSAFE_PLUGIN_FILE ),
			array(
				$this,
				'settings_support_link',
			),
			10,
			4
		);
		
		add_action( 'before_woocommerce_init', [ $this, 'is_hpos_compatible' ] );
		
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_payment_gateway' ) );
		
		// Load the query earlier since we are relying on adding an endpoint and the endpoint load in on priority 10
		add_action( 'init', array( $this, 'load_wc_query' ), 9 );
		add_action( 'init', array( $this, 'load_plugin_essentials' ) );
		
		add_action( 'template_redirect', array( $this, 'return_url_response_check' ) );
	}
	
	/**
	 * Runs the update procedure
	 *
	 * @since 3.3.0
	 */
	public function update_procedure() {
		$update = new Update_Procedures();
		$update->hooks();
	}
	
	/**
	 * @since 3.3.0
	 */
	public function load_plugin_essentials() {
		// No load, if WC is not loaded
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}
		
		$this->load_text_domain();
		
		include_once Paysafe::plugin_path() . '/includes/tokens/class-wc-payment-token-paysafe-cc.php';
		include_once Paysafe::plugin_path() . '/includes/tokens/class-wc-payment-token-paysafe-dd.php';
		include_once Paysafe::plugin_path() . '/includes/tokens/class-wc-payment-token-paysafe-payments-card.php';
		
		// Hosted url check for its iframe functions
		$this->cancel_url_response_check();
		
		$this->load_scripts();
		$this->load_ajax();
		$this->load_token_management();
		$this->load_compatibility();
		
		// Admin only
		if ( is_admin() ) {
			$this->load_admin();
		}
	}
	
	/**
	 * @since 3.3.0
	 */
	public function load_scripts() {
		$this->scripts = new Scripts();
		$this->scripts->hooks();
	}
	
	/**
	 * @since 3.3.0
	 */
	public function load_ajax() {
		$this->ajax = new Ajax_Loader();
		$this->ajax->register();
	}
	
	/**
	 * @since 3.3.0
	 */
	public function load_token_management() {
		// Bail, if for some reason we are running WC < 2.6
		if ( ! WC_Compatibility::is_wc_2_6() ) {
			return;
		}
		
		$manage = new Manage_Tokens( $this );
		$manage->hooks();
		
		$payment_method = new Update_Payment_Method();
		$payment_method->hooks();
	}
	
	/**
	 * Loads the query class to add endpoints
	 * @since 3.3.0
	 */
	public function load_wc_query() {
		$query = new WC_Query();
		$query->hooks();
	}
	
	/**
	 * Loads compatibility classes
	 * @since 3.5.2
	 */
	public function load_compatibility() {
		if ( class_exists( 'SitePress' ) && class_exists( 'WCML_WC_Gateways' ) ) {
			$wpml_compat = new Compatibility\WPML\Translations();
			$wpml_compat->hooks();
		}
	}
	
	/**
	 * @since 3.3.0
	 */
	public function load_admin() {
		$this->admin = new Admin();
	}
	
	/**
	 * Localisation
	 **/
	public function load_text_domain() {
		load_plugin_textdomain( 'wc_paysafe', false, dirname( plugin_basename( WC_PAYSAFE_PLUGIN_FILE ) ) . '/languages/' );
	}
	
	/**
	 * Add 'Settings' link to the plugin actions links
	 *
	 * @return array associative array of plugin action links
	 */
	public function settings_support_link( $actions, $plugin_file, $plugin_data, $context ) {
		
		$gateway = $this->get_gateway_class();
		if ( Compatibility\WC_Compatibility::is_wc_2_6() ) {
			$gateway = 'netbanx';
		}
		
		return array_merge(
			array( 'settings' => '<a href="' . Compatibility\WC_Compatibility::gateway_settings_page( $gateway ) . '">' . __( 'Settings', 'wc_paysafe' ) . '</a>' ),
			$actions
		);
	}
	
	/**
	 * Add the gateway to WooCommerce
	 *
	 * @param array $methods
	 *
	 *
	 * @return array
	 */
	public function add_payment_gateway( $methods ) {
		$methods[] = $this->get_gateway_class( 'hosted' );
		$methods[] = $this->get_gateway_class( 'checkout_payments' );
		
		$methods = array_filter( $methods );
		
		return $methods;
	}
	
	/**
	 * Get the correct gateway class name to load
	 *
	 * @param string $type hosted|direct
	 *
	 * @return string Class name
	 */
	public function get_gateway_class( $type = 'hosted' ) {
		if ( 'hosted' == $type ) {
			return $this->get_hosted_gateway_class();
		}
		
		if ( 'checkout_payments' == $type ) {
			return $this->get_checkout_payments_class();
		}
		
		return '';
	}
	
	/**
	 * @since 3.3.0
	 * @return string Returns the class of the hosted gateway
	 */
	public function get_hosted_gateway_class() {
		if ( self::is_subscriptions_active() || self::is_pre_orders_active() ) {
			$methods = '\\WcPaysafe\\Gateways\\Redirect\\Gateway_Addons';
		} else {
			$methods = '\\WcPaysafe\\Gateways\\Redirect\\Gateway';
		}
		
		return $methods;
	}
	
	/**
	 * @since 3.3.0
	 * @return string Returns the class of the direct gateway
	 */
	public function get_checkout_payments_class() {
		if ( self::is_subscriptions_active() ) {
			$methods = '\\WcPaysafe\\Gateways\\Redirect\\Payments\\Payments_Gateway_Addons';
		} else {
			$methods = '\\WcPaysafe\\Gateways\\Redirect\\Payments\\Payments_Gateway';
		}
		
		return $methods;
	}
	
	/**
	 * Load gateway files
	 *
	 * Legacy classes that need to be included manually
	 */
	public function load_gateway_files() {
		include_once( 'functions.php' );
	}
	
	/**
	 * Safely get GET variables
	 *
	 * @since 2.0
	 *
	 * @param string $name GET variable name
	 * @param array  $array
	 * @param string $default
	 *
	 * @return string The variable value
	 */
	public static function get_field( $name, $array, $default = '' ) {
		if ( isset( $array[ $name ] ) ) {
			return $array[ $name ];
		}
		
		return $default;
	}
	
	/**
	 * Add debug log message
	 *
	 * @deprecated Will be removed in 2019
	 *
	 * @param string $message
	 * @param string $handle The handle of the log file
	 * @param string $level  Level of severity: emergency|alert|critical|error|warning|notice|info|debug
	 */
	public static function add_debug_log( $message, $handle = 'paysafe', $level = 'debug' ) {
		Debug::add_debug_log( $message, $handle, $level );
	}
	
	/**
	 * Check, if debug logging is enabled
	 *
	 * @deprecated Will be removed in 2020
	 *
	 * @since      1.1.2
	 *
	 * @return bool
	 */
	public static function is_debug_enabled() {
		return Debug::is_debug_enabled();
	}
	
	/**
	 * Get the plugin url
	 *
	 * @return string
	 */
	public static function plugin_url() {
		if ( self::$plugin_url ) {
			return self::$plugin_url;
		}
		
		return self::$plugin_url = untrailingslashit( plugins_url( '/', WC_PAYSAFE_PLUGIN_FILE ) );
	}
	
	/**
	 * Get the plugin path
	 *
	 * @return string
	 */
	public static function plugin_path() {
		if ( self::$plugin_path ) {
			return self::$plugin_path;
		}
		
		return self::$plugin_path = untrailingslashit( plugin_dir_path( WC_PAYSAFE_PLUGIN_FILE ) );
	}
	
	/**
	 * Return the order number with stripped # or n° ( french translations )
	 *
	 * @param \WC_Order $order
	 *
	 * @deprecated Use Paysafe_Order::get_order_number
	 *
	 * @return string
	 */
	public static function get_order_number( \WC_Order $order ) {
		$paysafe_order = new Paysafe_Order( $order );
		
		return $paysafe_order->get_order_number();
	}
	
	/**
	 * Redirect the Customer to the Cancel order page.
	 * Used for iframe only, to escape from the iframe and return the customer to the main window.
	 *
	 * @since 2.0
	 */
	function cancel_url_response_check() {
		if ( false != self::get_field( 'cancel_order', $_GET, false )
		     && false != self::get_field( 'order', $_GET, false )
		     && false != self::get_field( 'order_id', $_GET, false )
		     && false != self::get_field( 'paysafe-hosted-return-cancel', $_GET, false )
		) {
			$order = wc_get_order( (int) self::get_field( 'order_id', $_GET ) );
			
			$redirect_url = $order->get_cancel_order_url_raw();
			
			wc_get_template(
				'paysafe/iframe-break.php',
				array(
					'redirect_url' => $redirect_url,
				),
				'',
				Paysafe::plugin_path() . '/templates/'
			);
			exit;
		}
	}
	
	/**
	 * Redirect the thank you page.
	 * Used for iframe only, to escape from the iframe and return the customer to the main window.
	 *
	 * @since 2.0
	 */
	function return_url_response_check() {
		if ( is_order_received_page() && self::get_field( 'paysafe-hosted-return', $_GET ) ) {
			$init_gateway = new Gateways\Redirect\Gateway();
			$order        = wc_get_order( (int) self::get_field( 'paysafe-hosted-return', $_GET ) );
			
			$redirect_url = add_query_arg( 'paysafe-payment-status', self::get_field( 'transaction_status', $_GET ), $init_gateway->get_return_url( $order ) );
			
			wc_get_template(
				'paysafe/iframe-break.php',
				array(
					'redirect_url' => $redirect_url,
				),
				'',
				Paysafe::plugin_path() . '/templates/'
			);
			exit;
		}
	}
	
	/**
	 * Detect if WC Subscriptions is active
	 *
	 * @since 2.0
	 * @return bool True if active, False if not
	 */
	public static function is_subscriptions_active() {
		if ( is_bool( self::$is_subscriptions_active ) ) {
			return self::$is_subscriptions_active;
		}
		
		self::$is_subscriptions_active = false;
		
		if ( class_exists( 'WC_Subscriptions' ) || function_exists( 'wcs_order_contains_subscription' ) ) {
			self::$is_subscriptions_active = true;
		}
		
		return self::$is_subscriptions_active;
	}
	
	/**
	 * Get back the Subscriptions version.
	 *
	 * @since 2.0
	 * @return bool Main Subscriptions version number (e.i. 1, 2, 3), False, if Subscriptions is not active
	 */
	public static function get_subscriptions_version() {
		if ( null !== self::$is_subscriptions_version ) {
			return self::$is_subscriptions_version;
		}
		
		self::$is_subscriptions_version = false;
		
		if ( function_exists( 'wcs_order_contains_subscription' ) ) {
			self::$is_subscriptions_version = 2;
		} elseif ( class_exists( 'WC_Subscriptions' ) ) {
			self::$is_subscriptions_version = 1;
		}
		
		return self::$is_subscriptions_version;
	}
	
	/**
	 * Detect if Pre-Orders is active
	 *
	 * @since 2.0
	 * @return bool True if active, False if not
	 */
	public static function is_pre_orders_active() {
		if ( is_bool( self::$is_pre_orders_active ) ) {
			return self::$is_pre_orders_active;
		}
		
		self::$is_pre_orders_active = false;
		
		if ( class_exists( 'WC_Pre_Orders' ) ) {
			self::$is_pre_orders_active = true;
		}
		
		return self::$is_pre_orders_active;
	}
	
	public function is_hpos_compatible() {
		if ( apply_filters( 'wc_paysafe_should_declare_hpos_compatibility', true, WC_PAYSAFE_PLUGIN_FILE )
		     && class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_PAYSAFE_PLUGIN_FILE, true );
		}
	}
}