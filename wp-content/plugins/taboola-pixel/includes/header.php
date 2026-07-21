<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Hook to add a custom JavaScript script in the head of every page

require_once 'common.php';

$tabpx_account_id = tabpx_get_account_id();

if (!empty($tabpx_account_id)) {
    add_action('wp_head', 'tabpx_add_pixel_to_head');
}

function tabpx_add_pixel_to_head()
{
    $tabpx_account_id = tabpx_get_account_id();
    $integration_type = tabpx_get_integration_type();

    // Register and enqueue a dummy script first so we can add inline script to it
    wp_register_script('taboola-pixel-tracker', '', array(), '1.0.0', false);
    wp_enqueue_script('taboola-pixel-tracker');

    // Add the Taboola pixel code as inline script
    $tabpx_script = "
        window._tfa = window._tfa || [];
        window._tfa.push({notify: 'event', name: 'page_view', it: '" . esc_js($integration_type) . "', id: " . esc_js($tabpx_account_id) . ", integration_version: '" . esc_js(tabpx_get_plugin_version()) . "'});
        !function (t, f, a, x) {
            if (!document.getElementById(x)) {
                t.async = 1;
                t.src = a;
                t.id = x;
                f.parentNode.insertBefore(t, f);
            }
        }(document.createElement('script'),
            document.getElementsByTagName('script')[0],
            '//cdn.taboola.com/libtrc/unip/" . esc_js($tabpx_account_id) . "/tfa.js',
            'tb_tfa_script');
    ";

    wp_add_inline_script('taboola-pixel-tracker', $tabpx_script);
}

?>