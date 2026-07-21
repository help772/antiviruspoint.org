<?php

namespace WcPaysafe\Api_Payments;

use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
interface Service_Interface {
	
	/**
	 * @param Data_Source_Interface $source TODO: Type the source to either the Data_Source_Abstract or Data_Source_Interface
	 *
	 * @return mixed
	 */
	public function get_fields( $source );
	
	public function get_configuration();
}