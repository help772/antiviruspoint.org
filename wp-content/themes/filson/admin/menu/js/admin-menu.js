jQuery(function($) {
 	jQuery(document).ready(function($) {
		"use strict";
  	function remove_add_error_loading(){
			var output ='';
 			output ='<div class="hexwp-errored">';
			output+= "خطا";
  			output+= '</div>';
		 $('.hexwp-mouse-wait').append(output);
		  setTimeout(function(){ $('.hexwp-mouse-wait').remove() }, 2500);
 	}
	
	$(document).ajaxError(function( event, jqxhr, settings, thrownError ) {
		remove_add_error_loading();
	  });
	  	jQuery(document).on("click" ,'.hexwp_title_tabs a' ,function(){
		$(this).parent().find('.hexwp_layout_active').removeClass('hexwp_layout_active');
		$(this).addClass('hexwp_layout_active');
		var value = $(this).attr('data-id');
		$(this).parents('.hexwp_icon,.hexwp_options_middle,.hexwp_model_middle').find('.hexwp_layout_group_active').removeClass('hexwp_layout_group_active');
		$(this).parents('.hexwp_icon,.hexwp_options_middle,.hexwp_model_middle').find('[data-tab="'+value+'"]').addClass('hexwp_layout_group_active');
 			
	});
		 
	   
	  	$(document).on("click",'.hexwp_title_tabs a',function() {
			var id =$(this).attr('data-id'); 
  					$('.hexwp_options_warp[data-tab="'+id+'"]').find('.hexwp_options_fold').each(function() {
					var show;
					$(this).find('.hexwp_options_fold_item').each(function() {
					var data_name = $(this).attr('data-name'); 			
					var data_value = $(this).attr('data-value');
					var type = $('.hexwp_options  [name="'+data_name+'"]').attr('type');
						if(type == 'radio'){
						var checked = $('.hexwp_options [name="'+data_name+'"][value="'+data_value+'"]').attr('checked');
							if( checked == 'checked' ){
									show = 'checked';
							}
						}else{
							var val =$('.hexwp_options [name="'+data_name+'"]').val();
							if(data_value == val){
								show = 'checked';
							}
							
						}
											
					});
						if( show == 'checked' ){
							$(this).parent().attr('data-active','show');
						}else{
							$(this).parent().attr('data-active','hide');
						}
				 });	
	 
	 
			$('.hexwp_options_warp[data-tab="'+id+'"]').find('.hexwp_options_fold_item').each(function() {
				var data_name = $(this).attr('data-name');
				var actives  = $('[name="'+data_name+'"]').parents(items).attr('data-active');
				if(	 actives == 'hide' ){
					$(this).parent().parent().attr('data-active','hide');
				}
 			}); 
		});
		
		
		
		 
  	
	 
	/****************************************************
	Icons
	*****************************************************/	
	jQuery(document).on("click",".hexwp_icon_close",function(){
 		jQuery('body').removeClass('hexwp-active-icon');
		$(this).parents('.hexwp_icon').remove();
	
	}); 		 
	jQuery(document).on("click",".hexwp_builder_choose_icon",function(){
 		jQuery('body').addClass('hexwp-active-icon');
 
		$('body').append('<div class="hexwp-mouse-wait"></div>');
  		var get = $(this).parents('.menu-type-icon').attr('data-id');
 		var data_this = $(this);
		$.ajax({
			type: 'POST',

 			url: hexwp_admin_menu_js.ajaxurl,
			data : {
				action : 'hexwp_icon_picker',
				id : get,
			},
			success:function(data) {
				
 				if( data.length){
					
					jQuery('body').append(data); 
					$('.hexwp-mouse-wait').remove(); 
				}else{
					 remove_add_error_loading();
				}
   			} 
		});  	
			 
		$.ajax({
			type: 'POST',

 			url: hexwp_admin_menu_js.ajaxurl,
			data : {
				action : 'hexwp_icon_fonts',
				ajax : '1',
 			},
			success:function(data) {
 				if( data.length){
					$('.hexwp-icon-fonts').remove();
  					jQuery('body').append(data); 
 				}else{
					 remove_add_error_loading();
				} 
			}
		});  	
		
	
		
				  
   	});
	 	 
	jQuery(document).on("click",".hexwp_icon ul li",function(){
		$(this).parents('.hexwp_icon').find('.hexwp_icon_item').removeClass('selected');
		$(this).addClass('selected');
	});
		 
	// Set Icon	 
	jQuery(document).on("click",".hexwp_set_icon",function(){
			
		var icon = $(this).parents('.hexwp_icon').find('.selected').data('icon');
		var id =   $(this).parents('.hexwp_icon').attr('data-id');
		var set = $('.menu-type-icon[data-id="'+id+'"]');
		$(set).find('.hexwp-menu-icon').remove();
		$(set).find('input').attr('value',icon);
		$(set).append('<i class="fa hexwp-menu-icon '+icon+'"><a  class="hexwp_builder_remove_icon" ></a></i>');
 		$(this).parents('.hexwp_icon').remove();
		jQuery('body').removeClass('hexwp-active-icon');
 	}); 
		
	jQuery(document).on("click",".hexwp_builder_remove_icon",function(){
		$(this).parents('.menu-type-icon').find('input').val('');
		$(this).parent().remove();
	}); 
	jQuery(document).on("keyup",".search-icon-control",function(){
		var val = $(this).val();
		if(val !== ''){
			 $(this).parents('.hexwp_icon').attr('hexwp-has-search','active');
 		}else{
			 $(this).parents('.hexwp_icon').attr('hexwp-has-search','deactive');
		}
	  $('.hexwp_icon_item').each(function(){
			 $(this).addClass('hexwp-search-item');
			if($(this).find('span').text().toLowerCase().indexOf(""+val+"") != -1 ){
			 $(this).addClass('hexwp-search-show');
			}else{
			 $(this).removeClass('hexwp-search-show');
			}
 	 });
	  $('.hexwp_icon_head').each(function(){
			 $(this).addClass('hexwp-search-item');
 	 });
  
  
 	});
	
	
	
	jQuery(document).on("click","[name*='hexwp_menu_icon_type'] option[value='image']",function(){
 		$(this).parents('.menu-item-settings').addClass('hexwp_is_icon_image');
 	}); 
		jQuery(document).on("click","[name*='hexwp_menu_icon_type'] option[value='icon']",function(){
 		$(this).parents('.menu-item-settings').removeClass('hexwp_is_icon_image');
 	}); 
		
 
  $("[name*='hexwp_menu_icon_type']").each(function(index, element) {
    if($(this).val() ==='image'){
 		$(this).parents('.menu-item-settings').addClass('hexwp_is_icon_image');
  }
});

	
 
	jQuery(document).on("click keydown keyup mouseenter mousedown","#menu-to-edit",function(){
		  $(".menu-item:not(.menu-item-depth-0) > .menu-item-settings > .menu-item-depth_1,.menu-item-depth-0 > .menu-item-settings >  .menu-item-depth_0").each(function(index, element) {
			$(this).find('input,select').removeAttr('disabled');
		});

 	}); 

	jQuery(document).on("click keydown keyup mouseenter mousedown","#menu-to-edit",function(){
		  $(".menu-item:not(.menu-item-depth-0) > .menu-item-settings > .menu-item-depth_0").each(function(index, element) {
			$(this).find('input,select').attr('disabled','disabled');
			});

 	}); 

  $(".menu-item:not(.menu-item-depth-0) > .menu-item-settings > .menu-item-depth_0").each(function(index, element) {
  		$(this).find('input,select').attr('disabled','disabled');
});

				 
	// the upload image button, saves the id and outputs a preview of the image
	$(document).on( 'click', '.hexwp_menu_add_image',function(event) {
		var imageFrame;
 		var that = $(this);
 		var remove = $(this).attr('data-remove-text');
		event.preventDefault();
		var this_class= $(this).parent();
		var options, attachment;
 		var $self = $(event.target);
 		
		// if the frame already exists, open it
		if ( imageFrame ) {
			imageFrame.open();
			return;
		}
		
		imageFrame = wp.media({
  			title: $(this).data('uploader-title'),
			button: {
				text: $(this).data('uploader-button-text'),
			},
			multiple: false
		});
		
		// set up our select handler
		imageFrame.on( 'select', function() {
			var selection = imageFrame.state().get('selection');
			
			if ( ! selection )
			return;
			
			// loop through the selected files
			selection.each( function( attachment ) {
				console.log(attachment);
				var src = attachment.attributes.sizes.full.url;
				var id = attachment.attributes.id;
				var data = '<a class="hexwp_remove_image button button-small">'+remove+'</a><img  src="'+src+'"/>';
				this_class.find('.hexwp_remove_image').remove();
				this_class.find('img').remove();
 				this_class.find('input').attr('value',id);	
 				this_class.append(data);
  			} );
		});
		
		// open the frame
		imageFrame.open();
	});
	
	$(document).on('click', '.hexwp_remove_image ', function(e) {
		$(this).parent().find('input').attr('value','');
		
		$(this).parent().find('img').remove()
		$(this).remove();
  	}); 	

   	$(document).on("click",".hexwp_options_radio_image img",function(){
			$(this).parents('.hexwp_menu_sub_column_ui').find('.selected').removeClass('selected');
			$(this).parents('.hexwp_menu_sub_column_ui').find('input:checked').attr('checked','');
 			$(this).parent().addClass('selected');
 			$(this).prev().attr('checked','checked');
  	});	
		 
 	$(document).on("click",'.hexwp_options_radio_image label',function(){
		$(this).parent().parent().find('[checked]').each(function() {
			$(this).removeAttr("checked");
		});
		$(this).find('input').attr("checked","checked");
	});
	
	
	
	});
});
 