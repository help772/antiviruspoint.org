<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define integration type constants
define('TABPX_INTEGRATION_WP', 'WP');
define('TABPX_INTEGRATION_WOOCOMMERCE', 'WOOCOMMERCE_INTEGRATION');

// Define hostname constants
define('TABPX_THIRD_PARTY_CONNECT_HOST', 'https://ads.realizeperformance.com/');
define('TABPX_API_HOST', 'https://spfy-pxl.archive-digger.com/');

function tabpx_get_plugin_version()
{
    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    $plugin_file = plugin_dir_path(__DIR__) . 'taboola-pixel.php';
    $plugin_data = get_plugin_data($plugin_file);
    
    return $plugin_data['Version'];
}

function tabpx_get_account_id()
{
    $options = get_option('taboola_pixel_settings');
    return isset($options['account_id']) ? trim(esc_js($options['account_id'])) : '';
}

function tabpx_is_woocommerce_active()
{
    // Woocommerce integration is not active in this version
    return false;
}

function tabpx_get_integration_type()
{
    // Check if WooCommerce is active
    if (tabpx_is_woocommerce_active()) {
        return TABPX_INTEGRATION_WOOCOMMERCE;
    }
    return TABPX_INTEGRATION_WP;
}