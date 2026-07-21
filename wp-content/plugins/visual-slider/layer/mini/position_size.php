<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 
	$option[]= array( 
		"responsive"	=>  "desktop",		
 		"name"			=> __('Align','visual-slider'),
   		"id"			=> "align", 
 		"width"			=> "100%", 
		"type"			=> "multi_options",
 		"default"		=> array('horizontal'=>'left','vertical'=>'top'),
 		
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"				=>  __('Horizontal Position','visual-slider'),
 						"id"			=> "horizontal",
 						"warp_width"	=> "50%",
						"width"				=> "22%",
 						"type"			=> "radio_image",
 						"options"		=>  array( 
 								'left'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_left.png',		
								'center'			=> VISUALSLIDER_DIR.'admin/assets/image/layer_center.png',
								'right'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_right.png',
							),	 		
					),
					array( 
					"name"				=>  __('Vertical Position','visual-slider'),
					"id"				=> "vertical",
					"warp_width"		=> "50%",
					"width"				=> "22%",
					"type"				=> "radio_image",
 						"options"		=>  array( 
 						'top'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_top.png',		
						'middle'			=> VISUALSLIDER_DIR.'admin/assets/image/layer_middle.png',
						'bottom'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_bottom.png',
					),	
					),				
				),	
  
 	); 		
	
	
	
	
	
	$option[]= array( 
		"responsive"	=>  "desktop",		
		"name"			=> __('Position','visual-slider'),
   		"id"			=> "position",
		
		 
 		"width"			=> "100%", 
		"type"			=> "multi_options",
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"			=> __('Horizontal','visual-slider'),
						"id"			=> "horizontal",
  						"unit"			=> 'px',
 						"warp_width"	=> "50%",
  						"width"			=> '60px',
  						"min"			=> '-99999',
  						"type"			=> "number",
					),
					array( 
						"name"			=> __('Vertical','visual-slider'),
						"id"			=> "vertical",
  						"unit"			=> 'px',
 						"warp_width"	=> "50%",
						"min"			=> '-99999',
   						"width"			=> '60px',
						"type"			=> "number",
					),					
				),	
  
 	); 	
	
	
	
	
	
	
	$option[]= array( 
		"responsive"	=>  "desktop",		
		"name"			=> __('Size','visual-slider'),
 		"id"			=> "size",
		"width"			=> "100%", 
				"default"			=> !empty($default)?$default:'', 
    		"type"			=> "multi_options",
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"			=> __('Width','visual-slider'),
						"id"			=> "width",
  						"unit"			=> 'px',
 						"warp_width"	=> "50%",
  						"type"			=> "number",
					),
					array( 
						"name"			=> __('Height','visual-slider'),
						"id"			=> "height",
  						"unit"			=> 'px',
 						"warp_width"	=> "50%",
 						"type"			=> "number",
					),					
				),	
  
 	); 	
	
 	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_tablet',  FILTER_VALIDATE_BOOLEAN )))){
 		$option[]= array( 
			"responsive"	=>  "tablet",		
			"name"			=> __('Responsive in Tablet','visual-slider'),
 		"group"				=>  __('Position and Size','visual-slider'),
			"id"			=>  "tablet_heading",
 			"type"			=> "heading",
		);
		$option[]= array( 
			"responsive"	=>  "tablet",		
			"name"				=> __('Display on Tablet','visual-slider'),
			"id"				=> "tablet_display", 
			"type"				=>  'radio',
 			"options"			=>  array( 
 				'show'				=> __('Show','visual-slider'),
   				'hide'				=> __('Hide','visual-slider'),
   			),	 			
 			"group"				=>  __('Position and Size','visual-slider'),
		); 	
		 
	$option[]= array( 
			"responsive"	=>  "tablet",		
	
		"name"			=> __('Position','visual-slider'),
   		"id"			=> "tablet_align", 
 		"width"			=> "100%", 
		"type"			=> "multi_options",
		
 
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"				=>  __('Horizontal Position','visual-slider'),
 						"id"			=> "horizontal",
 						"warp_width"	=> "50%",
						"width"				=> "22%",
 						"type"			=> "radio_image",
 						"options"		=>  array( 
 								''				=> VISUALSLIDER_DIR.'admin/assets/image/layer_none.png',		
 								'left'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_left.png',		
								'center'			=> VISUALSLIDER_DIR.'admin/assets/image/layer_center.png',
								'right'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_right.png',
							),	 		
					),
					array( 
					"name"				=>  __('Vertical Position','visual-slider'),
					"id"				=> "vertical",
					"warp_width"		=> "50%",
					"width"				=> "22%",
					"type"				=> "radio_image",
 						"options"		=>  array( 
 								''				=> VISUALSLIDER_DIR.'admin/assets/image/layer_none.png',		
 						'top'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_top.png',		
						'middle'			=> VISUALSLIDER_DIR.'admin/assets/image/layer_middle.png',
						'bottom'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_bottom.png',
					),	
					),				
				),	
  
 	); 		
	$option[]= array( 
		"responsive"	=>  "tablet",		
		"name"			=> __('Position','visual-slider'),
   		"id"			=> "tablet_position", 
 		"width"			=> "100%", 
		"type"			=> "multi_options",
 	
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"			=> __('Horizontal','visual-slider'),
						"id"			=> "horizontal",
  						"unit"			=> 'px',
  						"min"			=> '-99999',
 						"warp_width"	=> "50%",
  						"type"			=> "number",
					),
					array( 
						"name"			=> __('Vertical','visual-slider'),
						"id"			=> "vertical",
  						"unit"			=> 'px',
  						"min"			=> '-99999',
 						"type"			=> "number",
					),					
				),	
  
 	); 	
	
	
	
	
	
	
	$option[]= array( 
		"responsive"	=>  "tablet",		
		"name"			=> __('Size','visual-slider'),
 		"id"			=> "tablet_size",
 		"width"			=> "100%", 
 		"type"			=> "multi_options",
 	
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"			=> __('Width','visual-slider'),
						"id"			=> "width",
  						"unit"			=> 'px',
 						"warp_width"	=> "50%",
						"type"			=> "number",
					),
					array( 
						"name"			=> __('Height','visual-slider'),
						"id"			=> "height",
  						"unit"			=> 'px',
 						"warp_width"	=> "50%",
						"type"			=> "number",
					),					
				),	
  
 	); 	 
	
	}
	
 	if(!empty(wp_unslash(filter_input( INPUT_POST, 'vs_mobile',  FILTER_VALIDATE_BOOLEAN )))){
 $option[]= array( 
			"responsive"	=>  "mobile",		
			"name"			=> __('Responsive in Mobile','visual-slider'),
			"group"				=>  __('Position and Size','visual-slider'),
			"id"			=>  "mobile_heading",
 			"type"			=> "heading",
		);
		$option[]= array( 
			"name"				=> __('Display on Mobile','visual-slider'),
			"id"				=> "mobile_display", 
			"responsive"	=>  "mobile",		
			"type"				=>  'radio',
			"options"		=>  array( 
 				'show'				=> __('Show','visual-slider'),
   				'hide'				=> __('Hide','visual-slider'),
   			),	 			
 			"group"				=>  __('Position and Size','visual-slider'),
		); 	
	 
	$option[]= array( 
			"responsive"	=>  "mobile",		
	
		"name"			=> __('Position','visual-slider'),
   		"id"			=> "mobile_align", 
 		"width"			=> "100%", 
		"type"			=> "multi_options",
 
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"				=>  __('Horizontal Position','visual-slider'),
 						"id"			=> "horizontal",
 						"warp_width"	=> "50%",
						"width"				=> "22%",
 						"type"			=> "radio_image",
 						"options"		=>  array( 
 								''				=> VISUALSLIDER_DIR.'admin/assets/image/layer_none.png',		
 								'left'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_left.png',		
								'center'			=> VISUALSLIDER_DIR.'admin/assets/image/layer_center.png',
								'right'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_right.png',
							),	 		
					),
					array( 
					"name"				=>  __('Vertical Position','visual-slider'),
					"id"				=> "vertical",
					"warp_width"		=> "50%",
					"width"				=> "22%",
					"type"				=> "radio_image",
 						"options"		=>  array( 
 								''				=> VISUALSLIDER_DIR.'admin/assets/image/layer_none.png',		
 						'top'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_top.png',		
						'middle'			=> VISUALSLIDER_DIR.'admin/assets/image/layer_middle.png',
						'bottom'				=> VISUALSLIDER_DIR.'admin/assets/image/layer_bottom.png',
					),	
					),				
				),	
  
 	); 		
	$option[]= array( 
		"responsive"	=>  "mobile",		
		"name"			=> __('Position','visual-slider'),
   		"id"			=> "mobile_position", 
 		"width"			=> "100%", 
		"type"			=> "multi_options",
 		
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"			=> __('Horizontal','visual-slider'),
						"id"			=> "horizontal",
  						"unit"			=> 'px',
  						"min"			=> '-99999',
  						"warp_width"	=> "50%",
						"type"			=> "number",
					),
					array( 
						"name"			=> __('Vertical','visual-slider'),
						"id"			=> "vertical",
  						"unit"			=> 'px',
  						"min"			=> '-99999',
 						"warp_width"	=> "50%",
						"type"			=> "number",
					),					
				),	
  
 	); 	
	
	
	
	
	
	
	$option[]= array( 
		"responsive"	=>  "mobile",		
 		"name"			=> __('Size','visual-slider'),
 		"id"			=> "mobile_size",
		"width"			=> "100%", 
 		"type"			=> "multi_options",
 		
 		"group"				=>  __('Position and Size','visual-slider'),
 		"options"		=>  array( 
					array( 
						"name"			=> __('Width','visual-slider'),
						"id"			=> "width",
  						"unit"			=> 'px',
  						"warp_width"	=> "50%",
						"type"			=> "number",
					),
					array( 
						"name"			=> __('Height','visual-slider'),
						"id"			=> "height",
  						"unit"			=> 'px',
 						"warp_width"	=> "50%",
 						"type"			=> "number",
					),					
				),	
  
 	); 	 
	
	}	
	 