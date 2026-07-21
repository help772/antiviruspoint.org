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

namespace SkyVerge\WooCommerce\Cybersource\Gateway;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Cybersource\Gateway;
use SkyVerge\WooCommerce\Cybersource\Gateway\ThreeD_Secure\Frontend;
use SkyVerge\WooCommerce\Cybersource\Gateway\ThreeD_Secure\AJAX;
use SkyVerge\WooCommerce\Cybersource\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * 3D Secure (Payer Authentication) handler.
 *
 * @since 2.3.0
 */
class ThreeD_Secure {


	/** @var Gateway gateway instance */
	private Gateway $gateway;

	/** @var bool whether 3D Secure is enabled */
	private bool $is_enabled = false;

	/** @var bool whether test mode is enabled */
	private bool $is_test_mode = false;

	/** @var string[] enabled card types */
	private array $enabled_card_types = [];

	/** @var Frontend frontend handler instance */
	private Frontend $frontend_handler;

	/** @var AJAX AJAX handler instance */
	private AJAX $ajax_handler;


	/**
	 * Initializes the handler.
	 *
	 * @since 2.3.0
	 *
	 * @return ThreeD_Secure
	 */
	public function init(): self {

		if ( ! $this->is_enabled() ) {
			return $this;
		}

		if ( wp_doing_ajax() ) {
			$this->ajax_handler = new AJAX( $this );
		} elseif ( ! is_admin() ) {
			$this->frontend_handler = new Frontend( $this );
		}

		add_action( 'woocommerce_api_' . $this->get_step_up_response_handler_action_name(), [ $this, 'handle_step_up_response' ] );

		return $this;
	}


	/** Setter methods ************************************************************************************************/


	/**
	 * @param Gateway $gateway
	 * @return $this
	 */
	public function set_gateway( Gateway $gateway ): self {

		$this->gateway = $gateway;

		return $this;
	}


	/**
	 * Set whether 3D Secure is enabled.
	 *
	 * @since 2.3.0
	 *
	 * @param bool $is_enabled whether 3D Secure is enabled
	 * @return ThreeD_Secure
	 */
	public function set_enabled( bool $is_enabled ): self {

		$this->is_enabled = $is_enabled;

		return $this;
	}


	/**
	 * Sets the enabled card types.
	 *
	 * @since 2.3.0
	 *
	 * @param string[] $card_types enabled card types
	 * @return ThreeD_Secure
	 */
	public function set_enabled_card_types( array $card_types ): self {

		$this->enabled_card_types = array_intersect( $card_types, array_keys( self::get_supported_card_types() ) );

		return $this;
	}


	/**
	 * Set whether test mode is enabled.
	 *
	 * @since 2.3.0
	 *
	 * @param bool $is_test_mode whether test mode is enabled
	 * @return ThreeD_Secure
	 */
	public function set_test_mode( bool $is_test_mode ): self {

		$this->is_test_mode = $is_test_mode;

		return $this;
	}


	/** Conditional methods *******************************************************************************************/


	/**
	 * Determines whether 3D Secure is enabled.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {

		/**
		 * Filters whether 3D Secure is enabled.
		 *
		 * @since 2.3.0
		 *
		 * @param bool $is_enabled whether 3D Secure is enabled
		 */
		return (bool) apply_filters( 'wc_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_3d_secure_is_enabled', $this->is_enabled );
	}


	/**
	 * Determines whether test mode is enabled.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_test_mode(): bool {

		return $this->is_test_mode;
	}


	/** Getter methods ************************************************************************************************/


	/**
	 * Gets the enabled card types.
	 *
	 * @since 2.3.0
	 *
	 * @return string[]
	 */
	public function get_enabled_card_types(): array {

		/**
		 * Filters the card types enabled for 3D Secure.
		 *
		 * @since 2.3.0
		 *
		 * @param string[] $card_types card types enabled for 3D Secure
		 */
		return (array) apply_filters( 'wc_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_3d_secure_enabled_card_types', $this->enabled_card_types );
	}


	/**
	 * Gets the card types supported by 3D Secure.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public static function get_supported_card_types(): array {

		return [
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX       => __( 'American Express SafeKey', 'woocommerce-gateway-cybersource' ),
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB => __( 'Diners International', 'woocommerce-gateway-cybersource' ),
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER   => __( 'Discover ProtectBuy', 'woocommerce-gateway-cybersource' ),
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB        => __( 'JCB J-Secure', 'woocommerce-gateway-cybersource' ),
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD => __( 'MasterCard SecureCode and Identity Check', 'woocommerce-gateway-cybersource' ),
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA       => __( 'Verified by Visa', 'woocommerce-gateway-cybersource' ),
		];
	}


	/**
	 * Gets the gateway instance.
	 *
	 * @since 2.3.0
	 *
	 * @return Gateway
	 */
	public function get_gateway(): Gateway {

		return $this->gateway;
	}


	/**
	 * Gets the step-up challenge return URL action handler name.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	protected function get_step_up_response_handler_action_name(): string {

		return 'wc_' . $this->get_gateway()->get_id() . '_handle_payer_authentication_step_up_response';
	}


	/**
	 * Gets the step-up challenge return URL.
	 *
	 * @since 2.8.0
	 *
	 * @internal
	 *
	 * @return string
	 */
	public function get_step_up_challenge_return_url(): string {

		return add_query_arg( [
			'wc-api' => $this->get_step_up_response_handler_action_name(),
		], home_url( '/' ) );
	}


	/**
	 * Handles the step-up challenge response.
	 *
	 * This method is called when the step-up iframe redirects back to the return URL. Note that at this point,
	 * we don't know yet if the customer entered a valid code, or if they canceled the challenge.
	 *
	 * The authentication needs to be validated, which can happen in a separate request or when trying to authorize the
	 * payment.
	 *
	 * @since 2.8.0
	 *
	 * @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-stepup-frame-intro/pa2-ccdc-stepup-frame-receiving-stepup-results.html
	 *
	 * @return void
	 */
	public function handle_step_up_response(): void {

		// notify the frontend that the challenge is complete
		$origin = get_site_url( null, '', is_ssl() ? 'https' : 'http' );
		?>

		<script type="text/javascript">
			window.onload = function() {
				window.parent.postMessage( 'challenge_complete', '<?php echo esc_attr( $origin ); ?>' );
			};
		</script>
		<?php

		die;
	}


	/**
	 * Sets the Payer Authentication setup reference ID.
	 *
	 * @since 2.8.0
	 * @internal
	 *
	 * @param string $reference_id
	 * @return $this
	 */
	public function set_reference_id( string $reference_id ): self {
		WC()->session->set( 'cybersource_threed_secure_reference_id', $reference_id );

		return $this;
	}


	/**
	 * Sets the Payer Authentication check enrollment response status.
	 *
	 * @since 2.8.0
	 * @internal
	 *
	 * @param string $status
	 * @return $this
	 */
	public function set_enrollment_status(string $status ): self {
		WC()->session->set( 'wc_cybersource_threed_secure_enrollment_status', $status );

		return $this;
	}


	/**
	 * Sets the consumer authentication information in session.
	 *
	 * @since 2.8.0
	 * @internal
	 *
	 * @param object $data
	 * @return $this
	 */
	public function set_consumer_authentication_information( object $data ): self {
		WC()->session->set( 'wc_cybersource_threed_secure_consumer_authentication_information', $data );

		return $this;
	}


	/**
	 * Gets the Payer Authentication Request reference ID.
	 *
	 * @see AJAX::setup()
	 * @see https://developer.cybersource.com/docs/cybs/en-us/apifields/reference/all/rest/api-fields/cons-auth-info/cons-auth-info-reference-id.html
	 *
	 * @since 2.8.0
	 *
	 * @return string|null
	 */
	public function get_reference_id(): ?string {

		return WC()->session ? WC()->session->get( 'cybersource_threed_secure_reference_id' ) : null;
	}


	/**
	 * Gets the Payer Authentication check enrollment response status
	 *
	 * @see AJAX::check_enrollment()
	 * @see https://developer.cybersource.com/docs/cybs/en-us/payer-authentication/developer/all/rest/payer-auth/pa2-ccdc-enroll-intro/pa2-ccdc-enroll-interpreting-response.html
	 *
	 * @since 2.8.0
	 *
	 * @return string|null
	 */
	public function get_enrollment_status(): ?string {

		return WC()->session ? WC()->session->get( 'wc_cybersource_threed_secure_enrollment_status' ) : null;
	}


	/**
	 * Gets the consumer authentication information from session.
	 *
	 * This data is stored in session when checking enrollment.
	 *
	 * @see AJAX::check_enrollment()
	 *
	 * @since 2.8.0
	 *
	 * @return object|null
	 */
	public function get_consumer_authentication_information(): ?object {

		$data = WC()->session ? WC()->session->get( 'wc_cybersource_threed_secure_consumer_authentication_information' ) : null;

		return ! empty( $data ) ? (object) $data : null;
	}


	/**
	 * Clears 3DS session data.
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	public function clear_session_data(): void {

		unset(
			WC()->session->wc_cybersource_threed_secure_reference_id,
			WC()->session->wc_cybersource_threed_secure_enrollment_status,
			WC()->session->wc_cybersource_threed_secure_consumer_authentication_information
		);
	}


}
