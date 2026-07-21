<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'vs_global_slider_config' ) ) {
function vs_global_glider_config($global_key=false,$setting=false,$slide=false){

 	$pager='';
	$type = !empty($setting['type'])?$setting['type']:'slider';
 
 
	$classes=array(
 		'vs-custom-slider',
 		'vs-glider',
 		'vs-visualslider',
		'vs-global-'.esc_attr($global_key),
 		'vs-has-desktop',
 		
    	!empty($setting['glider_layout'])?'vs-'.$setting['glider_layout']:'vs-glider_1',
    	!empty($setting['tablet_glider_layout'])?'vs-'.$setting['tablet_glider_layout']:'vs-tablet_glider_1',
    	!empty($setting['mobile_glider_layout'])?'vs-'.$setting['mobile_glider_layout']:'vs-mobile_glider_1',
    	!empty($setting['responsive_tablet'])?'vs-has-tablet':'vs-not-tablet',
   		!empty($setting['responsive_mobile'])?'vs-has-mobile':'vs-not-mobile',
   		!empty($setting['full_width'])?'vs-full-width':'vs-not-width',
 
 	);
 
 		echo '<aside class="'.esc_attr(join( ' ', $classes )).'"  >';
  
        echo '<div class="vs-slide-list-warp" >';
			echo '<div class="vs-slide-list" >';
					vs_glide_list($global_key,$slide);
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
	$css.= vs_slider_var_2('ratio',$setting,'size','ratio');
	$css.= vs_slider_var('gp',$setting,'between');
  	if(!empty($setting['responsive_tablet'])){
 	$css.= vs_slider_var_2('tab-wt',$setting,'tablet_size','width');
 	$css.= vs_slider_var_2('tab-ratio',$setting,'tablet_size','ratio');
	}
	$css.= vs_slider_var('tab-gp',$setting,'tablet_between');
	if(!empty($setting['responsive_mobile'])){
	$css.= vs_slider_var_2('mob-wt',$setting,'mobile_size','width');
 	$css.= vs_slider_var_2('mob-ratio',$setting,'mobile_size','ratio');
	
	}
	$css.= vs_slider_var('mob-gp',$setting,'mobile_between');

 	$css.= vs_slider_var_unit('sp',$setting,'speed','ms');	 
	$css.= vs_slider_var_unit('pu',$setting,'pause','ms');	 
  
 		

	$css.= vs_global_css($setting);
 
	
  	echo '<div class="vs-style">';
	vs_icon_enqueue('1');
	
	$item='.vs-global-'.$global_key;
	echo '<style>'.wp_kses(vs_slider_item_css( $css, $item),vs_kses()).'</style>';	 
	echo '<div>';
 
	  
	echo '</aside>';
 }
}
if ( ! class_exists( 'vs_glider_layout_config' ) ) {

function vs_glider_layout_config($setting){
	$css='';
 	$css.=vs_glider_layout_desktop_config($setting);
 	$css.=vs_glider_layout_tablet_config($setting);
 	$css.=vs_glider_layout_mobile_config($setting);
		
	 
 	return $css;
}
}
 
 