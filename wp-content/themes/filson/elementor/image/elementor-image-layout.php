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
 	$column=__('Column','hexwp');
$layout=__('Layout','hexwp');
$this->add_control(
	'list_layout',
	array(
		'label'			=> __('List Layout','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		'default' => 'list_4',
 		"options"		=> array(
		
				"list_1"		=> "1 $column", 
				"list_2"		=> "2 $column", 
				"list_3"		=> "3 $column", 
				"list_4"		=> "4 $column", 
				"list_5"		=>  "5 $column", 
				"list_6"		=>  "6 $column", 
 
 				
		),
	)	 
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
$this->add_control(
	'image_width',
	[
		'label'			=> __('Image Width','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
  		'options'		=> hexwp_array_options('image_width_auto',true),
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
	 	 