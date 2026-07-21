<?php namespace WcPaysafe\Helpers;

use WcPaysafe\Api\Cards\Responses\Authorizations;
use WcPaysafe\Api\Cards\Responses\Refunds;
use WcPaysafe\Api\Cards\Responses\Settlements;
use WcPaysafe\Api\Cards\Responses\Verifications;
use WcPaysafe\Api\Config\Redirect;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Direct_Debit\Responses\Purchases;
use WcPaysafe\Api\Response_Abstract;
use WcPaysafe\Gateways\Redirect\Checkout\Response_Processors\Cards_Processor;
use WcPaysafe\Gateways\Redirect\Checkout\Response_Processors\Checkoutjs_Processor;
use WcPaysafe\Gateways\Redirect\Checkout\Response_Processors\Direct_Debit_Processor;
use WcPaysafe\Gateways\Redirect\Checkout\Response_Processors\Vault_Processor;
use WcPaysafe\Gateways\Redirect\Gateway;

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
class Factories {
	
	/**
	 * @param \WC_Payment_Gateway|Gateway $gateway
	 * @param Data_Source_Interface       $data_source
	 * @param string                      $payment_type cards|directdebit|interac
	 *
	 * @throws \Exception
	 * @return \WcPaysafe\Api\Client|\WcPaysafe\Api_Payments\Client
	 */
	public static function get_api_client( $gateway, $data_source = null, $payment_type = 'cards' ) {
		if ( ! class_exists( 'Paysafe\\PaysafeApiClient' ) ) {
			include_once \WcPaysafe\Paysafe::plugin_path() . '/vendor/paysafe-sdk/paysafe.php';
		}
		
		if ( 'paysafe_checkout_payments' == $gateway->id ) {
			/**
			 * @var \WcPaysafe\Api_Payments\Config\Redirect $gateway_config
			 */
			$gateway_config = self::get_gateway_config( $gateway );
			
			$client = new \WcPaysafe\Api_Payments\Client(
				$gateway_config,
				$gateway_config->get_account_id( $data_source, $payment_type )
			);
			
			return $client;
		}
		
		/**
		 * @var Redirect $gateway_config
		 */
		$gateway_config = self::get_gateway_config( $gateway );
		
		return new \WcPaysafe\Api\Client(
			new \Paysafe\PaysafeApiClient(
				$gateway_config->get_option( 'api_user_name' ),
				$gateway_config->get_option( 'api_password' ),
				$gateway_config->is_testmode() ? \Paysafe\Environment::TEST : \Paysafe\Environment::LIVE,
				$gateway_config->get_account_id( $data_source, $payment_type )
			),
			$gateway_config
		);
	}
	
	/**
	 * @param $gateway
	 *
	 * @throws \Exception
	 * @return \WcPaysafe\Api\Config\Redirect|\WcPaysafe\Api_Payments\Config\Redirect
	 */
	public static function get_gateway_config( $gateway ) {
		if ( $gateway instanceof \WcPaysafe\Gateways\Redirect\Gateway ) {
			return new Redirect( $gateway );
		}
		
		if ( $gateway instanceof \WcPaysafe\Gateways\Redirect\Payments\Payments_Gateway ) {
			return new \WcPaysafe\Api_Payments\Config\Redirect( $gateway );
		}
		
		throw new \Exception( __( 'Incorrect gateway was passed to the API client', 'wc_paysafe' ) );
	}
	
	/**
	 * Returns the gateway class by looking through the available gateways and matching the gateway by ID
	 *
	 * @param $id
	 *
	 * @return bool|\WC_Payment_Gateway|Gateway
	 */
	public static function get_gateway( $id ) {
		foreach ( WC()->payment_gateways()->payment_gateways() as $gateway ) {
			if ( $id == $gateway->id ) {
				return $gateway;
			}
		}
		
		return false;
	}
	
	/**
	 * Receives the API response and loads it into the specific integration response classes.
	 *
	 * @param Response_Abstract|\WcPaysafe\Api_Payments\Response_Abstract $response
	 * @param string                                                      $integration 'checkoutjs', 'paysafejs' or 'direct'
	 *
	 * @return Checkoutjs_Processor|\WcPaysafe\Gateways\Redirect\Payments\Response_Processors\Checkoutjs_Processor Returns the response class of the integration provided
	 */
	public static function load_response_processor( $response, $integration = 'checkoutjs' ) {
		
		if ( '' == $integration ) {
			throw new \InvalidArgumentException( __( 'Integration type is required in order to load the proper response object', 'wc_paysafe' ) );
		}
		
		// TODO: Needs improvement to count integrations other than checkoutjs
		if ( 'checkoutjs' == $integration ) {
			if ( $response instanceof Purchases ) {
				return new Direct_Debit_Processor( $response );
			} elseif ( $response instanceof Authorizations
			           || $response instanceof Settlements
			           || $response instanceof Refunds
			           || $response instanceof Verifications
			) {
				return new Cards_Processor( $response );
			} else {
				return new Vault_Processor( $response );
			}
		} elseif ( 'checkout_payments' == $integration ) {
			return new \WcPaysafe\Gateways\Redirect\Payments\Response_Processors\Cards_Processor( $response );
		}
		
		return null;
	}
}