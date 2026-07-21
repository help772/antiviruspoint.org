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

namespace SkyVerge\WooCommerce\Cybersource\Gateway\Visa_Checkout;

use SkyVerge\WooCommerce\Cybersource\Device_Data;
use SkyVerge\WooCommerce\Cybersource\Gateway\Visa_Checkout;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Payment Form Class.
 *
 * Handles rendering the payment form for Visa Checkout.
 *
 * @since 2.3.0
 */
class Payment_Form extends Framework\SV_WC_Payment_Gateway_Payment_Form {


	/** @var string URL for the Sandbox version of the Visa Checkout button image */
	const BUTTON_URL_SANDBOX = 'https://sandbox.secure.checkout.visa.com/wallet-services-web/xo/button.png';

	/** @var string URL for the Production version of the Visa Checkout button image */
	const BUTTON_URL_PRODUCTION = 'https://secure.checkout.visa.com/wallet-services-web/xo/button.png';


	/** @var Visa_Checkout gateway for this payment form */
	protected $gateway;

	/** @var string JS handler class name */
	protected $js_handler_base_class_name = 'WC_Cybersource_Visa_Checkout_Payment_Form_Handler';


	/**
	 * Returns the gateway for this form.
	 *
	 * @since 2.3.0
	 *
	 * @return Visa_Checkout
	 */
	public function get_gateway() {

		return $this->gateway;
	}


	/**
	 * Gets the JS handler class name.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_js_handler_class_name() {

		return $this->js_handler_base_class_name;
	}


	/**
	 * Gets the JS args for the payment form handler.
	 *
	 * render_js() will apply filters to the returned array of args.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_js_handler_args() {

		return array_merge(
			parent::get_js_handler_args(),
			[
				'api_key'    => $this->get_gateway()->get_visa_checkout_api_key(),
				'settings'   => $this->get_gateway()->get_visa_checkout_settings(),
				'i18n'       => [
					'generic_error_message'            => __( 'An error occurred, please try again or try an alternate form of payment', 'woocommerce-gateway-cybersource' ),
					'initialization_error_message'     => __( 'An error occurred trying to setup Visa Checkout.', 'woocommerce-gateway->cybersource' ),
					'missing_payment_response_message' => __( 'Visa Checkout payment response is missing', 'woocommerce-gateway-cybersource' ),
				],
			]
		);
	}


	/**
	 * Renders the Visa Checkout button.
	 *
	 * This also renders 2 hidden inputs:
	 *
	 * 1. wc_cybersource_visa_checkout_payment_request - payment request for Visa Checkout JS SDK
	 * 2. wc_cybersource_visa_checkout_payment_response - Visa Checkout authorized payment response
	 *
	 * Note these are rendered as hidden inputs and not passed to the script constructor
	 * because these will be refreshed and re-rendered when the checkout updates,
	 * which is important for the accuracy of things like the order total.
	 *
	 * @since 2.3.0
	 */
	public function render_payment_fields() {

		parent::render_payment_fields();

		?>

		<div id="wc-<?php echo esc_attr( $this->get_gateway()->get_id_dasherized() ); ?>-container">
			<?php $this->render_button(); ?>

			<input id="wc-<?php echo esc_attr( $this->get_gateway()->get_id_dasherized() ); ?>-payment-request"
			       type="hidden"
			       name="wc_<?php echo esc_attr( $this->get_gateway()->get_id() ); ?>_payment_request"
			       value="<?php echo esc_attr( $this->get_payment_request() ); ?>" />

			<input id="wc-<?php echo esc_attr( $this->get_gateway()->get_id_dasherized() ); ?>-payment-response"
				   type="hidden"
			       name="wc_<?php echo esc_attr( $this->get_gateway()->get_id() ); ?>_payment_response" />
		</div>

		<?php
	}


	/**
	 * Gets the JSON encoded version of the payment request for Visa Checkout.
	 *
	 * @see Visa_Checkout::get_payment_request()
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_payment_request() {

		return json_encode( $this->get_gateway()->get_payment_request() );
	}


	/**
	 * Renders a Visa Checkout button.
	 *
	 * @since 2.3.0
	 */
	protected function render_button() {

		?>

		<img alt="<?php esc_attr_e( 'Visa Checkout button', 'woocommerce-plugin-framework' ); ?>"
		     class="v-button"
		     role="button"
		     src="<?php echo esc_url( $this->get_button_url() ); ?>" />

		<?php
	}


	/**
	 * Gets the full URL of the Visa Checkout button image for the configured environment.
	 *
	 * Adds the cardBrands parameter to control the initial appearance of the button.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_button_url() {

		$params = [
			'cardBrands' => $this->get_accepted_card_brands(),
		];

		return add_query_arg( rawurlencode_deep( $params ), $this->get_button_base_url() );
	}


	/**
	 * Gets the base URL for the Visa Checkout button image.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_button_base_url() {

		if ( $this->gateway->is_test_environment() ) {
			return self::BUTTON_URL_SANDBOX;
		}

		return self::BUTTON_URL_PRODUCTION;
	}


	/**
	 * Gets a comma separated list of accepted card brands.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_accepted_card_brands() {

		return implode( ',', $this->get_gateway()->get_visa_checkout_accepted_card_brands() );
	}


}
