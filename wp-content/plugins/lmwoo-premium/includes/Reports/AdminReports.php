<?php
namespace LicenseManagerForWooCommerce\Reports;
/**
 * Reports Admin
 *
 * @author   Prospress
 * @category Admin
 * @package  WooCommerce Subscriptions/Admin
 * @version  2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if (class_exists('AdminReports', false)) {
	return new AdminReports();
}

/**
 * WCS_Admin_Reports Class
 *
 * Handles the reports screen.
 */
class AdminReports {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add the reports layout to the WooCommerce -> Reports admin section
		add_filter( 'woocommerce_analytics_report_menu_items', __CLASS__ . '::initialize_reports', 12, 1 );

		// Add any necessary scripts
		//add_action( 'admin_enqueue_scripts', __CLASS__ . '::reports_scripts' );
	}

	/**
	 * Add the 'Subscriptions' report type to the WooCommerce reports screen.
	 *
	 * @param array Array of Report types & their labels, excluding the Subscription product type.
	 * @return array Array of Report types & their labels, including the Subscription product type.
	 * @since 2.1
	 */
	public static function initialize_reports( $reports ) {
		$reports[] = array(
				'id'       => 'woocommerce-analytics-licenses',
				'title'    => __( 'Licenses', 'license-manager-for-woocommerce' ),
				'parent'   => 'woocommerce-analytics',
				'path'     => '/analytics/licenses',
				'nav_args' => array(
					'order'  => 110,
					'parent' => 'woocommerce-analytics',
				),
			);
		return $reports;
	}

	/**
	 * Add any subscriptions report javascript to the admin pages.
	 *
	 * @since 1.5
	 */
	public static function reports_scripts() {
		global $wp_query, $post;

		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$screen       = get_current_screen();
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce-subscriptions' ) );

		// Reports Subscriptions Pages
		if ( in_array( $screen->id, apply_filters( 'woocommerce_reports_screen_ids', array( $wc_screen_id . '_page_wc-reports', 'toplevel_page_wc-reports', 'dashboard' ) ) ) && isset( $_GET['tab'] ) && 'subscriptions' == $_GET['tab'] ) {

			wp_enqueue_script( 'wcs-reports', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'assets/js/admin/reports.js', array( 'jquery', 'jquery-ui-datepicker', 'wc-reports', 'accounting' ), WC_Subscriptions::$version );

			// Add currency localisation params for axis label
			wp_localize_script( 'wcs-reports', 'wcs_reports', array(
				'currency_format_num_decimals' => wc_get_price_decimals(),
				'currency_format_symbol'       => get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'  => esc_js( wc_get_price_decimal_separator() ),
				'currency_format_thousand_sep' => esc_js( wc_get_price_thousand_separator() ),
				'currency_format'              => esc_js( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
			) );

			wp_enqueue_script( 'flot-order', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'assets/js/admin/jquery.flot.orderBars' . $suffix . '.js', array( 'jquery', 'flot' ), WC_Subscriptions::$version );
			wp_enqueue_script( 'flot-axis-labels', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'assets/js/admin/jquery.flot.axislabels' . $suffix . '.js', array( 'jquery', 'flot' ), WC_Subscriptions::$version );

			// Add tracks script if tracking is enabled.
			if ( 'yes' === get_option( 'woocommerce_allow_tracking', 'no' ) ) {
				wp_enqueue_script( 'wcs-tracks', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'assets/js/admin/tracks.js', array( 'jquery' ), WC_Subscriptions::$version, true );
			}
		}
	}

}
