<?php

namespace WcPaysafe\Api\Vault\Parameters;

use WcPaysafe\Api\Parameters_Abstract;
use WcPaysafe\Paysafe_Customer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Bacs extends Parameters_Abstract {
	
	/**
	 * @param $token
	 * @param $profile_id
	 *
	 * @return array|mixed
	 */
	public function create_from_single_use_token_parameters( $token, $profile_id ) {
		
		$params = array(
			'singleUseToken' => $token,
			'profileID'      => $profile_id,
		);
		
		$params = apply_filters( 'wc_paysafe_create_from_single_use_token_parameters', $params, $token, $profile_id, $this );
		
		wc_paysafe_add_debug_log( 'BACS: Create from single-use token parameters: ' . print_r( $params, true ) );
		
		return $params;
	}
	
	/**
	 * @param $profile_id
	 * @param $card_id
	 * @param $single_use_token
	 *
	 * @return array
	 */
	public function update_from_single_use_token_parameters( $profile_id, $card_id, $single_use_token ) {
		
		$params = array(
			'singleUseToken' => $single_use_token,
			'profileID'      => $profile_id,
			'id'             => $card_id,
		);
		
		$params = apply_filters( 'wc_paysafe_update_from_single_use_token_parameters', $params, $profile_id, $card_id, $single_use_token, $this );
		
		wc_paysafe_add_debug_log( 'BACS: Update from single-use token parameters: ' . print_r( $params, true ) );
		
		return $params;
	}
	
	/**
	 * @param \WC_Payment_Token|\WC_Payment_Token_Paysafe_DD $wc_token
	 *
	 * @return array
	 */
	public function delete_method_parameters( $wc_token ) {
		$profile_id = $wc_token->get_profile_id();
		if ( ! $profile_id ) {
			$paysafe_customer = new Paysafe_Customer( new \WP_User( $wc_token->get_user_id() ) );
			$profile_id       = $paysafe_customer->get_vault_profile_id();
		}
		
		$params = array(
			'profileID' => $profile_id,
			'id'        => $wc_token->get_source_id(),
		);
		
		$params = apply_filters( 'wc_paysafe_delete_method_parameters', $params, $wc_token, $this );
		
		wc_paysafe_add_debug_log( 'BACS: Delete token parameters: ' . print_r( $params, true ) );
		
		return $params;
	}
}