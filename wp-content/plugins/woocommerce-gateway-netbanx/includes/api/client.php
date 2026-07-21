<?php

namespace WcPaysafe\Api;

use Paysafe\PaysafeApiClient;
use WcPaysafe\Api\Config\Configuration_Abstract;
use WcPaysafe\Api\Config\Redirect;
use WcPaysafe\Gateways\Redirect\Gateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles general routing to the appropriate integration service.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Client {
	
	/**
	 * @var PaysafeApiClient
	 */
	protected $sdk;
	protected $config;
	
	/**
	 * @param PaysafeApiClient               $sdk
	 * @param Configuration_Abstract|Gateway $config
	 *
	 * @throws \Exception
	 */
	public function __construct( $sdk, $config ) {
		$this->sdk    = $sdk;
		$this->config = $config;
	}
	
	public function sdk() {
		return $this->sdk;
	}
	
	/**
	 * @return Configuration_Abstract|Redirect
	 */
	public function get_config() {
		return $this->config;
	}
	
	/**===============================
	 * Services
	 * ================================*/
	
	/**
	 * Returns the Card Services object
	 *
	 * @since 3.3.0
	 *
	 * @return Cards\Service
	 */
	public function get_cards_service() {
		return new Cards\Service( $this->sdk(), $this->get_config() );
	}
	
	/**
	 * Returns the Vault Services
	 *
	 * @since 3.3.0
	 *
	 * @return Vault\Service
	 */
	public function get_vault_service() {
		return new Vault\Service( $this->sdk(), $this->get_config() );
	}
	
	/**
	 * Returns the Post Services object
	 *
	 * @since 3.3.0
	 *
	 * @return Direct_Debit\Service
	 */
	public function get_direct_debit_service() {
		return new Direct_Debit\Service( $this->sdk(), $this->get_config() );
	}
	
	/**
	 * @since 3.3.0
	 * @return Alternate_Payments\Service
	 */
	public function get_alternate_payment_service() {
		return new Alternate_Payments\Service( $this->sdk(), $this->get_config() );
	}
	
	/**
	 * @return Checkoutjs\Service
	 */
	public function get_checkoutjs_service() {
		return new Checkoutjs\Service( $this->sdk(), $this->get_config() );
	}
}