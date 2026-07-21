<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Image Text Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$slug=hexwp_slug();

	$option[]= array( 
			"name"			=>  '',
  			"type"			=> "not_depth",
 			"depth"			=> "depth_0",
			  
		); 
		$option[]= array( 
			"name"			=> esc_html__('Number of Posts to show','hexwp'),
			"id"			=> "hexwp_number",
 			"type"			=> "number",
 			"depth"			=> "depth_1",
		);
	
		 
		$option[]= array( 
			"name"			=> esc_html__('Category','hexwp'),
			"id"			=> "hexwp_cats",
			"type"			=> "select",
			"options"		=>  hexwp_category_array_options('cats'),	
 			"depth"			=> "depth_1",
		);
			
		$option[]= array( 
			"name"			=> esc_html__('Orderby','hexwp'),
			"id"			=> "hexwp_orderby",
			"type"			=> "select",
			"options"		=>  hexwp_array_options('orderby'),	
 			"depth"			=> "depth_1",
		); 		
		
		$option[]= array( 
			"name"			=> esc_html__('Tag','hexwp'),
			"id"			=> "hexwp_tag",
			"type"			=> "text",
 			"depth"			=> "depth_1",
 		); 		
		
		$option[]= array( 
			"name"			=> esc_html__('Platforms','hexwp'),
			"id"			=> "hexwp_platforms",
			"type"			=> "select",
			"options"		=>  hexwp_category_array_options('platforms'),
 			"depth"			=> "depth_1",
		); 		
		
		 		
		
	 
		$column=__('Column','hexwp');
	 /**********************Layout************************/
		$option[]= array( 
			"name"			=> esc_html__('Columns','hexwp'),
			"id"			=> "hexwp_column",
			"type"			=> "select",
 			"options"		=> array(
 					"3"	=> "3 $column", 
					"4"	=> "4 $column", 
					"5"	=> "5 $column", 
					"6"	=> "6 $column", 
					"7"	=> "7 $column", 
					"8"	=> "8 $column", 
  				),
 			"depth"			=> "depth_1",

					  
		); 
	 
	 
  			
		$option[]= array( 
			"name"			=> esc_html__('Space Between Item','hexwp'),
			"id"			=> "hexwp_between",
			"type"			=> "select",
			
			"default"		=>  '',
			"options"		=>  hexwp_array_options('between',true),		
 			"depth"			=> "depth_1",
		);
		
	 
		$option[]= array( 
			"name"			=> esc_html__('Image Ratio','hexwp'),
			"id"			=> "hexwp_ratio",
			"group"			=>  esc_html__('Layout','hexwp'),
 			"type"			=> "select",
			"options"		=>  hexwp_array_options('ratio',true),
 			"depth"			=> "depth_1",
			
		); 	 
	
	 	
		
		$option[]= array( 
			"name"			=> esc_html__('Image Size','hexwp'),
			"id"			=> "hexwp_thumb",
			"group"			=>  esc_html__('Layout','hexwp'),
			"type"			=> "select",
			"default"		=>  'full',
			"options" 		=>	hexwp_all_image_sizes(),
 			"depth"			=> "depth_1",
		); 	  
	 
	 
		$option[]= array( 
			'name'			=> esc_html__('Details Alignment','hexwp'),
			'id'			=> 'hexwp_alignment',
			"type"			=> "select",
 			"options"		=>  hexwp_array_options('alignment_justify_rtl',true),
 			"depth"			=> "depth_1",
		 
		); 
		$option[]= array( 
			"name"			=> esc_html__('Box Layout','hexwp'),
			"id"			=> "hexwp_box_layout", 		
			"type"			=> "select",
			"options"		=>  hexwp_array_options('box_layout',true),	
 			"depth"			=> "depth_1",
		); 		
	 
 		hexwp_admin_menu_options($item->ID,$option);

  
        