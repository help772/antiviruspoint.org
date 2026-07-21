<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( !function_exists ( "vs_module_slide" )){
function vs_module_slide($slide=false){
	global $post;
  
	echo '<div class="vs_module_slide_warp">';
 		echo '<div class="vs_module_add_warp">';
			echo '<a class="vs_module_slide_add vs_module_button">'.esc_html__('Add New Slide','visual-slider').'</a>';
			echo '<a class="vs_module_all_slide_template_save vs_module_button vs_module_template_save" data-id="all_slide">'.esc_html__('Save All Slides in Library','visual-slider').'</a>';
			echo '<a class="vs_module_slide_template_add vs_module_button">'.esc_html__('Add a Slide From Library','visual-slider').'</a>';

			
			
 		echo '</div>';
		echo '<ul class="vs_module_slide_list">';
		 vs_module_slide_list(true,$slide);
  	 	
		echo '</ul>';
	 
 	echo '</div>';
 
}
}

if ( !function_exists ( "vs_module_slide_list" )){
add_action('wp_ajax_vs_module_slide_list', 'vs_module_slide_list');
function vs_module_slide_list($none_ajax='',$slide =false){
	if(empty($none_ajax)){
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
		return;
		$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
		 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
			 return;
		}
	}
	
	
	
		if(!empty($_POST['slide_template_id']) && empty($none_ajax)){
			$template =	get_option( 'vs_slide_template');
 			$slide_json = urldecode($template[sanitize_text_field(wp_unslash( $_POST['slide_template_id']))]['slide']);
 			$slide= vs_options_array_row($slide_json);
   		} 
		$count=0;
 	if (!empty($slide)) :
            foreach($slide as $slide_key => $slide_value):
			$count++;
 
               vs_module_slide_item(true,$slide_key,$slide_value,$count);
			   
            endforeach;
            endif;

	if(empty($none_ajax)){
		die(0);
	}
}
}
if ( !function_exists ( "vs_module_slide_item" )){
add_action('wp_ajax_vs_module_slide_item', 'vs_module_slide_item');
function vs_module_slide_item($none_ajax='',$slide_key =false,$slide_value=false,$count=1){
	if(empty($none_ajax)){
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
		return;
		$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
		 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
			 return;
		}
	}
	$slide_template_id = !empty($_POST['slide_template_id'])?sanitize_key(wp_unslash( $_POST['slide_template_id'])):''; 
	
	if(!empty($slide_template_id)){
		$slide=$slide_value;	
		$key = 'r'.wp_rand(0000000001,9999999999);
	}else{
		$slide=$slide_value;	
		$key = !empty($_POST['key'])&& empty($none_ajax)? sanitize_key(wp_unslash($_POST['key'])): $slide_key;
	}
		
	echo '<li id="vs_module_slide_'.esc_attr($key).'" class="vs_module_slide_item"  data-key="'.esc_attr($key).'" data-value="slide" data-count="'.esc_attr($count).'">';
 		
		vs_slide_top();
 		
		vs_module_slide_inner(true,$slide);

	echo '</li> ';
	if(empty($none_ajax)){
		die(0);
	}
 
}
}
if ( !function_exists ( "vs_module_slide_inner" )){
 add_action('wp_ajax_vs_module_slide_inner', 'vs_module_slide_inner');
function vs_module_slide_inner($none_ajax='',$slide_value=false){
	if(empty($none_ajax)){
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
		return;
		$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
		 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
			 return;
		}
	}
 	if(!empty($_POST['option'])&& empty($none_ajax)){
  		$data_option = vs_options_encode(wp_kses(wp_unslash($_POST['option']),vs_kses()));
 	}else{
  		$data_option = !empty($slide_value['option'])?$slide_value['option']:'';
 	}
	
	if(!empty($_POST['layer']) && empty($none_ajax)){
  		$data_layer = vs_options_encode(wp_kses(wp_unslash($_POST['layer']),vs_kses()));
 	}else{
  		$data_layer = !empty($slide_value['layer'])?$slide_value['layer']:'';
 	}	
	
	$slide_array=array();
	$vs_slide_options = array(apply_filters('vs_slide_options',$slide_array));
	if(!empty($_POST['default']) && empty($none_ajax)){
		
		$value_default=array();
 		foreach ($vs_slide_options as $slide_options):
 			if(!empty($slide_options)):
			foreach ($slide_options as $option ):
	 			if($option['id'] =='title'){
					$option['default'] =!empty($_POST['default_title']) && empty($none_ajax) ?  sanitize_text_field(wp_unslash($_POST['default_title'])):'';
				}
				if(!empty($option['default']) && !empty($option['id'])){
					$value_default[$option['id']] = $option['default'];
				}
			endforeach;
			endif;	
			
		endforeach;
		
 		$data_option =!empty($value_default)? vs_options_encode(json_encode($value_default,JSON_UNESCAPED_UNICODE )) :'';
 	}else{
 		$data_option =$data_option;
	}
 	 
 	echo '<div class="vs_module_slide_inner">';
	 

 	echo '<div class="vs_module_slide_option">'.esc_html($data_option).'</div>';
 	echo '<div class="vs_module_slide_layer">'.esc_html($data_layer).'</div>';
 	$value_option = vs_options_decode(urldecode(esc_html($data_option)));	
	
	
 	$background_color =vs_slider_var_gradient_background_color('sl',$value_option,'background_color');
 	$background_image =vs_slider_background_image('sl',$value_option,'background_image');
 	 
 	echo '<div class="vs_module_slide_cover" style="'.esc_attr($background_image.''.$background_color).'"></div>';
	
 		$option_title =!empty($value_option['title'])?$value_option['title']:'';
 	
	echo '<span class="vs_module_slide_title">'.esc_html($option_title).'</span>';
	 
 	echo '</div>';
	if(empty($none_ajax)){
	 die();
	}
	
 }
}
if ( !function_exists ( "vs_slide_top" )){
 function vs_slide_top() {
	
 	
	echo '<div class="vs_module_slide_top">';
        echo '<a class="vs_module_slide_options"></a>';
        echo '<a class="vs_module_slide_duplicate"></a>';  
		echo '<a class="vs_module_slide_template_save vs_module_template_save" data-id="slide"></a>';
		echo '<a class="vs_module_slide_remove"></a>';
 	echo '</div>';
   
}
}
 
 
 if ( !function_exists ( "vs_module_panel_options" )){
add_action('wp_ajax_vs_module_panel_options', 'vs_module_panel_options');
function vs_module_panel_options(){
	
if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
  	 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
 		 return;
	}
 
 
	$key = !empty($_POST['key'])?sanitize_key(wp_unslash($_POST['key'])):'';
		
	echo '<div id="vs_options_'.esc_attr($key).'" class="vs_panel vs_panel_desktop" data-key="'.esc_attr($key).'">';
	echo '<div class="vs_panel_middle">';
 		//Title
		echo '<div class="vs_panel_title"><h3>'.esc_html__('Slide Options','visual-slider').'</h3>';
   			echo '<div class="vs_panel_cancel">'.esc_html__('Cancel','visual-slider').'</div>';
			echo '<div class="vs_panel_save">'.esc_html__('Save changes','visual-slider').'</div>';
 		echo '</div>';
 		//Content
	echo '<div class="vs_panel_scroll">';
	echo '<div class="vs_panel_content">';
 
 		echo '<div class="vs_module_add_warp">';
			echo '<a class="vs_module_slide_options_show vs_module_button">'.esc_html__('Slide Options','visual-slider').'</a>';
			echo '<a class="vs_module_layer_add vs_module_button">'.esc_html__('Add Layer','visual-slider').'</a>';
			echo '<a class="vs_module_all_layer_template_save vs_module_button vs_module_template_save"  data-id="all_layer">'.esc_html__('Save All Layer in Library','visual-slider').'</a>';
			echo '<a class="vs_module_layer_template_add vs_module_button">'.esc_html__('Add Layer From Library','visual-slider').'</a>';
		echo '</div>';

	 vs_module_slide_options();
 
 	 vs_icon_fonts();
 	 vs_module_layer();
	 vs_perview_slide_warp();
 
	echo '</div>';
	echo '</div>';
	echo '</div>';
 	echo '</div>';

	 die();
}
 }

 if ( !function_exists ( "vs_module_slide_tabs" )){
 function vs_module_slide_tabs(){
	$setting=array();
	$vs_setting_options = array(apply_filters('vs_slide_options',$setting));
  	$tab= array();
	
	foreach ($vs_setting_options as  $setting) :
		foreach ($setting as $option ) :
			if(!empty($option['group'])){
				$dass = sanitize_title($option['group']);
				if(!array_key_exists($dass,$tab)){
					$tab[sanitize_title($option['group'])] = $option['group'];
				}
			}else{ 	
				$general = esc_html__('General','visual-slider');
				$tab[sanitize_title($general)] = $general;
			}
		endforeach;
	endforeach;
	
	return  $tab;

}
 }

  if ( !function_exists ( "vs_module_slide_options" )){
function vs_module_slide_options(){
	
 
 	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
  	 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
 		 return;
	}
 
	$value_id = !empty($_POST['option']) ? vs_options_decode(urldecode(wp_kses(wp_unslash($_POST['option']),vs_kses()))):'';
		 
		$slide_array=array();
		 $vs_slide_options = array(apply_filters('vs_slide_options',$slide_array));
 			echo '<form id="vs_module_slide_options" class="vs_options  vs_side_options vs_active">';
			echo '<div class="vs_module_side_options_title"><span>'.esc_html__('Layer Options','visual-slider').'</span><i class="vs_module_side_options_close"></i>';
			echo '</div>';
		echo '<div class="vs_side_options_content">';
  				foreach ($vs_slide_options as  $slide_options):
					echo'<div class="vs_title_tabs">';
						$array_tab = vs_module_slide_tabs();
						$count_tab=0;
							
						foreach ($array_tab as  $key=> $tabs) :
								$count_tab++;
							if($count_tab==1){
								$tab_active = 'vs_layout_active';
							}else{
								$tab_active = '';
							}
							echo'<a class="vs_tab_'.esc_attr($key).' '.esc_attr($tab_active).'" data-id="'.esc_attr($key).'">'.esc_html($tabs).'</a>';
						endforeach;		
												
					echo'</div>';
				endforeach;  
	  
			
			
				echo '<div class="vs_options_content">';						
						
				foreach ($vs_slide_options as $slide_options):
		
					$array_tab = vs_module_slide_tabs();
					$count_container=0;
					foreach ($array_tab as  $key=> $tabs):
	 
						$count_container++;
						if($count_container==1){
							$group_active = 'vs_layout_group_active';
						}else{
							$group_active = '';
						}  
								
						echo '<section class="vs_options_warp '.esc_attr($group_active).' " data-tab="'.esc_attr($key).'">';
							foreach ($slide_options as $option ):
								$general = !empty($option['group']) ? sanitize_title($option['group']):sanitize_title(esc_html__('General','visual-slider'));
								if($key == $general ){
									if(!empty($option['name']) && !empty($option['id'])  && !empty($option['type'])){
										$data_options = !empty( $option['options'] ) ?  $option['options']  : null;
										$desc = !empty( $option['desc'] ) ?  $option['desc']  : null;
										$placeholder = !empty( $option['placeholder'] ) ?  $option['placeholder']  : null;
										$fold = !empty( $option['fold'] ) ?  $option['fold']  : null;
										
										$unit = !empty( $option['unit'] ) ?  $option['unit']  : null;
										$min = !empty( $option['min'] ) ?  $option['min']  : null;
										$max = !empty( $option['max'] ) ?  $option['max']  : null;
										$step = !empty( $option['step'] ) ?  $option['step']  : null;
										$width = !empty( $option['width'] ) ?  $option['width']  : null;	
										$responsive = !empty( $option['responsive'] ) ?  $option['responsive']  : null;
										$warp_width = !empty( $option['warp_width'] ) ?  $option['warp_width']  : null;
										
										
										
										$value =isset($value_id[$option['id']])?$value_id[$option['id']]:'';
										vs_options_function($value, $option['name'], $option['id'],$option['type'],$data_options,$desc,$placeholder,$fold,'slide_'.$option['id'],$unit,'vs_slide',$min,$max,$step,$width,$responsive,$warp_width);
										 
										 

									}
									
									
									
									
								}
							endforeach; 							
						echo '</section>';
										
					endforeach; 
				endforeach; 
 				 
 		echo '</div>';
 		echo '</div>'; 			
			
	 			
	 
	echo '</form>';
}
}
