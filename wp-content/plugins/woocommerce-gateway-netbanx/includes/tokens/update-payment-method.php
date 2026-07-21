<?php

namespace WcPaysafe\Tokens;

use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Payment_Form;
use WcPaysafe\Paysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Update_Payment_Method {
	
	public function hooks() {
		add_filter( 'woocommerce_payment_methods_list_item', array(
			$this,
			'add_update_button_to_token_list',
		), 10, 2 );
		
		add_action( 'woocommerce_account_update-payment-method_endpoint', array(
			$this,
			'update_payment_method_page',
		), 10, 1 );
		
		add_action( 'wc_update_payment_method_content-netbanx', array(
			$this,
			'update_payment_method_content',
		), 10, 1 );
		
		// Add a setting to the advanced > pages settings, so the merchant can properly translate the endpoint
		add_filter( 'woocommerce_settings_pages', array( $this, 'add_update_method_endpoint_setting' ), 10 );
	}
	
	/**
	 *
	 * @param array             $method_item
	 * @param \WC_Payment_Token $payment_token
	 *
	 * @return mixed
	 */
	public function add_update_button_to_token_list( $method_item, $payment_token ) {
		if ( 'netbanx' != $payment_token->get_gateway_id() ) {
			return $method_item;
		}
		
		// Get the endpoint slug (could be translated)
		$query_endpoint = wc_clean( wp_unslash( Paysafe::get_field( 'update-payment-method', WC()->query->get_query_vars(), 'update-payment-method' ) ) );
		$update_url     = wc_get_endpoint_url( $query_endpoint, $payment_token->get_id() );
		
		$method_item['actions']['update'] = array(
			'url'  => esc_url_raw( $update_url ),
			'name' => esc_html__( 'Update', 'wc_paysafe' ),
		);
		
		return $method_item;
	}
	
	public function update_payment_method_page( $token_id ) {
		
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit();
		} else {
			$token = \WC_Payment_Tokens::get( (int) $token_id );
			
			if ( ! $token ) {
				wc_add_notice( __( 'The token was not found. Please try again', 'wc_paysafe' ), 'error' );
				wp_safe_redirect( wc_get_account_endpoint_url( 'payment-methods' ) );
				exit();
			}
			
			// Call an action to display the content based on the gateway ID.
			// It will help with displaying different content if we add additional gateways
			do_action( 'wc_update_payment_method_content-' . $token->get_gateway_id(), $token_id );
		}
	}
	
	public function update_payment_method_content( $token_id ) {
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit();
		} else {
			try {
				do_action( 'before_woocommerce_update_payment_method' );
				$token = \WC_Payment_Tokens::get( (int) $token_id );
				
				// Bail, if no token
				if ( ! $token ) {
					throw new \Exception( __( 'The token was not found. Please try again or contact us if you require assistance.', 'wc_paysafe' ) );
				}
				
				$user = wp_get_current_user();
				
				// Bail, if the token does not belong to the user
				if ( $user->ID != $token->get_user_id() ) {
					throw new \Exception( __( 'This token does not belong to you.', 'wc_paysafe' ), 'error' );
				}
				
				/**
				 * @var Gateway $gateway
				 */
				$gateway = Factories::get_gateway( $token->get_gateway_id() );
				if ( false == $gateway || 'hosted' == $gateway->integration ) {
					throw new \Exception( __( 'Token gateway was not found. Please try again or contact us if you require assistance', 'wc_paysafe' ), 'error' );
				}
				
				do_action( 'before_woocommerce_update_payment_method_fields' );
				
				$integration        = $gateway->get_integration_object();
				$api_client         = $integration->get_api_client( new User_Source( $user ), 'iframe' );
				$checkoutjs_service = $api_client->get_checkoutjs_service();
				$paysafe_args       = $checkoutjs_service->get_iframe_user_parameters( $user );
				
				$paysafe_args['options']['update_token_id'] = $token->get_id();
				$paysafe_args['options']['hideAmount']      = true;
				$paysafe_args['processAction']              = 'update_payment_method';
				
				// If the label does not exist, add it
				$paysafe_args['options']['buttonLabel'] = _x( 'Submit', 'layover update method button label', 'wc_paysafe' );
				
				// Make sure we show the same method type that we are updating
				$paysafe_args['options']['displayPaymentMethods'] = 'Paysafe_DD' == $token->get_type() ? array( 'DirectDebit' ) : array( 'Cards' );
				
				// 3DS2 indicate that we are not making a payment
				if ( $gateway->use_layover_3ds2 ) {
					$paysafe_args['options']['threeDS']['authenticationPurpose']        = 'MAINTAIN_CARD';
					$paysafe_args['options']['threeDS']['requestorChallengePreference'] = $gateway->get_option( 'threeds2_challenge_preference', 'NO_PREFERENCE' );
					
					if ( $paysafe_args['options']['threeDS']['transactionIntent'] ) {
						// Needs to be set additionally for add/update/change method verifications
						$paysafe_args['options']['threeDS']['transactionIntent'] = 'CHECK_ACCEPTANCE';
					}
				}
				
				$paysafe_args = apply_filters( 'wc_paysafe_checkoutjs_update_payment_method_props', $paysafe_args, $user, $token );
				
				wp_localize_script( 'paysafe-checkout-js', 'paysafe_iframe_params', $paysafe_args );
				
				$payment_form = new Payment_Form( $gateway );
				$payment_form->output_update_payment_token_fields( $token );
				
				do_action( 'after_woocommerce_update_payment_method_fields' );
			}
			catch ( \Exception $e ) {
				$return_url = wc_get_account_endpoint_url( 'payment-methods' );
				wc_add_notice( $e->getMessage(), 'error' );
				wp_safe_redirect( $return_url );
				exit();
			}
		}
	}
	
	/**
	 * Adds settings to the Advanced tab
	 *
	 * - Update payment method endpoint
	 *
	 * @since 3.6.0
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function add_update_method_endpoint_setting( $settings ) {
		
		$gateway = Factories::get_gateway( 'netbanx' );
		if ( 'checkoutjs' !== $gateway->integration ) {
			return $settings;
		}
		
		$new_settings = array();
		
		foreach ( $settings as $setting ) {
			$new_settings[] = $setting;
			if ( 'woocommerce_myaccount_set_default_payment_method_endpoint' == $setting['id'] ) {
				$new_settings[] = array(
					'title'    => __( 'Paysafe Update Payment Method', 'wc_paysafe' ),
					'desc'     => __( 'Endpoint for the Paysafe - Update Payment Method page.', 'wc_paysafe' ),
					'id'       => 'wc_paysafe_update_payment_method_endpoint',
					'type'     => 'text',
					'default'  => 'update-payment-method',
					'desc_tip' => true,
				);
			}
		}
		
		return $new_settings;
	}
}