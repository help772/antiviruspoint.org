<?php
 			 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Image And Caption Style

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "caption_style_start",	
						"type"		=> "content"
				);
$of_options[]= array( 	"name"			=>	esc_html__('Image And Caption Style','hexwp'),
						"type"			=> "title",						
				); 	
				
				
$of_options[]=	array( 	"name"			=> __('Hover Image Effect','hexwp'),
						"id"			=> "image_effect",
						"type"			=> "select",
						"options"		=>  hexwp_array_options('hover_image'),						
				);	  	
				
  	
$of_options[]= array(	"name"			=> __('Caption Background Effect','hexwp'),
						"id"			=> "caption_effect",
						"type"			=> "select",
						"options"		=>	hexwp_array_options('caption_effect'),		
				);
  	
	
$of_options[]= array( "name"			=> esc_html__('Caption Background Color','hexwp'),
					"id"			=> "caption_background_color", 
  					"type"			=> "color_rgba", 
		
  				); 		
	
	
$of_options[]= array(	"name"			=> esc_html__('Caption Color','hexwp'),
 						"id"			=> "caption_color", 
						"type"			=> "color", 
   				);
	
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);			
