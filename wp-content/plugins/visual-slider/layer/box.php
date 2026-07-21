<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Text Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 if (is_admin()){
if ( !function_exists ( "vs_box_options" )){
add_filter('vs_layer_element_options', 'vs_box_options');
function vs_box_options( $element ) {
	$option = array();


 


 	$item = array(
 		'name'			=> 	__('Box','visual-slider'),
  		'id'			=> 'box',
		'img'			=> VISUALSLIDER_DIR .'admin/assets/image/layer_box.png'
		
  	); 
	
	$default = array( 'width' =>'100', 'height' =>'100');
   
 include VISUALSLIDER_PATH . 'layer/mini/position_size.php';

  
	 
 	/***********************************boxed************************************************************************************/
$option[]= array( 
		"name"			=> __('Hover','visual-slider'),
 		"id"			=>  "box_effects",
		"group"			=>  __('Box Style','visual-slider'),
 		"type"			=> "hover",
   		"options"		=>  array(
 			'normal' =>		 __('NORMAL','visual-slider'),		
			'hover' =>		 __('HOVER','visual-slider'),		
	 	),
	); 	 
 		
 
	 
 
 
 
 
		
	$option[]= array( 
		"name"			=> __('Box Background','visual-slider'),
 		"id"			=> "box_background",
		"group"			=>  __('Box Style','visual-slider'),
 		"fold"			=>	array(
								'normal' =>   'box_effects',
							) ,
 		"default"		=> array('first'=>"rgb(2, 159, 253)"),
		"type"			=> "multi_options",
		
  		"options"		=>  array(
				array(	"id"		=> "first",
						"type"		=> "color_rgba",
				),
 				array(	"id"		=> "second",
						"type"		=> "color_rgba",
  					),
 				array(
 						"id"		=> "orientation",
						"type"		=> "select",
						"width"		=> "50px",
						"options"	=> array(
							"horizontal"		=>  esc_html__('→','visual-slider'),
							"vertical"			=>  esc_html__('↓','visual-slider'),
							"diagonal"			=>  esc_html__('↘','visual-slider'),
							"diagonal-bottom"	=>  esc_html__('↗','visual-slider'),
							"radial"			=>  esc_html__('○','visual-slider'),

				),
				),
				),
 		
		
 	); 	
	
 
	$option[]= array( 
		"name"			=> __('Box Border','visual-slider'),
 		"id"			=> "box_border",
  		"group"			=>  __('Box Style','visual-slider'),
 		"type"			=> "multi_options",
 		"width"			=> "100%",
 		"fold"			=>	array(
								'normal' => 'box_effects',
							) ,
		"options"		=>  vs_multi_array_options('layer_border'),						
	); 	
	
	$option[]= array( 
		"name"			=> __('Box Shadow','visual-slider'),
 		"id"			=> "box_shadow",
  		"group"			=>  __('Box Style','visual-slider'),
 		"type"			=> "multi_options",
 		"width"			=> "100%",
 		"fold"			=>	array(
								'normal' =>  'box_effects',
							) ,
		"options"		=>  vs_multi_array_options('layer_shadow'),						
	); 	
	
	
 
	
	
	//******* hover***********/
			
	$option[]= array( 
		"name"			=> __('Box Hover Background','visual-slider'),
 		"id"			=> "box_hover_background",
		"group"			=>  __('Box Shadow','visual-slider'),
 		"fold"			=>	array(
								'hover' =>  'box_effects',
							) ,
		"type"			=> "multi_options",
		
  		"options"		=>  array(
				array(	"id"		=> "first",
						"type"		=> "color_rgba",
				),
 				array(	"id"		=> "second",
						"type"		=> "color_rgba",
  					),
 				array(
 						"id"		=> "orientation",
						"type"		=> "select",
						"width"		=> "50px",
						"options"	=> array(
							"horizontal"		=>  esc_html__('→','visual-slider'),
							"vertical"			=>  esc_html__('↓','visual-slider'),
							"diagonal"			=>  esc_html__('↘','visual-slider'),
							"diagonal-bottom"	=>  esc_html__('↗','visual-slider'),
							"radial"			=>  esc_html__('○','visual-slider'),

				),
				),
				),
 		
		
 	); 	
	
 
	$option[]= array( 
		"name"			=> __('Box Hover Border','visual-slider'),
 		"id"			=> "box_hover_border",
  		"group"			=>  __('Box Style','visual-slider'),
 		"type"			=> "multi_options",
 		"width"			=> "100%",
 		"fold"			=>	array(
								'hover' =>   'box_effects',
							) ,
		"options"		=>  vs_multi_array_options('layer_border'),						
	); 	
	
	$option[]= array( 
		"name"			=> __('Box Hover Shadow','visual-slider'),
 		"id"			=>  "box_hover_shadow",
  		"group"			=>  __('Box Style','visual-slider'),
 		"type"			=> "multi_options",
 		"width"			=> "100%",
 		"fold"			=>	array( 'hover' => 'box_effects') ,

		"options"		=>  vs_multi_array_options('layer_shadow'),						
	); 	
	
	$option[]= array( 
		"name"			=> __('Box Radius','visual-slider'),
 		"id"			=>  "box_radius",
  		"group"			=>  __('Box Style','visual-slider'),
 		"type"			=> "multi_options",
 		"width"			=> "100%",
 		"options"		=>  vs_multi_array_options('layer_radius'),						
	); 	
	
		 
	  

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
if ( !function_exists ( "vs_box_perview" )){
add_filter('vs_layer_perview_box', 'vs_box_perview');
function vs_box_perview( $value ) {
	$option= $value['option'];
	$key= $value['key'];

	echo '<aside id="vs_perview_layer_'.esc_attr($key).'" class="vs_draggable vs-layer-item " data-key="'.esc_attr($key).'" >';
	echo '<div class="vs-box">';
	echo '</div>';
 	 
 	 echo '<div class="vs-layer-style"> </div>';
  	 echo '</aside>';
 
}
}


if ( !function_exists ( "vs_box_config" )){
add_filter('vs_layer_box', 'vs_box_config');
function vs_box_config( $value ) {
  $option= $value['option'];
  $key= $value['key'];
  $global_key= $value['global_key'];
  $slide_key= $value['slide_key'];
  
  
   
	echo '<aside class="vs-layer-'.esc_attr($key).' vs-layer-item '.esc_attr(vs_layer_effect_class($option)).'" >';


		echo '<div class="vs-box  "    >';
		echo '</div>';
			 
 		 	
 		
		
  
		$css=vs_layer_position( $option);
 
		 
 		$css.= vs_slider_var_gradient_background_color( 'box',$option,'box_background');		 
		$css.= vs_slider_var_gradient_background_color( 'box-hv',$option,'box_hover_background');		 
		$css.= vs_slider_var_border( 'box',$option,'box_border');		 
		$css.= vs_slider_var_border( 'box-hv',$option,'box_hover_border');		 
		$css.= vs_slider_var_shadow( 'box',$option,'box_shadow');		 
		$css.= vs_slider_var_shadow( 'box-hv',$option,'box_hover_shadow');		 
		$css.= vs_slider_var_radius( 'box',$option,'box_radius');		 
 	 
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
   		echo '<style>'.wp_kses(vs_slider_item_css( $css, $item),vs_kses()).'</style>';	 
	
	 	echo '</aside>';
}
} 