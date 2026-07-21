<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	  Post Grid Masonry Class
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_masonry_class($option=false, $return=false){
	$column = !empty($option['column'])?  $option['column'] :3;
	$class=' ';
	$attr='';
 	//*********************************Desktop***********************/
	if(empty(hexwp_ismobile())){
		$class.= ' hw_col_1_'.$column.' ';
		$attr.=' data-col="'.$column.'" ';
 	}
  	//*********************************Mobile***********************/
	$responsive_column =!empty($option['responsive_column'])? $option['responsive_column']:'tab_3_mob_2';
 	if($responsive_column=='tab_2_mob_1'){
		$class.= 'hw_tab_1_2 ';
		$attr.= ' data-tab="2" ';
	
	}elseif($responsive_column=='tab_3_mob_1'){
		$class.= 'hw_tab_1_3 ';
		$attr.= ' data-tab="3" ';
 		
	}elseif($responsive_column=='tab_4_mob_1'){
		$class.= 'hw_tab_1_4 ';
		$attr.= ' data-tab="4" ';
 		
	}elseif($responsive_column=='tab_2_mob_2'){
		$class.= 'hw_tab_1_2 hw_mob_1_2 ';
		$attr.= ' data-tab="2" data-mob="2" ';
 		
	}elseif($responsive_column=='tab_3_mob_2'){
		$class.= 'hw_tab_1_3 hw_mob_1_2 ';
		$attr.= ' data-tab="3" data-mob="2" ';
			
	}elseif($responsive_column=='tab_4_mob_2'){
		$class.= 'hw_tab_1_4 hw_mob_1_2';
		$attr.= ' data-tab="4" data-mob="2" ';
	} 
	return $return=='attr'?$attr:$class;
	
}