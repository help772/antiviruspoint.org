<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Column
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_column($sidebar=false) {
	global  $post,$smof_data;
	  $show_sidebar =hexwp_option('mobile_sidebar');
	if( $show_sidebar == 'show' && !empty(hexwp_ismobile())){
	  		$column = 'main' ;
			
	 }elseif(!empty(hexwp_meta('full_width'  ))){
		
 		$column = 'main' ;
		
 	}elseif(get_post_type()=='portfolio' && !empty(hexwp_option('portfolio_full_width' ,true ))){
		
 		$column = 'main' ;
		
 	}elseif(function_exists ( "is_woocommerce" ) && (is_cart() || is_checkout() || is_account_page())){
		
 		$column = 'main' ;
		
	}elseif(function_exists ( "is_woocommerce" ) && (is_product() || is_shop() || is_product_category() ||  is_product_tag() || is_product_taxonomy()) ){
		
		if ( !is_singular( 'product' ) ) { 
 			$column =  !empty($_GET['product_column'])?$_GET['product_column']:hexwp_option('product_column') ;
 		}else{
 			$column = 'main' ;
		}
  		
	}else{
 		$column =  !empty($_GET['column'])?$_GET['column']:hexwp_option('column') ;
	}
  	
 if($column =='main_right'){
		$sidebar_1= ' ';
		$sidebar_2= '';
		$sidebar_3= 'right';
		$sidebar_4= '';
	
	} elseif($column =='left_main'){
		$sidebar_1= '';
		$sidebar_2= '';
		$sidebar_3= 'left';
		$sidebar_4= '';
	   
	} elseif($column =='main_left_right'){
 		$sidebar_1='';
		$sidebar_2='';
		$sidebar_3='left';
		$sidebar_4='right';

	} elseif($column =='left_main_right'){
		$sidebar_1='left';
		$sidebar_2='';
		$sidebar_3='';	
		$sidebar_4='right';	
	} elseif($column =='left_right_main'){
		$sidebar_1='';
		$sidebar_2='';
		$sidebar_3='right';
		$sidebar_4='left';
		
	}else{
		$sidebar_1= '';
		$sidebar_2= '';
		$sidebar_3= '';	
		$sidebar_4= '';	
	}
	
	
	
 	if($sidebar=='column'){
		return 'hw-'.$column;
	}elseif($sidebar== '1'){
		return $sidebar_1;
	
	}elseif($sidebar== '2'){
		return $sidebar_2;
		
	}elseif($sidebar== '3'){
		return $sidebar_3;
 
	}elseif($sidebar== '4'){
		return $sidebar_4;
	}
  
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Active Sidebar

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_active_sidebar($samt,$tool = false) {
	global  $post,$smof_data;
	if ( is_category() ) {
			$meta = get_term_meta(get_query_var( 'cat'  ));
	
	} elseif ( (  is_page() || is_single())) {
		$meta = get_post_meta( $post->ID );
	}	
	  
	 
	if(function_exists ( "is_woocommerce" ) && (is_shop() ||is_product_category() ||   is_product_tag() || is_product_taxonomy())){
		$sidebar = 'sidebar_woocommerce_'.$samt;
	
	  
	}  elseif( is_home() && !empty($smof_data['sidebar_home_'.$samt]) ){
		$sidebar =  $smof_data['sidebar_home_'.$samt];
	
	} elseif( is_single() && !empty($meta['sidebar_single_'.$samt][0]) ){
		$sidebar = $meta['sidebar_single_'.$samt][0];
	
	} elseif( is_single() &&  !empty($smof_data['sidebar_single_'.$samt])){
		$sidebar = $smof_data['sidebar_single_'.$samt];
	
	} elseif( is_page() && !empty($meta['sidebar_page_'.$samt][0]) ){
		$sidebar = $meta['sidebar_page_'.$samt][0];
	
	} elseif( is_page() && !empty( $smof_data['sidebar_page_'.$samt]) ){
		$sidebar = $smof_data['sidebar_page_'.$samt];
	
	} elseif( get_post_type() =='portfolio' && !empty($meta['sidebar_portfolio_'.$samt][0]) ){
		$sidebar = $meta['sidebar_portfolio_'.$samt][0] ;
	
	 } elseif( get_post_type() =='portfolio' && !empty($smof_data['sidebar_portfolio_'.$samt]) ){
		$sidebar = $smof_data['sidebar_portfolio_'.$samt] ;
	
	 
	
	}elseif( is_category() &&  !empty($meta['hexwp_cats_'.$samt][0] ) ){
		$sidebar =  $meta['sidebar_cats_'.$samt][0];
	
	}elseif( is_archive() &&  !empty($smof_data['sidebar_archive_'.$samt] ) ){
		$sidebar =  $smof_data['sidebar_archive_'.$samt];
	
	} elseif ( function_exists('is_bbpress') && is_bbpress() && !empty($smof_data['hexwp_bbpress_'.$samt] )){
		$sidebar =  $smof_data[ 'sidebar_bbpress_'.$samt];
	
	} else{
		$sidebar = 'sidebar_main_'.$samt;
	}
	if($tool == true){
	if(is_active_sidebar($sidebar)){
		return 300;
	}else{
		return '';
	}
	}else{
	return $sidebar;
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																		Body Class
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('body_class', 'hexwp_sidebar_body_class');
function hexwp_sidebar_body_class($classes) {
  	if(!empty(hexwp_option('hide_sidebar',true))){
	$classes[] = 'hw-hide-sidebar';
	}
	return $classes;
 
}  
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																		Hide Sidebar
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_hide_sidebar() {
	$display='show';
	if(hexwp_ismobile() &&  hexwp_option('hide_sidebar',true)){
		$display ='hide';
 	}else{
		$display ='show';
	}
	return $display;		
			
}