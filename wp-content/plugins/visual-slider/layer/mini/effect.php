<?php
 
  	 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 	
	
	$option[]= array( 
		"name"			=> __('Effect','visual-slider'),
 		"id"			=> "effect",
   		"group"			=>  __('Effect','visual-slider'),
		"type"			=> "select", 
 
		"options"		=> array( 
			""				=> __("None Effect",'visual-slider'),
			"move"			=>  __('Move','visual-slider'),
			"fade"			=> __("Fade",'visual-slider'),
			"scale"			=> __("Scale",'visual-slider'),
 		),
	);
	
	$option[]= array( 
		"name"			=> __('Initial Position','visual-slider'),
 		"id"			=> "initial",
   		"group"			=>  __('Effect','visual-slider'),
		"type"			=> "select", 
		"fold"			=> array( "move" => "effect" ),
		"options"		=> array( 
			"top"			=>  __('Top','visual-slider'),
			"left"			=>  is_rtl() ? esc_html__('Right','visual-slider'):esc_html__('Left','visual-slider'),
			"bottom"		=>  __('Bottom','visual-slider'),
			"right"			=>  is_rtl() ? esc_html__('Left','visual-slider'):esc_html__('Right','visual-slider'),
  		),
	);	
	
	
	$option[]= array( 
		"name"			=> __('Scale','visual-slider'),
 		"id"			=> "scale",
		"fold"			=> array( "scale" => "effect" ),
   		"group"			=>  __('Effect','visual-slider'),
		"type"			=> "number", 
 		"step"			=> "0.1", 	 	
		"min"			=> "0", 	 	
		"max"			=> "3", 
	);	
	 
	
	
	$option[]= array( 
		"name"			=> __('Effect start time','visual-slider'),
 		"id"			=> "time_start",
		"default"		=> "500",
		"min"			=> "0",
		"width"			=> "60px",
		
		"step"			=> "100",
		"fold"			=> array(
							"scale" => "effect",
							 "fade" => "effect",
							 "move" => "effect" ),
		
		"max"			=> "10000",
		"unit"			=> "ms",
		
    		"group"			=>  __('Effect','visual-slider'),
		"type"			=> "number",
   	);	
	
	$option[]= array( 
		"name"			=> __('End time of the effect','visual-slider'),
 		"id"			=> "time_end",
		"default"		=> "1000",
		"min"			=> "0",
		"unit"			=> "ms",
		"step"			=> "100",
		"width"			=> "60px",
		 
		"fold"			=> array(
							"scale" => "effect",
							 "fade" => "effect",
							 "move" => "effect" ),		
		"max"			=> "10000",
    		"group"			=>  __('Effect','visual-slider'),
		"type"			=> "number",
   	);