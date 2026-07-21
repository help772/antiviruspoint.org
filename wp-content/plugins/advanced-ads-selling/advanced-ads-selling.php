<?php
/**
 * Advanced Ads – Selling Ads
 *
 * @package   AdvancedAds
 * @author    Advanced Ads <support@wpadvancedads.com>
 * @license   GPL-2.0+
 * @link      https://wpadvancedads.com
 * @copyright since 2013 Advanced Ads
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Ads – Selling Ads
 * Version:           2.0.3
 * Description:       Let users purchase ads directly in the frontend of your site.
 * Plugin URI:        https://wpadvancedads.com/add-ons/selling-ads/
 * Author:            Advanced Ads
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-selling
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @requires
 * Requires at least: 5.7
 * Requires PHP:      7.4
 */

// Early bail!!
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( defined( 'AASA_FILE' ) ) {
	return;
}

define( 'AASA_FILE', __FILE__ );
define( 'AASA_VERSION', '2.0.3' );

// Load the autoloader.
require_once __DIR__ . '/includes/class-autoloader.php';
\AdvancedAds\SellingAds\Autoloader::get()->initialize();

if ( ! function_exists( 'wp_advads_sellingads' ) ) {
	/**
	 * Returns the main instance of the plugin.
	 *
	 * @since 1.5.0
	 *
	 * @return \AdvancedAds\SellingAds\Plugin
	 */
	function wp_advads_sellingads() {
		return \AdvancedAds\SellingAds\Plugin::get();
	}
}

\AdvancedAds\SellingAds\Bootstrap::get()->start();

