<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( !function_exists ( "vs_slide_list" )){
function vs_slide_list($global_key=false,$slide=false){

  $count=0;
	if (!empty($slide)) :
	foreach($slide as $slide_key => $slide_value):$count++;
 
		$option = vs_options_decode(urldecode($slide_value['option']));
		$layer = vs_options_decode(urldecode($slide_value['layer']));
 		if($count==1){
		$frist='vs-animte-active';	
		}else{
		$frist='vs-animte-active';	
		}
		$option['key']=$slide_key;
		vs_slide($global_key,$option,$layer,$frist);
 		 
 			   
	endforeach;
	endif;
	 
  
	
		 
} 
}
if ( !function_exists ( "vs_slide" )){
function vs_slide($global_key=false,$slide=false,$layer=false,$first=false){

  
 		echo '<div class="vs-slide-item vs-slide-'.esc_attr($slide['key'].' '.$first).' " >';
  			$slide_link=!empty($slide['link'])?' href="'.esc_url($slide['link']).'" ':'';
 		 	echo '<a '.wp_kses($slide_link,vs_kses()).' class="vs-slide-cover"></a>';
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
	 
   		echo '<style>'.wp_kses(vs_slider_item_css( $css, $item),vs_kses()).'</style>';	 
  

			
	echo '</div>';
  
	
		 
} 
} 