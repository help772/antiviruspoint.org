<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
	$option[]= array( 
		"name"			=> __('Button Font Family','visual-slider'),
 		"id"			=> "button_fontfamily",
   		"group"			=>  __('Typography','visual-slider'),
		"type"			=> "select", 	 	
		"options"		=> vs_array_options('fontfamily'),	
		 	 
  	);   
$option[]= array( 
 		"responsive"	=> "desktop",
		"name"			=> __('Font Size','visual-slider'),
 		"id"			=> "button_font_size",
   		"group"			=>  __('Typography','visual-slider'),
		"type"			=> "number", 	 	
		"unit"			=> "px", 	
		"max"			=> "200", 		
		 	
 	 
		 
  	);  	 	
 
	
	$option[]= array( 
		"name"			=> __('Font Weight','visual-slider'),
 		"id"			=> "button_font_weight",
    		"group"			=>  __('Typography','visual-slider'),
		"type"			=> "select", 	 	
		"options"		=>  
			array( 
				"normal"			=> __('Normal','visual-slider'),			
				"bold"			=> __('Bold','visual-slider')	,
				"300"			=> '300',			
				"400"			=> '400',			
				"500"			=> '500',			
				"600"			=> '600',			
				"700"			=> '700',			
				"800"			=> '800',			
				"900"			=> '900',			
 	
   			),
 	 
		 
  	); 
	
 
		
	$option[]= array( 
		"name"			=> __('Font Decoration','visual-slider'),
 		"id"			=> "button_font_decoration",
   		"group"			=>  __('Typography','visual-slider'),
		"type"			=> "select", 	 	
		"options"		=>   
  			array( 
 				""					=> __('Default','visual-slider'),
 				"overline"			=> 	__('Overline','visual-slider'),
 				"line-through"		=> __('Line-Through','visual-slider'),
  				"underline"			=> __('Underline','visual-slider'),
  			),		
 		
  	);   
	
	$option[]= array( 
		"name"			=> __('Font Style','visual-slider'),
 		"id"			=> "button_font_style",
   		"group"			=>  __('Typography','visual-slider'),
		"type"			=> "select", 	 	
		"options"		=>   
  			array( 
 				""					=> __('Default','visual-slider'),
 				"normal"			=> 	__('Normal','visual-slider'),
 				"italic"			=> __('italic','visual-slider'),
  				"oblique"			=> __('oblique','visual-slider'),
  			),		
 		
  	);   
	$option[]= array( 
		"name"			=> __('Text Transform','visual-slider'),
 		"id"			=> "button_font_transform",
   		"group"			=>  __('Typography','visual-slider'),
		"type"			=> "select", 	 	
		"options"		=>   
  			array( 
  				""			=> __('Default','visual-slider'),
 				"uppercase"					=> __('Uppercase','visual-slider'),
 				"lowercase"			=> 	__('Lowercase','visual-slider'),
 				"capitalize"			=> __('Capitalize','visual-slider'),
   			),		
 		
  	);   	
	 
 
	$option[]= array( 
		"name"			=> __('Letter Spacing','visual-slider'),
 		"id"			=> "button_spacing",
		"group"			=>  __('Typography','visual-slider'),
		"type"			=> "number", 	 	
 		"min"			=> "-5", 	
			"step"			=> "0.5", 	 	
		 	
		"max"			=> "10", 	 	
 		 
  	);  
 
 
	
 	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_tablet',  FILTER_VALIDATE_BOOLEAN )))){
		$option[]= array( 
 			"responsive"	=> "tablet",
			"name"			=> __('Responsive on Tablet','visual-slider'),
			"group"			=>  __('Typography','visual-slider'),
			"id"			=>  "tablet_typography_heading",
			"type"			=> "heading",
		);
		$option[]= array( 
 			"responsive"	=> "tablet",
			"name"			=> __('Font Size on Tablet','visual-slider'),
			"id"			=> "tablet_button_font_size",
			"group"			=>  __('Typography','visual-slider'),
			"type"			=> "number", 	 	
			"unit"			=> "px", 	
			"max"			=> "200", 		
				
		 
			 
		);  	 	
	 
	
	 
	}	
 	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){
 
		$option[]= array( 
 			"responsive"	=> "mobile",
		
			"name"			=> __('Responsive on Mobile','visual-slider'),
			"group"			=>  __('Typography','visual-slider'),
			"id"			=>  "mobile_typography_heading",
			"type"			=> "heading",
		);
		$option[]= array( 
 			"responsive"	=> "mobile",
		
			"name"			=> __('Font Size on Mobile','visual-slider'),
			"id"			=> "mobile_button_font_size",
			"group"			=>  __('Typography','visual-slider'),
			"type"			=> "number", 	 	
			"unit"			=> "px", 	
			"max"			=> "200", 		
				
		 
			 
		);  	 	
	 
	 
		 
	} 