<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	General Blog Options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			 
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "blog_archive_start",	
						"type"		=> "content"
				);
				
					
$of_options[] = array( 	"name" 			=> esc_html__( 'General Archive Options' , 'hexwp'),
						"type" 			=> "title"
				);
 
 
$of_options[] = array( "name" 			=> esc_html__( 'Blog Title' , 'hexwp'),
						"desc" 			=> esc_html__( 'This text will display in the page Title Bar of the assigned Blog Page' , 'hexwp'),
 						"id" 			=> "blog_title",
  						"type" 			=> "text",
  				);	


$of_options[] = array( "name" 			=> esc_html__( 'Limit Title length' , 'hexwp'),
						"desc" 			=> esc_html__( 'Please enter Maximum number of characters' , 'hexwp'),
						"id" 			=> "blog_title_limit",
						"type" 			=> "text-mini"
 				);
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Show Excerpt Posts' , 'hexwp'),
 						"id" 			=> "blog_excerpt",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
 			 			
									
$of_options[] = array( "name" 			=> esc_html__( 'Limit Excerpt length' , 'hexwp'),
						"desc" 			=> esc_html__( 'Please Enter Maximum Number of characters' , 'hexwp'),
						"id" 			=> "blog_excerpt_limit",
						"fold" 			=> "blog_excerpt",
						"type" 			=> "text-mini"
 				); 
				
 			
$of_options[] = array( 	"name" 			=> esc_html__( 'Author Meta' , 'hexwp'),
 						"id" 			=> "blog_meta_author",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
				
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Category Meta' , 'hexwp'),
 						"id" 			=> "blog_meta_category",
     					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);		
				
							
$of_options[] = array( 	"name" 			=> esc_html__( 'Date Meta' , 'hexwp'),
 						"id" 			=> "blog_meta_date",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
 
					
$of_options[] = array( 	"name" 			=> esc_html__( 'View Meta' , 'hexwp'),
 						"id" 			=> "blog_meta_view",
  		    		 	"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
				
					
$of_options[] = array( 	"name" 			=> esc_html__( 'Comments Meta' , 'hexwp'),
 						"id" 			=> "blog_meta_comments",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);				
					
 
$of_options[] = array( 	"name" 			=> esc_html__( 'Show Read More' , 'hexwp'),
   						"id" 			=> "blog_readmore",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Icon in Hover post' , 'hexwp'),
   						"id" 			=> "blog_hover_post_icon",
     					"type" 			=> "radio",
						"hide" 			=>	'hide',
						"options" 			=> array(
							'show' 			=> __( 'Enable', 'hexwp' ),						 
							'hide'			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);	
				
$of_options[] = array(	"name"			=> esc_html__( 'Time Format For Post', 'hexwp' ),
						"id" 			=> "time_format",
 						"type" 			=> "radio",
 						"options" 	=> array(	"traditional" 	=> esc_html__( 'Default WordPress', 'hexwp' ),
												"morden" 		=> esc_html__( 'Time ago', 'hexwp' ),
												 ),
				);						
$of_options[] = array( 	"name" 			=> esc_html__( 'Pagenavi Ajax' , 'hexwp'),
						"desc" 			=> esc_html__( 'Donot Load All Page in Pagenavi' , 'hexwp'),
  						"id" 			=> "pagenavi_ajax",
    					"type" 			=> "radio",
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);	
				
				
				
							
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);
 