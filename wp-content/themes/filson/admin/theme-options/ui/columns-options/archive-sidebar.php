<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Archive Sidebar

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "archive_sidebar_style_start",	
						"type"		=> "content"
				);	
$of_options[] = array( "name" 			=> esc_html__('Archive Sidebar' , 'hexwp'),
						"type" 			=> "title"
				); 
				
				
$of_options[] = array(  "name" 			=> esc_html__('Archive Main Right' , 'hexwp'),
						"desc" 			=> esc_html__('Select a sidebar in Archive Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_archive_right",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  
				

 $of_options[] = array(	"name" 			=> esc_html__('Archive Main Left' , 'hexwp'),
						"desc" 			=> esc_html__('Archive a sidebar in Archive Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_archive_left",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  				 
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);			 