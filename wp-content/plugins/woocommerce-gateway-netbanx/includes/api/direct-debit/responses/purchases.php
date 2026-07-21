<?php

namespace WcPaysafe\Api\Direct_Debit\Responses;

use Paysafe\DirectDebit\Purchase;
use WcPaysafe\Api\Response_Abstract;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Purchases extends Commons {
	
	protected static $fieldTypes = array(
		'id'             => 'string',
		'merchantRefNum' => 'string',
		'amount'         => 'string',
		'ach'            => '\Paysafe\DirectDebit\ACH',
		'eft'            => '\Paysafe\DirectDebit\EFT',
		'bacs'           => '\Paysafe\DirectDebit\BACS',
		'sepa'           => '\Paysafe\DirectDebit\SEPA',
		'profile'        => '\Paysafe\DirectDebit\Profile',
		'billingDetails' => '\Paysafe\DirectDebit\BillingDetails',
		'customerIp'     => 'string',
		'dupCheck'       => 'bool',
		'txnTime'        => 'string',
		'currencyCode'   => 'string',
		'error'          => '\Paysafe\Error',
		'status'         => array(
			'RECEIVED',
			'PENDING',
			'PROCESSING',
			'COMPLETED',
			'FAILED',
			'CANCELLED'
		),
		'links'          => 'array:\Paysafe\Link',
		'splitpay'       => 'array:\Paysafe\DirectDebit\SplitPay',
	);
	public $bank;
	public $bank_account_type;
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Purchase $response
	 */
	public function __construct( Purchase $response ) {
		parent::__construct( $response );
	}
	
	/**
	 * Returns true of the response contains direct debit data
	 *
	 * @return bool
	 */
	public function is_direct_debit() {
		return true;
	}
	
	/**
	 * Last digits on the Purchase object are the last digits of the account number
	 * @return string
	 */
	public function get_last_digits() {
		return str_pad( substr( $this->bank()->get_account_number() ? $this->bank()->get_account_number() : '****', - 4 ), 4, '*', STR_PAD_LEFT );
	}
	
	public function bank() {
		if ( null === $this->bank ) {
			if ( isset( $this->get_data()->ach ) ) {
				$this->bank = new Ach( $this->get_data()->ach );
			} else if ( isset( $this->get_data()->eft ) ) {
				$this->bank = new Eft( $this->get_data()->eft );
			} else if ( isset( $this->get_data()->bacs ) ) {
				$this->bank = new Bacs( $this->get_data()->bacs );
			} else if ( isset( $this->get_data()->sepa ) ) {
				$this->bank = new Sepa( $this->get_data()->sepa );
			}
		}
		
		return $this->bank;
	}
	
	/**
	 * @return string
	 */
	public function bank_type() {
		if ( null === $this->bank_account_type ) {
			if ( isset( $this->get_data()->ach ) ) {
				$this->bank_account_type = 'ach';
			} else if ( isset( $this->get_data()->eft ) ) {
				$this->bank_account_type = 'eft';
			} else if ( isset( $this->get_data()->bacs ) ) {
				$this->bank_account_type = 'bacs';
			} else if ( isset( $this->get_data()->sepa ) ) {
				$this->bank_account_type = 'sepa';
			}
		}
		
		return $this->bank_account_type;
	}
	
	public function __get( $name ) {
		if ( 'ach' == $name || 'bacs' == $name || 'sepa' == $name || 'eft' == $name ) {
			return $this->bank();
		}
		
		return parent::__get( $name );
	}
}