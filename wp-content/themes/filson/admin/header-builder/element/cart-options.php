<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Shop Cart Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
if ( function_exists ( "is_woocommerce" )){

 add_filter('vh_element_options', 'hexwp_cart_options');
function hexwp_cart_options($element) {
	
	
	$element['cart'] = array(
							'name'			=> 	esc_html__('Cart','hexwp'),
 							'id'			=> 'cart',
							'img'			=>   hexwp_DIR.'/admin/assets/images/header/cart.jpg'
  	); 
	 
   

	$option[] = array(			"name" 			=> esc_html__('Shop Cart Layout', 'hexwp' ),
								"id" 			=> "layout",
   								"type" 			=> "radio_image",
								"default"		=> hexwp_nav_default('cart_layout'),	
								"options" 		=> array(	
   													'icon'	=> hexwp_DIR.'/admin/assets/images/nav-layout/icon.jpg',
   													'text-right'	=>  hexwp_DIR.'/admin/assets/images/nav-layout/text-right.jpg',
 													'text-right-2'	=>  hexwp_DIR.'/admin/assets/images/nav-layout/text-right-2.jpg',
 													'text-bottom'	=>   hexwp_DIR.'/admin/assets/images/nav-layout/text-bottom.jpg',
 													)	
				);		
				
 	$option[] = array(			"name" 			=> __('Boxed Layout', 'hexwp' ),
								"id" 			=> "boxed_layout",
   								"type" 			=> "radio",
    							"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);		
	 
 	$option[] = array(			"name" 			=> __('Icon Layout', 'hexwp' ),
								"id" 			=> "icon_layout",
   								"type" 			=> "radio",
  								"default"		=> hexwp_nav_default('cart_icon_layout'),	
   								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Style Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
 
				
	$element['cart']['options']=$option;
     return $element;
	
}
}