<?php

use LicenseManagerForWooCommerce\Settings;

defined( 'ABSPATH' ) || exit;

function lmfwc_stock_increase( $product, $amount = 1 ) {
	return lmfwc_stock_modify( $product, 'increase', $amount );
}

function lmfwc_stock_decrease( $product, $amount = 1 ) {
	return lmfwc_stock_modify( $product, 'decrease', $amount );
}


function lmfwc_stock_modify( $product, $action, $amount = 1 ) {
	// Check if the setting is enabled
	if ( !  Settings::get( 'lmfwc_enable_stock_manager', Settings::SECTION_WOOCOMMERCE ) ) {
		return false;
	}

	// Retrieve the WooCommerce Product if we're given an ID
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );
	}

	// No need to modify if WooCommerce is not managing the stock
	if ( ! $product instanceof WC_Product || ! $product->managing_stock() ) {
		return false;
	}

	// Retrieve the current stock
	$stock = $product->get_stock_quantity();

	// Normalize
	if ( null === $stock ) {
		$stock = 0;
	}

	// Add or subtract the given amount to the stock
	if ( 'increase' === $action ) {
		$stock += $amount;
	} elseif ( 'decrease' === $action ) {
		$stock -= $amount;
	}
	/**
	 * Filter lmfwc_pre_manipulate_stock
	 *
	 * @since 1.0.0
	 */
	$stock = apply_filters( 'lmfwc_pre_manipulate_stock', $stock, $product, $action, $amount );

	// Set and save
	$product->set_stock_quantity( $stock );
	$product->save();

	return $product;
}
