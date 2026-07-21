<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Social Icon Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_social_options');
function hexwp_social_options($element) {
 	
	
	$element['social'] =  array( 'name'			=> 	__('Social Icon','hexwp'),
 								'id'			=> 'social',
								'img'			=>  hexwp_DIR.'/admin/assets/images/header/social.jpg'
  				); 
	 
	$option[] = array(			"name" 			=> esc_html__('Social Icon Position', 'hexwp' ),
								"id" 			=> "social_position",
   								"type" 			=> "radio",
								"responsive" 	=> "desktop",
 								"default"		=> hexwp_nav_default('social_position'),	
  								"options" 		=> array(
													'fixed'	=>   esc_html__('Fixed', 'hexwp' ),
 													'dropdown'	=>   esc_html__('Dropdown', 'hexwp' ), 
												),	
				);	
							
 				
	$option[] = array(			"name" 			=> __('Social Icons Layout', 'hexwp' ),
								"id" 			=> "layout",
 								"type" 			=> "radio_image",
								"responsive" 	=> "desktop",
 								"default"		=> hexwp_nav_default('social_layout'),	
  								"fold"			=>  array(
 															'dropdown'	=>	'social_position',
 													),
								"options" 		=> array(
													'icon'	=> hexwp_DIR.'/admin/assets/images/nav-layout/icon.jpg',
   													'text-right'	=>  hexwp_DIR.'/admin/assets/images/nav-layout/text-right.jpg',
  													'text-bottom'	=>   hexwp_DIR.'/admin/assets/images/nav-layout/text-bottom.jpg',
												)
					);			
  						 
				
 	$option[] = array(			"name" 			=> __('Boxed Layout', 'hexwp' ),
								"id" 			=> "boxed_layout",
								"responsive" 	=> "desktop",
   								"type" 			=> "radio",
  								"fold"			=>  array(
 															'dropdown'	=>	'social_position',
 													),
    							"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);		
				
 	$option[] = array(			"name" 			=> __('Icon Layout', 'hexwp' ),
								"id" 			=> "icon_layout",
								
								"responsive" 	=> "desktop",
    							"type" 			=> "radio",
								"default"		=> hexwp_nav_default('social_icon_layout'),	
  								"fold"			=>  array(
 															'dropdown'	=>	'social_position',
 													),
   								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
 
	$option[] = array(			"name" 			=> __('Social Item Padding', 'hexwp' ),
								"desc" 			=> __('Controls right (left on RTL) Social Item padding. Use a number without "px", Example 10', 'hexwp' ),
								"id" 			=> "social_padding",
								"default" 		=> "",
								"max" 			=> "50",
								"step" 			=> "1",
								"unit" 			=> "px",
								"type" 			=> "number"
					);
					
 
					
					 
	$option[] = array( 			"name" 			=> __('Social Icons Style', 'hexwp' ),
								"id" 			=> "social_style",
 								"type" 			=> "radio",
								"default" 		=> "",
								"group"			=> __('Style','hexwp'),
 								"options" 		=> array(
															"" => __('Default','hexwp'),
															"style-1" => __('Only Icon','hexwp'),
															"style-2" => __('Boxed Icon','hexwp'),
															"style-3" => __('Boxed Original Color','hexwp'),
 													)		
					);		
					
 
												 
 	$fold =  array( 	'dropdown'	=>	'social_position');
   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
					
 
			$option[] = array(			"name" 			=> __('Social Icons Size', 'hexwp' ),
								"id" 			=> "social_size",
								"group"			=> __('Typography','hexwp'),
								"type" 			=> "select",
								"options" 		=>  hexwp_array_options('icon_size',true),
					);	
									
					 
				 
 				
	$element['social']['options']=$option;

    return $element;
	
}


 