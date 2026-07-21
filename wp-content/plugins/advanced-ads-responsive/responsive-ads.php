<?php
/**
 * Advanced Ads – AMP Ads
 *
 * @package   AdvancedAds\AMP
 * @author    Advanced Ads <support@wpadvancedads.com>
 * @license   GPL-2.0+
 * @link      https://wpadvancedads.com
 * @copyright Advanced Ads
 *
 * Plugin Name:       Advanced Ads – AMP Ads
 * Version:           2.0.5
 * Description:       Ready your ads for AMP power!
 * Plugin URI:        https://wpadvancedads.com/add-ons/responsive-ads/
 * Author:            Advanced Ads
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-responsive
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

if ( defined( 'AAR_FILE' ) ) {
	return;
}

define( 'AAR_FILE', __FILE__ );
define( 'AAR_VERSION', '2.0.5' );

/**
 * Autoloader
 */
require_once __DIR__ . '/includes/class-autoloader.php';
\AdvancedAds\AMP\Autoloader::get()->initialize();

if ( ! function_exists( 'wp_advads_amp' ) ) {
	/**
	 * Returns the main instance of the plugin.
	 *
	 * @return \AdvancedAds\AMP\Plugin
	 */
	function wp_advads_amp() {
		return \AdvancedAds\AMP\Plugin::get();
	}
}

\AdvancedAds\AMP\Bootstrap::get()->start();

