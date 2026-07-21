<?php
/**
 * Software Add-On for WooCommerce Admin.
 *
 * @since 1.8.0
 */

namespace Themesquad\WC_Software_Addon\Admin;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Utilities\Admin_Utils;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin init.
	 *
	 * @since 1.8.0
	 */
	public static function init() {
		add_filter( 'woocommerce_screen_ids', array( __CLASS__, 'wc_screen_ids' ) );
		add_filter( 'plugin_action_links_' . WC_SOFTWARE_ADDON_BASENAME, array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

		Notices::init();
	}

	/**
	 * Filters the WooCommerce screen ids.
	 *
	 * @since 1.9.0
	 *
	 * @param array $screen_ids The screen ids.
	 * @return array
	 */
	public static function wc_screen_ids( $screen_ids ) {
		// Add the 'License keys' page to the list.
		$screen_ids[] = Admin_Utils::get_license_keys_screen_id();

		return $screen_ids;
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 1.8.0
	 *
	 * @param array $links The plugin links.
	 * @return array
	 */
	public static function action_links( $links ) {
		$license_keys_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( Admin_Utils::get_license_keys_url() ),
			_x( 'View Software Add-On for WooCommerce license keys', 'aria-label: settings link', 'woocommerce-software-add-on' ),
			_x( 'License Keys', 'plugin action link', 'woocommerce-software-add-on' )
		);

		$reports_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=wc-reports&tab=software' ) ),
			_x( 'View Software Add-On for WooCommerce reports', 'aria-label: settings link', 'woocommerce-software-add-on' ),
			_x( 'Reports', 'plugin action link', 'woocommerce-software-add-on' )
		);

		array_unshift( $links, $license_keys_link, $reports_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_SOFTWARE_ADDON_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/software-add-on/' ),
			esc_attr_x( 'View Software Add-On for WooCommerce documentation', 'aria-label: documentation link', 'woocommerce-software-add-on' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-software-add-on' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/my-account/create-a-ticket?select=18683' ),
			esc_attr_x( 'Open a support ticket at WooCommerce.com', 'aria-label: support link', 'woocommerce-software-add-on' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-software-add-on' )
		);

		return $links;
	}
}
