<?php
/**
 * PHPUnit bootstrap for Australia Post unit tests.
 *
 * These are lightweight, dependency-light unit tests that exercise pure helper
 * methods via reflection. They do not need the full WordPress/WooCommerce test
 * suite or a database, so the plugin class is loaded against a minimal
 * WC_Shipping_Method stub.
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', sys_get_temp_dir() . '/' );
}

if ( ! defined( 'WC_SHIPPING_AUSTRALIA_POST_ABSPATH' ) ) {
	define( 'WC_SHIPPING_AUSTRALIA_POST_ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

if ( ! class_exists( 'WC_Shipping_Method' ) ) {
	/**
	 * Minimal stub of the WooCommerce shipping method base class so the plugin
	 * class can be loaded without bootstrapping all of WooCommerce.
	 */
	class WC_Shipping_Method {} // phpcs:ignore Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
}

require_once WC_SHIPPING_AUSTRALIA_POST_ABSPATH . 'includes/class-wc-shipping-australia-post.php';
