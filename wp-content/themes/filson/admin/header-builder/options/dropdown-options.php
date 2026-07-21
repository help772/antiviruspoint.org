<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Dropdown Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
add_filter('vh_global_options', 'hexwp_dropdown_options');
function hexwp_dropdown_options($global) {
 	
	$global['dropdown'] = array(
							'name'			=> 	__('Dropdown Options','hexwp'),
 							'img'			=>   hexwp_DIR.'/admin/assets/images/header/icon/dropdown.png'
  	); 
 
	//************************************************ Dropdown ***********************************************/
  									
	$option[] = array(			"name" 			=> __('Dropdown Background Color' , 'hexwp'),
								"id" 			=> "background_color",
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					);
						
	$option[] = array( 			"name" 			=> __('Dropdown Link Color' , 'hexwp'),
								"id" 			=> "link_color",
								"type"			=> "multi_options",
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('Text Link Color','hexwp'),
																	"id"			=> "link",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Hover Color','hexwp'),			
																	"id"			=> "hover",
																	"type"			=> "color_rgba",
															),
													),
					);			
							
							
	$option[] = array(			"name" 			=> __('Dropdown Text color' , 'hexwp'),
								"id" 			=> "text_color",
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					);  
						
	 
	$option[] = array(			"name" 			=> __('Dropdown Highlight color' , 'hexwp'),
								"id" 			=> "highlight_color",
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					); 	
						
						
	$option[] = array( 			"name"			=>	__('Dropdown Grey field color' , 'hexwp'),
								"id"			=> "grey_color",
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"type"			=> "color_rgba"
					);
											
						
	$option[] = array( 			"name" 			=> __('Dropdown Border color' , 'hexwp'),
								"id" 			=> "border_color",
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					);   
						
						 
	$option[] = array(			"name" 			=> __('Dropdown Cover Border' , 'hexwp'),
								"id" 			=> "border",
								"type"			=> "multi_options",
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"options"		=> hexwp_multi_array_options('border_mini'),
					); 	
								
											
	$option[] = array(			"name" 			=> __('Dropdown Cover Shadow' , 'hexwp'),
								"id" 			=> "shadow",
								"type"			=> "multi_options",
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"options"		=> hexwp_multi_array_options('shadow_mini'),
					); 	
		
	
	$option[] = array( 			"name" 			=> __('Dropdown Border Radius' , 'hexwp'),
								"id" 			=> "radius",
								"type" 			=> "multi_options",
  								"group"			=> __( 'Dropdown Style', 'hexwp' ),
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"options"		=> hexwp_multi_array_options('radius_mini'),
 					); 	
					
	$option[] = array( 			 "name" 			=> esc_html__( 'Font Size', 'hexwp' ),
								"id" 			=> "font_size",
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"type" 			=> "number",
								"min" 			=> "10",
								"max" 			=> "40",
								"step" 			=> "1",
								"unit" 			=> "px",
								"group"			=> __('Typography','hexwp'),
   				);
	
	$option[] = array( 			 "name" 			=> esc_html__( 'Font Weight', 'hexwp' ),
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
		
	$global['dropdown']['options']=$option;
     return $global;
} 
 
?>