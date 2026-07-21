<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Text Options
 
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*
 if (is_admin()){
if ( !function_exists ( "vs_icon_options" )){
add_filter('vs_layer_element_options', 'vs_icon_options');
function vs_icon_options( $element ) {
	$option = array();


 


 	$item = array(
 		'name'			=> 	__('Icon','visual-slider'),
  		'id'			=> 'icon',
		'img'			=> VISUALSLIDER_DIR .'admin/assets/image/layer_icon.png'
		
  	); 
   
 include VISUALSLIDER_PATH . 'layer/mini/position_size.php';

 
	$option[]= array( 
		"name"			=> __('Icon','visual-slider'),
 		"default"		=> 'fa-star',
 		"id"			=> "icon",
		  		"group"			=>  __('Content','visual-slider'),

 		"type"			=> "icon",
 	); 
  
	
	$option[]= array( 
		"name"			=> __('Link','visual-slider'),
 		"id"			=> "icon_link",
   		"type"			=> "text",
   		"width"			=> "100%",
		 	 	"warp_width"			=> "100%",

 		"placeholder"	=> "http://",
   		"group"			=>  __('Content','visual-slider'),
	); 
 
	$option[] = array(
		"name"		=>  __('Open in a new window','visual-slider'),
		"id"		=> "icon_widows",
		"type"		=> "checkbox",
  		"group"			=>  __('Content','visual-slider'),
 	);		
	
 
	
	 
 
	
	
	$option[]= array( 
 		"responsive"	=> "desktop",
 		"name"			=> __('Alignment','visual-slider'),
 		"id"			=> "icon_align",
  		"type"			=> "radio_image",
 		"default"		=> 'center',
  		"group"			=>  __('Content','visual-slider'),
   		"options"		=>  array( 
  			'left'				=> VISUALSLIDER_DIR.'admin/assets/image/'.vs_rtl_has('left').'.jpg',
			'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
			'right'				=> VISUALSLIDER_DIR.'admin/assets/image/'.vs_rtl_has('right').'.jpg',
   		),	 						
 		
  	);		
	
 		if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_tablet',  FILTER_VALIDATE_BOOLEAN )))){
		$option[]= array( 
 			"responsive"	=> "tablet",
			"name"			=> __('Responsive on Tablet','visual-slider'),
  			"group"			=>  __('Align','visual-slider'),
			"id"			=>  "tablet_icon_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
 		"responsive"			=> "tablet",
	
		"name"			=> __('Alignment on Tablet','visual-slider'),
 		"id"			=> "tablet_icon_align",
  		"type"			=> "radio_image",
 		"default"		=> 'center',
  		"group"			=>  __('Content','visual-slider'),
   		"options"		=>  array( 
  			''				=> VISUALSLIDER_DIR.'admin/assets/image/none.jpg',		
  			'left'				=> VISUALSLIDER_DIR.'admin/assets/image/'.vs_rtl_has('left').'.jpg',		
  			'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
			'right'				=> VISUALSLIDER_DIR.'admin/assets/image/'.vs_rtl_has('right').'.jpg',
   		),	 						
 		
  	);			 	
	 
		
 
	}	
		if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){

 
	  
		$option[]= array( 
 			"responsive"	=> "mobile",
			"name"			=> __('Responsive on Tablet','visual-slider'),
  		"group"			=>  __('Content','visual-slider'),
			"id"			=>  "tablet_icon_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
 		"responsive"	=> "mobile",
 		"name"			=> __('Alignment on Tablet','visual-slider'),
 		"id"			=> "mobile_icon_align",
  		"type"			=> "radio_image",
 		"default"		=> 'center',
  		"group"			=>  __('Content','visual-slider'),
   		"options"		=>  array( 
  			''				=> VISUALSLIDER_DIR.'admin/assets/image/none.jpg',		
  			'left'				=> VISUALSLIDER_DIR.'admin/assets/image/'.vs_rtl_has('right').'.jpg',		
  			'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
			'right'				=> VISUALSLIDER_DIR.'admin/assets/image/'.vs_rtl_has('left').'.jpg',
   		),	 						
 		
  	);			
		 
	 
	}	 	
		
	$option[]= array( 
		"name"			=> __('Hover','visual-slider'),
 		"id"			=> "icon_effects",
		"group"			=>  __('Content','visual-slider'),
 		"type"			=> "hover",
   		"options"		=>  array(
 			'normal' =>		 __('NORMAL','visual-slider'),		
			'hover' =>		 __('HOVER','visual-slider'),	
	 	),
	); 	 
 		
	
	$option[]= array(
  				"name"		=>  __('Icon Color','visual-slider'),
  				"id"		=> "icon_color",
				"fold"			=>	array(
								'normal' => 'icon_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"			=> "multi_options",
 	 			"options"		=>  array( 
					array( 
						"id"			=> "first",
						"type"			=> "color_rgba",
					),
				),		
 	);
	
	$option[]= array(
  				"name"		=>  __('Icon Hover Color','visual-slider'),
  				"id"		=> "icon_hover_color",
				"fold"			=>	array(
					'hover' => 'icon_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"			=> "multi_options",
 	 			"options"		=>  array( 
					array( 
						"id"			=> "first",
						"type"			=> "color_rgba",
					),
				),	
 	);	
 
	$option[]= array(
  				"name"		=>  __('Icon Shadow','visual-slider'),
  				"id"		=> "icon_shadow",
				"fold"			=>	array(
								'normal' => 'icon_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"		=> "multi_options",
   		"options"		=>  vs_multi_array_options('text_shadow'),						
				
 	);	


	$option[]= array(
  				"name"		=>  __('Icon Hover Shadow','visual-slider'),
  				"id"		=> "icon_hover_shadow",
				"fold"			=>	array(
								'hover' => 'icon_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"		=> "multi_options",
 	
 		"options"		=>  vs_multi_array_options('text_shadow'),						
				
 	);	
	
$option[]= array( 
		"responsive"	=> "desktop",
 		"name"			=> __('Icon Size','visual-slider'),
 		"id"			=> "icon_font_size",
		"group"			=>  __('Style','visual-slider'),
		"type"			=> "number", 	 	
		"unit"			=> "px", 	
		"max"			=> "200", 		
		 	
 	 
		 
  	); 	


  	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_tablet',  FILTER_VALIDATE_BOOLEAN )))){
		$option[]= array( 
 			"responsive"	=> "tablet",
			"name"			=> __('Responsive on Tablet','visual-slider'),
   				"group"			=>  __('Style','visual-slider'),
			"id"			=>  "tablet_typography_heading",
			"type"			=> "heading",
		);
		$option[]= array( 
 			"responsive"	=> "tablet",
		
			"name"			=> __('Text Size on Tablet','visual-slider'),
			"id"			=> "tablet_icon_font_size",
   				"group"			=>  __('Style','visual-slider'),
			"type"			=> "number", 	 	
			"unit"			=> "px", 	
			"max"			=> "200", 		
				
		 
			 
		);  	 	
	 
		
 
	}	
	  
  	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){
		$option[]= array( 
 			"responsive"	=> "mobile",
		
			"name"			=> __('Responsive on Mobile','visual-slider'),
   				"group"			=>  __('Style','visual-slider'),
			"id"			=>  "mobile_typography_heading",
			"type"			=> "heading",
		);
		$option[]= array( 
 			"responsive"	=> "mobile",
		
			"name"			=> __('Text Size on Mobile','visual-slider'),
			"id"			=> "mobile_icon_font_size",
   				"group"			=>  __('Style','visual-slider'),
			"type"			=> "number", 	 	
			"unit"			=> "px", 	
			"max"			=> "200", 		
				
		 
			 
		);  	 	
	 
	 
		 
	 
	}	 
	/***********************************boxed************************************************************************************/
		$boxed='icon';

 	include VISUALSLIDER_PATH . 'layer/mini/boxed.php';
 
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
if ( !function_exists ( "vs_icon_perview" )){
add_filter('vs_layer_perview_icon', 'vs_icon_perview');
function vs_icon_perview( $value ) {
 $option= $value['option'];
  $key= $value['key'];
  
  	 
	////////////////////////////////////////////////////////////OUTPUT////////////////////////////////////////////////////////////////////////////////////////	  

	echo '<aside id="vs_perview_layer_'.esc_attr($key).'" class="vs_draggable vs-layer-item vs-layer-center" data-key="'.esc_attr($key).'" >';
		echo '<div class="vs-icon vs-layer-content"  >';
  	 echo '</div>';
	 
	 
 	 echo '<div class="vs-layer-style"> </div>';
	 
 	 echo '</aside>';	
}
}

if ( !function_exists ( "vs_icon_config" )){
add_filter('vs_layer_icon', 'vs_icon_config');
function vs_icon_config( $value ) {
  $option= $value['option'];
  $key= $value['key'];
  $global_key= $value['global_key'];
  $slide_key= $value['slide_key'];
	
 	$icon_boxed = !empty($option['icon_boxed'])?'vs-icon-'.$option['icon_boxed']:'vs-icon-none';

	 
	//text Css
	echo '<aside class="vs-layer-'.esc_attr($key).' vs-layer-item  '.esc_attr(vs_layer_effect_class($option)).'" >';


		echo '<div class="vs-icon '.esc_attr($icon_boxed).' vs-layer-content ">';
			if(!empty($option['icon_link'])){
			$target = !empty($option['icon_widows']) ? 'target="_blank"' : '';
 			 echo wp_kses('<a href="'.$option['icon_link'].'" '.$target.' >',vs_kses());	
			}
			if(!empty($option['icon'])){
				vs_icon_fontfamily($option['icon']);
				  echo '<i class="'.esc_attr($option['icon']).'"></i>';
			}
			 
		if(!empty($option['icon_link'])){
			 echo '</a>';	
			}	
		echo '</div>';
		
	$css=vs_layer_position( $option);

	$css.= vs_slider_var( 'icn-ag',$option,'icon_align' );		 
 		$css.= vs_slider_var_align( 'tab-icn-ag',$option,'tablet_icon_align' );		 
 		$css.= vs_slider_var_align( 'mob-icn-ag',$option,'mobile_icon_align' );	
	$css.= vs_slider_var_2( 'icn-cr',$option,'icon_color','first');		 
	$css.= vs_slider_var_2( 'icn-hv-cr',$option,'icon_hover_color','first');	
  	$css.= vs_slider_var_text_shadow( 'icn',$option,'icon_shadow');		 
	$css.= vs_slider_var_text_shadow( 'icn-hv',$option,'icon_hover_shadow','first');		
	$css.=vs_slider_var_unit( 'icn-fn-sz',$option,'icon_font_size','px');
	$css.=vs_slider_var_unit( 'tab-icn-fn-sz',$option,'tablet_icon_font_size','px');
	$css.=vs_slider_var_unit( 'mob-icn-fn-sz',$option,'mobile_icon_font_size','px');
 
 $boxed = !empty($option['icon_boxed'])?$option['icon_boxed']:'none';
  	if($boxed=='boxed'){	
 		$css.=vs_slider_var_padding( 'icn-box-pd',$option,'icon_boxed_padding');
		$css.= vs_slider_var_gradient_background_color( 'icn-box',$option,'icon_boxed_background');		 
		$css.= vs_slider_var_gradient_background_color( 'icn-box-hv',$option,'icon_boxed_hover_background');		 
		$css.= vs_slider_var_border( 'icn-box',$option,'icon_boxed_border');		 
		$css.= vs_slider_var_border( 'icn-box-hv',$option,'icon_boxed_hover_border');		 
		$css.= vs_slider_var_shadow( 'icn-box',$option,'icon_boxed_shadow');		 
		$css.= vs_slider_var_shadow( 'icn-box-hv',$option,'icon_boxed_hover_shadow');		 
		$css.= vs_slider_var_radius( 'icn-box',$option,'icon_boxed_radius');		 
  	} 
	
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