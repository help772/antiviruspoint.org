<?php
/**
 * The plugin bootstrap.
 *
 * @package AdvancedAds\GAM
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   2.4.0
 */

namespace AdvancedAds\GAM;

use Advanced_Ads_Gam_Admin;
use Advanced_Ads_Gam_Ajax;
use Advanced_Ads_Gam_Importer;
use Advanced_Ads_Network_Gam;
use AdvancedAds\Framework\Loader;

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
		return AAGAM_VERSION;
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
		Advanced_Ads_Network_Gam::get_instance()
			->register();

		if ( is_admin() ) {
			Advanced_Ads_Gam_Admin::get_instance();
			Advanced_Ads_Gam_Importer::get_instance();
			if ( wp_doing_ajax() ) {
				Advanced_Ads_Gam_Ajax::get_instance();
			}
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
		$this->define( 'AA_GAM_ABSPATH', dirname( AAGAM_FILE ) . '/' );
		$this->define( 'AA_GAM_BASENAME', plugin_basename( AAGAM_FILE ) );
		$this->define( 'AA_GAM_BASE_URL', plugin_dir_url( AAGAM_FILE ) );
		$this->define( 'AA_GAM_SLUG', 'advanced-ads-gam' );
		$this->define( 'AAGAM_OPTION', 'advanced-ads-gam-options' );
		$this->define( 'AAGAM_API_KEY_OPTION', 'advanced-ads-gam-apikey' );
		$this->define( 'AAGAM_NO_SOAP_URL', 'https://gam-connect.wpadvancedads.com/api/v1/' );

		// Deprecated Constants.
		/**
		 * AAGAM_BASE
		 *
		 * @deprecated 2.4.0 use AA_GAM_BASENAME now.
		 */
		define( 'AAGAM_BASE', AA_GAM_BASENAME );

		/**
		 * AAGAM_BASE_PATH
		 *
		 * @deprecated 2.4.0 use AA_GAM_ABSPATH now.
		 */
		define( 'AAGAM_BASE_PATH', AA_GAM_ABSPATH );

		/**
		 * AAGAM_BASE_URL
		 *
		 * @deprecated 2.4.0 use AA_GAM_BASE_URL now.
		 */
		define( 'AAGAM_BASE_URL', AA_GAM_BASE_URL );

		/**
		 * AAGAM_SETTINGS
		 *
		 * @deprecated 2.4.0 use AA_GAM_SLUG now.
		 */
		define( 'AAGAM_SETTINGS', AA_GAM_SLUG );
	}

	/**
	 * Includes core files used in admin and on the frontend.
	 *
	 * @return void
	 */
	private function includes(): void {
		$this->register_integration( Plugin_Types::class );
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
		$locale = apply_filters( 'plugin_locale', determine_locale(), 'advanced-ads-gam' );

		unload_textdomain( 'advanced-ads-gam' );
		if ( false === load_textdomain( 'advanced-ads-gam', WP_LANG_DIR . '/plugins/advanced-ads-gam-' . $locale . '.mo' ) ) {
			load_textdomain( 'advanced-ads-gam', WP_LANG_DIR . '/advanced-ads-gam/advanced-ads-gam-' . $locale . '.mo' );
		}

		load_plugin_textdomain( 'advanced-ads-gam', false, dirname( AA_GAM_BASENAME ) . '/languages' );
	}
}
