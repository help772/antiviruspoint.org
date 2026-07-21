<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Typography
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
 $this->start_controls_section(
	'typography',
	[
		'label' => __('Typography','hexwp'),
		'tab' => \Elementor\Controls_Manager::TAB_STYLE,
	]
);
  
 

$this->add_control(
	'post_title_typo',
	[
		'label' => __('Post Title Typography','hexwp'),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
 
		
	]
);
 
  
$this->add_control(
	'post_title_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-tl-fn-sz: {{VALUE}}px',
			),	
   	]
); 

$this->add_control(
	'post_title_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-tl-fn-wt: {{VALUE}}',
			),	
    	]
); 		
		
		
$this->add_control(
	'excerpt_typo',
	[
		'label' => esc_html__('Excerpt Typography','hexwp'),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-expt-fn-sz: {{VALUE}}px',
			),	
	]
);
 
  
$this->add_control(
	'excerpt_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-expt-fn-sz: {{VALUE}}px',
			),		
   	]
); 

$this->add_control(
	'excerpt_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-expt-fn-wt: {{VALUE}}',
			),	
	]
); 		
			
		 
 		 	 

 $this->end_controls_section();