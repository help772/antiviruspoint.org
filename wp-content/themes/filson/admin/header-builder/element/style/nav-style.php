<?php
	$fold=!empty($fold)?$fold:'';

 				
	$option[]= array(			"name"			=> esc_html__('Background Color','hexwp'),
								"id"			=> "background_color",	
								"type"			=> "multi_options",
 								"fold" 			=> array('boxed'  => 'boxed_layout'),
								"group"			=> __('Style','hexwp'),
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
				
				
				
					
	$option[]= array(			"name"				=> __('Text Color','hexwp'),
								"id"			=> "text_color",
								"fold" 			=> $fold,
   								"type"			=> "multi_options",
								"group"			=> __('Style','hexwp'),
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
					 		
	$option[]= array(			"name"			=> esc_html__('Border Radius','hexwp'),
								"id"			=> "border_radius",	
								"type"			=> "select",
  								"fold" 			=> array('boxed'  => 'boxed_layout'),
								"group"			=> __('Style','hexwp'),
								"options" 		=> hexwp_array_options('radius',true),
	
				); 		
				
				
	$option[] = array(			"name" 			=> esc_html__( 'Icon Style', 'hexwp' ),
								"id" 			=> "icon_style",
								"type" 			=> "heading",
								"group"			=> __('Style','hexwp'),
 				);					
 
		$option[]= array( 		"name"			=> esc_html__('Icon Background Color','hexwp'),
								"id"			=> "icon_background_color",	
								"type"			=> "multi_options",
 								"fold" 			=> array('boxed'  => 'icon_layout'),
								"group"			=> __('Style','hexwp'),
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
										
					
	$option[]= array(			"name"				=> __('Icon Color','hexwp'),
								"id"			=> "icon_color",	
								"type"			=> "multi_options",
								"group"			=> __('Style','hexwp'),
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
				
	
	$option[]= array(			"name"			=> esc_html__('Icon Radius','hexwp'),
								"id"			=> "icon_radius",	
								"type"			=> "select",
 								"fold" 			=> array('boxed'  => 'icon_layout'),
								"group"			=> __('Style','hexwp'),
								"options" 		=> hexwp_array_options('radius',true),
	
				); 		
					
	$option[] = array(			"name" 			=> esc_html__( 'Font Size', 'hexwp' ),
								"id" 			=> "font_size",
								"type" 			=> "number",
								"min" 			=> "10",
								"max" 			=> "40",
								"step" 			=> "1",
								"unit" 			=> "px",
								"group"			=> __('Typography','hexwp'),
								"desc" 			=> "Determine The Header Height Value In Pixels.",
  				);
	
	$option[] = array(			"name" 			=> esc_html__( 'Font Weight', 'hexwp' ),
								"id" 			=> "font_weight",
								"type" 			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 			=> hexwp_array_options('font_weight'),
  				);
 	
				
	$option[]=array( 			"name"			=> esc_html__('Text Transform','hexwp'),
								"id"			=> "text_transform",
								"type"			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options"		=>  array( 
															''				=>	__('Default','hexwp'),
															"none"			=> 	__('None','hexwp'),
															"uppercase"			=> 	__('Uppercase','hexwp'),
															"lowercase"			=> __('Lowercase','hexwp'),
															"capitalize"			=> __('Capitalize','hexwp'),
													) ,	
				);
				
	$option[] = array(			"name" 			=> esc_html__( 'Icon Size', 'hexwp' ),
								"id" 			=> "icon_size",
								"type" 			=> "select",
								"group"			=> __('Typography','hexwp'),
								"options" 		=> hexwp_array_options('icon_size',true),
				);		
 