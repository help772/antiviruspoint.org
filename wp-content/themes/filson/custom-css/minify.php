<?php

 hexwp_mergfile();


  function hexwp_mergfile() {
 	$style_left='';
 
	 
 	$style_right='/*
Theme Name: Filson
Text Domain: hexwp
Description: Woocomrece WordPress Theme
Author: Hex-WP
Version: 1.4
Tested up to: 6.3.2
Requires at least: 5.6.0
Requires PHP: 7.4
Tags: three-columns,  Threaded-comments, Translation-ready, Custom-menu
License: GNU General Public License v3.0 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
/*
/*
	
	';
 	$style_right.='/*';
	foreach(hexwp_style_array() as $key => $value){
$style_right.=''.$value.'
';
	}
 	$style_right.='*/';
	
	
	
	foreach(hexwp_style_array() as $key => $value){
		$style_right_file =   hexwp_FILE.'/css/'.$key.'.css';
		
		$style_right.='
/******************************************************************************************************************************************************
******************************************************************************************************************************************************

																	 	'.$value.'
																		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
'; 
		
		 
		
		if (file_exists($style_right_file)) {
			$style_right.=file_get_contents($style_right_file);
		}
	}	
	$style_right_write = fopen(hexwp_FILE.'/style.css', "w") or die("Unable to open file!");
 	fwrite($style_right_write,$style_right);
 	fclose($style_right_write);
	
	 
 	$woocommerce_right='';
	foreach(hexwp_woocommerce_array() as $key => $value){
  		$woocommerce_right_file =  hexwp_FILE.'/css/'.$key.'.css';
		if (file_exists($woocommerce_right_file)) {
			$woocommerce_right.=file_get_contents($woocommerce_right_file);
		}
	}	
	$woocommerce_right_write = fopen(hexwp_FILE.'/css/woocommerce.css', "w") or die("Unable to open file!");
 	fwrite($woocommerce_right_write,$woocommerce_right);
 	fclose($woocommerce_right_write);
 
	  
 	$scripts='';
	foreach(hexwp_scripts_array() as $key => $value){
   	$scripts.='';
		$scripts_file =   hexwp_FILE.'/js/'.$key.'.js';
		if (file_exists($scripts_file)) {
			$scripts.=file_get_contents($scripts_file);
		}
	}	
	$scripts_write = fopen(hexwp_FILE.'/js/all.js', "w") or die("Unable to open file!");
 	fwrite($scripts_write, $scripts );
 	fclose($scripts_write);
	 

       
}
 
 