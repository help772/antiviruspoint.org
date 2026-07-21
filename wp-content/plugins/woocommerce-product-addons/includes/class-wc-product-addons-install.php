<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installation/Migration Class.
 *
 * Handles the activation/installation of the plugin.
 *
 * @package  Installation
 * @version  8.3.1
 */
class WC_Product_Addons_Install {
	/**
	 * Initialize hooks.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	public static function init() {
		add_action( 'wc_pao_daily', array( __CLASS__, 'maybe_create_files' ) );
		self::run();
	}

	/**
	 * Run the installation.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	private static function run() {
		$installed_version = get_option( 'wc_pao_version' );

		/**
		 * Filter to skip the migration of data from 2.x to 3.0.
		 *
		 * @since 7.1.1
		 *
		 * @param bool $should_migrate Whether to migrate the product addons or not.
		 *
		 * @returns bool
		 */
		if ( apply_filters( 'woocommerce_product_addons_enable_migration_3_0', false ) ) {
			self::migration_3_0_product();
		}

		// Check the version before running.
		if ( ! defined( 'IFRAME_REQUEST' ) && ( $installed_version !== WC_PRODUCT_ADDONS_VERSION ) ) {
			if ( ! defined( 'WC_PAO_INSTALLING' ) ) {
				define( 'WC_PAO_INSTALLING', true );
			}

			self::update_plugin_version();
			self::create_events();
			self::create_files();

			if ( version_compare( $installed_version, '3.0', '<' ) ) {
				self::migration_3_0();
			}

			do_action( 'wc_pao_updated' );
		}
	}

	/**
	 * Updates the plugin version in db.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	private static function update_plugin_version() {
		delete_option( 'wc_pao_version' );
		add_option( 'wc_pao_version', WC_PRODUCT_ADDONS_VERSION );
	}

	/**
	 * 3.0 migration script.
	 *
	 * @since 3.0.0
	 */
	private static function migration_3_0() {
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/updates/class-wc-product-addons-migration-3-0.php';
	}

	/**
	 * 3.0 migration script for product level.
	 *
	 * @since 3.0.0
	 */
	private static function migration_3_0_product() {
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/updates/class-wc-product-addons-migration-3-0-product.php';
	}

	/**
	 * Schedule cron events.
	 *
	 * @since 6.1.3
	 */
	private static function create_events() {
		if ( ! wp_next_scheduled( 'wc_pao_daily' ) ) {
			wp_schedule_event( time() + 10, 'daily', 'wc_pao_daily' );
		}

		if ( ! wp_next_scheduled( 'wc_pao_hourly' ) ) {
			wp_schedule_event( time() + 10, 'hourly', 'wc_pao_hourly' );
		}
	}

	/**
	 * Create the index.html placeholder in the PAO uploads directory.
	 *
	 * Suppresses directory listing on Apache, Nginx, and LiteSpeed without
	 * relying on server-specific configuration. Called eagerly on install/upgrade
	 * (creates the directory if needed) and again via the daily healthcheck
	 * wrapper `maybe_create_files()` if the file is ever removed after the fact.
	 *
	 * @since 8.3.1
	 */
	public static function create_files() {
		/**
		 * Filter to skip creating files. Useful for read-only filesystems.
		 *
		 * @since 8.3.1
		 *
		 * @param bool $skip Whether to skip creating files. Default false.
		 */
		if ( apply_filters( 'woocommerce_pao_install_skip_create_files', false ) ) {
			return;
		}

		$upload_dir = wp_get_upload_dir();
		$base       = $upload_dir['basedir'] . '/product_addons_uploads';
		$target     = trailingslashit( $base ) . 'index.html';

		if ( ! wp_mkdir_p( $base ) ) {
			return;
		}

		if ( file_exists( $target ) ) {
			return;
		}

		$file_handle = @fopen( $target, 'wb' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		if ( $file_handle ) {
			fwrite( $file_handle, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
			fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		}
	}

	/**
	 * Daily healthcheck: re-create the index.html placeholder if the PAO uploads
	 * directory exists but the placeholder has been removed (filesystem reset,
	 * manual deletion, backup restore, etc.).
	 *
	 * Intentionally does not create the directory itself — sites that have never
	 * received a file-upload add-on don't need an empty directory pre-created
	 * just to host a placeholder. The directory is created either by the install
	 * hook (eager) or by `wp_handle_upload()` on the first real upload.
	 *
	 * @since 8.3.1
	 */
	public static function maybe_create_files() {
		$upload_dir = wp_get_upload_dir();
		$base       = $upload_dir['basedir'] . '/product_addons_uploads';

		if ( ! is_dir( $base ) ) {
			return;
		}

		self::create_files();
	}
}

WC_Product_Addons_Install::init();
