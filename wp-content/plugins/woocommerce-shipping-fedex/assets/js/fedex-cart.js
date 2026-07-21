/**
 * FedEx Shipping Cart Functionality
 *
 * Handles FedEx-specific product removal in cart.
 */
jQuery( function( $ ) {
	'use strict';

	// Handle FedEx product removal
	$( '.woocommerce' ).on( 'click', '.fedex-product-remove', function( e ) {
		e.preventDefault();
		var product_id = $( this ).data( 'product_id' );
		$( '.woocommerce-cart-form .product-remove > a[data-product_id="' + product_id + '"]' ).trigger( 'click' );
	} );
} );
