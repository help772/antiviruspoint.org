<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Post Style
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
$this->start_controls_section(
	'menu_style',
	[
		'label'			=> __('Menu Style','hexwp'),
		'tab'			=> \Elementor\Controls_Manager::TAB_STYLE,
				
 	]
);
	
	
	
$this->add_control(
	'title_background',
	[
		'label'			=> __('Title First Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
	 
 		'separator' => 'before',
		
 	]
); 	

	
$this->add_control(
	'title_background_2',
	[
		'label'			=> __('Title Secend Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
 
 		
 	]
); 	

$this->add_control(
	'title_text_color',
	[
		'label'			=> __('Title Text Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-tl-txt: {{VALUE}}',
		),			
 	]
); 	 
	
	
	
$this->add_control(
	'background_color',
	[
		'label'			=>__('Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-bg: {{VALUE}}',
		),	
		
 	]
);

 

	
	
	
$this->add_control(
	'menu_item_color',
	[
		'label'			=>  __('Menu Item Text Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-lk: {{VALUE}}',
		),		
 	]
); 	

$this->add_control(
	'menu_item_hover_color',
	[
		'label'			=> __('Menu Item Hover Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-hv-lk: {{VALUE}}',
		),		
		
 	]
); 	 
	


$this->add_control(
	'more_background_color',
	[
		'label'			=> __('More Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-more-bg: {{VALUE}}',
		),	
		

 	]
); 	

$this->add_control(
	'more_text_color',
	[
		'label'			=>__('More Text Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-more-txt: {{VALUE}}',
		),	
		
 	]
); 	 
	 
$this->add_control(
	'border_color',
	[
		'label'			=> __('Border Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-br-cr: {{VALUE}}',
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
	'radius',
	[
		'label'			=>__('Border Radius','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>   hexwp_array_options('radius_mini'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-rd: {{VALUE}}',
			),		
 	]
); 	 
 	
 
	
 $this->end_controls_section();
	
 			
	
		
	?>