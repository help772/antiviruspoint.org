<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Text Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 if (is_admin()){
if ( !function_exists ( "vs_button_options" )){
add_filter('vs_layer_element_options', 'vs_button_options');
function vs_button_options( $element ) {
	$option = array();


 


 	$item = array(
 		'name'			=> 	__('Button','visual-slider'),
  		'id'			=> 'button',
		'img'			=> VISUALSLIDER_DIR .'admin/assets/image/layer_button.png'
		
  	); 
   
 include VISUALSLIDER_PATH . 'layer/mini/position_size.php';

 
	$option[]= array( 
		"name"			=> __('Button Text','visual-slider'),
 		"default"		=> __('Button','visual-slider'),
 		"id"			=> "button",
 		"type"			=> "textarea",
  		"group"			=>  __('Content','visual-slider'),
	); 
  
  
  
	
	$option[]= array( 
		"name"			=> __('Link','visual-slider'),
 		"id"			=> "button_link",
   		"type"			=> "text",
   		"width"			=> "100%",
 	 	"warp_width"			=> "100%",
 		"placeholder"	=> "http://",
   		"group"			=>  __('Content','visual-slider'),
	); 
 
	$option[] = array(
		"name"		=>  __('Open in a new window','visual-slider'),
		"id"		=> "button_widows",
		"type"		=> "checkbox",
  		"group"			=>  __('Content','visual-slider'),
 	);		
	
 
	 
	
	$option[]= array( 
		"name"			=> __('Alignment','visual-slider'),
 		"id"			=> "button_align",
  		"type"			=> "radio_image",
 		"default"		=> 'center',
  		"group"			=>  __('Content','visual-slider'),
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
  			"group"			=>  __('Content','visual-slider'),
			"id"			=>  "tablet_button_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
 		"responsive"			=> "tablet",
	
		"name"			=> __('Alignment on Tablet','visual-slider'),
 		"id"			=> "tablet_button_align",
  		"type"			=> "radio_image",
 		"default"		=> 'center',
  		"group"			=>  __('Content','visual-slider'),
   		"options"		=>  array( 
  			''				=> VISUALSLIDER_DIR.'admin/assets/image/none.jpg',		
  			'left'				=> VISUALSLIDER_DIR.'admin/assets/image/left.jpg',		
  			'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
			'right'				=> VISUALSLIDER_DIR.'admin/assets/image/righy.jpg',
   		),	 						
 		
  	);			 	
	 
		
 
	}	
	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){
 
	  
		$option[]= array( 
 			"responsive"	=> "mobile",
			"name"			=> __('Responsive on Mobile','visual-slider'),
  		"group"			=>  __('Content','visual-slider'),
			"id"			=>  "tablet_button_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
 		"responsive"			=> "mobile",
	
		"name"			=> __('Alignment on Tablet','visual-slider'),
 		"id"			=> "mobile_button_align",
  		"type"			=> "radio_image",
 		"default"		=> 'center',
  		"group"			=>  __('Content','visual-slider'),
   		"options"		=>  array( 
  			''				=> VISUALSLIDER_DIR.'admin/assets/image/none.jpg',		
  			'left'				=> VISUALSLIDER_DIR.'admin/assets/image/left.jpg',		
  			'center'			=> VISUALSLIDER_DIR.'admin/assets/image/center.jpg',
			'right'				=> VISUALSLIDER_DIR.'admin/assets/image/right.jpg',
   		),	 						
 		
  	);			
		 
	 
	}	 	
		
	
		
	/// *********************** Text*************************************************************************************/
 	 $option[]= array( 
		"name"			=> __('Icon','visual-slider'),
  		"id"			=> "icon",
 		"type"			=> "icon",
  		"group"			=>  __('Icon','visual-slider'),
	); 
	$option[]= array( 
		"name"			=> __('Icon Align','visual-slider'),
 		"id"			=> "icon_align",
		"fold"			=>	array(
					true => 'icon',
				),
  		"type"			=> "radio_image",
  		"group"			=>  __('Icon','visual-slider'),
   		"options"		=>  array( 
  			'left'				=> VISUALSLIDER_DIR.'admin/assets/image/left.jpg',
 			'right'				=> VISUALSLIDER_DIR.'admin/assets/image/right.jpg',
   		),	 						
 		
  	);		
	
	$option[]= array( 
		"name"			=> __('Hover','visual-slider'),
 		"id"			=> "icon_effects",
  		"group"			=>  __('Icon','visual-slider'),
 		"type"			=> "hover",
			"fold"			=>	array(
					true => 'icon',
				) ,
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
 				),
				
  				"group"			=>  __('Icon','visual-slider'),
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

				),
  				"group"			=>  __('Icon','visual-slider'),
				"type"			=> "multi_options",
 	 			"options"		=>  array( 
					array( 
						"id"			=> "first",
						"type"			=> "color_rgba",
					),
				),	
 	);		
	 
 
	$option[]= array( 
		"name"			=> __('Hover','visual-slider'),
 		"id"			=> "button_effects",
		"group"			=>  __('Style','visual-slider'),
 		"type"			=> "hover",
   		"options"		=>  array(
 			'normal' =>		 __('NORMAL','visual-slider'),		
			'hover' =>		 __('HOVER','visual-slider'),	
	 	),
	); 	 
 		
	/// *********************** Text*************************************************************************************/
	$option[]= array(
  				"name"		=>  __('Button Color','visual-slider'),
  				"id"		=> "button_color",
				"fold"			=>	array(
								'normal' => 'button_effects',
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
  				"name"		=>  __('Button Hover Color','visual-slider'),
  				"id"		=> "button_hover_color",
				"fold"			=>	array(
					'hover' => 'button_effects',
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
  				"name"		=>  __('Text Shadow','visual-slider'),
  				"id"		=> "button_shadow",
				"fold"			=>	array(
								'normal' => 'button_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"		=> "multi_options",
   		"options"		=>  vs_multi_array_options('text_shadow'),						
				
 	);	


	$option[]= array(
  				"name"		=>  __('Hover Text Shadow','visual-slider'),
  				"id"		=> "button_hover_shadow",
				"fold"			=>	array(
								'hover' => 'button_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"		=> "multi_options",
 	
 		"options"		=>  vs_multi_array_options('text_shadow'),						
				
 	);	
	
	
	
 	/***********************************boxed************************************************************************************/
	$boxed='button';
	 include VISUALSLIDER_PATH . 'layer/mini/button_boxed.php';
	 
  	 include VISUALSLIDER_PATH . 'layer/mini/button_typography.php';

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
if ( !function_exists ( "vs_button_perview" )){
add_filter('vs_layer_perview_button', 'vs_button_perview');
function vs_button_perview( $value ) {
	$option= $value['option'];
	$key= $value['key'];
 	echo '<aside id="vs_perview_layer_'.esc_attr($key).'" class="vs_draggable vs-layer-item " data-key="'.esc_attr($key).'" >';
		echo '<div class="vs-button  vs-layer-content "  >';
  	 echo '</div>';
  	 echo '<div class="vs-layer-style"> </div>';
  	 echo '</aside>';
}
}

if ( !function_exists ( "vs_button_config" )){
add_filter('vs_layer_button', 'vs_button_config');
function vs_button_config( $value ) {
  $option= $value['option'];
  $key= $value['key'];
  $global_key= $value['global_key'];
  $slide_key= $value['slide_key'];
  
 	$button_boxed = !empty($option['button_boxed'])?$option['button_boxed']:'boxed';
	if($button_boxed=='boxed'){
   		$boxed ='vs-button-boxed';	
	}else{
  		$boxed ='vs-button-none';	
	}
	$icon_align = !empty($option['icon_align'])?'vs-icon-'.vs_rtl_has($option['icon_align']).' ':'';

	$button_link = !empty( $option['button_link'])? ' href="'.$option['button_link'].'"':'';
	$target = !empty($option['button_widows']) ? 'target="_blank"' : '';
  
  
   
 //button Css
	echo '<aside class="vs-layer-'.esc_attr($key).' vs-layer-item  '.esc_attr(vs_layer_effect_class($option)).'" >';


		echo wp_kses('<a class="vs-button '.esc_attr($boxed).' vs-layer-content '.esc_attr($icon_align).'" '.$button_link.''.$target.'    >',vs_kses());
			 
			if(!empty($option['icon'])){
				vs_icon_fontfamily($option['icon']);
				  echo '<i class="'.esc_attr($option['icon']).'"></i>';
			}
			if(!empty($option['button'])){
				  echo wp_kses($option['button'],vs_kses());
			}
		 	
		echo '</a>';
		
		
  
		$css=vs_layer_position( $option);
 		$css.= vs_slider_var_align( 'txt-ag',$option,'button_align' );	
 		$css.= vs_slider_var_align( 'tab-txt-ag',$option,'tablet_button_align' );		 
 		$css.= vs_slider_var_align( 'mob-txt-ag',$option,'mobile_button_align' );			 
		$css.= vs_slider_var_2( 'btn-cr',$option,'button_color','first');		 
		$css.= vs_slider_var_2( 'btn-hv-cr',$option,'button_hover_color','first');	
		$css.= vs_slider_var_text_shadow( 'btn',$option,'button_shadow');		 
		$css.= vs_slider_var_text_shadow( 'btn-hv',$option,'button_hover_shadow','first');		
		$css.= vs_slider_var_2( 'icn-cr',$option,'icon_color','first');		 
		$css.= vs_slider_var_2( 'icn-hv-cr',$option,'icon_hover_color','first');
		 
			
 		if($button_boxed=='boxed'){
		$css.=vs_slider_var_padding( 'btn-box-pd',$option,'button_boxed_padding');
		$css.= vs_slider_var_gradient_background_color( 'btn-box',$option,'button_boxed_background');		 
		$css.= vs_slider_var_gradient_background_color( 'btn-box-hv',$option,'button_boxed_hover_background');		 
		$css.= vs_slider_var_border( 'btn-box',$option,'button_boxed_border');		 
		$css.= vs_slider_var_border( 'btn-box-hv',$option,'button_boxed_hover_border');		 
		$css.= vs_slider_var_shadow( 'btn-box',$option,'button_boxed_shadow');		 
		$css.= vs_slider_var_shadow( 'btn-box-hv',$option,'button_boxed_hover_shadow');		 
		$css.= vs_slider_var_radius( 'btn-box',$option,'button_boxed_radius');		 
		} 
	$css.=vs_slider_var( 'btn-fn-fm',$option,'button_fontfamily');
		
		$css.=vs_slider_var_unit( 'btn-fn-sz',$option,'button_font_size','px');
 		$css.=vs_slider_var( 'btn-fn-wt',$option,'button_font_weight');
		$css.=vs_slider_var( 'btn-fn-de',$option,'button_font_decoration');
		$css.=vs_slider_var( 'btn-fn-tf',$option,'button_font_transform');
		$css.=vs_slider_var_unit( 'btn-lt-sp',$option,'button_spacing','px');
		$css.=vs_slider_var( 'btn-fn-st',$option,'button_font_style');
		
	$css.=vs_slider_var_unit( 'tab-btn-fn-sz',$option,'tablet_button_font_size','px');
	$css.=vs_slider_var_unit( 'mob-btn-fn-sz',$option,'mobile_button_font_size','px');		
	$css.=vs_layer_effect( $option);
	$fontface = vs_slider_fontface($option,'button_fontfamily','button_font_weight');	
	if(!is_rtl()){
		vs_slider_fonts_family($option,'text_fontfamily','text_font_weight');
	}   
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
 	  			echo '<style>'.wp_kses(vs_slider_item_css( $css,$item).$fontface,vs_kses()).'</style>';	 

	 	echo '</aside>';
}
}
   