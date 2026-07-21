<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Wishlist Options

*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 if ( function_exists ( "is_woocommerce" ) && function_exists('yith_wcwl_count_all_products')) {

add_filter('vh_element_options', 'hexwp_wish_options');
function hexwp_wish_options($element) {
 
	$element['wish'] = array(
							'name'			=> 	esc_html__('Wishlist','hexwp'),
 							'id'			=> 'wish',
							'img'			=>  hexwp_DIR.'/admin/assets/images/header/wish.jpg'
  	); 
	 
	 
					 
	$option[] = array( "name" 			=> esc_html__('Wishlist Layout', 'hexwp' ),
							"id" 			=> "layout",
							"type" 			=> "radio_image",
							"default" 		=> hexwp_nav_default('wish_layout'),	
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
  								"default"		=> hexwp_nav_default('wish_icon_layout'),	
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
 
						
				
	$element['wish']['options']=$option;

    return $element;
	
}
}				
