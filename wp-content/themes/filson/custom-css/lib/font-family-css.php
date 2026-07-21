<?php
//Body
$font_family = 'rubik'; 
 
if($font_family == 'rubik'){
	 
	$css.=" 
 	@font-face {
			font-family: '$font_family';
			font-weight:normal;
			font-weight:400;
			src: url('".get_template_directory_uri()."/fonts/$font_family-400.woff2') format('woff2');
		}
		@font-face {
			font-family: '$font_family';
			font-weight:bold;
			font-weight:700;
			src: url('".get_template_directory_uri()."/fonts/$font_family-700.woff2') format('woff2');
		} 
	
		@font-face {
			font-family: '$font_family';
			font-weight:medium;
			font-weight:500;
			src: url('".get_template_directory_uri()."/fonts/$font_family-500.woff2') format('woff2');
		} 
	
		@font-face {
			font-family: '$font_family';
 			font-weight:900;
			src: url('".get_template_directory_uri()."/fonts/$font_family-900.woff2') format('woff2');
		} 	
		@font-face {
			font-family: '$font_family';
 			font-weight:300;
			src: url('".get_template_directory_uri()."/fonts/$font_family-300.woff2') format('woff2');
		} 	
		
	";
} 