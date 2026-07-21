(function($){
 "use strict";
		
	$.fn.vs_custom_slider = function() {
	 var this_is =$(this);
 
 	 	 
 		function vs_slide_responsive(this_element){ 
			var width_warp= this_element.find('.vs-slide-list-warp').width();
   			return " --vs-sc-wt:"+width_warp+";";
		 
  		}
 		function vs_slide_auto(this_element,data_slider){ 
		if(!$('body').hasClass('wp-admin')){
				var width= this_element.find('.vs-slide-list-warp').width();
				var desktop_width= data_slider['desktop_width'];
				var tablet_width= data_slider['tablet_width'];
				var mobile_width= data_slider['mobile_width'];
 			 
				if (   window.matchMedia('(max-width: 1024px) and (min-width: 768px)').matches){
 						this_element.addClass('vs-in-tablet').removeClass('vs-in-mobile vs-in-desktop');
						 
								this_element.addClass('vs-has-scale').removeClass('vs-not-scale');
						 
 
				 }else if (  window.matchMedia('(max-width: 767px) and (min-width: 1px)').matches){
 
						this_element.addClass('vs-in-mobile').removeClass('vs-in-desktop vs-in-tablet');
				 
								this_element.addClass('vs-has-scale').removeClass('vs-not-scale');
					 
 						
				 } else{
						this_element.addClass('vs-in-desktop').removeClass('vs-in-mobile vs-in-tablet');
						if(width < desktop_width ){
								this_element.addClass('vs-has-scale').removeClass('vs-not-scale');
						}else{
							this_element.addClass('vs-not-scale').removeClass('vs-has-scale');
						}
						
				 }
				
		}
				
		}
		
		
		
		
		
		
		
		
		
		
		
		
   $(this).find('.vs-custom-slider').each(function(index, block) {	 
			var this_element = $(this);
 			var data_slider = jQuery.parseJSON( $(this).find('.vs-slide-options').html());
			vs_slide_auto(this_element,data_slider);
			var style='';
			 style+= vs_slide_responsive(this_element);
			this_element.find('.vs-slide-list-warp').attr('style',style);
					 
 			data_slider['onBeforeSlide']= function ($el, scene) {
  				$el.find('.vs-slide-item').each(function(index, block) {
 					this_element.removeClass('vs-not-transition');
 				});
			};
			 

 		 
		 
			data_slider['onBeforeNextSlide']= function ($el, scene) {
 							this_element.removeClass('vs-not-transition');
   
			}; 
			data_slider['onBeforePrevSlide']= function ($el, scene) {
				this_element.removeClass('vs-not-transition');
   			}; 
 			if(this_element.hasClass('vs-slider')){
 			this_element.find('.vs-slide-list').vs_lightSlider(data_slider);
			}
       	  	$(window).on('resize orientationchange', function () {
			   this_element.addClass('vs-not-transition');
			 });
		 
			
 			setTimeout(function(){  
 
			vs_slide_auto(this_element,data_slider);
			var style='';
		 	style+= vs_slide_responsive(this_element);
			this_element.find('.vs-slide-list-warp').attr('style',style);
			 
			$(window).resize(function () { 
			
				vs_slide_auto(this_element,data_slider);
				var style='';
		 		style+= vs_slide_responsive(this_element);

				this_element.find('.vs-slide-list-warp').attr('style',style);
	 
 			});
			},50);
	 
 		
 			
	});
  
};
	 
 })(jQuery);
 
 jQuery(function($) {
	'use strict'; 
	jQuery(document).ready(function() {
		  	if(jQuery().vs_slider){
   			 $('body').vs_slider();
			}
		$('body').vs_custom_slider();
 
  window.addEventListener('message', function(event) {
			if (event.data.massage === 'triggerChange') {
 					 $(event.data.element).vs_custom_slider();
			}
 			}, false);
	 
 	});
 }); 