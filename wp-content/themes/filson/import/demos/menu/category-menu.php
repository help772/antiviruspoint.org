<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Main Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		cat Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$product_categories = get_terms( 'product_cat', array('orderby'=> 'name','order'=> 'asc','hide_empty' => false) );
 $cat_array=array();
 $menu_count=0;
if( !empty($product_categories) ){
     foreach ($product_categories as $key => $category) {
   $menu_count++;
$cat_array[] = array(
					'slug' => $category->slug,
					'parent' => 0,
					'title' => $category->name,
					'url' => get_term_link($category)
			);
			
			if($menu_count==11){
				break;
			}

    } 
}

$cat_menu_name  = 'Category Menu';
$cat_menu = get_term_by( 'slug', $cat_menu_name, 'nav_menu' );
if(empty($cat_menu)){
	$cat_menu_id = wp_create_nav_menu($cat_menu_name);
	$cat_menu=array(array(
				'location' => false,
				'name' => $cat_menu_name,
				'slug' => 'cat-menu',
				'items' => $cat_array,
				),
			);
		
	hexwpdemoimport_menu_import_json($cat_menu);

echo $cat_menu_name .' Created <br>';
}else{
	$cat_menu_id = !empty($cat_menu->term_id)?$cat_menu->term_id:'0';
	echo $cat_menu_name .' It was already there<br>';
}
$locations['gamemart_category_menu'] =$cat_menu_id;
 
 

