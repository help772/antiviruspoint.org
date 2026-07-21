<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Portfolio Typography
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
 $this->start_controls_section(
	'typography',
	[
		'label' => __('Typography','hexwp'),
		'tab' => \Elementor\Controls_Manager::TAB_STYLE,
	]
);
/*****************************************************************************************************************************************************
															Title Box Main
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
  
$this->add_control(
	'title_box_main_typo',
	[
		'label' => __('Title Box Main Typography','hexwp'),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
	]
);
 
 
 
$this->add_control(
	'title_box_main_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-main-fn-sz: {{VALUE}}px',
			),	
		
   	]
);  


$this->add_control(
	'title_box_main_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-main-fn-wt: {{VALUE}}',
			),	
	]
); 

/*****************************************************************************************************************************************************
															Title Box Tabs
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
$this->add_control(
	'title_box_tab_typo',
	[
		'label' => __('Title Box Tabs Typography','hexwp'),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
	]
);
 
  
$this->add_control(
	'title_box_tab_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-tab-fn-sz: {{VALUE}}px',
			),	
		
   	]
); 

$this->add_control(
	'title_box_tab_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-tab-fn-wt: {{VALUE}}',
			),	
	]
); 
 
/*****************************************************************************************************************************************************
															Post Tile Typography
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
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
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-product-tl-fn-sz: {{VALUE}}px',
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
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-product-tl-fn-wt: {{VALUE}}',
			),	
    	]
); 	
/*****************************************************************************************************************************************************
															Price Typography
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
$this->add_control(
	'price_typo',
	[
		'label' => __('Price Typography','hexwp'),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
		
	]
);
 
  
$this->add_control(
	'price_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-price-fn-sz: {{VALUE}}px',
			),	
   	]
); 

$this->add_control(
	'price_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-price-fn-wt: {{VALUE}}',
			),	
    	]
); 		
		
				
/*****************************************************************************************************************************************************
															Excerpt Typography
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
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
			
/*****************************************************************************************************************************************************
															Meta Typography
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
$this->add_control(
	'meta_typo',
	[
		'label' => __('Meta Typography','hexwp'),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
	]
);
 
  
$this->add_control(
	'meta_font_size',
	[
		'label'			=>__('Font Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::NUMBER ,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-meta-fn-sz: {{VALUE}}px',
			),	
   	]
); 

$this->add_control(
	'meta_font_weight',
	[
		'label'			=>__('Font Weight','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
		'options'		=> hexwp_array_options('font_weight'),
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-meta-fn-wt: {{VALUE}}',
			),	
	]
); 		
			 	 

 $this->end_controls_section();