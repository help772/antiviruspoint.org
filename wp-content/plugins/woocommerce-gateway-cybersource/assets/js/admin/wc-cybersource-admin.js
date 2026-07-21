/**
 * WooCommerce CyberSource Admin scripts.
 */
jQuery( document ).ready( ( $ ) => {

	'use strict';

	/** Migrate orders button */

	$( '#js-wc-cybersource-migrate-orders' ).on( 'click', ( e ) => {

		e.preventDefault();

		if ( wc_cybersource_admin.migrate_disabled ) {

			alert( wc_cybersource_admin.migrate_disabled_text );
			return false;

		}

		if ( confirm( wc_cybersource_admin.confirmation_text ) ) {

			$( this ).closest( 'table' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );

			const data = {
				action: 'wc_cybersource_migrate_orders',
				nonce: wc_cybersource_admin.migrate_nonce,
				gateway_id: wc_cybersource_admin.gateway_id
			};

			$.post( wc_cybersource_admin.ajax_url, data, ( response ) => {

				if ( response.success ) {
					window.location = response.data;
				} else {
					console.log( response );
					$( '#wc-cybersource-migrate-status' ).text( wc_cybersource_admin.migrate_error_message );
				}
			} );
		}

		return false;
	} );
} );
