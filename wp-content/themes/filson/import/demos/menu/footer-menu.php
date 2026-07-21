<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Main Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$footer_menu_array=array();


$footer_menu_array[] =array(
					'slug' => '120001',
					'parent' => 0,
					'title' => 'Home',
					'url' => home_url( '/' )
 				);
				
$footer_menu_array[] =array(
					'slug' => '120002',
					'parent' => 0,
					'title' => 'About Us',
					'url' => home_url( '/' ).'about-us',
  				);
				
						
$footer_menu_array[] =array(
					'slug' => '120003',
					'parent' => 0,
					'title' => 'Contact Us',
					'url' => home_url( '/' ).'contact-us',
 				);
$footer_menu_array[] = array(
					'slug' => '120004',
					'parent' => 0,
					'title' => 'FAQs',
					'url' =>  home_url( '/' ).'faqs', 
 				);		
				
$footer_menu_array[] = array(
					'slug' => '120005',
					'parent' => 0,
					'title' => 'Privacy Policy',
					'url' =>  home_url( '/' ).'privacy-policy', 
 				);		
				



$footer_menu_name  = 'Footer Menu';
$footer_menu = get_term_by( 'slug', $footer_menu_name, 'nav_menu' );
if(empty($footer_menu)){
	$footer_menu_id = wp_create_nav_menu($footer_menu_name);
	$footer_menu=array(array(
				'location' => false,
				'name' => $footer_menu_name,
				'slug' => 'footer-menu',
				'items' => $footer_menu_array,
	));
 	
	hexwpdemoimport_menu_import_json($footer_menu);
 	echo $footer_menu_name .' Created <br>';
}else{
	echo $footer_menu_name .' It was already there<br>';
}