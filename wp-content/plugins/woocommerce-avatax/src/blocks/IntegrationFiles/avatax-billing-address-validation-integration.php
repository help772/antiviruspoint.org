<?php
/**
 * WooCommerce AvaTax - Billing Address Validation Block Integration
 */

require_once __DIR__ . '/abstract-avatax-blocks-integration.php';

/**
 * Billing Address Validation Block Integration.
 * Handles registration of billing address validation block for WooCommerce Checkout.
 */
class Billing_Address_Blocks_Integration extends Abstract_AvaTax_Blocks_Integration {

	/**
	 * Get integration configuration.
	 *
	 * @return array Configuration array with script paths and handles
	 */
	protected function get_integration_config() {
		return array(
			'name'                => 'billing-address-validation',
			'frontend_handle'     => 'checkout-billing-validation-block-frontend',
			'editor_handle'       => 'billing-address-validation-block-editor',
			'frontend_path'       => '/build/checkout-billing-validation-block-frontend.js',
			'frontend_asset_path' => '/build/checkout-billing-validation-block-frontend.asset.php',
			'editor_path'         => '/build/billingAddressValidation/index.js',
			'editor_asset_path'   => '/build/indexBillingAddressValidation.asset.php',
		);
	}
}