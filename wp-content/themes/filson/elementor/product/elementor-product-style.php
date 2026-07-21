<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Post Style
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
$this->start_controls_section(
	'post_style',
	[
		'label'			=> __('Product Style','hexwp'),
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
	'price_main_color',
	[
		'label'			=> __('Price Color','hexwp').'-'.__('Main Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-price-ma: {{VALUE}}',
			),		
	
 	]
); 	

 $this->add_control(
	'price_sale_color',
	[
		'label'			=>__('Price Color','hexwp').'-'.__('Sale Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-price-sa: {{VALUE}}',
			),	
 	]
); 	
$this->add_control(
	'price_regular_color',
	[
		'label'			=> __('Price Color','hexwp').'-'.__('Regular Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'after',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-price-re: {{VALUE}}',
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
	'countdown_background',
	[
		'label'			=>'Countdown Background',
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-cd-bg: {{VALUE}}',
			),	
		
 	]
); 												 
$this->add_control(
	'countdown_number',
	[
		'label'			=>__('Countdown Number','hexwp'),		
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'after',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-cd-num: {{VALUE}}',
			),	
			
 	]
); 	 	
											
													
$this->add_control(
	'countdown_text',
	[
		'label'			=>__('Countdown Text','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'after',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-cd-txt: {{VALUE}}',
			),	
 	]
); 	 		
	
$this->add_control(
	'rating_rating_color',
	[
		'label'			=>__('Rating Color','hexwp').'-'.__('Rating','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-rat-rat-cr: {{VALUE}}',
			),	
	
 	]
); 	 

$this->add_control(
	'rating_none_color',
	[
		'label'			=>__('Rating Color','hexwp').'-'.__('None Rating','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'separator' => 'after',
		'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-rat-no-cr: {{VALUE}}',
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
	
 			
	
		