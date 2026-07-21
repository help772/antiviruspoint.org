<?php
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		mob Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $mob_menu_array=array();
 $mob_menu_array[] =array(
					'slug' => '110001',
					'parent' => 0,
					'title' => 'Home',
					'url' => home_url( '/' )
 				);
				
$mob_menu_array[] =array(
					'slug' => '110002',
					'parent' => 0,
					'title' => 'Blog',
					'url' => '#',
  				);
				
						
$mob_menu_array[] =array(
					'slug' => '110003',
					'parent' => 0,
					'title' => 'Shop',
					'url' => home_url( '/' ).'shop',
 				);
$mob_menu_array[] = array(
					'slug' => '110004',
					'parent' => 0,
					'title' => 'News',
					'url' =>  home_url( '/' ).'news', 
 				);	
 
$product_categories = get_terms( 'product_cat', array('orderby'=> 'name','order'=> 'asc','hide_empty' => false) );

 $cat_count=0;
if( !empty($product_categories) ){
     foreach ($product_categories as $key => $category) {
   $cat_count++;
$mob_menu_array[] = array(
					'slug' => $category->slug,
					'parent' => 0,
					'title' => $category->name,
					'url' => get_term_link($category)
			);
			
			if($cat_count==6){
				break;
			}

    } 
}

$mobile_menu_name  = 'Mobile Menu';
$mobile_menu = get_term_by( 'slug', $mobile_menu_name, 'nav_menu' );
if(empty($mobile_menu)){
	$mobile_menu_id = wp_create_nav_menu($mobile_menu_name);
	$mobile_menu=array(array(
				'location' => false,
				'name' => $mobile_menu_name,
				'slug' => 'mob-menu',
				'items' => $mob_menu_array,
				),
			);
		
	hexwpdemoimport_menu_import_json($mobile_menu);
	echo $mobile_menu_name .' Created <br>';
}else{
	$mobile_menu_id = !empty($mobile_menu->term_id)?$mobile_menu->term_id:'0';
	
	echo $mobile_menu_name .' It was already there<br>';
}