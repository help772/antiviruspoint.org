<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Radius Mini
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Radius Mini
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(is_admin()){
if( ! function_exists( 'vs_array_options' ) ) {
function vs_array_options($value) {
	
		
	$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
 			sanitize_key('meta_value') => '1',
			'child_of' => 0,
			'parent' => -1,
			'post_type' => 'visual-slider',
			'post_status' => 'publish'
		); 
		 
		$options_page = array();
		$options_page_obj =get_posts($page_args); 
 		$options_page[''] = __('Default','visual-slider');

		if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $rezapage) {
			$options_page[$rezapage->ID] = $rezapage->post_title;
		}
	}	
	
	$options['visualslider_loop'] = $options_page;
	
 	$options['unit']= array(
		'px'				=>	'px',
		'%'					=>	'%', 
		'em'				=>	'em',
 		
	);
	
	$options['align']= array(
		'left'				=> is_rtl() ? esc_html__('Right','visual-slider'):esc_html__('Left','visual-slider'),  
		'center'			=> esc_html__('Center','visual-slider'), 
		'right'				=> is_rtl() ? esc_html__('Left','visual-slider'):esc_html__('Right','visual-slider'),  
   		
	);	
	
$options['align_mini']= array(
 		'left'				=> is_rtl() ? esc_html__('Right','visual-slider'):esc_html__('Left','visual-slider'),  
 		'right'				=> is_rtl() ? esc_html__('Left','visual-slider'):esc_html__('Right','visual-slider'),  
   		
	);		
		$options['button'] = array(
 			'style-1'					=> esc_html__('Style 1','visual-slider'),  
  			'style-2'					=> esc_html__('Style 2:Top Border inset','visual-slider'),  
 			'style-3'					=> esc_html__('Style 3:Bottom Border inset','visual-slider'),  
 			'style-4'					=> is_rtl()?  __('Style 5:Right Border inset','visual-slider'): __('Style 4:Left Border inset','visual-slider'),  
 			'style-5'					=> is_rtl()?   __('Style 4:Left Border inset','visual-slider'): __('Style 5:Right Border inset','visual-slider'),  
 			'style-6'					=> esc_html__('Style 6:Icon Background color','visual-slider'),   
 			'style-7'					=> esc_html__('Style 7:none boxed','visual-slider'),   
 	); 	
 $options['border_style']= array(
	
		'solid'			=>esc_html__('Solid','visual-slider'), 
		'dotted'		=> esc_html__('Dotted','visual-slider'), 
		'dashed'		=> esc_html__('Dashed','visual-slider'), 
		'double'		=> esc_html__('Double','visual-slider'), 
		'groove'		=> esc_html__('Groove','visual-slider'),  
  		 
  		
		
 	);
	
	
	
	$options['image_position']=  array(
		''			=> __('Default','visual-slider'),
		'center'			=> __('Center','visual-slider'),
		'left'				=> is_rtl()?__('Right','visual-slider'):__('Left','visual-slider'), 
		'left top'			=> is_rtl()?__('Right Top','visual-slider'): __('Left Top','visual-slider'), 
		'left bottom'		=> is_rtl()?__('Right Bottom','visual-slider'):__('Left Bottom','visual-slider'), 
		'left center'		=> is_rtl()?__('Right Center','visual-slider'): __('Left Center','visual-slider'), 
		'right' 			=> is_rtl()? __('Left','visual-slider'):__('Right','visual-slider'), 
		'right top'			=> is_rtl()? __('Left Top','visual-slider'): __('Right Top','visual-slider'), 
		'right bottom'		=> is_rtl()? __('Left Bottom','visual-slider'): __('Right Bottom','visual-slider'), 
		'right center'		=> is_rtl()? __('Left Center','visual-slider'): __('Right Center','visual-slider'),
		'top'				=> __('Top','visual-slider'),
		'top center'		=> __('Top Center','visual-slider'),
		'bottom'			=> __('Bottom','visual-slider'),
		'bottom center'		=> __('Bottom Center','visual-slider'),
	);	
	
	
	
	
	
if(is_rtl()){
 $options['fontfamily']= array(
		'' => 'پیشفرض',
		'vs-iransans' => 'ایران سنس',
		'vs-vazir' => 'وزیر',
		'vs-yekan' => 'یکان',
		"vs-sahel" => "ساحل",
		"vs-parastoo" => "پرستو",
		"vs-samim" => "صمیم",
		"vs-shabnam" => "شبنم",
		"vs-nahid" => "ناهید",
		 "vs-segoe" => 'سگو',
		"vs-tahoma" => "تاهوما",
	); 	
 }else{
 $options['fontfamily']=  vs_google_fonts();

}
 
 
 
 
 
 
return $options[$value];
 }	
}
if( ! function_exists( 'vs_multi_array_options' ) ) {
function vs_multi_array_options($value) {
		 $options['background2'] =array(
				array(	"id"		=> "first",
						"type"		=> "color_rgba",
  					),
					array(
 						"id"		=> "second",
						"type"		=> "color_rgba",
  					),	
			 					
					array(
 						"id"		=> "orientation",
						"type"		=> "select",
						"options"	=> array(
							"horizontal"		=>  esc_html__('→','visual-slider'),
							"vertical"			=>  esc_html__('↓','visual-slider'),
							"diagonal"			=>  esc_html__('↘','visual-slider'),
							"diagonal-bottom"	=>  esc_html__('↗','visual-slider'),
							"radial"			=>  esc_html__('○','visual-slider'),

  						),
				),
	);	
	$options['layer_padding'] = array( 
			array( 
				"name_bottom"			=> esc_html__('Top','visual-slider'),			
  				"id"			=> "top",
				"width"			=> "40px",
				"min"			=> "-1000",
				"max"			=> "10000",
				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=> is_rtl()? __('Left','visual-slider'):__('Right','visual-slider'),  
 				"id"			=> "right",
				"width"			=> "40px",
				"min"			=> "-1000",
				"max"			=> "10000",		
 				"type"			=> "number",
 			),	
			array( 
				"name_bottom"			=> esc_html__('Bottom','visual-slider'),
				"id"			=> "bottom",
				"width"			=> "40px",
				"min"			=> "-1000",
				"max"			=> "10000",			
 				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=> is_rtl()? __('Right','visual-slider'): __('Left','visual-slider'),
  				"id"			=> "left",
				"width"			=> "40px",
				"min"			=> "-1000",
				"max"			=> "10000",		
 				"type"			=> "number",
 			),	
			array( 
				"name_bottom"			=>  __('Unit','visual-slider'),
  				"id"			=> "unit",
  				"type"			=> "select",
				"width"			=> "auto",
 				"options"		=>  vs_array_options('unit'),
 			),
  	);
	
	
	
	
$options['margin'] = array( 
			array( 
				"name"			=> esc_html__('Top','visual-slider'),			
  				"id"			=> "top",
				"width"			=> "40px",
  				"min"			=> "-10000",
 				"max"			=> "10000",
				"type"			=> "number",
 			),
			array( 
				"name"			=> is_rtl()? __('Left','visual-slider'):__('Right','visual-slider'),  
 				"id"			=> "right",
				"width"			=> "40px",
  				"min"			=> "-10000",
 				"max"			=> "10000",		
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Bottom','visual-slider'),
				"id"			=> "bottom",
				"width"			=> "40px",
 				"max"			=> "10000",			
 				"type"			=> "number",
 			),
			array( 
				"name"			=> is_rtl()? __('Right','visual-slider'): __('Left','visual-slider'),
  				"id"			=> "left",
				"width"			=> "40px",
  				"min"			=> "-10000",
 				"max"			=> "10000",		
 				"type"			=> "number",
 			),	
			array( 
				"name"			=>  __('Uint','visual-slider'),
  				"id"			=> "unit",
  				"type"			=> "select",
				"width"			=> "auto",
	
				"options"		=>  vs_array_options('unit'),
 			),
  	);
		
	
$options['padding'] = array( 
			array( 
				"name"			=> esc_html__('Top','visual-slider'),			
  				"id"			=> "top",
				"width"			=> "40px",
  				"max"			=> "10000",
				"type"			=> "number",
 			),
			array( 
				"name"			=> is_rtl()? __('Left','visual-slider'):__('Right','visual-slider'),  
 				"id"			=> "right",
				"width"			=> "40px",
  				"max"			=> "10000",		
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Bottom','visual-slider'),
				"id"			=> "bottom",
				"width"			=> "40px",
  				"max"			=> "10000",			
 				"type"			=> "number",
 			),
			array( 
				"name"			=> is_rtl()? __('Right','visual-slider'): __('Left','visual-slider'),
  				"id"			=> "left",
				"width"			=> "40px",
  				"max"			=> "10000",		
 				"type"			=> "number",
 			),	
			array( 
				"name"			=>  __('Uint','visual-slider'),
  				"id"			=> "unit",
  				"type"			=> "select",
				"width"			=> "auto",
	
				"options"		=>  vs_array_options('unit'),
 			),
  	);
	
	
	
	$options['text_shadow'] = array( 
			array( 
				"name_bottom"			=> esc_html__('Horizontal','visual-slider'),			
  				"id"			=> "horizontal",
 				"width"			=> "40px", 
 				"min"			=> "-1000",
				"max"			=> "10000",		
 
				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=> esc_html__('Vertical','visual-slider'),
 				"id"			=> "vertical",
 				"width"			=> "40px", 
				"min"			=> "-1000",
				"max"			=> "10000",		
						
  				"type"			=> "number",
 			),	
			array( 
				"name_bottom"			=> esc_html__('Blur','visual-slider'),
				"id"			=> "blur",
 				"width"			=> "40px", 
				"min"			=> "0",
				"max"			=> "10000",		
  				"type"			=> "number",
 			),
 		  
			array( 
				"name_bottom"			=> esc_html__('Color','visual-slider'),
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
			 
			);
 
 
 $options['border'] = array( 
			array( 
				"name"			=> esc_html__('Top','visual-slider'),			
  				"id"			=> "top",
 				"width"			=> "40px",
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",	
				"type"			=> "number",
 			),
			array( 
				"name"			=> is_rtl()?__('Left','visual-slider'):__('Right','visual-slider'),
 				"id"			=> "right",
 				"width"			=> "40px",
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Bottom','visual-slider'),
				"id"			=> "bottom",
 				"width"			=> "40px",
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",
 				"type"			=> "number",
 			),
			array( 
				"name"			=> is_rtl()?__('Right','visual-slider'):__('Left','visual-slider'),
  				"id"			=> "left",
 				"width"			=> "40px",	
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",
 				"type"			=> "number",
 			),	
	 
			array( 
 				"name"			=> 	esc_html__('Style','visual-slider'),
 				"id"			=> "style",				
  				"type"			=> "select",
				"width"			=> "auto",
 				"options"		=>  vs_array_options('border_style'),
 			),			
						
			array( 
 				"name"			=> 	esc_html__('Color','visual-slider'),
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),	
  		); 
		
 	$options['layer_border'] = array( 
			array( 
				"name_bottom"			=> esc_html__('Top','visual-slider'),			
  				"id"			=> "top",
 				"width"			=> "40px",
   				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=> is_rtl()?__('Left','visual-slider'):__('Right','visual-slider'),
 				"id"			=> "right",
 				"width"			=> "40px",
    				"type"			=> "number",
 			),	
			array( 
				"name_bottom"			=> esc_html__('Bottom','visual-slider'),
				"id"			=> "bottom",
 				"width"			=> "40px",
				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=> is_rtl()?__('Right','visual-slider'):__('Left','visual-slider'),
  				"id"			=> "left",
 				"width"			=> "40px",	
    				"type"			=> "number",
 			),	
	 
			array( 
 				"name_bottom"			=> 	esc_html__('Style','visual-slider'),
 				"id"			=> "style",				
  				"type"			=> "select",
				"width"			=> "auto",
 				"width"			=> "70px",	
 				"options"		=>  vs_array_options('border_style'),
 			),			
						
			array( 
 				"name_bottom"			=> 	esc_html__('Color','visual-slider'),
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),	
  		); 
		
		
		
		$options['shadow'] = array( 
			array( 
				"name"			=> esc_html__('Horizontal','visual-slider'),			
  				"id"			=> "horizontal",
 				"width"			=> "40px",	
				"min"			=> "-10000",
  				"unit"			=> "px",
				"type"			=> "number",
 			),
			array( 
				"name"			=> esc_html__('Vertical','visual-slider'),
 				"id"			=> "vertical",
 				"width"			=> "40px",
				"min"			=> "-10000",
  				"unit"			=> "px",								
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Blur','visual-slider'),
				"id"			=> "blur",
 				"width"			=> "40px",	
   				"unit"			=> "px",			
 				"type"			=> "number",
 			),
			array( 
				"name"			=>  esc_html__('Spread','visual-slider'),
  				"id"			=> "spread",
   				"unit"			=> "px",				
 				"width"			=> "40px",								
 				"type"			=> "number",
 			),	
		 
			array( 
				"name"			=> 	esc_html__('Color','visual-slider'),
				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
			array( 
			
			"name"			=> 	esc_html__('Position','visual-slider'),
   				"id"			=> "position",
  				"type"			=> "select",
				"width"			=> "auto",
				"options"		=>  array( 
   					""			=> esc_html__('Outset','visual-slider'),
 					"inset"		=> esc_html__('Inset','visual-slider'),
 				),
 			),
			);
			
			
	$options['layer_shadow'] = array( 
			array( 
				"name_bottom"			=> esc_html__('Horizontal','visual-slider'),			
  				"id"			=> "horizontal",
 				"width"			=> "40px",	
				"min"			=> "-10000",
 				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=> esc_html__('Vertical','visual-slider'),
 				"id"			=> "vertical",
 				"width"			=> "40px",
				"min"			=> "-10000",
  				"type"			=> "number",
 			),	
			array( 
				"name_bottom"			=> esc_html__('Blur','visual-slider'),
				"id"			=> "blur",
 				"width"			=> "40px",	
  				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=>  esc_html__('Spread','visual-slider'),
  				"id"			=> "spread",
  				"width"			=> "40px",								
 				"type"			=> "number",
 			),	
		 
			array( 
				"name_bottom"			=> 	esc_html__('Color','visual-slider'),
				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
			array( 
 				"name_bottom"			=> 	esc_html__('Position','visual-slider'),
   				"id"			=> "position",
  				"type"			=> "select",
				"width"			=> "auto",
				"options"		=>  array( 
   					""			=> esc_html__('Outset','visual-slider'),
 					"inset"		=> esc_html__('Inset','visual-slider'),
 				),
 			),
			);
			
			
			
			
 	$options['radius'] = array( 
			array( 
				"name"			=> is_rtl()? esc_html__('Top Right','visual-slider'): esc_html__('Top Left','visual-slider'),			
  				"id"			=> "top_left",
	 			"width"			=> "40px",	
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",					
				"type"			=> "number",
 			),
			array( 
				"name"			=>  is_rtl()?  esc_html__('Top Left','visual-slider'): esc_html__('Top Right','visual-slider'),
 				"id"			=> "top_right",
	 			"width"			=> "40px",	
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",							
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> is_rtl()?   esc_html__('Bottom Left','visual-slider'):esc_html__('Buttom Right','visual-slider'),
				"id"			=> "bottom_right",
	 			"width"			=> "40px",	
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",											
 				"type"			=> "number",
 			),
			array( 
				"name"			=>  is_rtl()?  esc_html__('Bottom Right','visual-slider'):esc_html__('Bottom Left','visual-slider'),
  				"id"			=> "bottom_left",
	 			"width"			=> "40px",	
				"min"			=> "0",
				"max"			=> "10000",
 				"unit"			=> "px",								
 				"type"			=> "number",
 			), 
 		
	 
  	);	
	
	
	
$options['layer_radius'] = array( 
			array( 
				"name_bottom"			=> is_rtl()? esc_html__('Top Right','visual-slider'): esc_html__('Top Left','visual-slider'),	
  				"id"			=> "top_left",
	 			"width"			=> "60px",	
 				"type"			=> "number",
 			),
			array( 
				"name_bottom"			=>   is_rtl()?  esc_html__('Top Left','visual-slider'): esc_html__('Top Right','visual-slider'),
 				"id"			=> "top_right",
	 			"width"			=> "60px",	
  				"type"			=> "number",
 			),	
			array( 
				"name_bottom" 	=>is_rtl()?   esc_html__('Bottom Left','visual-slider'):esc_html__('Buttom Right','visual-slider'),
				"id"			=> "bottom_right",
	 			"width"			=> "60px",	
  				"type"			=> "number",
 			),
			array( 
				"name_bottom" 	=>  is_rtl()?  esc_html__('Bottom Right','visual-slider'):esc_html__('Bottom Left','visual-slider'),
  				"id"			=> "bottom_left",
	 			"width"			=> "60px",	
  				"type"			=> "number",
 			), 
 		
	 
  	);	
		
	
	$options['size'] = array( 
			array( 
				"name"			=> __('Width','visual-slider'),			
  				"id"			=> "width",
 				"type"			=> "number",
				"width"			=> "60px",												
  				"unit"			=> "px",
 			),
			array( 
				"name"			=> __('Height','visual-slider'),			
  				"id"			=> "height",
 				"type"			=> "number",
 				"unit"			=> "px",
				"width"			=> "60px",												
  			), 
	 
  	);		
return $options[$value];
  
 }	
}
}
?>