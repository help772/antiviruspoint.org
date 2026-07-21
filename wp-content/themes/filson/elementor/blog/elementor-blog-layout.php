<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Layout
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////         
$this->start_controls_section(
	'layout_section',
	[
		'label' => __( 'Layout', 'hexwp' ),
	]
);


/*****************************************************************************************************************************************************
  															Blog Layout
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
$this->add_control(
	'layout',
	[
		'label'			=> __('Layout','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'default'		=> 'list',
		"options"		=> array(
		
				"list"			=> __('List','hexwp'),
				"grid"			=>  __('Grid','hexwp'), 
				"featured"		=> __('Glider','hexwp'), 
				
		),
	]	 
);	 	
	$column=__('Column','hexwp');
$layout=__('Layout','hexwp');
$this->add_control(
	'list_layout',
	array(
		'label'			=> __('List Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'default' 		=> 'list_1',
		'condition'		=> array('layout' => 'list'),
		"options"		=> array(
		
				"list_1"		=> "1 $column", 
				"list_2"		=> "2 $column", 
				"list_3"		=> "3 $column", 
				"list_4"		=> "4 $column", 
				"list_5"		=> "$layout 5", 
				"list_6"		=> "$layout 6", 
 
 				
		),
	)	 
);	
 
$this->add_control(
	'grid_layout',
	[
		'label'			=> __('Grid Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'default' 		=> 'grid_1',
		'condition'		=> array('layout' => 'grid'),
		"options" 		=> array(
 				"grid_1"		=> "1 $column", 
				"grid_2"		=> "2 $column", 
				"grid_3"		=> "3 $column", 
				"grid_4"		=> "4 $column", 
				"grid_5"		=> "5 $column", 
				"grid_6"		=> "6 $column", 
				"grid_7"		=> "7 $column", 
				"grid_8"		=> "8 $column",
				"grid_9"		=> "$layout 9",
				"grid_10"		=> "$layout 10",
				"grid_11"		=> "$layout 11",
				"grid_12"		=> "$layout 12",
				"grid_13"		=> "$layout 13",
				"grid_14"		=> "$layout 14",
				"grid_15"		=> "$layout 15",
				
		),
	]	 
);	 
 
$this->add_control(
	'featured_layout',
	[
		'label'			=> __('Glider Layout','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'default'		=> 'featured_1',
		'condition'		=> array('layout' => 'featured'),
		"options"		=> array(
				"featured_1"		=> "1 $column", 
				"featured_2"		=> "2 $column", 
				"featured_3"		=> "3 $column", 
				"featured_4"		=> "4 $column", 
				"featured_5"		=> "5 $column", 
				"featured_6"		=> "6 $column", 
				"featured_7"		=> "7 $column", 
				"featured_8"		=> "8 $column",
				"featured_9"		=> "$layout 9 ",
				"featured_10"		=> "$layout 10",
				"featured_11"		=> "$layout 11",
				"featured_12"		=> "$layout 12",
				"featured_13"		=> "$layout 13",
				"featured_14"		=> "$layout 14",
				"featured_15"		=> "$layout 15",
				"featured_16"		=> "$layout 16",
				"featured_17"		=> "$layout 17",
				"featured_18"		=> "$layout 18", 
				"featured_19"		=> "$layout 19",
				"featured_20"		=> "$layout 20",
				"featured_21"		=> "$layout 21",
				"featured_22"		=> "$layout 22",
				"featured_23"		=> "$layout 23",
				"featured_24"		=> "$layout 24",
				"featured_25"		=> "$layout 25",
				"featured_26"		=> "$layout 26",
				"featured_27"		=> "$layout 27",
				"featured_28"		=> "$layout 28",
				"featured_29"		=> "$layout 29",
				"featured_30"		=> "$layout 30",
				"featured_31"		=> "$layout 31",
				"featured_32"		=> "$layout 32",
			
		)
	]
  );	
	
$this->add_control(
	'responsive_column',
	[ 	
 		"label"			=> __('Column in Tablet and Mobile','hexwp'),
  		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options" 		=> hexwp_array_options('first_responsive_column',true), 

		]
);
 
  
$this->add_control(
	'between',
	[
		'label'			=> __('Space Between Item','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'options'		=> hexwp_array_options('between',true),
 	]
);	
	 
/*****************************************************************************************************************************************************
  															Ratio
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$this->add_control(
	'ratio',
	[
		'label'			=> __('Image Ratio','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'options'		=> hexwp_array_options('ratio',true),
 	]
);	

 
		


$this->add_control(
	'ratio_2',
	[
		'label'			=> __('Image Ratio For Second Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'conditions' 	=> array(
		
 				'relation'	=> 'or',
 				'terms'		=> array(
						
						/**************************** List **********************************/	
						array('terms' => array(
								
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'list'
								),
								array(
										'name' => 'list_layout',
										'operator' => 'in',
										'value' =>  array('list_5','list_6'),
								)
						)),
						/**************************** Grid **********************************/	
						array('terms' => array(
									
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'grid'
								),
								array(
										'name' => 'grid_layout',
										'operator' => 'in',
										'value' =>  array('grid_9','grid_10','grid_11','grid_12','grid_13','grid_14','grid_15'),
								)
								 
						)),
						/**************************** Featured **********************************/	
						array('terms' => array(
									
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'featured'
								),
								array(
										'name' => 'featured_layout',
										'operator' => 'in',
										'value' =>  array('featured_14','featured_15','featured_16','featured_17','featured_18','featured_19','featured_20','featured_21','featured_22'),
								)
								 
						)),
						
				),
		),
		 
 		'options'		=> hexwp_array_options('ratio',true),
 	]
);	
	 	 
/*****************************************************************************************************************************************************
  															Image Width
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	 
$this->add_control(
	'image_width',
	[
		'label'			=> __('Image Width','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'condition'		=> array('layout' => 'list'),
 		'options'		=> hexwp_array_options('image_width',true),
 	]		
					
);

$this->add_control(
	'image_width_2',
	[
		'label'			=> __('Image Width For Second Layout','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
 		'conditions' 	=> array(
				
				'terms' 	=> array(
					
						array(
								'name' => 'layout',
								'operator' => '==',
								'value' => 'list'
						),
						array(
								'name' => 'list_layout',
								'operator' => 'in',
								'value' =>  array('list_5','list_6'),
						)
 			
				)
				
		),
		
  		'options'		=> hexwp_array_options('image_width',true),
 	]		
					
);	
/*****************************************************************************************************************************************************
  															Image Size
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
$this->add_control(
	'image_size',
	[
		'label'			=> __('Image Size','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		"options" 		=>	hexwp_all_image_sizes(),
   	]
);	

$this->add_control(
	'image_size_2',
	[
		'label'			=> __('Image Size For Second Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'conditions' 	=> array(
		
 				'relation'	=> 'or',
 				'terms'		=> array(
						
						/**************************** List **********************************/	
						array('terms' => array(
								
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'list'
								),
								array(
										'name' => 'list_layout',
										'operator' => 'in',
										'value' =>  array('list_5','list_6'),
								)
						)),
						/**************************** Grid **********************************/	
						array('terms' => array(
									
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'grid'
								),
								array(
										'name' => 'grid_layout',
										'operator' => 'in',
										'value' =>  array('grid_9','grid_10','grid_11','grid_12','grid_13','grid_14','grid_15'),
								)
								 
						)),
						/**************************** Featured **********************************/	
						array('terms' => array(
									
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'featured'
								),
								array(
										'name' => 'featured_layout',
										'operator' => 'in',
										'value' =>  array('featured_9','featured_10','featured_11','featured_12','featured_12','featured_14','featured_15','featured_16','featured_17'
															,'featured_18','featured_19','featured_20','featured_21','featured_22','featured_23','featured_24','featured_25','featured_26',
															'featured_27','featured_28'),
								)
								 
						)),
						
				),
		),
		
		"options" 		=>	hexwp_all_image_sizes(),
		
		
   	]
);			
	 	  
$this->add_control(
	'alignment',
	
	[
		'label'			=> __('Details Alignment','hexwp'),
  		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'options'		=> hexwp_array_options('alignment_justify',true),			

  	]
);			
	 				  
  	  
$this->add_control(
	'box_layout',
	[
		'label'			=> __('Box Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'condition'		=> array('layout' => array('grid','list')),
		"options" 		=> hexwp_array_options('box_layout',true), 

		
	]
);	

$this->add_control(
	'caption_layout',
	[
		'label'			=> __('Caption Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'condition'		=> array('layout' => 'featured'),
		"options" 		=> hexwp_array_options('caption_layout',true), 

		
	]
);			 

 
$this->end_controls_section();
	 	 
	?>