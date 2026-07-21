<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Text Options
 
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*
 if (is_admin()){
if ( !function_exists ( "vs_image_options" )){

add_filter('vs_layer_element_options', 'vs_image_options');
function vs_image_options( $element ) {
	$option = array();
  
 	$item = array(
 		'name'			=> 	__('Image','visual-slider'),
  		'id'			=> 'image',
		'img'			=> VISUALSLIDER_DIR .'admin/assets/image/layer_image.png'
		
  	); 
   
 include VISUALSLIDER_PATH . 'layer/mini/position_size.php';

 
	$option[]= array( 
		"name"			=> __('Image','visual-slider'),
  		"id"			=> "image",
		"default"       => VISUALSLIDER_DIR.'admin/assets/image/image.png',
		"group"			=>  __('Image','visual-slider'),
  		"type"			=> "image",
 	); 
   
   
	
	$option[]= array( 
		"name"			=> __('Alignment','visual-slider'),
 		"id"			=> "image_align",
  		"type"			=> "radio_image",
 		"default"		=> 'center',
		"group"			=>  __('Image','visual-slider'),
   		"options"		=>  array( 
  			'left'				=> VISUALSLIDER_DIR.'admin/assets/image/left.jpg',
			'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
			'right'				=> VISUALSLIDER_DIR.'admin/assets/image/right.jpg',
   		),	 						
 		
  	);		
	
 		if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_tablet',  FILTER_VALIDATE_BOOLEAN )))){
 
		$option[]= array( 
 			"responsive"	=> "tablet",
			"name"			=> __('Responsive on Tablet','visual-slider'),
		"group"			=>  __('Image','visual-slider'),
			"id"			=>  "tablet_image_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
			"responsive"			=> "tablet",
 			"name"			=> __('Alignment on Tablet','visual-slider'),
			"id"			=> "tablet_image_align",
			"type"			=> "radio_image",
			"default"		=> 'center',
		"group"			=>  __('Image','visual-slider'),
			"options"		=>  array( 
				''				=> VISUALSLIDER_DIR.'admin/assets/image/none.jpg',		
				'left'				=> VISUALSLIDER_DIR.'admin/assets/image/left.jpg',		
				'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
				'right'				=> VISUALSLIDER_DIR.'admin/assets/image/right.jpg',
			),	 						
			
		);			 	
	 
		
 
	}	
 		if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){
 
	  
		$option[]= array( 
 			"responsive"	=> "mobile",
			"name"			=> __('Responsive on Mobile','visual-slider'),
			"group"			=>  __('Image','visual-slider'),

			"id"			=>  "tablet_image_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
			"responsive"			=> "mobile",
		
			"name"			=> __('Alignment on Mobile','visual-slider'),
			"id"			=> "mobile_image_align",
			"type"			=> "radio_image",
			"default"		=> 'center',
			"group"			=>  __('Image','visual-slider'),

			"options"		=>  array( 
				''				=> VISUALSLIDER_DIR.'admin/assets/image/none.jpg',		
				'left'				=> VISUALSLIDER_DIR.'admin/assets/image/left.jpg',		
				'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
				'right'				=> VISUALSLIDER_DIR.'admin/assets/image/right.jpg',
			),	 						
 		);			
		 
	 
	}	 	
	
	 include VISUALSLIDER_PATH . 'layer/mini/effect.php';

			 
	$item['options']=$option;
	$element[]=$item;
    return $element;
} 
 }
 }
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Text Config
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( !function_exists ( "vs_image_perview" )){
add_filter('vs_layer_perview_image', 'vs_image_perview');
function vs_image_perview( $value ) {
  $option= $value['option'];
  $key= $value['key'];
  
  	 
	////////////////////////////////////////////////////////////OUTPUT////////////////////////////////////////////////////////////////////////////////////////	  

	echo '<aside id="vs_perview_layer_'.esc_attr($key).'" class="vs_draggable vs-layer-item " data-key="'.esc_attr($key).'" >';
		echo '<div class="vs-image vs-layer-content"  ></div>';
 	 
 	 echo '<div class="vs-layer-style"> </div>';
  	 echo '</aside>';
 
}
}
if ( !function_exists ( "vs_image_config" )){
add_filter('vs_layer_image', 'vs_image_config');
function vs_image_config( $value ) {
  $option= $value['option'];
  $key= $value['key'];
  $global_key= $value['global_key'];
  $slide_key= $value['slide_key'];
  
 	echo '<aside class="vs-layer-'.esc_attr($key).' vs-layer-item '.esc_attr(vs_layer_effect_class($option)).'" >';


		echo '<div class="vs-image vs-layer-content" >';
			 
			if(!empty($option['image'])){
				
			if(is_numeric($option['image'])){
					$attachment_image = wp_get_attachment_image_src($option['image'],'full');
					$value_url=!empty($attachment_image[0])?$attachment_image[0]:'';
				}else{
					$value_url=$option['image'];
				}
				
 				  echo '<'.esc_attr('img').' src="'.esc_url($value_url).'">';
			}
 		 	
		echo '</div>';
	$css=vs_layer_position( $option);
	$css.= vs_slider_var_align( 'img-ag',$option,'image_align' );		 
	$css.=vs_layer_effect( $option);
	
	$item='';
	if(!empty($global_key)){
		$item.='.vs-global-'.$global_key.' '; 
	}
	if(!empty($slide_key)){
		$item.='.vs-slide-'.$slide_key.' '; 
	}
	if(!empty($key)){
		$item.='.vs-layer-'.$key.' '; 
	}	  
 	 	  			echo '<style>'.wp_kses(vs_slider_item_css( $css,$item),vs_kses()).'</style>';	 

	 	echo '</aside>';		
		 
}
} 