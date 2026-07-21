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

require_once __DIR__ . '/abstract-avatax-blocks-integration.php';

define( 'AVA_BLOCK_VERSION', '1.0.0' );

/**
 * Class for integrating with WooCommerce Blocks.
 * Handles registration of ECM (E-Commerce Module) links blocks.
 * Note: This integration also registers block styles via additional_initialization().
 */
class Avatax_Blocks_Integration extends Abstract_AvaTax_Blocks_Integration {

	/**
	 * Get integration configuration.
	 *
	 * @return array Configuration array with script paths and handles
	 */
	protected function get_integration_config() {
		return array(
			'name'                => 'ecm-links',
			'frontend_handle'     => 'ecm-links',
			'editor_handle'       => 'ecm-links-block-editor',
			'frontend_path'       => '/build/ecm-links.js',
			'frontend_asset_path' => '/build/ecm-links.asset.php',
			'editor_path'         => '/build/index.js',
			'editor_asset_path'   => '/build/index.asset.php',
		);
	}

	/**
	 * Additional initialization to register block styles.
	 *
	 * @return void
	 */
	protected function additional_initialization() {
		$this->register_block_style();
	}

	/**
	 * Register styles for ECM links inner block.
	 *
	 * @return void
	 */
	private function register_block_style() {
		wp_enqueue_style( 'avatax-blocks-style' );
	}

	/**
	 * Get the version constant to use.
	 * Uses AVA_BLOCK_VERSION instead of ORDD_BLOCK_VERSION.
	 *
	 * @return string Version string
	 */
	protected function get_version_constant() {
		return defined( 'AVA_BLOCK_VERSION' ) ? AVA_BLOCK_VERSION : '1.0.0';
	}
}
