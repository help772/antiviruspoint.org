<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Product Layout

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
$of_options[] = array( 	"name" 		=> esc_html__('Product Layout' , 'hexwp'),
						"type" 		=> "title"
				);		 
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "product_layout_start",	
						"type"			=> "content"
				);

 $of_options[] = array(	"name" 		=> esc_html__( 'Product Layout' , 'hexwp'),
   						"id" 		=> 	"product_layout",
						"type" 		=> "radio",
						"options" 	=> array(	
												'grid' 	=>  esc_html__( 'Grid' , 'hexwp'),
												'list' 	=>  esc_html__( 'List' , 'hexwp'),
 										)
				);	
				 
				 
$of_options[] = array(	"name" 			=> esc_html__( 'Product Layout' , 'hexwp'),
   						"id" 			=> 	"product_grid_layout",		
						"type" 			=> "images",
						"options" 		=> array(	
													'grid_1'		=> $url_blog.'grid/grid_1.jpg',
													'grid_2'		=> $url_blog.'grid/grid_2.jpg',
													'grid_3'		=> $url_blog.'grid/grid_3.jpg',
													'grid_4'		=> $url_blog.'grid/grid_4.jpg',
													'grid_5'		=> $url_blog.'grid/grid_5.jpg',
													'grid_6'		=> $url_blog.'grid/grid_6.jpg',
											)
					);		

 
$of_options[] = array(	"name" 			=> esc_html__( 'Product List Layout' , 'hexwp'),
   						"id" 			=> 	"product_list_layout",
						"type" 			=> "images",
						"options" 		=> array(	
													'list_1' 	=> $url_blog.'list/list_1.jpg',
													'list_2' 	=> $url_blog.'list/list_2.jpg',
													'list_3' 	=> $url_blog.'list/list_3.jpg',
													'list_4' 	=> $url_blog.'list/list_4.jpg',
											)
				);		
$of_options[]=  array( 	"name"			=> esc_html__('Column in Tablet and Mobile','hexwp'),
						"id"			=> "product_responsive_column",
						"type"			=> "select",
 						"options" 		=> hexwp_array_options('responsive_column',true), 

);

$of_options[] = array( 	"name" 			=> esc_html__( 'Space Between Item' , 'hexwp'),
   						"id" 			=> "product_between",
 						"type" 			=> "radio",
						"options" 		=>  hexwp_array_options('between'),	

				);	
				 
			
$of_options[] = array( 	"name" 			=> esc_html__( 'Image Ratio' , 'hexwp'),
   						"id" 			=> "product_ratio",
 						"type" 			=> "radio",
						"options" 		=>  hexwp_array_options('ratio'),	
				);		
				
				
$of_options[] = array( 	"name" 			=> esc_html__( 'Image Size' , 'hexwp'),
   						"id" 			=> "product_image_size",
 						"type" 			=> "select",
						"options" 		=>  hexwp_all_image_sizes()
				);		
				
					
$of_options[] = array( 	"name" 			=> esc_html__( 'Image Width' , 'hexwp'),
   						"id" 			=> "product_image_width",
		 	
 						"type" 			=> "select",
						"options" 		=>  hexwp_array_options('image_width'),	
	 
				);		 
							 
							 
$of_options[]= array(	"name"			=> esc_html__('Second Image','hexwp'),
						"id"			=> "product_second_image",
 						'type'			=> 'radio',
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
   				); 	 
				
								
$of_options[] = array( 	'name'			=> esc_html__('Details Alignment','hexwp'),
						'id'			=> 'product_alignment',
						"fold_array"	=>  array(
												'grid'			=>'product_layout', 
 											),
 						'type'			=> 'radio',
						"options" 	=>  hexwp_array_options('alignment_justify'),
 
  						
				); 		 
				 					 
$of_options[]= array(	"name"			=> esc_html__('Product Box Layout','hexwp'),
						"id"			=> "product_box_layout",
						"type"			=> "select",
 						"options" 		=>  hexwp_array_options('box_layout',true),	
										 
 				); 				 
				
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);	
