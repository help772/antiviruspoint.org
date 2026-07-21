<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tabpx_options = get_option('taboola_pixel_settings');
$tabpx_account_id = isset($tabpx_options['account_id']) ? trim(esc_js($tabpx_options['account_id'])) : '';

// Hook to add the admin menu
add_action('admin_menu', 'tabpx_add_admin_menu');

// Hook to enqueue the admin styles
add_action('admin_enqueue_scripts', 'tabpx_enqueue_admin_styles');

add_action('admin_init', 'tabpx_settings_init');

function tabpx_settings_init()
{
    // Register the setting
    register_setting('taboola_pixel_settings_group', 'taboola_pixel_settings', 'tabpx_sanitize_settings');

    // Add a settings field
    add_settings_field(
        'taboola_pixel_syndicator_id',
        'Syndicator ID',
        'taboola_pixel_syndicator_id_callback',
        'taboola-pixel-plugin',
        'taboola_pixel_main_section'
    );
}

function tabpx_sanitize_settings($input)
{
    $sanitized = array();
    
    if (isset($input['account_id'])) {
        $sanitized['account_id'] = sanitize_text_field($input['account_id']);
    }
    
    return $sanitized;
}

function tabpx_add_admin_menu()
{
    add_menu_page(
        'Taboola Pixel', // Page title
        'Taboola Pixel', // Menu title
        'manage_options', // Capability
        'taboola-pixel-admin', // Menu slug
        'tabpx_admin_page', // Callback function
        plugin_dir_url(__DIR__) . 'assets/icons/menu_icon.png', // Icon URL
        100 // Position
    );
}

function tabpx_admin_page()
{
    $tabpx_options = get_option('taboola_pixel_settings');
    $tabpx_account_id = isset($tabpx_options['account_id']) ? trim(esc_js($tabpx_options['account_id'])) : '';
    $button_text = $tabpx_account_id ? 'Uninstall' : 'Install Pixel';
    $is_pixel_installed = $tabpx_account_id ? true : false;

    include plugin_dir_path(__FILE__) . '../templates/introduction.php';
}

function tabpx_enqueue_admin_styles()
{
    $version = tabpx_get_plugin_version();
    wp_enqueue_style('taboola-pixel-admin-style', plugin_dir_url(__DIR__) . 'assets/css/style.css', array(), $version);
}

function tabpx_enqueue_admin_scripts()
{
    $version = tabpx_get_plugin_version();
    wp_enqueue_script('taboola-pixel-admin-script', plugin_dir_url(__DIR__) . 'assets/js/script.js', array('jquery'), $version, true);
    
    // Determine platform based on WooCommerce presence
    $platform = tabpx_is_woocommerce_active() ? 'wc' : 'wp';
    
    wp_localize_script('taboola-pixel-admin-script', 'taboolaPixelAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('taboola_pixel_nonce'),
        'third_party_connect_host' => TABPX_THIRD_PARTY_CONNECT_HOST,
        'api_host' => TABPX_API_HOST,
        'platform' => $platform,
        'plugin_url' => plugin_dir_url(__DIR__),
        'loading_url' => plugin_dir_url(__DIR__) . 'assets/loading.html'
    ));
}

add_action('admin_enqueue_scripts', 'tabpx_enqueue_admin_scripts');

function tabpx_install_account_id()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized', 403);
    }

    check_ajax_referer('taboola_pixel_nonce', 'nonce');

    if (isset($_POST['account_id'])) {
        $account_id = sanitize_text_field(wp_unslash($_POST['account_id']));
        $tabpx_options = get_option('taboola_pixel_settings', array());
        $tabpx_options['account_id'] = $account_id;
        update_option('taboola_pixel_settings', $tabpx_options);
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}

function tabpx_uninstall_account_id()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized', 403);
    }

    check_ajax_referer('taboola_pixel_nonce', 'nonce');
    $tabpx_options = get_option('taboola_pixel_settings', array());
    unset($tabpx_options['account_id']);
    update_option('taboola_pixel_settings', $tabpx_options);
    wp_send_json_success();
}

add_action('wp_ajax_tabpx_install_account_id', 'tabpx_install_account_id');
add_action('wp_ajax_tabpx_uninstall_account_id', 'tabpx_uninstall_account_id');

function tabpx_inject_topbar()
{
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_taboola-pixel-admin') {
        ?>
        <div class="taboola-pixel-plugin">
            <div class="topbar">
                <span>
                    <img class="icon" src="<?php echo esc_url(plugin_dir_url(__DIR__) . 'assets/icons/logo.png'); ?>" width="20" height="20" alt="Taboola Logo">
                </span>
                <span>Taboola Pixel</span>
            </div>
        </div>
        <?php
    }
}

add_action('in_admin_header', 'tabpx_inject_topbar');



