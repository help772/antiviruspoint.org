<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( !function_exists ( "vs_glide_list" )){
function vs_glide_list($global_key=false,$slide=false){

  $count=0;
	if (!empty($slide)) :
	foreach($slide as $slide_key => $slide_value):$count++;
	
		$option = vs_options_decode(urldecode(wp_kses($slide_value['option'],vs_kses())));
		$layer = vs_options_decode(urldecode(wp_kses($slide_value['layer'],vs_kses())));
 		$option['key']=$slide_key;
		vs_glide($global_key,$option,$layer);
 		 
 			   
	endforeach;
	endif;
	 
  
	
		 
} 
}
if ( !function_exists ( "vs_glide" )){
function vs_glide($global_key=false,$slide=false,$layer=false,$first=false){

  
 		echo '<div class="vs-slide-item vs-animte-active vs-slide-'.esc_attr($slide['key']).' " >';
 		echo '<div class="vs-slide-inner" >';
  			$slide_link=!empty($slide['link'])?' href="'.esc_url($slide['link']).'" ':'';
 		 	echo wp_kses('<a '.$slide_link.' class="vs-slide-cover"></a>',vs_kses());
 			 vs_layer($global_key,$slide['key'],$layer);
 
 
 			$css =vs_slider_var_gradient_background_color('sl',$slide,'background_color');
 			$css.=vs_slider_background_image('sl',$slide,'background_image');
 			$css.=vs_slider_var_background_image_position('sl',$slide,'background_image_position');
 			$css.=vs_slider_var_background_image_position('sl-tab',$slide,'tablet_background_image_position');
 			$css.=vs_slider_var_background_image_position('sl-mob',$slide,'mobile_background_image_position');
			$item='';
			if(!empty($global_key)){
				$item.='.vs-global-'.$global_key.' '; 
			}
			if(!empty($slide['key'])){
				$item.='.vs-slide-'.$slide['key'].' '; 
			}
	 
  			echo '<style>'.wp_kses(vs_slider_item_css( $css,$item),vs_kses()).'</style>';	 
  

			
	echo '</div>';
	echo '</div>';
  
	
		 
} 
}
  