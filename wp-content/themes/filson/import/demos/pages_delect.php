<?php
 
	
	if($homepage_import=='product_page'){
		$page_shop = get_page_by_path( 'shop' );								
		if(!empty($page_shop->ID) ){
  			  wp_delete_post($page_shop->ID, true);
 			}
		
		$page_cart = get_page_by_path( 'cart' );								
		if(!empty($page_cart->ID)){
   	 wp_delete_post($page_cart->ID, true);
 			}	
		
		$page_checkout = get_page_by_path( 'checkout' );								
		if(!empty($page_checkout->ID)){
    wp_delete_post($page_checkout->ID, true);
 		}	
		
		$page_myaccount = get_page_by_path( 'my-account' );								
		if(!empty($page_myaccount->ID)){
    wp_delete_post($page_myaccount->ID, true);
 		}
	} 