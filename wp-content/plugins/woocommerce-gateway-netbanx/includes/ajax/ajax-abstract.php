<?php

namespace WcPaysafe\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  1.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2016 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Ajax_Abstract {
	
	/**
	 * Check if the request is from an admin or a user that with sufficient rights. <br/>
	 * Verify the _wpnonce.
	 *
	 * @access private
	 * @since  1.0
	 *
	 * @param string $nonce
	 * @param string $action - Nonce the ajax action is performed with
	 *
	 * @return bool
	 */
	public function verify_request( $nonce, $action ) {
		$valid = true;
		
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			$valid = false;
		}
		
		return $valid;
	}
	
	/**
	 * Checks for user permissions level
	 *
	 * @param string $capability
	 *
	 * @return bool
	 */
	public function verify_access_permissions( $capability = 'administrator' ) {
		if ( current_user_can( $capability ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Return the Ajax response as JSON data.
	 *
	 * @param array $result
	 */
	public function return_as_json( array $result ) {
		wp_send_json( $result );
	}
}