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
		
 				"grid"			=>  __('Grid','hexwp'), 
				"featured"		=> __('Glider','hexwp'), 
				
		) , 
 	]	 
);	 	
$column=__('Column','hexwp');
$this->add_control(
	'column',
	[
		'label' => __('Column Layout','hexwp'),
		'type' => \Elementor\Controls_Manager::SELECT,
		'default' => '3',
 		"options"		=> array(
		
  			"2"	=> "2 $column", 
 			"3"	=> "3 $column", 
 			"4"	=> "4 $column",  
 			"5"	=> "5 $column", 
 			"6"	=> "6 $column", 
			"7"	=> "7 $column", 
 			"8"	=> "8 $column", 
				
 		),
	]	 
);	 

$this->add_control(
	'responsive_column',
	[
		'label'			=> __('Column Width in Tablet and Mobile','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options" 		=> hexwp_array_options('responsive_column',true), 

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
		'condition'		=> array('layout' => 'grid'),
		'options'		=> hexwp_array_options('ratio',true),
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
		'condition'		=> array('layout' => 'grid'),
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
 