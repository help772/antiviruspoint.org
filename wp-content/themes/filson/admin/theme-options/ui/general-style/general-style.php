<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	General Style

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
  $of_options[] = array(	"position"		=> "start",
						"id" 			=> "general_style_start",	
						"type"		=> "content"
				);
$of_options[] = array( 	"name" 			=>	esc_html__('General Style' , 'hexwp'),
						"type" 			=>	"title"
				);	
  
$of_options[] = array( 	"name" 			=>	esc_html__('Primary Color' , 'hexwp'),
 						"id" 			=>	"primary_color",
 						"type" 			=>	"multi_options",
						"options"		=>	array( 	
													array( 	
															"name"			=> 	__('First Background','reza'),
															"id"			=> "background",
															"type"			=> "color_rgba",
													),
													array( 	
															"name"			=> 	__('Secend Background','reza'),
															"id"			=> "background_2",
															"type"			=> "color_rgba",
													),
													array( 
															"name"			=> __('Text Color','hexwp'),			
															"id"			=> "text",
															"type"			=> "color_rgba",
													),
											),
						
				); 	
 	 
								 
 
														 
								 
$of_options[] = array( 	"name" 			=>	esc_html__('Main Content Link Color' , 'hexwp'),
  						"id" 			=>	"main_link_color",
  						"type"			=>	"multi_options",
						"options"		=>	array( 	
													array( 
															"name"			=>		__('Text Link Color','hexwp'),
															"id"			=>	"link",
															"type"			=>	"color_rgba",
													), 						
													array( 
															"name"			=>	__('Hover Color','hexwp'),			
															"id"			=>	"hover",
															"type"			=>	"color_rgba",
													),
											),
 				);							
						
						
$of_options[] = array( 	"name" 			=>	esc_html__('Main Content Text color' , 'hexwp'),
  						"id" 			=>	"main_text_color",
 						"type" 			=>	"color_rgba"
				);   
					 

$of_options[] = array( 	"name" 			=>	esc_html__('Main Content Highlight  color' , 'hexwp'),
  						"id" 			=>	"main_highlight_color",
 						"type" 			=>	"color_rgba"
				);   
  
$of_options[] = array( 	"name" 			=>	esc_html__('Main Content Grey field  color' , 'hexwp'),
  						"id" 			=>	"main_grey_color",
 						"type" 			=>	"color_rgba"
				);   				
				
$of_options[] = array( 	"name" 			=>	esc_html__('Main Content Border color' , 'hexwp'),
  						"id" 			=>	"main_border_color",
 						"type" 			=>	"color_rgba"
				);   				
				
$of_options[] = array( 	"name" 			=>	esc_html__('Main Content Border Radius' , 'hexwp'),
  						"id" 			=>	"main_radius",
						"type"			=>	"radio",
						"options"		=>	hexwp_array_options('radius_mini'),
				);   									
 
 
				
$of_options[]= array( 	"name"			=>	esc_html__('Button Border Radius','hexwp'),
						"id"			=>	"button_radius", 
						"type"			=>	"radio",
						"options"		=>  hexwp_array_options('radius'),
 				);  
				
$of_options[]= array( 	"name"			=>	esc_html__('Social Style','hexwp'),
						"id"			=>	"social_style", 
						"type"			=>	"radio",
						"options" 		=> array(
 															"style-1" => __('Only Icon','hexwp'),
															"style-2" => __('Boxed Icon','hexwp'),
															"style-3" => __('Boxed Original Color','hexwp'),
 													)	
 				);  			
				
$of_options[]= array( 	"name"			=>	esc_html__('Social Border Radius','hexwp'),
						"id"			=>	"social_radius", 
						"type"			=>	"radio",
						"options"		=>  hexwp_array_options('radius'),
 				);  
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);				
				 