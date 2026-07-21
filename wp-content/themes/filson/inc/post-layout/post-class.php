<?php
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Post Class
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_post_class($option){
	$layout =!empty($option['layout'])?$option['layout']:'list';

 	if($layout=='list'){
		$data_class = hexwp_post_list_class($option);
 		
	}elseif($layout=='grid'){
		$data_class = hexwp_post_grid_class($option);
 		
	}else{
		$data_class = hexwp_post_featured_class($option);
 	}
	return $data_class;
 }
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Post Responsvie Class
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_responsive_column($option=false,$default =false){
	$class='';
	$responsive_column =!empty($option['responsive_column'])? $option['responsive_column']:$default;
		
	if($responsive_column=='tab_1_mob_1'){
		$class.= 'hw_tab_1_1 hw_mob_1_1' ;
 		
	}if($responsive_column=='tab_2_mob_1'){
		$class.= 'hw_tab_1_2 hw_mob_1_1';
 		
	}elseif($responsive_column=='tab_3_mob_1'){
		$class.= 'hw_tab_1_3 hw_mob_1_1';
 		
	}elseif($responsive_column=='tab_4_mob_1'){
		$class.= 'hw_tab_1_4 hw_mob_1_1';
 			
		
	}elseif($responsive_column=='tab_2_mob_2'){
		$class.= 'hw_tab_1_2 hw_mob_1_2';
 			
	}elseif($responsive_column=='tab_3_mob_2'){
		$class.= 'hw_tab_1_3 hw_mob_1_2';
 			
	}elseif($responsive_column=='tab_4_mob_2'){
		$class.= 'hw_tab_1_4 hw_mob_1_2';
 		
	}elseif($responsive_column=='first_tab_2_mob_1'){
		$class.= 'hw_first_full hw_tab_1_2 hw_mob_1_1';
 
	}elseif($responsive_column=='first_tab_3_mob_1'){
		$class.= 'hw_first_full hw_tab_1_3 hw_mob_1_1 ';
 
	}elseif($responsive_column=='first_tab_4_mob_1'){
		$class.= 'hw_first_full hw_tab_1_3 hw_mob_1_1 ';
	
	} elseif($responsive_column=='first_tab_2_mob_2'){
		$class.= 'hw_first_full hw_tab_1_2 hw_mob_1_2';
 
	}elseif($responsive_column=='first_tab_3_mob_2'){
		$class.= 'hw_first_full hw_tab_1_3 hw_mob_1_2';
 
	}elseif($responsive_column=='first_tab_4_mob_2'){
		$class.= 'hw_first_full hw_tab_1_4 hw_mob_1_2';
	}else{
		$class.= 'hw_tab_1_1 hw_mob_1_1' ;
	}
	return $class;
 }
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Post List Class
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_list_class($option=false){

	$list_layout =$option['list_layout']? $option['list_layout']:'list_1';
 	$class=' ';
  	if($list_layout=='list_1'){
		$class.= 'hw_col_1_1 '.hexwp_responsive_column($option,'tab_1_mob_1');
 			
	}elseif($list_layout=='list_2'){
		$class.= 'hw_col_1_2 '.hexwp_responsive_column($option,'tab_2_mob_1');
 			
	}elseif($list_layout=='list_3'){
		$class.= 'hw_col_1_3 '.hexwp_responsive_column($option,'tab_3_mob_1');
 			
	}elseif($list_layout=='list_4'){
 		$class.= 'hw_col_1_4 '.hexwp_responsive_column($option,'tab_2_mob_1');
 	
	} elseif($list_layout=='list_5'){
 		$class.= 'hw_col_1_2 hw_list_5 '.hexwp_responsive_column($option,'tab_2_mob_1');
 	
	} elseif($list_layout=='list_6'){
		$class.= 'hw_col_1_3 hw_list_6 '.hexwp_responsive_column($option,'tab_3_mob_1');
	 
	}elseif($list_layout=='list_5c'){
 		$class.= 'hw_col_1_5 '.hexwp_responsive_column($option,'tab_2_mob_1');
 	
	} elseif($list_layout=='list_6c'){
 		$class.= 'hw_col_1_6 '.hexwp_responsive_column($option,'tab_2_mob_1');
 	
	}else{
		$class.= 'hw_col_1_1 '.hexwp_responsive_column($option,'tab_1_mob_1');
 			
	}
	 
	return $class;
	
}
/*
******************************************************************************************************************************************************
 
																	  Post Grid Class
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_grid_class($option=false){
	$grid_layout = !empty($option['grid_layout'])?  $option['grid_layout'] :'grid_1';
 	$class=' ';
 	if($grid_layout=='grid_1'){
		$class.= 'hw_col_1_1 '.hexwp_responsive_column($option,'tab_1_mob_1').'';
 				
	}elseif($grid_layout=='grid_2'){
		$class.= 'hw_col_1_2 '.hexwp_responsive_column($option,'tab_2_mob_2').'';
 				
	}elseif($grid_layout=='grid_3'){
		$class.= 'hw_col_1_3 '.hexwp_responsive_column($option,'tab_3_mob_1').'';
 				
	}elseif($grid_layout=='grid_4'){
		$class.='hw_col_1_4 '.hexwp_responsive_column($option,'tab_2_mob_2').'';
 				
	}elseif($grid_layout=='grid_5'){
		$class.= 'hw_col_1_5 '.hexwp_responsive_column($option,'tab_3_mob_2').'';
 				
	}elseif($grid_layout=='grid_6'){
		$class.='hw_col_1_6 '.hexwp_responsive_column($option,'tab_3_mob_2').'';
 			
	}elseif($grid_layout=='grid_7'){
		$class.= 'hw_col_1_7 '.hexwp_responsive_column($option,'tab_3_mob_2').'';
 				
	}elseif($grid_layout=='grid_8'){
		$class.='hw_col_1_8 '.hexwp_responsive_column($option,'tab_4_mob_2').'';
 				
	}elseif($grid_layout=='grid_9'){
		$class.='hw_col_1_3 hw_grid_9 '.hexwp_responsive_column($option,'first_tab_2_mob_2').'';
 				
	}elseif($grid_layout=='grid_10'){
		$class.='hw_col_1_4 hw_grid_10 '.hexwp_responsive_column($option,'first_tab_2_mob_2').'';
 				
	}elseif($grid_layout=='grid_11'){
		$class.='hw_col_1_5 hw_grid_11 '.hexwp_responsive_column($option,'first_tab_3_mob_2').'';
 		
	}elseif($grid_layout=='grid_12'){
		$class.='hw_col_1_6 hw_grid_12 '.hexwp_responsive_column($option,'first_tab_4_mob_2').'';
			
	}elseif($grid_layout=='grid_13'){
		$class.='hw_col_1_4 hw_grid_13 '.hexwp_responsive_column($option,'first_tab_2_mob_2').'';
 		
	}elseif($grid_layout=='grid_14'){
		$class.='hw_col_1_3 hw_grid_14 '.hexwp_responsive_column($option,'first_tab_2_mob_2').'';
			
	}elseif($grid_layout=='grid_15'){
		$class.='hw_col_1_4 hw_grid_15 '.hexwp_responsive_column($option,'first_tab_3_mob_2').'';
	}					
		 
	
	return $class;
	
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured Class
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_class($option,$return=false){
   	$featured_layout =!empty($option['featured_layout'])? $option['featured_layout']:'featured_1';
 	$class=' ';
	//*********************************Desktop***********************/
	if($featured_layout=='featured_1'){
		$class.= 'hw_col_1_1 '.hexwp_responsive_column($option,'tab_1_mob_1').' ';
 				
	}elseif($featured_layout=='featured_2'){
		$class.= 'hw_col_1_2 '.hexwp_responsive_column($option,'tab_2_mob_1').' ';
 				
	}elseif($featured_layout=='featured_3'){
		$class.= 'hw_col_1_3 '.hexwp_responsive_column($option,'tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_4'){
		$class.='hw_col_1_4 '.hexwp_responsive_column($option,'tab_2_mob_1').' ';
 				
	}elseif($featured_layout=='featured_5'){
		$class.= 'hw_col_1_5 '.hexwp_responsive_column($option,'tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_6'){
		$class.='hw_col_1_6 '.hexwp_responsive_column($option,'tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_7'){
		$class.= 'hw_col_1_7 '.hexwp_responsive_column($option,'tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_8'){
		$class.='hw_col_1_8 '.hexwp_responsive_column($option,'tab_4_mob_1').' ';
 				
	}elseif($featured_layout=='featured_9'){
		$class.='hw_col_1_3 hw_featured_9 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 			
	} elseif($featured_layout=='featured_10'){
		$class.='hw_col_1_4 hw_featured_10 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 			
	} elseif($featured_layout=='featured_11'){
		$class.='hw_col_1_5  hw_featured_11 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 				
	}elseif($featured_layout=='featured_12'){
		$class.='hw_col_1_6  hw_featured_12 '.hexwp_responsive_column($option,'first_tab_4_mob_1').' ';
 			
	} elseif($featured_layout=='featured_13'){
		$class.='hw_col_1_4  hw_featured_13 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 			
	}elseif($featured_layout=='featured_14'){
		$class.='hw_col_1_2 hw_featured_14 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 			
	}elseif($featured_layout=='featured_15'){
		$class.='hw_col_1_3 hw_featured_15 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 		
	}elseif($featured_layout=='featured_16'){
		$class.='hw_col_1_4 hw_featured_16 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 				
	}elseif($featured_layout=='featured_17'){
		$class.='hw_col_1_5 hw_featured_17 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_18'){
		$class.='hw_col_1_3 hw_featured_18 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 			
	}elseif($featured_layout=='featured_19'){
		$class.='hw_col_1_4 hw_featured_19 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 			
	}elseif($featured_layout=='featured_20'){
		$class.='hw_col_1_5 hw_featured_20 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
	
	}elseif($featured_layout=='featured_21'){
		$class.='hw_col_1_4  hw_featured_21 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_22'){
		$class.='hw_col_1_5  hw_featured_22 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_23'){
		$class.='hw_col_1_4 hw_featured_23 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_24'){
		$class.='hw_col_1_4 hw_featured_24 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_25'){
		$class.='hw_col_1_4 hw_featured_25 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_26'){
		$class.='hw_col_1_4 hw_featured_26 '.hexwp_responsive_column($option,'first_tab_3_mob_2').' ';
 			
	}elseif($featured_layout=='featured_27'){
		$class.='hw_col_1_3 hw_featured_27 '.hexwp_responsive_column($option,'first_tab_3_mob_1').' ';
 			
	}elseif($featured_layout=='featured_28'){
		$class.='hw_col_1_4 hw_featured_28 '.hexwp_responsive_column($option,'tab_4_mob_2').' ';
 			
	}elseif($featured_layout=='featured_29'){
		$class.='hw_col_1_2 hw_featured_29 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 				
	}elseif($featured_layout=='featured_30'){
		$class.='hw_col_1_3 hw_featured_30 '.hexwp_responsive_column($option,'first_tab_2_mob_1').' ';
 				
	}elseif($featured_layout=='featured_31'){
		$class.='hw_col_1_4 hw_featured_31 '.hexwp_responsive_column($option,'tab_3_mob_1').' ';
 				
	}elseif($featured_layout=='featured_32'){
		$class.='hw_col_1_2 hw_featured_32 '.hexwp_responsive_column($option,'tab_3_mob_1').' ';
 				
	}
 	return $class;
	
} 