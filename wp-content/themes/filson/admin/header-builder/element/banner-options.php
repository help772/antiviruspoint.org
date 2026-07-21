<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																   Banner Options
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 add_filter('vh_element_options', 'hexwp_banner_options');

function hexwp_banner_options($element) {
	
 	$element['banner'] =array(
						'name'			=> 	__('Banner','hexwp'),
						'id'			=> 'banner',
						'img'			=>   hexwp_DIR.'/admin/assets/images/header/banner.jpg'
				); 
		 
						
	$option[] = array( 			"name" 		=>  __( 'Image Banner' , 'hexwp' ),
								"id" 		=> "banner_header_img",
								"type" 		=> "image",
 					);
	
	$option[] = array(			"name" 		=> __( 'Url Banner' , 'hexwp' ),
								"id" 		=> "banner_header_url",
								"type" 		=> "text",
					 
					);
																									
	$option[] = array(			"name" 		=> __( 'Open in New Window' , 'hexwp' ),
								"id" 		=> "banner_header_window",
  								"type" 		=> "radio",
								"options"	=>  array(
														''				=>__('Disable','hexwp'),
														'enable'		=>__('Enable','hexwp'),
												),
					);	
						
	$option[] = array( 		"name" 		=>  __('Nofollow','hexwp'),
							"id" 		=> "banner_header_nofollow",
  							"type" 		=> "radio",
							"options"	=>  array(
														''				=>__('Disable','hexwp'),
														'enable'		=>__('Enable','hexwp'),
												),
					);	

 
  				
	$element['banner']['options']=$option;
     return $element;
	
}


 
?>