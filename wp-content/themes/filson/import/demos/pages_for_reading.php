<?php
   $id = $_POST['id'];
 
 
		$type_import = !empty($_POST['type_import'])? $_POST['type_import']:'';
 		if($type_import=='homepage' || $homepage_import=='homepage'){
			$homepage = get_page_by_path( 'homepage' );								
			if(!empty($homepage->ID)){
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front',$homepage->ID );	
			}
		}
 	 
	 
  
  
	if($homepage_import=='product_page'){
		$page_shop = get_page_by_path( 'shop' );								
		if(!empty($page_shop->ID) ){
  		update_option( 'woocommerce_shop_page_id',$page_shop->ID );	
 		}
		
		$page_cart = get_page_by_path( 'cart' );								
		if(!empty($page_cart->ID)){
 		update_option( 'woocommerce_cart_page_id',$page_cart->ID );	
 		}	
		
		$page_checkout = get_page_by_path( 'checkout' );								
		if(!empty($page_checkout->ID)){
 		update_option( 'woocommerce_checkout_page_id',$page_checkout->ID );	
 		}	
		
		$page_myaccount = get_page_by_path( 'my-account' );								
		if(!empty($page_myaccount->ID)){
 		update_option( 'woocommerce_myaccount_page_id',$page_myaccount->ID );	
 		}
				$page_wishlist = get_page_by_path( 'wishlist' );								
		if(!empty($page_wishlist->ID)){
 		update_option( 'yith_wcwl_wishlist_page_id',$page_wishlist->ID );	
 		}

		} 