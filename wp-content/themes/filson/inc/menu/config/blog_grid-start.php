<?php


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Image Text Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	wp_reset_query();
	wp_reset_postdata();
 $argsa['key'] ='menu_blog_grid';
 $argsa['option'] =array(
 			'title' =>  '',				
 			'number' => hexwp_menu_data($item,'hexwp_number'),
 			'cats' => hexwp_menu_data($item,'hexwp_cats'),
 			'orderby' => hexwp_menu_data($item,'hexwp_orderby'),
			'post_title' => 1,
			'ignore_sticky_posts'=> false,
   			'layout' =>   'grid',
			'layout' =>  'grid_'.hexwp_menu_data($item,'hexwp_column'),
 			'between'=> hexwp_menu_data($item,'hexwp_between'),
 			'hover_post_icon'=> 'hide',
			 
 			'thumb' =>hexwp_menu_data($item,'hexwp_thumb'),
			'ratio' =>hexwp_menu_data($item,'hexwp_ratio'),
 			'alignment' => hexwp_menu_data($item,'hexwp_alignment'),
			'box_layout' => hexwp_menu_data($item,'hexwp_box_layout'),
 			 
		) ; 
 
 
ob_start(); 
  
if(  $depth!==0){

  	echo hexwp_blog_grid_config($argsa, true);
}
 
 $output .=  ob_get_clean();
	wp_reset_query();
	wp_reset_postdata();