<?php
// Prevent direct access
defined('ABSPATH') || exit;
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 												Duplicate Post Link
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_header_default" )){
add_action('wp_ajax_vh_header_default', 'vh_header_default');  
function vh_header_default() {
	
	
 	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vh_header_nonce' ) )
	return;
 	if (!current_user_can('manage_options')) return;	
	
	
	
	$key = !empty($_POST['key']) ?sanitize_text_field(wp_unslash(filter_input( INPUT_POST, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS ))):'';
 	if(has_filter('vh_make_default')){
		apply_filters('vh_make_default',$key);
	}
	
	
		
}  
}

/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 												  Post Post States
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/
if ( !function_exists ( "vh_display_post_states" )){
add_filter( 'display_post_states', 'vh_display_post_states', 10, 2 ); 
function vh_display_post_states( $post_states, $post ) {
	 
	 
   	if(has_filter('vh_post_states_default')){
	$default= apply_filters('vh_post_states_default',$post->post_name);
	} 
 	if(!empty($default) ){
		$post_states['vh_visualheader'] =__('This Header is Default','visual-header');
	} 	 
 	 
	return $post_states;
}
}
   