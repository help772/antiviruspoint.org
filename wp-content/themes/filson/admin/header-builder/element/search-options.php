<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Search Form Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_search_options');
function hexwp_search_options($element) {
 
	$element['search'] = array(	'name'			=> 	esc_html__('Search ','hexwp'),
								'id'			=> 'search',
								'img'			=>  hexwp_DIR.'/admin/assets/images/header/search.jpg'
 				 	); 
	
   
	$option[] = array(			"name" 			=> esc_html__('Search Layout', 'hexwp' ),
								"id" 			=> "search_position",
   								"type" 			=> "radio",
 								"default"		=> hexwp_nav_default('search_position'),	
  								"options" 		=> array(
													'fixed'	=>   esc_html__('Fixed', 'hexwp' ),
 													'dropdown'	=>   esc_html__('Dropdown', 'hexwp' ), 
												),	
				);	
				
				
				
				
	$option[] = array(			"name" 			=> esc_html__('Search Dropdown Layout', 'hexwp' ),
								"id" 			=> "search_layout",
 								"default"		=> hexwp_nav_default('search_layout'),	
  								"type" 			=> "radio_image",
 								"fold"			=>  array(
															'dropdown'	=>	'search_position',
  													),
 								"options" 		=> array(
													'icon'	=> hexwp_DIR.'/admin/assets/images/nav-layout/icon.jpg',
   													'text-right'	=>  hexwp_DIR.'/admin/assets/images/nav-layout/text-right.jpg',
  													'text-bottom'	=>   hexwp_DIR.'/admin/assets/images/nav-layout/text-bottom.jpg',
												),	
				);	
				
					
 	$option[] = array(			"name" 			=> __('Boxed Layout', 'hexwp' ),
								"id" 			=> "boxed_layout",
								"responsive" 	=> "desktop",
   								"type" 			=> "radio",
  								"fold"			=>  array(
															'dropdown'	=>	'search_position',
  													),
    							"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
				
 	$option[] = array(			"name" 			=> __('Icon Layout', 'hexwp' ),
								"id" 			=> "icon_layout",
    							"type" 			=> "radio",
								"default"		=> hexwp_nav_default('search_icon_layout'),	
  								"fold"			=>  array(
															'dropdown'	=>	'search_position',
  													),
   								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
				
				
				
				
	$option[] = array(			"name" 			=> esc_html__('Search Button in Form Layout', 'hexwp' ),
								"id" 			=> "search_button_layout",
   								"type" 			=> "radio",
								"default"		=> hexwp_nav_default('search_button_layout'),	
 								"default"		=> 'text',	
 								"responsive"	=> 'desktop',						
								"options" 		=> array(	
 															'text'		=>   esc_html__('Text', 'hexwp' ),
  															'icon'		=>   esc_html__('Icon', 'hexwp' ),
 												),	
				);					
	
 
	$option[] = array(			"name" 			=> esc_html__('Search Width', 'hexwp' ),
 								"id" 			=> "search_width",
								"default"		=> hexwp_nav_default('search_width'),	
		 						"responsive"	=> 'desktop',	
													
  								"type" 			=> "radio",
								"fold"			=>  array(
															'fixed'	=>	'search_position',
															'fixed'	=>	'search_position',
 													),	
 								"default"		=> '2_5',						
								"options" 		=> array(
															"1_2"			=> '1/2', 
															"1_3" 			=> '1/3', 
															"2_3" 			=> '2/3', 
															"1_4" 			=> '1/4', 
															"3_4" 			=> '3/4', 
															"1_5" 			=> '1/5', 
															"2_5" 			=> '2/5', 
															"3_5" 			=> '3/5', 
															"1_6" 			=> '1/6', 
															"1_1" 			=> 	__('Full Width', 'hexwp' ),													
													)						 
				);
	

	$option[] = array(			"name" 			=> esc_html__( 'Show Search Product Category' , 'hexwp'),
 								"id" 			=> "search_category",
  								"type" 			=> "radio",
 								"default"		=> 'show',						
  								"options"		=>	array( 	
															""				=>	__('Hide','hexwp'),
															"show"			=> 	__('Show','hexwp'),
  													),
					);
				
				
	$option[] = array(			"name" 			=> esc_html__( 'Search with AJAX' , 'hexwp'),
								"id" 			=> "search_ajax",
								"type" 			=> "radio",
 								"default"		=> 'show',						
								"options"		=>	array( 	
 															""				=>	__('Hide','hexwp'),
															"show"			=> 	__('Show','hexwp'),
 														),
 					);
 			 		
				
 	$fold =  array( 	'dropdown'	=>	'search_position');
   	include hexwp_PATH . '/admin/header-builder/element/style/nav-style.php'; 
				
				
	$option[] = array(			"name" 			=> esc_html__( 'Search Style', 'hexwp' ),
								"id" 			=> "searchstyle",
								"type" 			=> "heading",
								"group"			=> __('Style','hexwp'),
 				);									
	$option[] = array(			"name" 			=> esc_html__('Search Style' , 'hexwp'),
   								"id" 			=> "search_style",
   								"type"			=> "multi_options",
   								"group"			=> __('Style','hexwp'),
								"options"		=>	array( 	
														array( 
																"name"			=> 	__('Background Color','hexwp'),
																"id"			=> "background",
																"type"			=> "color_rgba",
														), 						
														array( 
															"name"			=> __('Text Color','hexwp'),			
															"id"			=> "text",
															"type"			=> "color_rgba",
														),
													),
				);
				  
				  	
	$option[] = array( 			"name"			=> esc_html__('Search Category Style' , 'hexwp'),
   								"id"			=> "search_category_style",
								"type"			=> "multi_options",
								"group"			=> __('Style','hexwp'),
								"fold"			=>  array(
															'show'	=>	'search_category',
													),		
								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('Background Color','hexwp'),
																	"id"			=> "background",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Text Color','hexwp'),			
																	"id"			=> "text",
																	"type"			=> "color_rgba",
															),
  														),
				);					

	$option[] = array(			"name"			=> esc_html__('Search Button Style' , 'hexwp'),
								"id"			=> "search_button_style",
								"group"			=> __('Style','hexwp'),
								"type"			=> "multi_options",
								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('First Background','hexwp'),
																	"id"			=> "background",
																	"type"			=> "color_rgba",
															), 		
															array( 
																	"name"			=> __('Second Background','hexwp'),			
																	"id"			=> "background_2",
																	"type"			=> "color_rgba",
															),				
															array( 
																	"name"			=> __('Text Color','hexwp'),			
																	"id"			=> "text",
																	"type"			=> "color_rgba",
															),
													),
					);					
 			

	$option[] = array(			"name" 			=>	esc_html__('Search Border Color' , 'hexwp'),
								"id" 			=>	"search_border_color",
								"group"			=> __('Style','hexwp'),
								"type" 			=>	"color_rgba"
				);   				
				
	$option[]= array(			"name"			=> esc_html__('Search Radius','hexwp'),
								"id"			=> "search_radius",	
								"type"			=> "select",
 								"group"			=> __('Style','hexwp'),
								"options" 		=> hexwp_array_options('radius',true),
	
				); 		

	$element['search']['options']=$option;

    return $element;
	
}
