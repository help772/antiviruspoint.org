<?php
// Prevent direct access
defined('ABSPATH') || exit;
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 												Duplicate Post Link
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_duplicate_post_link" )){
add_filter( 'post_row_actions', 'vh_duplicate_post_link', 10, 2 );
function vh_duplicate_post_link( $actions, $post ) {
	 
	if($post->post_type =='visualheader'){
		if ( current_user_can('edit_post', $post->ID )  ) {
  			$actions['vh_duplicate'] = '<a id="vh_duplicate_'.esc_attr($post->ID).'" vh-post-id="'.esc_attr($post->ID).'" class="vh_duplicate_post" >'.esc_html__('Duplicate', 'visual-header').'</a>' ;
  		}
	}
	
	return $actions;	 
}	
}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 												Duplicate
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_duplicate" )){
add_action('wp_ajax_vh_duplicate', 'vh_duplicate');
function vh_duplicate() {
	
	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vh_duplicate_nonce' ) )
	return;
 	 if (!current_user_can('manage_options')) return;	
	

	$post_id =!empty($_POST['post_id'])? absint(wp_unslash(filter_input( INPUT_POST, 'post_id',  FILTER_SANITIZE_NUMBER_INT ))):'';		
 	$title = get_the_title($post_id);
	$builder_json = get_post_meta($post_id, 'vh_builder_json', true);;
  
	$my_post = array(
		'post_title'    => wp_strip_all_tags( $title.' '.__(' Copy','visual-header' )),
		'post_status' => 'draft',
        'post_author'   => get_current_user_id(),
 		'post_type'  => 'visualheader',
 		'meta_input'    => array(
			'vh_builder_json' =>$builder_json,
		)
	);

 wp_insert_post( $my_post ); 
     
}
}
