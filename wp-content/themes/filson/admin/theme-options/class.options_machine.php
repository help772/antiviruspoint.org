<?php 
/**
 * SMOF Options Machine Class
 *
 * @package     WordPress
 * @subpackage  SMOF
 * @since       1.0.0
 * @author      Syamil MJ
 */

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

														Options Machine

*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
class Options_Machine {
    public $Inputs;
    public $Menu;
    public $Defaults;
	/**
	 * PHP5 contructor
	 *
	 * @since 1.0.0
	 */
	function __construct($options) {
		
		$return = $this->optionsframework_machine($options);
		
		$this->Inputs = $return[0];
		$this->Menu = $return[1];
		$this->Defaults = $return[2];
		
	}

	/** 
	 * Sanitize option
	 *
	 * Sanitize & returns default values if don't exist
	 * 
	 * Notes:
	 	- For further uses, you can check for the $value['type'] and performs
	 	  more speficic sanitization on the option
	 	- The ultimate objective of this function is to prevent the "undefined index"
	 	  errors some authors are having due to malformed options array
	 */
	 // gamepress Edit
	static function sanitize_option( $value ) {
		$defaults = array(
			"name" 		=> "",
			"name2" 	=> "",
			"desc" 		=> "",
			"id" 		=> "",
			"std" 		=> "",
			"mod"		=> "",
			"type" 		=> ""
		);

		$value = wp_parse_args( $value, $defaults );

		return $value;

	}
	 // END gamepress Edit

	/**
	 * Process options data and build option fields
	 *
	 * @uses get_theme_mod()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function optionsframework_machine($options) {
		global $smof_output, $smof_details, $smof_data;
		if (empty($options))
			return;
		if (empty($smof_data))
			$smof_data = of_get_options();
		$data = $smof_data;

		$defaults = array();   
	    $counter = 0;
		$menu = '';
		$output = '';
		$update_data = false;

		do_action('optionsframework_machine_before', array(
				'options'	=> $options,
				'smof_data'	=> $smof_data,
			));
		if ($smof_output != "") {
			$output .= $smof_output;
			$smof_output = "";
		}
		
		

		foreach ($options as $value) {
 			// sanitize option
			if ($value['type'] != "heading")
				$value = self::sanitize_option($value);

			$counter++;
			$val = '';
			
			//create array of defaults		
			if ($value['type'] == 'multicheck'){
				if (is_array($value['std'])){
					foreach($value['std'] as $i=>$key){
						$defaults[$value['id']][$key] = true;
					}
				} else {
						$defaults[$value['id']][$value['std']] = true;
				}
			} else {
					$value_std= !empty($value['std']) ? $value['std'] :hexwp_option_default($value['id'],'',true);

				if (isset($value['id'])) $defaults[$value['id']] = $value_std;
			}
			
			/* condition start */
			if(!empty($smof_data) || !empty($data)){
			
				if (array_key_exists('id', $value) && !isset($smof_data[$value['id']])) {
					$value_std= !empty($value['std']) ? $value['std'] :'';
					$smof_data[$value['id']] = $value_std;
					if ($value['type'] == "checkbox" && $value_std == 0) {
						$smof_data[$value['id']] = 0;
					} else {
						$update_data = true;
					}
				}
				if (array_key_exists('id', $value) && !isset($smof_details[$value['id']])) {
					$smof_details[$value['id']] = $smof_data[$value['id']];
				}

 			
			 if( $value['type'] == 'accordion' && $value['position'] == 'start' ) {
			 	$output .= '<div class="accordion">';
			 }
			 
			 //End gamepress Edit
			//Start Heading
			if( $value['type'] == 'content' && $value['position'] == 'end' ) {
			 }elseif ( $value['type'] != "heading" ){
			 	$class = ''; if(isset( $value['class'] )) { $class = $value['class']; }
				
				//hide items in checkbox group
				$fold='';
				if (array_key_exists("fold",$value)) {
					if (isset($smof_data[$value['fold']]) && $smof_data[$value['fold']]) {
						$fold="f_".esc_attr($value['fold'])." ";
					} else {
						$fold="f_".esc_attr($value['fold'])." temphide ";
					}
				}
			 //gamepress Edit
 
	 		if( $value['type'] == 'accordion-content' ) {
				$output .= '<div id="section-'.esc_attr($value['id']).'" style="display:none;">'."\n";
			 }
			  else {
				$output .= '<div id="section-'.esc_attr($value['id']).'" class="'.esc_attr($fold).' section options_item section-'.esc_attr($value['type']).' '.esc_attr($class) .'" data-active="show" >'."\n";
			 }
			 		 
			 
			 
				if(!empty($value['fold_array'])){
					$output .= '<div class="options_fold">';
					foreach($value['fold_array'] as $fold_key => $fold_value) : 
						$output .=  '<div class="options_fold_item" data-name="'.esc_attr($fold_value).'" data-value="'.esc_attr($fold_key).'"></div>';
					endforeach;
					$output .= '</div>';
				}
				if( $value['type'] == 'content' && $value['position'] == 'start' ) {
						$output .= '<div class="options_warp">';

			 	}else{
 				//only show header if 'name' value exists
					$output .= '<div class="headtitle">';
					if($value['name']) $output .= '<h3 class="heading">'.esc_attr($value['name']).'</h3>'."\n";
					if(!isset($value['desc'])){ $explain_value = ''; } else{ 
						if( empty($value['none_desc'])){
 							$explain_value = '<div class="explain">'.esc_html($value['desc']).'</div>'."\n"; 
						}
					} 
					$output .= ''.$explain_value."\n";
					$output .='</div>';
					$output .= '<div class="option">'."\n" . '<div class="controls">'."\n";
				}
	
			 } 
			 
			 //End gamepress Edit
 			 //End Heading

			//if (!isset($smof_data[$value['id']]) && $value['type'] != "heading")
			//	continue;
			
			//switch statement to handle various options type                              
			switch ( $value['type'] ) {
			
				//text input
				case 'text':
					$t_value = '';
					$t_value = stripslashes($smof_data[$value['id']]);
					
					$mini ='';
					if(!isset($value['mod'])) $value['mod'] = '';
					if($value['mod'] == 'mini') { $mini = 'mini';}
					
					$output .= '<input class="of-input '.esc_attr($mini).'" name="'.esc_attr($value['id']).'" id="'.esc_attr( $value['id']) .'" type="'.esc_attr($value['type']).'" autocomplete="off" value="'.esc_attr($t_value).'" />';
				break;
				//text input
				case 'text2':
					$t_value = '';
					$t_value = $smof_data[$value['id']];
					
					$mini ='';
					if(!isset($value['mod'])) $value['mod'] = '';
					if($value['mod'] == 'mini') { $mini = 'mini';}
					
					$output .= '<input class="of-input '.esc_attr($mini).'" name="'.esc_attr($value['id']).'" id="'.esc_attr( $value['id']) .'" type="'.esc_attr($value['type']).'" autocomplete="off" value="'.esc_attr($t_value).'" />';
				break;				
				//gamepress Edit
				//text-mini input
				case 'text-mini':
					$t_value = '';
					if( isset( $value['id'] ) && 
						( $value['id'] == 'date_format' || $value['id'] == 'alternate_date_format_month_year' || $value['id'] == 'alternate_date_format_day' || $value['id'] == 'timeline_date_format' )
					) {
						$t_value = $smof_data[$value['id']];
					} else {
						$t_value = stripslashes($smof_data[$value['id']]);
					}
					
					$mini ='';
					if(!isset($value['mod'])) $value['mod'] = '';
					if($value['mod'] == 'mini') { $mini = 'mini';}
					
					$output .= '<input class="of-input '.esc_attr($mini).'" name="'.esc_attr($value['id']).'" id="'.esc_attr( $value['id']) .'" type="text" autocomplete="off" value="'.esc_attr( $t_value) .'"  style="width:60px;"/>';
				break;
				//End gamepress Edit
				//select option
				case 'select':
					$mini ='';
					if(!isset($value['mod'])) $value['mod'] = '';
					if($value['mod'] == 'mini') { $mini = 'mini';}
						$output .= '<div class="select_wrapper ' .esc_attr( $mini ) . '">';
						$output .= '<select class="select of-input" name="'.esc_attr($value['id']).'" autocomplete="off" id="'.esc_attr($value['id']).'">';
 																						 

						if(!empty($value['options']) && is_array($value['options'])){
						foreach ($value['options'] as $select_ID => $option) {
							$theValue = $option;
							$theValue = $select_ID;
 							$value_id =!empty($smof_data[$value['id']])?$smof_data[$value['id']]:hexwp_option_default($value['id'],'',true);
							$output .= '<option id="' .esc_attr( $select_ID ). '" value="'.esc_attr($theValue).'" ' . selected($value_id, $theValue, false) . ' >'.$option.'</option>';	 
						 } 
					}
					$output .= '</select>';
					$output .= '</div>';
				break;
				
				//textarea option
				case 'textarea':	
					$cols = '8';
					$ta_value = '';
					
					if(isset($value['options'])){
							$ta_options = $value['options'];
							if(isset($ta_options['cols'])){
							$cols = $ta_options['cols'];
							} 
						}
						
						$ta_value = stripslashes($smof_data[$value['id']]);			
						$output .= '<textarea autocomplete="off" class="of-input" name="'.esc_attr($value['id']).'" id="'.esc_attr($value['id']).'" cols="'.esc_attr( $cols ).'" rows="8">'.esc_textarea($ta_value).'</textarea>';		
				break;
				
				//radiobox option
		case "radio":
				
					$value_id =isset($smof_data[$value['id']])?$smof_data[$value['id']]:hexwp_option_default($value['id'],'',true);
					
					
					
					 foreach($value['options'] as $radio_value=>$name) {
										$checked = isset($value_id) ? checked($value_id, $radio_value, false) : '';
	 
						 $off= isset($value['hide'])?$value['hide']:'off';

 						 if($radio_value===$off){
							$class_radio='of-hide'; 
						 }else{
							$class_radio=''; 
						 }
						$output .= '<input autocomplete="off" class="of-input of-radio  " id="of_label_'.esc_attr($value['id']).'_'.esc_attr($radio_value).'"  name="'.esc_attr($value['id']).'" type="radio" value="'.esc_attr($radio_value).'" ' . checked($value_id, $radio_value, false) . ' /><label class="radio '.esc_attr($class_radio).'"  for="of_label_'.esc_attr($value['id']).'_'.esc_attr($radio_value).'">'.esc_html($name).'</label>';				
					}
				break;
				//Category
				case 'category':
					$mini ='';
					if(!isset($value['mod'])) $value['mod'] = '';
					if($value['mod'] == 'mini') { $mini = 'mini';}
					$value_id =!empty($smof_data[$value['id']])?$smof_data[$value['id']]:hexwp_option_default($value['id'],'',true);

					$output .= '<div class="select_wrapper ' .esc_attr( $mini ) . '">';
					$output .= '<select autocomplete="off" class="select of-input" name="'.esc_attr($value['id']).'" id="'.esc_attr($value['id']).'">';
 					if(!empty($value['options'])){
					foreach ($value['options'] as $select_ID => $option) {
						$theValue = $select_ID;
						
						if( $option == 'Blog Sidebar' ) {
							$option = 'Default Sidebar';
						}
						
						if (!is_numeric($select_ID))
							$theValue = $select_ID;
						$output .= '<option id="' .esc_attr($theValue) . '" value="'.esc_attr($theValue).'" ' . selected($value_id, $theValue, false) . ' />'.esc_html($option).'</option>';	 
					 } 
					}
					$output .= '</select></div>';
				break;
				//checkbox option
				case 'checkbox':
				
					if (!isset($smof_data[$value['id']])) {
						$smof_data[$value['id']] = 0;
					}
					
					$fold = '';
					if (array_key_exists("folds",$value)) $fold="fld ";
		
					$output .= '<input autocomplete="off" type="hidden" class="' .esc_attr( $fold).'checkbox of-input" name="' .esc_attr( $value['id']).'" id="' .esc_attr(  $value['id']).'" value="0"/>';
					$output .= '<input autocomplete="off" type="checkbox" class="' .esc_attr( $fold).'checkbox of-input" name="' .esc_attr( $value['id']).'" id="' .esc_attr(  $value['id']) .'" value="1" '. checked($smof_data[$value['id']], 1, false) .' />';
				break;
				
				//multiple checkbox option
				case 'multicheck': 			
					(isset($smof_data[$value['id']]))? $multi_stored = $smof_data[$value['id']] : $multi_stored="";
								
					foreach ($value['options'] as $key => $option) {
						if (!isset($multi_stored[$key])) {$multi_stored[$key] = '';}
						$of_key_string = $value['id'] . '_' . $key;
						$output .= '<input type="checkbox" class="checkbox of-input" name="' .esc_attr( $value['id']).'['.esc_attr($key).']'.'" id="' .esc_attr( $of_key_string ).'" value="1" '. checked($multi_stored[$key], 1, false) .' /><label class="multicheck" for="' .esc_attr(  $of_key_string) .'">'. esc_html($option) .'</label><br />';								
					}			 
				break;
				
				// Color picker
				case "color":
					$default_color = '';
					$value_id =!empty($smof_data[$value['id']])?$smof_data[$value['id']]:hexwp_option_default($value['id'],'',true);

					if ( isset($value['std']) ) {
						$default_color =  $value['std'] ;
					}
if(!empty($value['name2'])){
								$output .= '<span id="'.esc_attr(  $value['id']). '" class="mini-option-span" autocomplete="off">' . esc_html($value['name2']). '</span>';
							}
					
					$output .= '<input autocomplete="off" name="' .esc_attr(  $value['id']) . '" id="'.esc_attr(  $value['id']). '" class="  hexwp-coloris"   data-rgba="false"  type="text" value="' .esc_attr($value_id). '" data-default-color="' .esc_attr($value_id ) .'" />';
		 	
				break;
				
				case "color_rgba":
					$default_color = '';
					if ( isset($value['std']) ) {
						$default_color =  $value['std'] ;
					}
					$value_id =!empty($smof_data[$value['id']])?$smof_data[$value['id']]:hexwp_option_default($value['id'],'',true);
					
						if(!empty($value['name2'])){
								$output .= '<span id="'.esc_attr(  $value['id']). '" class="mini-option-span" autocomplete="off">' . esc_html($value['name2']). '</span>';
							}
					
					$output .= '<input autocomplete="off" name="' .esc_attr(  $value['id']) . '" id="'.esc_attr(  $value['id']). '" class="   hexwp-coloris"   data-rgba="true"  type="text" value="' .esc_attr($value_id). '" data-default-color="' .esc_attr($value_id ) .'" />';
		 	
				break;
				
				//gamepress Edit
				
				// Background Color
				case "backgroundcolor":
				//var_dump($value['id'],$smof_data[$value['id']]);
					if( is_string( $smof_data[$value['id']] ) ) {
						$bg_color_stored = array( 'color' => $smof_data[$value['id']], 'opacity' => '1' );
					} else {
						$bg_color_stored = $smof_data[$value['id']];
					}
					
					$value_id =!empty($smof_data[$value['id']])?$smof_data[$value['id']]:hexwp_option_default($value['id'],'',true);
					
					$output .= '<div id="' .esc_attr($value['id']) . '_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($bg_color_stored['color']).'"></div></div>';
					$output .= '<input autocomplete="off"  class="of-color of-border of-bg-color" name="'.esc_attr($value['id']).'[color]" id="'.esc_attr( $value['id']) .'_color" type="text" value="'.esc_attr($bg_color_stored['color']) .'" />';

					$output .= '<div class="bg-opacity"><span class="of-bg-opacity-desc">' . 'Opacity:' . '</span>';
					$output .= '<div class="select_wrapper"><select class="of-input of-bg-opacity select" name="'.esc_attr($value['id']).'[opacity]" id="'.esc_attr( $value['id']).'_opacity" autocomplete="off" >';
					$opacitys = array(	'1.0'=>'1.0',
										'0.9'=>'0.9',
										'0.8'=>'0.8',
										'0.7'=>'0.7',
										'0.6'=>'0.6',
										'0.5'=>'0.5',
										'0.4'=>'0.4',
										'0.3'=>'0.3',
										'0.2'=>'0.2',
										'0.1'=>'0.1',
										'0.0'=>'0.0',
										);
									
					foreach ($opacitys as $i=>$opacity){
						$output .= '<option value="'.esc_attr($i) .'" ' . selected($bg_color_stored['opacity'], $i, false) . '>'.esc_html($opacity) .'</option>';		
					}
					
					$output .= '</select></div></div>';
	
	
				break;				
				// Background Two
				case "color2":
				//var_dump($value['id'],$smof_data[$value['id']]);
					if( is_string( $smof_data[$value['id']] ) ) {
						$bg_color_stored = array(
												'color' => $smof_data[$value['id']],
												 'color2' => '' );
					} else {
						$bg_color_stored = $smof_data[$value['id']];
					}
 					
					
					$output .= '<div id="'.esc_attr($value['id'] ). '_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($bg_color_stored['color']).'"></div></div>';
					$output .= '<input autocomplete="off" class="of-border of-bg-color hw-color  hexwp-coloris"   data-rgba="false" name="'.esc_attr($value['id']).'[color]" id="'.esc_attr($value['id']) .'_color" type="text" value="'.esc_attr($bg_color_stored['color'] ).'" />';

					$output .= '<div class="bg-opacity"><span class="of-bg-opacity-desc">' . esc_html($value['name2']). '</span>';
					$output .= '<div id="' .esc_attr($value['id']) . '_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($bg_color_stored['color2']).'"></div></div>';
					$output .= '<input autocomplete="off" class="of-color of-border of-bg-color hw-color  hexwp-coloris"   data-rgba="false" name="'.esc_attr($value['id']).'[color2]" id="'.esc_attr($value['id'] ).'_color2" type="text" value="'.esc_attr($bg_color_stored['color2']) .'" />';
					$output .= '</div>';
	
	
				break;
				case "color2_rgba":
				//var_dump($value['id'],$smof_data[$value['id']]);
					if( is_string( $smof_data[$value['id']] ) ) {
						$bg_color_stored = array( 'color' => $smof_data[$value['id']], 'color2' => '' );
					} else {
						$bg_color_stored = $smof_data[$value['id']];
					}
					
					$output .= '<div id="'.esc_attr($value['id'] ). '_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($bg_color_stored['color']).'"></div></div>';
					$output .= '<input class="of-border of-bg-color hw-color  hexwp-coloris"   data-rgba="true" name="'.esc_attr($value['id']).'[color]" id="'.esc_attr($value['id']) .'_color" type="text" value="'.esc_attr($bg_color_stored['color'] ).'" />';

					$output .= '<div class="bg-opacity"><span class="of-bg-opacity-desc">' . esc_html($value['name2']). '</span>';
					$output .= '<div id="' .esc_attr($value['id']) . '_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($bg_color_stored['color2']).'"></div></div>';
					$output .= '<input autocomplete="off" class="of-color of-border of-bg-color hw-color  hexwp-coloris"   data-rgba="false" name="'.esc_attr($value['id']).'[color2]" id="'.esc_attr($value['id'] ).'_color2" type="text" value="'.esc_attr($bg_color_stored['color2']) .'" />';
					$output .= '</div>';
	
	
				break;
				case "color2_rgba2":
				//var_dump($value['id'],$smof_data[$value['id']]);
					if( is_string( $smof_data[$value['id']] ) ) {
						$bg_color_stored = array( 'color' => $smof_data[$value['id']], 'color2' => '' );
					} else {
						$bg_color_stored = $smof_data[$value['id']];
					}
					
					$output .= '<div id="'.esc_attr($value['id'] ). '_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($bg_color_stored['color']).'"></div></div>';
					$output .= '<input autocomplete="off" class="of-border of-bg-color hw-color  hexwp-coloris"   data-rgba="true" name="'.esc_attr($value['id']).'[color]" id="'.esc_attr($value['id']) .'_color" type="text" value="'.esc_attr($bg_color_stored['color'] ).'" />';

					$output .= '<div class="bg-opacity"><span class="of-bg-opacity-desc">' . esc_html($value['name2']). '</span>';
					$output .= '<div id="' .esc_attr($value['id']) . '_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($bg_color_stored['color2']).'"></div></div>';
					$output .= '<input autocomplete="off" class="of-color of-border of-bg-color hw-color  hexwp-coloris"   data-rgba="true" name="'.esc_attr($value['id']).'[color2]" id="'.esc_attr($value['id'] ).'_color2" type="text" value="'.esc_attr($bg_color_stored['color2']) .'" />';
					$output .= '</div>';
	
	
				break;						//End gamepress Edite
				//typography option	
				case 'typography':
				
					$typography_stored = isset($smof_data[$value['id']]) ? $smof_data[$value['id']] : $value['std'];
					
					/* Font Size */
					
					if(isset($typography_stored['size'])) {
						$output .= '<div class="select_wrapper typography-size" original-title="Font size">';
						$output .= '<select class="of-typography of-typography-size select" name="'.esc_attr($value['id']).'[size]" id="'.esc_attr($value['id']).'_size">';
							for ($i = 9; $i < 20; $i++){ 
								$test = $i.'px';
								$output .= '<option value="'.esc_attr($i) .'px" ' . selected($typography_stored['size'], $test, false) . '>'.esc_attr($i) .'px</option>'; 
								}
				
						$output .= '</select></div>';
					
					}
					
					/* Line Height */
					if(isset($typography_stored['height'])) {
					
						$output .= '<div class="select_wrapper typography-height" original-title="Line height">';
						$output .= '<select autocomplete="off" class="of-typography of-typography-height select" name="'.esc_attr($value['id']).'[height]" id="'.esc_attr($value['id']).'_height">';
							for ($i = 20; $i < 38; $i++){ 
								$test = $i.'px';
								$output .= '<option value="'.esc_attr($i) .'px" ' . selected($typography_stored['height'], $test, false) . '>'. esc_attr($i) .'px</option>'; 
								}
				
						$output .= '</select></div>';
					
					}
						
					/* Font Face */
					if(isset($typography_stored['face'])) {
					
						$output .= '<div class="select_wrapper typography-face" original-title="Font family">';
						$output .= '<select autocomplete="off" class="of-typography of-typography-face select" name="'.esc_attr($value['id']).'[face]" id="'.esc_attr($value['id']).'_face">';
						
						$faces = array('arial'=>'Arial',
										'verdana'=>'Verdana, Geneva',
										'trebuchet'=>'Trebuchet',
										'georgia' =>'Georgia',
										'times'=>'Times New Roman',
										'tahoma'=>'Tahoma, Geneva',
										'palatino'=>'Palatino',
										'helvetica'=>'Helvetica' );			
						foreach ($faces as $i=>$face) {
							$output .= '<option value="'.esc_attr( $i ).'" ' . selected($typography_stored['face'], $i, false) . '>'.esc_html($face) .'</option>';
						}			
										
						$output .= '</select></div>';
					
					}
					
					/* Font Weight */
					if(isset($typography_stored['style'])) {
					
						$output .= '<div class="select_wrapper typography-style" original-title="Font style">';
						$output .= '<select autocomplete="off" class="of-typography of-typography-style select" name="'.esc_attr($value['id']).'[style]" id="'.esc_attr($value['id']).'_style">';
						$styles = array('normal'=>'Normal',
										'italic'=>'Italic',
										'bold'=>'Bold',
										'bold italic'=>'Bold Italic');
										
						foreach ($styles as $i=>$style){
						
							$output .= '<option value="'.esc_attr($i) .'" ' . selected($typography_stored['style'], $i, false) . '>'.esc_html($style) .'</option>';		
						}
						$output .= '</select></div>';
					
					}
					
					/* Font Color */
					if(isset($typography_stored['color'])) {
					
						$output .= '<div id="' .esc_html( $value['id'] ). '_color_picker" class="colorSelector typography-color"><div style="background-color: '.esc_attr($typography_stored['color']).'"></div></div>';
						$output .= '<input autocomplete="off" class="of-color of-typography of-typography-color" original-title="Font color" name="'.esc_attr($value['id']).'[color]" id="'.esc_attr($value['id']).'_color" type="text" value="'. $typography_stored['color'] .'" />';
					
					}
					
				break;
				
				//border option
				case 'border':
						
					/* Border Width */
					$border_stored = $smof_data[$value['id']];

  					$border_size = isset( $border_stored['size'])?  $border_stored['size']:'';
					$output .= '<div class="mini-option">';
					$output .= '<span>'.esc_html__('Size','hexwp').'</span><input style="width:60px;" class=" of-input" type="text" name="'.esc_attr($value['id']).'[size]" id="'.esc_attr($value['id']).'_size" value="'.esc_attr($border_size).'">';
 					$output .= '</div>';
					
					
					/* Border Style */
					
  					$border_stored_position = !empty( $border_stored['position'])?  $border_stored['position']:'';
					
					$output .= '<div class="mini-option">';
  					$output .= '<div class="select_wrapper border-position">';
					$output .= '<select autocomplete="off" class="of-border of-border-position select" name="'.esc_attr($value['id']).'[position]" id="'.esc_attr($value['id']).'_position">';
					
					$position_array = array(
						"round"			=>	esc_html__('All Round','hexwp'),  
						"top"			=>	esc_html__('Top','hexwp'),  
						"right"			=>	esc_html__('Right','hexwp'),  
						"bottom"		=>	esc_html__('Bottom','hexwp'),  
						"left"			=>	esc_html__('Left','hexwp'),  
						"top-bottom"	=>	esc_html__('Top And Bottom','hexwp')
					);
					foreach ($position_array as $position_i=>$position_name){
						$output .= '<option value="'. $position_i .'" ' . selected($border_stored_position, $position_i, false) . '>'. $position_name .'</option>';		
					}
					
					$output .= '</select></div>';
					$output .= '</div>';
				
  					$border_stored_style = !empty( $border_stored['style'])?  $border_stored['style']:'';
					
					/* Border Style */
 					$output .= '<div class="mini-option">';
  					$output .= '<div class="select_wrapper border-style">';
					$output .= '<selec autocomplete="off"t class="of-border of-border-style select" name="'.esc_attr($value['id']).'[style]" id="'.esc_attr($value['id']).'_style">';
					
					$style_array =hexwp_array_options('border_style');
					foreach ($style_array as $style_i=>$style_name){
						$output .= '<option value="'. $style_i .'" ' . selected($border_stored_style, $style_i, false) . '>'. $style_name .'</option>';		
					}
					
					$output .= '</select></div>';
					$output .= '</div>';
					


  					$border_stored_color = !empty( $border_stored['color'])?  $border_stored['color']:'';
				
					/* Border Color */		
					$output .= '<div id="'.esc_attr($value['id']).'_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($border_stored_color).'"></div></div>';
					$output .= '<input autocomplete="off" class="of-color of-border of-border-color  hexwp-coloris" name="'.esc_attr($value['id']).'[color]" id="'.esc_attr($value['id']).'_color" type="text" value="'.esc_attr( $border_stored_color) .'" />';
					
				break;
							//border option
				case 'shadow':
						
					/* Border Width */
					$shadow_stored = $smof_data[$value['id']];
					
					//Blur
  					$shadow_blur = !empty( $shadow_stored['blur'])?  $shadow_stored['blur']:'';
					
					$output .= '<div class="mini-option">';
					$output .= '<span>'.esc_html__('Blur','hexwp').'</span><input style="width:60px;" class=" of-input" type="text" name="'.esc_attr($value['id']).'[blur]" id="'.esc_attr($value['id']).'_blur" value="'.esc_attr($shadow_blur).'">';
 					$output .= '</div>';
					
  					$shadow_spread = !empty( $shadow_stored['spread'])?  $shadow_stored['spread']:'';
					
					$output .= '<div class="mini-option">';
					$output .= '<span>'.esc_html__('Spread','hexwp').'</span><input style="width:60px;" class=" of-input" type="text"  name="'.esc_attr($value['id']).'[spread]" id="'.esc_attr($value['id']).'_spread" value="'.esc_attr($shadow_spread).'">';
 					$output .= '</div>';
					  
					
					$output .= '<div class="mini-option">';
					/* Border Color */		
  					$shadow_color = !empty( $shadow_stored['color'])?  $shadow_stored['color']:'';
					$output .= '<div id="'.esc_attr($value['id']).'_color_picker" class="colorSelector"><div style="background-color: '.esc_attr($shadow_color).'"></div></div>';
					$output .= '<input autocomplete="off" class="of-color of-border of-border-color hw-color  hexwp-coloris" name="'.esc_attr($value['id']).'[color]" id="'.esc_attr($value['id']).'_color" type="text" value="'.esc_attr( $shadow_color) .'" />';
					$output .= '</div>';
					
				break;
				
				//gamepress Edit
				//images checkbox - use image as checkboxes
				case 'images':
				
 					$i = 0;
					
					$select_value = (isset($smof_data[$value['id']])) ? $smof_data[$value['id']] : '';
					
					foreach ($value['options'] as $key => $option) 
					{ 
					$i++;
			
						$checked = '';
						$selected = '';
						if(NULL!=checked($select_value, $key, false)) {
							$checked = checked($select_value, $key, false);
							$selected = 'of-radio-img-selected';  
						}
						$output .= '<span class="of-radio-'.esc_attr($key).'">';
						$output .= '<input autocomplete="off" type="radio" id="of-radio-img-'  .esc_attr( $value['id']) . $i . '" class="checkbox of-radio-img-radio '.esc_attr($value['id']).'_'.esc_attr($key).' " value="'.esc_attr($key).'" name="'.esc_attr($value['id']).'" '.esc_attr($checked).' />';
						$output .= '<div class="of-radio-img-label">'.esc_attr( $key) .'</div>';
						$output .= '<img src="'.esc_attr($option).'" alt="" class="of-radio-img-img '.esc_attr($selected) .'" onClick="document.getElementById(\'of-radio-img-'. esc_attr($value['id']) . ($i).'\').checked = true;" />';
						$output .= '</span>';				
					}
					
					
				break;
 
				case 'multi_options':
						
					/* Border Width */
					$select_value = (isset($smof_data[$value['id']])) ? $smof_data[$value['id']] : '';
					
					//Blur
 					 if(!empty($value['options'])){
						$output .= hexwp_options_function_multi_item($value['id'],$select_value,$value['options'] );
					}
					
				break;
 
				case 'accordion-content':
				break;	
				case 'content':
				break;	
				 
				case "accordion":
					$info_text = $value['name'];
					$output .= '<span class="fa panel-plus">+ '.esc_html($info_text).'</span>';
					$output .= '<span class="fa panel-minus" style="display:none;">- '.esc_html($info_text).'</span>';

				break;
				//End gamepress Edit
				//images checkbox - use image as checkboxes
				case 'images':
				
					$i = 0;
					
					$select_value = (isset($smof_data[$value['id']])) ? $smof_data[$value['id']] : '';
					
					foreach ($value['options'] as $key => $option) 
					{ 
					$i++;
			
						$checked = '';
						$selected = '';
						if(NULL!=checked($select_value, $key, false)) {
							$checked = checked($select_value, $key, false);
							$selected = 'of-radio-img-selected';  
						}
						$output .= '<span>';
						$output .= '<input autocomplete="off" type="radio" id="of-radio-img-'.esc_attr( $value['id']) .esc_attr($i) . '" class="checkbox of-radio-img-radio" value="'.esc_attr( $key).'" name="'.esc_attr($value['id']).'" '.esc_attr( $checked).' />';
						$output .= '<div class="of-radio-img-label">'.esc_html( $key) .'</div>';
						$output .= '<img src="'.esc_attr($option).'" alt="" class="of-radio-img-img '.esc_attr($selected) .'" onClick="document.getElementById(\'of-radio-img-'.esc_attr( $value['id']) .esc_attr(  $i).'\').checked = true;" />';
						$output .= '</span>';				
					}
					
				break;
				
				
									$output .= '<textarea autocomplete="off" id="export_data" rows="8">'.urlencode(serialize($smof_data)) /* 100% safe - ignore theme check nag */ .'</textarea>'."\n";
					$output .= '<a href="#" id="of_import_button" class="button" title="'.'Restore Options'.'">'.esc_html__('Import Predefined Styles' , 'hexwp').'</a>';
				
				//info (for small intro box etc)
				case "info":
					$info_text = $value['std'];
					$output .= '<div class="of-info">'.esc_html($info_text).'</div>';
				break;
				
				//display a single image
				case "image":
					$src = $value['std'];
					$output .= '<img src="'.esc_url($src).'">';
				break;
				
				//tab heading
	//tab heading
				case 'heading':
 					if($counter >= 2){
					   $output .= '</div>'."\n";
					}
					//custom icon
					$icon = '';
					if(isset($value['icon'])){
						$icon = ' style="background-image: url('.esc_url( $value['icon']) .');"';
					}
					$header_class = str_replace(' ','',strtolower($value['name']));
					$jquery_click_hook = str_replace(' ', '', strtolower($value['name']) );
					$jquery_click_hook = "of-option-" . $value['id']  ;
					$value_id = !empty($value['id'])?$value['id'] :''  ;
 
 
 
					$menu .= '<li class="'.esc_attr( $header_class) .'"><a title="'.esc_attr($value['name']) .'" href="#of-option-'. esc_attr($value_id)  .'"  data-id="'. esc_attr($value_id)  .'"  '.wp_kses_post($icon)  .'>'.esc_html($value['name']) .'</a></li>';
					$output .= '<div class="group" id="'.esc_attr( $jquery_click_hook)  .'"><h2>'.esc_html($value['name']).'</h2>'."\n";
				break;
				
				
				
				//drag & drop slide manager
				case 'slider':
					$output .= '<div class="slider"><ul id="'.esc_attr($value['id']).'">';
					$slides = $smof_data[$value['id']];
					$count = count($slides);
					if ($count < 2) {
						$oldorder = 1;
						$order = 1;
						$output .= Options_Machine::optionsframework_slider_function($value['id'],$value['std'],$oldorder,$order);
					} else {
						$i = 0;
						foreach ($slides as $slide) {
							$oldorder = $slide['order'];
							$i++;
							$order = $i;
							$output .= Options_Machine::optionsframework_slider_function($value['id'],$value['std'],$oldorder,$order);
						}
					}			
					$output .= '</ul>';
					$output .= '<a href="#" class="button slide_add_button">'.esc_html__('Add New Slide.','hexwp').'</a></div>';
					
				break;
				
				//drag & drop block manager
				case 'sorter':

				    // Make sure to get list of all the default blocks first
				    $all_blocks = $value['std'];

				    $temp = array(); // holds default blocks
				    $temp2 = array(); // holds saved blocks

					foreach($all_blocks as $blocks) {
					    $temp = array_merge($temp, $blocks);
					}

				    $sortlists = isset($data[$value['id']]) && !empty($data[$value['id']]) ? $data[$value['id']] : $value['std'];

				    foreach( $sortlists as $sortlist ) {
					$temp2 = array_merge($temp2, $sortlist);
				    }

				    // now let's compare if we have anything missing
				    foreach($temp as $k => $v) {
					if(!array_key_exists($k, $temp2)) {
					    $sortlists['disabled'][$k] = $v;
					}
				    }

				    // now check if saved blocks has blocks not registered under default blocks
				    foreach( $sortlists as $key => $sortlist ) {
					foreach($sortlist as $k => $v) {
					    if(!array_key_exists($k, $temp)) {
						unset($sortlist[$k]);
					    }
					}
					$sortlists[$key] = $sortlist;
				    }

				    // assuming all sync'ed, now get the correct naming for each block
				    foreach( $sortlists as $key => $sortlist ) {
					foreach($sortlist as $k => $v) {
					    $sortlist[$k] = $temp[$k];
					}
					$sortlists[$key] = $sortlist;
				    }

				    $output .= '<div id="'.esc_attr($value['id']).'" class="sorter">';


				    if ($sortlists) {

					foreach ($sortlists as $group=>$sortlist) {

					    $output .= '<ul id="'.esc_attr($value['id']).'_'.esc_attr($group).'" class="sortlist_'.esc_attr($value['id']).'">';
					    $output .= '<h3>'.$group.'</h3>';

					    foreach ($sortlist as $key => $list) {

						$output .= '<input autocomplete="off" class="sorter-placebo" type="hidden" name="'.esc_attr($value['id']).'['.esc_attr($group).'][placebo]" value="placebo">';

						if ($key != "placebo") {

						    $output .= '<li id="'.esc_attr($key).'" class="sortee">';
						    $output .= '<input class="position" type="hidden" name="'.esc_attr($value['id']).'['.esc_attr($group).']['.esc_attr($key).']" value="'.esc_attr($list).'">';
						    $output .= esc_html($list);
						    $output .= '</li>';

						}

					    }

					    $output .= '</ul>';
					}
				    }

				    $output .= '</div>';
				break;
				
				//background images option
				case 'tiles':
					
					$i = 0;
					$select_value = isset($smof_data[$value['id']]) && !empty($smof_data[$value['id']]) ? $smof_data[$value['id']] : '';
					if (is_array($value['options'])) {
						foreach ($value['options'] as $key => $option) { 
						$i++;
				
							$checked = '';
							$selected = '';
							if(NULL!=checked($select_value, $option, false)) {
								$checked = checked($select_value, $option, false);
								$selected = 'of-radio-tile-selected';  
							}
							$output .= '<span>';
							$output .= '<input autocomplete="off" type="radio" id="of-radio-tile-'.esc_attr($value['id']) .esc_attr($i) . '" class="checkbox of-radio-tile-radio" value="'.esc_attr($key).'" name="'.esc_attr($value['id']).'" '.$checked.' />';
							$output .= '<div class="of-radio-tile-img '.esc_attr( $selected) .'" style="background: url('.esc_attr($option).')" onClick="document.getElementById(\'of-radio-tile-'.esc_attr($value['id']) .esc_attr( $i).'\').checked = true;"></div>';
							$output .= '</span>';				
						}
					}
					
				break;
				
				//backup and restore options data
				case 'backup':
				
					$instructions = $value['desc'];
					$backup = of_get_options(BACKUPS);
					$init = of_get_options('smof_init');


					if(!isset($backup['backup_log'])) {
						$log = 'No backups yet';
					} else {
						$log = $backup['backup_log'];
					}
					
					$output .= '<div class="backup-box">';
					$output .= '<div class="instructions">'.$instructions."\n";
					$output .= '<p><strong>'. esc_html__('Last Backup :','hexwp').'<span class="backup-log">'.$log.'</span></strong></p></div>'."\n";
					$output .= '<a href="#" id="of_backup_button" class="button" title="Backup Options">'.esc_html__('Backup Options' , 'hexwp').'</a>';
					$output .= '<a href="#" id="of_restore_button" class="button" title="Restore Options">'.esc_html__('Restore Options' , 'hexwp').'</a>';
					$output .= '</div>';
				
				break;
				
				//export or import data between different installs
				case 'transfer':
				
					$instructions = $value['desc'];
					$output .= '<textarea autocomplete="off" id="export_data" rows="8">'.esc_html(urlencode(serialize($smof_data))) /* 100% safe - ignore theme check nag */ .'</textarea>'."\n";
					$output .= '<a href="#" id="of_import_button" class="button" title="Restore Options">'.esc_html__('Import Options' , 'hexwp').'</a>';
				
				break;
				
				// google font field
				case 'select_google_font':
					$output .= '<div class="select_wrapper">';
					$output .= '<select autocomplete="off" class="select of-input google_font_select" name="'.esc_attr($value['id']).'" id="'.esc_attr($value['id']).'">';
					foreach ($value['options'] as $select_key => $option) {
						$output .= '<option value="'.esc_attr($select_key).'" ' . selected((isset($smof_data[$value['id']]))? $smof_data[$value['id']] : "", $option, false) . ' />'.esc_html($option).'</option>';
					} 
					$output .= '</select></div>';
					
					if(isset($value['preview']['text'])){
						$g_text = $value['preview']['text'];
					} else {
						$g_text = '0123456789 ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz';
					}
					if(isset($value['preview']['size'])) {
						$g_size = 'style="font-size: '. $value['preview']['size'] .';"';
					} else { 
						$g_size = '';
					}
					$hide = " hide";
					if ($smof_data[$value['id']] != "none" && $smof_data[$value['id']] != "")
						$hide = "";
					
					$output .= '<p class="'.esc_attr($value['id']).'_ggf_previewer google_font_preview'.esc_attr($hide).'" '.esc_attr($g_size) .'>'.esc_html( $g_text ).'</p>';
				break;
				//gamepress Edit
				case 'predefined':
				
					$instructions = $value['desc'];
					
					
 					$i = 0;
					
					$select_value = (isset($smof_data[$value['id']])) ? $smof_data[$value['id']] : '';
					
					foreach ($value['options'] as $key => $option) 
					{ 
					$i++;
					
									
					$output .= '<span class="of-radio-'.esc_attr($key).'"  >';
  										$output .= '<img src="'.esc_attr($option).'" alt="" class="of-radio-img-img  "  />';
										$output .= '<a href="#" id="of_import_buttons" data-id="'.esc_attr($key).'" class="button" title="'.'Restore Options'.'">'.esc_html__('Import Predefined Styles' , 'hexwp').'</a>';

					$output .= '</span>';		
  					}
 									$output .= '<textarea autocomplete="off" id="export_data" rows="8" class="hexwpsss">'.urlencode(serialize($smof_data)) /* 100% safe - ignore theme check nag */ .'</textarea>'."\n";

 
				break;
				// End gamepress Edit
				//JQuery UI Slider
				case 'sliderui':
					$s_val = $s_min = $s_max = $s_step = $s_edit = '';//no errors, please
					
					$s_val  = stripslashes($smof_data[$value['id']]);
					
					if(!isset($value['min'])){ $s_min  = '0'; }else{ $s_min = $value['min']; }
					if(!isset($value['max'])){ $s_max  = $s_min + 1; }else{ $s_max = $value['max']; }
					if(!isset($value['step'])){ $s_step  = '1'; }else{ $s_step = $value['step']; }
					
					if(!isset($value['edit'])){ 
						$s_edit  = ' readonly="readonly"'; 
					}
					else
					{
						$s_edit  = '';
					}
					
					if ($s_val == '') $s_val = $s_min;
					
					//values
					$s_data = 'data-id="'.esc_attr($value['id']).'" data-val="'.esc_attr($s_val).'" data-min="'.esc_attr($s_min).'" data-max="'.esc_attr($s_max).'" data-step="'.esc_attr($s_step).'"';
					
					//html output
					$output .= '<input type="text" name="'.esc_attr($value['id']).'" id="'.esc_attr($value['id']).'" value="'.esc_attr( $s_val) .'" class="mini" '.esc_attr( $s_edit) .' />';
					$output .= '<div id="'.esc_attr($value['id']).'-slider" class="smof_sliderui" style="margin-left: 7px;" '. esc_attr($s_data) .'></div>';
					
				break;
				
				case 'switch_radio': 
					
	//Label ON
					if(!isset($value['on'])){
						$on = "On";
					}else{
						$on = $value['on'];
					}
					
					//Label OFF
					if(!isset($value['off'])){
						$off = "Off";
					}else{
						$off = $value['off'];
					}
					
					$select_value = !empty($smof_data[$value['id']]) ? $smof_data[$value['id']] : 'disable';
					
 					
					if ($select_value == 'enable'){
						$label_enabled = ' selected';
						$label_disabled = '';
					}else{
						$label_enabled = '';
						$label_disabled = ' selected';
					}
					
					
					
				 
					$output .= '<p class="radio-switch-options">'; 
   						$output .= '<input autocomplete="off" type="radio" id="of-radio-'.esc_attr($value['id']).'-enabled"  class="of-input of-input-enabled" '.checked($smof_data[$value['id']], 'enable', false).' name="'.esc_attr($value['id']).'"  value="enable" > ';	
 						$output .= '<label autocomplete="off" for="#of-radio-'.esc_attr($value['id']).'-enabled"  class="of-switch-enable '.$label_enabled .'">'.$on.'</label> ';	
						
  						$output .= '<input autocomplete="off"  type="radio" id="of-radio-'.esc_attr($value['id']).'-disable" class="of-input of-input-disable" '.checked($smof_data[$value['id']], 'disable', false).'  name="'.esc_attr($value['id']).'"  value="disable" > ';	
 						$output .= '<label autocomplete="off" for="#of-radio-'.esc_attr($value['id']).'-disable"  class="of-switch-disable '.$label_disabled .'">'.$off.'</label> ';	
						
					$output .= '</p>';
					
				break;
				
				case 'switch_custom': 
					
	//Label ON
					if(!isset($value['on'])){
						$on = "On";
					}else{
						$on = $value['on'];
					}
					
					//Label OFF
					if(!isset($value['off'])){
						$off = "Off";
					}else{
						$off = $value['off'];
					}
					
					$select_value = !empty($smof_data[$value['id']]) ? $smof_data[$value['id']] : 'disable';
					
 					
					if ($select_value == $value['on_id']){
						$label_enabled = ' selected';
						$label_disabled = '';
					}else{
						$label_enabled = '';
						$label_disabled = ' selected';
					}
					
					
					
				 
					$output .= '<p class="radio-switch-options">'; 
   						$output .= '<input autocomplete="off" type="radio" id="of-radio-'.esc_attr($value['id']).'-enabled"  class="of-input of-input-enabled" '.checked($smof_data[$value['id']], $value['on_id'], false).' name="'.esc_attr($value['id']).'"  value="'.$value['on_id'].'" > ';	
 						$output .= '<label autocomplete="off" for="#of-radio-'.esc_attr($value['id']).'-enabled"  class="of-switch-enable '.$label_enabled .'">'.$on.'</label> ';	
						
  						$output .= '<input autocomplete="off"  type="radio" id="of-radio-'.esc_attr($value['id']).'-disable" class="of-input of-input-disable" '.checked($smof_data[$value['id']], $value['off_id'], false).'  name="'.esc_attr($value['id']).'"  value="'.$value['off_id'].'" > ';	
 						$output .= '<label autocomplete="off" for="#of-radio-'.esc_attr($value['id']).'-disable"  class="of-switch-disable '.$label_disabled .'">'.$off.'</label> ';	
						
					$output .= '</p>';
					
				break;
				
				
				
				
				//Switch option
				case 'switch':
					if (!isset($smof_data[$value['id']])) {
						$smof_data[$value['id']] = 0;
					}
					
					$fold = '';
					if (array_key_exists("folds",$value)) $fold="s_fld ";
					
					$cb_enabled = $cb_disabled = '';//no errors, please
					
					//Get selected
					if ($smof_data[$value['id']] == 1){
						$cb_enabled = ' selected';
						$cb_disabled = '';
					}else{
						$cb_enabled = '';
						$cb_disabled = ' selected';
					}
					
					//Label ON
					if(!isset($value['on'])){
						$on = "On";
					}else{
						$on = $value['on'];
					}
					
					//Label OFF
					if(!isset($value['off'])){
						$off = "Off";
					}else{
						$off = $value['off'];
					}
					
					$output .= '<p class="switch-options">';
						$output .= '<label class="'.esc_attr($fold).'cb-enable'.esc_attr( $cb_enabled) .'" data-id="'.esc_attr($value['id']).'"><span>'. esc_html($on) .'</span></label>';
						$output .= '<label class="'.esc_attr($fold).'cb-disable'.esc_attr( $cb_disabled) .'" data-id="'.esc_attr($value['id']).'"><span>'.esc_html( $off ).'</span></label>';
						
						$output .= '<input autocomplete="off" type="hidden" class="'.esc_attr($fold).'checkbox of-input" name="'.esc_attr($value['id']).'" id="'.esc_attr($value['id']).'" value="0"/>';
						$output .= '<input autocomplete="off" type="checkbox" id="'.esc_attr($value['id']).'" class="'.$fold.'checkbox of-input main_checkbox" name="'.esc_attr($value['id']).'"  value="1" '. checked($smof_data[$value['id']], 1, false) .' />';
						
					$output .= '</p>';
					
				break;

				// Uploader 3.5
				case "upload":
				case "media":

					if(!isset($value['mod'])) $value['mod'] = '';
					
					$u_val = '';
					if($smof_data[$value['id']]){
						$u_val = stripslashes($smof_data[$value['id']]);
					}

					$output .= Options_Machine::optionsframework_media_uploader_function($value['id'],$u_val, $value['mod']);
					

				break;
				
			}

			do_action('optionsframework_machine_loop', array(
					'options'	=> $options,
					'smof_data'	=> $smof_data,
					'defaults'	=> $defaults,
					'counter'	=> $counter,
					'menu'		=> $menu,
					'output'	=> $output,
					'value'		=> $value
				));
			if ($smof_output != "") {
				$output .= $smof_output;
				$smof_output = "";
			}
			
			//description of each option
			//gamepress Eidt
				if( $value['type'] == 'content' && $value['position'] == 'start' ) { 
				 	$output .= '';
				
				}elseif( $value['type'] == 'content' && $value['position'] == 'end' ) {
			 		 $output .= '</div></div>';
			   
			   }elseif ( $value['type'] != 'heading' ) { 
			 
					$output .= '</div>';
					$output .= '<div class="clear"> </div></div></div>'."\n";
				}
			   if( $value['type'] == 'accordion' && $value['position'] == 'start' ) {
			 	$output .= '<div class="accordion-content">';

			   }
				if( $value['type'] == 'accordion' && $value['position'] == 'end' ) {
			 	 $output .= '</div></div>';
			   }
			   
				 
			    
			} /* condition empty end */
			//End gamepress Eidt
			 /* condition empty end */
		   
		}

		if ($update_data == true) {
			of_save_options($smof_data);
		}
		
	    $output .= '</div>';

	    do_action('optionsframework_machine_after', array(
					'options'		=> $options,
					'smof_data'		=> $smof_data,
					'defaults'		=> $defaults,
					'counter'		=> $counter,
					'menu'			=> $menu,
					'output'		=> $output,
					'value'			=> $value
				));
		if ($smof_output != "") {
			$output .= $smof_output;
			$smof_output = "";
		}
	    
	    return array($output,$menu,$defaults);
	    
	}


	/**
	 * Native media library uploader
	 *
	 * @uses get_theme_mod()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function optionsframework_media_uploader_function($id,$std,$mod){

	    $data = of_get_options();
	    $smof_data = of_get_options();
		
		$uploader = '';
		$upload = "";
		if (isset($smof_data[$id]))
	    	$upload = $smof_data[$id];
		$hide = '';
		
		if ($mod == "min") {$hide ='hide';}
		
	    if ( $upload != "") { $val = $upload; } else {$val = $std;}
	    
		$uploader .= '<input class="'.esc_attr($hide).' upload of-input" name="'.esc_attr( $id ).'" id="'.esc_attr( $id ).'_upload" value="'.esc_attr( $val) .'" />';	
		
		//Upload controls DIV
		$uploader .= '<div class="upload_button_div">';
		//If the user has WP3.5+ show upload/remove button
		if ( function_exists( 'wp_enqueue_media' ) ) {
			$uploader .= '<span class="button media_upload_button" id="'.$id.'">'.__('Upload','hexwp').'</span>';
			
			if(!empty($upload)) {$hide = '';} else { $hide = 'hide';}
			$uploader .= '<span class="button remove-image '.esc_attr( $hide ).'" id="reset_'.esc_attr( $id) .'" title="' .esc_attr( $id ) . '">'.esc_html__('Remove','hexwp').'</span>';
		}
		else 
		{
			$output .= '<p class="upload-notice"><i>'.esc_html__('Upgrade your version of WordPress for full media support','hexwp').'</i></p>';
		}

		$uploader .='</div>' . "\n";

		//Preview
		$uploader .= '<div class="screenshot">';
		if(!empty($upload)){	
	    	$uploader .= '<a class="of-uploaded-image" href="'.esc_url( $upload ). '">';
	    	$uploader .= '<img class="of-option-image" id="image_'.esc_attr($id).'" src="'.esc_url($upload).'" alt="" />';
	    	$uploader .= '</a>';			
			}
		$uploader .= '</div>';
		$uploader .= '<div class="clear"></div>' . "\n"; 
	
		return $uploader;
		
	}

	/**
	 * Drag and drop slides manager
	 *
	 * @uses get_theme_mod()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function optionsframework_slider_function($id,$std,$oldorder,$order){
		
	    $data = of_get_options();
	    $smof_data = of_get_options();
		
		$slider = '';
		$slide = array();
		if (isset($smof_data[$id]))
	    	$slide = $smof_data[$id];
		
	    if (isset($slide[$oldorder])) { $val = $slide[$oldorder]; } else {$val = $std;}
		
		//initialize all vars
		$slidevars = array('title','url','link','description');
		
		foreach ($slidevars as $slidevar) {
			if (!isset($val[$slidevar])) {
				$val[$slidevar] = '';
			}
		}
		
		//begin slider interface	
		if (!empty($val['title'])) {
			$slider .= '<li><div class="slide_header"><strong>'.stripslashes($val['title']).'</strong>';
		} else {
			$slider .= '<li><div class="slide_header"><strong>Slide '.esc_html( $order).'</strong>';
		}
		
		$slider .= '<input type="hidden" class="slide of-input order" name="'.esc_atrr( $id ).'['.esc_atrr($order).'][order]" id="'. esc_atrr($id).'_'.esc_atrr($order) .'_slide_order" value="'.esc_atrr($order).'" />';
	
		$slider .= '<a class="slide_edit_button" href="#">Edit</a></div>';
		
		$slider .= '<div class="slide_body">';
		
		$slider .= '<label>'.esc_html__('Title','hexwp').'</label>';
		$slider .= '<input class="slide of-input of-slider-title" name="'.esc_atrr( $id ).'['.esc_atrr($order).'][title]" id="'. esc_atrr($id ).'_'.esc_atrr($order) .'_slide_title" value="'.esc_atrr( stripslashes($val['title'])) .'" />';
		
		$slider .= '<label>'.esc_html__('URL','hexwp').'</label>';
		$slider .= '<input class="upload slide of-input" name="'.esc_atrr( $id) .'['.esc_atrr($order).'][url]" id="'. esc_atrr($id) .'_'.esc_atrr($order ).'_slide_url" value="'. esc_atrr( $val['url']) .'" />';
		
		$slider .= '<div class="upload_button_div"><span class="button media_upload_button" id="'.esc_atrr($id).'_'.esc_atrr($order) .'">'.esc_html__('Upload','hexwp').'</span>';
		
		if(!empty($val['url'])) {$hide = '';} else { $hide = 'hide';}
		$slider .= '<span class="button remove-image '. esc_atrr($hide).'" id="reset_'. esc_atrr($id) .'_'.esc_atrr($order) .'" title="' .esc_atrr( $id) . '_'.esc_atrr($order) .'">'.esc_html__('Remove','hexwp').'</span>';
		$slider .='</div>' . "\n";
		$slider .= '<div class="screenshot">';
		if(!empty($val['url'])){
			
	    	$slider .= '<a class="of-uploaded-image" href="'.esc_atrr( $val['url']) . '">';
	    	$slider .= '<img class="of-option-image" id="image_'.esc_atrr($id).'_'.esc_atrr($order) .'" src="'.esc_atrr($val['url']).'" alt="" />';
	    	$slider .= '</a>';
			
			}
		$slider .= '</div>';	
		$slider .= '<label>'.esc_html__('Link URL (optional)','hexwp').'</label>';
		$slider .= '<input class="slide of-input" name="'.esc_atrr( $id) .'['.esc_atrr($order).'][link]" id="'.esc_atrr( $id) .'_'.esc_atrr($order) .'_slide_link" value="'.esc_atrr( $val['link'] ).'" />';
		
		$slider .= '<label>'.esc_html__('Description (optional)','hexwp').'</label>';
		$slider .= '<textarea class="slide of-input" name="'.esc_atrr( $id) .'['.esc_atrr($order).'][description]" id="'.esc_atrr( $id ).'_'.esc_atrr($order) .'_slide_description" cols="8" rows="8">'.stripslashes($val['description']).'</textarea>';
	
		$slider .= '<a class="slide_delete_button" href="#">'.esc_html__('Delete','hexwp').'</a>';
	    $slider .= '<div class="clear"></div>' . "\n";
	
		$slider .= '</div>';
		$slider .= '</li>';
	
		return $slider;
		
	}

	
}//end Options Machine class

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

														Options Machine multi item

*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
			 
function hexwp_options_function_multi_item($id,$value,$options,$fold=false) {
		ob_start(); 

  		if(!empty($options)){
		foreach ($options as  $option_value) { 	
		$option_type = !empty( $option_value['type'] ) ?  $option_value['type']  : '';
				echo '<div class="mini-option mini-option-'.$option_type.'">';
						
			 	
 				$value_content = isset($value[$option_value['id']])?$value[$option_value['id']]:'';
				$data  = $id.'['.$option_value['id'].']';
				$placeholder = !empty( $option_value['placeholder'] ) ?  $option_value['placeholder']  : null;
				
				
				if(!empty($option_value['fold'])){
						echo '<div class="options_fold">';
 					foreach($option_value['fold'] as $fold_key => $fold_value) : 
					echo  '<div class="options_fold_item" data-name="'.esc_attr($fold_value).'" data-value="'.esc_attr($fold_key).'"></div>';
 					endforeach;
						echo'</div>';
				}	
				if(!empty($option_value['name'])){
									echo '<span for="sao_label_'.esc_attr($data).'" class="mini-option-span"  autocomplete="off"    >'. esc_html($option_value['name']).'</span>';
				}
				switch( $option_type ) {
					// Text
					case 'text':
								echo  '<input  type="text" placeholder="'.esc_attr($placeholder).'" style="width:100px"  name="'.esc_attr($data).'" autocomplete="off"  value="'.esc_attr($value_content).'">';
					break;
						 
					//Number 	
					case 'number':
					
							$value_id =!empty($value_content)?$value_content:hexwp_option_default($id,$option_value['id'],true);

							echo '<input type="text" placeholder="'.esc_attr($placeholder).'" style="width:50px" name="'.esc_attr($data).'"  id="sao_label_'.esc_attr($data).'" autocomplete="off" value="'.esc_attr($value_id).'" >';
					break;
						 
					//Select 	
					case 'select': 
						$value_id =!empty($value_content)?$value_content:hexwp_option_default($id,$option_value['id'],true);

						echo '<div class="select_wrapper ">';
						echo '<select autocomplete="off"  class="select of-input"  name="'.esc_attr($data).'"  style="max-width:100px"  id="sao_tabs_'.esc_attr($data).'" >';
							if(!empty($option_value['options'])){
							foreach ($option_value['options'] as  $select_key => $select_text) { 	
									echo '<option  value="'.esc_attr($select_key).'"'.selected( $value_id, $select_key).'>'.esc_html($select_text).'</option>'; 
							}
							}
						echo '</select>';
						echo '</div>';
 					break;
					
					//Color	
					case 'color':
					
							$value_id =!empty($value_content)?$value_content:hexwp_option_default($id,$option_value['id'],true);
 								
						
							echo  '<input  class=" hexwp-coloris "  data-rgba="false" type="text"   name="'.esc_attr($data).'" data-default-color="' .esc_attr($value_id ) .'"  id="sao_label_'.esc_attr($data).'" autocomplete="off" value="'.esc_attr($value_id).'">';
					break;
						
					//Color RGBA
					case 'color_rgba':
											$value_id =!empty($value_content)?$value_content:hexwp_option_default($id,$option_value['id'],true);
	 						 
							echo '<input  class=" hexwp-coloris "data-rgba="true" type="text"   name="'.esc_attr($data).'" id="sao_label_'.esc_attr($data).'" data-default-color="' .esc_attr($value_id ) .'"  autocomplete="off" value="'.esc_attr($value_id).'">';
					break;
					 
					 
				}
				echo '</div>';
 				
		}
		}
 
		return  ob_get_clean();;

	 
}
