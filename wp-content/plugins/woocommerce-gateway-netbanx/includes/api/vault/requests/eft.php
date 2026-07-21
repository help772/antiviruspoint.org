<?php

namespace WcPaysafe\Api\Vault\Requests;

use Paysafe\CustomerVault\EFTBankaccounts;
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
class Eft extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return \WcPaysafe\Api\Vault\Parameters\Eft
	 */
	public function get_request_builder( $source ) {
		return new \WcPaysafe\Api\Vault\Parameters\Eft( new Vault_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Vault\Responses\Eft
	 * @throws \Paysafe\PaysafeException
	 */
	public function create_from_single_use_token( $params ) {
		return new \WcPaysafe\Api\Vault\Responses\Eft( $this->sdk->customerVaultService()->createEFTBankAccountFromSingleUseToken(
			new EFTBankaccounts( $params )
		) );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Vault\Responses\Eft
	 * @throws \Paysafe\PaysafeException
	 */
	public function create( $params ) {
		return new \WcPaysafe\Api\Vault\Responses\Eft( $this->sdk->customerVaultService()->createEFTBankAccount(
			new EFTBankaccounts( $params )
		) );
	}
	
	/**
	 *
	 *
	 * @param $params
	 * @param $bank_account_params
	 *
	 * @return bool
	 * @throws \Paysafe\PaysafeException
	 */
	public function delete( $params ) {
		return $this->sdk->customerVaultService()->deleteEFTBankAccount(
			new EFTBankaccounts( $params )
		);
	}
	
	/**
	 * @param $params
	 * @param $bank_account_params
	 *
	 * @return \WcPaysafe\Api\Vault\Responses\Eft
	 * @throws \Paysafe\PaysafeException
	 */
	public function update( $params ) {
		return new \WcPaysafe\Api\Vault\Responses\Eft( $this->sdk->customerVaultService()->updateEFTBankAccount(
			new EFTBankaccounts( $params )
		) );
	}
	
	/**
	 * @param $params
	 * @param $bank_account_params
	 *
	 * @return \WcPaysafe\Api\Vault\Responses\Eft
	 * @throws \Paysafe\PaysafeException
	 */
	public function get( $params ) {
		return new \WcPaysafe\Api\Vault\Responses\Eft( $this->sdk->customerVaultService()->getEFTBankAccount(
			new EFTBankaccounts( $params )
		) );
	}
}