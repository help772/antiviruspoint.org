<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Content Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "columns_start",	
						"type"		=> "content"
				);
$of_options[] = array( 	"name" 			=> esc_html__( 'Columns', 'hexwp' ),
						"type" 			=> "title"
				);		
 
 		
$url_blog = hexwp_DIR . '/admin/assets/images/';
$of_options[] = array(	"name"			=> esc_html__( 'Columns' , 'hexwp'),
						"id" 			=> 	"column",
						"type" 			=> "images",
						"options" 		=> array(
													'main' 				=> $url_blog . 'main.png',
													'main_right' 		=> $url_blog . 'main_right.png',
													'left_main' 		=> $url_blog . 'left_main.png',
													'main_left_right' 	=> $url_blog . 'main_left_right.png', 
													'left_main_right' 	=> $url_blog . 'left_main_right.png',  
													'left_right_main' 	=> $url_blog . 'left_right_main.png',  
										)
				);	


$of_options[] = array(	"name"			=> esc_html__( 'Column Right', 'hexwp' ),
						"id" 			=> "column_right",
						"fold_array"	=>  array(
													'main_right'		=>	'column', 
													'main_left_right'	=>	'column', 
													'left_main_right'	=>	'column', 
													'left_right_main'	=>	'column', 
											),
						"type" 			=> "radio",
						"options" 		=> 	array(	
													"1_3" 			=> "1/3",
													"1_4" 			=> "1/4",
													"1_5" 			=> "1/5",
													"2_5" 			=> "2/5",
													"1_6" 			=> "1/6",
											) 
				);

				
$of_options[] = array(	"name" 			=>	esc_html__( 'Column Left', 'hexwp' ),
						"id" 			=> "column_left",
 						"fold_array"	=>  array(	
													'left_main'				=>'column', 
													'main_left_right'		=>'column', 
													'left_main_right'		=>'column', 
													'left_right_main'		=>'column', 
						), 
						"type" 			=> "radio",
						"options"	 	=> 	array(	 
													"1_3"			=> "1/3",
													"1_4" 			=> "1/4",
													"1_5"			=> "1/5",
													"2_5"			=> "2/5",
													"1_6"			=> "1/6",
											) 
				);
				 
				  
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);									
	  
  
 