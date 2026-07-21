<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/********************************************************************
sor Options Encode
*********************************************************************/
if (!function_exists("vs_urldecode")) {
    function vs_urldecode($inputString = '') {
     if (isset($inputString) && !is_array($inputString)) {
        $decodedString = base64_decode($inputString, true);
        if ($decodedString === false) {
             return '';
        }
        return urldecode($decodedString);
    } else {
        return '';
    }

    }
}
if ( !function_exists ( "vs_sanitize_text_or_array_field" )){

function vs_sanitize_text_or_array_field($array_or_string) {
    if( is_string($array_or_string) ){
        $array_or_string = wp_kses($array_or_string,vs_kses());
    }elseif( is_array($array_or_string) ){
 			
 
 		if(count($array_or_string) ===0){
						unset($array_or_string);

		}else{
			foreach ( $array_or_string as $key => &$value ) {
				
				if ( is_array( $value ) ) {
 						$value = vs_sanitize_text_or_array_field($value);
  					 
				} 
				else {
					if ($value === '') {
					unset($array_or_string[$key]);
					} else if ($value === 0 || $value === '0') {
						$array_or_string[$key] = 0;
					}else{
					 $value = wp_kses( $value,vs_kses() );
					}
				}
			}
		} 
     }
	

    return $array_or_string;
}
}
 if ( !function_exists ( "vs_options_encode" )){

add_action('wp_ajax_vs_options_encode', 'vs_options_encode');
function vs_options_encode($opt=false){
	

	if(!empty($opt)){
  		$option = $opt;
	} 
	 
	if( !empty($opt)){
   		return urlencode($option);	
 	}else{
   		echo urlencode($option);	

 	die();
		 
	}
 }
 
}
/********************************************************************
sor Options DeCode
*********************************************************************/
  if ( !function_exists ( "vs_options_decode" )){

function vs_options_decode($data){
 	$o = vs_serialize_code(stripslashes( $data),'vs');
	$a =array();
	//OPTION1
	if(is_array($o) && !empty($o)){
	foreach($o as $v) :
				if(isset( $v)&& $v !=='' ){

	 	$a[stripslashes($v['key'])] = vs_options_decode_array($v);
				}
	endforeach;
	}else{
		$a = json_decode(stripslashes($data),true);
	}
	return $a;
}
}

if ( !function_exists ( "vs_options_decode_array" )){
function vs_options_decode_array($v){
	 $a = array();
	 $o_2 = vs_serialize_code(stripslashes($v['value']),stripslashes($v['name']));
		if(is_array($o_2)){
		foreach($o_2 as $v_2) :
			if(isset( $v_2)&& $v_2 !=='' ){
 		  		$a[urldecode(stripslashes($v_2['key']))] = vs_options_decode_array($v_2);
			}
		endforeach;
		}else{
			$a = urldecode(stripslashes($v['value']));
		}
	 return $a;
 }
  }
 
 
 /********************************************************************
sor Options Array Row
*********************************************************************/
  if ( !function_exists ( "vs_options_array_row" )){
function vs_options_array_row($row){
	$rez=preg_replace('/\s+/', ' ', trim($row));
	$options = json_decode($rez,true);
  	$array = array();
	
	if(!empty($options)){
	foreach($options as $key => $value) :
		if(isset($value)&& $value !=='' ){
		foreach($value as $key => $value) :
				$array[$key] = $value;
		  
		endforeach;
		}
	endforeach;
	}
	return $array;
}
  }

if ( !function_exists ( "vs_serialize_code" )){
function vs_serialize_code($in,$code) { 
	preg_match_all('/\[('.$code.'_\d+)(_\d+)?(?: (attr_[^"]*)="([^"]*)")?\](.+(?=\[\/\1\2?\]))?/',$in,$out,PREG_SET_ORDER);
 	foreach($out as $sc){
		if(isset($sc[1]) && $sc[1] !=='' ){
        // store child data in parent's content array
        $shortcodes[$sc[1]]=array('name'=>$sc[1].$sc[2],'key'=>isset($sc[4]) && $sc[4] !=='' ? $sc[4]:'' ,'value'=> isset($sc[5]) && $sc[5] !==''? $sc[5]:'' );
    	}
	}
	$shortcod = isset($shortcodes) && $shortcodes !==''? $shortcodes:'';
	return  $shortcod;
}
}
?>