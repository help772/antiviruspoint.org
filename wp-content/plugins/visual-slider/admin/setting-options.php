<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( ! function_exists( 'vs_setting_options' ) ) {
 add_filter('vs_setting_options', 'vs_setting_options');
function vs_setting_options( $option ) {
	$option = array();
	$option[]= array( 
		"name"			=> __('Slider Full Width','visual-slider'),
 		"id"			=> "full_width",
		"default"    	=> 1,
  		"type"			=> "checkbox",
 		
 	); 		 
	 
	$option[]= array( 
		"name"			=> __('Slider Layout','visual-slider'),
 		"id"			=> "type",
   		"type"			=> "radio",
		"options"		=>  array( 
			"slider"			=> __('Slider','visual-slider'),
			"single"			=> __('Single Slide','visual-slider'),
			"glider"			=> __('Glider','visual-slider'),
  		),
 	); 	
	 
	 
	$option[]= array( 
		"responsive"	=>  "desktop",		
 		"name"			=> __('Glider layout','visual-slider'),
 		"id"			=> "glider_layout",
		"fold"			=> array('glider' =>'vs_setting[type]' ),
 		"type"			=> "radio_image",
		"options"		=>  array( 
 			"glider_1"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_1.jpg',
 			"glider_2"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_2.jpg',
 			"glider_3"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_3.jpg',
 			"glider_4"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_4.jpg',
 			"glider_5"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_5.jpg',
 			"glider_6"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_6.jpg',
 			"glider_7"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_7.jpg',
 			"glider_8"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_8.jpg',
 			"glider_9"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_9.jpg',
 			"glider_10"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_10.jpg',
 			"glider_11"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_11.jpg',
 			"glider_12"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_12.jpg',
 			"glider_13"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_13.jpg',
 			"glider_14"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_14.jpg',
 			"glider_15"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_15.jpg',
 			"glider_16"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_16.jpg',
 			"glider_17"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_17.jpg',
 			"glider_18"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_18.jpg',
 			"glider_19"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_19.jpg',
 			"glider_20"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_20.jpg', 
 			"glider_21"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_21.jpg', 
 			"glider_22"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_22.jpg', 
 			"glider_23"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_23.jpg', 
 			"glider_24"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_24.jpg', 
 			"glider_25"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_25.jpg', 
 			"glider_26"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_26.jpg', 
 			"glider_27"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_27.jpg', 
 			"glider_28"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_28.jpg', 
 			"glider_29"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_29.jpg', 
 			"glider_30"		=> VISUALSLIDER_DIR.'/admin/assets/image/glider/glider_30.jpg', 
 		),			
   	); 	
 

	$option[]=array( 
				"name"			=> __('Between Size','visual-slider'),			
  				"id"			=> "between",
   				"fold"			=> array('glider' =>'vs_setting[type]' ),
  	 			"min"			=> "0",
				"max"			=> "100",
				"unit"			=> "px",
 				"default"		=>  '40',
				"width"			=> "60px",
				"type"			=> "number",										
	);
 
	$option[]= array( 
		"name"			=> __('Slider Size','visual-slider'),
   		"id"			=> "size",
 		"desc"			=> __('Display on screens with a minimum size of 1025 pixels',"visual-slider"),
 		"default"		=>  array(
								'width' => '1200',
								'height' => '600',
							) ,
							
		"type"			=> "multi_options",
		"options"		=>  array( 
			array( 
				"name"			=> __('Width','visual-slider'),			
  				"id"			=> "width",
 				"type"			=> "number",
 				"min"			=> "1",
 				"max"			=> "10000",
				"width"			=> "60px",												
  				"unit"			=> "px",
 			),
			array( 
				"name"			=> __('Height','visual-slider'),			
  				"id"			=> "height",
				"min"			=> "1",
 				"max"			=> "10000",
 				"fold"			=> array('slider' =>'vs_setting[type]','single' =>'vs_setting[type]' ),
 				"type"			=> "number",
 				"unit"			=> "px",
				"width"			=> "60px",												
  			), 
			array( 
				"name"			=> __('Ratio','visual-slider'),			
  				"id"			=> "ratio",
   				"fold"			=> array('glider' =>'vs_setting[type]' ),
 				"type"			=> "select",
 				"options"		=> array(
  					'30'				=>	'10:3',
  					'33.33'				=>	'3:1',
 					'40'				=>	'5:2',
					'45'				=>	'10:9',
					'50'				=>	'2:1',
					'56'				=>	'16:9',
					'60'				=>	'5:3',
					'66.66'				=>	'3:2',
					'75'				=>	'4:3', 
					'100'				=>	'1:1',
					'135'				=>	'3:5',
					)											
  			)
	 
  		)
		
 	); 	
	
	 
 	$option[]= array( 
  		"name"			=> __('Glider Layout on Tablet','visual-slider'),
 		"id"			=> "tablet_glider_layout",
		"fold"			=> array('glider' =>'vs_setting[type]' ),
		"group"			=>  __('Responsive on tablet','visual-slider'),
		"type"			=> "radio_image",
		"options"		=>  array( 
 			"tablet_glider_1"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_1.jpg',
 			"tablet_glider_2"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_2.jpg',
 			"tablet_glider_3"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_3.jpg',
 			"tablet_glider_4"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_4.jpg',
 			"tablet_glider_5"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_5.jpg',
 			"tablet_glider_6"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_6.jpg',
 			"tablet_glider_7"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_7.jpg',
 			"tablet_glider_8"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_8.jpg',
 			"tablet_glider_9"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_9.jpg',
 			"tablet_glider_10"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_10.jpg',
 			"tablet_glider_11"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_11.jpg',
 			"tablet_glider_12"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_12.jpg',
 			"tablet_glider_13"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_13.jpg',
 			"tablet_glider_14"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_14.jpg',
 			"tablet_glider_15"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_15.jpg',
 			"tablet_glider_16"		=> VISUALSLIDER_DIR.'/admin/assets/image/tablet_glider/tablet_glider_16.jpg',
 
  		),			
   	); 	
	$option[]=array( 
				"name"			=> __('Between on Tablet','visual-slider'),			
  				"id"			=> "tablet_between",
	 			"group"			=>  __('Responsive on Tablet','visual-slider'),
   				"fold"			=> array('glider' =>'vs_setting[type]' ),
   	 			"min"			=> "0",
				"max"			=> "100",
				"unit"			=> "px",
 				"default"		=>  '40',
				"width"			=> "60px",
				"type"			=> "number",	
 	);
 	$option[]= array( 
 		"name"			=> __('Responsive on Tablet','visual-slider'),
 		"id"			=> "responsive_tablet",
		"group"			=>  __('Responsive on tablet','visual-slider'),
 		"desc"			=> __('If you want to change the slider settings on tablets, turn on this option, screen length below 1025 pixels and above 767 pixels',"visual-slider"),
 		"type"			=> "checkbox",
 	); 		
	
	
	$option[]= array( 
 		"name"			=> __('Slider Size on Table','visual-slider'),
	 		"group"			=>  __('Responsive on Tablet','visual-slider'),
 		"id"			=> "tablet_size",
		"fold"			=> array( 1 => 'vs_setting[responsive_tablet]'  ),
 		"default"		=>  array(
								'width' => '768',
								'height' => '400',
							) ,
							
   		"type"			=> "multi_options",
		"options"		=>  array(
			array( 
					"name"			=> __('Width','visual-slider'),			
					"id"			=> "width",
					"type"			=> "number",
					"min"			=> "1",
					"max"			=> "1024",
					"width"			=> "60px",												
					"unit"			=> "px",
				),
				array( 
					"name"			=> __('Height','visual-slider'),			
					"id"			=> "height",
					"min"			=> "1",
					"max"			=> "10000",
					
 					"fold"			=> array('slider' =>'vs_setting[type]','single' =>'vs_setting[type]' ),
					"type"			=> "number",
					"unit"			=> "px",
					"width"			=> "60px",												
				), 
				array( 
					"name"			=> __('Ratio','visual-slider'),			
					"id"			=> "ratio",
					"fold"			=> array('glider' =>'vs_setting[type]' ),
					"type"			=> "select",
					"options"		=> array(
						'30'				=>	'10:3',
						'33.33'				=>	'3:1',
						'40'				=>	'5:2',
						'50'				=>	'1:2',
						'56'				=>	'16:9',
						'60'				=>	'5:3',
						'75'				=>	'4:3', 
						'100'				=>	'1:1',
						'135'				=>	'3:5',
					)											
  			), 				
				
			),
		
 	); 			
	
	 
	$option []= array( 
 		"name"			=> __('Glider Layout on Mobile','visual-slider'),
		"group"			=>  __('Responsive on Mobile','visual-slider'),
 		"id"			=> "mobile_glider_layout",
		"fold"			=> array('glider' =>'vs_setting[type]' ),
 							
		"type"			=> "radio_image",
		"options"		=>  array( 
 			"mobile_glider_1"		=> VISUALSLIDER_DIR.'/admin/assets/image/mobile_glider/mobile_glider_1.jpg',
 			"mobile_glider_2"		=> VISUALSLIDER_DIR.'/admin/assets/image/mobile_glider/mobile_glider_2.jpg',
 			"mobile_glider_3"		=> VISUALSLIDER_DIR.'/admin/assets/image/mobile_glider/mobile_glider_3.jpg',
 			"mobile_glider_4"		=> VISUALSLIDER_DIR.'/admin/assets/image/mobile_glider/mobile_glider_4.jpg',
 			"mobile_glider_5"		=> VISUALSLIDER_DIR.'/admin/assets/image/mobile_glider/mobile_glider_5.jpg',
 			"mobile_glider_6"		=> VISUALSLIDER_DIR.'/admin/assets/image/mobile_glider/mobile_glider_6.jpg',
  			 
 		),			
   	); 	
	$option[]=array( 
				"name"			=> __('Between on Mobile','visual-slider'),			
  				"id"			=> "mobile_between",
	 			"group"			=>  __('Responsive on mobile','visual-slider'),
   				"fold"			=> array('glider' =>'vs_setting[type]' ),
				"min"			=> "0",
				"max"			=> "100",
				"unit"			=> "px",
 				"default"		=>  '40',
				"width"			=> "60px",
				"type"			=> "number",											
	);
 	$option[]= array( 
  		"name"			=> __('Responsive on Mobile','visual-slider'),
 		"id"			=> "responsive_mobile",
		"group"			=>  __('Responsive on Mobile','visual-slider'),
 		"desc"			=> __('If you want to change the slider settings in mobile phones, turn on this option, the screen length is below 768 pixels.',"visual-slider"),

		"type"			=> "checkbox",
	); 		
 
	$option[]= array( 
 	
		"name"			=> __('Slider Size in Mobile','visual-slider'),
 		"id"			=> "mobile_size",
		"group"			=>  __('Responsive on Mobile','visual-slider'),
		"fold"			=> array( true => 'vs_setting[responsive_mobile]'  ),
   		"default"		=>  array(
								'width' => '480',
								'height' => '750',
							) ,
							
   		"type"			=> "multi_options",
		"options"		=>  array(
				array( 
					"name"			=> __('Width','visual-slider'),			
					"id"			=> "width",
					"type"			=> "number",
					"min"			=> "1",
					"max"			=> "767",
					"width"			=> "60px",												
					"unit"			=> "px",
				),
				array( 
					"name"			=> __('Height','visual-slider'),			
					"id"			=> "height",
					"min"			=> "1",
					"max"			=> "10000",
					"type"			=> "number",
 					"fold"			=> array('slider' =>'vs_setting[type]','single' =>'vs_setting[type]' ),
					"unit"			=> "px",
					"width"			=> "60px",												
				), 
				array( 
				"name"			=> __('Ratio','visual-slider'),			
  				"id"			=> "ratio",
   				"fold"			=> array('glider' =>'vs_setting[type]' ),
 				"type"			=> "select",
 				"options"		=> array(
						'30'				=>	'10:3',
						'33.33'				=>	'3:1',
						'40'				=>	'5:2',
						'50'				=>	'1:2',
						'56'				=>	'16:9',
						'60'				=>	'5:3',
						'75'				=>	'4:3', 
						'100'				=>	'1:1',
						'135'				=>	'3:5',
					)											
  			), 				
			
		)
		
 	); 		
 	
	
	 
		
 	$option[]= array( 
		"name"			=> __('Pager','visual-slider'),
 		"id"			=> "pager",
		"default"		=>  1,
 		"group"			=>  __('Pager','visual-slider'),
  		"type"			=> "checkbox",
 	); 		
	
 	$option[]= array( 
		"name"			=> __('Pager visibility','visual-slider'),
 		"id"			=> "pager_visibility",
 		"group"			=>  __('Pager','visual-slider'),
		"default"		=>  'hover',
		"fold"			=> array( 1 =>'vs_setting[pager]'  ),
		"type"			=> "radio",
		"options"		=> array( 
			"hover" =>  __('Hover','visual-slider'),
			"fexid" =>  __('Fixed ','visual-slider')
		),
  		
  	); 	 
	
	
	 
 	$option[]= array( 
		"name"			=> __('Pager Position','visual-slider'),
 		"id"			=> "pager_position",
 		"group"			=>  __('Pager','visual-slider'),
		"fold"			=> array( 1 => 'vs_setting[pager]'   ),
 		"type"			=> "radio",
		"default"		=>  'top',
		
		"options"		=> array( 
			"top" =>  __('Top','visual-slider'),
			"bottom" =>  __('Bottom','visual-slider')
		),
  		
  	); 	 
	
 
	
	$option[]= array( 
		"name"			=>	__('Pager Color Layout','visual-slider'),
 		"id"			=>	"pager_color", 
		"fold"			=> array( 1 =>'vs_setting[pager]' ),
 		"group"			=>  __('Pager','visual-slider'),
  		"type"			=> "multi_options",
		"options"		=>  array( 
			array( 
				"name"			=> __('Color','visual-slider'),			
  				"id"			=> "color",
				"type"			=> "color_rgba",
 			),
 	 
			array( 
 				"name"			=> 	__('Active Color','visual-slider'),
 				"id"			=> "active",
  				"type"			=> "color_rgba",
  			),		
		)	
	); 		
	
	
	$option[]= array( 
		"name"			=> __('Arrows','visual-slider'),
 		"id"			=> "arrows",
 		"group"			=>  __('Arrows','visual-slider'),
		"default"		=>  1,
 		"type"			=> "checkbox",
 	); 		
		


	$option[]= array( 
			"name"			=> __('Arrows Visibility','visual-slider'),
			"id"			=> "arrows_visibility",
			"fold"			=> array( 1 =>'vs_setting[arrows]' ),
			"group"			=>  __('Arrows','visual-slider'),
			"type"			=> "radio",
			"options"		=> array( 
				"hover" =>  __('Hover','visual-slider'),
				"fexid" =>  __('Fixed ','visual-slider')
			),
		);
		
	$option[]= array( 
		"name"			=> __('Arrows Position','visual-slider'),
		"id"			=> "arrows_position",
		"fold"			=> array( 1 =>'vs_setting[arrows]' ),
		"group"			=>  __('Arrows','visual-slider'),
		"type"			=> "radio",
		"options"			=>	array( 	
			"inside" 				=>esc_html__('in Side','visual-slider'),
			"side" 				=>esc_html__('Side','visual-slider'),
			"outside" 				=>esc_html__('out Side','visual-slider'), 	 
		),
	);
				
		 	
	   
	 
	$option[]= array( 
		"name"			=> __('Arrows Color','visual-slider'),
 		"id"			=> "arrows_color",
		"fold"			=> array( 1 =>'vs_setting[arrows]' ),
		"group"			=>  __('Arrows','visual-slider'),
  		"type"			=> "multi_options",
		"options"			=>	array( 	
  			array( 
 				"name"			=> 	__('Background','visual-slider'),
 				"id"			=> "background",
  				"type"			=> "color_rgba",
  			
 			), 	
			
			array( 
				"name"			=> __('Arrow Color','visual-slider'),			
  				"id"			=> "text",
				"type"			=> "color_rgba",
 			),					
 		
		), 			
	 
	); 			
	$option[]= array( 
		"name"			=> __('Arrows Hover Color','visual-slider'),
 		"id"			=> "arrows_hover_color",
		"fold"			=> array( 1 =>'vs_setting[arrows]' ),

		"group"			=>  __('Arrows','visual-slider'),
  		"type"			=> "multi_options",
		"options"			=>	array( 	
  			array( 
 				"name"			=> 	__('Background','visual-slider'),
 				"id"			=> "background",
  				"type"			=> "color_rgba",
  			
 			), 	
			
			array( 
				"name"			=> __('Arrows','visual-slider'),			
  				"id"			=> "text",
				"type"			=> "color_rgba",
 			),					
 		
		), 			
	 
	); 				
		
	$option[]= array( 
		"name"			=> __('Arrows Radius','visual-slider'),
 		"id"			=> "arrows_radius",
		"fold"			=> array( 1 =>'vs_setting[arrows]' ),

		"min"			=> "0",
		"max"			=> "100",
		"width"			=> "60px",
		
		"unit"			=> "px",
		
		"group"			=>  __('Arrows','visual-slider'),
		"type"		=> "number",
 	); 	
	
$option[]= array(
		"responsive"			=> "desktop",
		"name"			=> __('Arrows Size','visual-slider'),
		"min"			=> "0",
		"max"			=> "100",
		"unit"			=> "px",
		"width"			=> "60px",
		
 		"id"			=> "arrows_size",
		"fold"			=> array( 1 =>'vs_setting[arrows]' ),
		"group"			=>  __('Arrows','visual-slider'),
		"type"			=> "number",
   	); 	
			
	
	
$option[]= array( 
			"responsive"	=>  "tablet",		
			"name"			=> __('Responsive on Tablet','visual-slider'),
			"group"			=>  __('Arrows','visual-slider'),
			"id"			=>  "arrows_heading",
 			"type"			=> "heading",
	);
	$option[]= array( 
		"responsive"	=>  "tablet",		
 		"name"			=> __('Arrows Size on Tablet','visual-slider'),
		"min"			=> "0",
		"max"			=> "100",
		"unit"			=> "px",
		"width"			=> "60px",
  		"id"			=> "tablet_arrows_size",
		"fold"			=> array( 1 =>'vs_setting[arrows]' ),
		"group"			=>  __('Arrows','visual-slider'),
		"type"			=> "number",
   	); 	

$option[]= array( 
			"responsive"	=>  "mobile",		
			"name"			=> __('Responsive on Tablet','visual-slider'),
			"group"			=>  __('Arrows','visual-slider'),
			"id"			=>  "arrows_heading",
 			"type"			=> "heading",
	);
	$option[]= array( 
		"responsive"	=>  "mobile",		
 		"name"			=> __('Arrows Size on Mobile','visual-slider'),
		"min"			=> "0",
		"max"			=> "100",
		"unit"			=> "px",
		"width"			=> "60px",
  		"id"			=> "mobile_arrows_size",
		"fold"			=> array( 1 =>'vs_setting[arrows]' ),
		"group"			=>  __('Arrows','visual-slider'),
		"type"			=> "number",
   	); 	
	
	
	
		
	$option[]= array( 
		"name"			=> __('Timer','visual-slider'),
 		"id"			=> "timer",
		"default"		=>  0,
 		"group"			=>  __('Timer','visual-slider'),
 		"type"			=> "checkbox",
 	); 		
 
	$option[]= array( 
		"name"			=>	__('Timer Color','visual-slider'),
 		"id"			=>	"timer_color", 
		"fold"			=> array( 1 =>'vs_setting[timer]' ),
  		"group"			=>  __('Timer','visual-slider'),
  		"type"			=> "multi_options",
		"options"		=> array(
		array(
				"name"		=>  __('First Color','visual-slider'),
				"id"		=> "first",
				"type"		=> "color_rgba",
			),
			array(
				"name"		=>  __('Second Color','visual-slider'),
				"id"		=> "second",
				"type"		=> "color_rgba",
			),
		 
			array(
				"name"		=>  __('Orientation','visual-slider'),
				"id"		=> "orientation",
 				"type"		=> "select",
				"options"	=> array(
					"horizontal"		=>  __('Horizontal  →','visual-slider'),
					"vertical"			=>  __('Vertical  ↓','visual-slider'),
					"diagonal"			=>  __('Diagonal  ↘','visual-slider'),
					"diagonal-bottom"	=>  __('Diagonal Bottom  ↗','visual-slider'),
					"radial"			=>  __('Radial  ○','visual-slider'),
				),
			),
		),							
 	); 


	$option[]= array( 
		"name"			=> __('Auto Play','visual-slider'),
 		"id"			=> "auto",
 		"group"			=>  __('Slider','visual-slider'),
  		"type"			=> "checkbox",
		"default"		=>  1,
	); 	 	  		 

	$option[]= array( 
		"name"			=> __('Loop','visual-slider'),
 		"id"			=> "loop",
 		"group"			=>  __('Slider','visual-slider'),
  		"type"			=> "checkbox",
 	); 	 	  		 
		  
	$option[]= array( 
		"name"			=> __('Effect','visual-slider'),
 		"id"			=> "effect",
 		"group"			=>  __('Slider','visual-slider'),
		"default"		=>  'fade',
 		"type"			=> "radio",
		"options"		=>  array( 
			"slide"			=> __('Slide','visual-slider'),
 			"fade"			=> __('Fade','visual-slider'),
		),
  	); 	 
	$option[]= array( 
		"name"			=> __('Animation Speed','visual-slider'),
 		"id"			=> "speed",
		"default"		=>  '2000',
 		"min"		=>  '0',
		"max"		=>  '10000',
		"step"		=>  '100',

		"unit"		=>  'ms',
		
 		"group"			=>  __('Slider','visual-slider'),
 		"type"			=> "number",
   	); 	 
	
	$option[]= array( 
		"name"			=> __('Animation Pause Time','visual-slider'),
 		"id"			=> "pause",
 		"group"			=>  __('Slider','visual-slider'),
		"default"		=>  '10000',
		"max"		=>  '20000',
		"step"		=>  '100',
 		"unit"		=>  'ms',
 		"min"		=>  '0',		
 		"type"			=> "number",
   		
  	); 	   	
	
	
	$option[]= array( 
		"name"			=> __('Background Color','visual-slider'),
 		"id"			=> "background_color",
  		"group"			=>  __('Style','visual-slider'),
 		"type"			=> "color",
 	); 	 
	$option[]= array( 
		"name"			=> __('Border','visual-slider'),
 		"id"			=> "border",
  		"group"			=>  __('Style','visual-slider'),
 		"type"			=> "multi_options",
		"options"		=>  vs_multi_array_options('border'),						
	); 	
	
	$option[]= array( 
		"name"			=> __('Shadow','visual-slider'),
 		"id"			=> "shadow",
  		"group"			=>  __('Style','visual-slider'),
 		"type"			=> "multi_options",
		"options"		=>  vs_multi_array_options('shadow'),						
	); 	
	
	
 
	
	$option[]= array( 
		"name"			=> __('Radius','visual-slider'),
 		"id"			=> "radius",
  		"group"			=>  __('Style','visual-slider'),
 		"type"			=> "multi_options",
		"options"		=>  vs_multi_array_options('radius'),						
	); 	
		
 
		
		
	$option[]= array( 
			"responsive"	=>  "desktop",		
	
		"name"			=> __('Margin','visual-slider'),
 		"id"			=> "margin",
  		"group"			=>  __('Spaces','visual-slider'),
 		"type"			=> "multi_options",
		"options"		=>  vs_multi_array_options('margin'),						
	); 	
		
		
	$option[]= array( 
		"responsive"	=>  "desktop",		
 		"name"			=> __('Padding','visual-slider'),
 		"id"			=> "padding",
  		"group"			=>  __('Spaces','visual-slider'),
 		"type"			=> "multi_options",
		"options"		=>  vs_multi_array_options('padding'),						
	); 	
		
  			  
	$option[]= array( 
			"responsive"	=>  "tablet",		
			"name"			=> __('Responsive on Tablet','visual-slider'),
  			"group"			=>  __('Spaces','visual-slider'),
			"id"			=>  "padding_heading",
 			"type"			=> "heading",
	);
	$option[]= array( 
		"responsive"	=>  "tablet",		
		"name"			=> __('Margin on Tablet','visual-slider'),
   		"id"			=> "tablet_margin", 
 		"type"			=> "multi_options",
		"group"			=>  __('Spaces','visual-slider'),
		"options"		=>  vs_multi_array_options('margin'),						
  
 	); 	
	
	$option[]= array( 
		"responsive"	=>  "tablet",		
		"name"			=> __('Padding','visual-slider'),
		"id"			=> "tablet_padding", 
 		"type"			=> "multi_options",
		"group"			=>  __('Spaces','visual-slider'),
		"options"		=>  vs_multi_array_options('padding'),						
  
 	); 	
	
	
	$option[]= array( 
			"responsive"	=>  "mobile",		
			"name"			=> __('Responsive on Mobile','visual-slider'),
  			"group"			=>  __('Spaces','visual-slider'),
			"id"			=>  "mobile_heading",
 			"type"			=> "heading",
	);
	
	
	$option[]= array( 
		"responsive"	=>  "mobile",		
		"name"			=> __('Margin on Mobile','visual-slider'),
   		"id"			=> "mobile_margin", 
 		"type"			=> "multi_options",
		"group"			=>  __('Spaces','visual-slider'),
		"options"		=>  vs_multi_array_options('margin'),						
  
 	); 	
	
	$option[]= array( 
		"responsive"	=>  "mobile",		
		"name"			=> __('Padding on Mobile','visual-slider'),
		"id"			=> "mobile_padding", 
 		"type"			=> "multi_options",
		"group"			=>  __('Spaces','visual-slider'),
		"options"		=>  vs_multi_array_options('padding'),						
  
 	); 	
	
	
	
	 
 	$option[]= array( 
		"name"			=> __('Disable Default Fonts','visual-slider'),
 		"id"			=> "disable_typography",
 		"group"			=>  __('Typography','visual-slider'),
 		"desc"			=> __('By checking this option, Element default fonts will be disabled and Element will take its fonts from your skin.',"visual-slider"),
 		"type"			=> "checkbox",
	); 	
	 
	
	  
 		
 
    return $option;
} 
}
  