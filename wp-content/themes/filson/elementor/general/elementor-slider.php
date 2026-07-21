<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Slider
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
$this->start_controls_section(
	'slider',
	[
		'label' => __( 'Slider', 'hexwp' ),
	]
);
 
 
		
$this->add_control(
	'arrows',
	[
		'label'			=>__('Arrows','hexwp'),
		
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		"default"		=> 'title-box' ,
		"options"		=>  [
				"" 				=>esc_html__('None','reza'),
			"title-box"		=>esc_html__('in Title Box','reza'),
			"content"		=>esc_html__('in Content','reza'),
 			 
		]
	]
); 			 
 
 
$this->add_control(
	'arrow_location',
	[
		'label'			=> __('Arrow Laction','reza'),
		'condition'		=> ['arrows' => 'content'],
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>  [
				"" 				=>esc_html__('Default','reza'),
			"slider-1"		=>esc_html__('Equals','reza'),
			"slider-2"		=>esc_html__('Inside','reza'),
			"slider-3"		=>esc_html__('Outside','reza'), 
			 
		]
	]
); 
 
 
$this->add_control(
	'arrow_layout',
	[
		'label'			=>  __('Arrow Layout','reza'),
		'condition'		=> ['arrows' => 'content'],
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>  [
				""				=> __('Default','reza'),	
				"hover"			=> __('On Hover','reza'),		 
				"fixed"			=> __('Fixed','reza'),	 
			 
		]
	]
); 

$this->add_control(
	'arrow_size',
	[
		'label'			=> __('Arrow Size','reza'),
		'condition'		=> ['arrows' => 'content'],
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>  [
			""				=> __('Default','reza'),	
			"small"			=> __('Small','reza'),		 
			"medium"		=> __('Medium','reza'),		 
			"large"			=> __('Large','reza'), 
			 
		]
	]
); 
$this->add_control(
	'auto',
	[
		'label'			=>__('Auto Play','hexwp'),
 		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'yes' ,
	]
); 

 

$this->add_control(
	'speed',
	[
		'label'			=>__('Animation Speed','hexwp'),
		'type'			=> \Elementor\Controls_Manager::TEXT ,
		"default"		=> '2000' ,
	]
); 
 
$this->add_control(
	'pause',
	[
		'label'			=> __('Animation Pause Time','hexwp'),
		'type'			=> \Elementor\Controls_Manager::TEXT ,
		"default"		=> '10000' ,
	]
); 
 
 $this->end_controls_section();
 