<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Slider
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
$this->start_controls_section(
	'slider_style',
	[
		'label' => __('Slider Style','hexwp'),
		'tab' => \Elementor\Controls_Manager::TAB_STYLE,
	]
);
 
 	 
$this->add_control(
	'arrow_background_color',
	[
		'label' 		=> __('Arrow Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'condition'		=> ['arrows' => 'yes'],
		'selectors' => [
					'{{WRAPPER}} [class*="rd-el-"]' => '--hw-aw-bg: {{VALUE}}',
		],			
		
		 
 	]
); 	



$this->add_control(
	'arrow_text_color',
	[
		'label' 		=>__('Arrow Text Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'condition'		=> ['arrows' => 'yes'],
		'selectors' => [
					'{{WRAPPER}} [class*="rd-el-"]' => '--hw-aw-txt: {{VALUE}}',
		],	

		
 	]
); 	

	   
 $this->end_controls_section();
 
	