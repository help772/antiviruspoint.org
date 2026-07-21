<?php

namespace WcPaysafe\Api_Payments;

use WcPaysafe\Api_Payments\Config\Configuration_Abstract;
use WcPaysafe\Api_Payments\Config\Redirect;
use WcPaysafe\Api_Payments\Request_Fields\Common_Fields;

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
abstract class Parameters_Abstract {
	
	/**
	 * @var Common_Fields
	 */
	protected $fields;
	protected $configuration;
	
	/**
	 * Authorizations constructor.
	 *
	 * @param Common_Fields                   $fields
	 * @param Redirect|Configuration_Abstract $configuration
	 */
	public function __construct( $fields, $configuration ) {
		$this->fields        = $fields;
		$this->configuration = $configuration;
	}
	
	/**
	 * @return Common_Fields
	 */
	public function get_fields() {
		return $this->fields;
	}
	
	public function get_configuration() {
		return $this->configuration;
	}
	
	public function allowed_payment_types() {
		return [
			'CARD',
			'APPLEPAY',
			'SKRILL',
			'NETELLER',
			'PAYSAFECASH',
			'PAYSAFECARD',
			'PAYPAL',
			'PAY BY BANK',
			'VENMO',
			'VIPPREFERRED',
			'MAZOOMA',
			'MBWAY',
			'MULTIBANCO',
			'SIGHTLINE',
			'INTERAC_ETRANSFER',
			'RAPID_TRANSFER',
			'SKRILL1TAP',
			'ACH',
			'EFT',
			'BACS',
			'SEPA',
			'ONLINE_BANK_TRANSFER',
			'PIX',
			'KHIPU',
			'MACH',
			'BOLETO_BANCARIO',
			'SAFETYPAY_CASH',
		];
	}
}