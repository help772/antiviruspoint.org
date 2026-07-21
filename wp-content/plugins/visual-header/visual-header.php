<?php
/**
 * Plugin Name: Visual Header
 * Description: You To Live Build Any Header Layout You Exactly Need. Intuitive And Easy To Use Drag & Drop Builder.
 * Version: 1.5.2
 * Author: visual-header
 * Text Domain: visual-header
 * Domain Path: /languages/ 
 * License:GPLv2 or later
 * Requires at least: 6.0
 * Requires PHP: 7.4.0
 * License URI:https://www.gnu.org/licenses/gpl-2.0.html
/* Plugin Framework Version Check */
 
 defined('ABSPATH') || exit;
if( ! function_exists( 'visualheader_constructor' ) ) {
    function visualheader_constructor() {

 		 if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
            load_plugin_textdomain( 'visual-header' );
        } else {
            load_textdomain( 'visual-header', plugin_dir_path(__FILE__) . 'languages/visual-header-' . determine_locale() . '.mo' );
        }
  
    }
}
add_action( 'visualheader_init', 'visualheader_constructor' );

 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																 Slider Install
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( ! function_exists( 'visualheader_install' ) ) {
function visualheader_install() {

     
            do_action( 'visualheader_init' );
}
 
add_action( 'plugins_loaded', 'visualheader_install', 1 );
}

/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															VISUALHEADER_PATH
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
 
 
if( !defined('VISUALHEADER_PATH') ){
	define( 'VISUALHEADER_PATH', plugin_dir_path(__FILE__) );
}
if( !defined('VISUALHEADER_DIR') ){
	define( 'VISUALHEADER_DIR', plugin_dir_url(__FILE__) );
}	
if( !defined('VISUALHEADER_FILE') ){
	define( 'VISUALHEADER_FILE', dirname( __FILE__ ) );
}	
  
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Registers Custom Slider Post Type
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
if( ! function_exists( 'vh_post_type' ) ) {
add_action( 'init', 'vh_post_type' );
function vh_post_type() {
	$labels = array(
		'name' 					=> __('Header Builder','visual-header'),
		'singular_name'			=> __('Header Builder','visual-header'),
		'add_new'				=> __('Add New','visual-header'),
		'add_new_item'			=>__('Add New Header Builder','visual-header'),
		'edit_item'				=> __('Edit Header Builder','visual-header'),
		'new_item'				=> __('New Header Builder','visual-header'),
		'view_item'				=> __('View Header Builder','visual-header'),
 		'all_items'				=>__('All Header Builder','visual-header'),
 		'search_items'			=> __('Search Header Builder','visual-header'),
		'not_found'				=> __('Header Builder not found','visual-header'),
		'not_found_in_trash'	=> __('The Header Builder was found in the trash','visual-header'),
		'parent_item_colon'		=> '',
		'menu_name'				=> __('Header Builder','visual-header')
	);
	
 
	$args = array(
		'labels'				=> $labels,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'show_in_nav_menus'   => false,
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'menu_position'			=> null,
		'supports' => array( 'title' )
	); 
	
	 
	register_post_type( 'visualheader', $args );
}
}

 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															include once
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/  
if ( is_admin()) {

 	include_once VISUALHEADER_PATH . 'framework/duplicate.php';
 	include_once VISUALHEADER_PATH . 'framework/metabox.php';
  	include_once VISUALHEADER_PATH . 'framework/builder.php';
  	include_once VISUALHEADER_PATH . 'framework/builder-navbar.php';
	include_once VISUALHEADER_PATH . 'framework/builder-column.php';
	include_once VISUALHEADER_PATH . 'framework/builder-element.php'; 
	include_once VISUALHEADER_PATH . 'framework/library.php'; 
	include_once VISUALHEADER_PATH . 'framework/default.php';
	
 
 } 	 
 
include_once VISUALHEADER_PATH . 'framework/perview.php';
 
if( ! function_exists( 'vh_isset' ) ) {
 function vh_isset($option=false,$id=false,$default = false){
   	return isset( $option[$id] ) ? $option[$id] : $default;
}   
}
if( ! function_exists( 'vh_isset_2' ) ) {
 function vh_isset_2($option=false,$id=false,$id_2=false,$default = false){
   return isset( $option[$id][$id_2] ) ? $option[$id][$id_2] : $default;
 
}  
}
 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 															Text
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if( ! function_exists( 'vh_text' ) ) {
function vh_text() {
 	return array(
		'saved'					=>	__('Header is Saved','visual-header'), 
		'vh_header'				=>	__('Header','visual-header'), 
		'defaulted'				=>	__('Header is Defaulted','visual-header'), 
		'imported'				=>	__('Header is Imported','visual-header'), 
		'choose'				=>	__('Choose Image','visual-header'), 
		'remove'				=>	__('Remove','visual-header'),
		'uploader_button'		=>	__('Use This Image','visual-header'), 
		'empty'					=>	__('Name is empty!','visual-header'), 
		'change_column'			=>	__('Change the Column','visual-header'), 
		'retry'					=>	__('Name already exist!','visual-header'), 
		'agree'					=>	__('Do you agree?','visual-header'), 
		'error'					=>	__('Error','visual-header'), 
		'general'				=>	__('General','visual-header'), 
		'cancel'				=>	__('Cancel','visual-header'), 
		'update'				=>	__('Update','visual-header'), 
		'addelement'			=>	__('Add Element','visual-header'), 
		'option'			 	=>	__('Options','visual-header'), 
		'export_header'			=>	__('Export Header','visual-header'), 
		'export_json'			=>	__('Copy The Code From The Following Text Area And Save It. You Will Be Able To Import It Later With Our Import Function In The Headers Manager.','visual-header'), 
		'vh_import'				=>	__('Import','visual-header'), 
		'import_header'			=>	__('Import Header','visual-header'), 
		'import_json'			=>	__('Paste Your Json Header Export Data Here And Click "Import"','visual-header'),
		'import_library'		=>	__('Import of the Library','visual-header'), 
		'upload'		=>	__('Upload','visual-header'), 
	);
 }
}

if( ! function_exists( 'vh_enqueue_register' ) ) {
add_action( 'wp_enqueue_scripts', 'vh_enqueue_register' ,999999); 
function vh_enqueue_register() {
	$var='1.4';
	wp_enqueue_style( 'vh_header_css', VISUALHEADER_DIR .'assets/css/header.css',[],$var,false ); 

	 if (!current_user_can('manage_options')) return;
 	wp_enqueue_style( 'vh_preview_css', VISUALHEADER_DIR .'assets/css/preview.css',[],$var,false ); 
  
}

}
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 														Admin  Enqueue scripts
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/ 
if( ! function_exists( 'vh_builder_enqueue' ) ) {
add_action('admin_enqueue_scripts', 'vh_builder_enqueue');
function vh_builder_enqueue($hook) {
	global $pagenow,$post; 
   	$var='1.5.2';
 	$min ='';
 	$post_type=wp_unslash(filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS ));
 	$vh_post_id=wp_unslash(filter_input( INPUT_GET, 'vh_post_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS ));
  	if (($pagenow == 'post.php' && get_post_type() === 'visualheader') || 
		($pagenow == 'post-new.php' &&  $post_type  === 'visualheader') ||
		($pagenow == 'customize.php' && !empty($vh_post_id))) {
 		
			wp_enqueue_style('vh_builder', VISUALHEADER_DIR .'assets/css/builder.css','',$var,false);
 			wp_enqueue_style('vh_model', VISUALHEADER_DIR .'assets/css/model.css','',$var,false);
			wp_enqueue_style('vh_options', VISUALHEADER_DIR .'assets/css/options.css','',$var,false);
			
 			 
			wp_enqueue_media();
			wp_enqueue_script('jquery-ui-slider');
			wp_enqueue_script( 'serializejson', VISUALHEADER_DIR .'assets/js/jquery.serializejson.min.js',[],$var,false); 
			 

 	
			wp_enqueue_style( 'rang_coloris',VISUALHEADER_DIR .'assets/css/coloris.css',[],$var,false); 
			wp_enqueue_script( 'rang_coloris', VISUALHEADER_DIR .'assets/js/coloris.js',[],$var,false); 
			wp_localize_script('rang_coloris', 'rang_coloris',array('rtl'=>is_rtl()?'true':''));
				
 
 				
 
 
			wp_enqueue_script('vh_options_functions', VISUALHEADER_DIR .'assets/js/options-functions.js', array('jquery') ,$var ,false);
			
			
 			
			
			wp_enqueue_script('vh_builder', VISUALHEADER_DIR .'assets/js/builder.js', array('jquery', 'jquery-ui-sortable') ,$var,false );
			
			
 			 
			$builder_js = array( 'ajaxurl' => admin_url( 'admin-ajax.php'));
 		
 			wp_localize_script( 'vh_builder', 'vh_builder_js', $builder_js  );	
			wp_localize_script('vh_builder', 'vh_text',vh_text()) ; 
			$post_id='';
		 
			 
			if(!empty( get_the_ID())){
				$post_id = get_the_ID();	
			}
			wp_localize_script('vh_builder', 'vh_settings', array(
				'global'	=>vh_global_options_array(),
				'navbar'	=> !empty(apply_filters('vh_navbar_options',''))?apply_filters('vh_navbar_options',''):'',
 				'element' 	=> vh_element_options_array(),
				'nonce'		=> wp_create_nonce( 'vh_header_nonce' )  ,
				'post_id'		=>$post_id,

 
			)); 
			wp_enqueue_script('vh_options', VISUALHEADER_DIR .'assets/js/options.js', array('jquery') ,$var,false );
 	} 
	
   	wp_enqueue_style('vh_duplicate', VISUALHEADER_DIR .'assets/css/duplicate.css','',$var,false);
  	wp_enqueue_script('vh_duplicate', VISUALHEADER_DIR .'assets/js/duplicate.js', array('jquery') ,$var,false );
 	wp_localize_script('vh_duplicate', 'vh_duplicate', array(
		 'ajaxurl'		 => admin_url( 'admin-ajax.php'),
 		'error'			=>	esc_html__('Error','visual-header'), 
		'duplicated'	=>	esc_html__('Duplicated','visual-header'), 
		'nonce'			=> wp_create_nonce( 'vh_duplicate_nonce' )  ,
	));
 	  
 }  
} 
 
 
 