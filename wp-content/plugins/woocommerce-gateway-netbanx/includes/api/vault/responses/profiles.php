<?php

namespace WcPaysafe\Api\Vault\Responses;

use Paysafe\CustomerVault\Profile;
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
class Profiles extends Response_Abstract {
	
	protected static $fieldTypes = array(
		'id'                 => 'string',
		'status'             => array(
			'INITIAL',
			'ACTIVE'
		),
		'merchantCustomerId' => 'string',
		'locale'             => array(
			'en_US',
			'fr_CA',
			'en_GB'
		),
		'firstName'          => 'string',
		'middleName'         => 'string',
		'lastName'           => 'string',
		'dateOfBirth'        => '\Paysafe\CustomerVault\DateOfBirth',
		'ip'                 => 'string',
		'gender'             => array(
			'M',
			'F'
		),
		'nationality'        => 'string',
		'email'              => 'email',
		'phone'              => 'string',
		'cellPhone'          => 'string',
		'paymentToken'       => 'string',
		'addresses'          => 'array:\Paysafe\CustomerVault\Address',
		'card'               => '\Paysafe\CustomerVault\Card',
		'cards'              => 'array:\Paysafe\CustomerVault\Card',
		'error'              => '\Paysafe\Error',
		'links'              => 'array:\Paysafe\Link',
		'achBankAccounts'    => 'array:\Paysafe\CustomerVault\ACHBankaccounts',
		'bacsBankAccounts'   => 'array:\Paysafe\CustomerVault\BACSBankaccounts',
		'eftBankAccounts'    => 'array:\Paysafe\CustomerVault\EFTBankaccounts',
		'sepaBankAccounts'   => 'array:\Paysafe\CustomerVault\SEPABankaccounts'
	);
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param Profile $response
	 */
	public function __construct( Profile $response ) {
		parent::__construct( $response );
	}
	
	public function __get( $name ) {
		$check_name = strtolower( $name );
		
		if ( 'achbankaccounts' == $check_name ) {
			return $this->get_ach();
		} elseif ( 'bacsbankaccounts' == $check_name ) {
			return $this->get_bacs();
		} elseif ( 'sepabankaccounts' == $check_name ) {
			return $this->get_sepa();
		} elseif ( 'eftbankaccounts' == $check_name ) {
			return $this->get_eft();
		} elseif ( 'cards' == $check_name ) {
			return $this->get_cards();
		}
		
		return parent::__get( $name );
	}
	
	public function get_ach() {
		return $this->load_array_into_object( $this->get_data()->achBankAccounts, '\\WcPaysafe\\Api\\Vault\\Responses\\Ach' );
	}
	
	public function get_bacs() {
		return $this->load_array_into_object( $this->get_data()->bacsBankAccounts, '\\WcPaysafe\\Api\\Vault\\Responses\\Bacs' );
	}
	
	public function get_sepa() {
		return $this->load_array_into_object( $this->get_data()->sepaBankAccounts, '\\WcPaysafe\\Api\\Vault\\Responses\\Sepa' );
	}
	
	public function get_eft() {
		return $this->load_array_into_object( $this->get_data()->eftBankAccounts, '\\WcPaysafe\\Api\\Vault\\Responses\\Eft' );
	}
	
	public function get_cards() {
		if ( ! isset( $this->cards ) ) {
			$this->cards = $this->load_array_into_object( $this->get_data()->cards, '\\WcPaysafe\\Api\\Vault\\Responses\\Cards' );
		}
		
		return $this->cards;
	}
	
	public function load_array_into_object( array $array, $object_name ) {
		$items = array();
		if ( $array ) {
			foreach ( $array as $item ) {
				$items[] = new $object_name( $item );
			}
		}
		
		return $items;
	}
}