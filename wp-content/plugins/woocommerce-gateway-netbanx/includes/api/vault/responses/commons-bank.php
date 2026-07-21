<?php

namespace WcPaysafe\Api\Vault\Responses;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Commons_Bank extends Commons_Vault {
	
	public function get_data_type() {
		return 'directdebit';
	}
	
	public function bank_type() {
		return '';
	}
	
	/**
	 * @return string
	 */
	public function get_account_type() {
		return $this->data->accountType;
	}
	
	/**
	 * @return string
	 */
	public function get_status_reason() {
		return $this->data->statusReason;
	}
	
	/**
	 * @return string
	 */
	public function get_account_number() {
		return $this->data->accountNumber;
	}
	
	/**
	 * @return string
	 */
	public function get_routing_number() {
		return $this->data->routingNumber;
	}
	
	/**
	 * @return string
	 */
	public function get_institution_id() {
		return '';
	}
}