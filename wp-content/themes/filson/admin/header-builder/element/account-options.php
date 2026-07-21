<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Account Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_account_options');
function hexwp_account_options($element) {
	
 	$element['account'] = array(			'name'			=> 	__('Account','hexwp'),
							'id'			=> 'account',
							'img'			=>  hexwp_DIR.'/admin/assets/images/header/account.jpg'
			); 
	 
	 
	$option[] = array(			"name"			=> __('Account Layout', 'hexwp' ),
								"id" 			=> "layout",
								"type" 			=> "radio_image",
								"default"		=> hexwp_nav_default('account_layout'),	
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
  								"default"		=> hexwp_nav_default('account_icon_layout'),	
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
 
				
					
	$element['account']['options']=$option;

    return $element;
	
}


