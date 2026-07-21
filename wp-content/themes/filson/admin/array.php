<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	All Image Size
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_all_image_sizes() {
    global $_wp_additional_image_sizes;
    $default_image_sizes = get_intermediate_image_sizes();

    foreach ( $default_image_sizes as $size ) {
        $image_sizes[ $size ][ 'width' ] = intval( get_option( "{$size}_size_w" ) );
        $image_sizes[ $size ][ 'height' ] = intval( get_option( "{$size}_size_h" ) );
        $image_sizes[ $size ][ 'crop' ] = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
    }

    if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
        $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
    } 
	 
 	$image = array(  		''				=>	esc_html__('Default','hexwp'));
 	
  	foreach ($image_sizes as $key => $value) {
     	$image[esc_html($key)] = esc_html($key).' '.$value['width'].' x '.$value['height'];
	}	
 	$image['full'] = esc_html__('Full','hexwp');
	 
	return $image;	
	
	
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Defualt radius
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 

function hexwp_builder_element_border_radius($radius) {
	 
 	$radius=array( 
			"size" => intval(hexwp_option('main_radius')),
 	 );
	return $radius; 
}
add_action('sao_builder_border_radius', 'hexwp_builder_element_border_radius');


 function hexwp_elementor_border_radius($radius) {
 	$radius= intval(hexwp_option('main_radius'));
	return $radius; 
}
add_action('sao_elementor_border_radius', 'hexwp_elementor_border_radius');

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Defualt Padding
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_builder_element_padding($padding) {
 	$padding=array( 
			"top"			=> '30',
			"left"			=> '20',
			"bottom"		=> '30',
			"right"			=> '20',
 	);
	return $padding; 
}
add_action('sao_builder_element_padding', 'hexwp_builder_element_padding');
function hexwp_elementor_default_padding($padding) {
 	$padding= '20px';
	return $padding; 
}
add_action('sao_elementor_element_padding', 'hexwp_elementor_default_padding');
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Array Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_array_options($value,$default=false) {
  
  
 		
  $options['icon_size'] = array(
 								'1em'				=>  '1x', 
								'1.25em'			=> '1.25x', 
								'1.5em'				=> '1.5x',
								'1.75em'			=> '1.75x', 
								'2em'				=> '2x', 
								'2.25em'				=> '2.25x', 
								'2.5em'				=> '2.5x', 
								'2.75em'				=> '2.75x', 
								'3em'				=> '3x', 
								'3.5em'				=> '3.5x', 
								'4em'				=> '4x', 
								'4.5em'				=> '4.5x', 
								'5em'				=> '5x', 
 	); 
  /******************************************************************************************************************************************************
																		Panel Shadow
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
	$blur= __('Blur','hexwp');	
	$spread= __('Spread','hexwp');	
 	
	$options['shadow'] = array(
		""				=> __('None','hexwp'),
		"0px-0px" 			=> "0px $blur - 0px $spread",
		"3px-0px" 			=> "3px $blur - 0px $spread",
		"5px-0px" 			=> "5px $blur - 0px $spread",
		"7px-0px" 			=> "7px $blur - 0px $spread",
		"10px-0px" 			=> "10px $blur - 0px $spread",
		"15px-0px" 			=> "15px $blur - 0px $spread",
		"20px-0px" 			=> "20px $blur - 0px $spread",
		"25px-0px" 			=> "25px $blur - 0px $spread",
		"30px-0px" 			=> "30px $blur - 0px $spread",
		"0px-1px" 			=> "0px $blur - 1px $spread",
		"0px-2px" 			=> "0px $blur - 2px $spread",
		"0px-3px" 			=> "0px $blur - 3px $spread",
		"0px-5px" 			=> "0px $blur - 5px $spread",
		"0px-7px" 			=> "0px $blur - 7px $spread",
		"0px-10px" 			=> "0px $blur - 10px $spread",
		"0px-15px" 			=> "0px $blur - 15px $spread",
		"0px-20px" 			=> "0px $blur - 20px $spread",
		"3px-3px" 			=> "3px $blur - 3px $spread",
		"5px-5px" 			=> "5px $blur - 5px $spread",
		"7px-7px" 			=> "7px $blur - 7px $spread",
		"10px-10px"			=> "10px $blur - 10px $spread",
		"15px-15px"			=> "15px $blur - 15px $spread",
		"20px-20px"			=> "20px $blur - 20px $spread",
	); 
	
/******************************************************************************************************************************************************
																		Panel Radius
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 	
	$options['radius'] = array(
		"0px" 				=>  '0px',
		"3px"				=> '3px',
		"5px" 				=> '5px',
		"7px" 				=> '7px',
		"10px" 				=> '10px',
		"15px" 				=> '15px',
		"20px" 				=> '20px',
		"25px" 				=> '25px',
		"30px" 				=> '30px',
		"1000px"			=> __('Circular','hexwp'),
 	); 	
	$Shadow= __('Shadow','hexwp');	
	$border= __('Border','hexwp');	
	$vertical= __('Vertical','hexwp');	
 	 
	$options['shadow_element'] = array(
		"0px" 			=> 	 __('none','hexwp'),
		"0px-0px-3px-0px" 			=> "3px $Shadow",
		"0px-0px-5px-0px" 			=> "5px $Shadow",
		"0px-0px-7px-0px" 			=> "7px $Shadow",
		"0px-0px-10px-0px" 			=> "10px $Shadow",
		"0px-0px-15px-0px" 			=> "15px $Shadow", 
		"0px-0px-20px-0px" 			=> "20px $Shadow", 
 		"0px-1px-3px-0px" 			=> "3px $Shadow $vertical",
		"0px-2px-5px-2px" 			=> "5px $Shadow $vertical",
		"0px-3px-7px-3px" 			=> "7px $Shadow $vertical",
		"0px-4px-10px-4px" 			=> "10px $Shadow $vertical",
		"0px-5px-15px-5px" 			=> "15px $Shadow $vertical", 
		"0px-10px-20px-5px" 			=> "20px $Shadow $vertical", 
  		"0px-0px-0px-1px" 			=> "1px $border",
		"0px-0px-0px-2px" 			=> "2px $border",
		"0px-0px-0px-3px" 			=> "3px $border",
		"0px-0px-0px-5px" 			=> "5px $border",  
	); 
	$options['radius_mini'] = array(
		"0px" 				=>  '0px',
		"3px"				=> '3px',
		"5px" 				=>  '5px',
		"7px" 				=>  '7px',
		"10px" 				=>  '10px',
		"15px" 				=>  '15px',
		"20px" 				=>  '20px',
		"25px" 				=>  '25px',
		"30px" 				=> '30px',
  	); 			
/******************************************************************************************************************************************************
																		Panel Radius
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
 	$allround= __('All Round','hexwp');	
	$top= __('Top','hexwp');	
	$left= __('Left','hexwp');	
	$right= __('Right','hexwp');	
	$bottom= __('Bottom','hexwp');	
	$topbottom= __('Top Bottom','hexwp');	
 	
	
	$options['border'] = array(
		"0px" 				=> "0px",
		"1px" 				=> "1px $allround",
		"2px" 				=> "2px $allround",
		"3px" 				=> "3px $allround",
		"5px" 				=> "5px $allround",
		"1px-top"			=> "1px $top",
		"2px-top" 			=> "2px $top",
		"3px-top" 			=> "3px $top",
		"5px-top" 			=> "5px $top",
		"1px-left" 			=> "1px $left",
		"2px-left" 			=> "2px $left",
		"3px-left" 			=> "3px $left",
		"5px-left" 			=> "5px $left",
		"1px-bottom" 		=> "1px $bottom",
		"2px-bottom" 		=> "2px $bottom",
		"3px-bottom" 		=> "3px $bottom",
		"5px-bottom" 		=> "5px $bottom",
		"1px-top-bottom"	=> "1px $topbottom",
		"2px-top-bottom"	=> "2px $topbottom",
		"3px-top-bottom"	=> "3px $topbottom",
		"5px-top-bottom"	=> "5px $topbottom",
					
  	); 	
	
$options['between_border'] = array(
		"none"				=> __('None','reza'),
		"border-1" 		=> __('Solid','reza'),
		"border-2" 		=> __('Dashed','reza'),
   	); 		
 /******************************************************************************************************************************************************
																		Responsvie Columns
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$in_tablet= __('in Table','hexwp');	
	$column= __('Column','hexwp');	
	$in_mobile= __('in Mobile','hexwp');	
	$options['responsive_column'] = array(
		"tab_1_mob_1"			=> "$in_tablet 1 $column , $in_mobile 1 $column",
		"tab_2_mob_1"			=> "$in_tablet 2 $column , $in_mobile 1 $column",
		"tab_3_mob_1"			=> "$in_tablet 3 $column , $in_mobile 1 $column",
		"tab_4_mob_1"			=> "$in_tablet 4 $column , $in_mobile 1 $column",
		"tab_2_mob_2"			=> "$in_tablet 2 $column , $in_mobile 2 $column",
		"tab_3_mob_2"			=> "$in_tablet 3 $column , $in_mobile 2 $column",
		"tab_4_mob_2"			=> "$in_tablet 4 $column , $in_mobile 2 $column",
  	); 	
 /******************************************************************************************************************************************************
																		Responsvie Columns
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
	$first= __('First','hexwp');	
	$options['first_responsive_column'] = array(
		"tab_1_mob_1"			=> "$in_tablet 1 $column , $in_mobile 1 $column",
		"tab_2_mob_1"			=> "$in_tablet 2 $column , $in_mobile 1 $column",
		"tab_3_mob_1"			=> "$in_tablet 3 $column , $in_mobile 1 $column",
		"tab_4_mob_1"			=> "$in_tablet 4 $column , $in_mobile 1 $column",
		"tab_2_mob_2"			=> "$in_tablet 2 $column , $in_mobile 2 $column",
		"tab_3_mob_2"			=> "$in_tablet 3 $column , $in_mobile 2 $column",
		"tab_4_mob_2"			=> "$in_tablet 4 $column , $in_mobile 2 $column",
		"first_tab_2_mob_1"		=> "$first 1 $column , $in_tablet 2 $column , $in_mobile 1 $column",
		"first_tab_3_mob_1"		=> "$first 1 $column , $in_tablet 3 $column , $in_mobile 1 $column",
		"first_tab_4_mob_1"		=> "$first 1 $column , $in_tablet 4 $column , $in_mobile 1 $column",
		"first_tab_2_mob_2"		=> "$first 1 $column , $in_tablet 2 $column , $in_mobile 2 $column",
		"first_tab_3_mob_2"		=> "$first 1 $column , $in_tablet 3 $column , $in_mobile 2 $column",
		"first_tab_4_mob_2"		=> "$first 1 $column , $in_tablet 4 $column , $in_mobile 2 $column",
  	); 	
	
/******************************************************************************************************************************************************
																		Between
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
	$options['between'] = array(
		"0px" 	=> '0px',
		"2px" 	=> '2px', 
		"5px" 	=> '5px', 
		"10px" 	=> '10px', 
		"15px" 	=> '15px', 
		"20px" 	=> '20px', 
		"30px" 	=> '30px', 
		"40px" 	=> '40px', 
		"60px" 	=> '60px',
					
  	); 	
			
 
 
 /******************************************************************************************************************************************************
																		Ratio
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 	
	$options['ratio'] = array(
		'hw-ratio-auto'				=>	__('Auto','hexwp'),
 		'hw-ratio40'				=>	'5:2',
 		'hw-ratio50'				=>	'1:2',
 		'hw-ratio55'				=>	'11:20',
 		'hw-ratio60'				=>	'5:3',
		'hw-ratio75'				=>	'4:3', 
		'hw-ratio100'				=>	'1:1',
		'hw-ratio135'				=>	'3:5',
  	); 	

	$options['image_width'] = array(
		"5" 	=>	'5%',
		"10" 	=>	'10%',
		"15" 	=> 	'15%',
		"20" 	=>	'20%',
		"25" 	=>	'25%',
		"27" 	=>	'27.5%',
		"30" 	=>	'30%',
		"32" 	=>	'32.5%',
		"35" 	=>	'35%',
		"37" 	=>	'37.5%',
		"40" 	=>	'40%',
		"45" 	=>	'45%',
		"50" 	=>	'50%',
		"55" 	=>	'55%',
		"60" 	=>	'60%',
		"65" 	=>	'65%',
		"70" 	=>	'75%',
  	);
	
	$options['image_width_auto'] = array(
		"auto" 	=>	__('Auto','hexwp'),
		"5" 	=>	'5%',
		"10" 	=>	'10%',
		"15" 	=> 	'15%',
		"20" 	=>	'20%',
		"25" 	=>	'25%',
		"27" 	=>	'27.5%',
		"30" 	=>	'30%',
		"32" 	=>	'32.5%',
		"35" 	=>	'35%',
		"37" 	=>	'37.5%',
		"40" 	=>	'40%',
		"45" 	=>	'45%',
		"50" 	=>	'50%',
		"55" 	=>	'55%',
		"60" 	=>	'60%',
		"65" 	=>	'65%',
		"70" 	=>	'75%',
  	); 	
 
	$options['alignment'] = array(
			'left' 		=> __('Left', 'hexwp' ),
			'center'	=> __('Center', 'hexwp' ),
			'right' 	=> __('Right', 'hexwp' ),	
  	); 	
	
	$options['alignment_justify'] = array(
			'left' 		=> __('Left', 'hexwp' ),
			'center'	=> __('Center', 'hexwp' ),
			'right' 	=> __('Right', 'hexwp' ),	
			'justify' 	=> __('Justify', 'hexwp' ),	
  	); 		
	
	$options['alignment_justify_inverse'] = array(
			'left' 		=> __('Right', 'hexwp' ),
			'center'	=> __('Center', 'hexwp' ),
			'right' 	=> __('Left', 'hexwp' ),	
			'justify' 	=> __('Justify', 'hexwp' ),	
  	); 		
		
	
	
	$options['alignment_inverse'] = array(
			'left' 		=> __('Right', 'hexwp' ),
			'center'	=> __('Center', 'hexwp' ),
			'right' 	=> __('Left', 'hexwp' ),	
   	); 		
	
	
	$options['alignment_mini'] = array(
			'left' 		=> __('Left', 'hexwp' ),
 			'right' 	=> __('Right', 'hexwp' ),	
   	); 		
	$options['alignment_mini_inverse'] = array(
			'left' 		=> __('Left', 'hexwp' ),
 			'right' 	=> __('Right', 'hexwp' ),	
   	); 		
 	
 
	$options['box_layout'] = array(
		"none"				=> __('None','hexwp'),
		"boxed-all" 		=> __('Boxed All','hexwp'),
		"boxed-content" 		=> __('Boxed Content','hexwp'),
		"boxed-item" 		=> __('Boxed Item','hexwp'),
		"boxed-item-2" 		=> __('Boxed Item 2','hexwp'),
		"boxed-details"		=> __('Boxed Details','hexwp'),
		"boxed-details-2"	=> __('Boxed Details 2','hexwp'),
  	); 	
	$options['caption_layout'] = array(
		"middle"			=> __('Caption in Middle','hexwp'),			
		"bottom"			=> __('Caption in Bottom','hexwp'),
		"gradient-bottom"	=> __('Gradient Caption in Bottom','hexwp'),	
		"hover-middle"		=> __('Caption in Hover','hexwp'),			
		"hover-bottom"		=> __('Caption in Hover Bottom','hexwp'),					
		"hide"				=> __('Hide Caption','hexwp'),	
  	); 	


 $options['orderby']= array(
		''							=>	__('Recent Posts','hexwp'),
		'rand'						=>	__('Randam','hexwp'),
		'rand-day'					=>	__('Randam - 1 day ago','hexwp'),
		'rand-week'					=>	__('Randam - 1 week ago','hexwp'),
		'rand-month'				=>	__('Randam - 1 month ago','hexwp'),
		'rand-year'					=>	__('Randam - 1 year ago','hexwp'),
		'most-comment'				=>	__('Most Comments ','hexwp'),
		'most-comment-day'			=>	__('Most Comments 1 day ago','hexwp'),
		'most-comment-week'			=>	__('Most Comments 1 week ago','hexwp'),
		'most-comment-month'		=>	__('Most Comments 1 month ago','hexwp'),
		'most-comment-year'			=>	__('Most Comments 1 year ago','hexwp'),		
		'views'						=>	__('Most Views','hexwp'),
		'views-day'					=>	__('Most Views - 1 day ago','hexwp'),
		'views-week'				=>	__('Most Views - 1 week ago','hexwp'),
		'views-month'				=>	__('Most Views - 1 month ago','hexwp'),
		'views-year'				=>	__('Most Views - 1 year ago','hexwp'), 
	); 
	
	 $options['title_box_style']= array(
  			'style-1'			=> __('Style 1:none','hexwp'),
 			'style-2'			=> __('style 2:mini Border Bottom','hexwp'),
			'style-3'			=> __('Style 3:Border Bottom','hexwp'),
			'style-4'			=> __('Style 4:Border Top Button','hexwp'),
			'style-5'			=> __('style 5:Border Middle','hexwp'),
			'style-6'			=> __('style 6:Border Cover','hexwp'),
 			'style-7'			=> __('Style 7:Background item','hexwp'),
 			'style-8'			=> __('Style 8:Background','hexwp'),
	); 
	
	$options['hover_image'] =  array( 
 			"none"			=> __('None','hexwp'),
 			"reduce-opacity"	=> __('Reduce Opacity','hexwp'),
			"remove-opacity"	=> __('Remove Opacity','hexwp'),
			"add-color"			=> __('Add Color','hexwp'),
			"remove-color"		=> __('Remove Color','hexwp'),
			"grow"				=> __('Grow','hexwp'),
			"shrink"			=> __('Shrink','hexwp'), 
			"rotate"			=> __('Rotate','hexwp'),
 			"add-blur"			=> __('Add Blur','hexwp'),
 			"remove-blur"		=> __('Remove Blur','hexwp'),
			"add-brighten"		=> __('Add Brighten','hexwp'),
			"remove-brighten"	=> __('Remove Brighten','hexwp'),
			"add-darkness"		=> __('Add Darkness','hexwp'),
			"remove-darkness"	=> __('Remove Darkness','hexwp'),			
	);	
	
 	$options['caption_effect'] = array( 
 		'imghvr-fade' 						=> __('Fade','hexwp'), 
		'imghvr-slide-up'					=> __('Slide Up','hexwp'), 
		'imghvr-slide-down'					=> __('Slide Down','hexwp'), 
		'imghvr-slide-left'					=> __('Slide Left', 'hexwp' ), 
		'imghvr-slide-right'				=> __('Slide Right', 'hexwp' ), 
		'imghvr-flip-vert'					=> __('Flip Vert','hexwp'),  	
		'imghvr-flip-horiz'					=> __('Flip Horiz','hexwp'),  	
		'imghvr-flip-diag-1'				=> __('Flip Diag 1','hexwp'),  	
		'imghvr-flip-diag-2'				=> __('Flip Diag 2','hexwp'),  	 	 
		'imghvr-zoom-in'					=> __('Zoom in','hexwp'), 
		'imghvr-zoom-out'					=> __('Zoom out','hexwp'),  
		'imghvr-layla'						=> __('Layla','hexwp'), 
		'imghvr-oscar'						=> __('Oscar','hexwp'), 
		'imghvr-bubba'						=> __('Bubba','hexwp'), 
		'imghvr-chico'						=> __('Chico','hexwp'), 
		'imghvr-selena'						=> __('Selena','hexwp'), 
		'imghvr-ming'						=> __('Ming','hexwp'), 
	 
	);	
 	$options['product_orderby'] =array(
        'date' => __( 'Sort by latest', 'hexwp' ),
		'popularity' => __( 'Sort by popularity', 'hexwp' ),
        'rating' => __( 'Sort by average rating', 'hexwp' ),
        'price' =>__( 'Sort by price: low to high', 'hexwp' ),  
        'price_desc' => __( 'Sort by price: high to low', 'hexwp' ),   
		'rand'  => __( 'Random', 'hexwp' ),
		'onsale' => __( 'On Sales', 'hexwp' ),
		'onsale_variation' => __( 'On Sales Variation', 'hexwp' ),
		'featured' => __( 'Featured', 'hexwp' ),
  		'stock' => __( 'In Stock', 'hexwp' ),

	);	
$options['user_social'] = array(
	 
   			"facebook" 				=> __('Facebook','hexwp'),
   			"twitter" 			=> __('Twitter (X)','hexwp'),
  			"googleplus" 			=> __('Google Plus','hexwp'),
			"linkedin" 			=> __('Linkedin','hexwp'),
 			"flickr" 			=> __('Flickr','hexwp'),
 			"skype" 			=> __('Skype','hexwp'),
 			"tumblr" 			=> __('Tumblr','hexwp'),
			"vimeo" 			=> __('Vimeo','hexwp'),
			"youtube" 			=> __('Youtube','hexwp'),
   			"instagram" 			=> __('Instagram','hexwp'),
			"telegram" 			=> __('Telegram','hexwp'),
 			"pinterest" 			=> __('Pinterest','hexwp'), 
 			"whatsapp" 			=> __('Whatsapp','hexwp'), 
			'tiktok'				=>__('Tiktok','hexwp'),  
			
	); 	
	
	$options['unit']= array(
		'px'				=>	 'px',
		'%'					=>	 '%',
		'em'				=>	 'em', 
 		
	); 
	$options['elementor_padding'] = array(
 			"0px" 			=> "0px",
			"1px" 			=> "1px", 
			"10px" 			=> "10px",
			"10px 15px" 	=> "10px - 15px",
			"10px 20px" 	=> "10px - 20px",
			"15px"		 	=> "15px",
			"15px 10px"		=> "15px - 10px",
			"15px 20px" 	=> "15px - 20px",
			"15px 30px" 	=> "15px - 30px",
 			"20px" 			=> "20px",
 			"20px 10px" 	=> "20px - 10px",
 			"20px 15px" 	=> "20px - 15px",
 			"20px 30px" 	=> "20px - 30px",
 			"20px 40px" 	=> "20px - 40px",
 			"30px" 			=> "30px",
 			"30px 10px" 	=> "30px - 10px",
 			"30px 15px" 	=> "30px - 15px",
 			"30px 20px" 	=> "30px - 20px",
 			"30px 40px" 	=> "30px - 40px",
			"40px" 			=> "40px",
			"40px 20px" 	=> "40px - 20px",
 			"50px" 			=> "50px",
   			"50px 20px" 	=> "50px - 20px",
 			"50px 25px" 	=> "50px - 25px",
  	); 
	$options['font_weight'] =  array( 
			""				=> __('Default','hexwp'),
			"300"			=> __('Light','hexwp'),
			"normal"		=> __('Normal','hexwp'),
			"500"			=> __('Medium','hexwp'),
			"bold"			=> __('Bold','hexwp'),
			"900"			=> __('Extra-Bold','hexwp'),
 
	);	
	$options['font_style'] =  array( 
			''				=>__('Default','hexwp'), 
			'normal'		=> __('Normal','hexwp'), 
			'italic'		=> __('Italic','hexwp'), 
			'oblique'		=> __('Oblique','hexwp'), 
		);
	$options['social'] =  array( 
			'rss'				=>__('RSS','hexwp'),  
			'facebook'			=>__('Facebook','hexwp'),  
			'twitter'				=>__('Twitter (X)','hexwp'),  
			'googleplus'				=>__('Google+','hexwp'),  
			'telegram'				=>__('Telegram','hexwp'),  
			'dribbble'				=>__('Dribbble','hexwp'),  
			'linkedIn'				=>__('LinkedIn','hexwp'),  
			'dropbox'				=>__('Dropbox','hexwp'),  
			'flickr'				=>__('Flickr','hexwp'),  
			'deviantArt'				=>__('DeviantArt','hexwp'),  
			'youTube'				=>__('YouTube','hexwp'),  
			'yahoo'				=>__('Yahoo','hexwp'),  
			'vimeo'				=>__('Vimeo','hexwp'),  
			'skype'				=>__('Skype','hexwp'),  
			'digg'				=>__('Digg','hexwp'),  
			'stumbleUpon'				=>__('StumbleUpon','hexwp'),  
			'tumblr'				=>__('Tumblr','hexwp'),  
			'pinterest'				=>__('Pinterest','hexwp'),  
			'instagram'				=>__('Instagram','hexwp'),  
			'paypal'				=>__('PayPal','hexwp'),  
			'behance'				=>__('Behance','hexwp'),  
			'whatsapp'				=>__('Whatsapp','hexwp'),  
			'discord'				=>__('Discord','hexwp'),  
			'tiktok'				=>__('Tiktok','hexwp'),  
			
		);	
		
		
		
		
		if(is_rtl()){	
		$options['social_rtl'] =  array( 
			 
			);
 		$options['social'] = $options['social'] + $options['social_rtl'];
					
		}
		$options['share'] =  array( 
				'facebook'				=>__('Facebook','hexwp'),  
				'twitter'				=>__('Twitter (X)','hexwp'),  
				'googleplus'			=>__('Google+','hexwp'),  
				'telegram'				=>__('Telegram','hexwp'),  
				'tumblr'				=>__('Tumblr','hexwp'),  
				'linkedin'				=>__('Linkedin','hexwp'),  
				'reddit'				=>__('reddit','hexwp'),  
				'mail'					=>__('Mail','hexwp'),  
				'whatsapp'				=>__('Whatsapp','hexwp'),  
	   
		);	
		
		$options['title_box_type'] =  array( 
 				'main-right'		=> __('Only Main Title Right','hexwp'),
				'main-center'	=> __('Only Main Title Center','hexwp'),
 				'main-tabs'		=> __('Main Title and Tabs Right','hexwp'),
				'tabs-center'	=> __('All Tabs Center','hexwp'),			
				'hide'			=> __('Hide','hexwp'),	 
		);		
	 
	
	if(!empty($default)){
		$none_default=array(''=>__('Default','hexwp'));
		if(!empty($options[$value])){
		$array =   $none_default +  $options[$value];
		}
		return !empty($array)?$array:'';
		
  	}else{
 		return !empty($options[$value])?$options[$value]:array() ;
	}
}	
 
