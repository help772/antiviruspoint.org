<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Option 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_option($id =false, $default =false) {
	global $smof_data,$post_id;
	
  	if( is_singular()) {
 		$meta = get_post_meta( $post_id );
	}  	
	
 	
 	if(!empty( $meta[$id][0] )){
		$hexwp_option = $meta[$id][0] ;

	}elseif(!empty($smof_data[$id])  ){
		$hexwp_option = $smof_data[$id];
		
	}else{
		$hexwp_option =!empty($default)?'':hexwp_option_default($id,'',true);
 	}
  	return $hexwp_option;
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Option Array
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_option_2($id =false ,$arge_2 =false ,$default =false) {
 		global $smof_data,$post;
 	$data=$smof_data;
	global $post;
	$meta='';
 	if(!empty($post->ID)){
		$meta = get_post_meta($post->ID, $id, true);
	}
  	if(!empty( $meta[$arge_2] )){
		$hexwp_option = $meta[$arge_2] ;

	}
	elseif(!empty($data[$id][$arge_2])){
		$hexwp_option = $data[$id][$arge_2];
		
	}else{
			$hexwp_option =!empty($default)?'':hexwp_option_default($id,$arge_2,true);
 	}
		 
 	return $hexwp_option;
}
function hexwp_meta($id =false, $default =false) {
	global $post; 
	$meta='';
 	if(!empty($post->ID)){
		$meta = get_post_meta($post->ID, $id, true);
	}
	
	$hexwp_option='';
 	if(!empty( $meta )){
		$hexwp_option = $meta ;
 	}else{
		$hexwp_option = $default ;
	}
  	return $hexwp_option;
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		RTL Left
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_is_left() {
	return 'left';
 
}
function hexwp_is_right() {
	return 'right';
}
function hexwp_alignment($element =false) {
	return $element;
}
function hexwp_alignment_inverse($element =false) {
 
  	if(strpos($element,'left')!==false){
		return str_replace('left','right', $element);
		
	}elseif(strpos($element,'right')!==false){
 
 		return str_replace('right','left',$element);
		
	} else{
		return $element;
		
	}	
	
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		RTL Right
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_rtl_left() {
	return 'right';
}
function hexwp_rtl_right() {
	return 'right';
}
 
function hexwp_rtl_has($element =false) {

		return $element;

}

 

function hexwp_ltr_has($element =false) {
		return $element;
	 
}
 function hexwp_slug() {
	return hex2bin('72657a61');
}