<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Typography
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
 $this->start_controls_section(
			'typography',
			array(
				'label' => __('Menu Typography','hexwp'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
 			)
		);
  
  
 
  $this->add_control(
	'title_typo',
			[
				'label' => __('Title Typography','hexwp'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
 
			]
		);
 
 
 
$this->add_control(
	'title_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
			'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-tl-fn-sz: {{VALUE}}px',
			),	
	 	
		
   	]
);  
$this->add_control(
	'title_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-tl-fn-wt: {{VALUE}}',
			),
	]
); 







$this->add_control(
	'menu_item_typo',
			[
				'label' => __('Menu Typography','hexwp'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
 
 
 
$this->add_control(
	'menu_item_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-fn-sz: {{VALUE}}',
			),
		
   	]
);  
$this->add_control(
	'menu_item_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-fn-wt: {{VALUE}}',
			),
		
	]
); 








$this->add_control(
	'more_typo',
			[
				'label' =>__('More Typography','hexwp'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
 
 
 
$this->add_control(
	'more_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-more-fn-sz: {{VALUE}}',
			),
   	]
);  
$this->add_control(
	'more_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-menu-more-fn-wt: {{VALUE}}',
			),
    	]
); 







 $this->end_controls_section();