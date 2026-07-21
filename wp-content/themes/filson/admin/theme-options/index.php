<?php
/*
Title		: SMOF
Description	: Slightly Modified Options Framework
Version		: 1.5.2
Author		: Syamil MJ
  
 
*/

define( 'SMOF_VERSION', '1.5.2' );

/**
 * Definitions
 *
 * @since 1.4.0
 */
$smof_theme_version = '';
$smof_output = '';
	    
if( function_exists( 'wp_get_theme' ) ) {
	if( is_child_theme() ) {
		$smof_temp_obj = wp_get_theme();
		$smof_theme_obj = wp_get_theme( $smof_temp_obj->get('Template') );
	} else {
		$smof_theme_obj = wp_get_theme();    
	}

	$smof_theme_version = $smof_theme_obj->get('Version');
	$smof_theme_name = $smof_theme_obj->get('Name');
	$smof_theme_uri = $smof_theme_obj->get('ThemeURI');
	$smof_author_uri = $smof_theme_obj->get('AuthorURI');
} else {
	$smof_theme_data = wp_get_theme( hexwp_PATH.'/style.css' );
	$smof_theme_version = $smof_theme_data['Version'];
	$smof_theme_name = $smof_theme_data['Name'];
	$smof_theme_uri = $smof_theme_data['ThemeURI'];
	$smof_author_uri = $smof_theme_data['AuthorURI'];
}



if( !defined('ADMIN_PATH') )
	define( 'ADMIN_PATH', hexwp_PATH . '/admin/theme-options/' );
if( !defined('ADMIN_DIR') )
	define( 'ADMIN_DIR', hexwp_DIR . '/admin/theme-options/' );

define( 'ADMIN_IMAGES', hexwp_DIR . '/admin/assets/images/' );

define( 'THEMENAME', $smof_theme_name );
/* Theme version, uri, and the author uri are not completely necessary, but may be helpful in adding functionality */
define( 'THEMEVERSION', $smof_theme_version );
define( 'THEMEURI', $smof_theme_uri );
define( 'THEMEAUTHORURI', $smof_author_uri );

define( 'BACKUPS','backups' );

/**
 * Required action filters
 *
 * @uses add_action()
 *
 * @since 1.0.0
 */
add_action('admin_head', 'optionsframework_admin_message');
add_action('admin_init','optionsframework_admin_init');
add_action('admin_menu', 'optionsframework_add_admin');
 
/**
 * Required Files
 *
 * @since 1.0.0
 */ 
 // hexwp Edit

 
// End hexwp Edit

/**
 * AJAX Saving Options
 *
 * @since 1.0.0
 */ 
include_once hexwp_PATH . '/admin/theme-options/functions.filters.php'; 
include_once hexwp_PATH . '/admin/theme-options/functions.interface.php'; 
include_once hexwp_PATH . '/admin/theme-options/functions.admin.php'; 
include_once hexwp_PATH. '/admin/theme-options/class.options_machine.php'; 
 

add_action('wp_ajax_of_ajax_post_action', 'of_ajax_callback');

