<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Menu Item Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$slug=hexwp_slug();


	$option[]= array( 
		"name"			=>  __('Lable Featured:','hexwp'),
		"id"			=> "hexwp_menu_featured",
		"type"			=> "select",
		"options"		=> hexwp_menu_array_options('menu_tags'),
 	);
	 $option[]= array( 
		"name"			=>  __('Icon Type:','hexwp'),
		"id"			=> "hexwp_menu_icon_type",
		"type"			=> "select",
		"options"		=> array(
					"icon"			=> __('Icon','hexwp'),
					"image"			=> __('Upload Image','hexwp'),
				),
		
 	);	
	
	
	$option[]= array( 
		"name"			=>  __('Icon:','hexwp'),
		"id"			=> "hexwp_menu_icon",
		"type"			=> "icon",
 	);	
	
$option[]= array( 
		"name"			=>  __('Image:','hexwp'),
		"id"			=> "hexwp_menu_image",
		"type"			=> "image",
 	);	
	
	
	
	$option[]= array( 
		"name"			=>  __('Sub Menu Width:','hexwp'),
		"id"			=> "hexwp_menu_sub_width",
		"type"			=> "select",
		"options"		=> hexwp_menu_array_options('menu_width'),
 	);
	
 
 	$option[]= array( 
		"name"			=>  __('Background Image For Sub Menu:','hexwp'),
		"id"			=> "hexwp_menu_background_image",
		"type"			=> "image",
 		"depth"			=> "depth_0",
  	);
	
 	$option[]= array( 
		"name"			=>  __('Background Size:','hexwp'),
		"id"			=> "hexwp_menu_background_size",
		"type"			=> "select",
		"options"		=> hexwp_menu_array_options('background_size'),
 		"depth"			=> "depth_0",
		 
  	);
	
 	$option[]= array( 
		"name"			=>  __('Background Position:','hexwp'),
		"id"			=> "hexwp_menu_background_position",
		"type"			=> "select",
		"options"		=> hexwp_menu_array_options('background_position'),
 		"depth"			=> "depth_0",
		 
  	);
	
	
 	$option[]= array( 
		"name"			=>  __('Background Opacity:','hexwp'),
		"id"			=> "hexwp_menu_background_opacity",
		"type"			=> "select",
		"options"		=> hexwp_menu_array_options('background_opacity'),
 		"depth"			=> "depth_0",
		 
  	);
	
	
  		hexwp_admin_menu_options($item->ID,$option);
	