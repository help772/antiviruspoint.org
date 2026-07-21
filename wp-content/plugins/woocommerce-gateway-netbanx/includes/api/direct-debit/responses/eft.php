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
class Eft extends Commons {
	
	protected $data;
	
	public function __construct( \Paysafe\DirectDebit\EFT $data ) {
		parent::__construct( $data );
	}
	
	public function bank_type() {
		return 'eft';
	}
	
	/**
	 * @return string
	 */
	public function get_institution_id() {
		return $this->bank()->institutionId;
	}
	
	/**
	 * @return string
	 */
	public function get_account_number() {
		return $this->bank()->accountNumber;
	}
}