<?php
/**
 * WooCommerce AvaTax - Checkout Messages Block Integration
 */

require_once __DIR__ . '/abstract-avatax-blocks-integration.php';

/**
 * Checkout Messages Block Integration.
 * Handles registration of checkout messages block for WooCommerce Checkout.
 * Note: This integration only has frontend scripts, no editor scripts.
 */
class Checkout_Messages_Blocks_Integration extends Abstract_AvaTax_Blocks_Integration {

	/**
	 * Get integration configuration.
	 * Note: editor_path is empty as this integration doesn't have editor scripts.
	 *
	 * @return array Configuration array with script paths and handles
	 */
	protected function get_integration_config() {
		return array(
			'name'                => 'CheckoutMessages',
			'frontend_handle'     => 'checkout-msg-block-editor',
			'editor_handle'       => 'checkout-msg-block-editor',
			'frontend_path'       => '/build/indexCheckoutMessages.js',
			'frontend_asset_path' => '/build/indexCheckoutMessages.asset.php',
			'editor_path'         => '', // No editor scripts for this integration
			'editor_asset_path'   => '',
		);
	}
}