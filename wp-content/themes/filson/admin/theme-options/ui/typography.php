<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Typography

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$of_options[] = array( 	"name" 			=> esc_html__('Typography' , 'hexwp'),
						"type" 			=> "heading",
						"id" 			=> "typography",
						"icon"		=> ADMIN_IMAGES."typography.png",
				); 
				
 

 
				
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Standard Fonts

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$of_options[] = array( 	"name" 			=> esc_html__('Standard Fonts' , 'hexwp'),
 						"type" 			=> "title"
				);
					
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "body_typo_start",	
						"type"		=> "content"
				);
$of_options[] = array( 	"name" 		=> esc_html__('Exmple Google Font' , 'hexwp'),
						"desc" 		=> esc_html__('Some description font' , 'hexwp'),
						"id" 		=> "g_select",
						"desc" 		=> esc_html__('Select a font' , 'hexwp'),
						"type" 		=> "select_google_font",
						"preview" 	=> array(	"text" => esc_html__('This is my preview text!' , 'hexwp'),
												"size" => "30px"
									),
						"options" 	=> $standahw_fonts,
 				);									
$of_options[] = array( 	"name" 			=> esc_html__('Font Family Body' , 'hexwp'),
						"id"     		=> "body_font_family",
 						"type" 			=> "select",
						"options" 		=> $standahw_fonts,
 				);	
									
 				
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);				
 
 					
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Typography Title Box

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "title_box_typo_start",	
						"type"		=> "content"
				);
					

$of_options[] = array( 	"name" 			=> esc_html__('Typography Title Box' , 'hexwp'),
						"type" 			=> "title"
					);
 
 					
$of_options[] = array( 	"name" 			=> esc_html__('Title Box Main Typography' , 'hexwp'),
 						"id" 			=> "title_box_main_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo'),
				);

$of_options[] = array( 	"name" 			=> esc_html__('Title Box Tabs Typography' , 'hexwp'),
 						"id" 			=> "title_box_tab_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo'),
				);
				
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Typography Posts

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "header_typo_start",	
						"type"		=> "content"
				);
						

$of_options[] = array( 	"name" 			=> esc_html__('Typography Posts' , 'hexwp'),
						"type" 			=> "title"
					);
 
$of_options[] = array( 	"name" 			=> esc_html__('Post Title Typography' , 'hexwp'),
 						"id" 			=> "post_title_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_mini'),
				); 		
				
$of_options[] = array( 	"name" 			=> esc_html__('Excerpt Typography' , 'hexwp'),
 						"id" 			=> "post_excerpt_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_mini'),
				); 		
$of_options[] = array( 	"name" 			=> esc_html__('Meta Typography' , 'hexwp'),
 						"id" 			=> "post_meta_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_mini'),
				); 	
				
 					
$of_options[] = array( 	"name" 			=> esc_html__('Read More Typography' , 'hexwp'),
 						"id" 			=> "read_more_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_mini'),
				); 	
					
$of_options[] = array( 	"name" 			=> esc_html__('More Posts Typography' , 'hexwp'),
 						"id" 			=> "more_posts_typo",
						"type" 			=> "multi_options",
						"options" 	=> hexwp_multi_array_options('typo_mini'),
				); 												
				  		 
 
 
$of_options[] = array( 	"name" 			=> esc_html__('Product Title Typography' , 'hexwp'),
 						"id" 			=> "product_title_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_mini'),
				); 	
					
				 
$of_options[] = array( 	"name" 			=> esc_html__('Price Typography' , 'hexwp'),
 						"id" 			=> "price_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_mini'),
				); 	 		
				
				
				
$of_options[] = array( 	"name" 			=> esc_html__('Article Typography' , 'hexwp'),
   						"id" 			=> "article_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_line'),
				); 	 		
					
									 							
 $of_options[] = array( "name" 			=> esc_html__('Button Typography' , 'hexwp'),
   						"id" 			=> "button_typo",
						"type" 			=> "multi_options",
						"options" 		=> hexwp_multi_array_options('typo_mini'),
				); 	 		
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);						  			 
