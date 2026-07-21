<?php
/**
 * WooCommerce AvaTax - VAT Validation Block Integration
 */

require_once __DIR__ . '/abstract-avatax-blocks-integration.php';

if ( ! defined( 'ORDD_BLOCK_VERSION' ) ) {
	define( 'ORDD_BLOCK_VERSION', '1.0.0' );
}

/**
 * VAT Validation Block Integration.
 * Handles registration of VAT validation block for WooCommerce Checkout.
 * Note: This integration adds an additional 'wc-avatax-frontend' dependency to frontend scripts.
 */
class VAT_Blocks_Integration extends Abstract_AvaTax_Blocks_Integration {

	/**
	 * Get integration configuration.
	 *
	 * @return array Configuration array with script paths and handles
	 */
	protected function get_integration_config() {
		return array(
			'name'                => 'VAT',
			'frontend_handle'     => 'checkout-VAT-block-frontend',
			'editor_handle'       => 'VAT-block-editor',
			'frontend_path'       => '/build/checkout-VAT-block-frontend.js',
			'frontend_asset_path' => '/build/checkout-VAT-block-frontend.asset.php',
			'editor_path'         => '/build/VAT/index.js',
			'editor_asset_path'   => '/build/indexVAT.asset.php',
		);
	}

	/**
	 * Get additional dependencies to add to frontend scripts.
	 * Adds 'wc-avatax-frontend' as a dependency.
	 *
	 * @return array Array of script handles to add as dependencies
	 */
	protected function get_additional_frontend_dependencies() {
		return array( 'wc-avatax-frontend' );
	}
}