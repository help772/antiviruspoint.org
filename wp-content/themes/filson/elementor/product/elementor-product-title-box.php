<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

														Title Box 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$this->start_controls_section(
	'title_box',
	[
		'label'			=>  __( 'Title Box', 'hexwp' ) ,
	]
);
		
		
$this->add_control(
	'title',
	[
		'label'			=> __( 'Title Box', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::TEXT,
		'placeholder'	=> __( 'Title Box', 'hexwp' ),
		'default'		=> __('Title Box','hexwp'),
	]
); 		
$this->add_control(
	'title_box_type',
	[
		'label'			=> __('Title Box Display','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>  array(
			''				=> __('Default','hexwp'),			
			'main-right'	=> __('Only Main Title Right','hexwp'),
			'main-center'	=> __('Only Main Title Center','hexwp'),
  			'main-tabs'		=> __('Main Title and Tabs Right','hexwp'),
 			'tabs-center'	=> __('All Tabs Center','hexwp'),			
 			'hide'			=> __('Hide','hexwp'),				
		)
	]
); 
 
$this->add_control(
	'title_box_all',
	[
		'label'			=>__('Title For All Tab','hexwp'),
		'type'			=> \Elementor\Controls_Manager::TEXT,
		'default'		=>  hexwp_t('all') ,
		
		
	]
); 
$this->add_control(
	'title_box_list_all',
	[
		'label'			=> __('Show Button See all','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'no' ,
	]
); 	 
$repeater = new \Elementor\Repeater();
   
	$repeater->add_control(
			'title',
			 [
				'label'			=> __( 'Title', 'hexwp' ),
				'type'			=> \Elementor\Controls_Manager::TEXT,
				'default'		=> __( 'Tab', 'hexwp' ),
				'label_block'	=> true,
			]
		);		
		
	$repeater->add_control(
			'cats',
			[
				'label'		=> __( 'Category', 'hexwp' ),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				"options"		=>  hexwp_category_array_options('product_cat'),	
			]
		);	
		
	$repeater->add_control(
			'orderby',
			[
				'label' => __( 'Orderby', 'hexwp' ),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				"options"		=>  hexwp_array_options('product_orderby'),	
			]
		);					 
 
 	$this->add_control(
			'tabs',
			[
				'label' => __('Add tabs','hexwp'),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
 				 
			]
		);   
  
$this->end_controls_section();
 