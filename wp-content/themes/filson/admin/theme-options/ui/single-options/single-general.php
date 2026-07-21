<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Single Post Template

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "single_general_start",	
						"type"		=> "content"
				);
				
$of_options[] = array( 	"name" 			=> esc_html__('Single Post General' , 'hexwp'),
						"type" 			=> "title"
				);

$url_single =  hexwp_DIR  . '/admin/assets/images/single/single-template-';
$of_options[] = array( "name" 			=> esc_html__('Single Template' , 'hexwp'),
   						"id" 			=> 	"single_template",
						"type" 			=> "images",
						"options" 		=> array(
													'1' 		=> $url_single . '1.jpg',
													'2'			=> $url_single . '2.jpg',
													'3'			=> $url_single . '3.jpg',
													'4'			=> $url_single . '4.jpg',
													'5'			=> $url_single . '5.jpg',
													'6'			=> $url_single . '6.jpg',
													'7'			=> $url_single . '7.jpg',
						  
											)
				);
 
 				
 $of_options[] = array( "name" 			=> esc_html__('Share icons' , 'hexwp'),
 						"id" 			=> "single_share_icons",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
 				);			
					 
$of_options[] = array( "name" 			=> esc_html__('Share icons Style', 'hexwp' ),
						"id" 			=> "single_share_icons_style",
    						"type" 			=> "radio",
						"fold" 			=> "single_share_icons",
						"options" 		=> array(
 													"style-1" => esc_html__('Style 1: only icon','hexwp'),
													"style-2" => esc_html__('Style 2: Boxed Icon','hexwp'),
													"style-3" => esc_html__('Style 3: Boxed Original Color','hexwp'),
											)		
				);		
 $of_options[] = array( "name" 			=> esc_html__('Post Tags' , 'hexwp'),
 						"id" 			=> "single_tags",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
 				);
						
 
   
$of_options[] = array( 	"name" 			=> esc_html__('Author Box' , 'hexwp'),
 						"id" 			=> "single_author_box",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);	
					
$of_options[] = array( "name" 			=> esc_html__('Comments Layout' , 'hexwp'),
   						"id" 			=> "comments_layout_type",
						"type" 			=> "radio",
 						"options" 		=> array(
													'hw-thread' 	=> esc_html__('Thread' , 'hexwp'),
													'hw-list' 		=> esc_html__('List' , 'hexwp'),
											)
				);
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);