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


  
	

$this->add_control(
	'layout',
	[
		'label' => __('Layout','hexwp'),
		'type' => \Elementor\Controls_Manager::SELECT,
		'default' => 'grid',
		"options"		=> array(
				"list"			=> __('List','hexwp'),
				"grid"			=>  __('Grid','hexwp'),
 			),
	]	 
);	 	
$column=__('Column','hexwp');
$layout=__('Layout','hexwp');
$this->add_control(
	'list_layout',
	[
		'label'			=> __('List Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'default' 		=> 'list_2',
		'condition'		=> array('layout' => 'list'),
		"options"		=> array(
				"list_1"		=> "1 $column", 
				"list_2"		=> "2 $column", 
				"list_3"		=> "3 $column", 
				"list_4"		=> "4 $column", 
				
 		),
	]	 
);	
 
$this->add_control(
	'grid_layout',
	[
		'label'			=> __('Grid Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'default' 		=> 'grid_6',
		'condition'		=> array('layout' => 'grid'),
		"options"		=> array(
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
	'responsive_column',
	[
		'label'			=> __('Column Width in Tablet and Mobile','hexwp'),
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
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		'conditions' 	=>  array(
		
				'terms' => array(
									
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
								 
				),
		),		
		
		"default"		=> 'no' ,
	]
); 
 
	 	 
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
		'conditions' 	=>  array(
		
				'terms' => array(
									
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
								 
				),
		),		
		
		"options" 		=>	hexwp_all_image_sizes(),
		
		
   	]
);	

 



$this->add_control(
	'second_image',
	[
		'label'			=> __('Second Image','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'yes' ,
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
 		"options" 		=> hexwp_array_options('box_layout',true), 
  	]
);	


 			
	 	  
	 
 $this->end_controls_section();
 