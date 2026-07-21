<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Page Sidebar

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "page_sidebar_style_start",	
						"type"		=> "content"
				);	
$of_options[] = array( "name" 			=> esc_html__('Page Sidebar' , 'hexwp'),
						"type" 			=> "title"
				); 
				
				
$of_options[] = array(  "name" 			=> esc_html__('Page Main Right' , 'hexwp'),
						"desc" 			=> esc_html__('Select a sidebar in Page Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_page_right",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  


$of_options[] = array(  "name" 			=> esc_html__('Page Main Left' , 'hexwp'),
						"desc" 			=> esc_html__('Select a sidebar in Page Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_page_left",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);		
 