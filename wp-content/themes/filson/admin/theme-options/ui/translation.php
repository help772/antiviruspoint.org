<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Translation

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$orginal = esc_html__('Orginal', 'hexwp');
$of_options[] = array( 	"name" 		=> esc_html__('Translation' , 'hexwp' ),
						"type" 		=> "heading",
						"id" 		=> "translation",
						"icon"		=> ADMIN_IMAGES."typography.png",

						 
				); 
 $of_options[] = array(	"position"		=> "start",
						"id" 			=> "translation_start",	
						"type"		=> "content"
				);
				
$of_options[] = array( 	"name" 		=> esc_html__('Translation' , 'hexwp' ),
						"type" 		=> "title"
				);	
 
 $hexwp_translation=hexwp_translation('array');
  if( is_array($hexwp_translation)){
 foreach($hexwp_translation as $trans_key => $trans_vale){
	$of_options[] = array( "name"			=> $orginal.' "'.esc_html($trans_vale).'"',
							"id"			=> "t_".esc_html($trans_key)."",
							"std"			=> esc_html($trans_vale),
							"type"			=> "text"
				);  	
	 
 }
 }
		$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
 				);						 
																																 
