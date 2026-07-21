<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'vs_layer' ) ) {
function vs_layer($global_key=false,$slide_key=false,$layer=false){

  
 	echo '<div class="vs-layer-warp">';
	echo '<div class="vs-layer-list">';
 
  
		if (!empty($layer)) :
		foreach($layer as $layer_key => $layer_value):
				
				$layer_value['global_key']=$global_key;
				$layer_value['slide_key']=$slide_key;
				$layer_value['key']=$layer_key;
                $id= $layer_value['id'];
				if(has_filter('vs_layer_'.$id)) {
					apply_filters('vs_layer_'.$id, $layer_value) ;	
				}
			   
		endforeach;
		endif; 
	
	echo '</div>';	
  	echo '</div>';	
  
	
		 
} 
}
if ( ! class_exists( 'vs_layer_effect_class' ) ) {
function vs_layer_effect_class( $option =false ) {
 	
	$class='';
	
	
		$class.= !empty($option['align']['horizontal'])?' vs-layer-'.vs_rtl_has($option['align']['horizontal']).' ':' vs-layer-'.vs_rtl_has('left').' ';
		$class.= !empty($option['align']['vertical'])?' vs-layer-'.$option['align']['vertical'].' ':' vs-layer-top ';
  
 
 		$class.= !empty($option['tablet_align']['horizontal'])? ' vs-layer-tablet-'.vs_rtl_has($option['tablet_align']['horizontal']).' ':'';
		$class.= !empty($option['tablet_align']['vertical'])?' vs-layer-tablet-'.$option['tablet_align']['vertical'].' ':'';
 		
		
		$class.= !empty($option['mobile_align']['horizontal'])? ' vs-layer-mobile-'.vs_rtl_has($option['mobile_align']['horizontal']).' ':'';
		$class.= !empty($option['mobile_align']['vertical'])?' vs-layer-mobile-'.$option['mobile_align']['vertical'].' ':'';	
 		
		$tablet_display = !empty($option['tablet_display'])?$option['tablet_display']:'';
			if($tablet_display === 'hide'){
				$class.=' vs-tablet-hide ';
			}
			$mobile_display = !empty($option['mobile_display'])?$option['mobile_display']:'';
			if($mobile_display === 'hide'){
				$class.=' vs-mobile-hide ';
			}
			$effect = !empty($option['effect'] )? $option['effect']:'';
			$initial = !empty($option['initial'] )? $option['initial']:'top';
			 
			if($effect=='move'){
				$initial = !empty($option['initial'] )? $option['initial']:'top';
					if( $initial == 'left'){ 
						$initial_class =vs_rtl_has('left');
					} elseif(  $initial == 'right'){ 
						$initial_class =vs_rtl_has('right');
					}else{
						$initial_class =$initial;
					}
				
 				
 				$class.='vs-effect-move-'.$initial_class;
			}elseif($effect=='fade'){
 				$class.='vs-effect-fade';
			}elseif($effect=='scale'){
 				$class.='vs-effect-scale';
			}
	
		return $class;			
 
}
} 