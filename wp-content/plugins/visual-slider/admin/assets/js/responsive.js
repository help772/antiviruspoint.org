(function ($, undefined) {
	  'use strict';
	  function isset(variable){
		return  variable  !== "undefined" && variable  !== "0" && variable !== null && variable !== '';
	} 

 
 
    /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Global Perview size
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
  

  $.fn.vs_perview_size = function() { 
 				var this_element = $('.vs-custom-slider');
				if($('.vs-custom-slider').hasClass('vs-visualslider')){
 					$('.vs_perview_global').removeClass('vs-active-loading vs-loading');
					
 				var data_slider = jQuery.parseJSON( this_element.find('.vs-slide-options').html());		 
 				var width= this_element.find('.vs-slide-list-warp').width();
				var desktop_width= data_slider['desktop_width'];
				var tablet_width= data_slider['tablet_width'];
				var mobile_width= data_slider['mobile_width'];
 			 
 			 	var content_width = $('.vs_perview_global_content').width();
 				
  				if (   (content_width <= 1024 && content_width >= 768) ){
 					
 							this_element.addClass('vs-in-tablet').removeClass('vs-in-mobile vs-in-desktop');
						if(this_element.hasClass('vs-has-tablet') && this_element.hasClass('vs-full-width')){
							if( this_element.hasClass('vs-has-tablet') && (width < tablet_width)  ){
									this_element.addClass('vs-has-scale').removeClass('vs-not-scale');
							}else{
								this_element.addClass('vs-not-scale').removeClass('vs-has-scale');
							}
						}else{
								this_element.addClass('vs-has-scale').removeClass('vs-not-scale');
						}
 
 	
				 }else if (  content_width <= 767 &&  content_width >= 1 ){
 					
						this_element.addClass('vs-in-mobile').removeClass('vs-in-desktop vs-in-tablet');
						if(this_element.hasClass('vs-has-mobile') && this_element.hasClass('vs-full-width') ){
							if((width < mobile_width)){
									this_element.addClass('vs-has-scale').removeClass('vs-not-scale');
							}else{
								this_element.addClass('vs-has-scale').removeClass('vs-has-scale');
							}
						}else{
								this_element.addClass('vs-has-scale').removeClass('vs-not-scale');
						}
 						
				 } else{
 
						this_element.removeClass('vs-in-mobile vs-in-desktop vs-in-tablet').addClass('vs-in-desktop');
						if(width < desktop_width ){
								this_element.removeClass('vs-not-scale').addClass('vs-has-scale');
						}else{
							this_element.removeClass('vs-has-scale').addClass('vs-not-scale');
						}
						
				 }   
		  	 
	
 
  				}
     
 };
 	
   /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Perview Global Click
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	

   $.fn.vs_perview_global_click = function(responsive) { 
	$(document).on('click', ".vs_perview_global .vs_perview_heading_"+responsive+",.vs_perview_global .vs_perview_heading_"+responsive+" label,.vs_perview_global .vs_perview_heading_"+responsive+" option", function(e) {
		$('.vs_perview_global').removeClass('vs_perview_tablet vs_perview_desktop vs_perview_mobile'); 
		$('.vs_perview_global').addClass('vs_perview_'+responsive); 
 		var responsive_width = $('.vs_perview_global #vs_perview_'+responsive+'_width').val();
		
	$('.vs_module_setting').removeClass('vs_global_mobile vs_global_tablet vs_global_desktop'); 
	$('.vs_module_setting').addClass('vs_global_'+responsive); 		
 		$('.vs_perview_global').attr('style','--vs-prw-wt:'+responsive_width+';'); 
 		$('body').vs_reload_global_slider();
    });	
    			 
}; 
 
    /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Slide Perview size
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	 
  $.fn.vs_perview_slide_size = function() { 
 			var json = $('.vs-custom-slider');
 			var this_element = $('.vs_perview_slide .vs-slide-warp'); 
 				var data_slider = jQuery.parseJSON( json.find('.vs-slide-options').html());		 
 
  				var width= this_element.find('.vs-slide').width();
				var desktop_width= data_slider['desktop_width'];
				var tablet_width= data_slider['tablet_width'];
				var mobile_width= data_slider['mobile_width'];
 			 	var content_width = $('.vs_perview_slide_content').width();
   				if (   (content_width <= 1024 && content_width >= 768) ){
						if(this_element.hasClass('vs-has-tablet') ){
 						if(    (width < tablet_width)  ){
								this_element.removeClass('vs-not-scale').addClass('vs-has-scale');
						}else{
							this_element.removeClass('vs-has-scale').addClass('vs-not-scale');
						}
						}else{
							this_element.removeClass('vs-not-scale').addClass('vs-has-scale');
						}
 	
				 }else if ( (content_width <= 767 &&  content_width >= 1) ){
 						if(this_element.hasClass('vs-has-mobile') ){
						if(   (width < mobile_width)  ){
								this_element.removeClass('vs-not-scale').addClass('vs-has-scale');
						}else{
							this_element.removeClass('vs-has-scale').addClass('vs-not-scale');
						}
						}else{
							this_element.removeClass('vs-not-scale').addClass('vs-has-scale');
 						}
				 } else{
   						if(width < desktop_width ){
								this_element.removeClass('vs-not-scale').addClass('vs-has-scale');
						}else{
							this_element.removeClass('vs-has-scale').addClass('vs-not-scale');
						}
						
				 } 
 
};
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Slide Responsive
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
  $.fn.vs_slide_responsive = function(this_element) { 

   			var width_warp= this_element.find('.vs-slide-list-warp').width();
   			return " --vs-sc-wt:"+width_warp+";";

};
   	
   /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Perview Slide Click
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	

$.fn.vs_perview_slide_click = function(responsive) { 
 $(document).on('click', ".vs_perview_slide .vs_perview_heading_"+responsive, function() {
    $('.vs_perview_slide').removeClass('vs_perview_mobile vs_perview_tablet  vs_perview_desktop'); 
	$('.vs_perview_slide').addClass('vs_perview_'+responsive+''); 
 	var responsvie_width = $('.vs_perview_slide #vs_perview_'+responsive+'_width').val();
	$('.vs_perview_slide').css({'--vs-prw-wt':responsvie_width}); 
	
    $('.vs_perview_slide .vs-slide-warp').removeClass('vs-in-mobile vs-in-tablet  vs-in-desktop'); 
	$('.vs_perview_slide .vs-slide-warp').addClass('vs-in-'+responsive+''); 
	$('.vs_panel').removeClass('vs_panel_mobile vs_panel_tablet vs_panel_desktop'); 
	$('.vs_panel').addClass('vs_panel_'+responsive); 
	$('body').vs_perview_slide_size();
   		var style = $('body').vs_slide_responsive($('.vs_perview_full_screen'));
		$('body').find('.vs-slide-warp').attr('style',style);	 
		 
   });
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
			$('body').vs_perview_size();

/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Full SCREEN
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	

$(document).on('click','.vs_perview_slide .vs_perview_full_screen_mode',function() {
		$('.vs_panel').removeClass('vs_panel_mobile vs_panel_tablet vs_panel_desktop'); 
 		$(this).parents('.vs_perview_slide').addClass('vs_perview_full_screen');	
		$('.vs_module_side_options_active').removeClass('vs_module_side_options_active');
		$('.vs_panel_options_active').removeClass('vs_panel_options_active');
		$('body').vs_perview_slide_size();
    		var style = $('body').vs_slide_responsive($('.vs_perview_full_screen'));
		$('body').find('.vs-slide-warp').attr('style',style);	 
});
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Full SCREEN Close
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	

$(document).on('click','.vs_perview_slide .vs_perview_full_screen_close',function() {
	$(this).parents('.vs_perview_slide').removeClass('vs_perview_full_screen');
	$( ".vs_perview_slide .vs_perview_heading_desktop" ).trigger( "click" );

});
 
$(document).on('click','.vs_perview_global .vs_perview_full_screen_mode',function() {
		$(this).parents('.vs_perview_global').addClass('vs_perview_full_screen');
		$('body').vs_reload_global_slider();
 });

$(document).on('click','.vs_perview_global .vs_perview_full_screen_close',function() {
 
$(this).parents('.vs_perview_global').removeClass('vs_perview_full_screen');
			$('body').vs_reload_global_slider();

});


/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Responsive_options
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
		$(document).on('click', '.vs_module_setting  .vs_responsive_options_desktop', function(e) {
		$( ".vs_perview_global  .vs_perview_heading_desktop" ).trigger( "click" );
 	});	 
  	 
   
	$(document).on('click', '.vs_module_setting  .vs_responsive_options_tablet', function(e) {
 	 $( ".vs_perview_global  .vs_perview_heading_tablet" ).trigger( "click" );
 	});	 
  	
 	$(document).on('click', '.vs_module_setting  .vs_responsive_options_mobile', function(e) {
  $( ".vs_perview_global  .vs_perview_heading_mobile" ).trigger( "click" );
 	});	
	
	$(document).on('click', '.vs_panel .vs_responsive_options_desktop', function(e) {
		$( ".vs_panel .vs_perview_heading_desktop" ).trigger( "click" );
 	});	 
  	 
   
	$(document).on('click', '.vs_panel .vs_responsive_options_tablet', function(e) {
 	 $( ".vs_panel .vs_perview_heading_tablet" ).trigger( "click" );
 	});	 
  	
 	$(document).on('click', '.vs_panel .vs_responsive_options_mobile', function(e) {
  $( ".vs_panel .vs_perview_heading_mobile" ).trigger( "click" );
 	});		
	
 		
	 
  
$('body').vs_perview_global_click('desktop');
$('body').vs_perview_global_click('tablet');
$('body').vs_perview_global_click('mobile');
 
    
$('body').vs_perview_slide_click('desktop');
$('body').vs_perview_slide_click('tablet');
$('body').vs_perview_slide_click('mobile');   

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
});  
	  
})(jQuery);
