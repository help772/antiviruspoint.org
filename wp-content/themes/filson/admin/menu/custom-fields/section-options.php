<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Section Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $slug=hexwp_slug();
	$option[]= array( 
			"name"			=>  '',
  			"type"			=> "not_depth",
 			"depth"			=> "depth_0",
			  
		); 
		$option[]= array( 
			"name"			=> esc_html__('Sub Column','hexwp'),
			"id"			=> "hexwp_menu_sub_column",
			"type"			=> "radio_image",
			"options"		=>  array(
							'1-1'			=> hexwp_DIR.'/admin/assets/images/menu-col/1-1.jpg',
							'1-2' 			=> hexwp_DIR.'/admin/assets/images/menu-col/1-2.jpg',
							'1-3' 			=> hexwp_DIR.'/admin/assets/images/menu-col/1-3.jpg',
							'2-3' 			=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/1-3-2-3.jpg' : hexwp_DIR.'/admin/assets/images/menu-col/2-3.jpg',
							'1-3-2-3' 		=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/2-3.jpg' : hexwp_DIR.'/admin/assets/images/menu-col/1-3-2-3.jpg',
							'1-4' 			=> hexwp_DIR.'/admin/assets/images/menu-col/1-4.jpg',
							'3-4'			=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/1-4-3-4.jpg' : hexwp_DIR.'/admin/assets/images/menu-col/3-4.jpg',
							'1-4-3-4'		=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/3-4.jpg' : hexwp_DIR.'/admin/assets/images/menu-col/1-4-3-4.jpg',
							'1-5'			=> hexwp_DIR.'/admin/assets/images/menu-col/1-5.jpg',
							'4-5'			=> hexwp_DIR.'/admin/assets/images/menu-col/4-5.jpg',
							'3-5'			=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/2-5-3-5.jpg': hexwp_DIR.'/admin/assets/images/menu-col/3-5.jpg',
							'2-5-3-5'		=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/3-5.jpg':hexwp_DIR.'/admin/assets/images/menu-col/2-5-3-5.jpg',
							'1-6'			=> hexwp_DIR.'/admin/assets/images/menu-col/1-6.jpg',
							'5-6'			=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/1-6-5-6.jpg': hexwp_DIR.'/admin/assets/images/menu-col/5-6.jpg',
							'1-6-5-6'		=> is_rtl() ? hexwp_DIR.'/admin/assets/images/menu-col/5-6.jpg': hexwp_DIR.'/admin/assets/images/menu-col/1-6-5-6.jpg',
							'1-7'			=> hexwp_DIR.'/admin/assets/images/menu-col/1-7.jpg',
							'1-8'			=> hexwp_DIR.'/admin/assets/images/menu-col/1-8.jpg',
						),
			 	
 			"depth"			=> "depth_1",
		);
 		
		$option[]= array( 
			"name"			=>  __('Padding Top:','hexwp'),
			"id"			=> "hexwp_menu_padding_top",
			"type"			=> "number",
  			"depth"			=> "depth_1",
		);
		$option[]= array( 
			"name"			=> is_rtl()? __('Padding Left:','hexwp'):__('Padding Right:','hexwp'),
			"id"			=> "hexwp_menu_padding_right",
			"type"			=> "number",
  			"depth"			=> "depth_1",
		);
		$option[]= array( 
			"name"			=> __('Padding Bottom:','hexwp'),
			"id"			=> "hexwp_menu_padding_bottom",
			"type"			=> "number",
  			"depth"			=> "depth_1",
		);
		
		$option[]= array( 
			"name"			=> is_rtl()? __('Padding Right:','hexwp'):__('Padding Left:','hexwp'),
			"id"			=> "hexwp_menu_padding_left",
			"type"			=> "number",
  			"depth"			=> "depth_1",
		);
		
		$option[]= array( 
			"name"			=> esc_html__('Gap Between Column:','hexwp'),
			"id"			=> "hexwp_menu_gap",
			"type"			=> "number",
  			"depth"			=> "depth_1",
		);
		
		
  		hexwp_admin_menu_options($item->ID,$option);
		