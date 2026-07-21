(function ($, undefined) {
	  'use strict';
	  function isset(variable){
		return  variable  !== "undefined" && variable  !== "0" && variable !== null && variable !== '';
	}
 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Layer Perview
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		
$.fn.vs_layer_options_perview =  function() { 

 	$(this).vs_layer_postion_size();
	$(this).vs_layer_text_perview(); 
 
 	$(this).vs_layer_button_perview(); 
 	$(this).vs_layer_icon_perview(); 
	$(this).vs_layer_image_perview();
	$(this).vs_layer_box_perview();
  	  
};
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Draggable Get Position
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	$.fn.vs_position_right = function() {
         return this.offsetParent().width() - (this.position().left + this.width());
    };
	$.fn.vs_position_left = function() {
         return $(this).position().left;
    };
	
	$.fn.vs_position_center = function() {
         return  (($(this).parent().width() - $(this).width()) / -2) + $(this).vs_position_left();
    };
	
	$.fn.vs_position_top = function() {
		return $(this).position().top;
    };
	
	$.fn.vs_position_bottom = function() {
         return this.offsetParent().height() - (this.position().top + this.height());
    };
	$.fn.vs_position_middle = function() {
         return  (($(this).parent().height() - $(this).height()) / -2) + $(this).vs_position_top();
    };
	
	$.fn.vs_draggable_remove_css = function() {
         return $(this).css({ 'left' : '', 'top' : '','bottom' : '','right' : '','width' : '','height' : '','transform':'' });
    };
	
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Draggable Get Horizontal
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/			
	$.fn.vs_get_horizontal = function(type) {
   	
			var horizontal='';
	
			if($(this).hasClass('vs-'+type+'-left')){
				horizontal= $(this).vs_position_left();
				
			}else if($(this).hasClass('vs-'+type+'-center')){
				
				horizontal = $(this).vs_position_center();
				
			}else if($(this).hasClass('vs-'+type+'-right')){
 
 				horizontal = $(this).vs_position_right();
				
			}else {
 				if($(this).hasClass('vs-layer-left')){
					horizontal= $(this).vs_position_left();
				}else if($(this).hasClass('vs-layer-center')){
					horizontal = $(this).vs_position_center();
				}else {
					horizontal = $(this).vs_position_right();
				}
			}
			 
			return horizontal;	

	};
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Draggable Get Vertical
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	
	$.fn.vs_get_vertical = function(type) {
	
			var vertical='';
		 	
			if($(this).hasClass('vs-'+type+'-bottom')){
				vertical= $(this).vs_position_bottom();
			}else if($(this).hasClass('vs-'+type+'-middle')){
				vertical = $(this).vs_position_middle();
			}else if($(this).hasClass('vs-'+type+'-bottom')){
				vertical = $(this).vs_position_top();	
			}else{
				if($(this).hasClass('vs-layer-bottom')){
					vertical= $(this).vs_position_bottom();
				}else if($(this).hasClass('vs-layer-middle')){
					vertical = $(this).vs_position_middle();
				}else {
					vertical = $(this).vs_position_top();	
				}
			}
			return vertical;	
	};
	 
	 
	 
	 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Draggable Position
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		
	$.fn.vs_draggable_position =  function() { 
		
 
			var key = $(this).attr('data-key');
	  
			if($('.vs_perview_slide').hasClass('vs_perview_tablet')){
			var horizontal  =$(this).vs_get_horizontal('layer-tablet');
				var vertical =$(this).vs_get_vertical('layer-tablet');	
 				$('.vs_module_layer_item[data-key="'+key+'"] input[name="tablet_position[horizontal]"]').attr('value', horizontal.toFixed(0));	 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="tablet_position[vertical]"]').attr('value',vertical.toFixed(0));	
 				  
 				$(this).css({
				'--vs-lr-tab-hor':horizontal.toFixed(0)+'px',
				'--vs-lr-tab-ver':vertical.toFixed(0) + 'px',
 			 
				});
			 
			}else if($('.vs_perview_slide').hasClass('vs_perview_mobile')){
				var horizontal  =$(this).vs_get_horizontal('layer-mobile');
				var vertical =$(this).vs_get_vertical('layer-mobile');	
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="mobile_position[horizontal]"]').attr('value', horizontal.toFixed(0));	 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="mobile_position[vertical]"]').attr('value',vertical.toFixed(0));	
 				  
 				$(this).css({
				'--vs-lr-mob-hor':horizontal.toFixed(0)+'px',
				'--vs-lr-mob-ver':vertical.toFixed(0) + 'px',
 			 
				});
			 
			}else{
				var horizontal  =$(this).vs_get_horizontal('layer');
				var vertical =$(this).vs_get_vertical('layer');	
 				$('.vs_module_layer_item[data-key="'+key+'"] input[name="position[horizontal]"]').attr('value', horizontal.toFixed(0));	 
 					 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="position[vertical]"]').attr('value',vertical.toFixed(0));	
 				  	
 				$(this).css({
				'--vs-lr-hor':horizontal.toFixed(0)+'px',
				'--vs-lr-ver':vertical.toFixed(0) + 'px', 
 		 
				});
 			 
		 	} 
	  };
 
 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Resizable Size Width
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	$.fn.vs_resizable_size_width =  function() { 
  			var width =  parseFloat($(this).width()) ;
 		
			var key = $(this).attr('data-key');
			if($('.vs_perview_slide').hasClass('vs_perview_tablet')){
			 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="tablet_size[width]"]').attr('value',width.toFixed(0));	 
 				 
 				$(this).css({
   				'--vs-lr-tab-wt':width.toFixed(0)+ 'px',
 				});
			}else if($('.vs_perview_slide').hasClass('vs_perview_mobile')){
 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="mobile_size[width]"]').attr('value',width.toFixed(0));	 
 				 
 				$(this).css({
   				'--vs-lr-mob-wt':width.toFixed(0)+ 'px',
 				});
			}else{
  				 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="size[width]"]').attr('value',width.toFixed(0));	 
		 
				$(this).css({
  				'--vs-lr-wt':width.toFixed(0)+ 'px',
 				});
 			 
			}
 	  };
 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Resizable Size Height
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	$.fn.vs_resizable_size_height =  function() { 
  			var height =  parseFloat($(this).height()) ;
 		
			var key = $(this).attr('data-key');
			if($('.vs_perview_slide').hasClass('vs_perview_tablet')){
			 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="tablet_size[height]"]').attr('value',height.toFixed(0));	 
 				 
 				$(this).css({
   				'--vs-lr-tab-ht':height.toFixed(0)+ 'px',
 				});
			}else if($('.vs_perview_slide').hasClass('vs_perview_mobile')){
 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="mobile_size[height]"]').attr('value',height.toFixed(0));	 
 				 
 				$(this).css({
   				'--vs-lr-mob-ht':height.toFixed(0)+ 'px',
 				});
			}else{
  				 
				$('.vs_module_layer_item[data-key="'+key+'"] input[name="size[height]"]').attr('value',height.toFixed(0));	 
		 
 				$(this).css({
  				'--vs-lr-ht':height.toFixed(0)+ 'px',
 				});
 			 
			}
 	  };	 
	 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Draggable
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
 
  	$.fn.vs_draggable =  function() { 
 
		$('.vs_draggable').draggable(
			{
				 start: function() {
				var width=  $(this).width()+'px';
				var height=  $(this).height()+'px';
 			 	$(this).css({'right':'auto','transform':'none','width':width,'height':height});
  			 $(this).parents('.vs_panel').find('.vs_draggable_active').removeClass('vs_draggable_active');
			$(this).addClass('vs_draggable_active');

 
 			},
			stop: function() {
				$(this).vs_draggable_position();
				$(this).vs_draggable_remove_css();

			}
			 
		}); 
		$('.vs_draggable').resizable(
				{
					handles: "n,e,s, w,ne,se,sw,nw",
					start: function() {
						if($(this).hasClass('vs-layer-right')){
							var left = $(this).position().left+'px';
							$(this).css({'left':left,'right':'auto'});
						}
						if($(this).hasClass('vs-layer-bottom')){
							var top = $(this).position().top+'px';
							$(this).css({'top':top,'bottom':'auto'});
						}
						},
					stop: function(event, ui) {

						$(this).vs_draggable_position();
						  if (ui.originalSize.width !== ui.size.width) {
							$(this).vs_resizable_size_width();

						}
						if (ui.originalSize.height !== ui.size.height) {
							$(this).vs_resizable_size_height();
						}
					$(this).vs_draggable_remove_css();


					}
				}
			);
  	};
	  	  
  /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Start Ready 
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		
	
jQuery(document).ready(function() {
	
	 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Sortable
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
 	$(document).on('mouseenter','.vs_draggable', function () {
		$(this).addClass('vs_draggable_hover');
	});	 
	$(document).on('mouseleave','.vs_draggable', function () {
		$(this).removeClass('vs_draggable_hover');
 	
	});
	
	$(document).on('click  keyup', '.vs_draggable', function() {
  	   
  		 $(this).parents('.vs_panel').find('.vs_draggable_active').removeClass('vs_draggable_active');
 		$(this).addClass('vs_draggable_active');
 	  
	 
  
	});		
   
	
	 
 			
	

  /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		 Layer draggable Active
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
		
	
		$(document).on('mousedown ','.vs_draggable',function(e) {
			var key =$(this).attr('data-key');
  		 $(this).parents('.vs_panel').find('.vs_module_side_options_active,.vs_module_slide_options_active').removeClass('vs_module_side_options_active vs_module_slide_options_active');
			$('#vs_module_layer_'+ key).addClass('vs_module_side_options_active');
			$(this).parents('.vs_panel').addClass('vs_panel_options_active');
	  			$('.vs_module_side_options_active').vs_fold_hide();

		});
 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Ready END 
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		

	$(document).on('click', ".vs_animte_play", function(e) {
		
 		
  	
     $('.vs_perview_slide').addClass('vs_perview_animte_active');
	
   		var duplicate = $(".vs-perview-slide").clone();
		$(".vs_perview_slide_content .vs-slide-list").append(duplicate);
		$(".vs_perview_slide_content div.vs-slide:last-child").addClass('vs-slide-item active').removeClass('vs-perview-slide');
 
			$('.vs_module_layer_item').each( function(index, element) {
   							$(this).vs_layer_effectss();
			});
 
 						  
		setTimeout(function(){
			$(".vs_perview_slide_content div.vs-slide:last-child").addClass('vs-animte-active');
			}, 100);


  	});	
	
$(document).on('click', ".vs_perview_animte_active .vs_animte_stop", function(e) {
		 
   $('.vs_perview_slide ').removeClass('vs_perview_animte_active');
	$('.vs_perview_slide_content .vs-slide-item').remove();

   
  });	
 
});  		


 
})(jQuery);
