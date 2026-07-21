<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Text Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_menu_options');
function hexwp_menu_options($element) {
 	
	$element['menu'] = array(		'name'			=> 	__('Menu','hexwp'),
 						'id'			=> 'menu',
						'img'			=>  hexwp_DIR.'/admin/assets/images/header/menu.jpg'
  			); 
	
	
 	$option[] = array(		"name"			=>__('Select Menu','hexwp'),
							"id"			=> "menu",
							"type"			=> "select",
							'options'		=> hexwp_category_array_options('menu'),	
 				);
 
	$option[] = array(			"name"			=> __('Menu Layout', 'hexwp' ),
								"id" 			=> "layout",
								"type" 			=> "radio_image",
								"default"		=> hexwp_nav_default('menu_layout'),	
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
  								"default"		=> hexwp_nav_default('account_icon_layout'),	
   								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
				
	$option[] = array(		"name" 			=> __('Menu Item Padding', 'hexwp' ),
							"desc" 			=> __('Controls right (left on RTL) menu padding. Use a number without "px", Example 10', 'hexwp' ),
							"id" 			=> "menu_padding",
   							"type" 			=> "number",
  							"min" 			=> "0",
							"max" 			=> "100",
							"step" 			=> "1",
							"unit" 			=> "px",
 				);
				
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Style Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
	
	$element['menu']['options']=$option;

    return $element;
	
}


 
?>