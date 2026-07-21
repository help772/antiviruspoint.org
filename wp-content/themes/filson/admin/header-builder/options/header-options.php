<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Header Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
add_filter('vh_global_options', 'hexwp_header_options');
function hexwp_header_options($global) {
 	
	$global['header'] = array(
							'name'			=> 	esc_html__('Header Options','hexwp'),
 							'img'			=>  hexwp_DIR.'/admin/assets/images/header/icon/header.png'
  	); 
 
	$option[] = array(			"name" 			=> __( 'Header Width', 'hexwp' ),
 								"id" 			=> "header_width",
  								"type" 			=> "radio",
					 			"options" 		=>	array(
															""		=> __( 'Default', 'hexwp' ),
															"1000px"		=> '1000px', 
															"1100px"		=> '1100px', 
															"1200px"		=> '1200px', 
															"1300px"		=> '1300px',  
															"1400px"		=> '1400px',  
															"1500px"		=> '1500px', 
															"1600px"		=> '1600px', 
															"1800px" 		=> '1800px', 
															"1920px"		=> '1920px',
															"100%"			=> __( 'Full Width', 'hexwp' ),
													),			
					);			
											
	$option[] = array(			"name" 			=> __('Header Shadow' , 'hexwp'),
								"id" 			=> "header_shadow",
								"type"			=> "multi_options",
								"group"			=> __( 'Header Style', 'hexwp' ),
								"options"		=> hexwp_multi_array_options('shadow_mini'),
					); 
	 	
		
	$global['header']['options']=$option;
     return $global;
} 
?>