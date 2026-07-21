<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

const DEFAULT_DOMAIN_SERVER = "https://tools.adcore.com";
 
$option_name = 'wporg_option';
 
delete_option($option_name);
 
// for site options in Multisite
delete_site_option($option_name);
 
// drop a custom database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mytable");

$nonce = wp_create_nonce();
$current_user = wp_get_current_user();
$title = get_bloginfo( 'name' );
$site_domain = get_site_url(); //  http://localhost
$site_url = $_SERVER['HTTP_HOST']; //  localhost
$params = array(
    "user_email" => get_bloginfo('admin_email'),
    "shop_url" => $site_url,
    "domain" => $site_domain,
    "shop_name" => $title,
    "user_name" => $current_user->user_login,
);
$instance = base64_encode(json_encode($params));
$effortless_marketing_setting_options = get_option( 'effortless_marketing_setting_option_name' ); // Array of All Options
$server_domain = empty($effortless_marketing_setting_options['server_domain']) ? DEFAULT_DOMAIN_SERVER : $effortless_marketing_setting_options['server_domain'];
$url = $src = $server_domain . '/api/woo/uninstall?instance_id=' . $instance;
$response = wp_remote_post( $url, $args );

?>