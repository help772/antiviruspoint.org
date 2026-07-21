<?php
/**
 * Advanced Ads – Google Ad Manager Integration
 *
 * @package   AdvancedAds
 * @author    Advanced Ads <support@wpadvancedads.com>
 * @license   GPL-2.0+
 * @link      https://wpadvancedads.com
 * @copyright since 2013 Advanced Ads
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Ads – Google Ad Manager Integration
 * Version:           3.0.2
 * Description:       Google Ad Manager Integration for WordPress
 * Plugin URI:        https://wpadvancedads.com/add-ons/google-ad-manager/
 * Author:            Advanced Ads GmbH
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-gam
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

if ( defined( 'AAGAM_FILE' ) ) {
	return;
}

define( 'AAGAM_FILE', __FILE__ );
define( 'AAGAM_VERSION', '3.0.2' );

// Load the autoloader.
require_once __DIR__ . '/includes/class-autoloader.php';
\AdvancedAds\GAM\Autoloader::get()->initialize();

if ( ! function_exists( 'wp_advads_gam' ) ) {
	/**
	 * Returns the main instance of the plugin.
	 *
	 * @since 2.4.0
	 *
	 * @return \AdvancedAds\GAM\Plugin
	 */
	function wp_advads_gam() {
		return \AdvancedAds\GAM\Plugin::get();
	}
}

\AdvancedAds\GAM\Bootstrap::get()->start();

