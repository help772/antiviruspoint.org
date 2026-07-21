<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Categoy Menu Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_category_menu_options');
function hexwp_category_menu_options($element) {
	
	
	$element['category_menu'] = array(
						'name'			=> 	__('Category Menu','hexwp'),
						'id'			=> 'category_menu',
						'img'			=>   hexwp_DIR.'/admin/assets/images/header/category_menu.jpg'
  	); 
	
		 
	$option[]= array( 		"name"			=>__('Select Category Menu','hexwp'),
 							"id"			=> "menu",
 							"type"			=> "select",
 							'options'		=> hexwp_category_array_options('menu'),	
 				);

	$option[] = array(			"name"			=> __('Heading Layout', 'hexwp' ),
								"id" 			=> "layout",
								"type" 			=> "radio_image",
								"default"		=> hexwp_nav_default('category_layout'),	
								"options" 		=> array(	
   													'icon'	=> hexwp_DIR.'/admin/assets/images/nav-layout/icon.jpg',
   													'text-right'	=>  hexwp_DIR.'/admin/assets/images/nav-layout/text-right.jpg',
  													)	
				);
 
  	$option[] = array(			"name" 			=> __('Heading Boxed Layout', 'hexwp' ),
								"id" 			=> "boxed_layout",
   								"type" 			=> "radio",
  								"default"		=> hexwp_nav_default('category_boxed_layout'),	
    							"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);		
	 
 	$option[] = array(			"name" 			=> __('Heading Icon Layout', 'hexwp' ),
								"id" 			=> "icon_layout",
   								"type" 			=> "radio",
  								"default"		=> hexwp_nav_default('category_icon_layout'),	
   								"options" 		=> array(
  															''	=>  __('None', 'hexwp' ),
  															'boxed'	=>  __('Boxed', 'hexwp' ),
    													),
				);	
				

 
				
	$option[] = array(		"name" 			=> __('Heading Text', 'hexwp' ),
 							"id" 			=> "text",
 							"type" 			=> "text",
 								"fold" 			=> array('text-right'  => 'layout'),
							"default" 		=> __("All Categories",'hexwp'),
				);
				
	$option[] = array(		"name" 			=> __('Width', 'hexwp' ),
 							"id" 			=> "width",
 							"type" 			=> "radio",
   								"default"		=> hexwp_nav_default('category_width'),	
 							"options" 		=> array(
														"1_3"			=> __( '1/3', 'hexwp' ),
														"1_4"			=> __( '1/4', 'hexwp' ),
														"1_5"			=> __( '1/5', 'hexwp' ),
														"1_6"			=> __( '1/6', 'hexwp' ),
												),
 				 
				);

	$option[] = array(		"name" 			=> __('Display Menu in Homepage', 'hexwp' ),
 							"id" 			=> "menu_homepage",
   							"type" 			=> "radio",
   							"default" 		=> 'show',
							"options" 		=> array(	
														""				=> __( 'Hide', 'hexwp' ),
														"show"			=> __( 'Show', 'hexwp' ),
 												),
  				);
 

 


	$option[] = array(		"name" 			=> __('Menu Margin Top', 'hexwp' ),
 							"id" 			=> "menu_margin_top",
 							"type" 			=> "number",
   							"fold" 		=>  array( "show" => 'sub_category_menu'),
							"min" 			=> "0",
							"max" 			=> "100",
							"step" 			=> "1",
							"unit" 			=> "px",
							"desc" 			=> __('Determine Value In Pixels.','hexwp'),
 				);
 
 	$option[] = array(		"name" 			=> __('Menu Item Padding', 'hexwp' ),
							"desc" 			=> __('Controls Top (top on bottom) menu padding. Use a number without "px", Example 10', 'hexwp' ),
							"id" 			=> "menu_padding",
   							"type" 			=> "number",
  							"min" 			=> "0",
							"max" 			=> "100",
							"step" 			=> "1",
							"unit" 			=> "px",
 				);
				
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																  Title Style 
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
	$option[]= array(			"name"			=> esc_html__('Heading Background Color','hexwp'),
								"id"			=> "background_color",	
								"type"			=> "multi_options",
 								"fold" 			=> array('boxed'  => 'boxed_layout'),
								"group"			=> __('Heading Style','hexwp'),
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
													),	
				); 		
				
				
				
					
	$option[]= array(			"name"				=> __('Heading Text Color','hexwp'),
								"id"			=> "text_color",
    							"type"			=> "multi_options",
								"group"			=> __('Heading Style','hexwp'),
 								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('Text Color','hexwp'),
																	"id"			=> "text",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Hover Color','hexwp'),			
																	"id"			=> "hover",
																	"type"			=> "color_rgba",
															),
													),							
  				 	);
					 		
	$option[]= array(			"name"			=> esc_html__('Heading Border Radius','hexwp'),
								"id"			=> "border_radius",	
								"type"			=> "select",
  								"fold" 			=> array('boxed'  => 'boxed_layout'),
								"group"			=> __('Heading Style','hexwp'),
								"options" 		=> hexwp_array_options('radius',true),
	
				); 		
				
				
	$option[] = array(			"name" 			=> esc_html__( 'Heading Icon Style', 'hexwp' ),
								"id" 			=> "icon_style",
								"type" 			=> "heading",
								"group"			=> __('Heading Style','hexwp'),
 				);					
 	
		$option[]= array(			"name"			=> esc_html__('Heading Background Color','hexwp'),
								"id"			=> "icon_background_color",	
								"type"			=> "multi_options",
 								"fold" 			=> array('boxed'  => 'icon_layout'),
								"group"			=> __('Heading Style','hexwp'),
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
													),	
				); 		
										
					
	$option[]= array(			"name"				=> __('Heading Icon Color','hexwp'),
								"id"			=> "icon_color",	
								"type"			=> "multi_options",
								"group"			=> __('Heading Style','hexwp'),
 								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('Icon Color','hexwp'),
																	"id"			=> "text",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Hover Color','hexwp'),			
																	"id"			=> "hover",
																	"type"			=> "color_rgba",
															),
													),							
  				 	); 			
				
	
	$option[]= array(			"name"			=> esc_html__('Heading Icon Radius','hexwp'),
								"id"			=> "icon_radius",	
								"type"			=> "select",
 								"fold" 			=> array('boxed'  => 'icon_layout'),
								"group"			=> __('Heading Style','hexwp'),
								"options" 		=> hexwp_array_options('radius',true),
	
				); 		
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																  Title Style 
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		 
 
	$option[] = array(		"name" 			=>	__('Menu Background Color' , 'hexwp'),
							"id"			=> 	"menu_background_color",	
  							"type"			=> 	"color",
							"group"			=> __( 'Menu Style', 'hexwp' ),	
							 
   					); 
 
 				
	$option[]= array(			"name"				=> __('Menu Text Color','hexwp'),
								"id"			=> "menu_text_color",
    							"type"			=> "multi_options",
								"group"			=> __( 'Menu Style', 'hexwp' ),	
 								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('Text Color','hexwp'),
																	"id"			=> "text",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Hover Color','hexwp'),			
																	"id"			=> "hover",
																	"type"			=> "color_rgba",
															),
													),							
  				 	); 
			 	
				
 	$option[]= array(			"name"				=> __('Menu Icon Color','hexwp'),
								"id"			=> "menu_icon_color",	
								"type"			=> "multi_options",
								"group"			=> __( 'Menu Style', 'hexwp' ),	
 								"options"		=>	array( 	
															array( 
																	"name"			=> 	__('Icon Color','hexwp'),
																	"id"			=> "text",
																	"type"			=> "color_rgba",
															), 						
															array( 
																	"name"			=> __('Hover Color','hexwp'),			
																	"id"			=> "hover",
																	"type"			=> "color_rgba",
															),
													),							
  				 	); 			
 
	$option[]= array(		"name"			=> __('Menu Border Color','hexwp'),
							"id"			=> "menu_border_color",
							 "group"			=> __( 'Menu Style', 'hexwp' ),	
  							"type"			=> "color_rgba",
 				); 		
				
	$option[]= array(	 	"name"			=> __('Menu Box Shadow Cover','hexwp'),
							"id"			=> "menu_shadow",
							"group"			=> __( 'Menu Style', 'hexwp' ),	
							 "type"			=> "multi_options",
 							"options"		=>	hexwp_multi_array_options('shadow_element'),
 				); 				

	$option[]= array(			"name"			=> esc_html__('Menu Box Radius','hexwp'),
								"id"			=> "menu_radius",	
								"type"			=> "select",
								"group"			=> __('Menu Style','hexwp'),
								"options" 		=> hexwp_array_options('radius_mini',true),
	
				); 	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																  Heading Typography 
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
				
	$option[] = array(			"name" 			=> esc_html__( 'Heading Typography', 'hexwp' ),
								"id" 			=> "heading_typography",
								"type" 			=> "heading",
								"group"			=> __('Typography','hexwp'),
 				);			
				
	$option[] = array(			"name" 			=> esc_html__( 'Heading Font Size', 'hexwp' ),
								"id" 			=> "font_size",
								"type" 			=> "number",
								"min" 			=> "10",
								"max" 			=> "40",
								"step" 			=> "1",
								"unit" 			=> "px",
								"group"			=> __('Typography','hexwp'),
								"desc" 			=> "Determine The Header Height Value In Pixels.",
  				);
	
	$option[] = array(			"name" 			=> esc_html__( 'Heading Font Weight', 'hexwp' ),
								"id" 			=> "font_weight",
								"type" 			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 			=> hexwp_array_options('font_weight'),
  				);
 	
				
	$option[]=array( 			"name"			=> esc_html__('Text Transform','hexwp'),
								"id"			=> "text_transform",
								"type"			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 			=> hexwp_array_options('text_transform'),
												 
				);
	$option[] = array(			"name" 			=> esc_html__( 'Heading Icon Size', 'hexwp' ),
								"id" 			=> "icon_size",
								"type" 			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 		=> hexwp_array_options('icon_size',true),
				);	
				
		$option[] = array(			"name" 			=> esc_html__( 'Menu Typography', 'hexwp' ),
								"id" 			=> "menu_typography",
								"type" 			=> "heading",
								"group"			=> __('Typography','hexwp'),
 				);			
				
		$option[] = array(			"name" 			=> esc_html__( 'Menu Font Size', 'hexwp' ),
								"id" 			=> "menu_font_size",
								"type" 			=> "number",
								"min" 			=> "10",
								"max" 			=> "40",
								"step" 			=> "1",
								"unit" 			=> "px",
								"group"			=> __('Typography','hexwp'),
								"desc" 			=> "Determine The Header Height Value In Pixels.",
  				);
	
	$option[] = array(			"name" 			=> esc_html__( 'Heading Font Weight', 'hexwp' ),
								"id" 			=> "menu_font_weight",
								"type" 			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 			=> hexwp_array_options('font_weight'),
  				);
 	
				
	$option[]=array( 			"name"			=> esc_html__('Text Transform','hexwp'),
								"id"			=> "menu_text_transform",
								"type"			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 			=> hexwp_array_options('text_transform'),
												 
				);
 		
	$option[] = array(			"name" 			=> esc_html__( 'Menu Icon Size', 'hexwp' ),
								"id" 			=> "menu_icon_size",
								"type" 			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 		=> hexwp_array_options('icon_size',true),
				);			
	$element['category_menu']['options']=$option;

    return $element;
	
}


  