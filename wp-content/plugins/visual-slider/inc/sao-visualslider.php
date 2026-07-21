<?php
 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Element Item Sao visualslider
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
 if ( !function_exists ( "vs_element_item_visualslider" )){
add_filter('sao_element_item', 'vs_element_item_visualslider');
function vs_element_item_visualslider( $element ) {
 	
 	$element[] = array(
 		'name'			=> __('Visual Slider', 'visual-slider'),
 		'id'			=> 'visualslider',
		'img'			=> VISUALSLIDER_DIR .'assets/image/sao-visualslider.png'
  	); 
   
   
 	return $element;
} 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Element Item Sao visualslider Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
 if ( !function_exists ( "sao_visualslider_options" )){
add_filter('sao_element_options_visualslider', 'vs_visualslider_options');
function vs_visualslider_options( $option ) {
	$option = array();
  	$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			        'numberposts'      => 99,
 
			'post_type' => 'visualslider',
			'number' => 'visualslider',
			'post_status' => 'publish'
		); 
		 
	$options_page = array();
	$options_page_obj =get_posts($page_args); 
 
	if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $rezapage) {
			$options_page[$rezapage->ID] = $rezapage->post_title;
		}
	} 
	
	$option[]= array( 
		"name"			=> __( 'Select Slider', 'visual-slider' ),
 		"id"			=> "sliders",
   		"type"			=> "select",
		'options'		=> $options_page,	
	);	 
	
	$option[]= array( 
		"name"			=> esc_html__('Element ID','visual-slider'),
 		"id"			=> "element_id",
 		"group"			=>  esc_html__('Attribute','visual-slider'),
		"desc"			=>  esc_html__('Enter Column ID ,','visual-slider').'<a href="https://www.w3schools.com/tags/att_global_id.asp">'.esc_html__('Learn more','visual-slider').'</a>',
		"type"			=> "text",
		 
	);
 
	$option[]= array( 
		"name"			=> __('Padding','visual-slider'),
 		"id"			=> "padding",
  		"group"			=>  __('Layout','visual-slider'),
		"default"		=>   sao_builder_default_padding(),
 		"type"			=> "multi_options",
 		"options"		=>  sao_multi_array_options('margin'),						
 	);
	 		 
 
 	$option[]= array( 
		"name"			=> esc_html__('CSS Animation','visual-slider'),
 		"id"			=> "cssanime",
		"desc"			=>  esc_html__('Select type of animation if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.','visual-slider'),
 		"group"			=>  esc_html__('Layout','visual-slider'),
		"type"			=> "select",
 		"options"		=>  sao_array_options('cssanime'),						
 	);

	$option[]= array( 
		"name"			=> esc_html__('Element Custom Class','visual-slider'),
 		"id"			=> "custom_class",
 		"group"			=>  esc_html__('Attribute','visual-slider'),
		"desc"			=>  esc_html__('Enter Class ,','visual-slider'),
		"type"		=> "text",

	);		
  	include SAOPAGE_PATH . 'element/mini/responsive.php';

 	return $option;
} 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Perview visualslider Config
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
 add_filter('sao_builder_perview_visualslider', 'vs_perview_visualslider_config');
function vs_perview_visualslider_config( $args ) {
	$key = $args['key'];
	$option = $args['option'];
	$output='';
	$css='';
	
	$sliders = !empty($option['sliders'])?$option['sliders']:'';

	$post_title =!empty($sliders)? get_the_title($sliders):'';
		
	if(!empty($post_title)){
		$output.='<h4  style="font-size:30px;text-align: center; width:100%; margin: 0px;">'.$post_title .'</h4>'; 
	}
		
	$setting_json = get_post_meta($sliders, 'vs_setting_json', true);
	if(!empty($setting_json)){
		$setting= vs_options_array_row($setting_json);
		$type = !empty($setting['type'])?$setting['type']:'slider';
		if($type=='slider'){
			$output.='<'.esc_attr('img').' src="'.VISUALSLIDER_DIR.'admin/assets/image/slider.jpg">'; 
		}
		if($type=='sinlge'){
			$output.='<'.esc_attr('img').' src="'.VISUALSLIDER_DIR.'admin/assets/image/image.jpg">'; 
		}
		if($type=='glider'){
					$glider_layout = !empty($setting['glider_layout'])?$setting['glider_layout']:'glider_1';

 			$output.='<'.esc_attr('img').' src="'.VISUALSLIDER_DIR.'admin/assets/image/glider/'.$glider_layout.'.jpg">'; 
		}
 	}
	$css.= '.sao-element-'.$key.' {text-align: center;width:100%;}'; 
   	$return['css']= $css;
	$return['output']= $output;
	return $return;
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Perview visualslider Config
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
 if ( !function_exists ( "sao_builder_visualslider" )){
add_filter('sao_builder_visualslider', 'vs_visualslider_config');
function vs_visualslider_config( $args ,$out = false,$out_css=false) {
			$option = $args['option'];
	$key = $args['key'];
	$output='';
	$css ='';
	
	if(sao_element_show($option)=='show'){
	ob_start(); 
 		
 	$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';
	
		$classes=array(
		'sao-el-'.$key,
		sao_element_show($option,true),
		$custom_class,
   
	);
      
	?>
 
    
 	 <aside <?php echo wp_kses(sao_el_id($option),vs_kses());?> class="<?php echo esc_attr(join( ' ', $classes ));?>"    >
		 <?php 
 		    // Define output and open element div.
			if(!empty($option['sliders'])){
				  vs_slider_config($option['sliders']);
			}
 			?>
		</aside>
        
        <?php
	
	
	
	
	
	$item = '.sao-el-'.$key.''; 
	
	 
 	$item_css = sao_element_padding($option);
   	$css.=sao_item_css($item_css,$item);
  	
   	$return['output']= ob_get_clean();
  	$return['css']= $css;
  	$return['emptybefore']= true;
  	$return['emptyafter']= true;
	return $return;	
	}
}
}  