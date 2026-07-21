<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    Avalara
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Abstract base class for AvaTax block integrations.
 * Provides common functionality for registering scripts and handling WooCommerce Blocks integration.
 */
abstract class Abstract_AvaTax_Blocks_Integration implements IntegrationInterface {

	/**
	 * Get integration configuration.
	 * Child classes must implement this method to provide their specific configuration.
	 *
	 * @return array {
	 *     Configuration array with the following keys:
	 *     @type string $name                Integration name
	 *     @type string $frontend_handle     Frontend script handle
	 *     @type string $editor_handle       Editor script handle
	 *     @type string $frontend_path       Path to frontend script (relative to plugin)
	 *     @type string $frontend_asset_path Path to frontend asset file (relative to plugin)
	 *     @type string $editor_path         Path to editor script (optional, relative to plugin)
	 *     @type string $editor_asset_path   Path to editor asset file (optional, relative to plugin)
	 * }
	 */
	abstract protected function get_integration_config();

	/**
	 * Get additional dependencies to add to frontend scripts.
	 * Override in child class if needed.
	 *
	 * @return array Array of script handles to add as dependencies
	 */
	protected function get_additional_frontend_dependencies() {
		return array();
	}

	/**
	 * Get the version constant to use for script versioning.
	 * Override in child class if using a different constant.
	 *
	 * @return string Version string
	 */
	protected function get_version_constant() {
		return defined( 'ORDD_BLOCK_VERSION' ) ? ORDD_BLOCK_VERSION : '1.0.0';
	}

	/**
	 * Additional initialization hook.
	 * Override in child class for custom initialization logic.
	 *
	 * @return void
	 */
	protected function additional_initialization() {
		// Can be overridden by child classes
	}

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		$config = $this->get_integration_config();
		return $config['name'];
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 *
	 * @return void
	 */
	public function initialize() {
		$this->register_block_frontend_scripts();
		
		if ( $this->has_editor_scripts() ) {
			$this->register_block_editor_scripts();
		}
		
		// Hook for additional initialization in child classes
		$this->additional_initialization();
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		$config = $this->get_integration_config();
		return array( $config['frontend_handle'] );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		$config = $this->get_integration_config();
		return array( $config['editor_handle'] );
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		return array();
	}

	/**
	 * Check if this integration has editor scripts.
	 *
	 * @return bool True if editor scripts are configured
	 */
	protected function has_editor_scripts() {
		$config = $this->get_integration_config();
		return ! empty( $config['editor_path'] );
	}

	/**
	 * Register scripts for block editor.
	 *
	 * @return void
	 */
	public function register_block_editor_scripts() {
		$config = $this->get_integration_config();
		
		$script_path       = $config['editor_path'];
		$script_url        = plugins_url( '/woocommerce-avatax' . $script_path );
		$script_asset_path = WP_PLUGIN_DIR . '/woocommerce-avatax' . $config['editor_asset_path'];
		
		$script_asset = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_asset_path ),
			);

		wp_register_script(
			$config['editor_handle'],
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}

	/**
	 * Register scripts for frontend block.
	 *
	 * @return void
	 */
	public function register_block_frontend_scripts() {
		$config = $this->get_integration_config();
		
		$script_path       = $config['frontend_path'];
		$script_url        = plugins_url( '/woocommerce-avatax' . $script_path );
		$script_asset_path = WP_PLUGIN_DIR . '/woocommerce-avatax' . $config['frontend_asset_path'];

		$script_asset = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_asset_path ),
			);

		// Add additional dependencies if any
		$additional_deps = $this->get_additional_frontend_dependencies();
		if ( ! empty( $additional_deps ) ) {
			$script_asset['dependencies'] = array_merge(
				$script_asset['dependencies'],
				$additional_deps
			);
		}

		wp_register_script(
			$config['frontend_handle'],
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return $this->get_version_constant();
	}
}
