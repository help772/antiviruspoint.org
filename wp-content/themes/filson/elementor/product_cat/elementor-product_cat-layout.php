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
$this->add_control(
	'column',
	[
		'label'			=> __('Column Layout','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'default'		=> '4',
  		"options"		=> array(

 			"1"	=> "1 $column", 
 			"2"	=> "2 $column", 
 			"3"	=> "3 $column", 
 			"4"	=> "4 $column",  
 			"5"	=> "5 $column", 
 			"6"	=> "6 $column", 
			"7"	=> "7 $column", 
 			"8"	=> "8 $column",  
 			"9"	=> "9 $column",  
 			"10" => "10 $column",  
 			"11" => "11 $column",  
 			"12" => "12 $column",  
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
		'options'		=> hexwp_array_options('ratio',true),
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
	 	 
 