<?php
/**
 * FedEx OAuth class file.
 *
 * @package WC_Shipping_Fedex
 */

namespace WooCommerce\FedEx;

use WC_Shipping_Fedex;

/**
 * Class FedEx_OAuth
 *
 * Handles the OAuth authentication for the FedEx REST API.
 *
 * @package WooCommerce\FedEx
 */
class FedEx_OAuth {

	/**
	 * Duration in seconds to cache authentication failures.
	 * Prevents repeated failed API calls when credentials are incorrect.
	 *
	 * @var int
	 */
	const FAILURE_CACHE_DURATION = 300; // 5 minutes

	/**
	 * WC_Shipping_Fedex method.
	 *
	 * @var WC_Shipping_Fedex
	 */
	private $shipping_method;

	/**
	 * The client ID used to authenticate with the FedEx OAuth API.
	 *
	 * @var string
	 */
	private $api_type;

	/**
	 * The client ID used to authenticate with the FedEx OAuth API.
	 *
	 * @var string
	 */
	private $client_id;
	/**
	 * The client secret used to authenticate with the FedEx OAuth API.
	 *
	 * @var string
	 */
	private $client_secret;
	/**
	 * The URL for the FedEx OAuth API endpoint.
	 *
	 * @var string
	 */
	private $endpoint;
	/**
	 * The name of the transient used to store the access token.
	 *
	 * @var string
	 */
	private $access_token_transient_name;

	/**
	 * The name of the transient used to store authentication failures.
	 *
	 * @var string
	 */
	private $auth_failure_transient_name;

	/**
	 * Constructor.
	 *
	 * @param WC_Shipping_Fedex $fedex_shipping_method The FedEx shipping method object.
	 */
	public function __construct( $fedex_shipping_method ) {
		$this->shipping_method             = $fedex_shipping_method;
		$this->api_type                    = $fedex_shipping_method->api_type();
		$this->client_id                   = $fedex_shipping_method->get_client_id();
		$this->client_secret               = $fedex_shipping_method->get_client_secret();
		$this->endpoint                    = $fedex_shipping_method->is_production() ? 'https://apis.fedex.com/oauth/token' : 'https://apis-sandbox.fedex.com/oauth/token';
		$credentials_hash                  = md5( $this->client_id . $this->client_secret . $fedex_shipping_method->api_mode );
		$this->access_token_transient_name = 'woocommerce_fedex_oauth_access_token_' . $credentials_hash;
		$this->auth_failure_transient_name = 'woocommerce_fedex_oauth_auth_failure_' . $credentials_hash;
	}

	/**
	 * Check if we've successfully authenticated.
	 *
	 * @return bool
	 */
	public function is_authenticated() {
		return (bool) $this->get_access_token();
	}

	/**
	 * Clear the cached access token and any cached authentication failures.
	 *
	 * @return void
	 */
	public function clear_access_token() {
		delete_transient( $this->access_token_transient_name );
		$this->clear_failure_cache();
		Logger::debug( 'FedEx_OAuth::clear_access_token: Access token and failure cache cleared.' );
	}

	/**
	 * Validate credentials by attempting to get a token.
	 *
	 * @param string $client_id     The client ID to validate.
	 * @param string $client_secret The client secret to validate.
	 *
	 * @return array Array with 'success' boolean and 'message' string.
	 */
	public function validate_credentials( $client_id, $client_secret ) {
		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return array(
				'success' => false,
				'message' => __( 'Client ID and Client Secret are required.', 'woocommerce-shipping-fedex' ),
			);
		}

		$response = $this->get_access_token_from_fedex( $client_id, $client_secret );

		if ( $response['success'] && ! empty( $response['access_token'] ) ) {
			return array(
				'success' => true,
				'message' => __( 'Successfully authenticated with FedEx.', 'woocommerce-shipping-fedex' ),
			);
		}

		return array(
			'success' => false,
			'message' => __( 'Failed to authenticate with FedEx. Please verify your Client ID and Client Secret are correct.', 'woocommerce-shipping-fedex' ),
		);
	}

	/**
	 * Check if there's a cached authentication failure.
	 *
	 * @return bool True if there's a cached failure, false otherwise.
	 */
	private function has_cached_failure() {
		return false !== get_transient( $this->auth_failure_transient_name );
	}

	/**
	 * Cache an authentication failure to prevent repeated failed API calls.
	 * Note: Server errors (500, 503) are NOT cached as they are temporary issues.
	 *
	 * @return void
	 */
	private function cache_failure() {
		set_transient( $this->auth_failure_transient_name, time(), self::FAILURE_CACHE_DURATION );
		Logger::warning( 'FedEx_OAuth::cache_failure: Authentication failure cached for ' . self::FAILURE_CACHE_DURATION . ' seconds to prevent repeated failed API calls.' );
	}

	/**
	 * Clear any cached authentication failure.
	 *
	 * @return void
	 */
	private function clear_failure_cache() {
		delete_transient( $this->auth_failure_transient_name );
	}

	/**
	 * Get an access token.
	 *
	 * @param bool $force_refresh Force token refresh.
	 *
	 * @return string|bool
	 */
	public function get_access_token( $force_refresh = false ) {

		if ( 'soap' === $this->api_type ) {
			return false;
		}

		// Check for cached authentication failure (unless force refresh is requested).
		if ( ! $force_refresh && $this->has_cached_failure() ) {
			Logger::debug( 'FedEx_OAuth::get_access_token: Returning early due to cached authentication failure. Will retry after cache expires.' );
			return false;
		}

		$access_token = get_transient( $this->access_token_transient_name );

		if ( false === $access_token ) {
			Logger::debug( 'FedEx_OAuth::get_access_token: Access token not found in cache or expired. Requesting new token.' );
		} elseif ( $force_refresh ) {
			Logger::debug( 'FedEx_OAuth::get_access_token: Force refresh requested. Requesting new token.' );
		} else {
			Logger::debug( 'FedEx_OAuth::get_access_token: Using cached access token.' );
		}

		if ( false !== $access_token && ! $force_refresh ) {
			return $access_token;
		}

		$response = $this->get_access_token_from_fedex( $this->client_id, $this->client_secret );

		if ( ! $response['success'] || empty( $response['access_token'] ) || empty( $response['expires_in'] ) ) {
			Logger::error( 'FedEx_OAuth::get_access_token: Invalid response from FedEx OAuth API.' );

			// Only cache authentication failures, not temporary server errors (500, 503).
			// Server errors should be retried on next request.
			$http_code = isset( $response['http_code'] ) ? absint( $response['http_code'] ) : 0;
			if ( $http_code < 500 && 0 !== $http_code ) {
				$this->cache_failure();
			} else {
				Logger::debug( "FedEx_OAuth::get_access_token: Not caching failure - HTTP $http_code is a temporary server error that may resolve quickly." );
			}

			return false;
		}

		// Clear any previous failure cache on successful authentication.
		$this->clear_failure_cache();

		$expires_in = absint( $response['expires_in'] );

		// Unusual short token expiration, do not cache.
		if ( $expires_in <= 60 ) {
			Logger::warning( "Short token expiry: {$expires_in}s. Not caching." );
			// Just return the token, don't cache it.
			return $response['access_token'];
		}

		// Store token with 60 second buffer to prevent edge cases where token expires during request.
		$cache_duration = $expires_in - 60;

		set_transient( $this->access_token_transient_name, $response['access_token'], $cache_duration );

		Logger::debug( "FedEx_OAuth::get_access_token: New token cached successfully. Expires in {$expires_in} seconds, cached for {$cache_duration} seconds." );

		return $response['access_token'];
	}

	/**
	 * Get an access token from the FedEx OAuth API.
	 *
	 * @param string $client_id     Client ID to use for authentication.
	 * @param string $client_secret Client secret to use for authentication.
	 *
	 * @return array The response from the FedEx OAuth API with 'success', 'access_token', 'expires_in', and 'http_code'.
	 */
	private function get_access_token_from_fedex( $client_id, $client_secret ) {
		if ( ! $client_id || ! $client_secret ) {
			Logger::error( 'FedEx_OAuth::get_access_token_from_fedex: Client ID or Client Secret is missing.' );
			return array(
				'success'      => false,
				'http_code'    => 0,
				'access_token' => '',
				'expires_in'   => 0,
			);
		}

		$headers = array(
			'Content-Type' => 'application/x-www-form-urlencoded',
		);

		$body = array(
			'grant_type'    => 'client_credentials',
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
		);

		$response = wp_remote_post(
			$this->endpoint,
			array(
				'headers' => $headers,
				'body'    => $body,
			)
		);

		$response_body = $this->get_response_body( $response );
		$http_code     = is_wp_error( $response ) ? 0 : wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) || empty( $response_body->access_token ) || empty( $response_body->expires_in ) ) {
			$error_message = is_wp_error( $response ) ? $response->get_error_message() : $this->retrieve_error_message_from_response( $response_body );

			Logger::error( "FedEx_OAuth::get_access_token_from_fedex: The FedEx OAuth endpoint returned the following error: $error_message (HTTP $http_code)" );

			return array(
				'success'      => false,
				'http_code'    => $http_code,
				'access_token' => '',
				'expires_in'   => 0,
			);
		}

		Logger::debug( 'FedEx_OAuth::get_access_token_from_fedex: Successfully obtained new access token from FedEx.' );

		return array(
			'success'      => true,
			'http_code'    => $http_code,
			'access_token' => isset( $response_body->access_token ) ? $response_body->access_token : '',
			'expires_in'   => isset( $response_body->expires_in ) ? $response_body->expires_in : 0,
		);
	}

	/**
	 * Get response body.
	 *
	 * @param  array $response FedEx API response.
	 *
	 * @return object
	 */
	private function get_response_body( $response ) {
		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Get error message from FedEx API response.
	 *
	 * @param  object $response FedEx API response.
	 *
	 * @return string
	 */
	private function retrieve_error_message_from_response( $response ) {
		$message = '';
		if ( isset( $response->errors ) && is_array( $response->errors ) ) {
			foreach ( $response->errors as $error ) {
				if ( empty( $error->code ) || empty( $error->message ) ) {
					continue;
				}

				$message .= "\n" . $error->code . ': ' . $error->message;
			}
		}

		return $message;
	}
}
