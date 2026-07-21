<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Home Sidebar

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////							
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "home_sidebar_style_start",	
						"type"		=> "content"
				);									
$of_options[] = array( "name" 			=> esc_html__('Home Sidebar' , 'hexwp'),
						"type" 			=> "title"
				); 
				

$of_options[] = array(  "name" 			=> esc_html__('Home Main Right' , 'hexwp'),
						"desc" 			=> esc_html__('Select a sidebar in Single Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_home_right",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  
 

 $of_options[] = array(  "name" 		=> esc_html__('Home Main Left' , 'hexwp'),
						"desc" 			=> esc_html__('Select a sidebar in Single Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_home_left",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);					 	
 		
 