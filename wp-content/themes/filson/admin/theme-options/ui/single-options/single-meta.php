<?php
 																							
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Single Post Meta Settings

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "single_meta_start",	
						"type"		=> "content"
				);
$of_options[] = array( "name" 			=> esc_html__('Single Post Meta Settings' , 'hexwp'),
						"type" 			=> "title"
				);				
 			
$of_options[] = array( 	"name" 			=> esc_html__('Author Meta' , 'hexwp'),
 						"id" 			=> "single_meta_author",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);	
					
$of_options[] = array( 	"name" 			=> esc_html__('Date Meta' , 'hexwp'),
 						"id" 			=> "single_meta_date",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
					
$of_options[] = array( 	"name" 			=> esc_html__('Categories Meta' , 'hexwp'),
 						"id" 			=> "single_meta_cats",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
					
$of_options[] = array( 	"name" 			=> esc_html__('View Meta' , 'hexwp'),
 						"id" 			=> "single_meta_view",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);
					
$of_options[] = array( 	"name" 			=> esc_html__('Comments Meta' , 'hexwp'),
 						"id" 			=> "single_meta_comments",
    					"type" 			=> "radio",
						"hide" 			=> 0,
						"options" 			=> array(
							1 			=> __( 'Enable', 'hexwp' ),						 
							0 			=>  __( 'Disable' , 'hexwp' ),
 						),	
				);	
				
				
$of_options[]= array( 	"name"			=> esc_html__('Meta Layout','hexwp'),
						"id"			=> "single_meta_layout", 
						"type"			=> "multi_options",
						"options"		=>	array( 	
									array( 
										"name"			=> __('Between','hexwp'),			
										"id"			=> "between",
										"type"			=> "select",
										"options"		=>	array( 	
											"between-1"		=> __('empty','hexwp'),	
											"between-2"		=> __('-','hexwp'),	
											"between-3"		=> __('|','hexwp'),	
											"between-4"		=> __('/','hexwp'),	
										),
									),
									array( 
										"name"			=> __('Layout','hexwp'),			
										"id"			=> "layout",
										"type"			=> "select",
										"options"		=>	array( 	
											"layout-1"		=> __('no Icon','hexwp'),
											"layout-2"		=> __('no Icon & Avater Author','hexwp'),
											"layout-3"		=> __('by Icon','hexwp'),	
											"layout-4"		=> __('by Icon & no Icon Author ','hexwp'),	
											"layout-5"		=> __('by Icon & Avater Author','hexwp'),
										),
									),
					),
   				); 	 
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);
