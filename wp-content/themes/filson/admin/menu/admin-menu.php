<?php
include_once hexwp_PATH . '/admin/menu/menu_array.php'; 
include_once hexwp_PATH . '/admin/menu/icon-picker.php'; 
include_once hexwp_PATH . '/admin/menu/menu-item-custom-fields.php'; 
  include_once hexwp_PATH . '/admin/menu/fonts/fa-icon.php'; 
  include_once hexwp_PATH . '/admin/menu/fonts/flaticon.php'; 
  include_once hexwp_PATH . '/admin/menu/fonts/flaticon_thin.php'; 

include_once hexwp_PATH . '/admin/menu/fonts/metrizeicon.php'; 
include_once hexwp_PATH . '/admin/menu/fonts/typcn.php'; 
add_action('admin_enqueue_scripts', 'hexwp_admin_menu_enqueue');
function hexwp_admin_menu_enqueue($hook) {
	global $pagenow; 
  	 $var='1.0';
	
			
  	wp_enqueue_style( 'hexwp_admin_menu',  hexwp_DIR.'/admin/menu/css/admin-menu.css',array(),$var);
  	wp_enqueue_style( 'hexwp_icon_picker',  hexwp_DIR.'/admin/menu/css/icon-picker.css',array(),$var);
	wp_register_script( 'hexwp_admin_menu', hexwp_DIR.'/admin/menu/js/admin-menu.js',array('jquery') ,$var);
 	wp_localize_script( 'hexwp_admin_menu', 'hexwp_admin_menu_js', array( 'ajaxurl' => admin_url( 'admin-ajax.php'  )));	
 	wp_enqueue_script( 'hexwp_admin_menu');
  	wp_enqueue_media();
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Widget Options Item
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_admin_menu_options($bass='',$hexwp_options='') { 
	global $post;
  
	foreach ($hexwp_options as  $option) {
		hexwp_admin_menu_options_item($bass,$option);
	}; 
	
}
function hexwp_admin_menu_options_item( $bass=false,$array=false) { 
	
	
 	
 	$label = !empty($array['name'])?$array['name']:'';
	$id = !empty($array['id'])? $array['id']:'';
	$type = !empty($array['type'])? $array['type']:'';
	$options = !empty($array['options'])?$array['options']:'';
	$depth = !empty($array['depth'])?$array['depth']:'';
 	$name=$id.'['.$bass.']';
	$data=$id.'-'.$bass.'';
  
  
  	$value_id = get_post_meta( $bass, $id, true );

   	if ( 1 == $value_id ){
	$checked= 'checked="checked"'; 
	} else{
		$checked='';
	}
 
  
   
	echo '<p class="field-custom  menu-item-'.esc_attr($depth).' menu-type-'.esc_attr($type).' menu-id-'.esc_attr($id).'   menu-description description description-wide" data-id="'.$bass.'">';
	if(!empty($label)){	
		echo '<label for="'.esc_attr($data).'">'. esc_html($label).'</label>  ';
	}

	switch( $type ) {
	// Title
	case 'text':
		echo '<input type="text" name="'.esc_attr($name).'" id="'.esc_attr($data).'" class="widefat"   style="width:100%;"  value="'.$value_id.'">';
  	break;
	
	case 'not_depth':
		echo __('You can not use this depth' , 'hexwp');
  	break;

		
	case 'textarea':
		echo '<textarea name="'.esc_attr($name).'" id="'.esc_attr($data).'" >'.esc_textarea($value_id).'</textarea>';
 	break;
		
	case 'number':
		echo '<input type="number"  name="'.esc_attr($name).'" id="'.esc_attr($data).'" style="width: 100px;" value="'.esc_attr($value_id).'" > ';
	break;
	case 'checkbox':
 		echo '<input type="checkbox"  name="'.esc_attr($name).'" id="'.esc_attr($data).'" '.wp_kses_post($checked).'   value="1">';
  	break;
	
 
	// Categories
	case 'select': 
 
		echo '<select name="'.esc_attr($name).'" id="'.esc_attr($data).'" >';
 			foreach ($options as  $keys => $text) { 	
 				echo'<option id="hw_'.esc_attr($id).'_'.esc_attr($keys).'" class="select_'.esc_attr($type).'" value="'.esc_attr($keys).'"'.selected( $value_id, $keys).'>'.esc_html($text).'</option>'; 
			}
		echo '</select>';
	break;	 
 
 
 	// Categories
	case 'chosen': 
 
		echo '<select class="hw-chosen" name="'.esc_attr($name).'" id="'.esc_attr($data).'" >';
 			foreach ($options as  $keys => $text) { 	
 				echo'<option id="hw_'.esc_attr($id).'_'.esc_attr($keys).'" class="select_'.esc_attr($type).'" value="'.esc_attr($keys).'"'.selected( $value_id, $keys).'>'.esc_html($text).'</option>'; 
			}
		echo '</select>';
	break;	 
 
 	// Categories
	case 'image': 
 
		echo'<a class="hexwp_menu_add_image button button-small"  data-uploader-title="'.esc_attr__('Choose Image','hexwp').'" data-uploader-button-text="'.esc_attr__('Use This Image','hexwp').'"  data-remove-text="'.__('Remove','hexwp').'" data-name="'.esc_attr($data).'"  >'.esc_html__('Upload','hexwp').'</a>';
				echo '<input  type="hidden" name="'.esc_attr($name).'" id="'.esc_attr($data).'" value="'.esc_attr($value_id).'">';
			if(!empty($value_id)){
  				$image_medium = wp_get_attachment_image_src($value_id, 'medium');
	
			 
				if(!empty($image_medium[0])){ 
					echo '<a class="hexwp_remove_image  button button-small"  >'.__('Remove','hexwp').'</a>';
					echo '<img src="'.esc_url($image_medium[0]).'"/>';
				}
 			}
	 
		
		
		
	break;	 
case 'icon': 
 	 		echo '<a  class="hexwp_builder_choose_icon button button-small" >'.__('Choose Icon:','hexwp').'</a>';
			echo '<input name="'.esc_attr($name).'" id="'.esc_attr($data).'"    type="hidden" value="'.esc_attr($value_id).'">'; 
			if(!empty($value_id)){ 
			 	 	 hexwp_icon_fontfamily($value_id);
  				echo '<i class="fa hexwp-menu-icon '.esc_attr($value_id).'"><a  class="hexwp_builder_remove_icon  " ></a></i>'; 
		 } 
 		
	break;	 
	case 'radio_image': 
 
  
		echo '<ul class="hexwp_options_radio_image hexwp_radio_selected">';
		foreach ($options as  $radio_key => $radio_link) :  
 			if( $value_id == $radio_key){$checked= 'checked="checked"'; } else{$checked='';}
				 
			echo '<li>';
				echo '<label>';
					echo '<input type="radio"   name="'.esc_attr($name).'" id="hexwp_label_'.esc_attr($data).'" '.wp_kses_post($checked).'  value="'.esc_attr($radio_key).'">';
					echo '<img for="hexwp_label_'.esc_attr($data).'" src="'. esc_url($radio_link).'"/>';
				echo '</label>';
			echo '</li>';
		
 		endforeach;
 		echo '</ul>';
		
		
		
	break;	 
	
	
 	}
 	echo '</p>';
}

function hexwp_admin_options_save($menu_item_db_id=false,$hexwp_options=false) { 
 	if(!empty($hexwp_options)){
	foreach ($hexwp_options as  $option) {
 
 			 if ( isset( $_POST[$option][ $menu_item_db_id ] ) ) {
				update_post_meta( $menu_item_db_id, $option, $_POST[$option][ $menu_item_db_id]  );
			}else {
				delete_post_meta( $menu_item_db_id,$option );
			}
		 
   	}
	}
	
 
}