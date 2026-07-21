<?php
$of_options[] = array( 	"name" 			=> esc_html__( 'Product Columns', 'hexwp' ),
						"type" 			=> "title"
				);		
				
				
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "product_columns_start",	
						"type"			=> "content"
				);
				

						
					
$url_product = hexwp_DIR . '/admin/assets/images/';
$of_options[] = array(	"name" 			=> esc_html__( 'Product Columns' , 'hexwp'),
   						"id" 			=> 	"product_column",
 						"type" 			=> "images",
						"options" 		=> array(
													'main' 				=> $url_product . 'main.png',
													'main_right' 		=> $url_product . 'main_right.png',
													'left_main' 		=> $url_product . 'left_main.png',
													'main_left_right' 	=> $url_product . 'main_left_right.png', 
													'left_main_right' 	=> $url_product . 'left_main_right.png',  
													'left_right_main' 	=> $url_product . 'left_right_main.png',  
  										)
				);
				
				
				
$of_options[] = array(  "name" 			=> esc_html__( 'Product Column Right', 'hexwp' ),
						"id" 			=> "product_column_right",
						"type" 			=> "radio",
						"fold_array"	=>  array(
													'main_right'			=>'product_column', 
													'main_left_right'			=>'product_column', 
													'left_main_right'			=>'product_column', 
													'left_right_main'			=>'product_column', 
											),							
						"options" 		=> 	array(	
													"1_3" => "1/3",
													"1_4" => "1/4",
													"1_5" => "1/5",
													"2_5" => "2/5",
													"1_6" => "1/6",
 											) 

 				);
				
				
$of_options[] = array(  "name" 			=> esc_html__( 'Product Column Left', 'hexwp' ),
						"id" 			=> "product_column_left",
						"fold_array"	=>  array(
													'left_main'			=>'product_column', 
													'main_left_right'			=>'product_column', 
													'left_main_right'			=>'product_column', 
													'left_right_main'			=>'product_column', 
											),	
						"type" 			=> "radio",
						"options" 		=> 	array(	 
													"1_3" => "1/3",
													"1_4" => "1/4",
													"1_5" => "1/5",
													"2_5" => "2/5",
													"1_6" => "1/6",
 											) 

 				);

			 
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);			 
