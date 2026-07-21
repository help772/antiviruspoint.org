<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

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
	
	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}
	
	public function get_settings() {
		$card_options = $this->gateway->card_options;
		
		return array(
			'api_key_settings_start' => array(
				'title'       => __( 'Credentials Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted',
			),
			
			'api_user_name' => array(
				'title'       => __( 'API Key: User Name', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( "The User Name from the section API Keys in the Paysafe account settings.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_hosted',
			),
			
			'api_password' => array(
				'title'       => __( 'API Key: Password', 'wc_paysafe' ),
				'type'        => 'password',
				'description' => __( "The corresponding password to the API User Name.", 'wc_paysafe' ),
				'default'     => '',
				'class'       => 'integration_hosted',
			),
			
			'api_key_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted',
			),
			
			'transaction_settings_start' => array(
				'title'       => __( 'Transaction Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted',
			),
			
			'locale'             => array(
				'title'       => __( 'Payment Pages Language', 'wc_paysafe' ),
				'type'        => 'select',
				'description' => __( 'Choose the language you want your Paysafe payment pages to be in.', 'wc_paysafe' ),
				'default'     => 'en_GB',
				'options'     => array(
					'en_GB' => __( 'British English', 'wc_paysafe' ),
					'en_US' => __( 'US English', 'wc_paysafe' ),
					'fr_FR' => __( 'French', 'wc_paysafe' ),
					'fr_CA' => __( 'French Canadian', 'wc_paysafe' )
				),
				'class'       => 'integration_hosted',
			),
			'authorization_type' => array(
				'title'       => __( 'Authorization Type', 'wc_paysafe' ),
				'type'        => 'select',
				'description' => __( '"Authorization and Capture", will capture the fund right after the transaction authorization. "Authorization Only", will only perform an authorization and let you capture the funds at a later date.', 'wc_paysafe' ),
				'default'     => 'sale',
				'options'     => array(
					'sale' => __( 'Authorization and Capture', 'wc_paysafe' ),
					'auth' => __( 'Authorization Only', 'wc_paysafe' ),
				),
				'class'       => 'integration_hosted',
			),
			'send_order_details' => array(
				'title'       => __( 'Send Order Details', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'label'       => __( 'Send Detailed Order to Paysafe', 'wc_paysafe' ),
				'description' => __( '<strong>Enable</strong>, if you want to send a breakdown of all order details (items, taxes, shipping costs) to Paysafe.<br/><strong>Disable</strong>, if you want to just send the Order Total for the customer to pay.', 'wc_paysafe' ),
				'default'     => 'yes',
				'class'       => 'integration_hosted',
			),
			
			'send_ip_address' => array(
				'title'       => __( 'Send Customer IP', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'label'       => __( 'Send Customer IP to Paysafe', 'wc_paysafe' ),
				'description' => __( '<strong>Enable</strong>, will send the customer IP to Paysafe.<br/><strong>Disable</strong>, will not send the IP to Paysafe.', 'wc_paysafe' ),
				'default'     => 'yes',
				'class'       => 'integration_hosted',
			),
			'synchro'         => array(
				'title'       => __( 'Synchronous responses', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'label'       => __( 'Use Synchronous payment responses', 'wc_paysafe' ),
				'description' => __( '<strong>Enable</strong>, payment response will be made synchronously, in-line with the customer returning to your store.<br/><strong>Disable</strong>, payment responses will be made asynchronously, will be delayed a bit, allowing the callback system can detect problems with the merchant system and retry any failed attempts until a successful response is received.<br/><strong>Recommended from PAYSAFE</strong>: Disable.', 'wc_paysafe' ),
				'default'     => 'no',
				'class'       => 'integration_hosted',
			),
			'user_prefix'     => array(
				'title'       => __( 'Customer ID Prefix', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Enter unique customer ID prefix. Make sure this is unique prefix for your store. (Example: WC-)', 'wc_paysafe' ),
				'default'     => uniqid() . '-',
				'class'       => 'integration_hosted',
			),
			
			'merchant_email_address' => array(
				'title'       => __( 'Merchant Notification Email', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Enter merchant email, to which the merchant will receive email notifications. If empty, the notification parameter will not be send.', 'wc_paysafe' ),
				'default'     => get_option( 'admin_email' ),
				'class'       => 'integration_hosted',
			),
			
			'available_cc' => array(
				'title'       => __( 'Accepted Cards', 'wc_paysafe' ),
				'type'        => 'multiselect',
				'description' => __( 'Choose the cards you are accepting and want to display their logos on the checkout page.', 'wc_paysafe' ),
				'options'     => $card_options,
				'class'       => 'wc-enhanced-select integration_hosted',
				'css'         => 'min-width: 350px;',
				'default'     => array( 'visa', 'mastercard', 'amex', 'discover', 'jcb' ),
			),
			
			'transaction_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted',
			),
			
			'iframe_settings_start' => array(
				'title'       => __( 'Iframe Settings', 'wc_paysafe' ),
				'type'        => 'title',
				'description' => __( 'In this section you will set the Iframe control settings.', 'wc_paysafe' ),
				'class'       => 'integration_hosted',
			),
			'use_iframe'            => array(
				'title'       => __( 'Enable Iframe', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable iframe to have the customer payment form display in an Iframe on your site. If disabled, your customers will be redirected to Paysafe payment page.', 'wc_paysafe' ),
				'default'     => 'no',
				'class'       => 'integration_hosted',
			),
			'iframe_width'          => array(
				'title'       => __( 'Iframe Width', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Width of the iframe window. Enter only numbers in pixels (i.e. 700) or you can enter number in percentage but you need to suffix it with "%" (i.e. 55%).', 'wc_paysafe' ),
				'default'     => '700',
				'class'       => 'show_if_hosted_use_iframe integration_hosted',
			),
			'iframe_height'         => array(
				'title'       => __( 'Iframe Height', 'wc_paysafe' ),
				'type'        => 'text',
				'description' => __( 'Height of the iframe window. Entered can be a number in pixels (i.e. 850) or you can enter number in percentage but you need to suffix it with "%" (i.e. 55%).', 'wc_paysafe' ),
				'default'     => '1075',
				'class'       => 'show_if_hosted_use_iframe integration_hosted',
			),
			'iframe_scroll'         => array(
				'title'       => __( 'Iframe Scroll', 'wc_paysafe' ),
				'type'        => 'checkbox',
				'description' => __( 'Should the iframe be scrollable or not. If scrollable, the customer will be able to scroll to the iframe to reach its borders.', 'wc_paysafe' ),
				
				'default' => 'no',
				'class'   => 'show_if_hosted_use_iframe integration_hosted',
			),
			
			'iframe_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted',
			),
			
			'reset_profiles_settings_start' => array(
				'title' => __( 'Reset Customer Profiles', 'wc_paysafe' ),
				'type'  => 'title',
				'class' => 'integration_hosted',
			),
			'reset_profiles_button'         => array(
				'title'       => __( 'Reset', 'wc_paysafe' ),
				'type'        => 'reset_button',
				'description' => __( 'Click the button to reset the saved customer profiles', 'wc_paysafe' ),
				'class'       => 'integration_hosted',
			),
			
			'reset_settings_end' => array(
				'title'       => '<hr/>',
				'type'        => 'title',
				'description' => '',
				'class'       => 'integration_hosted',
			),
		);
	}
}