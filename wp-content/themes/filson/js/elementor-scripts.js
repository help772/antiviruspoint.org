"use strict";
(function($){
 $.fn.hexwp_elementor = function() {
	 var this_elementor =$(this);
 
	/***********************************************************************************************************************************************************
	Auto Width
 	***********************************************************************************************************************************************************/
	if(jQuery().hexwp_header){
   		this_elementor.hexwp_header();
		
		$(window).on('resize',function () { 
		this_elementor.hexwp_header();
  		});	
	}	
	
	if(jQuery().hexwp_auto_width){
   		this_elementor.hexwp_auto_width();
		
		$(window).on('resize',function () { 
		this_elementor.hexwp_auto_width();
  		});	
	}		
	if(jQuery().hexwp_countdown){
   		this_elementor.hexwp_countdown();
	}
	if(jQuery().hexwp_cart_button){
   		this_elementor.hexwp_cart_button();
	}
	
	  
	/***********************************************************************************************************************************************************
	Tabs Ajax
 	***********************************************************************************************************************************************************/
	if(jQuery().hexwp_slider){
   		$('body').hexwp_slider();
	}
	if(jQuery().hexwp_masonry){
   		this_elementor.hexwp_masonry();
		$(window).on('resize',function () { 
		this_elementor.hexwp_masonry();
  		});			
	}
	
	if(jQuery().hexwp_counter){
   		this_elementor.hexwp_counter();
	}		
		 
 
	 	
	
	
   
 };
 })(jQuery); 
