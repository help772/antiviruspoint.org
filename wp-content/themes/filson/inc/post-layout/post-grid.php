<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid 
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_grid($option){
 	$grid_layout = hexwp_data($option,'grid_layout','grid_1');
 	//*********************************Desktop***********************/
 	if(empty(hexwp_ismobile())){ 
		if(	$grid_layout=='grid_1' || 
			$grid_layout=='grid_2' ||
			$grid_layout=='grid_3' ||
			$grid_layout=='grid_4' ||
			$grid_layout=='grid_5' || 
			$grid_layout=='grid_6' ||
			$grid_layout=='grid_7' ||
 			$grid_layout=='grid_8') hexwp_post_grid_main($option);
			
		if( $grid_layout=='grid_9'  ||
			$grid_layout=='grid_10' ||
			$grid_layout=='grid_11' ||
			$grid_layout=='grid_12' ||
			$grid_layout=='grid_13' ) hexwp_post_grid_9($option);
			
		if($grid_layout=='grid_14') hexwp_post_grid_14($option);
		if($grid_layout=='grid_15') hexwp_post_grid_15($option);
	} 

	//*********************************Mobile***********************/
	if(!empty(hexwp_ismobile())){ 
	
		$default='';
		 if(
			$grid_layout=='grid_9' ||
			$grid_layout=='grid_10' ||
			$grid_layout=='grid_11' ||
			$grid_layout=='grid_12' ||
			$grid_layout=='grid_13' ||
			$grid_layout=='grid_14' ||
			$grid_layout=='grid_15'
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
			hexwp_post_grid_first($option);
 		}else{
			hexwp_post_grid_main($option);
 		} 		 
	}		 
}
add_action('wp_ajax_nopriv_hexwp_post_grid', 'hexwp_post_grid');
add_action('wp_ajax_hexwp_post_grid', 'hexwp_post_grid');
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid Main
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_post_grid_main($option =false) {
  	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
  		  hexwp_module_2($option);
   	endwhile; 
	endif; 
} 
add_action('wp_ajax_nopriv_hexwp_post_grid_main', 'hexwp_post_grid_main');
add_action('wp_ajax_hexwp_post_grid_main', 'hexwp_post_grid_main');

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid Slider 2
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_grid_slider_2($option =false) {
 	
	$count=0;
  	$query = hexwp_query($option);
	  	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
 		$count++;
 		if($count==1){
			echo'<div class="hw-item">';
		}
 		hexwp_module_2($option); 
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
function hexwp_post_grid_slider_3($option =false) {
 	
	$count=0;
  	$query = hexwp_query($option);
	  	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
 		$count++;
 		if($count==1){
			echo'<div class="hw-item">';
		}
 		hexwp_module_2($option); 
		if($count==3){
			echo'</div>';
			$count=0;
 		}

  	endwhile; 
	endif; 
	
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid 9
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_grid_9($option = false) { 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
  	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
		if($count==1){
			hexwp_module_2($option,'hw-aw');
		} else{
			hexwp_module_2($option,'hw-second',true,true,true);
		}
 	 	  
   	endwhile; 
	endif;  	 
}
 
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid 14
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_grid_14($option) { 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
		$num = $query->post_count;	

	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
 		 if($count==1 || $count==2){
 			hexwp_module_2($option,'hw-aw');
  		}else{
			hexwp_module_2($option,'hw-second',true,true,true);
 			
		}
  
	endwhile; 
	endif;  
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid 15
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_grid_15($option = false) { 
    
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
  	$query = hexwp_query($option);
	$num = $query->post_count;	
 	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
  		 if($count==1 || $count==2|| $count==3){
 			hexwp_module_2($option,'hw-aw');
 	 
		}else{
			hexwp_module_2($option,'hw-second',true,true,true);
 		}
  
	endwhile; 
	endif; 
	 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Grid First
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_grid_first($option =false) {
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
   	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
		if($count==1){
			hexwp_module_2($option,'hw-aw');
 		}else{
 			hexwp_module_2($option);
		}
  	endwhile; 
	endif; 
} 