<?php
$of_options[] = array(	"position"		=> "start",
						"id" 			=> "sidebar_layout_start",	
						"type"		=> "content"
				);		
 
$of_options[] = array(	"name"			=> esc_html__('Sidebar Layout' , 'hexwp'),
						"type"			=> "title"
				);
				

$of_options[]= array(	"name"			=> esc_html__('Sidebar Box Layout','hexwp'),
						"id"			=> "sidebar_box_layout",
						"type"			=> "radio",
						"hide" 			=> 'none',
						"options" 		=>	array(
													"none"				=> esc_html__('None','hexwp'),
													"boxed-all"		=> esc_html__('Boxed All','hexwp'), 
													"boxed-item"		=> esc_html__('Boxed Item','hexwp'), 
						),
				); 					
				
				
$of_options[] = array(	"name"			=> esc_html__('Sticky Main Sidebar' , 'hexwp'),
						"id" 			=> "sticky_sidebar",
						"type" 			=> "radio",
						"hide" 			=> 'hide',
						"options" 			=> array(
							'show' 			=>  __( 'Enable' , 'hexwp' ),
							'hide' 			=> __( 'Disable', 'hexwp' ),						 
  						),	
				); 
				 
				
$of_options[] = array(	"name" 			=> esc_html__('Show Sidebar in Mobile' , 'hexwp'),
						"id" 			=> "mobile_sidebar",
						"type" 			=> "radio",
						"hide" 			=> 'hide',
						"options" 			=> array(
							'show' 			=>  __( 'Enable' , 'hexwp' ),
							'hide' 			=> __( 'Disable', 'hexwp' ),						 
  						),	
				);  
$of_options[] = array( 	"position" 		=> "end",
 						"type"			=> "content"
 			);								
				