<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Plus Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$top_menu_array=array();
$top_menu_array[] =array(
					'slug' => '120001',
					'parent' => 0,
					'title' => 'FAQs',
					'url' => home_url( '/faqs' )
 				);
				
 $top_menu_array[] =array(
					'slug' => '120002',
					'parent' => 0,
					'title' => 'About Us',
					'url' => home_url( '/about-us' )
 				);
				
 $top_menu_array[] =array(
					'slug' => '120003',
					'parent' => 0,
					'title' => 'Contact Us',
					'url' => home_url( '/contact-us' )
 				);
 $top_menu_array[] =array(
					'slug' => '120004',
					'parent' => 0,
					'title' => 'Order Tracking',
					'url' => home_url( '/order-tracking' )
 				);		
				
				
							
$top_menu_name  = 'Top Menu';
$top_menu = get_term_by( 'slug', $top_menu_name, 'nav_menu' );
if(empty($top_menu)){
$top_menu_id = wp_create_nav_menu($top_menu_name);
$top_menu=array(array(
			'location' => false,
            'name' => $top_menu_name,
            'slug' => 'cat-menu',
     		'items' => $top_menu_array,
			),
		);
 	
	hexwpdemoimport_menu_import_json($top_menu);
	echo $top_menu_name .' Created <br>';
	
}else{
	$top_menu_id = !empty($top_menu->term_id)?$top_menu->term_id:'0';
	
	echo $top_menu_name .' It was already there<br>';
}
 	    
		    
			
  
 