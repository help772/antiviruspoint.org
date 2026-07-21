<?php
/**
 * WooCommerce AvaTax - Shipping Address Validation Block Integration
 */

require_once __DIR__ . '/abstract-avatax-blocks-integration.php';

/**
 * Shipping Address Validation Block Integration.
 * Handles registration of shipping address validation block for WooCommerce Checkout.
 */
class Shipping_Address_Blocks_Integration extends Abstract_AvaTax_Blocks_Integration {

	/**
	 * Get integration configuration.
	 *
	 * @return array Configuration array with script paths and handles
	 */
	protected function get_integration_config() {
		return array(
			'name'                => 'shipping-address-validation',
			'frontend_handle'     => 'checkout-shipping-validation-block-frontend',
			'editor_handle'       => 'shipping-address-validation-block-editor',
			'frontend_path'       => '/build/checkout-shipping-validation-block-frontend.js',
			'frontend_asset_path' => '/build/checkout-shipping-validation-block-frontend.asset.php',
			'editor_path'         => '/build/ShippingAddressValidation/index.js',
			'editor_asset_path'   => '/build/indexShippingAddressValidation.asset.php',
		);
	}
}