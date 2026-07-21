<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Product Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					 
$of_options[] = array( 	"name" 			=> esc_html__( 'Single Product' , 'hexwp'),
						"type" 			=> "title"
				);
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "product_single_start",	
						"type"			=> "content"
				);
				
 
 					
$of_options[] = array( 	"name" 			=> esc_html__( 'Single Product Image Width' , 'hexwp'),
   						"id" 			=> "single_product_image_width",
  						"type" 			=> "select",
						"options" 		=>  hexwp_array_options('image_width',true),	
		 
				);		 
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Product Gallery Item' , 'hexwp'),
   						"id" 			=> "single_product_gallery_item",
  						"type" 			=> "radio",
						"options" 		=>  array(
							"3" => '3',	
							"4" => '4',	
							"5" => '5',		
							"6" => '6',		
 						)	
		 
				);		 				
						
 	
				
 $of_options[] = array( "name" 			=> esc_html__('Share icons' , 'hexwp'),
 						"id" 			=> "single_product_share_icons",
    					"type" 			=> "radio",
						"hide" 			=> "disable",
   						"options" 			=> array(
 							"enable" 			=>  __( 'Enable' , 'hexwp' ),
							"disable" 			=> __( 'Disable', 'hexwp' ),					 
						),		
 				);			
					 
$of_options[] = array( "name" 			=> esc_html__('Share icons Style', 'hexwp' ),
						"id" 			=> "single_product_share_icons_style",						
    					"type" 			=> "radio",
						"fold" 			=> "single_product_share_icons",
						"options" 		=> array(
  													"style-1" => esc_html__('Style 1: only icon','hexwp'),
													"style-2" => esc_html__('Style 2: Boxed Icon','hexwp'),
													"style-3" => esc_html__('Style 3: Boxed Original Color','hexwp'),
											)		
				);		
 $of_options[] = array( "name" 			=> esc_html__('Countdown in Single Product' , 'hexwp'),
 						"id" 			=> "single_product_countdown",
    					"type" 			=> "radio",
    					"options" 			=> array(
 							"enable" 			=>  __( 'Enable' , 'hexwp' ),
							"disable" 			=> __( 'Disable', 'hexwp' ),						 
						),		
 				);			
					  
 $of_options[] = array( "name" 			=> esc_html__('Related Product Columns' , 'hexwp'),
 						"id" 			=> "related_product_column",
 						"type" 			=> "radio",
						"options" 		=> array(
													"3"		=> __('3 Column','hexwp'),
													"4"		=> __('4 Column','hexwp'),
													"5"		=> __('5 Column','hexwp'),
													"6"		=> __('6 Column','hexwp'),
													"7"		=> __('7 Column','hexwp'),
													"8"		=> __('8 Column','hexwp'),
 											)						
						
 				);			
								 
					 
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);	
				
