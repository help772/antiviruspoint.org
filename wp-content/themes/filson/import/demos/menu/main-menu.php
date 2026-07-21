<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Main Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$main_menu_array=array();


$main_menu_array[] =array(
					'slug' => '110001',
					'parent' => 0,
					'title' => 'Home',
					'url' => home_url( '/' )
 				);
				
$main_menu_array[] =array(
					'slug' => '110002',
					'parent' => 0,
					'title' => 'News',
					'url' =>  home_url( '/' ).'category/news/', 
  				);
				
						
$main_menu_array[] =array(
					'slug' => '110003',
					'parent' => 0,
					'title' => 'Shop',
					'url' => home_url( '/' ).'shop',
 				);
				
	$main_menu_array[] =array(
					'slug' => '110004',
					'parent' => 0,
					'title' => 'About Us',
					'url' => home_url( '/' ).'about-us',
 				);
							
 $main_menu_array[] = array(
					'slug' => '110005',
					'parent' => 0,
					'title' => 'Contact Us',
					'url' =>  home_url( '/' ).'contact-us', 
 				);	
				
					

$main_menu_name  = 'Main Menu';
 $main_menu = get_term_by( 'slug', $main_menu_name, 'nav_menu' );
if(empty($main_menu)){
	$main_menu_id = wp_create_nav_menu($main_menu_name);	
	$main_menu=array(array(
				'location' => false,
				'name' => $main_menu_name,
				'slug' => 'main-menu',
				'items' =>  $main_menu_array,
				),
			);
		
	hexwpdemoimport_menu_import_json($main_menu);

	echo $main_menu_name .' Created <br>';
}else{
	$main_menu_id = !empty($main_menu->term_id)?$main_menu->term_id:'0';
	
	echo $main_menu_name .' It was already there<br>';
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Main Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////