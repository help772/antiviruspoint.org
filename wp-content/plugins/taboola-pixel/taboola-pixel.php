<?php
/**
 * Plugin Name:       Taboola Pixel
 * Description:       Taboola Pixel is a WordPress plugin that injects the Taboola Pixel code into your website.
 * Version:           1.0.24
 * Author:            Taboola
 * Author URI:        https://taboola.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       taboola-pixel
 */


if (!defined('ABSPATH')) {
    exit;
}

// Include the settings file
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';

// Include the header file
require_once plugin_dir_path(__FILE__) . 'includes/header.php';

// Include woocommerce events file
require_once plugin_dir_path(__FILE__) . 'includes/woocommerce-events.php';

?>
