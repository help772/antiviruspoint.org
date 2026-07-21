<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	All Header Layout

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$of_options[] = array( 	"name"			=> __( 'Header', 'hexwp' ),
						"type" 			=> "heading",
 						"id" 			=> "header-layout",
						"icon"			=> ADMIN_IMAGES . "header-layout.png",
 				); 
$of_options[] = array( 	"name" 			=> esc_html__( 'Header', 'hexwp' ),
						"type" 			=> "title"
				);	 

$of_options[] = array(	"position"		=> "start",
						"id" 			=> "header_layout_start",	
						"type"		=> "content"
				);
 	 $of_options[] = array(  "name" 			=> esc_html__( 'Select Header Builder', 'hexwp' ),
						"id" 			=> "header_builder",
 						"type" 			=> "select",
						"options" 		=> 	hexwp_category_array_options('header',true),
				);	
				
	$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);		
			