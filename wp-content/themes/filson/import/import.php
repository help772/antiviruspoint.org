<?php
 /*
Plugin Name: filson Demo Import
Description: import demo theme hexwp
Author: hex-wp 
Version: 1.0
License: GPLv2 or later
Text Domain: hexwpdemoimport
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more dSTails.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin StreST, Fifth Floor, Boston, MA  02110-1301, USA.
 
*/


if( !defined('hexwp_DI_PATH') ){
	define( 'hexwp_DI_PATH', hexwp_PATH . '/import/');
}
if( !defined('hexwp_DI_DIR') ){
	define( 'hexwp_DI_DIR', hexwp_DIR . '/import/');
}	

if( ! function_exists( 'hexwpdemoimport_importer_get_path_locate' ) ) {
  function hexwpdemoimport_importer_get_path_locate() {
    $dirname        = wp_normalize_path( dirname( __FILE__ ) );
    $plugin_dir     = wp_normalize_path( WP_PLUGIN_DIR );
    $located_plugin = ( preg_match( '#'. $plugin_dir .'#', $dirname ) ) ? true : false;
    $directory      = ( $located_plugin ) ? $plugin_dir : hexwp_DI_PATH;
    $directory_uri  = ( $located_plugin ) ? WP_PLUGIN_URL : hexwp_DI_DIR;
    $basename       = str_replace( wp_normalize_path( $directory ), '', $dirname );
    $dir            = $directory . $basename;
    $uri            = $directory_uri . $basename;
    return apply_filters( 'hexwp_DI_IMPORTER_get_path_locate', array(
      'basename' => wp_normalize_path( $basename ),
      'dir'      => wp_normalize_path( $dir ),
      'uri'      => $uri
    ) );
  }
}

/**
 * Scripts and styles for admin
 */
if ( ! function_exists( 'hexwpdemoimport_enqueue_scripts' ) ) {

function hexwpdemoimport_enqueue_scripts() {

    wp_enqueue_script( 'hexwpdemoimport-import-js', hexwp_DI_DIR . 'assets/js/dt-importer.js', array( 'jquery' ), '25', true);
    wp_enqueue_style( 'hexwpdemoimport-importer-css', hexwp_DI_DIR . 'assets/css/dt-importer.css', null);
}

add_action( 'admin_enqueue_scripts', 'hexwpdemoimport_enqueue_scripts' );
}
/**
 *
 * Decode string for backup options (Source from codestar)
 *
 * @since 1.0.0
 * @version 10001.0.0
 *
 */
if ( ! function_exists( 'cs_decode_string' ) ) {
  function cs_decode_string( $string ) {
    return unserialize( gzuncompress( stripslashes( call_user_func( 'base'. '64' .'_decode', rtrim( strtr( $string, '-_', '+/' ), '=' ) ) ) ) );
  }
}

/**
 * Importer constants
 */

$get_path = hexwpdemoimport_importer_get_path_locate();

define( 'hexwp_DI_IMPORTER_VER' , '1.0.0' );
define( 'hexwp_DI_IMPORTER_DIR' , $get_path['dir'] );
define( 'hexwp_DI_IMPORTER_URI' , $get_path['uri'] );
define( 'hexwp_DI_IMPORTER_CONTENT_DIR' , hexwp_PATH . '/import/demos/' );
define( 'hexwp_DI_IMPORTER_CONTENT_URI' , hexwp_DIR . '/import/demos/' );
 
if( ! function_exists( 'hexwpdemoimport_number_replace' ) ) {
 function hexwpdemoimport_number_replace($English_Number){
 
		return $English_Number;
	 
 }

}
  
  
 


require_once hexwp_DI_PATH . 'classes/abstract.class.php';
require_once hexwp_DI_PATH . 'classes/importer.class.php';
require_once hexwp_DI_PATH . 'classes/import.php';
require_once hexwp_DI_PATH . 'classes/widgets.php';
require_once hexwp_DI_PATH . 'classes/menu-export.php';
 
include_once hexwp_DI_PATH . 'demo.php';
  