<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Body Style

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "body_style_start",	
						"type"		=> "content"
				);

$of_options[] = array( 	"name" 			=> esc_html__( 'Body Style', 'hexwp' ),
						"type" 			=> "title"
				);	 					


 
 
 
$of_options[] = array( 	"name" 			=> esc_html__('Background Color' , 'hexwp'),
 						"id"			=> "body_background_color",
 						"type" 			=> "color"
				); 
 
 
$of_options[] = array( "name"			=> esc_html__('Body Background Type' , 'hexwp'),
						"desc" 			=> esc_html__('Select Background Type Gradient , Pattern And Upload Custom Image' , 'hexwp'),
						"id" 			=> "body_background_type",
 						"type" 			=> "radio",
 						"hide" 			=> "none",
						"options" 		=> array(
													'none'			=> esc_html__('None','hexwp'),
													'pattern' 		=> esc_html__('Pattern','hexwp'),
													'custom' 		=> esc_html__('Custom Image','hexwp'),
										)
				);
	
$of_options[] = array( 	"name" 			=> esc_html__('Background Pattern' , 'hexwp'),
						"desc" 			=> esc_html__('Select a background pattern' , 'hexwp'),
						"id" 			=> "body_background_pattern",
 						"fold_array"	=>  array(
													'pattern'		=>	'body_background_type', 
 											),
						"type" 			=> "tiles",
						"options" 		=> array(
									''=> 		hexwp_DIR.'/images/bg/none.png',
									'bg1'=>		hexwp_DIR.'/images/bg/bg1.png',
									'bg2'=>		hexwp_DIR.'/images/bg/bg2.png',
									'bg3'=>		hexwp_DIR.'/images/bg/bg3.png',
									'bg4'=>		hexwp_DIR.'/images/bg/bg4.png',
									'bg5'=>		hexwp_DIR.'/images/bg/bg5.png',
									'bg6'=>		hexwp_DIR.'/images/bg/bg6.png',
									'bg7'=>		hexwp_DIR.'/images/bg/bg7.png',
									'bg8'=>		hexwp_DIR.'/images/bg/bg8.png',
									'bg9'=>		hexwp_DIR.'/images/bg/bg9.png',
									'bg10'=>	hexwp_DIR.'/images/bg/bg10.png',
									'bg11'=>	hexwp_DIR.'/images/bg/bg11.png',
									'bg12'=>	hexwp_DIR.'/images/bg/bg12.png',
									'bg13'=>	hexwp_DIR.'/images/bg/bg13.png',
									'bg14'=>	hexwp_DIR.'/images/bg/bg14.png',
									'bg15'=>	hexwp_DIR.'/images/bg/bg15.png',
									'bg16'=>	hexwp_DIR.'/images/bg/bg16.png',
									'bg17'=>	hexwp_DIR.'/images/bg/bg17.png',
									'bg18'=>	hexwp_DIR.'/images/bg/bg18.png',
									'bg19'=>	hexwp_DIR.'/images/bg/bg19.png',
									'bg20'=>	hexwp_DIR.'/images/bg/bg20.png',
									'bg21'=>	hexwp_DIR.'/images/bg/bg21.png',
									'bg22'=>	hexwp_DIR.'/images/bg/bg22.png',
									'bg23'=>	hexwp_DIR.'/images/bg/bg23.png',
			
						),
  				);	
						 
						  
					
					
$of_options[] = array( 	"name" 			=> esc_html__('Custom Background Image' , 'hexwp'),
						"desc" 			=> esc_html__('Upload images using native media uploader from Wordpress 3.5+' , 'hexwp'),
						"id" 			=> "body_background_image",
 						"fold_array"	=>  array(
													'custom'		=>	'body_background_type', 
 											),
 						"type" 			=> "upload",
 						"fold" 			=> "body_background_type",
				);
				 
$of_options[] = array( 	"name" 			=> esc_html__( 'Body Layout', 'hexwp' ),
 						"id" 			=> "body_layout",
						"type" 			=> "radio",
						"options" 			=> array(
							"disable" 			=> __( 'Wide', 'hexwp' ),						 
							"enable" 			=>  __( 'Boxed' , 'hexwp' ),
 						),		
 				);					 
 				

 
				
 $of_options[] = array( "name" 			=> esc_html__('Boxed Background Color' , 'hexwp'),
						"id" 			=> "body_boxed_background",
						"fold_array"	=>  array(
													'enable'		=>'body_layout', 
											),		
 						"type" 			=> "color_rgba",
 
						 
				);				
				
 $of_options[] = array( "name" 			=> esc_html__('Boxed Shadow' , 'hexwp'),
						"id" 			=> "body_boxed_shadow",
						"fold_array"	=>  array(
													'enable'		=>'body_layout', 
											),		
    					"type"			=> "multi_options",
 						"options"		=> hexwp_multi_array_options('shadow'),
						
 					 
				);
					 	
$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);							  						