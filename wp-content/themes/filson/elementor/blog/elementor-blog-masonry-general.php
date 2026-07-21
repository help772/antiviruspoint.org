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
		"default"		=> '5' ,
  	]
); 		
	
$this->add_control(
	'multi_cats',
	[
		'label'			=> __('Category','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT2,
		'multiple'		=> true,
		'options'		=> hexwp_category_array_options('cats'),	
 	]
);

$this->add_control(
	'orderby',
	[
		'label'			=> __('Orderby','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'options'		=> hexwp_array_options('orderby'),	
 	]
);
 	 
$this->add_control(
	'ignore_sticky_posts',
	[
		'label'			=> __('Ignore Sticky Posts','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'yes' ,
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
		"default"		=> 'yes' ,
	]
); 	 
 
$this->add_control(
	'excerpt_limit',
	[
		'label'			=> __('Limit Excerpt length','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'description'	=> __('example: "200"','hexwp') ,
		'condition'		=> array('excerpt' => 'yes'),
		
	]
); 
		
		
$this->add_control(
	'meta_author',
	[
		'label'			=> __( 'Author Meta' , 'hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
	]
); 			
		
$this->add_control(
	'meta_category',
	[
		'label'			=> __( 'Category Meta' , 'hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'yes' ,
	]
); 	
		

$this->add_control(
	'meta_date',
	[
		'label'			=> __( 'Date Meta' , 'hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		"default"		=> 'yes' ,
  	]
); 			
		
$this->add_control(
	'meta_view',
	[
		'label'			=> __( 'View Meta' , 'hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
	]
); 	
			
$this->add_control(
	'meta_comments',
	[
		'label'			=> __( 'Comments Meta' , 'hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
	]
); 		

$this->add_control(
	'readmore',
	[
		'label'			=> __('Show Read More','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
	]
); 		 

$this->add_control(
	'hover_post_icon',
	[
		'label'			=> __('Icon in Hover post','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
 		"options"		=>  array(
		
				'' 		=>	 __('Default', 'hexwp' ),
				'show'	=>   __('Show', 'hexwp' ),
				'hide'	=>   __('Hide', 'hexwp' ),
				
		)					
	]
); 	
$this->add_control(
	'more_posts',
	[
 		'label'			=> __('More Posts','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT ,
		"options"		=>   array(
		
			""				=> 	__('None','hexwp'),
			"load_more"		=> 	__('Load More','hexwp'),
 					 
		)	
	]
);     
		
 $this->end_controls_section();
