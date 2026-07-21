<?php
// Prevent direct access
defined('ABSPATH') || exit;
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 																	Column Builder  
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
function vh_builder_column($get_option=false,$column_key=false,$column_value=false){
  	global $post,$vh_options_column;
	
  	 
  
    $element_json = !empty($get_option['element'])?urldecode($get_option['element']):'';
  	$element= vh_json_array_row($element_json);
	 
 	$column_id =!empty($column_value['id'])?$column_value['id']:'';
 	$column_child =!empty($column_value['child'])?$column_value['child']:'';
 
   
   	 
    
	echo '<li class="vh_column_item"   data-key="'.esc_attr($column_key).'" data-id="'.esc_attr($column_id).'"  data-child="'.esc_attr($column_child).'">';
		//vh_column_value($key,$value); 
		echo '<div class="vh_column_content">';
		
			echo '<ul class="vh_element_list">';
				
				if (!empty($element)) :
				foreach($element as $element_key => $element_value) :
					
					if($element_value['childern'] == $column_key){  
						vh_builder_element($element_key,$element_value);
					}
				endforeach;
				endif;  
							   
			echo '</ul>';
				
 				echo '<a class="vh_add_element"></a>';
 				
		echo '</div>';
	echo '</li> ';
    
          

}  