<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Text Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_mobile_category_menu_options');
function hexwp_mobile_category_menu_options($element) {
 	
	$element['mobile_category_menu'] = array(  'name'			=> 	__('Mobile Category Menu','hexwp'),
 						'id'			=> 'mobile_category_menu',
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
 
 				 
					
 	 				
	$option[] = array(		"name" 			=> __('Category Menu Title', 'hexwp' ),
 							"id" 			=> "category_menu_title",
 							"type" 			=> "text",
							"fold"			=>  array(
															'text-right'	=>	'menu_layout',
															'text-bottom'	=>	'menu_layout',
													),	
							"default" 		=> __("Categories",'hexwp'),
				);
   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
					
				
	$element['mobile_category_menu']['options']=$option;

    return $element;
	
} 
?>