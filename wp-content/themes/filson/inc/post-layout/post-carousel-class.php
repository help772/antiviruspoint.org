<?php
 
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Post Carousel Class
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_post_carousel_class( $option=false ) {
	$layout =!empty($option['layout'])?$option['layout']:'list';
 	$column =!empty($option['column'])?$option['column']:3;
	$data_class='';
 	if(empty(hexwp_ismobile())){
 		$data_class.= ' hw-slider-list-warp hw_col_1_'.$column;
 		 
  	}
	 
	$data_class.= '';
	$column_default =  '200';	
	if($layout=='list'){
		$column_default = '350';
	} 
	$data_class.= !empty($option['responsive_column']) ?  ' hw_col_'.$option['responsive_column'] : ' hw_col_'.$column_default;	
 	return $data_class;
}