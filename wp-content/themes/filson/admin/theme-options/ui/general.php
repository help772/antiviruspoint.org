<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	General

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$of_options[] = array(	"name" 			=> esc_html__( 'General', 'hexwp' ),
						"type" 			=> "heading",
						"id" 			=> "general",
						"icon"			=> ADMIN_IMAGES . "gerneral.png",
); 
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "general_start",	
						"type"		=> "content"
				);
$of_options[] = array( 	"name" 			=> esc_html__( 'General' , 'hexwp'),
						"type" 			=> "title"
				);
 

$of_options[] = array(	"name" 			=> esc_html__( 'Responsive Design', 'hexwp' ),
						"desc" 			=> esc_html__( 'SCheck this box to use the responsive design features. If left unchecked then the fixed layout is used.', 'hexwp' ),
						"id" 			=> "responsive",
   						"type" 			=> "radio",
   						"hide" 			=> "disable",
   						"options" 			=> array(
									"enable" 			=>  __( 'Enable' , 'hexwp' ),
									"disable" 			=> __( 'Disable', 'hexwp' ),						 
								),
							);		
$of_options[] = array(  "name" 			=> esc_html__( 'Body Width', 'hexwp' ),
 						"id" 			=> "body_width",
  						"type" 			=> "radio",
 						"options" 		=> 	array(	
													"1000px"		=> '1000px', 
													"1100px"		=> '1100px', 
													"1200px"		=> '1200px', 
													"1300px"		=> '1300px',  
													"1400px"		=> '1400px',  
													"1500px"		=> '1500px', 
													"1600px"		=> '1600px', 
													"1800px" 		=> '1800px', 
													"1920px"		=> '1920px',
													"100%"			=> __( 'Full Width', 'hexwp' ),
  										)
 				);	 
 

  									
								
$of_options[] = array(	"name" 			=> esc_html__( 'Script Code', 'hexwp' ),
						"desc" 			=> esc_html__( 'Add Custom Script Code', 'hexwp' ),
						"id"			=> "custom_script",
 						"type"			=> "textarea" ,
				);
   
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);		


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Breadcrumbs

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "breadcrumbs_style_start",	
						"type"		=> "content"
				);
 					
$of_options[]= array( 	"name"			=>	esc_html__('Breadcrumbs','hexwp'),
   						"type"			=>	"title",
 				);
 	
$of_options[] = array( 	"name" 			=>	esc_html__( 'Display Breadcrumbs', 'hexwp' ),
 						"id" 			=>	"breadcrumbs_show",
						"type" 			=>	"radio",
   						"hide" 			=> "hide",
 						"options" 		=>	array(
													"show"			=>	esc_html__( 'Enable', 'hexwp' ),
													"hide" 			=>	esc_html__( 'Disable', 'hexwp' ),
											)						 
				);	
						
						
$of_options[]= array(	"name"			=> esc_html__('Breadcrumbs Color','hexwp'),
						"id"			=> "breadcrumbs_color",	
 						"fold_array"	=>  array(
													'show'		=>	'breadcrumbs_show', 
 											),
						"type"			=> "multi_options",		
 						"options"		=>	array( 	
													array( 
														"name"			=> __('Background Color','hexwp'),			
														"id"			=> "background",
														"type"			=> "color_rgba",
													),
													array( 
														"name"			=> 	__('Text Color','hexwp'),
														"id"			=> "text",
														"type"			=> "color_rgba",
													) 	
											),						
 		 
   				); 	
				
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);				
  