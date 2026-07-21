<?php
// Prevent direct access
defined('ABSPATH') || exit;
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 																	  Element Builder
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_builder_element" )){
function vh_builder_element($element_key=false,$element_value=false){
  
	$element_id =!empty($element_value['id'])?$element_value['id']:'';
 	$childern =!empty($element_value['childern'])?$element_value['childern']:'';
 	$option =!empty($element_value['option'])?urldecode($element_value['option']):'';
 if(!vh_isValidJSON($option)){
		$option='';
	} 
	echo '<li class="vh_element_item " data-key="'.esc_attr($element_key).'"  data-id="'.esc_attr($element_id).'"  data-childern="'.esc_attr($childern).'" data-row="element">';
		echo '<vh_data_json class="vh_data_json"  data-row="element">'.esc_html($option).'</vh_data_json>';
   		 vh_element_title($element_id); 
	echo '</li>';
}
}
 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 																	  Element Value
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if ( !function_exists ( "vh_element_title" )){
function vh_element_title($element_id){

 		$vh_element_options= vh_element_options_array(); 
		$name =!empty($vh_element_options[$element_id]['name'])?$vh_element_options[$element_id]['name']:'';
 		 
		 
		 $data_name = is_rtl()?  __('Options','visual-header').' '.$name :  $name .' '.__('Options','visual-header');
		echo '<div class="vh_element_title"  data-name="'.esc_attr($data_name).'">';
		echo '<span class="vh_element_name" >'.esc_html($name).'</span>';
 		echo '<div class="vh_element_title_bottom">';
		echo '<a class="vh_element_options"></a>';
 		echo '<a class="vh_element_duplicate"></a>';
 		echo '<a class="vh_element_remove"></a>';
  		echo '</div>';	
 	echo '</div>';	
}
}
if ( !function_exists ( "vh_element_options_array" )){
function vh_element_options_array() { 
	global $vh_element_options;
	$element=array();
 
	if(has_filter('vh_element_options')) {
		$vh_element_options = apply_filters('vh_element_options', $element);
	}
  				 
 	return $vh_element_options;
}
}
