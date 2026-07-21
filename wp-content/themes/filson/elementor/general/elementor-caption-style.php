<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Post Style
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
$this->start_controls_section(
	'caption_style',
	[
		'label' => __('Image And Caption Style','hexwp'),
		'tab' => \Elementor\Controls_Manager::TAB_STYLE,
	]
);
 
	
	
	
$this->add_control(
	'image_effect',
	[
		'label' 		=>__('Hover Image Effect','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>   hexwp_array_options('hover_image',true)
 	]
); 

$this->add_control(
	'caption_effect',
	[
		'label' 		=>	__('Caption Background Effect','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>   hexwp_array_options('caption_effect',true)
 	]
); 


$this->add_control(
	'caption_background_color',
	[
		'label' 		=>	__('Caption Background Color','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::COLOR,
  	]
); 

$this->add_control(
	'caption_color',
	[
		'label' 		=>	__('Caption Color','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::COLOR,
  	]
); 


 $this->end_controls_section(); 
						
 