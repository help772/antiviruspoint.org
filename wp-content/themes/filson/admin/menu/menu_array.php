<?php
function hexwp_menu_array_options($value) {
	$options['menu_tags'] =array(
		''					=> __('None','hexwp'), 
		'new'				=> hexwp_t('new'), 
		'hot'				=> hexwp_t('hot'), 
		'featured'			=> hexwp_t('featured'),
		'sale'				=> hexwp_t('sale'), 
	
	);
				 
				
	$options['menu_size'] =array(
			''					=>esc_html__('Default','hexwp'), 
		'fa-1x'				=> '1x', 
		'fa-1-25x'			=> '1.25x',  
		'fa-1-5x'			=>  '1.5x', 
		'fa-1-75x'			=>  '1.5x', 
		'fa-2x'				=>  '2x', 
		'fa-2-5x'			=>  '2.5x',  
		'fa-3x'				=>  '3x',  
		'fa-3-5x'			=>  '3.5x' , 
		'fa-4x'				=>  '4x',  
		'fa-4-5x'			=>  '4.5x' , 
		'fa-5x'				=>  '5x' , 
					 
	);
	$options['menu_width'] =array(
			''					=>esc_html__('Default','hexwp'), 
		'200'				=>  '200px', 
		'300'				=>  '300px', 
		'400'				=>  '400px', 
		'500'				=>  '500px', 
		'600'				=> '600px', 
		'700'				=>  '700px',  
		'800'				=>  '800px',  
		'900'				=>  '900px', 
		'1000'				=>  '1000px', 
 		'full-width'		=>  __('Full Width','hexwp'), 
 	);
	$options['background_position'] =array(
		''			=> __('Default','hexwp'),
		'center'			=> __('Center','hexwp'),
		'left'				=> is_rtl()? __('Right','hexwp'):__('Left','hexwp'), 
		'left-top'			=> is_rtl()? __('Right Top','hexwp'):__('Left Top','hexwp'), 
		'left-bottom'		=> is_rtl()? __('Right Bottom','hexwp'):__('Left Bottom','hexwp'), 
		'left-center'		=> is_rtl()? __('Right Center','hexwp'):__('Left Center','hexwp'), 
		'right' 			=> is_rtl()? __('Left','hexwp'):__('Right','hexwp'), 
		'right-top'			=> is_rtl()? __('Left Top','hexwp'):__('Right Top','hexwp'), 
		'right-bottom'		=> is_rtl()?__('Left Bottom','hexwp') :__('Right Bottom','hexwp'), 
		'right-center'		=> is_rtl()? __('Left Center','hexwp'):__('Right Center','hexwp'),
		'top'				=> __('Top','hexwp'),
		'top-center'		=> __('Top Center','hexwp'),
		'bottom'			=> __('Bottom','hexwp'),
		'bottom-center'		=> __('Bottom Center','hexwp'),
	);	
	
	$options['background_size'] =array(
		''					=>esc_html__('Default','hexwp'), 
		'auto'				=> __('Auto','hexwp'), 
		'cover'				=> __('Cover','hexwp'), 
		'contain'			=> __('Contain','hexwp'),
		'5%'					=> '5%', 
		'10%'				=> '10%', 
		'15%'				=> '15%', 
		'20%'				=> '20%', 
		'25%'				=> '25%', 
		'30%'				=> '30%', 
		'33%'				=> '33.33%', 
		'35%'				=> '35%', 
		'40%'				=> '40%', 
		'45%'				=> '45%',  
		'50%'				=> '50%',  
		'55%'				=> '55%',  
		'60%'				=> '60%',  
		'65%'				=> '65%',  
		'66%'				=> '66.66%',  
		'70%'				=> '70%',  
		'75%'				=> '75%',  
		'80%'				=> '80%',  
		'85%'				=> '85%',  
		'90%'				=> '90%',  
		'95%'				=> '95%',  
		'100%'				=> '100%',  
	 );	
		
	$options['background_opacity'] =array(
		''					=>esc_html__('Default','hexwp'), 
		'1.0'					=> '1.0', 
		'0.9'					=> '0.9', 
		'0.8'					=> '0.8', 
		'0.7'					=> '0.7', 
		'0.6'					=> '0.6', 
		'0.5'					=> '0.5', 
		'0.4'					=> '0.4', 
		'0.3'					=> '0.3', 
		'0.2'					=> '0.2', 
		'0.1'					=> '0.1', 
	);	
		
 
	 
  
return $options[$value];
}