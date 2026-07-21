<?php
include_once hexwp_PATH . '/custom-css/var-custom-css.php';
include_once hexwp_PATH . '/custom-css/custom-css.php';
include_once hexwp_PATH . '/custom-css/element-css.php';	
include_once hexwp_PATH . '/custom-css/var-element-css.php';	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_css($header_builder=false) {
	
	 
	$css='';
	$item_css='';
  	include_once hexwp_PATH . '/custom-css/lib/font-family-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/body-style-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/header-layout-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/header-element-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/dropdown-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/mobbar-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/post-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/footer-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/page-builder-css.php';
	$css.=':root{'.$item_css.'}';
	
	if(!empty($blog_css)){
		$css.='.hw-post-blog{'.$blog_css.'}';
	}
	
	if(!empty($single_css)){
		$css.='.hw-el-head-single,.hw-el-single{'.$single_css.'}';
	}
	if(!empty($portfolio_css)){
 	$css.='.hw-post-portfolio{'.$portfolio_css.'}';
	}
	$css.="@font-face {
			font-family: 'fontsite';
			font-weight:normal;
			font-weight:400;
			src: url('".get_template_directory_uri()."/fonts/fontsite.woff2') format('woff2');
	}";
	
	include_once hexwp_PATH . '/custom-css/lib/nav-responsive-category.php';
include_once hexwp_PATH . '/custom-css/lib/nav-responsive.php';
  	
	  
 	return $css;

      
}
  
 
	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Body Width
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
function hexwp_body_width($width) {
	wp_reset_postdata();
	wp_reset_query();
	global $smof_data,$post;
 
	$width=hexwp_option('body_width');
 
	return $width; 
}
add_action('sao_builder_section_width', 'hexwp_body_width');
  