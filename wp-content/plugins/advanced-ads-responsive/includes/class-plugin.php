<?php
/**
 * The plugin bootstrap.
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP;

use AdvancedAds\Framework\Loader;
use AdvancedAds\AMP\admin\Backend;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class
 */
class Plugin extends Loader {
	/**
	 * Get the singleton
	 *
	 * @return Plugin
	 */
	public static function get() {
		static $instance;

		if ( null === $instance ) {
			$instance = new Plugin();
			$instance->setup();
		}

		return $instance;
	}

	/**
	 * Setup and initialize
	 *
	 * @return void
	 */
	private function setup() {
		$this->define_constants();
		$this->includes();

		if ( is_admin() ) {
			$this->includes_admin();
		} else {
			$this->includes_frontend();
		}

		add_action( 'init', [ $this, 'load_textdomain' ] );
		$this->load();
	}

	/**
	 * Get settings of Adsense matched content.
	 *
	 * @param \stdClass $content parsed Ad content.
	 *
	 * @return array
	 */
	public function get_matched_content_settings( $content ) {
		return [
			'customize_enabled' => ! empty( $content->matched_content_customize_enabled ),
			'ui_type_m'         => ( isset( $content->matched_content_ui_type_m ) ) ? $content->matched_content_ui_type_m : 'image_sidebyside',
			'ui_type'           => ( isset( $content->matched_content_ui_type ) ) ? $content->matched_content_ui_type : 'image_sidebyside',
			'rows_num_m'        => ! empty( $content->matched_content_rows_num_m ) ? absint( $content->matched_content_rows_num_m ) : 2,
			'rows_num'          => ! empty( $content->matched_content_rows_num ) ? absint( $content->matched_content_rows_num ) : 2,
			'columns_num_m'     => ! empty( $content->matched_content_columns_num_m ) ? absint( $content->matched_content_columns_num_m ) : 2,
			'columns_num'       => ! empty( $content->matched_content_columns_num ) ? absint( $content->matched_content_columns_num ) : 2,
		];
	}

	/**
	 * Load translations
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'advanced-ads-responsive', false, AA_AMP_ABSPATH . 'languages' );
	}

	/**
	 * Define Advanced Ads constant
	 *
	 * @return void
	 */
	private function define_constants(): void {
		$this->define( 'AA_AMP_ADSENSE_ONLY', true );
		$this->define( 'AA_AMP_ABSPATH', plugin_dir_path( AAR_FILE ) );
		$this->define( 'AA_AMP_BASE_URL', plugin_dir_url( AAR_FILE ) );
		$this->define( 'AA_AMP_BASE_DIR', dirname( plugin_basename( AAR_FILE ) ) );
		$this->define( 'AA_AMP_PLUGIN_NAME', 'AMP Ads' );
		$this->define( 'AA_AMP_SLUG', 'responsive-ads' );

		/**
		 * AAR_AMP_ADSENSE_ONLY
		 *
		 * @deprecated 1.13.0 use AA_AMP_ADSENSE_ONLY.
		 */
		define( 'AAR_AMP_ADSENSE_ONLY', true );

		/**
		 * AAR_PLUGIN_NAME
		 *
		 * @deprecated 1.13.0 use AA_AMP_PLUGIN_NAME.
		 */
		define( 'AAR_PLUGIN_NAME', 'AMP Ads' );

		/**
		 * AAR_BASE_PATH
		 *
		 * @deprecated 1.13.0 use AA_AMP_ABSPATH.
		 */
		define( 'AAR_BASE_PATH', plugin_dir_path( AAR_FILE ) );

		/**
		 * AAR_BASE_URL
		 *
		 * @deprecated 1.13.0 use AA_AMP_BASE_URL.
		 */
		define( 'AAR_BASE_URL', plugin_dir_url( AAR_FILE ) );

		/**
		 * AAR_BASE_DIR
		 *
		 * @deprecated 1.13.0 use AA_AMP_BASE_DIR.
		 */
		define( 'AAR_BASE_DIR', dirname( plugin_basename( AAR_FILE ) ) ); // Directory of the plugin without any paths.

		/**
		 * AAR_SLUG
		 *
		 * @deprecated 1.13.0 use AA_AMP_SLUG.
		 */
		define( 'AAR_SLUG', 'responsive-ads' );
	}

	/**
	 * Includes core files used in admin and on the frontend.
	 *
	 * @return void
	 */
	private function includes(): void {
		$this->register_integration( Tablet::class );
		$this->register_integration( AdSense::class );
		$this->register_integration( Amp::class );
	}

	/**
	 * Includes core files used in frontend.
	 *
	 * @return void
	 */
	private function includes_frontend(): void {
		$this->register_integration( \AdvancedAds\AMP\frontend\Amp::class );
	}

	/**
	 * Includes core files used in admin.
	 *
	 * @return void
	 */
	private function includes_admin(): void {
		$this->register_integration( Backend::class );
		$this->register_integration( \AdvancedAds\AMP\admin\Amp::class );
		$this->register_integration( \AdvancedAds\AMP\admin\AdSense::class );
	}

	/**
	 * Return the name of the add-on's option in the DB
	 *
	 * @return string
	 */
	public function get_option_slug(): string {
		return ADVADS_SLUG . '-responsive';
	}
}
