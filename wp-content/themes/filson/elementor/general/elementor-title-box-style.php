<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

														Title Box 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$this->start_controls_section(
	'title_box_tab_style',
	[
		'label' => __( 'Title Box Style', 'hexwp' ),
		'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
 	]
);
		 
 
		
 
$this->add_control(
	'title_box_style',
	[
		'label' =>__('Title Box Style','hexwp'),
		'type' => \Elementor\Controls_Manager::SELECT,
		"options" 		=> hexwp_array_options('title_box_style',true), 
 
				
 	]
); 	


$this->add_control(
	'title_box_main_background',
	[
		'label'			=>__('Main - Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'condition' 	=> array('title_box_style'		=> 'style-7','style-8'),
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-main-bg: {{VALUE}}',
		],			
		
 		
 	]
); 	

$this->add_control(
	'title_box_main_text',
	[
		'label' 		=>__('Main - Text Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-main-txt: {{VALUE}}',
		],	
 	]
); 	



$this->add_control(
	'title_box_tab_background',
	[
		'label'			=>__('Tabs - Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'condition' 	=> array('title_box_style'		=> 'style-7'),
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-tab-bg: {{VALUE}}',
		],	
 	]
); 	

$this->add_control(
	'title_box_tab_text',
	[
		'label'			=>__('Tabs - Text Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-tab-txt: {{VALUE}}',
		],	
		
 	]
); 	

$this->add_control(
	'title_box_active_background',
	[
		'label'			=>__('Active Tabs - Background Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'condition' 	=> array('title_box_style'		=> 'style-7'),
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-atv-bg: {{VALUE}}',
		],	
		
 	]
); 	

$this->add_control(
	'title_box_active_text',
	[
		'label'			=>__('Active Tabs - Text Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-atv-txt: {{VALUE}}',
		],	
 	]
); 	
	  
	
	
$this->add_control(
	'title_box_border_color',
	[
		'label'			=>__('Title Box Border Color','hexwp'),
		'type'			=> \Elementor\Controls_Manager::COLOR,
		'condition' 	=> array('title_box_style' => array('style-2','style-3','style-4','style-5')),
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-br-cr: {{VALUE}}',
		],	
 ]
); 	
	  
	  
$this->add_control(
	'title_box_radius',
	[
		'label' =>		__('Title box Radius','hexwp'),
		'type' => 		\Elementor\Controls_Manager::SELECT,
		"options"		=>  hexwp_array_options('radius',true), 
		'condition' 	=> array('title_box_style' => array('style-6','style-7')),
		'selectors' => [
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-tbox-rd: {{VALUE}}',
		],	
				
 	]
); 	
	   
 $this->end_controls_section();
