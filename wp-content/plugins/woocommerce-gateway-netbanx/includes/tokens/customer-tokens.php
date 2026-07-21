<?php

namespace WcPaysafe\Tokens;

use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Response_Abstract;
use WcPaysafe\Api\Vault\Responses\Commons_Bank;
use WcPaysafe\Api\Vault\Responses\Cards as VaultCards;
use WcPaysafe\Api\Vault\Responses\Commons_Vault;
use WcPaysafe\Api_Payments\Payments\Responses\Payments_Card_Token_Wrapper;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Customer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the Paysafe Customer Tokens.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2017 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Customer_Tokens {
	
	public $gateway_id;
	public $user_id;
	
	/**
	 * Customer_Tokens constructor.
	 *
	 * @param int    $user_id
	 * @param string $gateway_id The gateway string ID. This allows for different gateways to use the same methods, i.e. paysafe_direct.
	 */
	public function __construct( $user_id, $gateway_id = 'netbanx' ) {
		$this->user_id    = (int) $user_id;
		$this->gateway_id = $gateway_id;
	}
	
	/**---------------------------------
	 * GETTERS
	 * -----------------------------------*/
	
	/**
	 * Wrapper of get_customer_tokens
	 *
	 * @since 3.3.0
	 *
	 * @return array|mixed
	 */
	public function get_tokens() {
		$tokens = \WC_Payment_Tokens::get_customer_tokens( $this->user_id, $this->gateway_id );
		
		return $tokens;
	}
	
	/**
	 * Returns the specific token/profile by the provided token ID.
	 *
	 * @since 3.3.0
	 *
	 * @param $token_id
	 *
	 * @throws \Exception
	 *
	 * @return \WC_Paytrace_Token|\WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD
	 */
	public function get_token( $token_id ) {
		$tokens = $this->get_tokens();
		
		/**
		 * @var \WC_Payment_Token $token
		 */
		foreach ( $tokens as $id => $token ) {
			if ( $token_id == $token->get_id() ) {
				return $tokens[ $id ];
			}
		}
		
		throw new \Exception( __( 'The Token was not found.', 'wc_paysafe' ) );
	}
	
	/**
	 * Provide the token value and receive the WC_Payment_Token corresponding to it.
	 *
	 * @since 3.3.0
	 *
	 * @param string $token_value
	 *
	 * @return \WC_Paytrace_Token|\WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD|\WC_Payment_Token_Paysafe_Payments_Card|False
	 */
	public function get_token_from_value( $token_value ) {
		$tokens = $this->get_tokens();
		
		/**
		 * @var \WC_Payment_Token $token
		 */
		foreach ( $tokens as $id => $token ) {
			if ( $token->get_token() == $token_value ) {
				return $tokens[ $id ];
			}
		}
		
		return false;
	}
	
	/**
	 * Converts the Paysafe response object to a WC_Token and saves it
	 *
	 * @since 3.3.0
	 *
	 * @param Response_Abstract|VaultCards|Commons_Bank|Payments_Card_Token_Wrapper $paysafe_token The response object from a card or bank account creation
	 *
	 * @throws \Exception
	 * @return \WC_Payment_Token
	 */
	public function create_wc_token( $paysafe_token ) {
		$paysafe_customer = new Paysafe_Customer( new \WP_User( $this->user_id ) );
		$profile_id       = $paysafe_customer->get_vault_profile_id();
		
		wc_paysafe_add_debug_log( 'Creating WC token from the Vault token...', Formatting::get_log_id($this->gateway_id) );
		wc_paysafe_add_debug_log( 'Is instance of VaultCards' . print_r( $paysafe_token instanceof VaultCards, true ), Formatting::get_log_id($this->gateway_id) );
		wc_paysafe_add_debug_log( 'Create from token' . print_r( $paysafe_token, true ), Formatting::get_log_id($this->gateway_id) );
		
		/**
		 * Legend:
		 * Checkout V1: cards, directdebit
		 * Checkout V2: payments_card
		 */
		try {
			if ( 'directdebit' == $paysafe_token->get_data_type() && $paysafe_token instanceof Commons_Vault ) {
				$wc_paysafe_token = new \WC_Payment_Token_Paysafe_DD();
				$wc_paysafe_token->set_user_id( $this->user_id );
				$wc_paysafe_token->set_token( $paysafe_token->get_payment_token() );
				$wc_paysafe_token->set_bank_account_type( $paysafe_token->bank_type() );
				$wc_paysafe_token->set_gateway_id( $this->gateway_id );
				$wc_paysafe_token->set_last4( $paysafe_token->get_last_digits() );
				$wc_paysafe_token->set_billing_address_id( $paysafe_token->get_billing_address_id() );
				$wc_paysafe_token->set_profile_id( '' != $paysafe_token->get_profile_id() ? $paysafe_token->get_profile_id() : $profile_id );
				$wc_paysafe_token->set_source_id( $paysafe_token->get_id() );
			} elseif ( 'cards' == $paysafe_token->get_data_type() && $paysafe_token instanceof VaultCards ) {
				// Add the token a WC Token
				$wc_paysafe_token = new \WC_Payment_Token_Paysafe_CC();
				$wc_paysafe_token->set_user_id( $this->user_id );
				$wc_paysafe_token->set_token( $paysafe_token->get_payment_token() );
				$wc_paysafe_token->set_gateway_id( $this->gateway_id );
				$wc_paysafe_token->set_card_type( $paysafe_token->get_card_type() );
				$wc_paysafe_token->set_last4( $paysafe_token->get_last_digits() );
				$wc_paysafe_token->set_expiry_month( $paysafe_token->get_expiry_month() );
				$wc_paysafe_token->set_expiry_year( $paysafe_token->get_expiry_year() );
				$wc_paysafe_token->set_billing_address_id( $paysafe_token->get_billing_address_id() );
				$wc_paysafe_token->set_profile_id( '' != $paysafe_token->get_profile_id() ? $paysafe_token->get_profile_id() : $profile_id );
				$wc_paysafe_token->set_source_id( $paysafe_token->get_id() );
			} elseif ( 'payments_card' == $paysafe_token->get_data_type() && $paysafe_token instanceof Payments_Card_Token_Wrapper ) {
				// Add the token a WC Token
				$wc_paysafe_token = new \WC_Payment_Token_Paysafe_Payments_Card();
				$wc_paysafe_token->set_user_id( $this->user_id );
				$wc_paysafe_token->set_token( $paysafe_token->get_payment_token() );
				$wc_paysafe_token->set_gateway_id( $this->gateway_id );
				$wc_paysafe_token->set_card_type( $paysafe_token->get_card_type() );
				$wc_paysafe_token->set_last4( $paysafe_token->get_last_digits() );
				$wc_paysafe_token->set_expiry_month( $paysafe_token->get_expiry_month() );
				$wc_paysafe_token->set_expiry_year( $paysafe_token->get_expiry_year() );
				$wc_paysafe_token->set_merchant_customer_id( $paysafe_token->get_merchant_customer_id() );
				$wc_paysafe_token->set_customer_id( $paysafe_token->get_customer_id() );
			}
			
			if ( $wc_paysafe_token ) {
				$wc_paysafe_token->save();
			}
			
			wc_paysafe_add_debug_log( 'Token created. Token ID:  ' . print_r( $wc_paysafe_token->get_id(), true ), Formatting::get_log_id( $this->gateway_id ) );
			
			return $wc_paysafe_token;
		}
		catch ( \Exception $e ) {
			throw new \Exception( sprintf( __( 'The Token of the transaction was not successfully saved. Error: %s' ), $e->getMessage() ) );
		}
	}
	
	/**
	 * Converts the Paysafe response object to a WC_Token and saves it
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Payment_Token|\WC_Payment_Token_Paysafe_DD|\WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_Payments_Card $wc_token
	 * @param Response_Abstract|VaultCards|Commons_Bank|Payments_Card_Token_Wrapper                                               $paysafe_token The response object from a card or bank account creation
	 *
	 * @throws \Exception
	 * @return \WC_Payment_Token
	 */
	public function update_wc_token( $wc_token, $paysafe_token ) {
		$paysafe_customer = new Paysafe_Customer( new \WP_User( $this->user_id ) );
		$profile_id       = $paysafe_customer->get_vault_profile_id();
		
		/**
		 * Legend: Token data_types
		 * Checkout V1: cards, directdebit
		 * Checkout V2: payments_card
		 */
		try {
			if ( 'directdebit' == $paysafe_token->get_data_type() && $paysafe_token instanceof Commons_Vault ) {
				$wc_token->set_user_id( $this->user_id );
				$wc_token->set_token( $paysafe_token->get_payment_token() );
				$wc_token->set_bank_account_type( $paysafe_token->bank_type() );
				$wc_token->set_gateway_id( $this->gateway_id );
				$wc_token->set_last4( $paysafe_token->get_last_digits() );
				$wc_token->set_billing_address_id( $paysafe_token->get_billing_address_id() );
				$wc_token->set_profile_id( '' != $paysafe_token->get_profile_id() ? $paysafe_token->get_profile_id() : $profile_id );
				$wc_token->set_source_id( $paysafe_token->get_id() );
			} elseif ( 'cards' == $paysafe_token->get_data_type() && $paysafe_token instanceof VaultCards ) {
				// Add the token a WC Token
				$wc_token->set_user_id( $this->user_id );
				$wc_token->set_token( $paysafe_token->get_payment_token() );
				$wc_token->set_gateway_id( $this->gateway_id );
				$wc_token->set_card_type( $paysafe_token->get_card_type() );
				$wc_token->set_last4( $paysafe_token->get_last_digits() );
				$wc_token->set_expiry_month( $paysafe_token->get_expiry_month() );
				$wc_token->set_expiry_year( $paysafe_token->get_expiry_year() );
				$wc_token->set_billing_address_id( $paysafe_token->get_billing_address_id() );
				$wc_token->set_profile_id( '' != $paysafe_token->get_profile_id() ? $paysafe_token->get_profile_id() : $profile_id );
				$wc_token->set_source_id( $paysafe_token->get_id() );
			} elseif ( 'payments_card' == $paysafe_token->get_data_type() && $paysafe_token instanceof Payments_Card_Token_Wrapper ) {
				// Add the token a WC Token
				$wc_token->set_user_id( $this->user_id );
				$wc_token->set_token( $paysafe_token->get_payment_token() );
				$wc_token->set_gateway_id( $this->gateway_id );
				$wc_token->set_card_type( $paysafe_token->get_card_type() );
				$wc_token->set_last4( $paysafe_token->get_last_digits() );
				$wc_token->set_expiry_month( $paysafe_token->get_expiry_month() );
				$wc_token->set_expiry_year( $paysafe_token->get_expiry_year() );
				$wc_token->set_merchant_customer_id( $paysafe_token->get_merchant_customer_id() );
			}
			
			if ( $wc_token ) {
				$wc_token->save();
			}
			
			wc_paysafe_add_debug_log( 'Token updated: ' . print_r( $wc_token, true ), Formatting::get_log_id($this->gateway_id) );
			
			return $wc_token;
		}
		catch ( \Exception $e ) {
			throw new \Exception( sprintf( __( 'The Token of the transaction was not successfully saved. Error: %s' ), $e->getMessage() ) );
		}
	}
	
	/**
	 *
	 * @since   3.3.0
	 *
	 * @param $token
	 *
	 * @return bool
	 */
	public function set_default_source( $token ) {
		
		try {
			$gateway = Factories::get_gateway( $this->gateway_id );
			
			$integration = $gateway->get_integration_object();
			$wc_token    = $this->get_token_from_value( $token );
			
			// Only cards have default indicator
			if ( ! $wc_token instanceof \WC_Payment_Token_Paysafe_CC ) {
				return false;
			}
			
			wc_paysafe_add_debug_log( 'Set default token: ' . $wc_token->get_id(), Formatting::get_log_id($this->gateway_id) );
			
			$data_source   = new User_Source( new \WP_User( $wc_token->get_user_id() ) );
			$api_client    = $integration->get_api_client( $data_source, 'cards' );
			$vault_service = $api_client->get_vault_service();
			
			$response = $vault_service->card()->update(
				$vault_service->card()->get_request_builder( $data_source )->set_default_source_parameters( $wc_token )
			);
			
			wc_paysafe_add_debug_log( 'Source is set as default: ' . $response->get_default_card_indicator(), Formatting::get_log_id($this->gateway_id) );
		}
		catch ( \Exception$e ) {
			return false;
		}
	}
}