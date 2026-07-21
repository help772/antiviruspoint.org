<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Post Style

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "post_style_start",	
						"type"		=> "content"
				);
$of_options[] = array(	"name" 			=> esc_html__('Post Style' , 'hexwp'),
						"type" 			=> "title"
				);	 
	
	
$of_options[] = array(	"name" 			=> esc_html__('Post Background Color' , 'hexwp'),
						"id" 			=> "post_background_color",
						"type"			=> "color_rgba",
				);
					
					
$of_options[] = array(	"name" 			=> esc_html__('Post Link Color Title' , 'hexwp'),
						"id" 			=> "post_title_color",
						"type"			=> "multi_options",
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
  				
				
$of_options[]= array( 	"name"			=> esc_html__('Price Color','hexwp'),
						"id"			=> "price_color",	 
 						"type"			=> "multi_options",
						"options"		=>	array( 	
													array( 
															"name"			=> 	__('Main Color','hexwp'),
															"id"			=> "main",
															"type"			=> "color_rgba",
													),
													array( 
															"name"			=> __('Sale Color','hexwp'),			
															"id"			=> "sale",
															"type"			=> "color_rgba",
													),
													array( 
															"name"			=> __('Regular Color','hexwp'),			
															"id"			=> "regular",
															"type"			=> "color_rgba",
													), 
															
											), 					
   				); 	 
				
	 
 $of_options[] = array( "name" 			=> esc_html__('Post Excerpt Color' , 'hexwp'),
   						"id" 			=> "post_excerpt_color",
						"type" 			=> "color"
				);	
			
 					
$of_options[] = array( "name" 			=> esc_html__('Post Meta Text Color' , 'hexwp'),
   						"id" 			=> "post_meta_color",
  						"type"			=> "color_rgba",
				);	  			 
						
$of_options[] = array(	"name" 			=>	esc_html__('Countdown Color' , 'hexwp'),
						"id" 			=>	"countdown_color",
						"type"			=> "multi_options",
						"options"		=>	array( 	
													array( 
															"name"			=> 	__('Background Color','hexwp'),
															"id"			=> "background",
															"type"			=> "color_rgba",
													), 						
													array( 
															"name"			=> __('Number Color','hexwp'),			
															"id"			=> "number",
															"type"			=> "color_rgba",
													),
													array( 
															"name"			=> __('Text Color','hexwp'),	
															"id"			=> "text",
															"type"			=> "color_rgba",
													),													
											),
				);						 		
 
						
$of_options[]= array( 	"name"			=> esc_html__('Rating Color','hexwp'),
						"id"			=> "rating_color",	 
 						"type"			=> "multi_options",
						"options"		=>	array( 	
													array( 
														"name"			=> 	__('Rating','hexwp'),
														"id"			=> "rating",
														"type"			=> "color_rgba",
													),
													array( 
														"name"			=> __('None Rating','hexwp'),			
														"id"			=> "none",
														"type"			=> "color_rgba",
													),
  						), 					
   				); 	 
  				
  				
								
$of_options[]= array( 	"name"			=> esc_html__('Product Featured Color','hexwp'),
						"id"			=> "featured_color",	 
 						"type"			=> "multi_options",
						"options"		=>	array( 	
													 	
													array( 	
															"name"			=> 	__('First Background','hexwp'),
															"id"			=> "background",
															"type"			=> "color_rgba",
													),
													array( 	
															"name"			=> 	__('Secend Background','hexwp'),
															"id"			=> "background_2",
															"type"			=> "color_rgba",
													),
													array( 
														"name"			=> __('Text','hexwp'),			
														"id"			=> "text",
														"type"			=> "color_rgba",
													),
  						), 					
   				); 	 				
				
 $of_options[] = array( "name" 			=> esc_html__('Between Border ' , 'hexwp'),
   						"id" 			=> "between_border",
  						"type"			=> "select",
						"options"		=>	hexwp_array_options('between_border'), 
				);
$of_options[] = array( "name" 			=> esc_html__('Box Shadow Size' , 'hexwp'),
   						"id" 			=> "box_border_size",
  						"type"			=> "select",
						"options"		=>	hexwp_array_options('shadow_element'), 
				);	  			 
				 		
$of_options[] = array( "name" 			=> esc_html__('Box Shadow Color' , 'hexwp'),
   						"id" 			=> "box_border_color",
  						"type"			=> "color_rgba",
				);	  			 
				 		
 
				
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);				
