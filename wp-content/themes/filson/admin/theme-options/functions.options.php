<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

													of options

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('init','of_options');

if( !defined('hexwp_OPT_PATH') ){
	define( 'hexwp_OPT_PATH', hexwp_PATH.'/admin/theme-options/ui' );
}
 


if (!function_exists('of_options')){
	
function of_options() {
	  if(is_admin()){
		$bg_images_path = get_stylesheet_directory(). '/images/bg/'; // change this to where you store your bg images
		$bg_images_url = hexwp_DIR.'/images/bg/'; // change this to where you store your bg images
		$bg_images = array();
		
 		include_once hexwp_PATH . '/admin/theme-options/google-font.php';
 		$uploads_arr 		= wp_upload_dir();
		$all_uploads_path 	= $uploads_arr['path'];
		$all_uploads 		= get_option('of_uploads');
		$other_entries 		= array("Select a number:","1","2","3","4","5","6");
		$body_repeat 		= array("no-repeat","repeat-x","repeat-y","repeat");
 
 
 		global $of_options;
 	
 
 
		include_once hexwp_OPT_PATH . '/general.php';
 
		 include_once hexwp_OPT_PATH . '/general-style.php';
		include_once hexwp_OPT_PATH . '/header-layout.php';
 		include_once hexwp_OPT_PATH . '/columns-options.php';
    		include_once hexwp_OPT_PATH . '/blog-options.php';
		include_once hexwp_OPT_PATH . '/single-options.php';
		
 		
		if ( function_exists ( "is_woocommerce" )){
 		include_once hexwp_OPT_PATH . '/product-options.php';
		}
		
    		include_once hexwp_OPT_PATH . '/post-style.php';
 		include_once hexwp_OPT_PATH . '/footer-options.php';
   		include_once hexwp_OPT_PATH . '/ads-settings.php';
		
		include_once hexwp_OPT_PATH . '/social-networks.php';
		include_once hexwp_OPT_PATH . '/typography.php';
 		include_once hexwp_OPT_PATH . '/translation.php';
 		include_once hexwp_OPT_PATH . '/backup.php';
  
 
	 
}
	
	
}}