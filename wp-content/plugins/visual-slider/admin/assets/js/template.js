jQuery(function($) {
	'use strict';
	jQuery(document).ready(function() {
		 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Error
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
  
	$(document).ajaxError(function( event, jqxhr, setting, thrownError ) {
		 $('body').vs_remove_add_error_loading();
	});
	function isset(variable){
		return  variable  !== "undefined" && variable  !== "0" && variable !== null && variable !== '';
}

 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			to Save
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
$(document).on('click', '.vs_template_cancel', function(e) {
 	$(this).parents('.vs_template').remove();
});	
//Option Full Save
 	$(document).on('click', '.vs_module_template_save', function(e) {
 			var data_id = $(this).attr('data-id');	
			
			var data_key='';
			
			if(data_id=='slide'){
				  data_key= $(this).parents('.vs_module_slide_item').attr('data-key');	
			}else if(data_id=='layer'){
				  data_key= $(this).parents('.vs_module_layer_item').attr('data-key');	
			}
			
 			$('body').append('<div class="vs-mouse-wait"></div>');
			
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_save',
					id :data_id,
					key:data_key,
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
 				},
				success:function(data) {
						$('body').append(data);
  						$('.vs-mouse-wait').remove(); 
  				 
				} 

			});	
			 
   	 });	
	 
	 	
	 	
	  
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			 Save Pross
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	 
	//Option Full Save
	$(document).on('click', '.vs_template_global .vs_template_save', function(e) {
  		var data_id = 'global';
   		var data_name = $('[name="vs_template_name"]').val();
   		var data_setting = $('[name="vs_setting_json"]').val();
   		var data_slide = $('[name="vs_slide"]').val();
		if(isset(data_name)){
 			$('body').append('<div class="vs-mouse-wait"></div>');
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_save_global',
					setting : data_setting,
					slide : data_slide,
					name : data_name,
 					id : data_id,
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
				},
				success:function(data) {
					$('.vs_template').remove();
					$('.vs-mouse-wait').remove(); 
				} 

			});	
		}else{
			$('.vs_template_massage').addClass('vs_template_massage_active');
			
		}
			 
   	 });	 
	
	//Option Full Save
	$(document).on('click', '.vs_template_all_slide .vs_template_save', function(e) {
  		var template_key = $(this).parents('.vs_template_slide').attr('data-key');		
		
 			
     		var data_name = $('[name="vs_template_name"]').val();
   			var data_slide = $('[name="vs_slide"]').val();
 			
  			$('body').append('<div class="vs-mouse-wait"></div>');
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_save_slide',
 					slide : data_slide,
				 	id : 'slide',
 					name : data_name,
 					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
				},
				success:function(data) {
 
					$('.vs_template').remove();
						$('.vs-mouse-wait').remove(); 
				} 

			});	
			 
   	 });	
	 
	 
	 
	 
	 
	 	//Option Full Save
	$(document).on('click', '.vs_template_slide .vs_template_save', function(e) {
  		var template_key = $(this).parents('.vs_template_slide').attr('data-key');		
		
		
  		var slide =  [];
			$('#vs_module_slide_'+template_key).each(function() {
				var key = $(this).attr('data-key');
				var value = $(this).attr('data-value');
				var option = $(this).find('.vs_module_slide_option').html();
				var layer = $(this).find('.vs_module_slide_layer').html();
	 
				var slide_key  = {};
				  slide_key[key] = {'value' : value,'option':option ,'layer':layer };
				 slide.push(slide_key); 
			});
 		
		
 			var data_slide = JSON.stringify(slide);
     		var data_name = $('[name="vs_template_name"]').val();
  			$('body').append('<div class="vs-mouse-wait"></div>');
			
			 
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_save_slide',
 					slide : data_slide,
 				 	id : 'slide',
 					name : data_name,
 					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
				},
				success:function(data) {
 
					$('.vs_template').remove();
						$('.vs-mouse-wait').remove(); 
				} 

			});	
			 
   	 });	
	 
	 
	$(document).on('click', '.vs_template_all_layer .vs_template_save', function(e) {
  		var template_key = $(this).parents('.vs_template_layer').attr('data-key');		
		
  		 
  			var data_layer =   '['+JSON.stringify($('body').vs_layer_json())+']';
      		var data_name = $('[name="vs_template_name"]').val();
  			$('body').append('<div class="vs-mouse-wait"></div>');
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_save_layer',
 					layer : data_layer,
 					id : 'layer',
					name : data_name,
 					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
				},
				success:function(data) {
 
					$('.vs_template').remove();
						$('.vs-mouse-wait').remove(); 
				} 

			});	
			 
   	 });	
	 
	 
	$(document).on('click', '.vs_template_layer .vs_template_save', function(e) {
  			var template_key = $(this).parents('.vs_template_layer').attr('data-key');		
     		 		var layer_key  = {};
			$('#vs_module_layer_'+template_key).each(function() {
				var key = $(this).attr('data-key');
				var id = $(this).attr('data-id');
				var option = $(this).find('#vs_layer_options').serializeJSON();
	  
				  layer_key[key] = {'id' : id,'option':option };
			});
		
		 
		 
  			var data_layer =   '['+JSON.stringify(layer_key)+']';
      		var data_name = $('[name="vs_template_name"]').val();
  			$('body').append('<div class="vs-mouse-wait"></div>');
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_save_layer',
 					layer : data_layer,
 					id : 'layer',
					name : data_name,
 					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
				},
				success:function(data) {
 
					$('.vs_template').remove();
						$('.vs-mouse-wait').remove(); 
				} 

			});	
			 
   	 });	
	 	 
 
	 
	 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			 template Options
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	





  	$(document).on('click', '.vs_module_global_template_demo', function(e) {
		 
 			$('body').append('<div class="vs-mouse-wait"></div>');
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_demo',
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
  				},
				success:function(data) {
						$('body').append(data);
  						$('.vs-mouse-wait').remove(); 
 
  				 
				} 

			});	
			 
   	 });	
 	$(document).on('click', '.vs_module_global_template_add', function(e) {
		 
 			$('body').append('<div class="vs-mouse-wait"></div>');
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_options',
					id : 'global',
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
 				},
				success:function(data) {
						$('body').append(data);
  						$('.vs-mouse-wait').remove(); 
 
  				 
				} 

			});	
			 
   	 });	
	 
	 	$(document).on('click', '.vs_module_slide_template_add ', function(e) {
		 
 			$('body').append('<div class="vs-mouse-wait"></div>');
			
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_options',
					id : 'slide',
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
 				},
				success:function(data) {
						$('body').append(data);
  						$('.vs-mouse-wait').remove(); 
   				 
				} 

			});	
			 
   	 });	

	$(document).on('click', '.vs_module_layer_template_add ', function(e) {
		 
 			$('body').append('<div class="vs-mouse-wait"></div>');
			
  			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_options',
					id : 'layer',
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
 				},
				success:function(data) {
						$('body').append(data);
  						$('.vs-mouse-wait').remove(); 
   				 
				} 

			});	
			 
   	 });	


 


	// Full Template ADD Select
	$(document).on('click', '.vs_template_item', function(e) {
		$(this).parent().find('.selected').removeClass('selected');
		$(this).addClass("selected");
  	});
	 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			 Export Pross
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	 /*************************************************************************************************************************************************************************
	--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
																			 UrlCode
	--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	**************************************************************************************************************************************************************************/
 	function urlencode(inputString='') {
		if (inputString && typeof inputString !== 'object') {
			try {
				return btoa(encodeURIComponent(inputString));
			} catch (e) {
				 return '';
			}
		} else {
			return '';
		}
	}
	//Option Full Save
	$(document).on('click', ' .vs_module_global_template_export  ', function(e) {
    	var data_setting = $('[name="vs_setting_json"]').val();
   		var data_slide = $('[name="vs_slide"]').val();
 			$('body').append('<div class="vs-mouse-wait"></div>');
    			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_export',
					setting : urlencode(data_setting),
					slide : urlencode(data_slide),
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
					
				},
				success:function(data) {
						$('body').append(data);
  						$('.vs-mouse-wait').remove(); 
				} 

			});	
		 
			 
   	 });
	 
	//Option Full Save
	$(document).on('click', ' .vs_module_global_template_import  ', function(e) {
 
 			$('body').append('<div class="vs-mouse-wait"></div>');
    			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_import',
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
 				},
				success:function(data) {
						$('body').append(data);
  						$('.vs-mouse-wait').remove(); 
				} 

			});	
		 
			 
   	 });
 
	 
 	$(document).on('click', '.vs_template_import .vs_template_add', function(e) {
		var vs_template_import = $('[name="vs_template_import"]').val();
 		
		 
  		$('body').append('<div class="vs-mouse-wait"></div>');
  		
   		$.ajax({
			type: 'POST',
	
 			url: visualslider.ajaxurl,
			data : {
				action : 'vs_module_content',
				template_import : vs_template_import,
				post_id : visualslider.post_id,
				_wpnonce : visualslider.nonce,
   			},
			success:function(data) {
		
 				if( data.length){
					$('.vs_module_content').html('');
					$('.vs_module_content').append( data);
						$('.vs-mouse-wait').remove(); 
						$('.vs_template').remove();
						$('body').vs_reload_global_slider();
						$('body').vs_sortable();
						$('body').vs_coloris();	
						$('body').vs_setting_json();	
						$(".vs_module_setting").on('change keyup mousedown',function() { 
							$('body').vs_setting_json();
						});

				}else{
					$('body').vs_remove_add_error_loading();
				}	
			 	 
			} 
		});
  	});		
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			 template add
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	

 	$(document).on('click', '.vs_template_demo .vs_template_add', function(e) {
		var data_id = $('.vs_template_item.selected').attr('data-id');
 		
		 
  		$('body').append('<div class="vs-mouse-wait"></div>');
  		
   		$.ajax({
			type: 'POST',
	
 			url: visualslider.ajaxurl,
			data : {
				action : 'vs_module_content',
				global_demo_template_id : data_id,
				post_id : visualslider.post_id,
				_wpnonce : visualslider.nonce,
   			},
			success:function(data) {

 				if( data.length){
					$('.vs_module_content').html('');
					$('.vs_module_content').append( data);
						$('.vs-mouse-wait').remove(); 
						$('.vs_template').remove();
						$('body').vs_reload_global_slider();
						$('body').vs_sortable();
						$('body').vs_coloris();	
						$('body').vs_setting_json();	
						$(".vs_module_setting").on('change keyup mousedown',function() { 
							$('body').vs_setting_json();
						});

				}else{
					$('body').vs_remove_add_error_loading();
				}	
			 	 
			} 
		});
  	});		
	 



 	$(document).on('click', '.vs_template_global .vs_template_add', function(e) {
		var data_id = $('.vs_template_item.selected').attr('data-id');
 		
		 
  		$('body').append('<div class="vs-mouse-wait"></div>');
  		
   		$.ajax({
			type: 'POST',
	
 			url: visualslider.ajaxurl,
			data : {
				action : 'vs_module_content',
				global_template_id : data_id,
				post_id : visualslider.post_id,
				_wpnonce : visualslider.nonce,
   			},
			success:function(data) {

 				if( data.length){
					$('.vs_module_content').html('');
					$('.vs_module_content').append( data);
						$('.vs-mouse-wait').remove(); 
						$('.vs_template').remove();
						$('body').vs_reload_global_slider();
						$('body').vs_sortable();
						$('body').vs_coloris();	
						$(".vs_module_setting").on('change keyup mousedown',function() { 
							$('body').vs_setting_json();
						});

				}else{
					$('body').vs_remove_add_error_loading();
				}	
			 	 
			} 
		});
  	});		
	 
 	$(document).on('click', '.vs_template_slide .vs_template_add', function(e) {
		var data_id = $('.vs_template_item.selected').attr('data-id');
 		
   		$('body').append('<div class="vs-mouse-wait"></div>');
  		
   		$.ajax({
			type: 'POST',
	
 			url: visualslider.ajaxurl,
			data : {
				action : 'vs_module_slide_list',
				slide_template_id : data_id,
				post_id : visualslider.post_id,
				_wpnonce : visualslider.nonce,

   			},
			success:function(data) {

 				if( data.length){
					$('.vs_module_slide_list').append( data);
						$('.vs-mouse-wait').remove(); 
						$('.vs_template').remove();
						$('body').vs_slide_json();
						$('body').vs_reload_global_slider();
						$('body').vs_sortable();
						$('body').vs_coloris();	
						$('body').vs_setting_json();	

				}else{
					$('body').vs_remove_add_error_loading();
				}	
			 	 
			} 
		});
  	});		
 

	function empty(variable){
		if(variable  === "undefined" && variable === null && variable === ''){
			return '';
		}else{
			return variable;
		}
	}	
	
 	$(document).on('click', '.vs_template_layer .vs_template_add', function(e) {
		var data_id = $('.vs_template_item.selected').attr('data-id');
		$('body').append('<div class="vs-mouse-wait"></div>');
  		  		var data_tablet=  $('.vs_live_setting_responsive_tablet').find('input:checked').val();
  		var data_mobile=  $('.vs_live_setting_responsive_mobile').find('input:checked').val();
   		$.ajax({
			type: 'POST',
	
 			url: visualslider.ajaxurl,
			data : {
				action : 'vs_module_layer_list',
				layer_template_id : data_id,
 				vs_tablet :  empty(data_tablet),
 				vs_mobile :  empty(data_mobile),
				post_id : visualslider.post_id,
				_wpnonce : visualslider.nonce,
   			},
			success:function(data) {

 				if( data.length){
					$('.vs_module_layer_list').append( data);
						$('.vs-mouse-wait').remove(); 
						$('.vs_template').remove();
						$('body').vs_reload_layer();

						$('body').find('.vs_module_layer_item').each(function() {
							$(this).vs_layer_options_perview();
						}); 
					 	$('body').vs_sortable_layer();
						$('body').vs_draggable();
 

				}else{
					$('body').vs_remove_add_error_loading();
				}	
			 	 
			} 
		});
  	});		
	

	//Template Remove	
	$(document).on('click', '.vs_template_remove', function(e) {
 		var data_template = $(this).parent();
		var data_key = data_template.attr('data-id');
		var data_row = $(this).parents('.vs_template').attr('data-row');

		$('body').append('<div class="vs-mouse-wait"></div>');
 		var txt;
		var r = confirm('Do you agree to delete this item?');

 	  	if (r == true) {
	
			$.ajax({
				url: visualslider.ajaxurl,
				type: 'POST',
				data : {
					action : 'vs_template_remove',
					 key : data_key,
					 id : data_row,
					post_id : visualslider.post_id,
					_wpnonce : visualslider.nonce,
				},
				success:function(data) { 
 					if( data.length){
						$(data_template).remove();
						$('.vs-mouse-wait').remove(); 
					}else{
					$('body').vs_remove_add_error_loading();
					}
						
				} 
			 });	
			
		}
   	 });	 
	});
	 
});
 