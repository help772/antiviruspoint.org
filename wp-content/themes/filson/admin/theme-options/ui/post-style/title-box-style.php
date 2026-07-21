<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Title Box

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
$of_options[]= array(	"name"			=>	esc_html__('Title Box','hexwp'),
						"type"			=>	"title",
				);
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "title_box_start",	
						"type"		=> "content"
				);
  
 
 
$of_options[]= array(	"name"			=>	esc_html__('Title Box Style','hexwp'),
						"id"			=>	"title_box_style",	
						"type"			=>	"select",
							"options"		=>	hexwp_array_options('title_box_style'),

				); 		
				
 
$of_options[]= array(	"name"			=>	esc_html__('Main Title Color','hexwp'),
						"id"			=>	"title_box_main_color",	
						"type"			=>	"multi_options",		
						"options"		=>	array( 	
													array( 
														"name"			=> __('Background Color','hexwp'),			
														"id"			=> "background",
														 "fold"			=>	array( 	
																					"style-7"		=> "title_box_style",
																					"style-8"		=> "title_box_style",
																			), 
														"type"			=> "color_rgba",
													),
													array( 
														"name"			=> 	__('Text Color','hexwp'),
														"id"			=> "text",
														"type"			=> "color_rgba",
													) 	
											),						
    			); 		 
	
	
	
$of_options[]= array(	"name"			=>	esc_html__('Tab Item Color','hexwp'),
						"id"			=>	"title_box_tab_color",	
						"type"			=>	"multi_options",
						"options"		=>	array( 	
													array( 
														"name"			=>	__('Background Color','hexwp'),			
														"id"			=>	"background",
														 "fold"			=>	array( 	
																					"style-7"		=> "title_box_style",
																			), 			
														"type"			=> "color_rgba",
													),
													array( 
														"name"			=> 	__('Text Color','hexwp'),
														"id"			=> "text",
														"type"			=> "color_rgba",
 													) 	
											),							
			); 		
	 
	 
$of_options[]= array(	"name"			=> esc_html__('Tab Active Color','hexwp'),
 						"id"			=> "title_box_active_color",	
						"type"			=> "multi_options",
 						"options"		=>	array( 	
													array( 
														"name"			=> __('Background Color','hexwp'),			
														"id"			=> "background",
														"fold"			=>	array( 	
																					"style-7"		=> "title_box_style",
																			), 				
														"type"			=> "color_rgba",
													),
													array( 
														"name"			=> 	__('Text Color','hexwp'),
														"id"			=> "text",
														"type"			=> "color_rgba",
 													) 	
											),							
   			); 	
	
	
$of_options[]= array(	"name"			=> esc_html__('Title Box Border Color','hexwp'),
						"id"			=> "title_box_border_color",
						 "fold_array"	=>	array( 	
													"style-2"		=> "title_box_style",
													"style-3"		=> "title_box_style",
													"style-4"		=> "title_box_style", 
													"style-5"		=> "title_box_style", 
											), 			
						"type"			=> "color_rgba",	 		
				); 		
				
	
$of_options[]= array( 	"name"			=> esc_html__('Title box Radius','hexwp'),
						"id"			=> "title_box_radius", 
						"fold_array"	=>	array( 	
													"style-6"		=> "title_box_style",
													"style-7"		=> "title_box_style",
											), 
						"type"			=> "radio",
						"options"		=>  hexwp_array_options('radius'),
 				); 
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);					 	
