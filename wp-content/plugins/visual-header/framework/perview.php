<?php
defined('ABSPATH') || exit;
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 														Builder Perview Reload
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_header_perview" )){
add_action('wp_ajax_vh_header_perview', 'vh_header_perview');
 function vh_header_perview(){
	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vh_header_nonce' ) )
	return;
	if (!current_user_can('manage_options')) return;
	
	$visualheader=!empty($_POST['header_preview'])?wp_kses_post(wp_unslash($_POST['header_preview'])):'';
	
 	$header= json_decode(stripslashes($visualheader),true);
 	if(has_filter('vh_header_perview')) {
		 apply_filters('vh_header_perview', $header);
	}
	if(has_filter('vh_header_perview_css')) {
		 apply_filters('vh_header_perview_css', $header);
	}
  	 
	die('');
	 
	
}
}
 
  