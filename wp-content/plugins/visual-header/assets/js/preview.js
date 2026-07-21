
 	jQuery(document).ready(function($) {
		"use strict";
 
	wp.customize( 'vh_builder_json', function( value ) {
 		value.bind( function( newval ) {
  		$('#hw-header-wrapper').addClass('hw-perview-loading');
    			$.ajax({
				type: "POST",
				url: vh_js.ajax_url,
				data: {
					
				action: "vh_header_perview",
				header_perview : newval,
				// wpnonce: vh_js.nonce,

				 },
				success: function(response) {
				 					var $response = $(response);
	
 
					$('#hw-header-wrapper').html('');
					$('#hw-header-wrapper').removeClass('hw-perview-loading');
					$('#hw-header-wrapper').append($response);
 				}
			});
		});
 
   
});});
 