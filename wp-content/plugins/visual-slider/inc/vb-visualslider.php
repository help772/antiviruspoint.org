<?php
 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Element Item Sao visualslider
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
 
	
if( ! function_exists( 'vs_vb_visualslider_options' ) ) {
add_filter('vb_element_options', 'vs_vb_visualslider_options');
function vs_vb_visualslider_options($global) {
 	
	$global['visualslider'] = 
	[
		'name'			=> 	esc_html__('Visual Slider','visual-slider'),
		'img'			=> VISUALSLIDER_DIR .'assets/image/vb-visualslider.png',
 		'type'			=>  'element',
		'group'				=>__( 'General', 'visual-slider' ), 
   	]; 
 	$option=[];
	
 
	$option = array();
  	$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			        'numberposts'      => 99,
 
			'post_type' => 'visualslider',
			'number' => 'visualslider',
			'post_status' => 'publish'
		); 
		 
	$options_page = array(''=>__('None','visual-slider'));
	$options_page_obj =get_posts($page_args); 
 
	if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $rezapage) {
			$options_page[$rezapage->ID] = $rezapage->post_title;
		}
	} 
	
	$option[ "sliders"]= array( 
		"name"			=> __( 'Select Slider', 'visual-slider' ),
    		"type"			=> "select",
		'options'		=> $options_page,	
	);	 
	
 
	$option['element']=
	[
  		"type"					=> "total_control",
		"preview"				=> 	true,
  		"options"				=> "element", 
  	];	 
	  					
    
	$global['visualslider']['options']=$option;
     return $global;
} 
}
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Perview visualslider Config
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
 if ( !function_exists ( "vs_vb_visualslider_config" )){
 add_filter('vb_element_visualslider', 'vs_vb_visualslider_config');

function vs_vb_visualslider_config( $args) {
	extract(vb_extract($args));
  	if(!empty($data['sliders'])){
	  vs_slider_config($data['sliders']);
	}
 		  
	 
}
}  