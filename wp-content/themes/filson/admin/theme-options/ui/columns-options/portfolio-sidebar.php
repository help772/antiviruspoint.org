<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Portfolio Sidebar

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "portfolio_sidebar_style_start",	
						"type"		=> "content"
				);	
$of_options[] = array( "name" 			=> esc_html__('Portfolio Sidebar' , 'hexwp'),
						"type" 			=> "title"
				); 
				
				
$of_options[] = array(  "name" 			=> esc_html__('Portfolio Main Right' , 'hexwp'),
						"desc" 			=> esc_html__('Select a sidebar in Portfolio Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_portfolio_right",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  


$of_options[] = array(  "name" 			=> esc_html__('Portfolio Main Left' , 'hexwp'),
						"desc" 			=> esc_html__('Select a sidebar in Portfolio Main Sidebar' , 'hexwp'),
						"id" 			=> "sidebar_portfolio_left",
  						"type" 			=> "select",
 						"options" 		=> hexwp_category_array_options('sidebars'),
				);  
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);			
 