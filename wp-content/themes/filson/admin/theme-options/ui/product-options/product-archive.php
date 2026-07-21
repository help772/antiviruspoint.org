<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Product Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					 
$of_options[] = array( 	"name" 			=> esc_html__( 'Product Archive' , 'hexwp'),
						"type" 			=> "title"
				);
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "product_archive_start",	
						"type"			=> "content"
				);
				

 $of_options[] = array(	"name" 			=> esc_html__( 'Number of products Displayed Per page' , 'hexwp'),
 						"id" 			=>	"product_number",
						"type" 			=> "text-mini"
 				);		
  
 						
 
$of_options[] = array( "name" 			=> esc_html__( 'Limit Title length' , 'hexwp'),
						"desc" 			=> esc_html__( 'Please enter Maximum number of characters' , 'hexwp'),
						"id" 			=> "product_title_limit",
						"type" 			=> "text-mini"
 				);
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Show Excerpt Posts' , 'hexwp'),
 						"id" 			=> "product_excerpt",
    					"type" 			=> "radio",
						"hide" 			=> "disable",
   						"options" 			=> array(
							"enable" 			=>  __( 'Enable' , 'hexwp' ),
							"disable" 			=> __( 'Disable', 'hexwp' ),						 
						),		
				);
 			 			
									
$of_options[] = array( "name" 			=> esc_html__( 'Limit Excerpt length' , 'hexwp'),
						"desc" 			=> esc_html__( 'Please Enter Maximum Number of characters' , 'hexwp'),
						"id" 			=> "product_excerpt_limit",
 						"type" 			=> "text-mini",
  				); 
				
 
$of_options[] = array( 	"name" 			=> esc_html__( 'Categories Meta' , 'hexwp'),
 						"id" 			=> "product_meta_category",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);  
				
 
 
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Rating Meta' , 'hexwp'),
 						"id" 			=> "product_meta_rating",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);  
				
				
$of_options[]= array(	"name"			=> esc_html__('Show Add to Cart','hexwp'),
						"id"			=> "product_addcart",
 					
 						"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				); 	
				
$of_options[]= array(	"name"			=> esc_html__('Onsale Tags','hexwp'),
						"id"			=> "product_onsale",
						"type" 			=> "radio",
						"hide" 			=> "hide",
						"options" 		=> 	array(	 
 													"sale" 			=> __( 'Sale Text', 'hexwp' ),
													"percentage"	=> __( 'Percentage', 'hexwp' ),
													"hide" 			=> __( 'Hide', 'hexwp' ),
  											) 
				); 	

				
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);	