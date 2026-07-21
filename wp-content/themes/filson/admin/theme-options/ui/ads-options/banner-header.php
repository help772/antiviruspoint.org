<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Banner Header

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "banner_header_widgets_start",	
						"type"		=> "content"
				);
$of_options[] = array( 	"name" 		=> __( 'Banner in Header' , 'hexwp' ),
						"type" 		=> "title"
				);
 
$of_options[] = array( 	"name" 		=> __( 'Banner in Header' , 'hexwp' ),
 						"id" 		=> "banner_header",
     					"type" 			=> "radio",
						"hide" 			=> "disable",
   						"options" 			=> array(
							"enable" 			=>  __( 'Enable' , 'hexwp' ),
							"disable" 			=> __( 'Disable', 'hexwp' ),						 
						),		
 				);	
 
					
$of_options[] = array( 	"name" 		=>  __( 'Image Banner in Header' , 'hexwp' ),
						"id" 		=> "banner_header_img",
 						"type" 		=> "upload",
						"fold_array"	=>  array(
													'enable'		=>'banner_header',
 											),
				);

$of_options[] = array( 	"name" 		=> __( 'Url Banner in Header' , 'hexwp' ),
						"id" 		=> "banner_header_url",
 						"type" 		=> "text",
						"fold_array"	=>  array(
													'enable'		=>'banner_header',
 											),
				);
																								
$of_options[] = array( 	"name" 			=> __( 'Open in New Window' , 'hexwp' ),
						"id" 			=> "banner_header_window",
  						"fold_array"	=>  array(
													'enable'		=>'banner_header',
 											),
   						"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);	
				
				
						
$of_options[] = array( 	"name" 			=>  __('Nofollow','hexwp'),
						"id" 			=> "banner_header_nofollow",
						"fold_array"	=>  array(
													'enable'		=>'banner_header',
 											),
   						"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);	

 
 
 $of_options[] = array( "name" 			=>  __('Alignment','hexwp'),
						"id" 			=> "banner_header_align",
 						"type" 			=> "radio",
						"options" 		=> hexwp_array_options('alignment_rtl'),
						"fold_array"	=>  array(
													'enable'		=>'banner_header',
 											),
				);
					
$of_options[] = array( 	"name" 			=>  __('Widget in Header','hexwp'),
						"id" 			=> "widget_header",
						"desc" 			=> esc_html__( 'To use the widget, you must leave the image empty Area' , 'hexwp' ),
 						"fold_array"	=>  array(
													'enable'		=>'banner_header',
 											),
   						"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
 				);	
	  
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);			 