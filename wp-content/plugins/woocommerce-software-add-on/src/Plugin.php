<?php
/**
 * Software Add-On for WooCommerce.
 *
 * @since 1.8.0
 */

namespace Themesquad\WC_Software_Addon;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Software_Addon\Admin\Admin;
use Themesquad\WC_Software_Addon\Database\Installer;
use Themesquad\WC_Software_Addon\Internal\Traits\Singleton;

/**
 * Plugin class.
 */
class Plugin {

	use Singleton;

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	protected function __construct() {
		$this->define_constants();
		$this->init();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.8.0
	 */
	private function define_constants() {
		$this->define( 'WC_SOFTWARE_ADDON_VERSION', '1.9.2' );
		$this->define( 'WC_SOFTWARE_VERSION', WC_SOFTWARE_ADDON_VERSION );
		$this->define( 'WC_SOFTWARE_ADDON_PATH', plugin_dir_path( WC_SOFTWARE_ADDON_FILE ) );
		$this->define( 'WC_SOFTWARE_ADDON_URL', plugin_dir_url( WC_SOFTWARE_ADDON_FILE ) );
		$this->define( 'WC_SOFTWARE_ADDON_BASENAME', plugin_basename( WC_SOFTWARE_ADDON_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.8.0
	 *
	 * @param string      $name  The constant name.
	 * @param string|bool $value The constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Init plugin.
	 *
	 * @since 1.8.0
	 */
	private function init() {
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'woocommerce_loaded', array( $this, 'wc_loaded' ) );

		Installer::init();

		if ( is_admin() ) {
			Admin::init();
		}
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 1.8.0
	 */
	public function declare_compatibility() {
		// Compatible with the 'High-Performance Order Storage' feature.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_SOFTWARE_ADDON_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.8.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-software-add-on', false, dirname( WC_SOFTWARE_ADDON_BASENAME ) . '/languages' );
	}

	/**
	 * Load more functionality after WC has been initialized.
	 *
	 * @since 1.9.0
	 */
	public function wc_loaded() {
		if ( class_exists( 'WC_Abstract_Privacy' ) ) {
			include_once WC_SOFTWARE_ADDON_PATH . 'includes/class-wc-software-privacy.php';
		}
	}
}
