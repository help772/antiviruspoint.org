<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Call Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
 
 add_filter('vh_element_options', 'hexwp_call_options');
function hexwp_call_options($element) {
	
		$element['call'] = array(
			'name'			=> 	__('Call','hexwp'),
			'id'			=> 'call',
			'img'			=>   hexwp_DIR.'/admin/assets/images/header/call.jpg'
		); 
					
 
		 
		$option[] = array(			"name" 			=> __('Call Layout', 'hexwp' ),
									"id" 			=> "layout",	
									"type" 			=> "radio",
									"type" 			=> "radio_image",
									"default"		=> hexwp_nav_default('call_layout'),	
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
								"default"		=> hexwp_nav_default('call_icon_layout'),	
     								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
				
	$option[] = array( 			"name" 			=> __( 'Call Title', 'hexwp' ),
	 								"id" 			=> "call_title",
									"type" 			=> "text",
									"fold"			=>  array(
															'text-right-2'	=>	'layout',
													),	
									"default" 		=>__( 'Call Number', 'hexwp' ),
 
  					);
				 
		$option[] = array( 			"name" 			=> __( 'input Text In Call', 'hexwp' ),
	 								"id" 			=> "call_textarea",
									"default" 		=>__( '0032 541 6488', 'hexwp' ),
									"type" 			=> "textarea",
  					);
		  
		 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Style Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
 
				
					 
		   
	$element['call']['options']=$option;

    return $element;
	
}
