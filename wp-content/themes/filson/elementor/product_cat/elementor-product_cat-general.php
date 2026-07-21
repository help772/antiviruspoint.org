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

$this->add_control(
	'number',
	[
		'label'			=> __( 'Categories Count', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::NUMBER,
		'default'		=> '4',
	]
);
$this->add_control(
	'source',
	[
		'label'			=> __( 'Source', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'options'		=> array(
			'' 						=> __( 'Show All', 'hexwp' ),
			'by_id' 				=> __( 'Manual Selection', 'hexwp' ),
			'by_parent' 			=> __( 'By Parent', 'hexwp' ),
			'current_subcategories' => __( 'Current Subcategories', 'hexwp' ),
		),
 	]
);

$categories = get_terms( 'product_cat' );

$options = array();
	foreach ( $categories as $category ) {
		$options[ $category->term_id ] = $category->name;
}

$this->add_control(
	'categories',
	[
		'label'			=> __( 'Categories', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SELECT2,
		'options'		=> $options,
		'default'		=> [],
		'label_block'	=> true,
		'multiple'		=> true,
		'condition'		=> array('source' => 'by_id'),
	]
);

$parent_options = [ '0' => __( 'Only Top Level', 'hexwp' ) ] + $options;
$this->add_control(
	'parent',
	[
		'label'			=> __( 'Parent', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'default'		=> '0',
		'options'		=> $parent_options,
		'condition'		=> array('source' => 'by_parent'),
	]
);

$this->add_control(
	'hide_empty',
	[
		'label'			=> __( 'Hide Empty', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SWITCHER,
		'default'		=> '',
		'label_on'		=> 'Hide',
		'label_off'		=> 'Show',
	]
);

$this->add_control(
	'orderby',
	[
		'label'			=> __( 'Order By', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'default'		=> 'name',
		'options'		=> array(
			'name'			=> __( 'Name', 'hexwp' ),
			'slug'			=> __( 'Slug', 'hexwp' ),
			'description'	=> __( 'Description', 'hexwp' ),
			'count'			=> __( 'Count', 'hexwp' ),
		)
	]
);

$this->add_control(
	'order',
	[
		'label'			=> __( 'Order', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SELECT,
		'default'		=> 'asc',
		'options'		=> array(
			'asc'			=> __( 'ASC', 'hexwp' ),
			'desc'			=> __( 'DESC', 'hexwp' ),
		),
	]
);

$this->add_control(
	'hide_title',
	[
		'label'			=> __( 'Hide Categories Ttile', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SWITCHER,
		'default'		=> '',
		'label_on'		=> 'Hide',
		'label_off'		=> 'Show',
	]
);
   
$this->add_control(
	'hide_count',
	[
		'label'			=> __( 'Hide Categories Count', 'hexwp' ),
		'type'			=> \Elementor\Controls_Manager::SWITCHER,
		'default'		=> '',
		'label_on'		=> 'Hide',
		'label_off'		=> 'Show',
	]
);
   
  



 $this->end_controls_section();

