<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main API class. Contains general tasks used by both Requests and Response processes.
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Abstract_Hosted {
	
	/**
	 * WC_Payment_Gateway
	 *
	 * @var null
	 */
	private $gateway = null;
	private $client = null;
	
	/**
	 * Constructor of the class
	 *
	 * @since 2.0
	 *
	 * @param \WC_Payment_Gateway $gateway
	 */
	public function __construct( \WC_Payment_Gateway $gateway ) {
		$this->set_gateway( $gateway );
	}
	
	/**
	 * Sets the gateway class to a class variable
	 *
	 * @since 2.0
	 *
	 * @param \WC_Payment_Gateway $gateway
	 */
	private function set_gateway( \WC_Payment_Gateway $gateway ) {
		$this->gateway = $gateway;
	}
	
	/**
	 * Returns the variable with the gateway class
	 *
	 * @since 2.0
	 *
	 * @return Gateway
	 */
	public function get_gateway() {
		return $this->gateway;
	}
	
	/**
	 * Returns the initiated API Client class.
	 *
	 * Double checks that the API Client class is initiated and initiates it, if it is not.
	 *
	 * @since 2.0
	 * @throws \Exception
	 * @return \Paysafe\PaysafeApiClient
	 */
	public function get_client() {
		if ( null == $this->client ) {
			$this->client = $this->set_client();
			$this->client->setApiEndPoint( $this->get_base_sdk_url() );
		}
		
		return $this->client;
	}
	
	/**
	 * Returns the base URL. We need this because we combine the Legacy Hosted API and the new API SDK
	 * @return string
	 */
	public function get_base_sdk_url() {
		if ( 'yes' == $this->get_gateway()->get_option( 'testmode' ) ) {
			$url = "https://api.test.netbanx.com";
		} else {
			$url = "https://api.netbanx.com";
		}
		
		return $url;
	}
	
	/**
	 * Loads the SDK and initiates the API Client class
	 *
	 * @since 2.0
	 *
	 * @return \Paysafe\PaysafeApiClient
	 * @throws \Exception
	 */
	private function set_client() {
		if ( ! class_exists( 'Paysafe\\PaysafeApiClient' ) ) {
			include_once Paysafe::plugin_path() . '/vendor/paysafe-sdk/paysafe.php';
		}
		
		return new \Paysafe\PaysafeApiClient(
			$this->get_api_key( $this->get_api_string() ),
			$this->get_api_secret( $this->get_api_string() ),
			$this->get_request_environment()
		);
	}
	
	/**
	 * Retrieves the API key from the gateway settings
	 *
	 * @since 2.3
	 *
	 * @return mixed
	 */
	public function get_api_string() {
		// Note: This method is only here for backward compatibility.
		$string = $this->get_gateway()->get_option( 'api_user_name' ) . ':' . $this->get_gateway()->get_option( 'api_password' );
		
		/**
		 * @deprecated 'wc_netbanx_filter_api_key' Will be removed soon, use 'wc_paysafe_filter_api_key'
		 */
		$key = apply_filters( 'wc_netbanx_filter_api_key', $string );
		$key = apply_filters( 'wc_paysafe_filter_api_key', $key );
		
		return $key;
	}
	
	/**
	 * Returns the API Key from the given API String
	 *
	 * @since 2.0
	 *
	 * @param string $api_string
	 *
	 * @return mixed
	 */
	private function get_api_key( $api_string ) {
		$auth_credentials = explode( ':', $api_string );
		
		return Paysafe::get_field( 0, $auth_credentials );
	}
	
	/**
	 * Returns the API Secret from the given API String
	 *
	 * @since 2.0
	 *
	 * @param string $api_string
	 *
	 * @return mixed
	 */
	private function get_api_secret( $api_string ) {
		$auth_credentials = explode( ':', $api_string );
		
		return Paysafe::get_field( 1, $auth_credentials );
	}
	
	/**
	 * Returns the Paysafe environment based on the testmode plugin setting
	 *
	 * @return mixed
	 */
	private function get_request_environment() {
		if ( 'yes' == $this->get_gateway()->get_option( 'testmode' ) ) {
			$environment = \Paysafe\Environment::TEST;
		} else {
			$environment = \Paysafe\Environment::LIVE;
		}
		
		return $environment;
	}
	
	/**
	 * Save a payment profile to the customer meta data
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param object    $response
	 *
	 * @return void
	 */
	public function save_customer_payment_profile( \WC_Order $order, $response ) {
		if ( ! isset( $response->profile ) ) {
			return;
		}
		
		// Don't save profiles for guest customers
		$user_id = $order->get_user_id();
		if ( '' != $user_id || 0 != $user_id ) {
			$this->save_profile_to_user( $user_id, $response );
		}
	}
	
	/**
	 * Save profile data to the order meta
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param object    $response
	 */
	protected function save_profile_to_order( \WC_Order $order, $response ) {
		// Get any existing profile values
		$profile = $this->get_order_profile_id( WC_Compatibility::get_order_id( $order ) );
		$token   = $this->get_order_profile_token( WC_Compatibility::get_order_id( $order ) );
		
		wc_paysafe_add_debug_log( 'Saving to Order.' );
		
		$allowed_saving_status = $this->allowed_order_status_to_save_profile();
		
		$ps_order = new Paysafe_Order( $order );
		
		// Update the profile id, if needed
		if ( isset( $response->profile->id )
		     && $profile != $response->profile->id
		     // TODO: Save only for orders that will not be reused for other customers as below
		     && ( in_array( $order->get_status(), $allowed_saving_status ) )
		) {
			wc_paysafe_add_debug_log( 'Saved ID' );
			$ps_order->save_order_profile_id( $response->profile->id );
		}
		
		// Update token if it's present and different then the already existing
		if ( isset( $response->profile->paymentToken )
		     && $token != $response->profile->paymentToken
		     // TODO: Save only for orders that will not be reused for other customers as below
		     && in_array( $order->get_status(), $allowed_saving_status )
		) {
			wc_paysafe_add_debug_log( 'Saved Token' );
			$ps_order->save_order_profile_token( $response->profile->paymentToken );
		}
	}
	
	/**
	 * Save profile data to the user meta
	 *
	 * @since 2.0
	 *
	 * @param int    $user_id
	 * @param object $response
	 */
	private function save_profile_to_user( $user_id, $response ) {
		// Get any existing profile values
		$profile = $this->get_user_profile_id( $user_id );
		$token   = $this->get_user_profile_token( $user_id );
		
		wc_paysafe_add_debug_log( 'Saving to User.' );
		
		// Update the profile id, if needed
		if ( isset( $response->profile->id ) && $profile != $response->profile->id ) {
			wc_paysafe_add_debug_log( 'Saved ID' );
			update_user_meta( $user_id, $this->get_user_profile_id_field_name(), $response->profile->id );
		}
		
		// Update token if it's present and different then the already existing
		if ( isset( $response->profile->paymentToken ) && $token != $response->profile->paymentToken ) {
			wc_paysafe_add_debug_log( 'Saved Token' );
			update_user_meta( $user_id, $this->get_user_profile_token_field_name(), $response->profile->paymentToken );
		}
	}
	
	/**
	 * Returns profile ID saved to the user meta
	 *
	 * @since 2.0
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function get_user_profile_id( $user_id ) {
		return get_user_meta( $user_id, $this->get_user_profile_id_field_name(), true );
	}
	
	/**
	 * Returns profile token saved to the user meta
	 *
	 * @since 2.0
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function get_user_profile_token( $user_id ) {
		return get_user_meta( $user_id, $this->get_user_profile_token_field_name(), true );
	}
	
	/**
	 * Returns profile ID saved to the order meta
	 *
	 * @since 2.0
	 *
	 * @param int $order_id
	 *
	 * @return mixed
	 */
	public function get_order_profile_id( $order_id ) {
		$ps_order = new Paysafe_Order( wc_get_order( $order_id ) );
		
		return $ps_order->get_order_profile_id();
	}
	
	/**
	 * Returns profile token saved to the order meta
	 *
	 * @since 2.0
	 *
	 * @param int $order_id
	 *
	 * @return mixed
	 */
	public function get_order_profile_token( $order_id ) {
		$ps_order = new Paysafe_Order( wc_get_order( $order_id ) );
		
		return $ps_order->get_order_profile_token();
	}
	
	/**
	 * Returns order profile ID meta field name
	 *
	 * @since 2.0
	 * @return string
	 */
	private function get_order_profile_id_field_name() {
		return '_netbanx_hosted_order_profile_id';
	}
	
	/**
	 * Returns order profile token meta field name
	 *
	 * @since 2.0
	 * @return string
	 */
	private function get_order_profile_token_field_name() {
		return '_netbanx_hosted_order_profile_token';
	}
	
	/**
	 * Returns user profile ID meta field name
	 *
	 * @since 2.0
	 * @return string
	 */
	private function get_user_profile_id_field_name() {
		return '_netbanx_hosted_customer_profile_id';
	}
	
	/**
	 * Returns user profile token meta field name
	 *
	 * @since 2.0
	 * @return string
	 */
	private function get_user_profile_token_field_name() {
		return '_netbanx_hosted_customer_profile_token';
	}
	
	/**
	 * Format amount for requests. Amount should be with no decimals and no leading 0.
	 *
	 * @since 2.0
	 *
	 * @param $amount
	 *
	 * @return string
	 */
	public function format_amount( $amount ) {
		$formatted = ltrim( number_format( $amount, 2, '', '' ), '0' );
		
		// Since we are trimming 0 we can end up with an empty string on free orders
		// so in this case make sure amount is 0.
		if ( '' == $formatted ) {
			$formatted = 0;
		}
		
		return $formatted;
	}
	
	/**
	 * Convert string to UTF-8
	 *
	 * @since 2.0
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public function convert_to_utf( $str ) {
		if ( ! function_exists( 'mb_convert_encoding' ) ) {
			return wp_check_invalid_utf8( $str, true );
		}
		
		return mb_convert_encoding( $str, 'utf-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS,windows-1251' );
	}
	
	/**
	 * Returns the Paysafe Response object ID also referred as Transaction ID.
	 *
	 * This ID can be used to run re-bill transactions.
	 *
	 * @deprecated Will be removed in the next major version (4.0.0) use \WcPaysafe\Paysafe_Order::get_payment_order_id() instead
	 *
	 * @since      2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return mixed
	 */
	public function get_payment_order_id( \WC_Order $order ) {
		$ps_order = new Paysafe_Order( $order );
		
		return $ps_order->get_payment_order_id();
	}
	
	/**
	 * Formats and returns a the passed string
	 *
	 * @since 2.0
	 *
	 * @param string $string            String to be formatted
	 * @param int    $limit             Limit characters of the string
	 * @param bool   $remove_restricted Whether to remove restricted characters
	 *
	 * @return string
	 */
	public function format_string( $string, $limit, $remove_restricted = true ) {
		if ( strlen( $string ) > $limit ) {
			$string = substr( $string, 0, ( $limit - 3 ) ) . '...';
		}
		
		if ( $remove_restricted ) {
			$string = $this->remove_restricted_characters( $string );
		}
		
		return html_entity_decode( $this->convert_to_utf( $string ), ENT_NOQUOTES, 'UTF-8' );
	}
	
	/**
	 * Removes Paysafe request restricted characters from a string.
	 *
	 * 'paysafe_restricted_characters' - can be used to add to the restricted characters
	 *
	 * @since 2.1
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function remove_restricted_characters( $string ) {
		/**
		 * @deprecated netbanx_restricted_characters is deprecated use the filter below
		 */
		$restricted_characters = apply_filters(
			'netbanx_restricted_characters',
			array( '"', ';', '^', '*', '<', '>', '/', '[', ']', "\\", PHP_EOL )
		);
		
		$restricted_characters = apply_filters(
			'paysafe_restricted_characters',
			$restricted_characters
		);
		
		return str_replace( $restricted_characters, '', $string );
	}
	
	/**
	 * Returns the allowed order statuses, in which we can save the customer Paysafe profile to the order.
	 * We don't want to save the profiles too early in the order process.
	 * We want to make sure that the order is at least in a status that will not get overwritten by the WC order generation process.
	 *
	 * @since. 2.3
	 *
	 * @return mixed
	 */
	public function allowed_order_status_to_save_profile() {
		/**
		 * @deprecated wc_netbanx_allowed_order_status_to_save_profile is deprecated use the action below
		 */
		$status = apply_filters(
			'wc_netbanx_allowed_order_status_to_save_profile',
			array(
				'processing',
				'on-hold',
				'completed',
			)
		);
		
		$status = apply_filters(
			'wc_paysafe_allowed_order_status_to_save_profile',
			$status
		);
		
		return $status;
	}
}