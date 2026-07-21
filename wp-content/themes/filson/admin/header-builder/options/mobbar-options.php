<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Mobbar Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_global_options', 'hexwp_mobbar_options');
function hexwp_mobbar_options($global) {
 	
	$global['mobbar'] = array(
							'name'			=> 	__('Mobile Bar Options','hexwp'),
							'img'			=>  hexwp_DIR.'/admin/assets/images/header/icon/mobbar.png'
  	); 

	
	$option[] = array(			"name"				=>__('Show Social Icon','hexwp'),
								"id"				=> "social",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);			
	
	$option[] = array(			"name"				=>__('Show Contact Us','hexwp'),
								"id"				=> "contact_us",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);		
				 
	$option[] = array( 			"name" 			=> __( 'input Text In Contact Us', 'hexwp' ),
								"fold"			=>  array(
															'show'	=>	'contact_us',
													),	
	 							"id" 			=> "contact_us_textarea",
								"default" 		=>__( 'example@email.com', 'hexwp' ),
								"type" 			=> "textarea",
  					);
					
					
					
					
	$option[] = array(			"name"				=>__('Show Call','hexwp'),
								"id"				=> "call",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);	
					
 
	$option[] = array( 			"name" 			=> __( 'input Text In Call', 'hexwp' ),
	 							"id" 			=> "call_textarea",
								"fold"			=>  array(
															'show'	=>	'call',
													),	
								"default" 		=>__( '0032 541 6488', 'hexwp' ),
								"type" 			=> "textarea",
						);
								
					
	$option[] = array(			"name"				=>__('Text,Html And Sortcode','hexwp'),
								"id"				=> "text_html",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);	
				
	$option[] = array(			"name" 			=> esc_html__( 'input Text in Text,Html And Shortcode', 'hexwp' ),
								"fold"			=>  array(
															'show'	=>	'text_html',
													),	
 								"id" 			=> "text_html_textarea",
   								"type" 			=> "textarea",
  				);
 				
						
	//************************************************ Mobile Bar ***********************************************/
  									
	$option[] = array(			"name" 			=> __('Mobile Bar Background Color' , 'hexwp'),
								"id" 			=> "background_color",
								"group"			=> __( 'Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					);
						
	$option[] = array( 			"name" 			=> __('Mobile Bar Link Color' , 'hexwp'),
								"id" 			=> "link_color",
								"type"			=> "multi_options",
								"group"			=> __( 'Style', 'hexwp' ),
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
							
							
	$option[] = array(			"name" 			=> __('Mobile Bar Text color' , 'hexwp'),
								"id" 			=> "text_color",
								"group"			=> __( 'Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					);  
						
	 
	$option[] = array(			"name" 			=> __('Mobile Bar Highlight color' , 'hexwp'),
								"id" 			=> "highlight_color",
								"group"			=> __( 'Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					); 	
						
						
	$option[] = array( 			"name"			=>	__('Mobile Bar Grey field color' , 'hexwp'),
								"id"			=> "grey_color",
								"group"			=> __( 'Style', 'hexwp' ),
								"type"			=> "color_rgba"
					);
											
						
	$option[] = array( 			"name" 			=> __('Mobile Bar Border color' , 'hexwp'),
								"id" 			=> "border_color",
								"group"			=> __( 'Style', 'hexwp' ),
								"type" 			=> "color_rgba"
					);   
					
					
	$option[] = array( 			"name" 			=> __('Social Icons Style', 'hexwp' ),
								"id" 			=> "social_style",
 								"type" 			=> "radio",
								"fold"			=>  array(
															'show'	=>	'social',
													),	
 								"options" 		=> array(
															"" => __('Default','hexwp'),
															"style-1" => __('Only Icon','hexwp'),
															"style-2" => __('Boxed Icon','hexwp'),
															"style-3" => __('Boxed Original Color','hexwp'),
 													)		
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
 
	$global['mobbar']['options']=$option;
     return $global;
} 
 
?>