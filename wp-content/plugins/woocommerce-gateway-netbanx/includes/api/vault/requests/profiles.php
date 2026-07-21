<?php

namespace WcPaysafe\Api\Vault\Requests;

use Paysafe\CustomerVault\Profile;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Request_Abstract;
use WcPaysafe\Api\Request_Fields\Vault_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper for the SDK Vault services.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Profiles extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return \WcPaysafe\Api\Vault\Parameters\Profiles
	 */
	public function get_request_builder( $source ) {
		return new \WcPaysafe\Api\Vault\Parameters\Profiles( new Vault_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Vault\Responses\Profiles
	 * @throws \Paysafe\PaysafeException
	 */
	public function create( $params ) {
		return new \WcPaysafe\Api\Vault\Responses\Profiles( $this->sdk->customerVaultService()->createProfile(
			new Profile( $params )
		) );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Vault\Responses\Profiles
	 * @throws \Paysafe\PaysafeException
	 */
	public function update( $params ) {
		return new \WcPaysafe\Api\Vault\Responses\Profiles( $this->sdk->customerVaultService()->updateProfile(
			new Profile( $params )
		) );
	}
	
	/**
	 * @param      $params
	 * @param bool $include_addresses
	 * @param bool $include_cards
	 * @param bool $include_ach
	 * @param bool $include_eft
	 * @param bool $include_bacs
	 * @param bool $include_sepa
	 *
	 * @return \WcPaysafe\Api\Vault\Responses\Profiles
	 * @throws \Paysafe\PaysafeException
	 */
	public function get(
		$params,
		$include_addresses = false,
		$include_cards = false,
		$include_ach = false,
		$include_eft = false,
		$include_bacs = false,
		$include_sepa = false
	) {
		return new \WcPaysafe\Api\Vault\Responses\Profiles( $this->sdk->customerVaultService()->getProfile(
			new Profile( $params ),
			$include_addresses,
			$include_cards,
			$include_ach,
			$include_eft,
			$include_bacs,
			$include_sepa
		) );
	}
	
	/**
	 * @param $params
	 *
	 * @return bool
	 * @throws \Paysafe\PaysafeException
	 */
	public function delete( $params ) {
		return $this->sdk->customerVaultService()->deleteProfile(
			new Profile( $params )
		);
	}
}