/**
 * WooCommerce CyberSource payment methods handler.
 */
jQuery( document ).ready( ( $ ) => {

	'use strict';

	window.WC_Cybersource_My_Payment_Methods_Handler = window.SV_WC_Payment_Methods_Handler_v5_15_11;

	// dispatch loaded event
	$( document.body ).trigger( 'wc_cybersource_my_payment_methods_handler_loaded' );

} );
