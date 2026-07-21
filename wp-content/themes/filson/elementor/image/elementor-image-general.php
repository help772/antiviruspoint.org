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
		
$repeater = new \Elementor\Repeater();


 
	$repeater->add_control(
			'image', [
			'label' => __( 'Image', 'hexwp' ),
			'type'			=> \Elementor\Controls_Manager::MEDIA,
 			]
		);
	$repeater->add_control(
			'title', [
				'label' =>  __( 'Title', 'hexwp' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Title', 'hexwp' ),
				'label_block' => true,
			]
		);		
		
	$repeater->add_control(
			'content', [
			'label' => __( 'Details', 'hexwp' ),
			'type'			=> \Elementor\Controls_Manager::TEXTAREA,
 			]
		);	 	
	 		 
    
 	 
	   
 	$this->add_control(
			'item',
			[
				'label' => __( 'Add Image', 'hexwp' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
 				 
			]
		); 
  	$repeater->add_control(
			'url', [
				'label' => __( 'Add URL', 'hexwp' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' =>  'https://your-link.com' ,
 			]
		);		
		
  
		
 $this->end_controls_section();

	 ?>