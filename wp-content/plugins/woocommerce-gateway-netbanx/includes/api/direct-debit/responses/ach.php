<?php

namespace WcPaysafe\Api\Direct_Debit\Responses;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Ach extends Commons {
	
	protected $data;
	
	public function __construct( \Paysafe\DirectDebit\ACH $data ) {
		parent::__construct( $data );
	}
	
	public function bank_type() {
		return 'ach';
	}
}