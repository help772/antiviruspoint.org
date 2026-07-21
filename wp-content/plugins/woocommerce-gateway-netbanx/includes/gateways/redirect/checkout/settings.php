<?php

namespace WcPaysafe\Gateways\Redirect\Checkout;

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
	
	public $gateway;
	public $integration_type;
	
	public function __construct( $gateway ) {
		$this->gateway          = $gateway;
		$this->integration_type = 'checkoutjs';
	}
	
	public function get_settings() {
		$card_options = $this->gateway->card_options;
		
		return array(
			'api_key_settings_start' => array(
				'title'       => __( 'Credentials Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'api_user_name' => array(
				'title'       => __( 'API Key: User Name', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'text',
				'description' => __( "The User Name from the section API Keys in the Paysafe account settings.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'api_password' => array(
				'title'       => __( 'API Key: Password', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'password',
				'placeholder' => __( 'API Key: Password', 'wc_paysafe' ),
				'description' => __( "The corresponding password to the API User Name.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'single_use_token_user_name' => array(
				'title'       => __( 'Single-Use Token: User Name', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'text',
				'description' => __( "The User Name from the section 'Single-Use Token' in the Paysafe account settings.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'single_use_token_password' => array(
				'title'       => __( 'Single-Use Token: Password', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'password',
				'placeholder' => __( 'Single-Use Token: Password', 'wc_paysafe' ),
				'description' => __( "The corresponding password to the 'Single-Use Token' User Name.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'api_key_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'account_id_settings_start' => array(
				'title'       => __( 'Merchant Accounts Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'card_accounts' => array(
				'title'       => __( 'Cards Account IDs', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'account_ids',
				'description' => __( 'Used for card payments in your store currency. For currencies other than your default one, please contact support.', 'wc_paysafe' ),
				'desc_tip'    => true,
				'default'     => array(
					array(
						'account_currency' => get_woocommerce_currency(),
						'account_id'       => '',
					),
				),
				'class'       => 'integration_checkoutjs',
			),
			
			'direct_debit_accounts' => array(
				'title'       => __( 'Direct Debit Account IDs', 'wc_paysafe' ) . ' ' . '<span style="color:red;" title="' . __( 'Required field', 'wc_paysafe' ) . '">*</span>',
				'type'        => 'account_ids',
				'description' => __( 'Used for direct debit transactions in your default currency.', 'wc_paysafe' ),
				'desc_tip'    => true,
				'default'     => array(
					array(
						'account_currency' => get_woocommerce_currency(),
						'account_id'       => '',
					),
				),
				'class'       => 'integration_checkoutjs',
			),
			
			'account_id_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'threeds_settings_start' => array(
				'title'       => __( '3DS Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'use_layover_3ds' => array(
				'title'       => __( '3DS(v1)', 'wc_paysafe' ),
				'label'       => __( 'Enable 3DS Authentication for card payments', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'Your account ID needs to be 3DS enabled or the transactions will fail', 'wc_paysafe' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'class'       => 'integration_checkoutjs',
			),
			
			'use_layover_3ds2' => array(
				'title'       => __( '3DS(v2)', 'wc_paysafe' ),
				'label'       => __( 'Enable 3DS2 Authentication for card payments', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'Your account ID needs to be 3DS2 enabled or the transactions will fail', 'wc_paysafe' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'class'       => 'integration_checkoutjs',
			),
			
			'threeds2_challenge_preference' => array(
				'title'       => __( '3DS2: Challenge Preference', 'wc_paysafe' ),
				'description' => __( 'The challenge preference tells the processor whether you want the 3DS2 challenge to be required or it can be skipped based on their assessment.', 'wc_paysafe' ),
				'type'        => 'select',
				'default'     => 'NO_PREFERENCE',
				'options'     => array(
					'CHALLENGE_MANDATED'  => __( 'Required', 'wc_paysafe' ),
					'CHALLENGE_REQUESTED' => __( 'Requested', 'wc_paysafe' ),
					'NO_PREFERENCE'       => __( 'No Preference', 'wc_paysafe' ),
				),
				'desc_tip'    => true,
				'class'       => 'integration_checkoutjs',
			),
			
			'threeds_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'transaction_settings_start' => array(
				'title'       => __( 'Transaction Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
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
				'class'       => 'integration_checkoutjs',
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
				'class'       => 'integration_checkoutjs',
			),
			
			'available_cc' => array(
				'title'       => __( 'Accepted Cards', 'wc_paysafe' ),
				'type'        => 'multiselect',
				'description' => __( 'Choose the cards you are accepting and want to display their logos on the checkout page.', 'wc_paysafe' ),
				'options'     => $card_options,
				'class'       => 'wc-enhanced-select integration_checkoutjs',
				'css'         => 'min-width: 350px;',
				'default'     => array( 'visa', 'mastercard', 'amex', 'discover', 'jcb' ),
			),
			
			'transaction_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'vault_settings_start' => array(
				'title'       => __( 'Vault Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'saved_cards' => array(
				'title'       => __( 'Saved Cards', 'wc_paysafe' ),
				'label'       => __( 'Enable Payment via Saved Cards', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are stored in the Paysafe Vault, not in the store database.', 'wc_paysafe' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'class'       => 'integration_checkoutjs',
			),
			
			'user_prefix' => array(
				'title'       => __( 'Vault Profile Prefix', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Enter unique customer prefix for their Vault profile ID. Make sure this is unique prefix for your store. (Example: WC-)', 'wc_paysafe' ),
				'default'     => uniqid() . '-',
				'class'       => 'integration_checkoutjs show_if_checkoutjs_saved_cards',
			),
			
			'is_cvv_required_with_tokens' => array(
				'title'       => __( 'CVV Card Field', 'wc_paysafe' ),
				'label'       => __( 'Require CVV with Card Token Payments', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'To comply with 3DS2 the gateway processor requires the CVV to be sent with the card token. Enabling this option will add a CVV field to the payment form and require the customer to fill it in.', 'wc_paysafe' ),
				'default'     => 'yes',
				'desc_tip'    => true,
				'class'       => 'integration_checkoutjs show_if_checkoutjs_saved_cards',
			),
			
			'vault_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'payment_layover_settings_start' => array(
				'title'       => __( 'Layover Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'layover_image_url' => array(
				'title'       => __( 'Layover Image', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Optionally enter the URL to a 56x56 pixels or a 1:1 aspect ratio (for vector images) of your brand or product. e.g. <code>https://yoursite.com/wp-content/uploads/2013/09/yourimage.jpg</code>', 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'layover_merchant_name' => array(
				'title'       => __( 'Merchant Name', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Optionally enter the merchant name you want to display on the Payment Layover. Up to 60 characters', 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'layover_button_color' => array(
				'title'       => __( 'Button Color', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Optionally enter the hex color for the payment form buttons e.g. <code>#ff0000</code>', 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_checkoutjs',
			),
			
			'layover_preferred_payment_method' => array(
				'title'       => __( 'Default Payment Method', 'wc_paysafe' ),
				'type'        => 'select',
				'description' => __( 'The payment method selected when Paysafe Checkout is opened', 'wc_paysafe' ),
				'options'     => apply_filters( 'wc_paysafe_checkoutjs_preferred_payment_method', array(
					''            => __( 'Optionally Select', 'wc_paysafe' ),
					'Cards'       => 'Cards',
					'DirectDebit' => 'Direct Debit',
				) ),
				'class'       => 'wc-enhanced-select integration_checkoutjs',
				'css'         => 'min-width: 350px;',
			),
			
			'payment_layover_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_checkoutjs',
			),
		);
	}
}