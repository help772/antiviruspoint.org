<?php

namespace WcPaysafe\Api_Payments;

use Paysafe\PaysafeApiClient;
use WcPaysafe\Api_Payments\Config\Configuration_Abstract;
use WcPaysafe\Api_Payments\Config\Redirect;
use WcPaysafe\Gateways\Redirect\Gateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles general routing to the appropriate integration service.
 *
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Client {
	
	/**
	 * @var PaysafeApiClient
	 */
	protected $config;
	protected $account_id;
	
	/**
	 * @param Configuration_Abstract|Gateway $config
	 * @param string                         $account_id
	 *
	 * @throws \Exception
	 */
	public function __construct( $config, $account_id ) {
		$this->config = $config;
		
		if ( $account_id ) {
			$this->set_account_id( $account_id );
		}
	}
	
	/**
	 * @return Configuration_Abstract|Redirect
	 */
	public function get_config() {
		return $this->config;
	}
	
	public function set_account_id( $account_id ) {
		$this->account_id = $account_id;
	}
	
	public function get_account_id() {
		return $this->account_id;
	}
	
	/**===============================
	 * Services
	 * ================================*/
	
	/**
	 * @since 4.0.0
	 *
	 * @return Payments\Service
	 */
	public function get_cards_service() {
		return new Payments\Service( $this->get_config() );
	}
	
	/**
	 * Returns the Card Services object
	 *
	 * @since 4.0.0
	 *
	 * @return Payments\Service
	 */
	public function get_payments_service() {
		return new Payments\Service( $this->get_config() );
	}
	
	/**
	 * @return \WcPaysafe\Api_Payments\Refunds\Service
	 */
	public function get_refunds_service() {
		return new Refunds\Service( $this->get_config() );
	}
	
	/**
	 * @return \WcPaysafe\Api_Payments\Settlements\Service
	 */
	public function get_settlements_service() {
		return new Settlements\Service( $this->get_config() );
	}
	
	/**
	 * @return Checkoutjs_V2\Service
	 */
	public function get_checkoutjs_service() {
		return new Checkoutjs_V2\Service( $this->get_config() );
	}
	
	/**
	 * @return \WcPaysafe\Api_Payments\Customers\Service
	 */
	public function get_customers_service() {
		return new Customers\Service( $this->get_config() );
	}
	
	/**
	 * @return \WcPaysafe\Api_Payments\Payment_Handles\Service
	 */
	public function get_payment_handles_service() {
		return new Payment_Handles\Service( $this->get_config() );
	}
}