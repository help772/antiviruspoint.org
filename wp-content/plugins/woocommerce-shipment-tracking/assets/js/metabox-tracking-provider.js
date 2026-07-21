/**
 * Tracking Provider Handler for Metabox
 *
 * Handles dynamic tracking link preview based on selected provider.
 *
 * @package WC_Shipment_Tracking
 */

jQuery( function( $ ) {

	'use strict';

	var wcShipmentTrackingProvider = {

		/**
		 * Initialize the tracking provider handler.
		 */
		init: function() {
			// Hide custom fields by default.
			$( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).hide();

			// Bind change event handlers.
			$( 'input#custom_tracking_link, input#tracking_number, #tracking_provider' ).on( 'change', this.updateTrackingLink ).trigger( 'change' );
		},

		/**
		 * Update tracking link preview.
		 */
		updateTrackingLink: function() {
			var tracking  = $( 'input#tracking_number' ).val();
			var provider  = $( '#tracking_provider' ).val();
			var providers = wcShipmentTrackingMetabox.providers;
			var postcode  = $( '#_shipping_postcode' ).val();
			var country   = $( '#_shipping_country' ).val();
			var link      = '';

			// Fallback to billing postcode if shipping postcode is empty.
			if ( ! postcode.length ) {
				postcode = $( '#_billing_postcode' ).val();
			}

			postcode = encodeURIComponent( postcode );
			country  = encodeURIComponent( country );

			// Check if provider is predefined or custom.
			if ( provider && providers[ provider ] ) {
				// Predefined provider.
				link = providers[ provider ];
				link = link.replace( '%251%24s', tracking );
				link = link.replace( '%252%24s', postcode );
				link = link.replace( '%253%24s', country );
				link = decodeURIComponent( link );

				$( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).hide();
			} else {
				// Custom provider.
				$( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).show();
				link = $( 'input#custom_tracking_link' ).val();
			}

			// Update preview link.
			if ( link ) {
				$( 'p.preview_tracking_link a' ).attr( 'href', link );
				$( 'p.preview_tracking_link' ).show();
			} else {
				$( 'p.preview_tracking_link' ).hide();
			}
		}
	};

	wcShipmentTrackingProvider.init();

} );
