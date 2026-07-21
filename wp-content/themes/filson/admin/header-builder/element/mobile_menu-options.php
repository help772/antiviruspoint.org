<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Text Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_mobile_menu_options');
function hexwp_mobile_menu_options($element) {
 	
	$element['mobile_menu'] = array(		'name'			=> 	__('Mobile Menu','hexwp'),
 						'id'			=> 'mobile_menu',
						'img'			=>  hexwp_DIR.'/admin/assets/images/header/mobile_menu.jpg'
  			); 
	
 
	 
 	$option[] = array(		"name"			=>__('Select Menu','hexwp'),
							"id"			=> "mobile_menu",
							"type"			=> "select",
							'options'		=> hexwp_category_array_options('menu'),	
 				);
  
  
  
  $option[] = array(			"name" 			=> esc_html__('Menu Layout', 'hexwp' ),
								"id" 			=> "layout",
   								"type" 			=> "radio_image",
								"default"		=> hexwp_nav_default('mobile_menu_layout'),	
								"options" 		=> array(	
   													'icon'	=> hexwp_DIR.'/admin/assets/images/nav-layout/icon.jpg',
   													'text-right'	=>  hexwp_DIR.'/admin/assets/images/nav-layout/text-right.jpg',
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
  								"default"		=> hexwp_nav_default('mobile_menu_icon_layout'),	
   								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
 
 				
	$option[] = array(		"name" 			=> __('Menu Title', 'hexwp' ),
 							"id" 			=> "menu_title",
 							"type" 			=> "text",
							"fold"			=>  array(
															'text-right'	=>	'menu_layout',
															'text-bottom'	=>	'menu_layout',
													),	
							"default" 		=> __("Menu",'hexwp'),
							
				);
					
 	$option[] = array(			"name"				=>__('Show Wishlist','hexwp'),
								"id"				=> "wishlist",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);	
  
	$option[] = array(			"name"				=>__('Show Account','hexwp'),
								"id"				=> "account",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);	
					
   	$option[] = array(			"name"				=>__('Show Search Form','hexwp'),
								"id"				=> "search",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);		
					
	$option[] = array(			"name"				=>__('Show Cart','hexwp'),
								"id"				=> "cart",
								"type"				=> "radio",
 								'options'			=> array(
															''   =>	 __('Hide','hexwp'),
															'show'   =>	 __('Show','hexwp'),
														),
 					);						
   
   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
					
				
	$element['mobile_menu']['options']=$option;

    return $element;
	
}  