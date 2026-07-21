<?php

namespace WcPaysafe\Api_Payments\Payments\Responses;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Monitor extends Commons {
	
	protected static $fieldTypes = array(
		'status' => 'READY',
	
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param string|\stdClass $response
	 */
	public function __construct( $response ) {
		parent::__construct( $response );
	}
}