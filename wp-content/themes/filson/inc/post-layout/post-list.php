<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Post List
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_list($option=false){
	$list_layout = hexwp_data($option,'list_layout','list_1');
	
 	//*********************************Desktop***********************/
	if(empty(hexwp_ismobile())){ 
		if(	$list_layout=='list_1' || 
			$list_layout=='list_2' ||
			$list_layout=='list_3' ||
			$list_layout=='list_4' ){
			hexwp_post_list_main($option);
		}elseif($list_layout=='list_5' || $list_layout=='list_6' ){
			hexwp_post_list_5($option);
			 
			
		}
	}
 	//*********************************Mobile***********************/
 	if(!empty(hexwp_ismobile())){ 
 		$default='';
		 if(
			$list_layout=='list_5' ||
			$list_layout=='list_6'  
		){ 
			$default='first_tab_2_mob_2';
		} 
		$responsive_column =!empty($option['responsive_column'])? $option['responsive_column']:$default;
  		if(	$responsive_column=='first_tab_2_mob_1'||
			$responsive_column=='first_tab_3_mob_1'||
			$responsive_column=='first_tab_4_mob_1'||
			$responsive_column=='first_tab_2_mob_2'||
			$responsive_column=='first_tab_3_mob_2'||
			$responsive_column=='first_tab_4_mob_2' 
		){
			hexwp_post_list_first($option);
 		
		}else{
 			hexwp_post_list_main($option);
 		} 		 
	}


}
add_action('wp_ajax_nopriv_hexwp_post_list', 'hexwp_post_list');
add_action('wp_ajax_hexwp_post_list', 'hexwp_post_list');


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Post List 1
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_list_main($option =false) {
  	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
 		  hexwp_module_1($option);
  	endwhile; 
	endif; 
} 
add_action('wp_ajax_nopriv_hexwp_post_list_main', 'hexwp_post_list_main');
add_action('wp_ajax_hexwp_post_list_main', 'hexwp_post_list_main'); 

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid Slider 2
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_list_slider_2($option =false) {
 	
	$count=0;
  	$query = hexwp_query($option);
	  	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
 		$count++;
 		if($count==1){
			echo'<div class="hw-item">';
		}
 		hexwp_module_1($option); 
		if($count==2){
			echo'</div>';
			$count=0;
 		}

  	endwhile; 
	endif; 
	
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid Slider 3
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_list_slider_3($option =false) {
 	
	$count=0;
  	$query = hexwp_query($option);
	  	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
 		$count++;
 		if($count==1){
			echo'<div class="hw-item">';
		}
 		hexwp_module_1($option); 
		if($count==3){
			echo'</div>';
			$count=0;
 		}

  	endwhile; 
	endif; 
	
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Post List 5
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_list_5($option) { 

 	$count = hexwp_data($option,'ajaxcount')?10:0; 
  	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
		if($count==1){
			 hexwp_module_1($option,'hw-aw');
  		}else{
 			hexwp_module_1($option,'hw-second',true,true,true);
 			
		}
	endwhile; 
	endif; 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Post List First
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_list_first($option =false) {
	
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
	
  	$query = hexwp_query($option);
 	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
 		if($count==1){
			hexwp_module_1($option,'hw-aw');
  		}else{
 			hexwp_module_1($option);
 			
		}
  	endwhile; 
	endif; 
} 
