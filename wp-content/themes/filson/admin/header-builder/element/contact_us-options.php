<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Contact Us Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
 
add_filter('vh_element_options', 'hexwp_contact_us_options');
function hexwp_contact_us_options($element) {
	
		$element['contact_us'] = array(
			'name'			=> 	__('Contact Us','hexwp'),
			'id'			=> 'contact_us',
			'img'			=>   hexwp_DIR.'/admin/assets/images/header/contact_us.jpg'
		); 
					
 
		 
		$option[] = array(			"name" 			=> __('Contact Us Layout', 'hexwp' ),
									"id" 			=> "layout",	
									"type" 			=> "radio_image",
									"default"		=> hexwp_nav_default('contact_us_layout'),	
  									"options" 		=> array(
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
								"default"		=> hexwp_nav_default('contact_us_icon_layout'),	
     								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
				
	$option[] = array( 			"name" 			=> __( 'Contact Us Title', 'hexwp' ),
	 								"id" 			=> "contact_us_title",
									"type" 			=> "text",
									"fold"			=>  array(
															'text-right-2'	=>	'layout',
													),	
									"default" 		=>__( 'Contact Us', 'hexwp' ),
 
  					);
				 
	 
				
		$option[] = array( 			"name" 			=> __( 'input Text In Contact Us', 'hexwp' ),
	 								"id" 			=> "contact_us_textarea",
									"default" 		=>__( 'example@email.com', 'hexwp' ),
									"type" 			=> "textarea",
  					);
		  
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Style Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
 
				
					 
	$element['contact_us']['options']=$option;

    return $element;
	
}
