<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Abstract Class
 * A helper class for action and filter hooks
 *
 * @since 1.0.0
 * @version 10001.0.0
 *
 */
 if ( !class_exists( 'hexwpdemoimport_Importer_Abstract' ) ) {

abstract class hexwpdemoimport_Importer_Abstract {
  public function __construct() {}
  public function addAction( $hook, $function_to_add, $priority = 30, $accepted_args = 1 ) {
    add_action( $hook, array( &$this, $function_to_add), $priority, $accepted_args );
  }
  public function addFilter( $tag, $function_to_add, $priority = 30, $accepted_args = 1 ) {
    add_action( $tag, array( &$this, $function_to_add), $priority, $accepted_args );
  }
}
 }