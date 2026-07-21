<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/********************************************************************
cssanime
*********************************************************************/ 
if ( !function_exists ( "vs_slider_query" )){
function vs_slider_query($option){

 
 
	$number = $option['number'];
	$sliders = $option['sliders'];
	$args['post_type'] = 'vs_slide';
	$args = array();
  		$args['posts_per_page']=$number;

 		
		$args['post_type'] = 'vs_slide';
 		$args['no_found_rows'] = 1;
		if(!empty($sliders)){
			$args[sanitize_key('tax_query')] =  array(
				array(
					'taxonomy' => 'vs_sliders',
					'terms' => $sliders,
					'field' => 'slug',
				)
			);
		}
 	 
   
 	return  new WP_Query($args );
} 
}
 
if ( ! class_exists( 'vs_slider_el_id' ) ) {
function vs_slider_el_id($option =false) {
   if(!empty( $option['element_id'])){
 		echo ' id="'.esc_attr($option['element_id']).'"  ' ;
   }
} 
}
if ( ! class_exists( 'vs_slider_el_cssanime' ) ) {
function vs_slider_el_cssanime($option =false) {
   if(!empty( $option['cssanime'])){
	   	global $vs_aos_style,$vs_aos_script;
		$vs_aos_style++;
		$vs_aos_script++;
		echo ' data-aos="'.esc_attr(vs_builder_rtl_has($option['cssanime'])).'" ' ;
   }
} 
}
 

/********************************************************************
Sao LightSlider
*********************************************************************/
if ( ! class_exists( 'vs_slider_lightslider' ) ) {
function vs_slider_lightslider($item,$slider_options=false) {
	global 
	$vs_lightslider_style,
	$vs_slide_script,
	$vs_lightslider_script;
	$vs_lightslider_style=true;
	$vs_slide_script=true;
	$vs_lightslider_script=true;
	
	
 	$slider_options["item"] = (int)$item;
	$slider_options["slideMove"] = 1;
 	if(is_rtl()){
 	$slider_options["rtl"] = true;
	}
	global $vs_lightslider_style,$vs_lightslider_script,$vs_slide_script;
	$vs_lightslider_style++;
	$vs_lightslider_script++;
	$vs_slide_script++;
   
 	?>
 	<div class="vs-slide-options" ><?php echo wp_kses(json_encode($slider_options),[]);?></div>
    <?php
	

 } 
}
 if ( !function_exists ( "vs_slider_rtl_left" )){
function vs_slider_rtl_left() {
 	if(is_rtl()){
		return 'right';
	}else{
		return 'left';
	}
}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																  Slider Rtl Right
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
if ( !function_exists ( "vs_slider_rtl_right" )){
function vs_slider_rtl_right() {
 	if(is_rtl()){
		return 'left';
	}else{
		return 'right';
	}
} 
}

  