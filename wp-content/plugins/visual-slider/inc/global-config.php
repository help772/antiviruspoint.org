<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'vs_global_config' ) ) {
function vs_global_config($global_key=false,$setting=false,$slide=false){

 	$pager='';
	$type = !empty($setting['type'])?$setting['type']:'slider';
 	if($type=='single'){
		vs_global_single_config($global_key,$setting,$slide);		
	}elseif($type=='glider'){
		vs_global_glider_config($global_key,$setting,$slide);		
	} else{
		vs_global_slider_config($global_key,$setting,$slide);		
	}
}
	
}
 