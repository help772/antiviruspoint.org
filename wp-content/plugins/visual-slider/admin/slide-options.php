<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( ! function_exists( 'vs_slide_options' ) ) {
 add_filter('vs_slide_options', 'vs_slide_options');
function vs_slide_options( $option ) {
	$option = array();
	 
 	$option[]= array( 
		"name"			=> __('Slide Title','visual-slider'),
 		"id"			=> "title",
	 	"width"			=> "100%",
     		"type"			=> "text",
 	);
	
 	$option[]= array( 
		"name"			=> __('Link','visual-slider'),
 		"id"			=> "link",
 	 	"width"			=> "100%",
 	 	"warp_width"			=> "100%",
 		 
 		"placeholder"	=> "http://",
 		"type"			=> "text",
 	);	
		   
	$option[]= array( 
		"name"			=> __('Background color','visual-slider'),
 		"id"			=> "background_color",
 		"group"			=>  __('Background','visual-slider'),
		"type"			=> "multi_options",
		"options"		=> array(
			array(
 				"id"		=> "first",
				"type"		=> "color_rgba",
			),
			array(
 				"id"		=> "second",
				"type"		=> "color_rgba",
			),
			array(
 				"id"		=> "third",
				"type"		=> "color_rgba",
 			),	
			array(
 						"id"		=> "orientation",
						"type"		=> "select",
						"options"	=> array(
							"horizontal"		=>  esc_html__('→','visual-slider'),
							"vertical"			=>  esc_html__('↓','visual-slider'),
							"diagonal"			=>  esc_html__('↘','visual-slider'),
							"diagonal-bottom"	=>  esc_html__('↗','visual-slider'),
							"radial"			=>  esc_html__('○','visual-slider'),

  						),				
  						),				
	
		),
 	);		
	
	 
	$option[]= array( 
		"name"			=> __('Background Image','visual-slider'),
 		"id"			=> "background_image",
	 	"width"			=> "100%",
	
 		"group"			=>  __('Background','visual-slider'),
  		"type"			=> "image",
 	);	
	
   
	$option[]= array( 
			"responsive"	=> "desktop",
		"name"			=> __('Background Image Position','visual-slider'),
 		"id"			=> "background_image_position",
 		"group"			=>  __('Background','visual-slider'),
		"type"			=> "select",
 		"options"		=> vs_array_options('image_position')
 	);
 	 
	 
	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_tablet',  FILTER_VALIDATE_BOOLEAN )))){

		$option[]= array( 
			"responsive"	=> "tablet",
			"name"			=> __('Responsive on Tablet','visual-slider'),
			"group"			=>  __('Background','visual-slider'),
			"id"			=>  "tablet_background_heading",
			"type"			=> "heading",
		);
		$option[]= array( 
			"responsive"	=> "tablet",
			"name"			=> __('Background Image Position on Tablet','visual-slider'),
			"id"			=> "tablet_background_image_position",
			"group"			=>  __('Background','visual-slider'),
			"type"			=> "select",
			"options"		=> vs_array_options('image_position')
		);
		
  
 
	}
	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){
 
		$option[]= array( 
			"responsive"	=> "mobile",
			"name"			=> __('Responsive on Mobile','visual-slider'),
			"group"			=>  __('Background','visual-slider'),
			"id"			=>  "mobile_background_heading",
			"type"			=> "heading",
		);
		$option[]= array( 
				"responsive"	=> "mobile",
			"name"			=> __('Background Image Position on Mobile','visual-slider'),
			"id"			=> "mobile_background_image_position",
			"group"			=>  __('Background','visual-slider'),
			"type"			=> "select",
			"options"		=> vs_array_options('image_position')
		);
		
 
		}
 
	
 
  			
 
    return $option;
} 
} 