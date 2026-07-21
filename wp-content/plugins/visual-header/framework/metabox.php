<?php
// Prevent direct access
defined('ABSPATH') || exit;
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 														Header Builder Metabox
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_add_metabox" )){
add_action('add_meta_boxes', 'vh_add_metabox');
function vh_add_metabox($post_type) {
    $types = array('visualheader');
    global $wp_meta_boxes;
    $post_type = 'visualheader';
 	$submit_box_id = 'submitdiv';
	if(!empty($wp_meta_boxes[$post_type]['side']['core'][$submit_box_id])){
    $wp_meta_boxes[$post_type]['side']['core'] = array( $wp_meta_boxes[$post_type]['side']['core'][$submit_box_id] );
	}
    if (in_array($post_type, $types)) {
		
      add_meta_box(
        'vh_metabox',
         __('Header Builder','visual-header'),
        'vh_panel_option',
        'visualheader',
        'normal',
        'high'
      );
    }
}
}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 														HB Panel Options
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_panel_option" )){
function vh_panel_option() {
	
    wp_nonce_field( 'vh_metabox_nonce', 'vh_metabox_nonce' );
 	echo '<div class="vh_panel">';
	vh_builder(true);
	echo '</div>';

}
}

/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 													Load Image
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_id_image" )){
 add_action('wp_ajax_vh_id_image', 'vh_id_image');
	
function vh_id_image() { 
 	
 	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'vh_header_nonce' ) )
	return;

	 if (!current_user_can('manage_options')) {
	 		wp_die(__('You do not have permission to access this action.', 'visual-header'));
	 	}
	$id =!empty($_POST['id'])? absint(wp_unslash(filter_input( INPUT_POST, 'id',  FILTER_SANITIZE_NUMBER_INT ))):'';	 
	if(!empty($id)){
		echo '<div class="vh_image_item_medium"  >';
		$image_medium = wp_get_attachment_image_src($id, 'medium');
		if(!empty($image_medium[0])){ 
					echo '<a class="vh_image_remove"  ></a>';
					echo '<img src="'.esc_url($image_medium[0]).'"/>';
		}
		echo '</div>';
	}
 	 
	 die('');
  
}
}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 														Meta Box Save
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_metabox_save" )){
add_action('save_post', 'vh_metabox_save'); 
function vh_metabox_save($post_id) {
	
 	if ( ! isset( $_POST['vh_metabox_nonce'] ) ||
	! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['vh_metabox_nonce'])), 'vh_metabox_nonce' ) )
	return;
	 
    if (!current_user_can('edit_post', $post_id)) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	
	if(!empty($_POST['vh_builder_json'])) {
		$vh_builder = wp_kses_post(wp_unslash($_POST['vh_builder_json']));
       	update_post_meta($post_id, 'vh_builder_json', $vh_builder);
    } else {
     	 delete_post_meta($post_id, 'vh_builder_json');
    }
 
}
}

 