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
	'excerpt_2',
	[
		'label'			=> __('Show Excerpt Posts For Second Layout','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
		'conditions' 	=> array(
		
 				'relation'	=> 'or',
 				'terms'		=> array(
						
						/**************************** List **********************************/	
						array('terms' => array(
								
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'list'
								),
								array(
										'name' => 'list_layout',
										'operator' => 'in',
										'value' =>  array('list_5','list_6'),
								)
						)),
						/**************************** Grid **********************************/	
						array('terms' => array(
									
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
								 
						)),
						/**************************** Featured **********************************/	
						array('terms' => array(
									
								array(
										'name' => 'layout',
										'operator' => '==',
										'value' => 'featured'
								),
								array(
										'name' => 'featured_layout',
										'operator' => 'in',
										'value' =>  array('featured_9','featured_10','featured_11','featured_12','featured_12','featured_14','featured_15','featured_16','featured_17'
															,'featured_18','featured_19','featured_20','featured_21','featured_22','featured_23','featured_24','featured_25','featured_26',
															'featured_27','featured_28'),
								)
								 
						)),
						
				),
		),		
		
		"default"		=> 'no' ,
	]
);

$this->add_control(
	'excerpt_limit',
	[
		'label'			=> __('Limit Excerpt length','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'description'	=> __('example: "200"','hexwp') ,
		'condition'	=> ['excerpt' => 'yes'],
		
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
		
				'' 		=>   		esc_html__('Default', 'hexwp' ),
				'show'	=>   esc_html__('Show', 'hexwp' ),
				'hide'	=>   esc_html__('Hide', 'hexwp' ),
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
				"pagenavi"		=>  esc_html__('Page Number','hexwp'),
						 
		)	
	]
);     
		
 $this->end_controls_section();

