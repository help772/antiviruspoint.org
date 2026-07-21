<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Metabox
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( !function_exists ( "vs_add_metabox" )){

add_action('add_meta_boxes', 'vs_add_metabox');
function vs_add_metabox($post_type) {
    $types = array('visualslider');

    if (in_array($post_type, $types)) {
      add_meta_box(
        'vs_metabox',
        esc_html__('Slider','visual-slider'),
        'vs_metabox',
        'visualslider',
        'normal',
        'high'
      );
    }
}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Meta Panel
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( !function_exists ( "vs_metabox" )){
function vs_metabox($post) {
 	global $post;
 
   wp_nonce_field( 'vs_metabox_nonce', 'vs_metabox_nonce' );
 
 
	  
	echo '<div class="vs_metabox">';
		 
 	 		vs_module_heading();
			
			
			echo '<div class="vs_module_content">';
 			vs_module_content(true);
			echo '</div>';
 	 	 
			vs_add_layer();
			vs_perview_global_warp();
 	echo '</div>';
    
	 
}   
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
															 	Slider Meta Save
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( !function_exists ( "vs_module_heading" )){

function vs_module_heading() {
	 	global $post;

	?>
		 <div class="vs_module_heading">
			 <div class="vs_module_shortcode">
 			
			
			 <div class="vs_options_name">
				<label for="vs_shortcode"><?php echo esc_html__('Shortcode','visual-slider');?></label>
 			</div>
			
			<div class="vs_options_setting">
				<textarea id="vs_shortcode" name="vs_shortcode" readonly ><?php echo esc_html('[visualslider id="'.intval($post->ID).'"]');?></textarea>
			</div>
		 </div>
			 <div class="vs_module_global_template">
			 	<a class="vs_module_global_template_save vs_module_button vs_module_template_save" data-id="global" ><?php echo esc_html__('Save Full Template in Library','visual-slider');?></a>
			 	<a class="vs_module_global_template_add vs_module_button"><?php echo esc_html__('Add Full Template From Library','visual-slider');?></a>
                
			 	<a class="vs_module_global_template_demo vs_module_button" ><?php echo esc_html__('Add Ready-Made Full Template','visual-slider');?></a>
			 	<a class="vs_module_global_template_import vs_module_button" ><?php echo esc_html__('Import','visual-slider');?></a>
			 	<a class="vs_module_global_template_export vs_module_button" ><?php echo esc_html__('Export','visual-slider');?></a>
 			
			</div>
			
			 
			
	</div>
<?php
}

}
if ( !function_exists ( "vs_module_content" )){

add_action('wp_ajax_vs_module_content', 'vs_module_content');
function vs_module_content($none_ajax='') {
	if(empty($none_ajax)){
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
		return;
		$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])):absint(get_the_ID()) ;
      if (!current_user_can('edit_post', $post_id)) return;
	}
 	
 
 	
	 	global $post;
 		$global_demo_template_id =!empty($_POST['global_demo_template_id']) && empty($none_ajax) ? sanitize_key(wp_unslash($_POST['global_demo_template_id'])):'';
 
		if(!empty($_POST['global_demo_template_id']) && empty($none_ajax)){
 			$settings_json = vs_module_file_json($global_demo_template_id,'setting');
			$slide_json =  vs_module_file_json($global_demo_template_id,'slide');
			
  		}elseif(!empty($_POST['template_import']) && empty($none_ajax)){
  			$template_import = json_decode(wp_kses(wp_unslash($_POST['template_import']),vs_kses()),true);
  			$settings_json = !empty($template_import['setting'])?vs_urldecode($template_import['setting']):'';
 			$slide_json =  !empty($template_import['slide'])? vs_urldecode($template_import['slide']):'';
			
		}else if(!empty($_POST['global_template_id']) && empty($none_ajax)){
			
			$template =	get_option( 'vs_global_template');
 			$global_template_id =!empty($_POST['global_template_id']) && empty($none_ajax)? sanitize_key(wp_unslash($_POST['global_template_id'])):'';

			$settings_json = urldecode($template[$global_template_id]['setting']);
			$slide_json = urldecode($template[$global_template_id]['slide']);
  		}else{
  			$settings_json = get_post_meta(absint(get_the_ID()), 'vs_setting_json', true);
  			$slide_json = get_post_meta(absint(get_the_ID()), 'vs_slide', true);
 		}
	 
  		$settings= vs_options_array_row($settings_json);
 
		$slide= vs_options_array_row($slide_json);
 			
 		$settings_json_textarea = !empty( $settings_json ) ? $settings_json : '';
		echo '<textarea  type="hidden"   name="vs_setting_json" id="vs_setting_json">'.esc_textarea($settings_json_textarea).'</textarea>';
 		$slide_json_textarea = !empty( $slide_json ) ? $slide_json : '';
		echo '<textarea  type="hidden"   name="vs_slide" id="vs_slide">'.esc_textarea($slide_json_textarea).'</textarea>';
 
		vs_module_setting($settings);
		
		vs_module_slide($slide);
		
		
 
 
 
	if(empty($none_ajax)){
		die();
	}
}
}

 if ( !function_exists ( "vs_module_file_json" )){
function vs_module_file_json($id,$type) {
 	return file_get_contents(VISUALSLIDER_DIR .'import/'.$id.'/'.$id.'_'.$type.'.json');
  }

 }
  if ( !function_exists ( "vs_metabox_save" )){
add_action('save_post', 'vs_metabox_save'); 
function vs_metabox_save($post_id) {
 	if ( ! isset( $_POST['vs_metabox_nonce'] ) ||
	! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['vs_metabox_nonce'])), 'vs_metabox_nonce' ) )
		return;
	 
    if (!current_user_can('edit_post', $post_id)) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	
	if(!empty($_POST['vs_setting_json'])) {
		$vs_setting = wp_kses(wp_unslash($_POST['vs_setting_json']),vs_kses());
       	update_post_meta($post_id, 'vs_setting_json', $vs_setting);
    } else {
     	 delete_post_meta($post_id, 'vs_setting_json');
    }

	if(!empty($_POST['vs_slide'])) {
		$vs_slide = wp_kses(wp_unslash($_POST['vs_slide']),vs_kses());
       	update_post_meta($post_id, 'vs_slide', $vs_slide);
    } else {
     	 delete_post_meta($post_id, 'vs_slide');
    }
	 
	 
	 

}
  }