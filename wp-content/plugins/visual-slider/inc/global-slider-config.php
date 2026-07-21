<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'vs_global_slider_config' ) ) {
function vs_global_slider_config($global_key=false,$setting=false,$slide=false){

 	$pager='';
	$type = !empty($setting['type'])?$setting['type']:'slider';
 
 
	$classes=array(
 		'vs-custom-slider',
 		'vs-slider',
 		'vs-visualslider',
		'vs-global-'.esc_attr($global_key),
 		'vs-has-desktop',
 		
    	!empty($setting['responsive_tablet'])?'vs-has-tablet':'vs-not-tablet',
   		!empty($setting['responsive_mobile'])?'vs-has-mobile':'vs-not-mobile',
   		!empty($setting['full_width'])?'vs-full-width':'vs-not-width',
 
 	);
 		$classes[]=!empty($setting['pager'])?(!empty($setting['pager_position']) ? 'vs-pager-'.$setting['pager_position']:'vs-pager-top'):'';
		$classes[]=	!empty($setting['pager'])?(!empty($setting['pager_visibility']) ? 'vs-pager-'.$setting['pager_visibility']:'vs-pager-hover'):'';	 
		$classes[]=	!empty($setting['arrows'])?(!empty($setting['arrows_position']) ? 'vs-arrows-'.$setting['arrows_position']:'vs-arrows-side'):'';
		$classes[]=!empty($setting['arrows'])?(!empty($setting['arrows_visibility']) ? 'vs-arrows-'.$setting['arrows_visibility']:'vs-arrows-hover'):'';
		$classes[]=	!empty($setting['timer'])? 'vs-timer':'';
  
 		echo '<aside class="'.esc_attr(join( ' ', $classes )).'"  >';
  
        echo '<div class="vs-slide-list-warp" >';
			echo '<div class="vs-slide-list" >';
					vs_slide_list($global_key,$slide);
			echo '</div>';
			echo '<div class="vs-arrow-warp"><a class="vs-arrow-prev"></a><a class="vs-arrow-next"></a></div>';		
 		echo '</div>';
        
       
         $slider_options = array( ); 	
         
		$slider_options['controls']=!empty($setting['arrows']) ? $setting['arrows'] : '';
		$slider_options['mode']= !empty($setting['effect']) ? $setting['effect'] : 'fade';
		$slider_options['speed']= !empty($setting['speed']) ? (int)$setting['speed'] : 2000;
		$slider_options['pause']= !empty($setting['pause']) ? (int)$setting['pause'] : 5000;;	
		$slider_options['auto']= !empty($setting['auto']) ? true :'';	
			 
		$slider_options['loop']=!empty($setting['loop']) ? true : false;
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
	
 	$css.= vs_slider_var_unit('sp',$setting,'speed','ms');	 
	$css.= vs_slider_var_unit('pu',$setting,'pause','ms');	 
  
	if(!empty($setting['timer'])){
		$css.= vs_slider_var_gradient_background_color('ti',$setting,'timer_color');
	}			
  	$css.= vs_pager_css($setting);
	$css.= vs_arrows_css($setting);
	$css.= vs_global_css($setting);
 
	
  	echo '<div class="vs-style">';
	vs_icon_enqueue('1');
	
	$item='.vs-global-'.$global_key;
	echo '<style>'.wp_kses(vs_slider_item_css( $css, $item),vs_kses()).'</style>';	 
	echo '<div>';
 
	  
	echo '</aside>';
 }
} 