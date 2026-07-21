<?php
/**
 * Handles the plugin activation and install.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Database;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Admin\Notices;
use Themesquad\WC_Software_Addon\Utilities\Admin_Utils;

/**
 * Class Installer.
 */
class Installer {

	/**
	 * Plugin updater.
	 *
	 * @var Updater
	 */
	private static $updater;

	/**
	 * Database updates that need to be run per version.
	 *
	 * @var array
	 */
	private static $db_updates = array(
		'1.9.0' => 'Themesquad\WC_Software_Addon\Database\Updates\Update_190_Migrate_Options',
	);

	/**
	 * Init installation.
	 *
	 * @since 1.9.0
	 */
	public static function init() {
		Tables::register_tables();

		register_activation_hook( WC_SOFTWARE_ADDON_FILE, array( __CLASS__, 'install' ) );

		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'init', array( __CLASS__, 'init_updater' ), 7 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
		add_action( 'wc_software_addon_updater_complete', array( __CLASS__, 'updated' ) );
		add_filter( 'wpmu_drop_tables', array( Tables::class, 'drop_tables' ) );
	}

	/**
	 * Get the database updates.
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public static function get_db_updates() {
		return self::$db_updates;
	}

	/**
	 * Checks the plugin version and run the installation process if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 1.9.0
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( Versions::get_version(), WC_SOFTWARE_ADDON_VERSION, '<' ) ) {
			self::install();
		}
	}

	/**
	 * Initializes the plugin updater.
	 *
	 * @since 1.9.0
	 */
	public static function init_updater() {
		self::$updater = new Updater();
	}

	/**
	 * Install actions when an update button is clicked within the admin area.
	 *
	 * @since 1.9.0
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_wc_software_addon'] ) ) {
			check_admin_referer( 'wc_software_addon_db_update', 'wc_software_addon_db_update_nonce' );
			self::update();
		}

		if ( ! empty( $_GET['force_update_wc_software_addon'] ) ) {
			check_admin_referer( 'wc_software_addon_force_db_update', 'wc_software_addon_force_db_update_nonce' );
			self::$updater->force_process();
			wp_safe_redirect( Admin_Utils::get_license_keys_url() );
			exit;
		}
	}

	/**
	 * Removes any notices added to admin.
	 *
	 * @since 1.9.0
	 */
	private static function remove_notices() {
		// Remove not dismissed notice from a previous update.
		Notices::remove_notice( 'wc_software_addon_updated' );
	}

	/**
	 * Add installer/updater notices + styles if needed.
	 *
	 * @since 1.9.0
	 */
	public static function add_notices() {
		// Dismiss the 'updated' notice before displaying more notices.
		if ( Notices::has_notice( 'wc_software_addon_updated' ) ) {
			return;
		}

		if ( self::needs_db_update() ) {
			if ( self::$updater->is_updating() || ! empty( $_GET['do_update_wc_software_addon'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				Notices::add_notice( 'updating' );
			} else {
				Notices::add_notice( 'update' );
			}
		}
	}

	/**
	 * Init installation.
	 *
	 * @since 1.9.0
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running the installation process.
		if ( 'yes' === get_transient( 'wc_software_addon_installing' ) ) {
			return;
		}

		// Add a transient to indicate that we are running the installation process.
		set_transient( 'wc_software_addon_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		Tables::create_tables();
		self::remove_notices();
		self::maybe_create_pages();
		Versions::update_version( WC_SOFTWARE_ADDON_VERSION );
		self::maybe_update_db();

		// Installation finished.
		delete_transient( 'wc_software_addon_installing' );

		flush_rewrite_rules();

		/**
		 * Fires when the plugin installation finished.
		 *
		 * @since 1.9.0
		 */
		do_action( 'wc_software_addon_installed' );
	}

	/**
	 * Creates pages on installation.
	 *
	 * @since 1.9.0
	 */
	public static function maybe_create_pages() {
		if ( ! function_exists( 'wc_create_page' ) ) {
			include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/wc-admin-functions.php';
		}

		// Lost license page.
		wc_create_page(
			'lost-license',
			'woocommerce_lost_license_page_id',
			_x( 'Lost License', 'Title of a page', 'woocommerce-software-add-on' ),
			'<!-- wp:shortcode -->[woocommerce_software_lost_license]<!-- /wp:shortcode -->'
		);
	}

	/**
	 * Update the database if necessary.
	 *
	 * @since 1.9.0
	 */
	private static function maybe_update_db() {
		if ( ! self::needs_db_update() ) {
			Versions::update_version( WC_SOFTWARE_ADDON_VERSION, 'db' );
		}
	}

	/**
	 * Get if the database needs to be updated or not.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	private static function needs_db_update() {
		$db_version = Versions::get_version( 'db' );

		if ( ! $db_version ) {
			return false;
		}

		$updates = array_keys( self::get_db_updates() );

		usort( $updates, 'version_compare' );

		return version_compare( $db_version, end( $updates ), '<' );
	}

	/**
	 * Pushes all needed database updates to the queue for processing.
	 *
	 * @since 1.9.0
	 */
	private static function update() {
		$db_version    = Versions::get_version( 'db' );
		$db_updates    = self::get_db_updates();
		$update_queued = false;

		foreach ( $db_updates as $version => $updates ) {
			if ( version_compare( $db_version, $version, '<' ) ) {
				$updates = (array) $updates;

				foreach ( $updates as $update ) {
					self::$updater->push_to_queue( compact( 'update' ) );
					$update_queued = true;
				}

				// Update db version.
				self::$updater->push_to_queue(
					array(
						'update' => '\Themesquad\WC_Software_Addon\Database\Updates\Update_DB_Version',
						'args'   => compact( 'version' ),
					)
				);
			}
		}

		if ( $update_queued ) {
			self::$updater->save()->dispatch();
		}
	}

	/**
	 * Database updated.
	 *
	 * @since 1.9.0
	 */
	public static function updated() {
		Versions::update_version( WC_SOFTWARE_ADDON_VERSION, 'db' );

		Notices::add_dismiss_notice(
			'wc_software_addon_updated',
			__( 'Software Add-On for WooCommerce update completed. Thank you for updating to the latest version!', 'woocommerce-software-add-on' )
		);
	}
}
