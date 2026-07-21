<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Post Style
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
$this->start_controls_section(
	'post_style',
	[
		'label'			=> __('Post Style','hexwp'),
		'tab'			=> \Elementor\Controls_Manager::TAB_STYLE,
				
 	]
);
	
$this->add_control(
	'background_color',
	[
		'label'			=>__('Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-bg: {{VALUE}}',
			),	
 	]
);


$this->add_control(
	'title_color_link',
	[
		'label'			=>__('Title Color - Link Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-tl-lk: {{VALUE}}',
			),	
 	]
); 	

$this->add_control(
	'title_color_hover',
	[
		'label'			=>__('Title Color - Hover Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-tl-hv-lk: {{VALUE}}',
			),	
		
 	]
); 	


$this->add_control(
	'excerpt_color',
	[
		'label'			=>__('Excerpt Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-expt-txt: {{VALUE}}',
			),	
 	]
); 	


$this->add_control(
	'meta_color',
	[
		'label'			=>__('Meta Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-meta-txt: {{VALUE}}',
			),	
 	]
); 	
	  
	
$this->add_control(
	'border_color',
	[
		'label'			=>__('Border Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-br-cr: {{VALUE}}',
			),	
 	]
); 	 

$this->add_control(
	'box_border_color',
	[
		'label'			=>__('Box Shadow Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-sd: {{VALUE}};--hw-post-hv-sd: {{VALUE}};',
			),	
 	]
); 	
 	$this->add_control(
		'between_border',
		[
			'label'			=>__('Between Border','hexwp'),
			'type'			=> \Elementor\Controls_Manager::SELECT,
 			'options' 	=>  hexwp_array_options('between_border',true)
		]
	); 	 
	
 
$this->add_control(
	'radius',
	[
		'label'			=>__('Border Radius','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>   hexwp_array_options('radius_mini',true),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-rd: {{VALUE}}',
			),
 	]
); 	 
 	
 
	
 $this->end_controls_section();
	
