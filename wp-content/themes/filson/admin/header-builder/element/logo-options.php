<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Logo Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('vh_element_options', 'hexwp_logo_options');
function hexwp_logo_options($element) {
	
 	$element['logo'] = array(	'name'			=> 	__('Logo','hexwp'),
								'id'			=> 'logo',
								'img'			=>  hexwp_DIR.'/admin/assets/images/header/logo.jpg'
				); 
		
	$option[] =	array(		"name" 			=> __('Logo Image', 'hexwp' ),
							"desc" 			=> __('Upload Select an image file for your logo', 'hexwp' ),
							"id" 			=> "logo",
							"type" 			=> "image",
	
					);	
					
	$option[] = array(		"name" 			=> __('Logo Image in Overlap', 'hexwp' ),
							"desc" 			=> __('Upload Select an image file for your logo', 'hexwp' ),
							"id" 			=> "logo_overlap",
							"type" 			=> "image",
					);				
					
		
	$option[] = array( 		"name"			=> __('Logo Width', 'hexwp' ),
								"desc" 			=> __('Determine Value In Pixels.','hexwp'),
							"id" 			=> "logo_width",
							"default" 		=> "250",
							"min" 			=> "1",
							"max" 			=> "500",
							"step" 			=> "1",
							"unit" 			=> "px",
							"type" 			=> "number"
					);
					
  				
	$element['logo']['options']=$option;
 
    return $element;
	
}


 
?>