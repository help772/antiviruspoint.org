<?php
 


/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Registers Custom Slider Post Type
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
if(function_exists( 'visualheader_constructor' ) ) {
 add_action( 'admin_menu', 'hexwp_header_admin_menu' );
function hexwp_header_admin_menu() {
	add_submenu_page(
		'hexwp_theme',
			__( 'Header Builder', 'hexwp' ),
			__( 'Header Builder', 'hexwp' ),
		'edit_pages',
		'edit.php?post_type=visualheader'
	);
 }
}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															include once
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
if ( is_admin()) {

include_once hexwp_PATH . '/admin/header-builder/options/header-options.php';
include_once hexwp_PATH . '/admin/header-builder/options/dropdown-options.php';
include_once hexwp_PATH . '/admin/header-builder/options/mobbar-options.php';
include_once hexwp_PATH . '/admin/header-builder/options/navbar-options.php';


 include_once hexwp_PATH. '/admin/header-builder/element/logo-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/menu-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/category_menu-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/account-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/search-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/social-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/contact_us-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/call-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/text_html-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/cart-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/wish-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/banner-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/mobile_menu-options.php';
include_once hexwp_PATH . '/admin/header-builder/element/mobile_category_menu-options.php';


} 	 
 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Builder Navbar
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
add_filter('vh_builder_navbar', 'hexwp_builder_navbar');
function hexwp_builder_navbar($navbar){
 
	$navbar['top']['value']= 'top';	
	$navbar['top']['name']= __('Top Header','hexwp');

	$navbar['middle']['value']= 'middle';	
	$navbar['middle']['name']= __('Middle Header','hexwp');
	
	$navbar['bottom']['value']= 'bottom';	
	$navbar['bottom']['name'] =__('Bottom Header','hexwp');
 
	$navbar['mobile_top']['value']= 'mobile_top';	
	$navbar['mobile_top']['name']= __('Mobile Top Header','hexwp');

	$navbar['mobile_middle']['value']= 'mobile_middle';	
	$navbar['mobile_middle']['name']= __('Mobile Middle Header','hexwp');
	
	$navbar['mobile_bottom']['value']= 'mobile_bottom';	
	$navbar['mobile_bottom']['name'] =__('Mobile Bottom Header','hexwp');
	return $navbar;
}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Builder Column
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
add_filter('vh_builder_column', 'hexwp_header_column');
 function hexwp_header_column($column=array()) {
	 
 	$column['top_left']['id']= '1_3';	
	$column['top_left']['child'] ='top';	
	$column['top_left']['side'] ='left';	
	
	$column['top_center']['id']= '1_3';	
	$column['top_center']['child'] ='top';
	$column['top_center']['side'] ='center';
		
	$column['top_right']['id'] ='1_3';	
	$column['top_right']['child'] ='top';	
	$column['top_right']['side'] ='right';
	
	$column['middle_left']['id']= '1_3';	
	$column['middle_left']['child'] ='middle';	
 	$column['middle_left']['side'] ='left';
	
	$column['middle_center']['id']= '1_3';
 	$column['middle_center']['child'] ='middle';	
	$column['middle_center']['side'] ='center';
	
	$column['middle_right']['id'] ='1_3';	
	$column['middle_right']['child'] ='middle';
	$column['middle_right']['side'] ='right';
	
	$column['bottom_left']['id']= '1_3';	
  	$column['bottom_left']['child'] ='bottom';	
  	$column['bottom_left']['side'] ='left';	
	
	
	$column['bottom_center']['id']= '1_3';	
	$column['bottom_center']['child'] ='bottom';
	$column['bottom_center']['side'] ='center';
	
		
	$column['bottom_right']['id'] ='1_3';	
	$column['bottom_right']['child'] ='bottom';
	$column['bottom_right']['side'] ='right';
		
	$column['mobile_top']['id']= '1_1';	
	$column['mobile_top']['child'] ='mobile_top';	
	$column['mobile_top']['side'] ='center';	
	
	
	$column['mobile_middle_left']['id']= '1_3';	
	$column['mobile_middle_left']['child'] ='mobile_middle';	
	$column['mobile_middle_left']['side'] ='left';	
	
	$column['mobile_middle_center']['id']= '1_3';	
 	$column['mobile_middle_center']['child'] ='mobile_middle';
 	$column['mobile_middle_center']['side'] ='center';
	
		
	$column['mobile_middle_right']['id'] ='1_3';	
	$column['mobile_middle_right']['child'] ='mobile_middle';
	$column['mobile_middle_right']['side'] ='right';
	
	$column['mobile_bottom']['id']= '1_1';	
	$column['mobile_bottom']['child'] ='mobile_bottom';	
	$column['mobile_bottom']['side'] ='center';	
 return $column;
	
}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Perview Custom Css
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
 add_filter('vh_header_perview_css', 'hexwp_builder_perview_custom_css');
function hexwp_builder_perview_custom_css($header_builder=false) {
 
  	$css='';
	$item_css='';
	$res_css='';
	$header_perview=true;
 	 
  	include_once hexwp_PATH . '/custom-css/lib/header-layout-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/header-element-css.php';
  	include_once hexwp_PATH . '/custom-css/lib/dropdown-css.php';
 	include_once hexwp_PATH . '/custom-css/lib/mobbar-css.php';
	 
	$css.=':root{'.$item_css.'}';
 	
 	include_once hexwp_PATH . '/custom-css/lib/nav-responsive.php';
 	echo '<style>'.$css.'</style>';
  
}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Perview Make Default
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
add_filter('vh_make_default', 'hexwp_header_builder_default');
function hexwp_header_builder_default($key) {
  
 
 	$old_data =	get_option('theme_mods_filson');
	
  	if(!empty($key)){
	$old_data['header_builder']=$key;
	}
	update_option( 'theme_mods_filson', $old_data );

		
}   
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Header Has Default
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
add_filter('vh_has_default', 'hexwp_header_has_default');
add_filter('vh_post_states_default', 'hexwp_header_has_default');
function hexwp_header_has_default($slug) {
  global $smof_data;
   if(hexwp_isset($smof_data,'header_builder')==$slug){
	  return true;
  }else{
	  return false;
   } 
}    
   
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Header frest Active
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Header frest Active
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/
$filson_activation=0;
if(is_admin()){
function hexwp_activation_hook() {
    $vh_first_active = get_option('filson_first_active');
	global $filson_activation;

    if (empty($vh_first_active)) {
		$filson_activation++;
		if($filson_activation==1){
 		update_option('filson_first_active', true);
 		$header_default = hexwp_header_default(array());
		$visualheader = $header_default['header_default']['value'];
 
 
		$title=__('Header Default','hexwp');

		$my_post = array(
			'post_title'    => wp_strip_all_tags( $title),
			'post_status' => 'publish',
			'post_name' => 'header-filson',
			
			'post_author'   => get_current_user_id(),
			'post_type'  => 'visualheader',
			'meta_input'    => array(
					'vh_builder_json' =>$visualheader,
				)
 		);

		wp_insert_post( $my_post ); 
		}
     }
	 
}
add_action('init', 'hexwp_activation_hook'); 
} 
 