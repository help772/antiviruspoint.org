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
	'number',
	[
		'label'			=>__('Number of Posts to show','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		"default"		=> '6' ,
  	]
); 		
	
$this->add_control(
	'multi_cats',
	[
		'label'			=> __('Category','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT2,
		'multiple'		=> true,
		'options'		=> hexwp_category_array_options('product_cat'),	
 	]
);

$this->add_control(
	'orderby',
	[
		'label'			=> __('Orderby','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'options'		=> hexwp_array_options('product_orderby'),	
 	]
);
 
		
$this->add_control(
	'title_limit',
	[
		'label'			=> __('Limit Title length','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'description'	=> __('example: "100"','hexwp') ,
	]
); 
	 	
		
$this->add_control(
	'excerpt',
	[
		'label'			=> __('Show Excerpt Posts','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
 	]
); 	 
 
$this->add_control(
	'excerpt_limit',
	[
		'label'			=> __('Limit Excerpt length','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'description'	=> __('example: "200"','hexwp') ,
		'condition'	=> array('excerpt' => 'yes'),
		
	]
); 
		
		
$this->add_control(
	'countdown',
	[
		'label'			=> __('Show Countdown Sale Timer','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
	]
); 			
		
$this->add_control(
	'meta_category',
	[
		'label'			=> __('Show Category Meta','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
 	]
); 	
		

$this->add_control(
	'rating',
	[
		'label'			=> __( 'Show Rating' , 'hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'yes' ,
  	]
); 			
		 
$this->add_control(
	'addcart',
	[
		'label'			=> __('Show Add to Cart','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'yes' ,
  	]
); 			
	
 
		
 $this->end_controls_section();
