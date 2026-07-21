<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'vs_global_slider_config' ) ) {
function vs_global_single_config($global_key=false,$setting=false,$slide=false){

 	$pager='';
	$type = !empty($setting['type'])?$setting['type']:'slider';
 
 
	$classes=array(
 		'vs-custom-slider',
 		'vs-single',
 		'vs-visualslider',
		'vs-global-'.esc_attr($global_key),
 		'vs-has-desktop',
 		
    	!empty($setting['responsive_tablet'])?'vs-has-tablet':'vs-not-tablet',
   		!empty($setting['responsive_mobile'])?'vs-has-mobile':'vs-not-mobile',
   		!empty($setting['full_width'])?'vs-full-width':'vs-not-width',
 
 	);
 	 
  
 		echo '<aside class="'.esc_attr(join( ' ', $classes )).'"  >';
  
        echo '<div class="vs-slide-list-warp" >';
			echo '<div class="vs-slide-list  vs-mode-fade" >';
					vs_single_list($global_key,$slide);
			echo '</div>';
  		echo '</div>';
        
       
         $slider_options = array( ); 	
      
		$slider_options['desktop_width']=!empty($setting['size']['width']) ? $setting['size']['width'] : false;
		$slider_options['desktop_height']=!empty($setting['size']['height']) ? $setting['size']['height'] : false;
		if(!empty($setting['responsive_tablet'])){
			 $slider_options['tablet_width']=!empty($setting['tablet_size']['width']) ? $setting['tablet_size']['width'] : false;
			$slider_options['tablet_height']=!empty($setting['tablet_size']['height']) ? $setting['tablet_size']['height'] : false;
		}
			 
		if(!empty($setting['responsive_mobile'])){
			 $slider_options['mobile_width']=!empty($setting['mobile_size']['width']) ? $setting['mobile_size']['width'] : false;
			$slider_options['mobile_height']=!empty($setting['mobile_size']['height']) ? $setting['mobile_size']['height'] : false;
		}
		vs_slider_lightslider('1',$slider_options);
		 

		
 	 
	$css = vs_slider_var_2('wt',$setting,'size','width');
	$css.= vs_slider_var_2('ht',$setting,'size','height');
	if(!empty($setting['responsive_tablet'])){
 	$css.= vs_slider_var_2('tab-wt',$setting,'tablet_size','width');
	$css.= vs_slider_var_2('tab-ht',$setting,'tablet_size','height');
	}
		
	if(!empty($setting['responsive_mobile'])){
	$css.= vs_slider_var_2('mob-wt',$setting,'mobile_size','width');
	$css.= vs_slider_var_2('mob-ht',$setting,'mobile_size','height');
	}
  
   
	$css.= vs_global_css($setting);
 		

  	echo '<div class="vs-style">';
	vs_icon_enqueue('1');
	
	$item='.vs-global-'.$global_key;
	echo '<style>'.wp_kses(vs_slider_item_css( $css, $item),vs_kses()).'</style>';	 
	echo '<div>';
	if(empty($setting['disable_typography'])){
	wp_enqueue_style( 'vs_fontfamily',VISUALSLIDER_DIR .'assets/css/fontfamily.css',array(),'1'); 
	}
	  
	echo '</aside>';
 }
}
