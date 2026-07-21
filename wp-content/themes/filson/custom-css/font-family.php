<?php
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Font Family
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$font_family = hexwp_option('body_font_family');
 
if( $font_family == 'Rubik'){
 	  $font_family='rubik';
	$css.=" 
 	@font-face {
			font-family: 'Rubik';
			font-weight:normal;
			font-weight:400;
			src: url('".get_template_directory_uri()."/fonts/rubik-400.woff2') format('woff2');
		}
		@font-face {
			font-family: 'Rubik';
			font-weight:bold;
			font-weight:700;
			src: url('".get_template_directory_uri()."/fonts/rubik-700.woff2') format('woff2');
		} 
	
		@font-face {
			font-family:'Rubik';
			font-weight:medium;
			font-weight:500;
			src: url('".get_template_directory_uri()."/fonts/rubik-500.woff2') format('woff2');
		} 
	
		@font-face {
			font-family: 'Rubik';
 			font-weight:900;
			src: url('".get_template_directory_uri()."/fonts/rubik-900.woff2') format('woff2');
		} 	
		@font-face {
			font-family: 'Rubik';
 			font-weight:300;
			src: url('".get_template_directory_uri()."/fonts/rubik-300.woff2') format('woff2');
		} 	
		
	";
} 