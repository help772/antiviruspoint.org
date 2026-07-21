<?php
/**
 * Plugin Name: License Manager for WooCommerce (Premium)
 * Plugin URI: https://www.licensemanager.at/
 * Description: Easily sell and manage software license keys through your WooCommerce shop.
 * Version: 1.3.1
 * Update URI: https://api.freemius.com
 * Author: WPExperts
 * Requires Plugins: woocommerce
 * Author URI: https://www.licensemanager.at/
 * Requires at least: 4.7
 * Tested up to: 6.8.1
 * Requires PHP: 5.6
 * WC requires at least: 2.7
 * WC tested up to: 9.8.1
 * Text Domain: license-manager-for-woocommerce
 */
namespace LicenseManagerForWooCommerce;

defined('ABSPATH') || exit;
require_once __DIR__ . '/freemius_integration.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions/lmfwc-core-functions.php';
require_once __DIR__ . '/functions/lmfwc-generator-functions.php';
require_once __DIR__ . '/functions/lmfwc-license-functions.php';
require_once __DIR__ . '/functions/lmfwc-meta-functions.php';
require_once __DIR__ . '/functions/lmfwc-stock-functions.php';

// Define LMFWC_PLUGIN_FILE.
if (!defined('LMFWC_PLUGIN_FILE')) {
	define('LMFWC_PLUGIN_FILE', __FILE__);
	define('LMFWC_PLUGIN_DIR', __DIR__);
}

// Define LMFWC_PLUGIN_URL.
if (!defined('LMFWC_PLUGIN_URL')) {
	define('LMFWC_PLUGIN_URL', plugins_url('', __FILE__) . '/');
}

// Define LMFWC_VERSION.
if (!defined('LMFWC_VERSION')) {
	define('LMFWC_VERSION', '1.3.0');
}
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
	add_action('plugins_loaded', function () {
		load_plugin_textdomain('license-manager-for-woocommerce', false, basename(dirname(__FILE__)) . '/languages/');
	} );
} );
/**
 * Main instance of LicenseManagerForWooCommerce.
 *
 * Returns the main instance of SN to prevent the need to use globals.
 *
 * @return Main
 */
function lmfwc() {
	return Main::instance();
}

// Global for backwards compatibility.
$GLOBALS['license-manager-for-woocommerce'] = lmfwc();
