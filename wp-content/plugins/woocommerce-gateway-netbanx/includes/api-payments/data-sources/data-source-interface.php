<?php

namespace WcPaysafe\Api_Payments\Data_Sources;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
interface Data_Source_Interface {
	
	public function get_source();
	
	public function get_source_type();
	
	public function get_address_field( $name, $type = 'billing' );
	
	public function get_description();
}