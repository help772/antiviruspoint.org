<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Call Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
 
add_filter('vh_element_options', 'hexwp_text_html_options');
function hexwp_text_html_options($element) {
	
	$element['text_html'] = array(
							'name'			=> 	__('Text,Html,Sortcode','hexwp'),
							'id'			=> 'text_html',
							'img'			=>  hexwp_DIR.'/admin/assets/images/header/text_html.jpg'
			); 
				 
				 
				
				
	$option[] = array(			"name" 			=> esc_html__( 'input Text in Text,Html And Shortcode', 'hexwp' ),
 								"id" 			=> "text_html_textarea",
   								"type" 			=> "textarea",
  				);
				 
	$option[] = array(			"name" 			=> esc_html__( 'Url Link', 'hexwp' ),
 								"id" 			=> "text_html_link",
 								"placeholder" 			=> "https://",
    								"type" 			=> "text",
  				);
				 
 
 	$option[] = array(			"name" 			=> __('Boxed Layout', 'hexwp' ),
								"id" 			=> "boxed_layout",
   								"type" 			=> "radio",
    							"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);		
 
 				
	$option[]= array(			"name"			=> esc_html__('Background Color','hexwp'),
								"id"			=> "background_color",	
								"type"			=> "multi_options",
 								"fold" 			=> array('boxed'  => 'boxed_layout'),
								"group"			=> __('Style','hexwp'),
 								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('First Background','hexwp'),
																	"id"			=> "background",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Second Background','hexwp'),			
																	"id"			=> "background_2",
																	"type"			=> "color_rgba",
															),
													),	
				); 		
				
				
				
					
	$option[]= array(			"name"				=> __('Text Color','hexwp'),
								"id"			=> "text_color",
    							 "type"			=> "multi_options",
								"group"			=> __('Style','hexwp'),
 								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('Text Color','hexwp'),
																	"id"			=> "text",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Hover Color','hexwp'),			
																	"id"			=> "hover",
																	"type"			=> "color_rgba",
															),
													),							
  				 	);
					 		
	$option[]= array(			"name"			=> esc_html__('Border Radius','hexwp'),
								"id"			=> "border_radius",	
								"type"			=> "select",
  								"fold" 			=> array('boxed'  => 'boxed_layout'),
								"group"			=> __('Style','hexwp'),
								"options" 		=> hexwp_array_options('radius',true),
	
				); 		
				
				
 
					
	$option[] = array(			"name" 			=> esc_html__( 'Font Size', 'hexwp' ),
								"id" 			=> "font_size",
								"type" 			=> "number",
								"min" 			=> "10",
								"max" 			=> "40",
								"step" 			=> "1",
								"unit" 			=> "px",
								"group"			=> __('Typography','hexwp'),
								"desc" 			=> "Determine The Header Height Value In Pixels.",
  				);
	
	$option[] = array(			"name" 			=> esc_html__( 'Font Weight', 'hexwp' ),
								"id" 			=> "font_weight",
								"type" 			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 			=> hexwp_array_options('font_weight'),
  				);
 	
				
	$option[]=array( 			"name"			=> esc_html__('Text Transform','hexwp'),
								"id"			=> "text_transform",
								"type"			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options"		=>  array( 
															''				=>	__('Default','hexwp'),
															"none"			=> 	__('None','hexwp'),
															"uppercase"			=> 	__('Uppercase','hexwp'),
															"lowercase"			=> __('Lowercase','hexwp'),
															"capitalize"			=> __('Capitalize','hexwp'),
													) ,	
				);
				 
				

	$element['text_html']['options']=$option;

    return $element;
	
}
