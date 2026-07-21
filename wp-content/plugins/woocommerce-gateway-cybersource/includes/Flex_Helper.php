<?php
/**
 * WooCommerce CyberSource
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce CyberSource to newer
 * versions in the future. If you wish to customize WooCommerce CyberSource for your
 * needs please refer to http://docs.woocommerce.com/document/cybersource-payment-gateway/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use SkyVerge\WooCommerce\Cybersource\API\DataObjects\CaptureContextData;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * Helper class for Flex Microform.
 *
 * @since 2.3.0
 */
class Flex_Helper {


	/**
	 * Configures the Flex Microform script using the capture context JWT.
	 *
	 * Extracts the clientLibrary and clientLibraryIntegrity values from the JWT and adds them to the script tag.
	 * This method should be called whenever the Flex Microform script is enqueued.
	 *
	 * @see https://developer.cybersource.com/docs/cybs/en-us/digital-accept-flex-api/developer/all/rest/flex-api/microform-integ-v2/microform-integ-getting-started-v2/setting-up-client-side-v2.html
	 *
	 * @since 2.9.0
	 *
	 * @param string $capture_context the capture context JWT value
	 * @return void
	 */
	public static function addFlexMicroformScriptHooks(): void {
		/*
		 * Getting the correct library from CyberSource involves an API request. We don't want to do this unnecessarily
		 * when `wp_register_script()` is called, but the script is never enqueued. So we delay setting the _real_ src
		 * until the script actually gets used, which is why we use the `script_loader_src` filter.
		 */
		add_filter('script_loader_src', [static::class, 'maybeModifyScriptSrc'], 10, 2);
		add_filter('script_loader_tag', [static::class, 'maybeInjectClientLibraryIntegrity'], 10, 2);
	}

	/**
	 * Conditionally manipulates the script `src` parameter to use the "clientLibrary" value from the capture context data.
	 * @internal
	 * @since 2.9.0
	 */
	public static function maybeModifyScriptSrc($src, $handle) : string
	{
		if ($handle !== 'wc-cybersource-flex-microform') {
			return $src;
		}

		$captureContextData = Flex_Helper::extract_data_from_capture_context(CaptureContextRetriever::getCaptureContext());
		if (! $captureContextData) {
			return $src;
		}

		if (is_string($src) && ! str_contains($src, $captureContextData->clientLibrary)) {
			// keep any query string parameters
			return trim($captureContextData->clientLibrary . '?' . (explode('?', $src)[1] ?? ''), '?');
		}

		return $src;
	}

	/**
	 * Conditionally manipulates the script tag to append a `integrity` and `crossorigin` parameters.
	 * @internal
	 * @since 2.9.0
	 */
	public static function maybeInjectClientLibraryIntegrity($tag, $handle) : string
	{
		if ($handle !== 'wc-cybersource-flex-microform') {
			return $tag;
		}

		$captureContextData = Flex_Helper::extract_data_from_capture_context(CaptureContextRetriever::getCaptureContext());
		if (! $captureContextData) {
			return $tag;
		}

		if (is_string($tag) && ! str_contains($tag, 'integrity="')) {
			return str_replace('></script>', ' integrity="' . esc_attr($captureContextData->clientLibraryIntegrity) . '" crossorigin="anonymous"></script>', $tag);
		}

		return $tag;
	}

	/**
	 * Extracts the client library and integrity values from a JWT capture context.
	 *
	 * @since 2.9.0
	 *
	 * @param string $capture_context JWT capture context value
	 * @return ?CaptureContextData
	 */
	public static function extract_data_from_capture_context(string $capture_context) : ?CaptureContextData
	{
		if (empty($capture_context) || ! str_contains($capture_context, '.')) {
			return null;
		}

		$pieces = explode('.', $capture_context);
		$payload = $pieces[1] ?? null;
		if (! $payload) {
			return null;
		}

		$decodedPayload = base64_decode($payload);
		if (! $decodedPayload) {
			return null;
		}

		$payload = json_decode($decodedPayload, true);
		$captureContextData = $payload['ctx'][0]['data'] ?? null;

		if (empty($captureContextData) || ! is_array($captureContextData) || empty($captureContextData['clientLibraryIntegrity']) || empty($captureContextData['clientLibrary'])) {
			return null;
		}

		return new CaptureContextData(
			clientLibraryIntegrity: (string) $captureContextData['clientLibraryIntegrity'],
			clientLibrary: (string) $captureContextData['clientLibrary']
		);
	}


	/**
	 * Decodes a Flex Microform token.
	 *
	 * @since 2.3.0
	 *
	 * @param string $form_jwt JWT value created by the Microform JS
	 * @param string $api_jwt JWT value returned by the Flex Keys API
	 * @return array
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public static function decode_flex_token( $form_jwt, $api_jwt ) {

		$payload = JWT::decode( $form_jwt, self::get_public_key_set( $api_jwt ) );

		return json_decode( json_encode( $payload ), true );
	}


	/**
	 * Gets the public key set from the given Flex API JWT.
	 *
	 * @since 2.3.0
	 *
	 * @param string $jwt encoded JWT
	 * @return array
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	private static function get_public_key_set( $jwt ) {

		$payload = self::get_jwt_payload( $jwt );

		if ( empty( $payload['flx']['jwk'] ) ) {
			throw new Framework\SV_WC_Plugin_Exception( 'JWK claim is missing' );
		}

		$payload = [
			'keys' => [
				array_merge( ['alg' => 'RS256' ], $payload['flx']['jwk'] ),
			],
		];

		return JWK::parseKeySet( $payload );
	}


	/**
	 * Gets the payload of a JWT as an array.
	 *
	 * Note: this does not validate the JWT. self::decode_flex_token() or JWT::decode() should be used when validation
	 * is required.
	 *
	 * @since 2.3.0
	 *
	 * @param string $jwt JWT value
	 * @return array
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	private static function get_jwt_payload( $jwt ) {

		[ $headers, $payload, $sig ] = explode( '.', $jwt );

		$payload = json_decode( base64_decode( $payload ), true );

		if ( ! is_array( $payload ) ) {
			throw new Framework\SV_WC_Plugin_Exception( 'JWT is invalid' );
		}

		return $payload;
	}


}
