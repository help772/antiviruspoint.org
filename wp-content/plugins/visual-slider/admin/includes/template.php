<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 if ( !function_exists ( "vs_template_save" )){
add_action('wp_ajax_vs_template_save', 'vs_template_save');
function vs_template_save(){
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}
	$id = !empty($_POST['id'])?sanitize_key(wp_unslash( $_POST['id'])):''; 
 	 
	echo '<div id="vs_template" class="vs_template vs_template_save_options vs_options  vs_template_'.esc_attr($id).'" data-id="'.esc_attr($id).'"  data-key="'.esc_attr($key).'">';
	echo '<div class="vs_template_middle">';
		//Title
		echo '<div class="vs_template_title"><h3>';
		if($id=='global'){
		echo esc_html__('Save Full Template in Library','visual-slider');
		} 
		if($id=='slide'){
		echo esc_html__('Save Slide in Library','visual-slider');
		}
	if($id=='layer'){
		echo esc_html__('Save Layer in Library','visual-slider');
		}		 
 		echo '</h3>';
   			echo '<div class="vs_template_cancel">'.esc_html__('Cancel','visual-slider').'</div>';
			echo '<div class="vs_template_save">'.esc_html__('Save in Library','visual-slider').'</div>';
 		echo '</div>';	        
		echo '<div class="vs_template_content">';
				
				echo '<div class="vs_template_massage">'.esc_html__('* The Name is Empty, Please Fill it in','visual-slider').'</div>';
				
				echo '<div class="vs_options_name">';
					echo '<label for="#vs_template_name">'.esc_html__('Name to save','visual-slider').'</label>';
 				echo '</div>';
				
				echo '<div class="vs_options_setting">';
					echo '<input id="vs_template_name" type="text" class="vs_form_text" name="vs_template_name"  value=""  >';
				echo '</div>';
		
			    
		echo '</div>';
	echo '</div>';
	echo '</div>';
 	die('');
}  
}
if ( !function_exists ( "vs_template_save_global" )){
add_action('wp_ajax_vs_template_save_global', 'vs_template_save_global');
function vs_template_save_global() {
	
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
	
	$old_data =	get_option( 'vs_global_template');
	if( !is_array($old_data)){
		$old_data=array();
	}
	$key = 'r'.wp_rand(0,999999999999); 

 	$old_data[sanitize_key($key)]['name'] =  !empty($_POST['name'])?sanitize_title(wp_unslash($_POST['name'])):'';
	if(!empty($_POST['setting'])){
		$old_data[sanitize_key($key)]['setting'] =  urlencode(wp_kses(wp_unslash($_POST['setting']),vs_kses()));
	}
	
	if(!empty($_POST['slide'])){
		$old_data[sanitize_key($key)]['slide'] =  urlencode(wp_kses(wp_unslash($_POST['slide']),vs_kses()));
	}
	 
  
	update_option( 'vs_global_template', $old_data );
	die(0);
    
} 
 
 }
 if ( !function_exists ( "vs_template_save_slide" )){
 add_action('wp_ajax_vs_template_save_slide', 'vs_template_save_slide');
function vs_template_save_slide() {
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
	
$old_data =	get_option( 'vs_slide_template');
	if( !is_array($old_data)){
		$old_data=array();
	}
	$key = 'r'.wp_rand(0,999999999999); 
	$old_data[sanitize_key($key)]['name'] = !empty($_POST['name'])?sanitize_title(wp_unslash($_POST['name'])):'';
 
	if(!empty($_POST['slide'])){
		$old_data[sanitize_key($key)]['slide'] =  urlencode(wp_kses(wp_unslash($_POST['slide']),vs_kses()));
	}
	 
  
	update_option( 'vs_slide_template', $old_data );
	die(0);
    
} 

 }
 if ( !function_exists ( "vs_template_save_layer" )){
 add_action('wp_ajax_vs_template_save_layer', 'vs_template_save_layer');
function vs_template_save_layer() {

		
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
	
	
	$old_data =	get_option( 'vs_layer_template');
	
	if( !is_array($old_data)){
		$old_data=array();
	}
	
	$key = 'r'.wp_rand(0,999999999999); 
 	$old_data[sanitize_key($key)]['name'] = !empty($_POST['name'])?sanitize_title(wp_unslash($_POST['name'])):'';
 
	if(!empty($_POST['layer'])){
		$old_data[sanitize_key($key)]['layer'] =  urlencode(wp_kses(wp_unslash($_POST['layer']),vs_kses()));
	}
	 
  
	update_option( 'vs_layer_template', $old_data );
	die(0);
    
} 
 }

 if ( !function_exists ( "vs_template_options" )){
add_action('wp_ajax_vs_template_options', 'vs_template_options');
function vs_template_options() {
	
	
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
 	
	
 
	$id = !empty($_POST['id'])?sanitize_key(wp_unslash( $_POST['id'])):''; 
	$old_data =	get_option( 'vs_'.$id.'_template');
	
	echo '<div  id="vs_template" class="vs_template vs_template_options vs_template_'.esc_attr($id).'" data-row="'.esc_attr($id).'">';
	echo '<div class="vs_template_middle">';
		//Title
		echo '<div class="vs_template_title"><h3>';
		if($id =='global'){
		echo esc_html__('Add Full Template From Library','visual-slider');
		} 
		if($id =='slide'){
		echo esc_html__('Add Slide From Library','visual-slider');
		} 
			if($id =='layer'){
		echo esc_html__('Add Layer From Library','visual-slider');
		} 	
 		echo '</h3>';
 
   			echo '<div class="vs_template_cancel">'.esc_html__('Cancel','visual-slider').'</div>';
			echo '<div class="vs_template_add">'.esc_html__('Add From Library','visual-slider').'</div>';
			
 		echo '</div>';	        
		echo '<div class="vs_template_content">';
	
		if (!empty($old_data)) :
			foreach($old_data as $key => $value ):
				echo '<li class="vs_template_item" data-id="'.esc_attr($key).'">';
					echo '<div class="vs_template_name">'.esc_html($value['name']).'</div>';
 					echo '<a class="vs_template_remove"><'.esc_attr('img').' src="'.esc_url(VISUALSLIDER_DIR.  'admin/assets/image/remove.png').'">'.esc_html__('Remove','visual-slider').'</a>';
				echo '</li>';
 	
			endforeach;
			endif;		
			    
		echo '</div>';
	echo '</div>';
	echo '</div>'; 	
	 
  	die(0);
}   
 }
if ( !function_exists ( "vs_template_remove" )){

add_action('wp_ajax_vs_template_remove', 'vs_template_remove');
function vs_template_remove() {
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
	
	$id = !empty($_POST['id'])?sanitize_key(wp_unslash( $_POST['id'])):''; 
	$key = !empty($_POST['key'])?sanitize_key(wp_unslash( $_POST['key'])):''; 
	$old_data =	get_option( 'vs_'.$id.'_template');
	unset($old_data[sanitize_key($key )]);
 	update_option( 'vs_'.$id.'_template', $old_data );
	die(0);
	
}  
}
if ( !function_exists ( "vs_template_demo" )){
add_action('wp_ajax_vs_template_demo', 'vs_template_demo');
function vs_template_demo() {
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
	
   	echo '<div  id="vs_template" class="vs_template vs_template_options vs_template_demo " data-row="global">';
	echo '<div class="vs_template_middle">';
		//Title
		echo '<div class="vs_template_title"><h3>';
		echo esc_html__('Add Ready-Made Full Template','visual-slider');
 		echo '</h3>';
 
   			echo '<div class="vs_template_cancel">'.esc_html__('Cancel','visual-slider').'</div>';
			echo '<div class="vs_template_add">'.esc_html__('Add','visual-slider').'</div>';
			
 		echo '</div>';	        
		echo '<div class="vs_template_content">';
		
		
		 $old_data = vs_import_options();
 		 
		if (!empty($old_data)) :
			foreach($old_data as $key => $value ):
				echo '<li class="vs_template_item" data-id="'.esc_attr($key).'">';
					echo '<'.esc_attr('img').' src="'.esc_url($value['img']).'">';
					echo '<a class="vs_template_demo_link"  target="_blank" href="'.esc_url($value['demo']).'">'.esc_html__('Perview','visual-slider').'</a>';
				echo '</li>';
 	
			endforeach;
			endif;		
 		echo '</div>';
	echo '</div>';
	echo '</div>'; 	
	 
  	die(0);
}  
}
if ( !function_exists ( "vs_template_import" )){
add_action('wp_ajax_vs_template_import', 'vs_template_import');
function vs_template_import() {
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
	
   	echo '<div  id="vs_template" class="vs_template vs_template_options vs_template_import " data-row="global">';
	echo '<div class="vs_template_middle">';
		//Title
		echo '<div class="vs_template_title"><h3>';
		echo esc_html__('Export Template','visual-slider');
 		echo '</h3>';

   			echo '<div class="vs_template_cancel">'.esc_html__('Cancel','visual-slider').'</div>';
			echo '<div class="vs_template_add">'.esc_html__('Add','visual-slider').'</div>';
 			
 		echo '</div>';	        
		echo '<div class="vs_template_content">';
		
		
  		 
		 
			echo '<textarea name="vs_template_import"></textarea>';
			
 		echo '</div>';
	echo '</div>';
	echo '</div>'; 	
	 
  	die(0);
}  
}
if ( !function_exists ( "vs_template_export" )){
add_action('wp_ajax_vs_template_export', 'vs_template_export');
function vs_template_export() {
	
	if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
	if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
		 return;
	}	
	
   	echo '<div  id="vs_template" class="vs_template vs_template_options vs_template_export " data-row="global">';
	echo '<div class="vs_template_middle">';
		//Title
		echo '<div class="vs_template_title"><h3>';
		echo esc_html__('Export Template','visual-slider');
 		echo '</h3>';

   			echo '<div class="vs_template_cancel">'.esc_html__('Cancel','visual-slider').'</div>';
 			
 		echo '</div>';	        
		echo '<div class="vs_template_content">';
		
		
  		 
			if(!empty($_POST['setting'])){
				$data['setting'] =  wp_kses(wp_unslash( $_POST['setting']),vs_kses());
			}
			
			if(!empty($_POST['slide'])){
				$data['slide'] =wp_kses(wp_unslash( $_POST['slide']),vs_kses());
			}
			
			echo '<textarea>'.wp_kses(wp_unslash(json_encode($data)),vs_kses()).'</textarea>';
			
 		echo '</div>';
	echo '</div>';
	echo '</div>'; 	
	 
  	die(0);
}  
}