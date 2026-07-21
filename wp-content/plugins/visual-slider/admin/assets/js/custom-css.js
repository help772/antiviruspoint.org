(function ($, undefined) {
	  'use strict';
	function isset(variable) {
		if (variable === "undefined") {
			return '';
		} else if (variable === undefined) {
	
			return '';
		} else if (variable === null) {
			return '';
		} else if (variable === 0) {
			return 0;
		} else if (variable === '0') {
			return '0';
		} else {
			return variable;
		}
	
	}

 


 $.fn.vs_check_nubmer = function() { 
   	 
  
	   var val= $(this).val();
 	   var val_max= parseFloat($(this).attr('max'));
	   var val_min= parseFloat($(this).attr('min'));
 
	       
	  if( isset(val) ) {
 		   if($.isNumeric(val)  ) {
			   var num_val = parseFloat(val);
			   if( val_max >= num_val && num_val >= val_min ){
 			  $(this).removeClass('vs_bad');
			   }else{
				 $(this).addClass('vs_bad');
			   }
		  }else{
			 $(this).addClass('vs_bad');
			}
	  }else{
		  
			$(this).removeClass('vs_bad');
	  }
	 
 };
 
   
  function left_right(value) {

/*	if(value==='left'){
		return 'right';
	}else if(value==='right'){
		return 'left';
	}else{*/
			return value;
	 
	//}
	
 }

 $.fn.vs_perview_slide  = function(background_image) { 
		var slide_background_color= $(this).vs_background_gradient('sl-bg-cr','slide_background_color');
		var slide_background_image= $(this).vs_background_image('sl','slide_background_image',background_image);
		var background_image_position= $(this).vs_background_position('sl-bg-pos','slide_background_image_position');
		var tablet_background_image_position= $(this).vs_background_position('sl-tab-bg-pos','slide_tablet_background_image_position');
		var mobile_background_image_position= $(this).vs_background_position('sl-mob-bg-pos','slide_mobile_background_image_position');
    		$('.vs_perview_slide .vs-slide').attr('style',slide_background_color+slide_background_image+background_image_position+tablet_background_image_position+mobile_background_image_position); 
 };
	   
 
  
	 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Perview Postion and Size
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	 
 $.fn.vs_layer_postion_size = function() { 
		 var key =$(this).attr('data-key');
   		  $(this).vs_horizontal_align(key,'layer','layer_align_horizontal','left');
   		  $(this).vs_vertical_align(key,'layer','layer_align_vertical','top');
		  
   		  $(this).vs_horizontal_align(key,'layer-tablet','layer_tablet_align_horizontal');
   		  $(this).vs_vertical_align(key,'layer-tablet','layer_tablet_align_vertical');
		
		$(this).vs_horizontal_align(key,'layer-mobile','layer_mobile_align_horizontal');
   		  $(this).vs_vertical_align(key,'layer-mobile','layer_mobile_align_vertical');
		
		
		var  horizontal = $(this).vs_size('lr-hor','layer_position_horizontal');
		 var  vertical = $(this).vs_size('lr-ver','layer_position_vertical'); 
		 
 		 var  width = $(this).vs_size('lr-wt','layer_size_width');
 		 var  height = $(this).vs_size('lr-ht','layer_size_height');
		 
		$('#vs_perview_layer_'+key).css(horizontal).css(vertical).css(width).css(height);		 
	
		 var tablet_display =  $(this).find('.vs_live_layer_tablet_display').find('input:checked').val();
 		if(tablet_display==='hide'){
			$('#vs_perview_layer_'+key).addClass('vs_tablet_hide');
 		}else{
			$('#vs_perview_layer_'+key).removeClass('vs_tablet_hide');
		}

			
 			var tablet_horizontal = $(this).vs_size('lr-tab-hor','layer_tablet_position_horizontal');
 			 var tablet_vertical = $(this).vs_size('lr-tab-ver','layer_tablet_position_vertical');
 			 var tablet_width = $(this).vs_size('lr-tab-wt','layer_tablet_size_width');
 		 	var tablet_height = $(this).vs_size('lr-tab-ht','layer_tablet_size_height');
			
			$('#vs_perview_layer_'+key).css(tablet_horizontal).css(tablet_vertical).css(tablet_width).css(tablet_height);		 
		
		var mobile_display =  $(this).find('.vs_live_layer_mobile_display').find('input:checked').val();
		if(mobile_display==='hide'){
			$('#vs_perview_layer_'+key).addClass('vs_mobile_hide');
  		}else{
			$('#vs_perview_layer_'+key).removeClass('vs_mobile_hide');
 		 }	
 			var mobile_horizontal = $(this).vs_size('lr-mob-hor','layer_mobile_position_horizontal');
 			 var mobile_vertical = $(this).vs_size('lr-mob-ver','layer_mobile_position_vertical');
 			 var mobile_width = $(this).vs_size('lr-mob-wt','layer_mobile_size_width');
 		 	var mobile_height = $(this).vs_size('lr-mob-ht','layer_mobile_size_height');
				$('#vs_perview_layer_'+key).css(mobile_horizontal).css(mobile_vertical).css(mobile_width).css(mobile_height);		 
		     
			
		
 
     
 };
	  
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Effect 
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	 
 $.fn.vs_layer_effectss = function() { 
		 var key =$(this).attr('data-key');
 
 		 var layer =$('.vs_perview_slide_content div.vs-slide:last-child #vs_perview_layer_'+key);
  
		 var layer_effect = $(this).find('.vs_live_layer_effect select').val() ;
		 var layer_initial = $(this).find('.vs_live_layer_initial select').val() ;
  		
		var effect_classs='';
		 if(layer_effect==='move'){
			if(layer_initial==='top'){
				effect_classs='vs-effect-move-top';
		 	}else if(layer_initial==='left'){
				effect_classs='vs-effect-move-'+left_right('left');
		 	}else if(layer_initial==='right'){
				effect_classs='vs-effect-move-'+left_right('right');
		 	}	else if(layer_initial==='bottom'){
				effect_classs='vs-effect-move-bottom';
		 	}
		 }else if(layer_effect==='scale'){
			effect_classs='vs-effect-scale';

 		 }else if(layer_effect==='fade'){
			effect_classs='vs-effect-fade';
  		 }
		layer.addClass(effect_classs);
		
		var layer_time_start = parseInt($(this).find('.vs_live_layer_time_start .vs_form_number').val());
		var layer_time_end = parseInt($(this).find('.vs_live_layer_time_end .vs_form_number').val());
		var layer_scale = parseInt($(this).find('.vs_live_layer_scale .vs_form_number').val());
		var css='';
	 
	 	if(isset(layer_effect)){
			if(isset(layer_scale)){
 				  css+="--vs-lr-ef-sc:"+layer_scale+";";
			}
 		  css+="--vs-lr-ef-st:"+layer_time_start+"ms;";
		  css+="--vs-lr-ef-en:"+(layer_time_end - layer_time_start)+"ms;";
		}


	layer.find('.vs-layer-style').append('<style>.vs-slide-item #vs_perview_layer_'+key+'{'+css+'}</style>');
			 
 			
 };
		  
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Shadow
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		
 $.fn.vs_layer_text_perview = function(items) { 
	 var key =$(this).attr('data-key');
	 var id =$(this).attr('data-id');
	 if(id==='text'){
  		//Text
		//
		var text_textarea = $(this).find('.vs_live_layer_text textarea').val() ;
		$('#vs_perview_layer_'+key+' .vs-text').html(text_textarea); 
		$('#vs_module_layer_'+key+' .vs_module_layer_name span').html(': '+text_textarea); 
		
 		 var text_align= $(this).vs_text_align('txt','layer_text_align');
 		 var tablet_text_align= $(this).vs_text_align('tab-txt','layer_tablet_text_align');
 		 var mobile_text_align= $(this).vs_text_align('mob-txt','layer_mobile_text_align');
  		var text_color= $(this).vs_color('txt','layer_text_color_first');
  
		var text_hover_color= $(this).vs_color('txt-hv','layer_text_hover_color_first');
  		var text_shadow= $(this).vs_text_shadow('txt','layer_text_shadow');
  		var text_hover_shadow= $(this).vs_text_shadow('txt-hv','layer_text_hover_shadow');
 

 		var icon = $(this).find('.vs_live_layer_icon input').val() ;
		$('#vs_perview_layer_'+key+' .vs-text i').remove();
		$('#vs_perview_layer_'+key+' .vs-text').prepend('<i class="'+icon+'"></i>');
	
		
  			
			var icon_align=$(this).vs_align('icn','layer_icon_align');
			 if(icon_align==='left'){
				$('#vs_perview_layer_'+key+' .vs-text').addClass('vs-icon-left');
 			 }else{
				$('#vs_perview_layer_'+key+' .vs-text').removeClass('vs-icon-left');
			 }
 
  		var  icon_color= $(this).vs_color('icn','layer_icon_color_first');
 		var  icon_hover_color= $(this).vs_color('icn-hv','layer_icon_hover_color_first');		
 
		var text_boxed = $(this).find('.vs_live_layer_text_boxed select').val() ;
 		 
		if(text_boxed==='boxed'){ 
		$('#vs_perview_layer_'+key+' .vs-text').addClass('vs-text-boxed').removeClass('vs-text-none');
		
			var text_boxed_padding= $(this).vs_padding('txt-box','layer_text_boxed_padding');
			var text_boxed_background= $(this).vs_background_gradient('txt-box-bg-cr','layer_text_boxed_background');
			var text_boxed_border= $(this).vs_border('txt-box','layer_text_boxed_border');
			var text_boxed_shadow= $(this).vs_shadow('txt-box','layer_text_boxed_shadow');
			
			var text_boxed_hover_background= $(this).vs_background_gradient('txt-box-hv-bg-cr','layer_text_boxed_hover_background');
  			var text_boxed_hover_border= $(this).vs_border('txt-box-hv','layer_text_boxed_hover_border');
			var text_boxed_hover_shadow= $(this).vs_shadow('txt-box-hv','layer_text_boxed_hover_shadow');			
		 	var text_boxed_radius= $(this).vs_radius('txt-box','layer_text_boxed_radius');
		}else{
					$('#vs_perview_layer_'+key+' .vs-text').addClass('vs-text-none').removeClass('vs-text-boxed');

			var text_boxed_padding='';
			var text_boxed_background='';
			var text_boxed_border='';
			var text_boxed_shadow='';
			
 			var text_boxed_hover_background='';
			var text_boxed_hover_border='';
			var text_boxed_hover_shadow='';
			var text_boxed_radius='';
			 
			//	var text_boxed_radius='';
			
		}
		 
  		var text_fontfamily= $(this).vs_fontfamily('txt','layer_text_fontfamily');
  		var text_font_size= $(this).vs_font_size('txt','layer_text_font_size');
  		var text_line_height= $(this).vs_line_height('txt','layer_text_line_height');
		
  		var tablet_text_font_size= $(this).vs_font_size('tab-txt','layer_tablet_text_font_size');
  		var tablet_text_line_height= $(this).vs_line_height('tab-txt','layer_tablet_text_line_height');		
		
  		var mobile_text_font_size= $(this).vs_font_size('mob-txt','layer_mobile_text_font_size');
  		var mobile_text_line_height= $(this).vs_line_height('mob-txt','layer_mobile_text_line_height');		
	
		
		
		
		
  		var text_font_weight= $(this).vs_font_weight('txt','layer_text_font_weight');
  		var text_font_decoration= $(this).vs_font_decoration('txt','layer_text_font_decoration');
  		var text_font_transform= $(this).vs_font_transform('txt','layer_text_font_transform');
  		var text_spacing= $(this).vs_spacing('txt','layer_text_spacing');
  		var text_font_style= $(this).vs_font_style('txt','layer_text_font_style');
		
  		var fontfamily= $(this).vs_input_fontfamily('layer_text');
		$(this).vs_link_fontfamily('layer_text',key);


		 $('#vs_perview_layer_'+key).find('.vs-layer-style').html('');
      			 $('#vs_perview_layer_'+key).find('.vs-layer-style').append('<style>#vs_perview_layer_'+key+'{'+
		 	text_align+
		 	tablet_text_align+
		 	mobile_text_align+
		 	text_color+
		 	text_hover_color+
			icon_color+
		 	icon_hover_color+		
			text_shadow+
		 	text_hover_shadow+
		 	text_boxed_padding+
			text_boxed_background+
			text_boxed_hover_background+
			text_boxed_border+
			text_boxed_hover_border+
			text_boxed_shadow+
			text_boxed_hover_shadow+
			text_boxed_radius+
			text_fontfamily+
		 	text_font_size+
			text_line_height+
		 	tablet_text_font_size+
			tablet_text_line_height+
		 	mobile_text_font_size+
			mobile_text_line_height+
		 	text_font_weight+
 			text_font_decoration+
			text_font_transform+
		 	text_font_style+
	 		text_spacing+
			'}'+fontfamily+'</style>'	
			 
 			
			) ;
	 }
		 
 };
 
			/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Shadow
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		
  $.fn.vs_layer_button_perview = function(items) { 
  
	 var key =$(this).attr('data-key');
	 var id =$(this).attr('data-id');
	 if(id==='button'){
		//Text
		//
		var button_textarea = $(this).find('.vs_live_layer_button textarea').val() ;
		$('#vs_perview_layer_'+key+' .vs-button').html(button_textarea); 
		$('#vs_module_layer_'+key+' .vs_module_layer_name span').html(': '+button_textarea); 
		 	 
		var button_align= $(this).vs_text_align('txt','layer_button_align');
 		 var tablet_button_align= $(this).vs_text_align('tab-txt','layer_tablet_button_align');
 		 var mobile_button_align= $(this).vs_text_align('mob-txt','layer_mobile_button_align');		
		
		
  		var button_color= $(this).vs_color('btn','layer_button_color_first');
  
		var button_hover_color= $(this).vs_color('btn-hv','layer_button_hover_color_first');
  		var button_shadow= $(this).vs_text_shadow('btn','layer_button_shadow');
  		var button_hover_shadow= $(this).vs_text_shadow('btn-hv','layer_button_hover_shadow');
 
 
 		var icon = $(this).find('.vs_live_layer_icon input').val() ;
		$('#vs_perview_layer_'+key+' .vs-button i').remove();
		$('#vs_perview_layer_'+key+' .vs-button').prepend('<i class="'+icon+'"></i>');
	
		
  			
			var icon_align=$(this).vs_align('icn','layer_icon_align');
			 if(icon_align==='left'){
				$('#vs_perview_layer_'+key+' .vs-button').addClass('vs-icon-left');
 			 }else{
				$('#vs_perview_layer_'+key+' .vs-button').removeClass('vs-icon-left');
			 }
 
  		var  icon_color= $(this).vs_color('icn','layer_icon_color_first');
 		var  icon_hover_color= $(this).vs_color('icn-hv','layer_icon_hover_color_first');		
 
		var button_boxed = $(this).find('.vs_live_layer_button_boxed select').val() ;
 		 
		if(button_boxed=='boxed'){ 
		$('#vs_perview_layer_'+key+' .vs-button').addClass('vs-button-boxed').removeClass('vs-button-none');
		
			var button_boxed_padding= $(this).vs_padding('btn-box','layer_button_boxed_padding');
			var button_boxed_background= $(this).vs_background_gradient('btn-box-bg-cr','layer_button_boxed_background');
			var button_boxed_border= $(this).vs_border('btn-box','layer_button_boxed_border');
			var button_boxed_shadow= $(this).vs_shadow('btn-box','layer_button_boxed_shadow');
			
			var button_boxed_hover_background= $(this).vs_background_gradient('btn-box-hv-bg-cr','layer_button_boxed_hover_background');
  			var button_boxed_hover_border= $(this).vs_border('btn-box-hv','layer_button_boxed_hover_border');
			var button_boxed_hover_shadow= $(this).vs_shadow('btn-box-hv','layer_button_boxed_hover_shadow');			
		 	var button_boxed_radius= $(this).vs_radius('btn-box','layer_button_boxed_radius');
		}else{
					$('#vs_perview_layer_'+key+' .vs-button').addClass('vs-button-none').removeClass('vs-button-boxed');

			var button_boxed_padding='';
			var button_boxed_background='';
			var button_boxed_border='';
			var button_boxed_shadow='';
			
 			var button_boxed_hover_background='';
			var button_boxed_hover_border='';
			var button_boxed_hover_shadow='';
			var button_boxed_radius='';
			 
			//	var button_boxed_radius='';
			
		}
		 
  		var button_fontfamily= $(this).vs_fontfamily('btn','layer_button_fontfamily');
  		var button_font_size= $(this).vs_font_size('btn','layer_button_font_size');
 		
  		var tablet_button_font_size= $(this).vs_font_size('tab-btn','layer_tablet_button_font_size');
   		var mobile_button_font_size= $(this).vs_font_size('mob-btn','layer_mobile_button_font_size');
	
		
		
		
		
  		var button_font_weight= $(this).vs_font_weight('btn','layer_button_font_weight');
  		var button_font_decoration= $(this).vs_font_decoration('btn','layer_button_font_decoration');
  		var button_font_transform= $(this).vs_font_transform('btn','layer_button_font_transform');
  		var button_spacing= $(this).vs_spacing('btn','layer_button_spacing');
  		var button_font_style= $(this).vs_font_style('btn','layer_button_font_style');
		
  		var fontfamily= $(this).vs_input_fontfamily('layer_button');
		 	$('body').vs_link_fontfamily(key);

      		 $('#vs_perview_layer_'+key).find('.vs-layer-style').html('<style>#vs_perview_layer_'+key+'{'+
		 	button_align+
		 	tablet_button_align+
		 	mobile_button_align+
		 	button_color+
		 	button_hover_color+
						icon_color+
		 	icon_hover_color+		
			button_shadow+
		 	button_hover_shadow+
		 	button_boxed_padding+
			button_boxed_background+
			button_boxed_hover_background+
			button_boxed_border+
			button_boxed_hover_border+
			button_boxed_shadow+
			button_boxed_hover_shadow+
			button_boxed_radius+
			button_fontfamily+
		 	button_font_size+
		 	tablet_button_font_size+
		 	mobile_button_font_size+
		
		 	button_font_weight+
 			button_font_decoration+
			button_font_transform+
		 	button_font_style+
	 		button_spacing+
			'}'+fontfamily+'</style>'	
			 
 			
			) ; 
	 }
 };
		  /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Shadow
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		
 $.fn.vs_layer_icon_perview = function(items) { 
	 var key =$(this).attr('data-key');
	 var id =$(this).attr('data-id');
	 if(id==='icon'){	
	 	var icon = $(this).find('.vs_live_layer_icon input').val() ;
		$('#vs_perview_layer_'+key+' .vs-icon i').remove();
		$('#vs_perview_layer_'+key+' .vs-icon').append('<i class="'+icon+'"></i>');
		$('#vs_module_layer_'+key+' .vs_module_layer_name span').html('');
		$('#vs_module_layer_'+key+' .vs_module_layer_name span').append(': <i class="'+icon+'"></i>'); 
 	 
  	  

 		var icon_align= $(this).vs_text_align('txt','layer_icon_align');
		var tablet_icon_align= $(this).vs_text_align('tab-txt','layer_tablet_icon_align');
		var mobile_icon_align= $(this).vs_text_align('mob-txt','layer_mobile_icon_align');		
				
		
  		var icon_color= $(this).vs_color('icn','layer_icon_color_first');
  

		var icon_hover_color= $(this).vs_color('icn-hv','layer_icon_hover_color_first');
  		var icon_shadow= $(this).vs_text_shadow('icn','layer_icon_shadow');
  		var icon_hover_shadow= $(this).vs_text_shadow('icn-hv','layer_icon_hover_shadow');
 
 
		var icon_boxed = $(this).find('.vs_live_layer_icon_boxed select').val() ;
 		 
		if(icon_boxed=='boxed'){ 
		$('#vs_perview_layer_'+key+' .vs-icon').addClass('vs-icon-boxed').removeClass('vs-icon-none');
			var icon_boxed_padding= $(this).vs_padding('icn-box','layer_icon_boxed_padding');
			var icon_boxed_background= $(this).vs_background_gradient('icn-box-bg-cr','layer_icon_boxed_background');
			var icon_boxed_border= $(this).vs_border('icn-box','layer_icon_boxed_border');
			var icon_boxed_shadow= $(this).vs_shadow('icn-box','layer_icon_boxed_shadow');
			
			var icon_boxed_hover_background= $(this).vs_background_gradient('icn-box-hv-bg-cr','layer_icon_boxed_hover_background');
  			var icon_boxed_hover_border= $(this).vs_border('icn-box-hv','layer_icon_boxed_hover_border');
			var icon_boxed_hover_shadow= $(this).vs_shadow('icn-box-hv','layer_icon_boxed_hover_shadow');			
		 	var icon_boxed_radius= $(this).vs_radius('icn-box','layer_icon_boxed_radius');
		}else{
					$('#vs_perview_layer_'+key+' .vs-icon').addClass('vs-boxed-none').removeClass('vs-boxed-none');

			var icon_boxed_padding='';
			var icon_boxed_background='';
			var icon_boxed_border='';
			var icon_boxed_shadow='';
			
 			var icon_boxed_hover_background='';
			var icon_boxed_hover_border='';
			var icon_boxed_hover_shadow='';
			var icon_boxed_radius='';
			 
			//	var icon_boxed_radius='';
			
		}
		 
		
   		var icon_font_size= $(this).vs_font_size('icn','layer_icon_font_size');
		var tablet_icon_font_size= $(this).vs_font_size('tab-icn','layer_tablet_icon_font_size');
   		var mobile_icon_font_size= $(this).vs_font_size('mob-icn','layer_mobile_icon_font_size');
 				 
		       		 $('#vs_perview_layer_'+key).find('.vs-layer-style').html('<style>#vs_perview_layer_'+key+'{'+

		 	icon_align+
		 	tablet_icon_align+
		 	mobile_icon_align+
		 	icon_color+
		 	
			icon_hover_color+
			icon_shadow+
		 	icon_hover_shadow+
			 
		 	icon_boxed_padding+
			icon_boxed_background+
			icon_boxed_hover_background+
			icon_boxed_border+
			icon_boxed_hover_border+
			icon_boxed_shadow+
			icon_boxed_hover_shadow+
			icon_boxed_radius+
			
 		 	icon_font_size+
 		 	tablet_icon_font_size+
 		 	mobile_icon_font_size+'}'
		 	
 			 
 			
			) ;
					 }}
					 
					 
					 
					 
 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Shadow
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/		
 $.fn.vs_layer_box_perview = function(items) { 
	 var key =$(this).attr('data-key');
	 var id =$(this).attr('data-id');
	 if(id==='box'){	
 			var box_background= $(this).vs_background_gradient('box-bg-cr','layer_box_background');
			var box_border= $(this).vs_border('box','layer_box_border');
			var box_shadow= $(this).vs_shadow('box','layer_box_shadow');
			
			var box_hover_background= $(this).vs_background_gradient('box-hv-bg-cr','layer_box_hover_background');
  			var box_hover_border= $(this).vs_border('box-hv','layer_box_hover_border');
			var box_hover_shadow= $(this).vs_shadow('box-hv','layer_box_hover_shadow');			
		 	var box_radius= $(this).vs_radius('icn-box','layer_box_radius');
	 
				 
		       		 $('#vs_perview_layer_'+key).find('.vs-layer-style').html('<style>#vs_perview_layer_'+key+'{'+

		  
			 
 			box_background+
			box_hover_background+
			box_border+
			box_hover_border+
			box_shadow+
			box_hover_shadow+
			box_radius+
			'}'
		 	
 			 
 			
			) ;
	 }
 };
			
 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Shadow
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	 
   $.fn.vs_layer_image_perview = function(items) { 
	 var key =$(this).attr('data-key');
 var id =$(this).attr('data-id');
	 if(id==='image'){	
		//Text
		//
		var image_url = $(this).find('.vs_live_layer_image .vs_image_item img').attr('src');
 
 		if(isset(image_url) && image_url !=='undefined'){
  		$('#vs_perview_layer_'+key+' .vs-image').html('');
  		$('#vs_perview_layer_'+key+' .vs-image').append('<img src="'+image_url+'">');
	$('#vs_module_layer_'+key+' .vs_module_layer_name span').html('');
		$('#vs_module_layer_'+key+' .vs_module_layer_name span').append(': <img src="'+image_url+'">'); 		
		
		}else{
  		$('#vs_perview_layer_'+key+' .vs-image').html('');
		}
 		var image_align= $(this).vs_text_align('txt','layer_image_align');
 		var tablet_image_align= $(this).vs_text_align('tab-txt','layer_tablet_image_align');
 		var mobile_image_align= $(this).vs_text_align('mob-txt','layer_mobile_image_align');
	$('#vs_perview_layer_'+key).find('.vs-layer-style').html('<style>#vs_perview_layer_'+key+'{'+

	image_align+tablet_image_align+mobile_image_align+'}') ;

	 }
 };
 


})(jQuery);