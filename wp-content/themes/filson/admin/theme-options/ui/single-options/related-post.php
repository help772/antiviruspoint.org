<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Related Posts Settings

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "related_post_start",	
						"type"		=> "content"
				);

$of_options[] = array( 	"name" 			=> esc_html__('Related Posts Settings' , 'hexwp'),
						"type" 			=> "title"
				);

$of_options[] = array( 	"name" 			=> esc_html__('Related Posts' , 'hexwp'),
   						"id" 			=> "related",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
				
				
$of_options[] = array( 	"position"		=> "start",
						"id"			=> "related_start",
						"fold" 			=> "related",
						"type"			=> "content"
 				);		 
 	 
$of_options[] = array( 	"name" 			=> esc_html__('Related Posts Title' , 'hexwp'),
						"desc" 			=> esc_html__('This text will display in title bar Related Posts.' , 'hexwp'),
  						"id" 			=> "related_title",
 						"type" 			=> "text",
 				);		
	 
$url_blog = hexwp_DIR . '/admin/assets/images/';
$of_options[] = array( "name" 			=> esc_html__('Rows Per Page To Show' , 'hexwp'),
 						"id" 			=> "related_row",
						"type" 			=> "text-mini",
						"fold" 			=> "related",
 				); 			
					
 $of_options[] = array( "name" 			=> esc_html__('Query Type' , 'hexwp'),
  						"id" 			=> "related_query",
 						"type" 			=> "radio",
 						"options" 		=> array(
													'recent' 	=> esc_html__('Recent' , 'hexwp'),
													'category' 	=> esc_html__('Category' , 'hexwp'),
													'tag' 		=> esc_html__('Tags' , 'hexwp'),
													'random' 	=> esc_html__('Random' , 'hexwp'),
											)
				);
									
									
$of_options[] = array(	"name" 			=> esc_html__( 'Related Layout' , 'hexwp'),
   						"id" 			=> 	"related_layout",
 						"type" 			=> "radio",
						"options" 		=> array(	
 											"grid_3"		=> __('3 Column','hexwp'),
											"grid_4"		=> __('4 Column','hexwp'),
											"grid_5"		=> __('5 Column','hexwp'),
											"grid_6"		=> __('6 Column','hexwp'),
 										)
				);											

$of_options[]=  array( 	"name"			=> esc_html__('Column in Tablet and Mobile','hexwp'),
						"id"			=> "related_responsive_column",
						"type"			=> "select",
 						"options" 		=> hexwp_array_options('responsive_column',true), 
			 
);
										
$of_options[] = array( 	"name" 		=> esc_html__( 'Related Space Between Item' , 'hexwp'),
   						"id" 		=> "related_between",
 						"type" 		=> "radio",
					"options" 		=> hexwp_array_options('between'), 

				);	
	
$of_options[] = array( 	"name" 			=> esc_html__( 'Image Ratio' , 'hexwp'),
   						"id" 			=> "related_ratio",
 						"type" 			=> "radio",
						"options" 		=>  hexwp_array_options('ratio')
				);		
				
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Image Size' , 'hexwp'),
   						"id" 			=> "related_image_size",
 						"type" 			=> "select",
						"options" 		=>  hexwp_all_image_sizes()
				);		


$of_options[] = array( 	"name" 			=> esc_html__( 'Show Excerpt Posts' , 'hexwp'),
 						"id" 			=> "related_excerpt",
     					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
				
				 
$of_options[]= array(	"name"			=> esc_html__('Related Box Layout','hexwp'),
						"id"			=> "related_box_layout",
						"type"			=> "select",
						"hide" 			=> 'none',
						"options" 	=>  hexwp_array_options('box_layout'),
					); 						
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);				