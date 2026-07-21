<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog General
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 

$this->start_controls_section(
	'general',
	[
		'label'			=> __( 'General', 'hexwp' ),
 	]
); 
 
$this->add_control(
	'title',
	[
		'label'			=>__('Title Box','hexwp'),
		'type'			=> \Elementor\Controls_Manager::TEXT ,
		"default"		=> __('All Categories','hexwp'),
  	]
); 		
 
  
 
$this->add_control(
	'menu',
	[
		'label'			=>__('Select Menu','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
 		'options'		=> hexwp_category_array_options('menu'),	
 	]
);
 
 
 
$this->add_control(
	'number',
	[
		'label'			=>__('Select Menu','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		"default"		=> '10' ,
  	]
); 		
	 
 $this->end_controls_section();

	 ?>