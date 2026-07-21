<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Image Text Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
			"name"			=> esc_html__('Image URL','hexwp'),
			"id"			=> "hexwp_menu_image_url",
			"type"			=> "text",
 			"depth"			=> "depth_1",
); 		


$option[]= array( 
			'name'			=> esc_html__('Details Alignment','hexwp'),
			'id'			=> 'hexwp_menu_alignment',
			"type"			=> "select",
 			"options"		=>  hexwp_array_options('alignment_rtl',true),
 			"depth"			=> "depth_1",
		 
); 	
 

hexwp_admin_menu_options($item->ID,$option);



 
        