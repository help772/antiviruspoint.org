<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

														Title Box 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$this->start_controls_section(
	'title_box',
	[
		'label'			=>  __( 'Title Box', 'hexwp' ) ,
	]
);
		
		
$this->add_control(
	'title',
	[
		'label'			=> __( 'Title Box', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::TEXT,
		'placeholder'	=> __( 'Title Box', 'hexwp' ),
		'default'		=> __('Title Box','hexwp'),
	]
); 		
$this->add_control(
	'title_box_type',
	[
		'label'			=> __('Title Box Display','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		"options"		=>  array(
			''				=> __('Default','hexwp'),			
			'main-right'	=> __('Only Main Title Right','hexwp'),
			'main-center'	=> __('Only Main Title Center','hexwp'),
  			'main-tabs'		=> __('Main Title and Tabs Right','hexwp'),
 			'hide'			=> __('Hide','hexwp'),				
		)
	]
); 
 
  
$this->end_controls_section();
 