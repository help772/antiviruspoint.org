jQuery(function($) {
 	jQuery(document).ready(function($) {
		"use strict";
 
	function mouse_wait_start(){
			$('body').append('<div class="vh-duplicate"></div>');
		  setTimeout(function(){ $('.vh-duplicate').addClass('vh-duplicate-active').addClass('vh-duplicate-loading'); }, 1);
	}
 
 	function remove_add_error_loading(){
		$('.vh-duplicate').removeClass('vh-duplicate-loading');
			var output ='';
 			output ='<div class="vh-duplicate-message vh-errored">';
			output+= vh_duplicate.error;
  			output+= '</div>';
		 $('.vh-duplicate').append(output);
		  setTimeout(function(){ $('.vh-duplicate').remove() }, 2000);
 	}
 				
	function mouse_wait_end(){
		$('.vh-duplicate').removeClass('vh-duplicate-loading');
  		
 			var output ='';
 			output ='<div class="vh-duplicate-message vh-duplicated">';
 			output+= vh_duplicate.duplicated;
   			output+= '</div>';
		 $('body').append(output);
		  setTimeout(function(){  location.reload(); }, 2000);
 	}
	
	
	
 	$(document).on('click', '.vh_duplicate_post', function(e) {
		alert(1);
				var data_post_id =$(this).attr('vh-post-id');
					mouse_wait_start();
			 alert(vh_duplicate.nonce);
			$.ajax({
				url:vh_duplicate.ajaxurl,
				type: 'POST',
				data : {
					action : 'vh_duplicate',
					post_id :data_post_id,
 					_wpnonce: vh_duplicate.nonce,
				},
				success:function(data) {
					if(data.length){
						mouse_wait_end();
 					} else{
						 remove_add_error_loading();
					}
				} 
			});
			
		});
		
	});
});