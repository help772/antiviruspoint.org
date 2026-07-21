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


  $cat_footer_array=array();
 $cat_footer_count=0;
if( !empty($product_categories) ){
     foreach ($product_categories as $key => $category) {
   $cat_footer_count++;
$cat_footer_array[] = array(
					'slug' => $category->slug,
					'parent' => 0,
					'title' => $category->name,
					'url' => get_term_link($category)
			);
			
			if($cat_footer_count==5){
				break;
			}

    } 
}
 
$cat_footer_menu_name  = 'Footer Categories';
$cat_footer_menu = get_term_by( 'slug', $cat_footer_menu_name, 'nav_menu' );
if(empty($cat_footer_menu)){

	$cat_footer_menu_id = wp_create_nav_menu($cat_footer_menu_name);
	$cat_footer_menu=array(array(
			'location' => false,
            'name' => $cat_footer_menu_name,
            'slug' => 'cat_footer',
     		'items' => $cat_footer_array,
		));
 	
	hexwpdemoimport_menu_import_json($cat_footer_menu);
 	echo $cat_footer_menu_name .' Created <br>';
}else{
	echo $cat_footer_menu_name .' It was already there<br>';
}