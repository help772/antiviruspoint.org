<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 if ( !function_exists ( "vs_perview_glider_tablet_css" )){
function vs_perview_glider_tablet_css($setting){
	$post_count = absint(wp_unslash(filter_input( INPUT_POST, 'count',  FILTER_SANITIZE_NUMBER_INT )));	
	$count =isset($post_count)?$post_count:10;	
 

	$layout=!empty($setting['tablet_glider_layout'])?$setting['tablet_glider_layout']:'tablet_glider_1';
	$width = !empty($setting['tablet_size']['width'])?$setting['tablet_size']['width']:'';
	$ratio = !empty($setting['tablet_size']['ratio'])?$setting['tablet_size']['ratio']:'';
	$between = !empty($setting['between'])?$setting['between']:'0';
	$between = !empty($setting['tablet_between'])?$setting['tablet_between']:$between;
  	$width= $width + $between;
 	/**********************************************************************
	Flex 1
	**********************************************************************/
	    	$flex=1;

	if($layout=='tablet_glider_1'){
    	$flex=1;
	}
 	/**********************************************************************
	Glider 2
	**********************************************************************/
	if($layout=='tablet_glider_2'){
    	$flex=2;
 	}
 	/**********************************************************************
	Glider 3
	**********************************************************************/
	if($layout=='tablet_glider_3'){
    	$flex=3;
  	}
 
 	/**********************************************************************
	Tablet 4
	**********************************************************************/
	if($layout=='tablet_glider_4'){
     	$flex=2;
 		if($count==1){
      		$flex=1;
		}
	} 	
	
 	/**********************************************************************
	Glider 7
	**********************************************************************/
	if( $layout=='tablet_glider_5'){
    	$flex=2;
  		if($count==1){
    		$ratio_sz=0.5;
     		$flex=1;
 		}
	} 	

	/**********************************************************************
	Glider 10
	**********************************************************************/
	if( $layout=='tablet_glider_6'){
    	$flex=3;
  		if($count==1){
    		$ratio_sz=0.5;
  			$flex=1.5;
 			
		}
	}
	/**********************************************************************
	Glider 11
	**********************************************************************/
	if( $layout=='tablet_glider_7'){
    	$flex=3;
		if($count==1){
     		$flex=1.5;
		}
	} 	
	/**********************************************************************
	Glider 8
	**********************************************************************/
	if($layout=='tablet_glider_8' ){
    	$flex=2;
		if($count==1){
    		$ratio_sz=2;
		}
	}
 	/**********************************************************************
	Glider 9
	**********************************************************************/
	if($layout=='tablet_glider_9' ){
    	$flex=2;
		if($count==3){
    		$ratio_sz=2;
		}
	}
 
	/**********************************************************************
	Glider 10
	**********************************************************************/
	if($layout=='tablet_glider_10' ){
    	$flex=6;
 		
		if($count==1||$count==2){
     		$flex=2;
  		}else{
     		$flex=3;
 		}
		 
	}
	/**********************************************************************
	Glider 10
	**********************************************************************/
	if( $layout=='tablet_glider_11'){
    	$flex=3;
  		if($count==2){
    		$ratio_sz=0.5;
  			$flex=1.5;
 			
		}
	}
	/**********************************************************************
	Glider 12
	**********************************************************************/
	if( $layout=='tablet_glider_12'){
    	$flex=3;
		if($count==1 ){
      		$ratio_sz=2;
 		}
  		if($count==2){
    		$ratio_sz=0.5;
  			$flex=1.5;
 			
		}
	}
	/**********************************************************************
	Glider 20
	**********************************************************************/
	if($layout=='tablet_glider_13'){
    	$flex=3;
 		if($count==1||$count==4){
     		$ratio_sz=2;
  		}
		 
	}
	/**********************************************************************
	Glider 21
	**********************************************************************/
	if($layout=='tablet_glider_14'){
    	$flex=3;
		
		if($count==1 ){
      		$ratio_sz=2;
 		}
		 
	} 
	/**********************************************************************
	Glider 22
	**********************************************************************/
	if($layout=='tablet_glider_15'){
     	$flex=3;
 		if($count==3 ){
      		$ratio_sz=2;
 		}
		 
	} 	 
	
		/**********************************************************************
	Glider 12
	**********************************************************************/
	if( $layout=='tablet_glider_16'){
    	$flex=3;
	
  		if($count==5){
    		$ratio_sz=2;
  			
		}
	}
	$css='';
	if(!empty($setting['responsive_tablet'])){
	
		$wt =  ($width / $flex );
		$ratio_sz=!empty($ratio_sz)?$ratio_sz:1;
		$css.='--vs-tab-wt:'.($wt - $between).';';	
		$css.='--vs-tab-ht:'.($wt * ($ratio * $ratio_sz * 0.01) - $between).';';
	 
		 	
	}
 	return $css;
}
 }