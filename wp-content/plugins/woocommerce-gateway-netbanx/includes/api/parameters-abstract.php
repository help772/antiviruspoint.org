<?php

namespace WcPaysafe\Api;

use WcPaysafe\Api\Config\Configuration_Abstract;
use WcPaysafe\Api\Config\Direct;
use WcPaysafe\Api\Config\Redirect;
use WcPaysafe\Api\Request_Fields\Alternate_Payments_Fields;
use WcPaysafe\Api\Request_Fields\Card_Fields;
use WcPaysafe\Api\Request_Fields\Direct_Debit_Fields;
use WcPaysafe\Api\Request_Fields\Vault_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Parameters_Abstract {
	
	/**
	 * @var Card_Fields|Direct_Debit_Fields|Vault_Fields
	 */
	protected $fields;
	protected $configuration;
	
	/**
	 * Authorizations constructor.
	 *
	 * @param Card_Fields|Vault_Fields|Direct_Debit_Fields|Alternate_Payments_Fields $fields
	 * @param Direct|Redirect|Configuration_Abstract                                 $configuration
	 */
	public function __construct( $fields, $configuration ) {
		$this->fields        = $fields;
		$this->configuration = $configuration;
	}
	
	/**
	 * @return Card_Fields|Vault_Fields|Direct_Debit_Fields
	 */
	public function get_fields() {
		return $this->fields;
	}
	
	public function get_configuration() {
		return $this->configuration;
	}
}