<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																  Nav Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
add_filter('vh_navbar_options', 'hexwp_navbar_options');
function hexwp_navbar_options() {
 	
 	$option= array();
  
	$option[] = array(			"name" 			=> esc_html__( 'Layout', 'hexwp' ),
								"id" 			=> "layout",
								"type" 			=> "radio_image",
 								"options" 		=> array(
														'' => hexwp_DIR.'/admin/assets/images/nav-layout/normal.png',
														'flex' => hexwp_DIR.'/admin/assets/images/nav-layout/flex.png',
														'flex-center' => hexwp_DIR.'/admin/assets/images/nav-layout/flex-center.png',
								)
  				);
	$option[] = array(			"name" 			=> esc_html__( 'Height', 'hexwp' ),
								"id" 			=> "height",
								"type" 			=> "number",
								"min" 			=> "20",
								"max" 			=> "250",
								"step" 			=> "1",
								"unit" 			=> "px",
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
  				);
				 	
	$option[] = array(  		"name" 			=> esc_html__( 'Height of Elements', 'hexwp' ),
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"id" 			=> "height_element",
				 
								"type" 			=> "number",
								"min" 			=> "20",
								"max" 			=> "250",
								"step" 			=> "1",
								"unit" 			=> "px",
  				);
					
	$option[] = array(			"name" 			=> esc_html__( 'Make it Sticky', 'hexwp' ),
								"id" 			=> "sticky",
								"type" 			=> "radio",
								"responsive" 	=> "desktop",
								"options"		=>	array(   
														"" 			=>esc_html__( 'Disbale', 'hexwp' ),
														"enable" 			=>esc_html__( 'Enable', 'hexwp' ),
 													),
  				);	 
					
 
				
	$option[] = array(			"name" 			=> esc_html__( 'Make it Sticky', 'hexwp' ),
								"id" 			=> "mobile_sticky",
								"type" 			=> "radio",
								"responsive" 	=> "mobile",
								"options"		=>	array(   
														"" 			=>esc_html__( 'Disbale', 'hexwp' ),
														"top" 		=>esc_html__( 'Top', 'hexwp' ),
														"bottom" 		=>esc_html__( 'Bottom', 'hexwp' ),
 													),
  				);	 
					
						
					
	$option[] = array(  		"name" 			=> esc_html__( 'Height on Sticky Header', 'hexwp' ),
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"id" 			=> "sticky_height",
								"fold" 			=> array(
													'enable' 	=> 'sticky',
													'top' 		=> 'mobile_sticky',
													'bottom' 	=> 'mobile_sticky',
												
												),
								
								"type" 			=> "number",
								"min" 			=> "20",
								"max" 			=> "250",
								"step" 			=> "1",
								"unit" 			=> "px",
  				);
				
	$option[] = array(  		"name" 			=> esc_html__( 'Height of Elements on Sticky Header', 'hexwp' ),
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"id" 			=> "sticky_height_element",
								"fold" 			=> array(
													'enable' 	=> 'sticky',
													'top' 		=> 'mobile_sticky',
													'bottom' 	=> 'mobile_sticky',
												
												),
								
								"type" 			=> "number",
								"min" 			=> "20",
								"max" 			=> "250",
								"step" 			=> "1",
								"unit" 			=> "px",
  				);
				
				
	$option[] = array(			"name" 			=> esc_html__( 'Make it Overlap', 'hexwp' ),
								"desc" 			=>  esc_html__( 'Make the Header Overlap the Content.Overlap Only Homepage', 'hexwp' ),
								"id" 			=> "overlap",
 								"type" 			=> "radio",
								"options"		=>	array(   
															"" 			=>esc_html__( 'Disbale', 'hexwp' ),
															"enable" 			=>esc_html__( 'Enable', 'hexwp' ),
 													),
      					  
 				);	 	
 
				
				
 				 			
				
	$option[]= array(			"name"			=> esc_html__('Background Color','hexwp'),
								"id"			=> "background_color",	
								"type"			=> "multi_options",
								
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
						

	$option[] = array(			"name" 			=> __('Border' , 'hexwp'),
								"id" 			=> "border",
								"type"			=> "multi_options",
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
								"group"			=> __( 'Style', 'hexwp' ),
								"options"		=> hexwp_multi_array_options('border_mini_2'),
					); 	
					
	
	$option[]= array(			"name"				=>  __('Overlap Style','hexwp'),
								"id"			=> "overlap_heading",	
								"type"			=> "heading",
								"fold" 			=> array('enable' => 'overlap'),
								"group"			=> __('Style','hexwp'),
				); 	
	$option[]= array(			"name"				=>  __('Overlap Background Color','hexwp'),
								"id"			=> "overlap_background_color",	
 								"fold" 			=> array('enable' => 'overlap'),
								"group"			=> __('Style','hexwp'),
								"type"			=> "multi_options",
								
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
					
					
	$option[]= array(			"name"				=> __('Overlap Text Color','hexwp'),
								"id"			=> "overlap_text_color",	
								"type"			=> "multi_options",
 								"fold" 			=> array('enable'  => 'overlap'),
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
						
	$option[] = array(			"name" 			=> __('Overlap Border' , 'hexwp'),
								"id" 			=> "overlap_border",
								"type"			=> "multi_options",
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
 								"fold" 			=> array('enable'  => 'overlap'),
								"group"			=> __( 'Style', 'hexwp' ),
								"options"		=> hexwp_multi_array_options('border_mini_2'),
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
	
     return $option;
} 
 ?>