<?php

include_once hexwp_PATH . '/header/logo.php';
include_once hexwp_PATH . '/header/menu.php';
include_once hexwp_PATH . '/header/mobile_menu.php';
include_once hexwp_PATH . '/header/search.php';
include_once hexwp_PATH . '/header/account.php';
include_once hexwp_PATH . '/header/social.php';
include_once hexwp_PATH . '/header/contact-us.php';
include_once hexwp_PATH . '/header/call.php';
include_once hexwp_PATH . '/header/text_html.php';
 include_once hexwp_PATH . '/header/cart.php';
include_once hexwp_PATH . '/header/wish.php';
include_once hexwp_PATH . '/header/header.php';
include_once hexwp_PATH . '/header/banner.php';
include_once hexwp_PATH . '/header/mobbar.php';
include_once hexwp_PATH . '/header/header-default.php';

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		WP Head
																		
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_head', 'hexwp_wp_head' );
function hexwp_wp_head() {
 
  	$responsive = hexwp_option('responsive') ;
	?>  
    
	<?php if ( $responsive == 'enable' ) { ?>
 		<meta name="viewport" content="width=device-width, initial-scale=1">
 	<?php }else{?>
    	<meta name="viewport" content="width=1920px, initial-scale=1">
	<?php }?>
 
  
<?php 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Header Data
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_header_data() { 
 
   if(get_query_var('post_type')=='visualheader'){
		global $post_id;
		$header_builder =!empty($post_id)?get_post_meta($post_id, 'vh_builder_json', true):'';
		$header= json_decode(stripslashes($header_builder),true);
 	}else{
 		$header_post = get_page_by_path( hexwp_option('header_builder'),'','visualheader' );
		if(!empty($header_post->ID)){
			$header_builder = get_post_meta($header_post->ID, 'vh_builder_json', true);
		}
		 if(!empty( $header_builder)){
			$header= json_decode(stripslashes($header_builder),true);
			 
		 }else{
			$header_default = hexwp_header_default(array());
			$header= json_decode(stripslashes($header_default['header_default']['value']),true);
	 
		} 
	}

	 return $header  ;
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Header Has Toolbar
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_has_toolbar($navbar_key=false,$header_builder=false) {
 	 $has=0;
 	 $has_mobile=0;
 
  	foreach(hexwp_header_column() as $column_key => $column_value):
	if( $column_value['child']==$navbar_key){
		if(!empty($header_builder['element'])){
		foreach(hexwp_header_decode($header_builder['element']) as $element_key => $element_value):
			$element_childern = !empty($element_value['childern'])?$element_value['childern']:'';
			if($element_childern==$column_key){
				$has=true;
			}
		endforeach;
		}
	}
	endforeach;
	 if(empty(hexwp_ismobile()) && ($navbar_key=='top' || $navbar_key=='middle' || $navbar_key=='bottom')){
		$has_mobile=true;
 
	 }elseif($navbar_key=='mobile_top' || $navbar_key=='mobile_middle' || $navbar_key=='mobile_bottom'){
		$has_mobile=true;

	 }
	  
	if(!empty($has_mobile) && !empty($has)){
		return true;
	}else{
		return false;
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Header Has Column
																	 	
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
function hexwp_has_column($flex=false,$column_key=false,$header_builder=false) {
 	 $has=0;
	 if(!empty($flex)&&
	 	 ($column_key =='top_center' ||
			$column_key =='middle_cente' ||
			$column_key =='bottom_center'  ||
			$column_key =='mobile_middle_center' )) {
		 
		 
  		if(!empty($header_builder['element'])){
		foreach(hexwp_header_decode($header_builder['element']) as $element_key => $element_value):
			$element_childern = !empty($element_value['childern'])?$element_value['childern']:'';
			if($element_childern==$column_key){
				$has=true;
			}
		endforeach;
		}
	 }else{
 		return true;

	 }
 	return $has;
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Header Decode
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_header_decode($row){
	$rez=preg_replace('/\s+/', ' ', trim($row));
	
	$options = json_decode(stripslashes(urldecode($rez)),true);
   	$array = array();
	
	if(!empty($options)){
	foreach($options as $key => $value) :
		if(!empty($value)){
		foreach($value as $key => $value) :
				$array[$key] = $value;
		  
		endforeach;
		}
	endforeach;
	}
	return $array;
 }
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Json Decode
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_json_decode($row){
	return json_decode(stripslashes(urldecode($row)),true);
  
 }
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Isset
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_isset($option=false,$id=false,$default = false){
   	return isset( $option[$id] ) ? $option[$id] : $default;
 	
}   
 function hexwp_isset_2($option=false,$id=false,$id_2=false,$default = false){
   return isset( $option[$id][$id_2] ) ? $option[$id][$id_2] : $default;
 	
}   
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		WP Title
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_wp_title( $title, $sep ) {
	if ( is_feed() ) {
		return $title;
	}

	global $page, $paged;
	$title .= get_bloginfo( 'name', 'display' );

	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $site_description";
	}

	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title .= " $sep " . sprintf(  hexwp_t('page').  ' %s' , max( $paged, $page ) );
	}

	return $title;
}
 
add_filter( 'wp_title', 'hexwp_wp_title', 10, 2 );
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Title Setup
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'after_setup_theme', 'hexwp_title_setup' );
function hexwp_title_setup() {
	add_theme_support( 'title-tag' );
}

if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function hexwp_slug_render_title() {?>
    
		<title><?php wp_title( '|', true, 'right' ); ?></title>
        
	<?php
	}
	add_action( 'wp_head', 'hexwp_slug_render_title' );
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Header Transparent  
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_header_overlap() {
     global $post_id ;
  	 if(!empty($_POST['header_perview'])){
 		return 'enable';
	 }else if(get_query_var('post_type')=='visualheader'){
 		return 'enable';
	}elseif(!empty(get_post_meta($post_id,'header_builder',true ))){
 		return 'enable';
	}else { 
		if(is_front_page()){
			return 'enable';
		}else{
			return 'disbale';
		}
	}
}

function hexwp_header_sticky($navbar_key=false,$navbar_option=false){
$sticky='';
		if($navbar_key =='mobile_top'|| $navbar_key =='mobile_middle' || $navbar_key =='mobile_bottom'){
			$mobile_sticky=!empty($navbar_option['mobile_sticky']) ?$navbar_option['mobile_sticky']:'';
			if($mobile_sticky=='top'){
				$sticky='hw-sticky';
				
			}elseif($mobile_sticky=='bottom'){
				$sticky='hw-sticky-bottom';
				
			}else{
				$sticky='';
			}
 
 		}else{
			$sticky=!empty($navbar_option['sticky']) ?'hw-sticky':'';
		}
	return $sticky;
}
 