<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Image Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$slug=hexwp_slug();


$option[]= array( 
			"name"			=>  '',
  			"type"			=> "not_depth",
 			"depth"			=> "depth_0",
 ); 
$option[]= array( 
			"name"			=> esc_html__('Image','hexwp'),
			"id"			=> "hexwp_menu_image",
			"type"			=> "image",
 			"depth"			=> "depth_1",
); 		

 
 
$option[]= array( 
			"name"			=> esc_html__('Full Width','hexwp'),
			"id"			=> "hexwp_menu_full_width",
			"type"			=> "checkbox",
 			"depth"			=> "depth_1",
); 		

$option[]= array( 
			"name"			=> esc_html__('Full Height','hexwp'),
			"id"			=> "hexwp_menu_full_height",
			"type"			=> "checkbox",
 			"depth"			=> "depth_1",
); 		

$option[]= array( 
			"name"			=> esc_html__('Image URL','hexwp'),
			"id"			=> "hexwp_menu_image_url",
			"type"			=> "text",
 			"depth"			=> "depth_1",
); 		

  hexwp_admin_menu_options($item->ID,$option);
