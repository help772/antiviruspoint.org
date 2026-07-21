/**
 * FedEx Shipping Debug Functionality
 *
 * Handles debug information display toggle on cart/checkout pages.
 */
jQuery( function( $ ) {
	'use strict';

	// Handle debug reveal toggle
	$( document.body ).on( 'click', 'a.debug_reveal', function( e ) {
		e.preventDefault();
		$( this ).closest( 'div' ).find( '.debug_info' ).slideToggle();
		if ( $( this ).text() === 'Show' ) {
			$( this ).text( 'Hide' );
		} else {
			$( this ).text( 'Show' );
		}
	} );

	// Hide debug info initially
	$( 'pre.debug_info' ).hide();

	// Hide debug info after cart/checkout updates
	$( document.body ).on( 'updated_wc_div wc_update_cart updated_checkout added_to_cart removed_from_cart wc_update_cart', function() {
		$( 'pre.debug_info' ).hide();
	} );
} );
