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

defined( 'ABSPATH' ) or exit;

/**
 * Device data handler class.
 *
 * @since 2.3.0
 */
class Device_Data {


	/** @var string|null current session ID */
	private static ?string $session_id = null;


	/**
	 * Renders the session ID input, to be used on the payment form.
	 *
	 * @since 2.3.0
	 *
	 * @deprecated 2.8.3
	 *
	 * @param string|null $gateway_id unused since 2.8.0
	 * @param string|null $session_id session ID
	 */
	public static function render_session_id_input( ?string $gateway_id = null, ?string $session_id = null ): void {

		_deprecated_function( __METHOD__, '2.8.3' );
	}


	/**
	 * Renders the <noscript> version of the device profiling scripts.
	 *
	 * @since 2.3.0
	 *
	 * @param string $organization_id organization ID
	 * @param string $merchant_id merchant ID
	 * @param string $session_id session ID
	 */
	public static function render_noscript_iframe( string $organization_id, string $merchant_id, string $session_id ): void {

		?>
		<noscript>
			<iframe
				style="width: 100px; height: 100px; border: 0; position: absolute; top: -5000px;"
				src="<?php echo esc_url( self::get_iframe_url( $organization_id, $merchant_id, $session_id ) ); ?>"
			></iframe>
		</noscript>
		<?php
	}


	/**
	 * Gets the stored device data session ID, if any.
	 *
	 * @since 2.3.0
	 *
	 * @param string|null $gateway_id unused since 2.8.0
	 * @return string
	 */
	public static function get_session_id( ?string $gateway_id = null ): string {

		return WC()->session ? (string) WC()->session->get( "wc_cybersource_device_data_session_id", '' ) : '';
	}


	/**
	 * Sets the device data session ID.
	 *
	 * @since 2.3.0
	 *
	 * @param string|null $gateway_id unused since 2.8.0
	 * @param string|null $session_id session ID to set
	 */
	public static function set_session_id( ?string $gateway_id = null, ?string $session_id = null ): void {

		if ( WC()->session && $session_id ) {
			WC()->session->set( "wc_cybersource_device_data_session_id", wc_clean( $session_id ) );
		}
	}


	/**
	 * Starts a Device Data fingerprinting session.
	 *
	 * Since the tags.js only allows to be loaded once per page, we can't start a new session for each gateway on teh same page.
	 *
	 * @return string|null
	 */
	public static function start_session(): ?string {

		if ( ! self::$session_id ) {
			self::$session_id = self::generate_session_id();

			self::set_session_id( null, self::$session_id );
		}

		return self::$session_id;
	}


	/**
	 * Generates a new session UUID.
	 *
	 * @return string
	 */
	public static function generate_session_id(): string {

		return wp_generate_uuid4();
	}


	/**
	 * Gets the device data JS URL.
	 *
	 * @since 2.3.0
	 *
	 * @param string $organization_id organization ID
	 * @param string $merchant_id merchant ID
	 * @param string $session_id session ID
	 * @return string
	 */
	public static function get_js_url( string $organization_id, string $merchant_id, string $session_id ): string {

		return add_query_arg( [
			'org_id'      => wc_clean( $organization_id ),
			'session_id'  => wc_clean( $merchant_id ) . wc_clean( $session_id ),
		], self::get_root_url() . '/tags.js' );
	}


	/**
	 * Gets the device data iframe URL.
	 *
	 * @since 2.3.0
	 *
	 * @param string $organization_id organization ID
	 * @param string $merchant_id merchant ID
	 * @param string $session_id session ID
	 * @return string
	 */
	public static function get_iframe_url( string $organization_id, string $merchant_id, string $session_id ): string {

		return add_query_arg( [
			'org_id'     => wc_clean( $organization_id ),
			'session_id' => wc_clean( $merchant_id ) . wc_clean( $session_id ),
		], self::get_root_url() . '/tags' );
	}


	/**
	 * Gets the device data root URL.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	private static function get_root_url(): string {

		/**
		 * Filters the target device data URL.
		 *
		 * @since 2.3.0
		 *
		 * @param string $url device data URL
		 */
		return apply_filters( 'wc_cybersource_device_data_root_url', 'https://h.online-metrix.net/fp' );
	}


}
