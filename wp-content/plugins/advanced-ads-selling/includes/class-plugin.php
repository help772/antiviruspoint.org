<?php
/**
 * The plugin bootstrap.
 *
 * @package AdvancedAds\SellingAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.5.0
 */

namespace AdvancedAds\SellingAds;

use Advanced_Ads_Selling;
use Advanced_Ads_Selling_Admin;
use Advanced_Ads_Selling_Admin_Ad_Order_Meta_Box;
use Advanced_Ads_Selling_Admin_Ad_Product;
use Advanced_Ads_Selling_Admin_Order_Page;
use Advanced_Ads_Selling_Admin_Placements;
use Advanced_Ads_Selling_Ads_Page_Endpoint;
use Advanced_Ads_Selling_Ajax;
use Advanced_Ads_Selling_Notifications;
use Advanced_Ads_Selling_Order;
use Advanced_Ads_Selling_Plugin;
use Advanced_Ads_Selling_Public_Order;
use AdvancedAds\Framework\Loader;
use AdvancedAds\SellingAds\Installation\Install;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin.
 */
class Plugin extends Loader {

	/**
	 * Main instance
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @return Plugin
	 */
	public static function get(): Plugin {
		static $instance;

		if ( null === $instance ) {
			$instance = new Plugin();
			$instance->setup();
		}

		return $instance;
	}

	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	public function get_version(): string {
		return AASA_VERSION;
	}

	/**
	 * Bootstrap plugin.
	 *
	 * @return void
	 */
	private function setup(): void {
		$this->define_constants();
		$this->includes();
		$this->includes_admin();

		/**
		 * Old loading strategy
		 *
		 * TODO: need to remove it in future.
		 */
		( new Advanced_Ads_Selling_Notifications() );
		( new Advanced_Ads_Selling_Order() );

		if ( wp_doing_ajax() ) {
			( new Advanced_Ads_Selling_Ajax() );
		}

		if ( is_admin() ) {
			( new Advanced_Ads_Selling_Admin() );
			( new Advanced_Ads_Selling_Admin_Ad_Product() );
			( new Advanced_Ads_Selling_Admin_Ad_Order_Meta_Box() );
			( new Advanced_Ads_Selling_Admin_Order_Page() );
			( new Advanced_Ads_Selling_Admin_Placements() );
		} else {
			( new Advanced_Ads_Selling() );
			( new Advanced_Ads_Selling_Public_Order() );
		}

		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		if ( isset( $options['ads-page'] ) && $options['ads-page'] ) {
			update_option( 'advanced-ads-selling-permalinks-flushed', 0 );
			( new Advanced_Ads_Selling_Ads_Page_Endpoint() );
			add_action( 'init', [ $this, 'flush_rewrite_rules_maybe' ] );
		}

		add_action( 'init', [ $this, 'load_textdomain' ] );
		$this->load();
	}

	/**
	 * Define Advanced Ads constant
	 *
	 * @return void
	 */
	private function define_constants(): void {
		$this->define( 'AA_SELLING_ABSPATH', dirname( AASA_FILE ) . '/' );
		$this->define( 'AA_SELLING_BASENAME', plugin_basename( AASA_FILE ) );
		$this->define( 'AA_SELLING_BASE_URL', plugin_dir_url( AASA_FILE ) );
		$this->define( 'AA_SELLING_SLUG', 'advanced-ads-selling' );

		// Deprecated Constants.
		/**
		 * AASA_BASE_PATH
		 *
		 * @deprecated 1.5.0 use AA_SELLING_ABSPATH now.
		 */
		define( 'AASA_BASE_PATH', AA_SELLING_ABSPATH );

		/**
		 * AASA_BASE_URL
		 *
		 * @deprecated 1.5.0 use AA_SELLING_BASE_URL now.
		 */
		define( 'AASA_BASE_URL', AA_SELLING_BASE_URL );

		/**
		 * AASA_BASE_DIR
		 *
		 * @deprecated 1.5.0 use AA_SELLING_BASENAME now.
		 */
		define( 'AASA_BASE_DIR', AA_SELLING_BASENAME );

		/**
		 * AASA_SLUG
		 *
		 * @deprecated 1.5.0 use AA_SELLING_SLUG now.
		 */
		define( 'AASA_SLUG', AA_SELLING_SLUG );
	}

	/**
	 * Includes core files used in admin and on the frontend.
	 *
	 * @return void
	 */
	private function includes(): void {
		$this->register_initializer( Install::class );
	}

	/**
	 * Includes core files used in admin.
	 *
	 * @return void
	 */
	private function includes_admin(): void {
		if ( ! is_admin() ) {
			return;
		}
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		$locale = apply_filters( 'plugin_locale', determine_locale(), 'advanced-ads-selling' );

		unload_textdomain( 'advanced-ads-selling' );
		if ( false === load_textdomain( 'advanced-ads-selling', WP_LANG_DIR . '/plugins/advanced-ads-selling-' . $locale . '.mo' ) ) {
			load_textdomain( 'advanced-ads-selling', WP_LANG_DIR . '/advanced-ads-selling/advanced-ads-selling-' . $locale . '.mo' );
		}

		load_plugin_textdomain( 'advanced-ads-selling', false, dirname( AA_SELLING_BASENAME ) . '/languages' );
	}

	/**
	 * Flush rewrite rules if the previously added flag doesn't exist,
	 * and then set the flag
	 */
	public function flush_rewrite_rules_maybe() {
		if ( ! get_option( 'advanced-ads-selling-permalinks-flushed' ) || get_option( 'advanced-ads-selling-permalinks-flushed' ) === 0 ) {
			flush_rewrite_rules( false );
			update_option( 'advanced-ads-selling-permalinks-flushed', 1 );
		}
	}
}
