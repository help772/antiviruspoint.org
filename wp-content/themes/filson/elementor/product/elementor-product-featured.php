<?php
	  
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Prodcut Layout
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 	
$this->start_controls_section(
	'img_featured',
	[
		'label' => __('Featured Image','hexwp'),
	]
);	

$this->add_control(
	'image_featured',
	[
		'label'			=> __('Featured Image','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SWITCHER ,
 	]
); 	 
 

$this->add_control(
	'image_featured_query',
	[
 		'label'			=> __('Featured Image Type','hexwp'),
		'type' 			=> \Elementor\Controls_Manager::SELECT ,
		'conditions' => [
			'relation' => 'and',
			'terms' => [
 				[
					'name' => 'image_featured',
					'operator' => '==',
					'value' => 'yes'
				] 
			]
		],
		"options"		=>   [
  			"slider"	=> __('Visual Slider','hexwp'),
			"upload"	=> __('Upload Image','hexwp'),
			"link"		=> __('Image Src','hexwp'),
					 
		]	
	]
);      
$this->add_control(
	'sliders',
	[
		'label'			=> esc_html__('Sliders','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
 		'conditions' => [
			'relation' => 'and',
			'terms' => [
 				[
					'name' => 'image_featured_query',
					'operator' => '==',
					'value' => 'slider'
				],
					[
					'name' => 'image_featured',
					'operator' => '==',
					'value' => 'yes'
				]
			]
		],
  		"options"		=>  hexwp_category_array_options('sao_slider'),						
	]
); 
	 
$this->add_control(
	'image_featured_id',
	[
		'label'			=> __('Upload Image','hexwp'),
		'type'			=> \Elementor\Controls_Manager::MEDIA ,
  		'conditions' => [
			'relation' => 'and',
			'terms' => [
 				[
					'name' => 'image_featured_query',
					'operator' => '==',
					'value' => 'upload'
				],
					[
					'name' => 'image_featured',
					'operator' => '==',
					'value' => 'yes'
				]
			]
		],
		
		
 	]
); 
	  
$this->add_control(
	'image_featured_image_size',
	[
		'label'			=> __('Featured Image Size','hexwp'),
		'type'			=> \Elementor\Controls_Manager::SELECT ,
   		'conditions' => [
			'relation' => 'and',
			'terms' => [
 				[
					'name' => 'image_featured_query',
					'operator' => '==',
					'value' => 'upload'
				],
					[
					'name' => 'image_featured',
					'operator' => '==',
					'value' => 'yes'
				]
			]
		],
		
 		"options"		=>  hexwp_all_image_sizes(),						
	]
);


$this->add_control(
	'image_featured_image_link',
	[
		'label'			=> __('Src Featured Image','hexwp'),
		'type'			=> \Elementor\Controls_Manager::TEXT , 
   		'conditions' => [
			'relation' => 'and',
			'terms' => [
 				[
					'name' => 'image_featured_query',
					'operator' => '==',
					'value' => 'link'
				],
					[
					'name' => 'image_featured',
					'operator' => '==',
					'value' => 'yes'
				]
			]
		],
		
 		"options"		=>  hexwp_all_image_sizes(),						
	]
);
	
$this->add_control(
	'image_featured_image_url',
	[
		'label'			=> __('Url Featured Image','hexwp'),
		'type'			=> \Elementor\Controls_Manager::TEXT ,
		'conditions' => [
			'relation' => 'and',
			'terms' => [
 				[
					'name' => 'image_featured_query',
					'operator' => '==',
					'value' =>  ['link','upload']
				],
					[
					'name' => 'image_featured',
					'operator' => '==',
					'value' => 'yes'
				]
			]
		],
			

  		"options"		=>  hexwp_all_image_sizes(),						
	]
);
	
 
	 	
$this->add_control(
	'image_featured_width',
	[
		'label'			=> 'Width Featured Image' ,
		'type'			=> \Elementor\Controls_Manager::SELECT ,
 		'conditions' => [
			'relation' => 'and',
			'terms' => [
 		 
					[
					'name' => 'image_featured',
					'operator' => '==',
					'value' => 'yes'
				]
			]
		],
		
 		"options"		=>   array( 
 			'1_2'					=> '1/2', 
			'1_3_2_3'				=> '1/3', 
			'1_4_3_4'				=> '1/4', 
			'1_5_4_5'				=> '1/5', 
 			'1_6_5_6'				=> '1/6', 
			'1_7_6_7'				=> '1/7', 
    		'1_8_7_8'				=> '1/8',  
   			 
		),							
	]
); 
 $this->end_controls_section();
	
	?>