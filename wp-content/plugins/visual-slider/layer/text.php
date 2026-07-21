<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Text Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 if (is_admin()){
if ( !function_exists ( "vs_text_options" )){

add_filter('vs_layer_element_options', 'vs_text_options');
function vs_text_options( $element ) {
	$option = array();


 


 	$item = array(
 		'name'			=> 	__('Text','visual-slider'),
  		'id'			=> 'text',
		'img'			=> VISUALSLIDER_DIR .'admin/assets/image/layer_text.png'
		
  	); 
   
	 include VISUALSLIDER_PATH . 'layer/mini/position_size.php';

 
	$option[]= array( 
		"name"			=> __('Text','visual-slider'),
 		"default"		=> __('Lorem ipsum dolor sit amet, consectetur adipiscing elit','visual-slider'),
 		"id"			=> "text",
  		"type"			=> "textarea",
	 	 	"warp_width"			=> "100%",
	
  		"group"			=>  __('Content','visual-slider'),
	); 
  
	
	$option[]= array( 
		"name"			=> __('Link','visual-slider'),
 		"id"			=> "text_link",
   		"type"			=> "text",
		 		"placeholder"	=> "http://",
 	 	"warp_width"			=> "100%",
   		"width"			=> "100%",
    		"group"			=>  __('Content','visual-slider'),
	); 
 
	$option[] = array(
		"name"		=>  __('Open in a new window','visual-slider'),
		"id"		=> "text_widows",
		"type"		=> "checkbox",
  		"group"			=>  __('Content','visual-slider'),
 	);		
	
 
	
	$option[]= array( 
		"name"			=> __('HTML Tag','visual-slider'),
 		"id"			=> "text_html_tag",
   		"group"			=>  __('Content','visual-slider'),
		"type"			=> "select", 	
 		"default"		=> '',
		"options"		=>  
			array( 
				""			=> __('Default','visual-slider'),			
				"h1"			=> 'H1',			
				"h2"			=> 'H2',			
				"h3"			=> 'H3',	 	
				"h4"			=> 'H4',	 	
				"h4"			=> 'H5',	 	
				"h6"			=> 'H6',	 	
				"div"			=> 'div',	 	
				"span"			=> 'span',	 	
				"p"				=> 'p',	 	
    			),
    	); 
	
 
	
	
	$option[]= array( 
 		"responsive"			=> "desktop",
	
		"name"			=> __('Alignment','visual-slider'),
 		"id"			=> "text_align",
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
			"name"			=> __('Responsive on tablet','visual-slider'),
  			"group"			=>  __('Content','visual-slider'),
			"id"			=>  "tablet_text_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
			"responsive"			=> "tablet",
 			"name"			=> __('Alignment on Tablet','visual-slider'),
			"id"			=> "tablet_text_align",
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
  		if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){
 
	  
		$option[]= array( 
 			"responsive"	=> "mobile",
			"name"			=> __('Responsive on Mobile','visual-slider'),
  			"group"			=>  __('Content','visual-slider'),
			"id"			=>  "tablet_text_align_heading",
			"type"			=> "heading",
		);
		
		$option[]= array( 
			"responsive"			=> "mobile",
		
			"name"			=> __('Alignment on Mobile','visual-slider'),
			"id"			=> "mobile_text_align",
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
		"name"			=> __('Icon Alignment','visual-slider'),
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
 		"id"			=> "text_effects",
		"group"			=>  __('Style','visual-slider'),
 		"type"			=> "hover",
   		"options"		=>  array(
 			'normal' =>		 __('NORMAL','visual-slider'),		
			'hover' =>		 __('HOVER','visual-slider'),	
	 	),
	); 	 
 		
	
	$option[]= array(
  				"name"		=>  __('Text Color','visual-slider'),
  				"id"		=> "text_color",
				"fold"			=>	array(
								'normal' => 'text_effects',
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
  				"name"		=>  __('Text Hover Color','visual-slider'),
  				"id"		=> "text_hover_color",
				"fold"			=>	array(
					'hover' => 'text_effects',
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
  				"id"		=> "text_shadow",
				"fold"			=>	array(
								'normal' => 'text_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"		=> "multi_options",
   				"options"		=>  vs_multi_array_options('text_shadow'),						
				
 	);	


	$option[]= array(
  				"name"		=>  __('Text Hover Shadow','visual-slider'),
  				"id"		=> "text_hover_shadow",
				"fold"			=>	array(
								'hover' => 'text_effects',
				) ,
   				"group"			=>  __('Style','visual-slider'),
				"type"		=> "multi_options",
  				"options"		=>  vs_multi_array_options('text_shadow'),						
				
 	);	

	/***********************************boxed************************************************************************************/
	$boxed='text';
	 include VISUALSLIDER_PATH . 'layer/mini/boxed.php';
	 include VISUALSLIDER_PATH . 'layer/mini/text_typography.php';

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
if ( !function_exists ( "vs_text_perview" )){

add_filter('vs_layer_perview_text', 'vs_text_perview');
function vs_text_perview( $value ) {
   $key= $value['key'];
  
  	 

	echo '<aside id="vs_perview_layer_'.esc_attr($key).'" class="vs_draggable vs-layer-item  " data-key="'.esc_attr($key).'"    >';
		echo '<div class="vs-text vs-layer-content"  >';
	 
 	 echo '</div>';
   	 echo '<div class="vs-layer-style"></div>';
	 
 	 echo '</aside>';
	////////////////////////////////////////////////////////////End////////////////////////////////////////////////////////////////////////////////////////	  
     
   
   
    
 }
}
if ( !function_exists ( "vs_text_config" )){

add_filter('vs_layer_text', 'vs_text_config');
function vs_text_config( $value ) {
  $option= $value['option'];
  $key= $value['key'];
  $global_key= $value['global_key'];
  $slide_key= $value['slide_key'];
  
   $text_html_tag = !empty($option['text_html_tag'])?$option['text_html_tag']:'div';
	$text_boxed = !empty($option['text_boxed'])?$option['text_boxed']:'';
	if($text_boxed=='boxed'){
   		$boxed ='vs-text-boxed';	
	}else{
  		$boxed ='vs-text-none';	
	}
	$icon_align = !empty($option['icon_align'])?'vs-icon-'.vs_rtl_has($option['icon_align']).' ':'';
 	
  
 	//text Css
	echo '<aside class="vs-layer-'.esc_attr($key).' vs-layer-item  '.esc_attr(vs_layer_effect_class($option)).' vs-layer-text">';
 
		echo wp_kses('<'.$text_html_tag.' class="vs-text vs-layer-content '.$boxed.' '.$icon_align.'">',vs_kses());
			if(!empty($option['text_link'])){
				  	$target = !empty($option['text_widows']) ? 'target="_blank"' : '';

			 echo wp_kses('<a href="'.$option['text_link'].'" '.$target.' >',vs_kses());	
			}
			if(!empty($option['icon'])){
				vs_icon_fontfamily($option['icon']);
				  echo '<i class="'.esc_attr($option['icon']).'"></i>';
			}
			if(!empty($option['text'])){
				  echo wp_kses($option['text'],vs_kses());
			}
		if(!empty($option['text_link'])){
			 echo '</a>';	
			}	
		echo wp_kses( '</'.$text_html_tag.'>',vs_kses());
		
		
  
		$css=vs_layer_position( $option);
		
 		$css.= vs_slider_var_align( 'txt-ag',$option,'text_align' );		
  		$css.= vs_slider_var_align( 'tab-txt-ag',$option,'tablet_text_align' );		 
 		$css.= vs_slider_var_align( 'mob-txt-ag',$option,'mobile_text_align' );	
 			 
		$css.= vs_slider_var_2( 'txt-cr',$option,'text_color','first');		 
		$css.= vs_slider_var_2( 'txt-hv-cr',$option,'text_hover_color','first');	
		$css.= vs_slider_var_text_shadow( 'txt',$option,'text_shadow');		 
		$css.= vs_slider_var_text_shadow( 'txt-hv',$option,'text_hover_shadow','first');		
		$css.= vs_slider_var_2( 'icn-cr',$option,'icon_color','first');		 
		$css.= vs_slider_var_2( 'icn-hv-cr',$option,'icon_hover_color','first');
		 
			
 		if($text_boxed=='boxed'){
		$css.=vs_slider_var_padding( 'txt-box-pd',$option,'text_boxed_padding');
		$css.= vs_slider_var_gradient_background_color( 'txt-box',$option,'text_boxed_background');		 
		$css.= vs_slider_var_gradient_background_color( 'txt-box-hv',$option,'text_boxed_hover_background');		 
		$css.= vs_slider_var_border( 'txt-box',$option,'text_boxed_border');		 
		$css.= vs_slider_var_border( 'txt-box-hv',$option,'text_boxed_hover_border');		 
		$css.= vs_slider_var_shadow( 'txt-box',$option,'text_boxed_shadow');		 
		$css.= vs_slider_var_shadow( 'txt-box-hv',$option,'text_boxed_hover_shadow');		 
		$css.= vs_slider_var_radius( 'txt-box',$option,'text_boxed_radius');		 
		} 
	$css.=vs_slider_var( 'txt-fn-fm',$option,'text_fontfamily');
		
		$css.=vs_slider_var_unit( 'txt-fn-sz',$option,'text_font_size','px');
		$css.=vs_slider_var_unit( 'txt-li-ht',$option,'text_line_height','em');
		$css.=vs_slider_var( 'txt-fn-wt',$option,'text_font_weight');
		$css.=vs_slider_var( 'txt-fn-de',$option,'text_font_decoration');
		$css.=vs_slider_var( 'txt-fn-tf',$option,'text_font_transform');
		$css.=vs_slider_var_unit( 'txt-lt-sp',$option,'text_spacing','px');
		$css.=vs_slider_var( 'txt-fn-st',$option,'text_font_style');
		
	$css.=vs_slider_var_unit( 'tab-txt-fn-sz',$option,'tablet_text_font_size','px');
	$css.=vs_slider_var_unit( 'tab-txt-li-ht',$option,'tablet_text_line_height','em');	
	
	
	$css.=vs_slider_var_unit( 'mob-txt-fn-sz',$option,'mobile_text_font_size','px');
	$css.=vs_slider_var_unit( 'mob-txt-li-ht',$option,'mobile_text_line_height','em');			   
		   
		
	$css.=vs_layer_effect( $option);
	$fontface = vs_slider_fontface($option,'text_fontfamily','text_font_weight');	   
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
   