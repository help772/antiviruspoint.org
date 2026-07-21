<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Blog Layout

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "blog_layout_start",	
						"type"		=> "content"
				);
				
									
$of_options[] = array( 	"name" 			=> esc_html__('Blog Layout' , 'hexwp'),
						"type" 			=> "title"
				);		 
 						 
 
 $of_options[] = array(	"name" 			=> esc_html__( 'Blog Layout' , 'hexwp'),
   						"id" 			=> "blog_layout",
						"type" 			=> "radio",
						"options" 		=> array(	
													'grid' 	=>  esc_html__( 'Grid' , 'hexwp'),
													'list' 	=>  esc_html__( 'List' , 'hexwp'),
											)
				);	
				 
$url_blog = hexwp_DIR . '/admin/assets/images/';
$of_options[] = array(	"name" 			=> esc_html__( 'Grid Layout' , 'hexwp'),
   						"id" 			=> 	"blog_grid_layout",
						"fold_array"	=>  array(
												'grid'			=>'blog_layout', 
											),			
						"type" 			=> "images",
						"options" 		=> array(	
													'grid_1' 	=> $url_blog.'grid/grid_1.jpg',
													'grid_2' 	=> $url_blog.'grid/grid_2.jpg',
													'grid_3' 	=> $url_blog.'grid/grid_3.jpg',
													'grid_4' 	=> $url_blog.'grid/grid_4.jpg',
													'grid_5' 	=> $url_blog.'grid/grid_5.jpg',
													'grid_6' 	=> $url_blog.'grid/grid_6.jpg',
											)
				);		

 
$of_options[] = array(	"name" 			=> esc_html__( 'List Layout' , 'hexwp'),
   						"id" 			=> 	"blog_list_layout",
						"fold_array"	=>  array(
													'list'			=>'blog_layout', 
											),	
						"type" 		=> "images",
						"options" 	=> array(	
											'list_1' 	=> $url_blog.'list/list_1.jpg',
											'list_2' 	=> $url_blog.'list/list_2.jpg',
											'list_3' 	=> $url_blog.'list/list_3.jpg',
											'list_4' 	=> $url_blog.'list/list_4.jpg',
 										)
				);		
$of_options[]=  array( 	"name"			=> esc_html__('Column in Tablet and Mobile','hexwp'),
						"id"			=> "blog_responsive_column",
						"type"			=> "select",
 						"options" 		=> hexwp_array_options('responsive_column',true), 

);
 
$of_options[] = array( 	"name" 		=> esc_html__( 'Space Between Item' , 'hexwp'),
   						"id" 		=> "blog_between",
 						"type" 		=> "radio",
						 "options"		=>	 hexwp_array_options('between'),
				);	
 
							
$of_options[] = array( 	"name" 		=> esc_html__( 'Image Ratio' , 'hexwp'),
   						"id" 		=> "blog_ratio",
 						"type" 		=> "radio",
						"options" 	=>  hexwp_array_options('ratio'),
				);		
				
				
$of_options[] = array( 	"name" 		=> esc_html__( 'Image Size' , 'hexwp'),
   						"id" 		=> "blog_image_size",
 						"type" 		=> "select",
						"options" 	=>  hexwp_all_image_sizes()
				);	
					
					
$of_options[] = array( 	"name" 			=> esc_html__( 'Image Width' , 'hexwp'),
   						"id" 			=> "blog_image_width",
  						"type" 		=> "select",
						"options" 	=>  hexwp_array_options('image_width'),
				);				 
					 

$of_options[] = array( 	'name'			=> esc_html__('Details Alignment','hexwp'),
						'id'			=> 'blog_alignment',
  						'type'			=> 'radio',
						"options" 		=>  hexwp_array_options('alignment_justify'),
 				);
				
				 
$of_options[]= array(	"name"			=> esc_html__('Blog Box Layout','hexwp'),
						"id"			=> "blog_box_layout",
						"type"			=> "select",
						"options" 	=>  hexwp_array_options('box_layout'),
					); 				
					
$of_options[]= array(	"name"			=> esc_html__('Blog Caption Layout','hexwp'),
						"id"			=> "blog_caption_layout", 
 						"type"			=> "select",
						"hide" 			=> 'none',
						"options" 	=>  hexwp_array_options('caption_layout'),
				); 	
					 					 
  
$of_options[]= array(	"name"			=> esc_html__('Meta Layout','hexwp'),
						"id"			=> "blog_meta_layout", 
						"type"			=> "multi_options",
 						"options" 	=>  hexwp_multi_array_options('meta_layout'),
		
  				); 	
				  
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);	
 