<?php

namespace WcPaysafe\Tokens;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Customer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the Customer Tokens.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Manage_Tokens {
	
	/**
	 * @var Paysafe
	 */
	protected $plugin;
	public static $running_customer_tokens_transfer = false;
	
	/**
	 * constructor.
	 *
	 * @param \WcPaysafe\Paysafe $plugin
	 */
	public function __construct( Paysafe $plugin ) {
		$this->plugin = $plugin;
	}
	
	/**
	 * Returns the manage tokens URL for the plugin
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public static function manage_tokens_url() {
		return wc_get_account_endpoint_url( 'payment-methods' );
	}
	
	/**
	 * Loads the class hooks
	 *
	 * @since 2.0
	 */
	public function hooks() {
		// Bail, if for some reason we are running WC < 2.6
		if ( ! WC_Compatibility::is_wc_2_6() ) {
			return;
		}
		
		add_filter( 'woocommerce_payment_methods_list_item', array(
			$this,
			'wc_get_account_saved_payment_methods_list_item',
		), 10, 2 );
		
		// Delete the payment method before
		add_action( 'woocommerce_paysafe_payment_token_delete', array(
			$this,
			'delete_payment_token',
		), 10, 2 );
		
		if ( WC_Compatibility::is_wc_3_1_2() ) {
			add_filter( 'woocommerce_credit_card_type_labels', array(
				$this,
				'add_payment_options_labels',
			) );
		} else {
			add_filter( 'wocommerce_credit_card_type_labels', array(
				$this,
				'add_payment_options_labels',
			) );
		}
		
		add_action( 'woocommerce_payment_token_set_default', array(
			$this,
			'woocommerce_payment_token_set_default',
		), 10, 2 );
	}
	
	/**
	 * Delete the Profile after we delete a token
	 *
	 * @since 2.0
	 *
	 * @param int                                                                                               $token_id
	 * @param \WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD|\WC_Payment_Token_Paysafe_Payments_Card $token
	 *
	 * @throws \Exception
	 * @return bool
	 */
	public function delete_payment_token( $token_id, $token ) {
		$allowed_delete_token_types = apply_filters( 'wc_paysafe_allowed_delete_token_types', [
			'Paysafe_CC',
			'Paysafe_DD',
			'Paysafe_Payments_Card',
		], $token );
		
		// Bail, if not our gateway
		if ( ! in_array( $token->get_type(), $allowed_delete_token_types ) ) {
			return false;
		}
		
		try {
			/**
			 * @var \WcPaysafe\Gateways\Redirect\Gateway|\WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway $gateway
			 */
			$gateway = Factories::get_gateway( $token->get_gateway_id() );
			
			// Delete profile
			return $gateway->delete_profile_token( $token );
		}
		catch ( \Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}
	}
	
	/**
	 * Controls the output for credit cards on the my account page.
	 *
	 * @since 2.0
	 *
	 * @param array                                                                        $item          Individual list item from woocommerce_saved_payment_methods_list
	 * @param \WC_Paytrace_Token|\WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD $payment_token The payment token associated with this method entry
	 *
	 * @return array                           Filtered item
	 */
	function wc_get_account_saved_payment_methods_list_item( $item, $payment_token ) {
		// TODO: Change ID if we change the gateway ID
		if ( 'netbanx' !== $payment_token->get_gateway_id() && 'paysafe_checkout_payments' !== $payment_token->get_gateway_id() ) {
			return $item;
		}
		
		if ( 'Paysafe_CC' === $payment_token->get_type() ) {
			$card_type               = $payment_token->get_card_type();
			$item['method']['brand'] = ( ! empty( $card_type ) ? ucfirst( $card_type ) : esc_html__( 'Card', 'woocommerce' ) );
			$item['expires']         = $payment_token->get_expiry_month() . '/' . substr( $payment_token->get_expiry_year(), - 2 );
		} elseif ( 'Paysafe_DD' === $payment_token->get_type() ) {
			$item['method']['brand'] = sprintf( __( '%s bank account' ), strtoupper( $payment_token->get_bank_account_type() ) );
		} elseif ( 'Paysafe_Payments_Card' === $payment_token->get_type() ) {
			$card_type               = $payment_token->get_card_type();
			$item['method']['brand'] = ( ! empty( $card_type ) ? ucfirst( $card_type ) : esc_html__( 'Card', 'woocommerce' ) );
			$item['expires']         = $payment_token->get_expiry_month() . '/' . substr( $payment_token->get_expiry_year(), - 2 );
		}
		
		$item['method']['last4'] = $payment_token->get_last4();
		
		return $item;
	}
	
	/**
	 * Adds the card types to the WC card types
	 *
	 * @since 2.0
	 *
	 * @param $labels
	 *
	 * @return mixed
	 */
	public function add_payment_options_labels( $labels ) {
		$map = apply_filters(
			'paysafe_card_types_map', array(
				'VI' => __( 'Visa', 'wc_paysafe' ),
				'MC' => __( 'Master Card', 'wc_paysafe' ),
				'AM' => __( 'American Express', 'wc_paysafe' ),
				'DC' => __( 'Discover', 'wc_paysafe' ),
				'DI' => __( 'Diners', 'wc_paysafe' ),
			)
		);
		
		foreach ( $map as $key => $string ) {
			if ( ! isset( $labels[ strtolower( $key ) ] ) ) {
				$labels[ strtolower( $key ) ] = $string;
			}
		}
		
		return $labels;
	}
	
	/**
	 *
	 * @param int               $token_id
	 * @param \WC_Payment_Token $wc_token
	 *
	 *
	 * @since   3.3.0
	 * @version 3.3.0
	 */
	public function woocommerce_payment_token_set_default( $token_id, $wc_token = null ) {
		$token = \WC_Payment_Tokens::get( $token_id );
		
		if ( 'netbanx' === $token->get_gateway_id() ) {
			$ps_customer = new Customer_Tokens( get_current_user_id() );
			$ps_customer->set_default_source( $token->get_token() );
		}
	}
}