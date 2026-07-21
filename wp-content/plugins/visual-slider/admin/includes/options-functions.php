<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/********************************************************************
sor Options Funtions
*********************************************************************/
if( ! function_exists( 'vs_options_function' ) ) {


function vs_options_function($value,$label,$id,$type, $options = false,$description = false,$placeholder = false,$fold=false,$class=false,$unit=false,$id_key=false,$min=false,$max=false,$step=1,$width=false,$responsive=false,$warp_width=false) { 
 
 	$value_id = isset( $value ) ? $value : '';
 	
	$data=$id;			 
	$responsive_class= !empty($responsive) ? ' vs_responsive_options_'.$responsive.' ':'  ';
		
  		
	if( $type=='multi_options'    ){
		$options_width='style="width:'.$width.' !important;"';
	}else{
		$options_width='';	
	}
	
	if(isset($warp_width)){
		$warp_width='style="width:'.$width.' !important;"';
	}else{
		$warp_width='';	
	}
			
  	if($type =='unit'  ){}
	
	elseif( $type == 'heading'  ) {
		echo'<li class="vs_options_item vs_options_'.esc_attr($type).' vs_id_'.esc_attr($id).' vs_live_'.esc_attr($class).' '.esc_attr($responsive_class).'"   data-active="show" >';
 		echo '<span >'. esc_html($label).'</span>';
		echo '</li>';
	} else{
		
		
 		$number_slider = !empty($max) ? 'vs_number_slider' :'';
  		 echo'<li class="vs_options_item  '.esc_attr($number_slider.' '.$responsive_class).' vs_options_'.esc_attr($type).' vs_id_'.esc_attr($id).' vs_live_'.esc_attr($class).'"   data-active="show" >';
		 	
			 
			if(!empty($fold)){
				echo '<div class="vs_options_fold">';
 				foreach($fold as $key => $value) : 
					echo '<div class="vs_options_fold_item" data-name="'.esc_attr($id_key.$value).'" data-value="'.esc_attr($key).'"></div>';
 				endforeach;
				echo '</div>';
			}
		if( $type != 'title' || $type !== 'radio'){
				echo '<div class="vs_options_name">';
				echo '<label for="vs_label_'.esc_attr($data).'" >'. esc_html($label).'</label>';
				echo '<div class="vs_options_description">'.esc_html($description).'</div>';
				echo '</div>';
		}
		 
	 
		if( $type == 'tabs' ) {
			echo '<a class="vs_add_tab button" data-id="'.esc_attr($data).'" data-option="'.esc_attr(urlencode(serialize($options))).'" >'.esc_html__('Add Tab','visual-slider');
  			echo '</a>';
			 
		}	
		if( $type == 'list' ) {
			echo '<a class="vs_add_list button" data-id="'.esc_attr($data).'" data-option="'.esc_attr(urlencode(serialize($options))).'" >'.esc_html__('Add Item','visual-slider');
  			echo '</a>';;
		}			
		echo '<div class="vs_options_setting" '.wp_kses($options_width.' '.$warp_width,vs_kses()).'>';
  	}
	
	 
	if( $type == 'start'){
		echo '<ul class="vs_options_start" >';
	}

	switch( $type ) {
		
	// Text
	case 'text':
			if( !empty($width)){
				$width='style="width:'.$width.' !important;"';
			}else{
				$width='';	
			}
 		echo '<input type="text"  autocomplete="off"  class="vs_form_text" name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" placeholder="'.esc_attr($placeholder).'"   '.wp_kses($width,vs_kses()).' value="'.esc_html($value_id).'">';
 	break;
	
	//Number 	
	case 'number':
	$number_step=isset($step)?$step :'0';
	$data_min =isset($min)?$min:'0';
	
	if( isset($width)){
				$width='style="width:'.$width.' !important;"';
			}else{
				$width='';	
			}	
		if(isset($max)){
 			echo ' <input type="range" class="vs_form_range" autocomplete="off" min="'.esc_attr($data_min).'" max="'.esc_attr($max).'" value="'.esc_attr($value_id).'" step="'.esc_attr($number_step).'" >';
			$data_max=$max;
		}else{
			$data_max='99999999';
		}
 		$vs_bad =vs_check_nubmer($value_id,$data_max,$data_min);

 		echo '<input type="number"   name="'.esc_attr($data).'"  class="vs_form_number '.esc_attr($vs_bad).'" min="'.esc_attr($data_min).'"   min="'.esc_attr($step).'"  max="'.esc_attr($data_max).'" id="vs_label_'.esc_attr($data).'" '.wp_kses($width,vs_kses()).'  placeholder="'.esc_attr($placeholder).'"   value="'.esc_attr($value_id).'" ><span>'.esc_html($unit).'</span>';
	break;
 		
	//Color		
	case 'color':	
 		echo '<input    class="vs_form_color" autocomplete="off"  data-rgba="false" type="text"    name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" value="'.esc_attr($value_id).'">';
  	break;
	
	//Color RGBA
	case 'color_rgba':
		echo '<input    class="vs_form_color" autocomplete="off" data-rgba="true" type="text"   name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" value="'.esc_attr($value_id).'">';
 	break;
		
	//Textarea	
	case 'textarea':
		echo '<textarea autocomplete="off" class="vs_form_textarea" name="'.esc_attr($data).'"  id="vs_label_'.esc_attr($data).'"   placeholder="'.esc_attr($placeholder).'" >'.esc_textarea($value_id).'</textarea>';
 	break;
	
	
	//Select
	case 'select': 
 		echo '<select autocomplete="off"  name="'.esc_attr($data).'"  class="vs_form_select" placeholder="'.esc_attr($placeholder).'" id="vs_label_'.esc_attr($data).'" >';
 			foreach ($options as  $select_key => $select_text) { 	
 				echo'<option  value="'.esc_attr($select_key).'"'.selected( $value_id, $select_key).'>'.esc_html($select_text).'</option>'; 
			}
		echo '</select>';
	break;	
	
	//Checkbox	
	case 'checkbox':
 	
		if ( 1 == $value_id ){$checked= 'checked="checked"'; $checked_false='';} else{ $checked='';$checked_false= 'checked="checked"';  }
  
		echo '<div class="vs_checkbox vs_checkbox_primary  ">';
 			echo '<div class="vs_label_checkbox_on">';
  			echo '<input autocomplete="off" type="radio"  id="vs_label_'.esc_attr($data).'_on" name="'.esc_attr($data).'" '.wp_kses($checked,vs_kses()).'   value="1">';
 		 	echo '<label for="vs_label_'.esc_attr($data).'_on">'.esc_html__("Enable","visual-slider").'</label>';
			echo '</div>';
			
				echo '<div class="vs_label_checkbox_off">';
  			echo '<input autocomplete="off" type="radio"  id="vs_label_'.esc_attr($data).'_off" name="'.esc_attr($data).'"   '.wp_kses($checked_false,vs_kses()).'  value="">';
 		 	echo '<label for="vs_label_'.esc_attr($data).'_off">'.esc_html__("Disable","visual-slider").'</label>';
			echo '</div>';
  		echo '</div>';
	break;
  	case 'radio': 
		echo '<ul class="vs_radio_warp">';
		$radio_count=0;
		foreach ($options as  $radio_key => $radio_text) :  
			$radio_count++;
			
 			if(!empty($value_id)){
 				if ( $value_id == $radio_key){$checked= 'checked="checked"'; } else { $checked='';}
			}else{
				if($radio_count ==1){$checked= 'checked="checked"';}else { $checked='';}
			}
 			echo '<li class="vs_radio_item">';
				echo '<input autocomplete="off" type="radio" name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data.'_'.$radio_key).'"  '.wp_kses($checked,vs_kses()).'  value="'.esc_attr($radio_key).'">';
				echo '<label for="vs_label_'.esc_attr($data.'_'.$radio_key).'">'. esc_html($radio_text).'</label>';
			echo '</li>';
		
 		endforeach;
		$radio_count=0;
		echo '</ul>';
 	
	break;		
	// Radio
	case 'hover': 
		echo '<ul class="vs_hover_warp">';
		$count_radio=0;
		foreach ($options as  $radio_key => $radio_text) : 
			$count_radio++;
			 
		
			if( $count_radio == 1){$checked= 'checked="checked"'; $class="vs_hover_checked"; } else{$checked='';$class="";}
				 
			echo '<li class="vs_hover_item">';
				echo '<label class="'.esc_attr($class).'">';
					echo '<input type="radio"  autocomplete="off" name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" '.wp_kses($checked,vs_kses()).'  value="'.esc_attr($radio_key).'">';
					echo '<span for="vs_label_'.esc_attr($data).'">'. esc_html($radio_text).'</span>';
				echo '</label>';
			echo '</li>';
		
 		endforeach;
		echo '</ul>';

	break;	
	
	// Radio Image
	case 'radio_image': 
		echo '<ul class="vs_radio_image_warp">';
 		foreach ($options as  $radio_key => $radio_link) :  
		
			if( $value_id == $radio_key){$checked= 'checked="checked"'; } else{$checked='';}
				 
			echo '<li class="vs_radio_image_item">';
				echo '<label>';
					echo '<input type="radio" autocomplete="off"  name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" '.wp_kses($checked,vs_kses()).'  value="'.esc_attr($radio_key).'">';
					echo '<'.esc_attr('img').' for="vs_label_'.esc_attr($data).'" src="'. esc_url($radio_link).'"/>';
				echo '</label>';
			echo '</li>';
		
 		endforeach;
		echo '</ul>';
	break;		 
	// Multi Options
	case 'multi_options':
 		echo '<ul class="vs_multi_options">'; 
 			vs_options_function_multi_item($data,$value_id,$options,'',$class);
  		echo '</ul>';
	break;	
	
	// Multi Options
 
		 
		  
	// Editor
	case 'editor': 
 		echo '<div class="vs-editor-hander" data-id="vs_'.esc_attr($data).'">';
		
 			$settings = array( 'textarea_name' => $data,'textarea_rows' => 5 );

 			wp_editor(  $value_id , 'vs_'.$data ,$settings);
   	
		echo '</div>';
  	break;	
	  


	// icon Image
	case 'icon': 
		$icon_rand = wp_rand(10000000, 99999999);
 		echo'<div  class="vs_menu_icon" id-icon="'.esc_attr($icon_rand).'" >';
 
			echo'<a  class="vs_builder_choose_icon button button-small" >'.esc_html__('Choose Icon','visual-slider').'</a>';
			echo '<input name="'.esc_attr($data).'" autocomplete="off" id="'.esc_attr($data).'"    type="hidden" value="'.esc_attr($value).'">';
			if(!empty($value_id)){  
				echo '<i class="fa vs-menu-icon '.esc_attr($value_id).'"><a  class="vs_builder_remove_icon  " ></a></i>';
			}
  		echo'</div>'; 
 
	break;
	// Image
	case 'image': 
	  	echo'<a class="vs_image_upload button button-small"  data-name="'.esc_attr($data).'"  >'.esc_html__('Upload Image','visual-slider').'</a>';
		echo '<input  type="text" autocomplete="off" class="vs_attachment_'.esc_attr($data).' vs_form_text" name="'.esc_attr($data).'" '.wp_kses($width,vs_kses()).'	value="'.esc_html($value_id).'">';
		
		 
		
		if(!empty($value_id)){
 			echo '<div class="vs_image_item">';
  
		 
		if(!empty($value_id)){ 
		if(is_numeric($value_id)){
			$attachment_image = wp_get_attachment_image_src($value_id,'full');
			$value_url=!empty($attachment_image[0])?$attachment_image[0]:'';
		}else{
			$value_url=$value_id;
		}
  
   	  		echo '<a class="vs_image_remove"  ></a>';
 			echo '<'.esc_attr('img').' src="'.esc_url($value_url).'"/>';
 		}
 			echo '</div>';
		}
	 
	 
	break;

 
	
	//list
	 
   
	}
  
 	 
 	if ( $type == 'end'  ){
			echo'</ul>';				 
	}  
  	elseif (  $type =='unit' || $type =='heading' ){}
	 
 	 else{
		echo '</div>';
		echo '</li>';
	}




}
}
if( ! function_exists( 'vs_options_function_multi_item' ) ) {
	function vs_options_function_multi_item($id =false,$value =false,$options =false,$fold=false,$class=false) {
 		if(isset($options)){
		foreach ($options as  $option_value) { 	
		$option_type = isset( $option_value['type'] ) ?  $option_value['type']  : '';
				$option_id =isset($option_value['id'])?$option_value['id']:'';
				$value_content = isset($value[$option_id])?$value[$option_id]:'';
				$data  = $id.'['.$option_id.']';
				$placeholder = isset( $option_value['placeholder'] ) ?  $option_value['placeholder']  : null;
								$min = isset( $option_value['min'] ) ?  $option_value['min']  : null;
				$max = isset( $option_value['max'] ) ?  $option_value['max']  : null;
				$unit = isset( $option_value['unit'] ) ?  $option_value['unit']  : null;
				$width = isset( $option_value['width'] ) ?  $option_value['width']  : null;
				$warp_width = isset( $option_value['warp_width'] ) ?  $option_value['warp_width']  : null;
 			
			
			if(isset($warp_width)){
				$warp_width='style="width:'.$warp_width.' !important;"';
			}else{
				$warp_width='';	
			}
			
			
			if(isset($width)){
				$width='style="width:'.$width.' !important;"';
			}else{
				$width='';	
			}
			
	 	
			
			$name_bottom_class= isset($option_value['name_bottom'])? ' vs_name_bottom ':'';
	
 	
				
			echo '<li class="vs_multi_options_item  vs_options_'.esc_attr($option_type.' '.$name_bottom_class.' vs_live_'.$class.'_'.$option_value['id']).'" '.wp_kses($warp_width,vs_kses()).' >';
						
					if(isset($option_value['name'])){
						echo '<label for="vs_label_'.esc_attr($data).'" >'. esc_html($option_value['name']).'</label>';
					}
				 
				 
				if(isset($option_value['fold'])){
					echo '<div class="vs_options_fold">';
  					foreach($option_value['fold'] as $fold_key => $fold_value) : 
						echo '<div class="vs_options_fold_item" data-name="'.esc_attr($fold_value).'" data-value="'.esc_attr($fold_key).'"></div>';
 					endforeach;
					echo '</div>';
				}	
				
				switch( $option_type ) {
					// Text
					case 'text':
							echo '<input  autocomplete="off" type="text" placeholder="'.esc_attr($placeholder).'" style="width:100px"  name="'.esc_attr($data).'"   value="'.esc_html($value_content).'">';
					break;
						 
					//Number 	
					case 'number':
						$data_min =isset($min)?$min:'0';
						$vs_bad='';
						
						if(isset($max)){
							$data_max=$max;
						}else{
							$data_max='99999999';
						}
 
						$vs_bad =vs_check_nubmer($value_content,$data_max,$data_min);
						
  						echo '<input autocomplete="off" type="number" class="vs_form_number '.esc_attr($vs_bad).'"   min="'.esc_attr($data_min).'" max="'.esc_attr($data_max).'"  placeholder="'.esc_html($placeholder).'" '.wp_kses($width,vs_kses()).'  name="'.esc_attr($data).'"  id="vs_label_'.esc_attr($data).'"  value="'.esc_attr($value_content).'" >';
						if(isset($unit)){
							echo'<span>'.esc_html($unit).'</span>';
						};
					break;
						 
					//Select 	
					case 'select': 
					 
						echo '<select autocomplete="off"  class="vs_form_select" name="'.esc_attr($data).'"  '.wp_kses($width,vs_kses()).'   id="vs_tabs_'.esc_attr($data).'" >';
							if(isset($option_value['options'])){
							foreach ($option_value['options'] as  $select_key => $select_text) { 	
									echo'<option  value="'.esc_attr($select_key).'"'.selected( $value_content, $select_key).'>'.esc_html($select_text).'</option>'; 
							}
							}
						echo '</select>';
					break;
					
					//Color	
					case 'color':
						echo '<input autocomplete="off"    class="vs_form_color"   data-rgba="false" type="text"   name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" value="'.esc_attr($value_content).'">';
					break;
						
					//Color RGBA
					case 'color_rgba':
						echo '<input  autocomplete="off"  class="vs_form_color"  data-rgba="true" type="text"    name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" value="'.esc_attr($value_content).'">';
					break;
	// Radio Image
	case 'radio_image': 
 	
		echo '<ul class="vs_radio_image_warp">';
				
			$radio_count=0;
				foreach ($option_value['options'] as  $radio_key => $radio_link) :  
					$radio_count++;
					
					if(isset($value_content)){
						if ( $value_content == $radio_key){$checked= 'checked="checked"'; } else { $checked='';}
					}else{
						if($radio_count ==1){$checked= 'checked="checked"';}else { $checked='';}
					}
				 
						 
					echo '<li class="vs_radio_image_item" '.wp_kses($width,vs_kses()).' >';
						echo '<label>';
							echo '<input type="radio" autocomplete="off"  name="'.esc_attr($data).'" id="vs_label_'.esc_attr($data).'" '.wp_kses($checked,vs_kses()).'  value="'.esc_attr($radio_key).'">';
							echo '<'.esc_attr('img').' for="vs_label_'.esc_attr($data).'" src="'. esc_url($radio_link).'"/>';
						echo '</label>';
					echo '</li>';
				
				endforeach;
			$radio_count=0;
		echo '</ul>';

 			
			break;		 
					// Checkbox  
					case 'checkbox':
					
 						if ( 1 == $value_content ){$checked= 'checked="checked"'; } else{$checked='';}
						echo '<div class="vs_checkbox vs_checkbox_primary">';
							echo '<input autocomplete="off" type="checkbox" name="'.esc_attr($data).'"  id="vs_label_'.esc_attr($data).'" '.wp_kses($checked,vs_kses()).'   value="'.esc_attr($value_content).'">';
							echo '<label for="vs_label_'.esc_attr($data).'" ></label>';
						echo '</div>';
						
					break;	 
				}
	 if(isset($option_value['name_bottom'])){
						echo '<label for="vs_label_'.esc_attr($data).'"      >'. esc_html($option_value['name_bottom']).'</label>';
					}
				 
			echo '</li>';
 				
		}
		}
	 
}
 
  
if ( !function_exists ( "vs_icon_element" )){
  
add_action('init','vs_icon_element');
 function vs_icon_element() { 
 
	global $vs_icon_element;
	
	$element=array();
 
		if(has_filter('vs_icon_element')) {
			$vs_icon_element = apply_filters('vs_icon_element', $element);
		}
  				 
 
}
}
 if ( !function_exists ( "vs_icon_picker" )){
add_action('wp_ajax_vs_icon_picker', 'vs_icon_picker');
 function vs_icon_picker() {
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
		return;
		$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
		 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
			 return;
		}
	 
		global $vs_icon_element;
 		$vs_icons = array();
		$vs_icons =  $vs_icon_element; 
	 
 
		$id=!empty($_POST['id'])?sanitize_key(wp_unslash($_POST['id'])):'';
		echo '<div class="vs_icon" data-id="'.esc_attr($id).'">';
						echo '<div class="vs_icon_middle">';
							echo '<div class="vs_icon_title">'.esc_html__('Choose Icon','visual-slider').'<i class="vs_icon_close"></i><div class="vs_icon_search"><input id="search-input" class="search-icon-control" placeholder="'.esc_html__('Search Icons','visual-slider').'" autocomplete="off" spellcheck="false" autocorrect="off" tabindex="1"></div>';
							
  								$count_tab = 0;
								echo'<div class="vs_title_tabs">';

								foreach ($vs_icons as  $icon_key => $icon_value):
									$count_tab++;
  												if($count_tab==1){
													$tab_active = 'vs_layout_active';
												}else{
													$tab_active = '';
												}
												
											echo'<a class="vs_tab_'.esc_attr($icon_key).' '.esc_attr($tab_active).'" data-id="'.esc_attr($icon_key).'">'.esc_html($icon_key).'</a>';
								endforeach;  
								echo'</div>';


 							
							echo '</div>';
  							echo '<ul class="vs_icon_content">';
							  $count_active=0;
							 	if(!empty($vs_icons)){
								foreach ($vs_icons as $font => $font_title) {
									$count_active++;
  												if($count_active==1){
													$warp_active = 'vs_layout_group_active';
												}else{
													$warp_active = '';
												}
									
									echo '<section class="vs_icon_warp '.esc_attr($warp_active).'" data-tab="'.esc_attr($font).'">';
									
 									
										foreach ($font_title as $value_head => $font_child) {
 											echo  '<h2 class="vs_icon_head" >'.esc_attr($value_head).'</h2>';
										
 											foreach ($font_child as $key => $value ) {
												echo  '<li class="vs_icon_item" data-icon="'.esc_attr($key).'">';
													echo'<i class="'.esc_attr($key).'"></i>';
													echo'<span>'.esc_attr($value).'</span>';
												echo   '</li>';	
											}
										  
										}
									echo '</section>';							

								}
								} 
						 					
							echo'</ul>';
							echo '<div class="vs_icon_bottom"><a class="vs_set_icon button-primary">'.esc_html__('Set Icon','visual-slider').'</a></div>';	 
						echo'</div>';
			echo '</div>';
			  	
}
 }

if ( !function_exists ( "vs_check_nubmer" )){
function vs_check_nubmer($val=false,$max=false,$min=false) { 

   	 
  
	   $val_max=  intval($max);
	   $val_min=  intval($min);
	   
	       
	  if(!empty($val) ) {
 		   if(is_numeric($val)  ) {
			  $num_val = intval($val);
			   if( $num_val <= $val_max && $num_val >= $val_min ){
 			  	return '';
			   }else{
				return 'vs_bad';
			   }
		  }else{
				return 'vs_bad';
			}
	  }else{
		return '';
	  }
		 
 		
	 
 };
}
 
if ( !function_exists ( "vs_icon_element" )){
add_action('init','vs_icon_element');
 function vs_icon_element() { 
 
	global $vs_icon_element;
	
	$element=array();
 
		if(has_filter('vs_icon_element')) {
			$vs_icon_element = apply_filters('vs_icon_element', $element);
		}
  				 
 
}
} 
  
 
 if ( !function_exists ( "vs_icon_fonts" )){

add_action('wp_ajax_vs_icon_fonts', 'vs_icon_fonts');
 
function vs_icon_fonts() {
 	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash(filter_input( INPUT_POST, 'post_id',  FILTER_SANITIZE_NUMBER_INT ))):absint(get_the_ID()) ;
      if (!current_user_can('edit_post', $post_id)) return;
 
 	

echo'<div class="vs-icon-fons">';
	$var='';
	
	$array =array(
	'fontawesome',
	'flaticonarrow',
	'flaticonmultimedia',
	'flaticonbusiness',
	'flaticonoffice',
	'flaticoninterface',
	'flaticonessentialset',
	'flaticontechsupport',
	'flaticontech',
	'flaticonstrategy',
	'flaticonhipster',
	'flaticonfashion',
	'flaticonwebdesign',
	'flaticontravel',
	'flaticonnetwork',
	'metrizeicon',
	'typcn'
	);
		global $vs_fonticon_style;
	
 	foreach($array as $font){
 		$link='link';
 			$media='media="all"';
				echo  '<'.esc_attr($link).' rel="'.esc_attr('stylesheet').'" id="vs_'.esc_attr($font).'-css" href="' .esc_url(VISUALSLIDER_DIR."assets/css/fonts/".$font.".css?ver=".$var) . '" '.esc_attr('media').'="all" />';
 				 
	}
 	
 echo'</div>';
 
//die();			  	
}}
} 