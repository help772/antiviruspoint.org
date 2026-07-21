<?php

/**
 *
 * Helps install a FREE SSL certificate from LetsEncrypt, fixes mixed content, insecure content by redirecting to https, and forces SSL on all pages.
 *
 * Plugin Name: SSL Zen - Free SSL Certificate & HTTPS Redirect for WordPress (Premium)
 * Plugin URI:        https://sslzen.com
 * Description:       Helps install a free SSL certificate from LetsEncrypt, fixes mixed content, insecure content by redirecting to https, and forces SSL on all pages.
 * Version:           4.7.2
 * Update URI: https://api.freemius.com
 * Author:            SSL Zen
 * Author URI:        http://sslzen.com
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ssl-zen
 * Domain Path:       ssl_zen/languages
 *
 * @author      SSL Zen
 * @category    Plugin
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * @fs_premium_only /ssl_zen/cron.php
 *
 */
/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
    die( 'Access Denied' );
}
/**
 * Require external package dependencies
 */
require_once dirname( __FILE__ ) . '/vendor/autoload.php';
if ( !function_exists( 'sz_fs' ) ) {
    // Create a helper function for easy SDK access.
    function sz_fs() {
        global $sz_fs;
        if ( !isset( $sz_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $sz_fs = fs_dynamic_init( array(
                'id'              => '4586',
                'slug'            => 'ssl-zen',
                'type'            => 'plugin',
                'public_key'      => 'pk_89da8f4d86d21701663c6381a4ab4',
                'is_premium'      => true,
                'premium_suffix'  => 'Pro',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'has_affiliation' => 'all',
                'menu'            => array(
                    'pricing'    => false,
                    'slug'       => 'ssl_zen',
                    'first-path' => 'admin.php?page=ssl_zen&tab=step1',
                ),
                'is_live'         => true,
            ) );
        }
        return $sz_fs;
    }

    // Init Freemius.
    sz_fs();
    // Disable after deactivation subscription cancellation window
    // sz_fs()->add_filter( 'show_deactivation_subscription_cancellation', '__return_false' );
    // Trying to customize the freemius message
    function sz_fs_custom_connect_message_on_update(
        $message,
        $user_first_name,
        $product_title,
        $user_login,
        $site_link,
        $freemius_link
    ) {
        return sprintf(
            __( 'Hey %1$s', 'my-text-domain' ) . ',<br>' . __( 'We highly recommend that you opt-in to our security notifications. Opting in also helps us provide you fast support. We track non-sensitive diagnostic data using Freemius.', 'ssl-zen' ),
            $user_first_name,
            '<b>' . $product_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }

    sz_fs()->add_filter(
        'connect_message_on_update',
        'sz_fs_custom_connect_message_on_update',
        10,
        6
    );
    // Signal that SDK was initiated.
    do_action( 'sz_fs_loaded' );
}
/**
 * Define constants used in the plugin
 */
if ( !defined( 'SSL_ZEN_PLUGIN_VERSION' ) ) {
    define( 'SSL_ZEN_PLUGIN_VERSION', '4.7.2' );
}
if ( !defined( 'SSL_ZEN_DIR' ) ) {
    define( 'SSL_ZEN_DIR', plugin_dir_path( __FILE__ ) . 'ssl_zen/' );
}
if ( !defined( 'SSL_ZEN_TEMPLATE_DIR' ) ) {
    define( 'SSL_ZEN_TEMPLATE_DIR', plugin_dir_path( __FILE__ ) . 'ssl_zen/templates/' );
}
if ( !defined( 'SSL_ZEN_URL' ) ) {
    define( 'SSL_ZEN_URL', plugin_dir_url( __FILE__ ) . 'ssl_zen/' );
}
if ( !defined( 'SSL_ZEN_BASEFILE' ) ) {
    define( 'SSL_ZEN_BASEFILE', plugin_basename( __FILE__ ) );
}
if ( sz_fs()->is__premium_only() ) {
    define( 'SSL_ZEN_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
    define( 'SSL_ZEN_FREE_PLUGIN_BASENAME', str_replace( '-premium', '', SSL_ZEN_PLUGIN_BASENAME ) );
}
if ( !defined( 'SSL_ZEN_PLUGIN_ALLOW_DEV' ) ) {
    // to enable development on local environments.
    define( 'SSL_ZEN_PLUGIN_ALLOW_DEV', false );
}
if ( !defined( 'SSL_ZEN_PLUGIN_ALLOW_DEBUG' ) ) {
    // to enable debugging logs
    define( 'SSL_ZEN_PLUGIN_ALLOW_DEBUG', true );
}
if ( !defined( 'SSL_ZEN_DISABLE_LETS_DEBUG' ) ) {
    define( 'SSL_ZEN_DISABLE_LETS_DEBUG', false );
}
if ( !defined( 'SSL_ZEN_PLUGIN_AUTH_HOST' ) ) {
    // the host of the auth plugin, with or without trailing slash.
    define( 'SSL_ZEN_PLUGIN_AUTH_HOST', 'https://api.sslzen.com' );
}
/**
 * Include the core file of the plugin
 */
require_once SSL_ZEN_DIR . 'classes/class.ssl_zen.php';
if ( !function_exists( 'ssl_zen_init' ) ) {
    /**
     * Function to initialize the plugin.
     *
     * @return class object
     */
    function ssl_zen_init() {
        /* Initialize the base class of the plugin */
        return ssl_zen::instance();
    }

}
/**
 * Create the main object of the plugin when the plugins are loaded
 */
add_action( 'plugins_loaded', 'ssl_zen_init' );
if ( sz_fs()->can_use_premium_code__premium_only() ) {
    /**
     * @since 1.3
     *
     * Hook to support auto deactivation on the other version activation.
     */
    register_activation_hook( __FILE__, '_activate_plugin_event_hook' );
    function _activate_plugin_event_hook() {
        add_option( 'ssl_zen_activated', 1 );
        add_option( 'ssl_zen_activated_date', time() );
        $is_premium_version_activation = current_filter() !== 'activate_' . SSL_ZEN_FREE_PLUGIN_BASENAME;
        // This logic is relevant only to plugins since both the free and premium versions of a plugin can be active at the same time.
        // 1. If running in the activation of the FREE module, get the basename of the PREMIUM.
        // 2. If running in the activation of the PREMIUM module, get the basename of the FREE.
        $other_version_basename = ( $is_premium_version_activation ? SSL_ZEN_FREE_PLUGIN_BASENAME : SSL_ZEN_PLUGIN_BASENAME );
        /**
         * If the other module version is active, deactivate it.
         *
         * is_plugin_active() checks if the plugin is active on the site or the network level and
         * deactivate_plugins() deactivates the plugin whether it's activated on the site or network level.
         *
         */
        if ( is_plugin_active( $other_version_basename ) ) {
            deactivate_plugins( $other_version_basename );
        }
        if ( sz_fs()->is__premium_only() ) {
            if ( sz_fs()->is_plan( 'cdn', true ) && 1 === intval( get_option( 'ssl_zen_deactivated' ) ) ) {
                // add this so that when the plugin is reactivated, we know we have to fire the reactivation sequence.
                add_option( 'ssl_zen_stackpath_reactivate', 1 );
                // remove this so that reactivation does not get fired repeatedly.
                delete_option( 'ssl_zen_deactivated' );
            }
        }
    }

}