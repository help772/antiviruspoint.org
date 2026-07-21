<?php

namespace WcPaysafe\Gateways\Redirect\Payments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings for Hosted Payments Integration
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Settings {
	
	/**
	 * @var \WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway
	 */
	public $gateway;
	public $integration_type;
	
	public function __construct( $gateway ) {
		$this->gateway          = $gateway;
		$this->integration_type = $gateway->id;
	}
	
	public function get_settings() {
		$card_options = $this->gateway->card_options;
		
		return array(
			'api_key_settings_start' => array(
				'title'       => __( 'Credentials Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'api_user_name' => array(
				'title'       => __( 'Secret Key: Username', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'text',
				'description' => __( "The Username from the section 'Developer > API Keys > Secret Key'.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_' . $this->integration_type,
				'desc_tip'    => true,
			),
			
			'api_password' => array(
				'title'       => __( 'Secret Key: Password', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'password',
				'placeholder' => __( 'API Key: Password', 'wc_paysafe' ),
				'description' => __( "The corresponding password from the section 'Developer > API Keys > Secret Key'.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_' . $this->integration_type,
				'desc_tip'    => true,
			),
			
			'single_use_token_user_name' => array(
				'title'       => __( 'Public Key: Username', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'text',
				'description' => __( "The Username from the section 'Developer > API Keys > Public Key' in the Paysafe account settings.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_' . $this->integration_type,
				'desc_tip'    => true,
			),
			
			'single_use_token_password' => array(
				'title'       => __( 'Public Key: Password', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'password',
				'placeholder' => __( 'Public Key: Password', 'wc_paysafe' ),
				'description' => __( "The password from 'Developer > API Keys > Public Key'.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_' . $this->integration_type,
				'desc_tip'    => true,
			),
			
			'api_key_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'available_payments_settings_start' => array(
				'title'       => __( 'Available Payments Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'available_payment_options' => array(
				'title'       => __( 'Available Payment Methods', 'wc_paysafe' ),
				'type'        => 'multiselect',
				'description' => __( 'Please only choose the method active for your Paysafe account. This option can only use the already active methods, it will not activate the methods in your Paysafe account.', 'wc_paysafe' ),
				'options'     => [
					'card'      => __( 'Card', 'wc_paysafe' ),
					'applePay'  => __( 'Apple Pay', 'wc_paysafe' ),
					'googlePay' => __( 'Google Pay', 'wc_paysafe' ),
				],
				'class'       => 'wc-enhanced-select integration_' . $this->integration_type,
				'css'         => 'min-width: 350px;',
				'default'     => array( 'card' ),
				'desc_tip'    => true,
			),
			
			'available_payments_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'account_id_settings_start' => array(
				'title'       => __( 'Merchant Accounts Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'card_accounts' => array(
				'title'       => __( 'Cards Account IDs', 'wc_paysafe' ),
				'type'        => 'account_ids',
				'description' => __( 'This field is required only if you have more than one account configured for the same payment method and currency.', 'wc_paysafe' ),
				'desc_tip'    => true,
				'default'     => array(
					array(
						'account_currency' => get_woocommerce_currency(),
						'account_id'       => '',
					),
				),
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'applepay_accounts' => array(
				'title'       => __( 'Apple Pay Account IDs', 'wc_paysafe' ),
				'type'        => 'account_ids',
				'description' => __( 'Used for Apple Pay transactions, if you have more than one account for a currency, please specify here the account you want to use, Leave empty, if you only have one account.', 'wc_paysafe' ),
				'desc_tip'    => true,
				'default'     => array(
					array(
						'account_currency' => get_woocommerce_currency(),
						'account_id'       => '',
					),
				),
				'class'       => 'apple_pay_element integration_' . $this->integration_type,
			),
			
			'googlepay_accounts' => array(
				'title'       => __( 'Google Pay Account IDs', 'wc_paysafe' ),
				'type'        => 'account_ids',
				'description' => __( 'Used for Google Pay transactions, if you have more than one account for a currency, please specify here the account you want to use, Leave empty, if you only have one account.', 'wc_paysafe' ),
				'desc_tip'    => true,
				'default'     => array(
					array(
						'account_currency' => get_woocommerce_currency(),
						'account_id'       => '',
					),
				),
				'class'       => 'google_pay_element integration_' . $this->integration_type,
			),
			
			'account_id_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'google_pay_settings_start' => array(
				'title'       => __( 'Google Pay Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'google_pay_block integration_' . $this->integration_type,
			),
			
			'google_pay_merchant_name' => array(
				'title'       => __( 'Merchant Name', 'wc_paysafe' ),
				'type'        => 'safe_text',
				'description' => __( "Merchant name used for the Google Pay method. Merchant name is rendered in the payment sheet.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'google_pay_element integration_' . $this->integration_type,
				'desc_tip'    => true,
				
			),
			
			'google_pay_merchant_id' => array(
				'title'       => __( 'Merchant ID', 'wc_paysafe' ),
				'type'        => 'safe_text',
				'description' => __( "Merchant ID used for the Google Pay method. The Merchant ID is available for a production environment after approval by Google.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'google_pay_element integration_' . $this->integration_type,
				'desc_tip'    => true,
			),
			
			'google_pay_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'google_pay_block integration_' . $this->integration_type,
			),
			
			'transaction_settings_start' => array(
				'title'       => __( 'Transaction Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'locale' => array(
				'title'       => __( 'Payment Pages Language', 'wc_paysafe' ),
				'type'        => 'select',
				'description' => __( 'Choose the language you want your Paysafe payment pages to be in.', 'wc_paysafe' ),
				'default'     => 'en_GB',
				'options'     => array(
					'en_US' => __( 'US English', 'wc_paysafe' ),
					'fr_CA' => __( 'French Canadian', 'wc_paysafe' ),
				),
				'class'       => 'integration_' . $this->integration_type,
				'desc_tip'    => true,
			),
			
			'authorization_type' => array(
				'title'       => __( 'Authorization Type', 'wc_paysafe' ),
				'type'        => 'select',
				'description' => __( '"Sale", will capture the fund right after the transaction authorization. "Authorization Only", will only perform an authorization and let you capture the funds at a later date.', 'wc_paysafe' ),
				'default'     => 'sale',
				'options'     => array(
					'sale' => __( 'Sale', 'wc_paysafe' ),
					'auth' => __( 'Authorization Only', 'wc_paysafe' ),
				),
				'class'       => 'integration_' . $this->integration_type,
				'desc_tip'    => true,
			),
			
			'available_cc' => array(
				'title'       => __( 'Method Icons to add on the checkout', 'wc_paysafe' ),
				'type'        => 'multiselect',
				'description' => __( 'Choose the payment types you want to display as logos on the checkout page.', 'wc_paysafe' ),
				'options'     => $card_options,
				'class'       => 'wc-enhanced-select integration_' . $this->integration_type,
				'css'         => 'min-width: 350px;',
				'default'     => array( 'visa', 'mastercard', 'amex', 'discover', 'jcb' ),
				'desc_tip'    => true,
			),
			
			'transaction_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'threeds_settings_start' => array(
				'title'       => __( '3DS Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'use_layover_3ds2' => array(
				'title'       => __( '3DS(v2)', 'wc_paysafe' ),
				'label'       => __( 'Enable 3DS2 Authentication for card payments', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'Your account ID needs to be 3DS2 enabled or the transactions will fail', 'wc_paysafe' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'threeds2_challenge_preference' => array(
				'title'       => __( '3DS2: Challenge Preference', 'wc_paysafe' ),
				'description' => __( 'The challenge preference tells the processor whether you want the 3DS2 challenge to be required or it can be skipped based on their assessment.', 'wc_paysafe' ),
				'type'        => 'select',
				'default'     => 'CHALLENGE_MANDATED',
				'options'     => array(
					'CHALLENGE_MANDATED'  => __( 'Required', 'wc_paysafe' ),
					'CHALLENGE_REQUESTED' => __( 'Requested', 'wc_paysafe' ),
					'NO_PREFERENCE'       => __( 'No Preference', 'wc_paysafe' ),
				),
				'desc_tip'    => true,
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'threeds_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'vault_settings_start' => array(
				'title'       => __( 'Vault Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'saved_cards' => array(
				'title'       => __( 'Saved Cards', 'wc_paysafe' ),
				'label'       => __( 'Enable Payment via Saved Cards', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are stored in the Paysafe Vault, not in the store database.', 'wc_paysafe' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'class'       => 'integration_' . $this->integration_type,
			),
			
			'is_cvv_required_with_tokens' => array(
				'title'       => __( 'CVV Card Field', 'wc_paysafe' ),
				'label'       => __( 'Require CVV with Card Token Payments', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'To comply with 3DS2 the gateway processor requires the CVV to be sent with the card token. Enabling this option will add a CVV field to the payment form and require the customer to fill it in.', 'wc_paysafe' ),
				'default'     => 'yes',
				'desc_tip'    => true,
				'class'       => 'show_if_paysafe_checkout_payments_saved_cards integration_' . $this->integration_type,
			),
			
			'vault_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_' . $this->integration_type,
			),
		);
	}
}