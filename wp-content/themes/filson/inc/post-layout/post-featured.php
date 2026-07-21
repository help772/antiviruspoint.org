<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured($option){
  	$featured_layout =hexwp_data($option,'featured_layout','featured_1');
	
 	//*********************************Desktop***********************/
 	if(empty(hexwp_ismobile())){ 
		if(	$featured_layout=='featured_1' || 
			$featured_layout=='featured_2' ||
			$featured_layout=='featured_3' ||
			$featured_layout=='featured_4' ||
			$featured_layout=='featured_5' || 
			$featured_layout=='featured_6' ||
			$featured_layout=='featured_7' ||
			$featured_layout=='featured_8') hexwp_post_featured_main($option);
		if($featured_layout=='featured_9' ||
			$featured_layout=='featured_10' ||
			$featured_layout=='featured_11' ||
			$featured_layout=='featured_12' ||
	 		$featured_layout=='featured_13' ||
	 		$featured_layout=='featured_23' ) hexwp_post_featured_9($option);
			
		if(	$featured_layout=='featured_14' ||
			$featured_layout=='featured_15'||
			$featured_layout=='featured_16'||
			$featured_layout=='featured_17' ) hexwp_post_featured_14($option);
		if(	$featured_layout=='featured_18' ||
			$featured_layout=='featured_19' ||
			$featured_layout=='featured_20' ) hexwp_post_featured_18($option);
		if( $featured_layout=='featured_21' ||
			$featured_layout=='featured_22') hexwp_post_featured_21($option);
 		if($featured_layout=='featured_24') hexwp_post_featured_24($option);
		if($featured_layout=='featured_25') hexwp_post_featured_25($option);
		if($featured_layout=='featured_26') hexwp_post_featured_26($option);
		if($featured_layout=='featured_27') hexwp_post_featured_27($option);
		if($featured_layout=='featured_28') hexwp_post_featured_28($option);
		if($featured_layout=='featured_29') hexwp_post_featured_29($option);
		if($featured_layout=='featured_30') hexwp_post_featured_30($option);
		if($featured_layout=='featured_31') hexwp_post_featured_31($option);
		if($featured_layout=='featured_32') hexwp_post_featured_32($option);
	}
	 
	
	
	//*********************************Mobile***********************/
	if(!empty(hexwp_ismobile())){ 
  		$default='';
		if(	$featured_layout =='featured_9'||
			$featured_layout =='featured_10'||
			$featured_layout =='featured_11'||
			$featured_layout =='featured_12'|| 
			$featured_layout =='featured_13'||
			$featured_layout =='featured_14'||
			$featured_layout =='featured_15'||
			$featured_layout =='featured_16'||
			$featured_layout =='featured_17'||
			$featured_layout =='featured_18'||
			$featured_layout =='featured_19'||
			$featured_layout =='featured_20'||
			$featured_layout =='featured_21'||
			$featured_layout =='featured_22'||
			$featured_layout =='featured_23'||
			$featured_layout =='featured_24'||
			$featured_layout =='featured_25'||
			$featured_layout =='featured_26'||
			$featured_layout =='featured_27'||
 			$featured_layout =='featured_29'|| 
			$featured_layout =='featured_30'
 		){ 
			$default='first_tab_2_mob_1';
		}
		
     	$responsive_column =!empty($option['responsive_column'])? $option['responsive_column']:$default;
 		if(	$responsive_column=='first_tab_2_mob_1'||
			$responsive_column=='first_tab_3_mob_1'||
			$responsive_column=='first_tab_4_mob_1'||
			$responsive_column=='first_tab_2_mob_2'||
			$responsive_column=='first_tab_3_mob_2'||
			$responsive_column=='first_tab_4_mob_2' 
		){
			hexwp_post_featured_first($option);
 		}else{
			 hexwp_post_featured_main($option);
 		} 		 
  	
	}
 
}
add_action('wp_ajax_nopriv_hexwp_post_featured', 'hexwp_post_featured');
add_action('wp_ajax_hexwp_post_featured', 'hexwp_post_featured'); 
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured Main
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_main($option =false) {
  	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
  		  hexwp_module_3($option);
 
	endwhile; 
	endif; 
} 
 add_action('wp_ajax_nopriv_hexwp_post_featured_main', 'hexwp_post_featured_main');
add_action('wp_ajax_hexwp_post_featured_main', 'hexwp_post_featured_main'); 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 9
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_9($option = false) { 
	 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
  	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
	
		$count++;
		if($count==1){
			hexwp_module_3($option,'hw-aw');
		}else{
			hexwp_module_3($option,'',true,false,true);
 		}
		
	endwhile; 
	endif; 
 	 
}  
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 14
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_14($option) { 
 	
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
 		if($count==1){
			hexwp_module_3($option,'hw-aw hw-ratio1-2x');
		}else{
			hexwp_module_3($option,'hw-second ',true,false,true);
 		}
   
	endwhile; 
	endif; 

}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 18
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_18($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
	$num = $query->post_count;	
	
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
	
		 
		 if($count==1||$count==2){
		 
				hexwp_module_3($option,'hw-aw');
		 
		}else{
  			hexwp_module_3($option,'hw-second ',true,true,true);
 			
		}
  
	endwhile; 
	endif; 
  
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 21
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
function hexwp_post_featured_21($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
	$num = $query->post_count;	
	
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
 		if($count==1||$count==2|$count==3){
			hexwp_module_3($option,'hw-aw');
 		}else{
  			hexwp_module_3($option,'hw-second ',true,true,true);
 			
		}
 	endwhile; 
	endif; 
   
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 24
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_24($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
 		if($count==1){
 				hexwp_module_3($option,'hw-aw');
 			 
 		}elseif($count==2){
 			hexwp_module_3($option,'hw-ratio1-2x hw-aw',true,false,true);
 			
		}else{
 			hexwp_module_3($option,'',true,false,true);
 			
		}
  
	endwhile; 
	endif; 
   
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 25
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_25($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
		if($count==1){
 				hexwp_module_3($option);
 			 
 		}elseif($count==2){
 			hexwp_module_3($option,'hw-ratio2x hw-aw ',false,false,false);
 			
		}else{
 			hexwp_module_3($option,'',true,false,true);
 			
		}
 	endwhile; 
	endif; 
   
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 26
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_26($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
 	
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
	
		if($count==1){
			hexwp_module_3($option,'hw-aw');
 		}elseif($count==2){
			hexwp_module_3($option,'hw-ratio1-2x hw-aw',true,false);
   		 	 
 		}elseif($count==3 || $count==4 || $count==5 || $count==6){
			hexwp_module_3($option,'',true,false,true);
 		 
 		 
   		}elseif($count==7){
			hexwp_module_3($option,'hw-ratio2x hw-aw',true,false);
   		}else{
			hexwp_module_3($option,'',true,false,true);
		}
		
	endwhile; 
	endif; 
  
   
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 27
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_27($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
	$num = $query->post_count %  7;	
	
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
	
			
		if($count==1 || $count==7){
			hexwp_module_3($option,'hw-ratio2x hw-aw');
  		}else{
			hexwp_module_3($option,'',true,false,true);
 		} 
  
	endwhile; 
	endif; 
   
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 28
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_28($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
 	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
 		if($count==1 || $count==3||$count==6||$count==8){
			hexwp_module_3($option,'hw-ratio2x hw-aw' );
 		}else {
			hexwp_module_3($option,'',true,false,true);
 		}  
 	endwhile; 
	endif; 
	 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 29
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_29($option = false) { 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
  	$query = hexwp_query($option);
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
  		if($count==1){
 			hexwp_module_3($option,'hw-ratio2x hw-aw');
  		}else{
			hexwp_module_3($option,'',true,false,false);
  		} 
 	endwhile; 
	endif; 
   
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 30
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_30($option = false) { 

 	$count = hexwp_data($option,'ajaxcount')?10:0; 
  	$query = hexwp_query($option);
  	if( $query->have_posts() ) : 
 	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
	
		 if($count==1){
			hexwp_module_3($option,'hw-aw');
  		}else {
			hexwp_module_3($option,'hw-ratio1-2x',true);
 		}
 
  
	endwhile; 
	endif; 
	 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 31
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_31($option = false) { 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
  	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post();
	
		$count++;
 		if($count==5){
 				hexwp_module_3($option,'hw-aw');
 		} else{
			 
			hexwp_module_3($option,'',true,false);
			 
		} 
 		
   	endwhile; 
	endif; 
	 
	
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured 32
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_featured_32($option = false) { 
 
 	$count = hexwp_data($option,'ajaxcount')?10:0; 
 	$query = hexwp_query($option);
 	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
  		if($count==3){
 			hexwp_module_3($option,'hw-ratio2x hw-aw');
  		}else{
 			hexwp_module_3($option,true);
   		}  
 	endwhile; 
	endif;  
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Featured First
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_post_featured_first($option=false) {
 	$query = hexwp_query($option);
	$count=0;
	if( $query->have_posts() ) : 
	while ( $query->have_posts() ) : $query->the_post(); 
		$count++;
 		if($count==1){
			hexwp_module_3($option,'hw-aw');
  		}else{
 			hexwp_module_3($option);
 			
		}
  	endwhile; 
	endif; 
 
} 