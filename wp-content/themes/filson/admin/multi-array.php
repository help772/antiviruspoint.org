<?php

function hexwp_multi_array_options($value) {
	$none_default=array(''=>__('Default','hexwp'));

 /******************************************************************************************************************************************************
																		Panel Radius
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
 	$options['border'] = array( 
				array( 
					"name"			=> esc_html__('Size','hexwp'),			
					"id"			=> "size",
					"type"			=> "select",
					"options"		=>  hexwp_array_options('border'),
				),
							
				array( 
					"id"			=> "color",
					"type"			=> "color_rgba",
				),	
			 					
	); 
	
	$options['shadow'] = array( 
 
			array( 
 				"name"			=> 	esc_html__('Size','hexwp'),
 				"id"			=> "size",
  				"type"			=> "select",
				"options"		=>  hexwp_array_options('shadow'),
 			),	
			array( 
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
		 
		);
			
		$options['shadow_mini'] = array( 
 
			array( 
				"name"			=> esc_html__('Blur','hexwp'),
				"id"			=> "blur",
 				"type"			=> "number",
 				"unit"			=> "px",
 			),
			array( 
				"name"			=>  esc_html__('Spread','hexwp'),
  				"id"			=> "spread",
 				"type"			=> "number",
 				"unit"			=> "px",
 			),	
		 
			array( 
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
		 
		);
			
			$options['shadow_element'] = array( 
 
			array( 
 				"name"			=> 	esc_html__('Size','hexwp'),
 				"id"			=> "size",
  				"type"			=> "select",
				"options"		=>  hexwp_array_options('shadow_element_inset',true),
 			),	
			array( 
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
		 
		);
				
		$options['border_mini'] = array( 
 
			array( 
				"name"			=>  esc_html__('Size','hexwp'),  
  				"id"			=> "size",
 				"type"			=> "number",
 				"unit"			=> "px",
 			),
			array( 
   				"id"			=> "position",
				"type"			=> "select",
				"options"		=> array(
  					"round"			=>	esc_html__('All Round','hexwp'),  
					"top"			=>	esc_html__('Top','hexwp'),  
					"right"			=>	__('Right','hexwp'),  
					"bottom"		=>	esc_html__('Bottom','hexwp'),  
					"left"			=>	__('Left','hexwp'),   
   					"top-bottom"	=>	esc_html__('Top And Bottom','hexwp'), 
 				),
			),
 			array( 
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
		 
		);
		
	
		$options['border_mini_2'] = array( 
 
			array( 
				"name"			=>  esc_html__('Size','hexwp'),  
  				"id"			=> "size",
 				"type"			=> "number",
 				"unit"			=> "px",
 			),
			array( 
   				"id"			=> "position",
				"type"			=> "select",
				"options"		=> array(
					"top"			=>	esc_html__('Top','hexwp'),  
					"bottom"		=>	esc_html__('Bottom','hexwp'),  
    					"top-bottom"	=>	esc_html__('Top And Bottom','hexwp'), 
 				),
			),
 			array( 
   				"id"			=> "color",
 				"type"			=> "color_rgba",
 			),				
		 
		);		
			
		$options['radius_mini'] = array( 
 
			array( 
				"name"			=> __('Size','hexwp'),			
  				"id"			=> "size",
				"unit"			=> "px",
				"type"			=> "number",
 			),
 			array(
  				"id"			=> "position",
  				"type"			=> "select",
				"options"		=> array(
											"round"							=>	esc_html__('All Round','hexwp'),  
											"top"							=>	esc_html__('Top','hexwp'),  
											"bottom"						=>	esc_html__('Bottom','hexwp'),  
											"top-right-bottom-left"			=>	esc_html__('Top Right & Bottom Left','hexwp'),
											"top-left-bottom-right"			=> 	esc_html__('Top Left & Bottom Right','hexwp'),
 									)
				)
	 		
		 
		);
				
			
  	
	$options['margin'] = array( 
			array( 
				"name"			=> esc_html__('Top','hexwp'),			
  				"id"			=> "top",
				"type"			=> "number",
 			),
			array( 
				"name"			=> esc_html__('Right','hexwp'),
 				"id"			=> "right",
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Bottom','hexwp'),
    				"id"			=> "bottom",
 				"type"			=> "number",
 			),
			array( 
				"name"			=>  esc_html__('Left','hexwp'),
  				"id"			=> "left",
 				"type"			=> "number",
 			),	
			array( 
 				"name"			=> 	esc_html__('Unit','hexwp'),
 				"id"			=> "unit",
  				"type"			=> "select",
				"options"		=>  hexwp_array_options('unit'),
 			),
  	);
	
	$options['meta'] = array( 
	 
			array( 
				"name"			=> esc_html__('Author','hexwp'),
 				"id"			=> "meta_author",
 				"type"			=> "checkbox",
 			),	
			array( 
				"name"			=> esc_html__('Category','hexwp'),
				"id"			=> "meta_category",
 				"type"			=> "checkbox",
 			), 
			array( 
				"name"			=> esc_html__('Date','hexwp'),
				"id"			=> "meta_date",
 				"type"			=> "checkbox",
 			), 
			
 			array( 
				"name"			=> esc_html__('View','hexwp'),
				"id"			=> "meta_view",
 				"type"			=> "checkbox",
 			),			
			array( 
				"name"			=>  esc_html__('Comments','hexwp'),
  				"id"			=> "meta_comments",
 				"type"			=> "checkbox",
 			),	
			
			
			 
 		); 
	$options['portfolio_meta'] = array( 
	 
			array( 
				"name"			=> esc_html__('Author','hexwp'),
 				"id"			=> "meta_author",
 				"type"			=> "checkbox",
 			),	
			array( 
				"name"			=> esc_html__('Category','hexwp'),
				"id"			=> "meta_category",
 				"type"			=> "checkbox",
 			), 
			array( 
				"name"			=> esc_html__('Date','hexwp'),
				"id"			=> "meta_date",
 				"type"			=> "checkbox",
 			), 
			
 			array( 
				"name"			=> esc_html__('View','hexwp'),
				"id"			=> "meta_view",
 				"type"			=> "checkbox",
 			),			
			 
			
			 
 		); 
	
	$options['meta_layout'] = array( 	
		array( 	"name"			=> __('Location','hexwp'),			
			"id"			=> "location",
			"type"			=> "select",
			"options"		=>	array( 	
					''				=>	__('Default','hexwp'),
				"title-top"			=> __('Top Title','hexwp'),
				"title-bottom"		=> __('Bottom Title','hexwp'),
				"details-bottom"	=> __('Bottom Details','hexwp'),
			),	
		),
		array( 
			"name"			=> __('Between','hexwp'),			
			"id"			=> "between",
			"type"			=> "select",
			"options"		=>	array( 	
					''				=>	__('Default','hexwp'),
				"between-1"		=> __('empty','hexwp'),	
				"between-2"		=> __('-','hexwp'),	
				"between-3"		=> __('|','hexwp'),	
				"between-4"		=> __('/','hexwp'),	
			),
		),
		array( 
			"name"			=> __('Layout','hexwp'),			
			"id"			=> "layout",
			"type"			=> "select",
			"options"		=>	array( 	
					''				=>	__('Default','hexwp'),
				"layout-1"		=> __('no Icon','hexwp'),
				"layout-2"		=> __('no Icon & Avater Author','hexwp'),
				"layout-3"		=> __('by Icon','hexwp'),	
				"layout-4"		=> __('by Icon & no Icon Author ','hexwp'),	
				"layout-5"		=> __('by Icon & Avater Author','hexwp'),
			),
		),
	);	
 	
	$options['typo'] = array( 
	 
			array( 
				"name"			=> esc_html__('Font Size','hexwp'),
 				"id"			=> "font_size",
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Font Weight','hexwp'),
				"id"			=> "font_weight",
 				"type"			=> "select",
				"options"		=>  array( 
					''				=>	__('Default','hexwp'),
 					"300"			=> esc_html__('Light','hexwp'),
					"normal"		=> esc_html__('Normal','hexwp'),
					"500"			=> esc_html__('Medium','hexwp'),
					"bold"			=> esc_html__('Bold','hexwp'),
					"900"			=> esc_html__('Extra-Bold','hexwp'),
					) ,
 			), 
 			array( 
				"name"			=> esc_html__('Text Transform','hexwp'),
				"id"			=> "text_transform",
 				"type"			=> "select",
				"options"		=>  array( 
						''				=>	__('Default','hexwp'),
  						"none"			=> 	__('None','hexwp'),
 						"uppercase"			=> 	__('Uppercase','hexwp'),
 						"lowercase"			=> __('Lowercase','hexwp'),
  						"capitalize"			=> __('Capitalize','hexwp'),
				) ,
				) ,
		);

	$options['typo_icon'] = array( 
	 
			array( 
				"name"			=> esc_html__('Font Size','hexwp'),
 				"id"			=> "font_size",
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Font Weight','hexwp'),
				"id"			=> "font_weight",
 				"type"			=> "select",
				"options"		=>  array( 
					''				=>	__('Default','hexwp'),
 					"300"			=> esc_html__('Light','hexwp'),
					"normal"		=> esc_html__('Normal','hexwp'),
					"500"			=> esc_html__('Medium','hexwp'),
					"bold"			=> esc_html__('Bold','hexwp'),
					"900"			=> esc_html__('Extra-Bold','hexwp'),
					) ,
 			), 
 			array( 
				"name"			=> esc_html__('Text Transform','hexwp'),
				"id"			=> "text_transform",
 				"type"			=> "select",
				"options"		=>  array( 
						''				=>	__('Default','hexwp'),
  						"none"			=> 	__('None','hexwp'),
 						"uppercase"			=> 	__('Uppercase','hexwp'),
 						"lowercase"			=> __('Lowercase','hexwp'),
  						"capitalize"			=> __('Capitalize','hexwp'),
				) ,
			) ,
 			array( 
				"name"			=> esc_html__('Icon Size','hexwp'),
				"id"			=> "icon_size",
 				"type"			=> "select",
				"options"		=> hexwp_array_options('icon_size',true)
				) ,				
		);			
	 
	
	
	$options['typo_mini'] = array( 
	 
			 
			array( 
				"name"			=> esc_html__('Font Weight','hexwp'),
				"id"			=> "font_weight",
 				"type"			=> "select",
				"options"		=>  array( 
					''				=>	__('Default','hexwp'),
 					"300"			=> esc_html__('Light','hexwp'),
					"normal"		=> esc_html__('Normal','hexwp'),
					"500"			=> esc_html__('Medium','hexwp'),
					"bold"			=> esc_html__('Bold','hexwp'),
					"900"			=> esc_html__('Extra-Bold','hexwp'),
					) ,
 			), 
 			array( 
				"name"			=> esc_html__('Text Transform','hexwp'),
				"id"			=> "text_transform",
 				"type"			=> "select",
				"options"		=>  array( 
						''				=>	__('Default','hexwp'),
  						"none"			=> 	__('None','hexwp'),
 						"uppercase"			=> 	__('Uppercase','hexwp'),
 						"lowercase"			=> __('Lowercase','hexwp'),
  						"capitalize"			=> __('Capitalize','hexwp'),
				) ,
				) ,
		);	
	
$options['typo_style'] = array( 
	 
			array( 
				"name"			=> esc_html__('Font Size','hexwp'),
 				"id"			=> "font_size",
 				"type"			=> "number",
 			),	
			array( 
				"name"			=> esc_html__('Font Weight','hexwp'),
				"id"			=> "font_weight",
 				"type"			=> "select",
				"options"		=>  array( 
					""				=> esc_html__('Default','hexwp'),
					"300"			=> esc_html__('Light','hexwp'),
					"normal"		=> esc_html__('Normal','hexwp'),
					"500"			=> esc_html__('Medium','hexwp'),
					"bold"			=> esc_html__('Bold','hexwp'),
					"900"			=> esc_html__('Extra-Bold','hexwp'),
					) ,
 			), 
 			array( 
				"name"			=> esc_html__('Text Transform','hexwp'),
				"id"			=> "text_transform",
 				"type"			=> "select",
				"options"		=>  array( 
						""					=> __('Default','hexwp'),
 						"none"			=> 	__('none','hexwp'),
 						"uppercase"			=> 	__('Uppercase','hexwp'),
 						"lowercase"			=> __('Lowercase','hexwp'),
  						"capitalize"			=> __('Capitalize','hexwp'),
				),
			) ,
			array( 
					"name"			=> esc_html__('Font Style','hexwp'),
					"id"			=> "font_style",
					"type"			=> "select",
					"options"		=>  array( 
						''				=>esc_html__('Default','hexwp'), 
						'normal'		=> esc_html__('Normal','hexwp'), 
						'italic'		=> esc_html__('Italic','hexwp'), 
						'oblique'		=> esc_html__('Oblique','hexwp'), 
					) ,	
				) ,					
				
			 
 		);		
$options['typo_line'] = array( 
	 
			array( 
				"name"			=> esc_html__('Font Size','hexwp'),
 				"id"			=> "font_size",
 				"type"			=> "number",
 			),
			
			array( 
				"name"			=> esc_html__('Line Height','hexwp'),
 				"id"			=> "line_height",
 				"type"			=> "select",
				"options"		=>  array( 
 						"1em"		=> "1em",
						"1.05em"	=> "1.05em",
						"1.1em"		=> "1.1em",
						"1.15em"	=> "1.15em",
						"1.2em" 	=> "1.2em",
						"1.2em" 	=> "1.25em",
						"1.3em" 	=> "1.3em",
						"1.35em"	=> "1.35em",
						"1.4em" 	=> "1.4em",
						"1.45em"	=> "1.45em",
						"1.5em" 	=> "1.5em",
 						"1.6em" 	=> "1.6em",
 						"1.7em" 	=> "1.7em",
 						"1.8em" 	=> "1.8em",
 						"1.9em" 	=> "1.9em",
 						"2em" 		=> "2em",
						"2.1em" 		=> "2.1em",
						"2.2em" 		=> "2.2em",
						"2.3em" 		=> "2.3em",
						"2.4em" 		=> "2.4em",
						"2.5em" 		=> "2.5em",
   			),	
			 
 			 
				) ,
		);				
	 
	
return $options[$value];
}	
