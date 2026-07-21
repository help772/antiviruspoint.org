<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( ! function_exists( 'vs_perview_layer_size' ) ) {
function vs_perview_layer_size($value=false){
	$css='';
	if(!empty($value['horizontal'])){
		$css.='--vs-lr-rt:'.$value['horizontal'].'%;';
	}
	if(!empty($value['vertical'])){
		$css.='--vs-lr-tp:'.$value['vertical'].'%;';
	}	
	if(!empty($value['width'])){
		$css.='--vs-lr-wt:'.$value['width'].'%;';
	}	
	if(!empty($value['height'])){
		$css.='--vs-lr-ht:'.$value['height'].'%;';
	}	
 		$css.='--vs-lr-lt:auto;';
 
	
	
	return $css;	
 
}
}