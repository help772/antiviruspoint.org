<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 if ( !function_exists ( "vs_perview_glider_mobile_css" )){
function vs_perview_glider_mobile_css($setting){
	$post_count = absint(wp_unslash(filter_input( INPUT_POST, 'count',  FILTER_SANITIZE_NUMBER_INT )));	
	$count =isset($post_count)?$post_count:10;	

	$layout=!empty($setting['mobile_glider_layout'])?$setting['mobile_glider_layout']:'mobile_glider_1';
	$width = !empty($setting['mobile_size']['width'])?$setting['mobile_size']['width']:'';
	$ratio = !empty($setting['mobile_size']['ratio'])?$setting['mobile_size']['ratio']:'';
	$between = !empty($setting['between'])?$setting['between']:'0';
	$between = !empty($setting['mobile_between'])?$setting['mobile_between']:$between;
	
 	$width= $width + $between;
 	/**********************************************************************
	Flex 1
	**********************************************************************/
	if($layout=='mobile_glider_1'){
    	$flex=1;
	}
 	/**********************************************************************
	Glider 2
	**********************************************************************/
	if($layout=='mobile_glider_2'){
    	$flex=2;
 	}
  
 	/**********************************************************************
	mobile 4
	**********************************************************************/
	if($layout=='mobile_glider_3'){
     	$flex=2;
 		if($count==1){
      		$flex=1;
		}
	} 	
	
 	/**********************************************************************
	Glider 7
	**********************************************************************/
	if( $layout=='mobile_glider_4'){
    	$flex=2;
  		if($count==1){
    		$ratio_sz=0.5;
     		$flex=1;
 		}
	} 	

 
 
	/**********************************************************************
	Glider 12
	**********************************************************************/
	if($layout=='mobile_glider_5' ){
    	$flex=2;
		if($count==1){
    		$ratio_sz=2;
		}
	}
	/**********************************************************************
	Glider 12
	**********************************************************************/
	if($layout=='mobile_glider_6' ){
    	$flex=2;
		if($count==3){
    		$ratio_sz=2;
		}
	}	
  
	
	if(!empty($setting['responsive_mobile'])){
	
	$wt =  $width / $flex ;
  	$ratio_sz=!empty($ratio_sz)?$ratio_sz:1;
	$css.='--vs-mob-wt:'.($wt - $between).';';	
	$css.='--vs-mob-ht:'.($wt * ($ratio * $ratio_sz * 0.01) - $between).';';
 
	}
	return $css;
}
 }