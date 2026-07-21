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
class Bacs extends Commons {
	
	protected $data;
	
	public function __construct( \Paysafe\DirectDebit\BACS $data ) {
		parent::__construct( $data );
	}
	
	public function bank_type() {
		return 'bacs';
	}
	
	/**
	 * @return string
	 */
	public function get_mandate_reference() {
		return $this->bank()->mandateReference;
	}
	
	/**
	 * @return string
	 */
	public function get_routing_number() {
		return $this->bank()->sortCode;
	}
}