<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Social Networks

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$of_options[] = array( 	"name" 			=> esc_html__('Social Networks' , 'hexwp' ),
						"type" 			=> "heading",
						"id" 			=> "social-networks",
						"icon"			=> ADMIN_IMAGES."social-networks.png",

						 
				);  
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "social_start",	
						"type"		=> "content"
				);
				
									
$of_options[] = array( 	"name" 			=> esc_html__('Social Networking' , 'hexwp' ),
						"type" 			=> "title"
				);	
				
 $hexwp_social=hexwp_array_options('social');
  if( is_array($hexwp_social)){
 foreach($hexwp_social as $social_key => $social_name){
	$of_options[] = array( "name"			=>  esc_html($social_name),
							"id"			=> 'social_'.$social_key,
 							"type"			=> "text",
							"desc" 			=> esc_html__('Insert your custom link to show. Leave blank to hide icon' , 'hexwp' ),
				);  	
	 
 }
 	
 
}
	 		$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);							 											 
