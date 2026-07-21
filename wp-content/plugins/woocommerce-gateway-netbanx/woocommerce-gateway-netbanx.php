<?php
/**
 * Plugin Name: WooCommerce Paysafe Gateway
 * Plugin URI: https://woocommerce.com/products/paysafe-payment-gateway/
 * Description: Allows you to use <a href="https://www.paysafe.com/en/">Paysafe</a> payment processor with the WooCommerce plugin.
 * Version: 4.0.3
 * Author: VanboDevelops
 * Author URI: http://www.vanbodevelops.com
 * Woo: 122157:a356067d101646331ab9daa636dc0d6e
 * WC requires at least: 3.0.0
 * WC tested up to: 9.6
 * Text Domain: wc_paysafe
 * Domain Path: /languages
 *
 *        Copyright: (c) 2012 - 2025 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'a356067d101646331ab9daa636dc0d6e', '122157' );

if ( ! is_woocommerce_active() ) {
	return;
}

if ( ! defined( 'WC_PAYSAFE_PLUGIN_FILE' ) ) {
	define( 'WC_PAYSAFE_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'WC_PAYSAFE_PLUGIN_DIRECTORY' ) ) {
	define( 'WC_PAYSAFE_PLUGIN_DIRECTORY', dirname( __FILE__ ) );
}

if ( ! defined( 'WC_PAYSAFE_PLUGIN_VERSION' ) ) {
	define( 'WC_PAYSAFE_PLUGIN_VERSION', '4.0.3' );
}

if ( ! defined( 'WC_PAYSAFE_PLUGIN_FILES_VERSION' ) ) {
	define( 'WC_PAYSAFE_PLUGIN_FILES_VERSION', '4.0.3' );
}

try {
	// Load the autoloader
	load_wc_paysafe_autoloader();
}
catch ( Exception $e ) {
	// The plugin will not load
	return;
}

/**
 * Loads the plugin autoloader
 * @throws Exception
 * @since 3.3.0
 */
function load_wc_paysafe_autoloader() {
	if ( class_exists( '\\WcPaysafe\\Autoloader' ) ) {
		return false;
	}
	
	include_once dirname( __FILE__ ) . '/includes/autoloader.php';
	
	$loader = new \WcPaysafe\Autoloader( WC_PAYSAFE_PLUGIN_DIRECTORY, WC_PAYSAFE_PLUGIN_FILES_VERSION, 'includes' );
	spl_autoload_register( array( $loader, 'load_classes' ) );
}

/**
 * Returns the plugin instance.
 * Prevents the possible case for loading the class twice
 *
 * @since  3.3.0
 * @return \WcPaysafe\Paysafe
 */
function wc_paysafe_instance() {
	return \WcPaysafe\Paysafe::instance();
}

add_action( 'plugins_loaded', 'wc_paysafe_gateway_load' );
/**
 * Loads the plugin class
 * @return void
 */
function wc_paysafe_gateway_load() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	
	$plugin = wc_paysafe_instance();
	$plugin->hooks();
}

