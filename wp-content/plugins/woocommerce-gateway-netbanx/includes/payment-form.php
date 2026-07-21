<?php

namespace WcPaysafe;

use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Tokens\Customer_Tokens;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Credit Card Payment Gateway
 *
 * @since                  2.6.0
 * @package                WooCommerce/Classes
 * @author                 WooThemes
 */
class Payment_Form {
	
	protected $tokens = array();
	protected $gateway;
	protected $saved_checks = array();
	protected $saved_cards = array();
	protected $show_save_to_account = false;
	
	/**
	 * constructor.
	 *
	 * @param \WcPaysafe\Gateways\Redirect\Gateway $gateway
	 */
	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}
	
	/**
	 * @return Gateway
	 */
	public function get_gateway() {
		return $this->gateway;
	}
	
	/**
	 * Outputs the payment fields for the plugin.
	 *
	 * @since 2.1
	 */
	public function output_payment_fields() {
		$tokens = $this->get_customer_tokens();
		$this->output_saved_payment_tokens( $tokens );
		$this->output_cvv_field();
	}
	
	/**
	 * Outputs the update payment method fields
	 * @since 3.3.0
	 *
	 * @param $token
	 */
	public function output_update_payment_token_fields( $token ) {
		wc_get_template(
			'paysafe/checkoutjs/myaccount/update-payment-method.php',
			array(
				'form'  => $this,
				'token' => $token,
			),
			'',
			Paysafe::plugin_path() . '/templates/'
		);
		
		// Output the iframe fields
		$submit_button_text = apply_filters( 'wc_paysafe_checkoutjs_update_card_button_text', __( 'Update Method', 'wc_paysafe' ) );
		$this->output_checkoutjs_iframe_payment_block( $submit_button_text );
	}
	
	/**
	 * Outputs the checkout JS iframe fields
	 * @since 3.3.0
	 *
	 * @param $submit_button_text
	 */
	public function output_checkoutjs_iframe_payment_block( $submit_button_text ) {
		wc_get_template(
			'paysafe/checkoutjs/pay/payment-block.php',
			array(
				'form'        => $this,
				'button_text' => $submit_button_text,
			),
			'',
			Paysafe::plugin_path() . '/templates/'
		);
	}
	
	/**
	 * Outputs the saved payment tokens fields
	 *
	 * @since 2.1
	 *
	 * @param array $tokens Tokens
	 */
	public function output_saved_payment_tokens( $tokens ) {
		// Load the saved checks form template
		wc_get_template(
			'paysafe/checkoutjs/checkout/saved-tokens.php',
			array(
				'tokens' => $tokens,
				'form'   => $this,
			),
			'',
			Paysafe::plugin_path() . '/templates/'
		);
	}
	
	public function output_cvv_field() {
		
		if ( wc_paysafe_is_change_method_page() ) {
			return;
		}
		
		// Load the saved checks form template
		wc_get_template(
			'paysafe/checkoutjs/checkout/cvv-field.php',
			array(
				'form' => $this,
			),
			'',
			Paysafe::plugin_path() . '/templates/'
		);
	}
	
	/**
	 * Outputs the "Save to account" checkbox field for all payment forms
	 * @return bool
	 */
	public function output_save_to_account_field() {
		if ( ! $this->show_save_to_account ) {
			return false;
		}
		
		wc_get_template(
			'paysafe/checkoutjs/save-to-account-field.php',
			array(
				'form' => $this,
			),
			'',
			Paysafe::plugin_path() . '/templates/'
		);
	}
	
	/**
	 * Loads the payment tokens to the class props
	 *
	 * @since 2.1
	 *
	 * @return bool|array
	 */
	public function get_customer_tokens() {
		
		// Guest has no tokens
		if ( ! is_user_logged_in() ) {
			return false;
		}
		
		// No tokens, if Vault is not enabled
		if ( ! $this->get_gateway()->saved_cards ) {
			return false;
		}
		
		// No need to load/sort the tokens more than once
		if ( ! empty( $this->tokens ) ) {
			return $this->tokens;
		}
		
		$paysafe_customer_tokens = new Customer_Tokens( get_current_user_id(), $this->get_gateway()->id );
		$customer_tokens         = $paysafe_customer_tokens->get_tokens();
		
		/**
		 * If 3rd party adds a specific token type,
		 * the code will filter all tokens and display only the ones allowed
		 *
		 * Possible values are:
		 * 'Paysafe_CC' and 'Paysafe_DD'
		 */
		$allowed_token_types = apply_filters( 'wc_paysafe_checkoutjs_allowed_token_types', array() );
		
		if ( $allowed_token_types ) {
			/**
			 * @var \WC_Payment_Token $token
			 */
			foreach ( $customer_tokens as $key => $token ) {
				if ( ! in_array( $token->get_type(), $allowed_token_types ) ) {
					unset( $customer_tokens[ $key ] );
				}
			}
		}
		
		$this->tokens = $customer_tokens;
		
		return $this->tokens;
	}
	
	/**
	 * Set the show save customer prop.
	 * Controls the display decisions on the "Save to account" checkbox.
	 *
	 * @since 2.1
	 *
	 * @param $show_save_to_account
	 */
	public function set_show_save_to_account( $show_save_to_account ) {
		$this->show_save_to_account = $show_save_to_account;
	}
}
