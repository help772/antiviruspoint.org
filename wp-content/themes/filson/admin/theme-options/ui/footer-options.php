<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
$of_options[] = array(	"name"	 		=> esc_html__('Footer Options', 'hexwp' ),
						"type" 			=> "heading",
						"id" 			=> "footer-options",
 						"icon"			=> ADMIN_IMAGES . "footer.png",
				); 
				
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "top_footer_start",	
						"type"		=> "content"
				);
  
$of_options[] = array( 	"name" 			=> esc_html__('Top Footer', 'hexwp' ),
 						"type" 			=> "title"
				);			
				
$of_options[] = array(  "name" 			=> esc_html__( 'Select Page Builder For Top Footer', 'hexwp' ),
						"desc" 			=> esc_html__( 'Select Page in display Top Footer', 'hexwp' ),
 						"id" 			=> "page_top_footer",
  						"type" 			=> "category",
						"options" 		=> hexwp_category_array_options('page_builder'),
  				);					
			 
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);														
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "footer_start",	
						"type"		=> "content"
				);
  
$of_options[] = array( 	"name" 			=> esc_html__('Footer', 'hexwp' ),
 						"type" 			=> "title"
				);			
				
 
$of_options[] = array(  "name" 			=> esc_html__( 'Select Page Builder For Footer', 'hexwp' ),
						"desc" 			=> esc_html__( 'Select Page in displays Footer,note:if select page builder hide footer', 'hexwp' ),
 						"id" 			=> "page_footer",
  						"type" 			=> "category",
						"options" 		=> hexwp_category_array_options('page_builder_footer'),
  				);	
				
$of_options[] = array(  "name" 			=> 	esc_html__( 'Footer Column', 'hexwp' ),
  						"id" 			=> 	"footer_column",
  						"type" 			=> 	"ratio",
						"options" 		=>	array(
													 '1'			=>	"1",
													 '2'			=>	"2",
													 '3'			=>	"3",
													 '4'			=>	"4",
													 '5'			=> "5",
													 '6'			=> "6"
											),
  				);	

$of_options[] = array( 	"name" 			=> esc_html__( 'input  Text in Bottom Footer', 'hexwp' ),
 						"id" 			=> "footer_bottom_code",
  						"type" 			=> "textarea",
   				);												
			 
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);										
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer Social

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "footer_social_start",	
						"type"		=> "content"
				);
$of_options[] = array( "name" 			=> esc_html__( 'Footer Social Icon', 'hexwp' ),
						"type" 		=> "title"
				);

$of_options[] = array( 	"name" 			=> esc_html__( 'Display Social Icons', 'hexwp' ),
 						"id" 			=> "footer_social",
						"type" 			=> "radio",
						"hide" 			=> "hide",
  						"options" 		=> 	array(	 
													"show" 	=> esc_html__( 'Enable', 'hexwp' ),
 													"hide" 			=> esc_html__( 'Disable', 'hexwp' ),
											)
						
						
				);
				
				

$of_options[]= array(	"name"			=> __('Icon Style','hexwp'),
						"id"			=> "footer_icon_style",
						"type"			=> "radio",
						"options"		=> array(
													"style-1" => esc_html__('Style 1: only icon','hexwp'),
													"style-2" => esc_html__('Style 2: Boxed Icon','hexwp'),
													"style-3" => esc_html__('Style 3: Boxed Original Color','hexwp'),
											),						
				); 
				
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);								
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer Style

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////											
$of_options[] = array( "name" 			=> esc_html__( 'Footer Style', 'hexwp' ),
						"type" 			=> "title"
				);					
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "footer_style_start",	
						"type"		=> "content"
				);			
$of_options[] = array( 	"name" 			=> esc_html__('Footer Background Color' , 'hexwp'),
  						"id" 			=> "footer_background_color",
 						"type" 			=> "color_rgba"
				);
  					
					
$of_options[] = array( 	"name" 			=> esc_html__('Footer Link Color' , 'hexwp'),
  						"id" 			=> "footer_link_color",
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
  						"type"			=> "multi_options",
				);		
					
						
$of_options[] = array( 	"name" 			=> esc_html__('Footer Text color' , 'hexwp'),
  						"id" 			=> "footer_text_color",
 						"type" 			=> "color_rgba"
				);  
 
$of_options[] = array( 	"name" 			=> esc_html__('Footer Highlight  color' , 'hexwp'),
  						"id" 			=> "footer_highlight_color",
 						"type" 			=> "color_rgba"
				); 	
					
$of_options[] = array( 	"name" 			=> esc_html__('Footer Grey field color' , 'hexwp'),
  						"id" 			=> "footer_grey_color",
 						"type" 			=> "color_rgba"
				);					
					
$of_options[] = array( 	"name" 			=> esc_html__('Footer Border color' , 'hexwp'),
  						"id" 			=> "footer_border_color",
 						"type" 			=> "color_rgba"
				);   
					  

$of_options[] = array( 	"name" 			=> esc_html__('Footer Border Radius' , 'hexwp'),
						"id" 			=> "footer_radius",
						"type" 			=> "radio",
						"options"		=> hexwp_array_options('radius'),
							
 				); 								
 	
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			); 			 