<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Main Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$useful_links_array=array();


$useful_links_array[] =array(
					'slug' => '140001',
					'parent' => 0,
					'title' => 'Privacy Policy',
					'url' => home_url( '/' ).'Privacy Policy'
 				);
				
$useful_links_array[] =array(
					'slug' => '140002',
					'parent' => 0,
					'title' => 'Delivery Information',
					'url' => home_url( '/' ).'#',
  				);
				
						
$useful_links_array[] =array(
					'slug' => '140003',
					'parent' => 0,
					'title' => 'Terms & Conditions',
					'url' => home_url( '/' ).'#',
 				);
$useful_links_array[] = array(
					'slug' => '140004',
					'parent' => 0,
					'title' => 'Customer Service',
					'url' =>  home_url( '/' ).'#', 
 				);		
				
$useful_links_array[] = array(
					'slug' => '140005',
					'parent' => 0,
					'title' => 'Return Policy',
					'url' =>  home_url( '/' ).'#', 
 				);		
				



$useful_links_name  = 'Useful Links';
$useful_links = get_term_by( 'slug', $useful_links_name, 'nav_menu' );
if(empty($useful_links)){
	$useful_links_id = wp_create_nav_menu($useful_links_name);
	$useful_links=array(array(
				'location' => false,
				'name' => $useful_links_name,
				'slug' => 'useful-lsinks',
				'items' => $useful_links_array,
	));
 	
	hexwpdemoimport_menu_import_json($useful_links);
 	echo $useful_links_name .' Created <br>';
}else{
	echo $useful_links_name .' It was already there<br>';
}