<?php
/**
 * Handles the admin notices.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Admin;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Utilities\Admin_Utils;
use WC_Admin_Notices;

/**
 * Notices class.
 */
class Notices {

	/**
	 * Stores notices.
	 *
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Stores dismissible notices.
	 *
	 * @var array
	 */
	private static $dismiss_notices = array();

	/**
	 * Init.
	 *
	 * @since 1.9.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'includes' ) );
		add_action( 'current_screen', array( __CLASS__, 'load_dismiss_notices' ), 20 );
		add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ), 20 );
	}

	/**
	 * Includes the necessary files.
	 *
	 * @since 1.9.0
	 */
	public static function includes() {
		if ( ! class_exists( 'WC_Admin_Notices' ) ) {
			include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/class-wc-admin-notices.php';
		}
	}

	/**
	 * Loads dismissible notices.
	 *
	 * @since 1.9.0
	 */
	public static function load_dismiss_notices() {
		$notices = WC_Admin_Notices::get_notices();

		foreach ( $notices as $notice ) {
			if ( 0 !== strpos( $notice, 'wc_software_addon_' ) ) {
				continue;
			}

			if ( ! get_option( 'woocommerce_admin_notice_' . $notice ) ) {
				self::$dismiss_notices[] = $notice;
			}
		}
	}

	/**
	 * Gets core notices.
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Gets dismissible notices.
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public static function get_dismiss_notices() {
		return self::$dismiss_notices;
	}

	/**
	 * Adds a notice.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name        Notice name.
	 * @param string $notice_html Optional. Notice HTML.
	 */
	public static function add_notice( $name, $notice_html = '' ) {
		if ( ! empty( $notice_html ) ) {
			self::add_dismiss_notice( $name, $notice_html );
		} else {
			self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );
		}
	}

	/**
	 * Adds a dismissible notice.
	 *
	 * @since 1.9.0
	 *
	 * @param string $name        Notice name.
	 * @param string $notice_html Optional. Notice HTML. Default: empty.
	 */
	public static function add_dismiss_notice( $name, $notice_html = '' ) {
		if ( empty( $notice_html ) ) {
			self::$dismiss_notices = array_unique( array_merge( self::get_dismiss_notices(), array( $name ) ) );
		}

		WC_Admin_Notices::add_custom_notice( $name, $notice_html );
	}

	/**
	 * Removes a notice from being displayed.
	 *
	 * @since 1.9.0
	 *
	 * @param string $name Notice name.
	 */
	public static function remove_notice( $name ) {
		if ( in_array( $name, self::get_notices(), true ) ) {
			self::$notices = array_diff( self::get_notices(), array( $name ) );
		} else {
			self::$dismiss_notices = array_diff( self::get_dismiss_notices(), array( $name ) );
			WC_Admin_Notices::remove_notice( $name );
		}
	}

	/**
	 * Gets if a notice is being shown or not.
	 *
	 * @since 1.9.0
	 *
	 * @param string $name Notice name.
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		$has_notice = in_array( $name, self::get_notices(), true );

		if ( ! $has_notice ) {
			$has_notice = WC_Admin_Notices::has_notice( $name );
		}

		return $has_notice;
	}

	/**
	 * Gets if there are notices registered or not.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public static function has_notices() {
		$notices         = self::get_notices();
		$dismiss_notices = self::get_dismiss_notices();

		return ( ! empty( $notices ) || ! empty( $dismiss_notices ) );
	}

	/**
	 * Adds notices + styles if needed.
	 *
	 * @since 1.9.0
	 */
	public static function add_notices() {
		if ( ! self::has_notices() ) {
			return;
		}

		// If the notice scripts has already been enqueued by WC, we don't need to check these conditions.
		if ( ! wp_style_is( 'woocommerce-activation' ) ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$screen_id       = Admin_Utils::get_screen_id();
			$show_on_screens = array(
				'dashboard',
				'plugins',
			);

			// Notices should only show on WooCommerce screens, the main dashboard, and on the plugins screen.
			if ( ! in_array( $screen_id, wc_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
				return;
			}

			self::enqueue_scripts();
		}

		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
	}

	/**
	 * Enqueues scripts.
	 *
	 * @since 1.9.0
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( 'woocommerce-activation', plugins_url( '/assets/css/activation.css', WC_PLUGIN_FILE ), array(), WC_VERSION );
		wp_style_add_data( 'woocommerce-activation', 'rtl', 'replace' );
	}

	/**
	 * Outputs the notices.
	 *
	 * @since 1.9.0
	 */
	public static function output_notices() {
		$notices = array_merge( self::get_notices(), self::get_dismiss_notices() );

		foreach ( $notices as $notice ) {
			$class = self::get_notice_classname( $notice );

			if ( class_exists( $class ) ) {
				call_user_func( array( $class, 'output' ) );
			}
		}
	}

	/**
	 * Gets the notice classname.
	 *
	 * @since 1.9.0
	 *
	 * @param string $notice The notice name.
	 * @return string
	 */
	protected static function get_notice_classname( $notice ) {
		$class = ucwords( str_replace( 'wc_software_addon_', '', $notice ) ) . '_Notice';

		return __NAMESPACE__ . '\Notices\\' . $class;
	}
}
