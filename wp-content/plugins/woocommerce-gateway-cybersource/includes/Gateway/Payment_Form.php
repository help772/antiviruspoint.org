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

use SkyVerge\WooCommerce\Cybersource\CaptureContextRetriever;
use SkyVerge\WooCommerce\Cybersource\Flex_Helper;
use SkyVerge\WooCommerce\Cybersource\Gateway;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handles the Flex CyberSource payment form.
 *
 * This overrides the framework's implementation to support CyberSource Flex Microform,
 * which uses iframes for the card inputs.
 *
 * @since 2.0.0
 *
 * @method Gateway get_gateway()
 */
class Payment_Form extends Base_Payment_Form {


	/** @var string Flex key generation key ID */
	protected $flex_microform_key_id;


	/**
	 * Gets the form handler class name.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_js_handler_class_name() {

		return 'WC_Cybersource_Flex_Payment_Form_Handler';
	}


	/**
	 * Gets the credit card field definitions.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_credit_card_fields() {

		$fields = parent::get_credit_card_fields();

		if ( isset( $fields['card-csc'] ) ) {
			$fields['card-csc']['name'] = '';
		}

		return $fields;
	}


	/**
	 * Renders the payment form description.
	 *
	 * Adds content for easier testing in sandbox mode.
	 *
	 * @since 2.0.0
	 */
	public function render_payment_form_description() {

		parent::render_payment_form_description();

		// render a test card number in test mode when the flex form is used, since we cannot pre-fill it
		if ( $this->get_gateway()->is_test_environment() && $this->get_gateway()->is_flex_form_enabled() ) : ?>
			<p><?php printf( esc_html__( 'Test card number: %s', 'woocommerce-gateway-cybersource' ), '<code>4111 1111 1111 1111</code>' ); ?></p>
		<?php endif;
	}


	/**
	 * Renders the payment fields.
	 *
	 * Overridden to add the hidden fields for Flex Microform tokenization.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_Payment_Form::render_payment_fields()
	 *
	 * @since 2.0.0
	 */
	public function render_payment_fields() {

		if ( empty( $this->get_flex_microform_key() ) ) {

			// error initializing Flex Microform, display error message instead of the form
			printf(
				/* translators: Placeholders: %1$s - <div> tag, %2$s - </div> tag */
				__( '%1$sCurrently unavailable. Please try a different payment method.%2$s', 'woocommerce-gateway-cybersource' ),
				'<div class="woocommerce-error">',
				'</div>'
			);

			return;
		}

		$input_id = 'wc-' . $this->get_gateway()->get_id_dasherized();

		parent::render_payment_fields();

		$hidden_fields = [
			'flex-token',
			'flex-key',
			'masked-pan',
			'card-type',
			'instrument-identifier-id',
			'instrument-identifier-new',
			'instrument-identifier-state',
		];

		foreach ( $hidden_fields as $field ) {
			echo '<input type="hidden" name="' . $input_id . '-' . sanitize_html_class( $field ) . '" />';
		}
	}


	/**
	 * Renders the payment fields.
	 *
	 * Overridden to replace the card number input with the Flex Microform container div.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field payment field params
	 */
	public function render_payment_field( $field ) {

		$hosted_fields = [
			'wc-cybersource-credit-card-account-number',
			'wc-cybersource-credit-card-csc',
		];

		if ( isset( $field['id'] ) && in_array( $field['id'], $hosted_fields, true ) ) {

			?>
			<div class="form-row <?php echo implode( ' ', array_map( 'sanitize_html_class', $field['class'] ) ); ?>">
				<label id="<?php echo esc_attr( $field['id'] ) . '-label'; ?>" for="<?php echo esc_attr( $field['id'] ) . '-hosted'; ?>"><?php echo esc_html( $field['label'] ); if ( $field['required'] ) : ?><abbr class="required" title="required">&nbsp;*</abbr><?php endif; ?></label>
				<div id="<?php echo esc_attr( $field['id'] ) . '-hosted'; ?>" class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $field['input_class'] ) ); ?>" data-placeholder="<?php echo isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : ''; ?>"></div>
			</div>
			<?php

		} else {

			parent::render_payment_field( $field );
		}
	}


	/**
	 * Attempts to get the transaction specific public key used to initiate the Flex Microform.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_flex_microform_key() {

		if ( empty( $this->flex_microform_key_id ) ) {

			try {

				$this->flex_microform_key_id = CaptureContextRetriever::getCaptureContext();

			} catch ( Framework\SV_WC_API_Exception $exception ) {

				$this->log_event( 'Error generating transaction specific public key used to initiate the Flex Microform: ' . $exception->getMessage() );
			}
		}

		return $this->flex_microform_key_id;
	}


	/**
	 * Gets the JS args for the payment form handler.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	protected function get_js_handler_args() {

		$args = [];

		if ( ! empty( $key_id = $this->get_flex_microform_key() ) ) {

			$args = [
				'plugin_id'               => $this->get_gateway()->get_plugin()->get_id(),
				'id'                      => $this->get_gateway()->get_id(),
				'id_dasherized'           => $this->get_gateway()->get_id_dasherized(),
				'type'                    => $this->get_gateway()->get_payment_type(),
				'csc_required'            => $this->get_gateway()->csc_enabled(),
				'csc_required_for_tokens' => $this->get_gateway()->csc_enabled_for_tokens(),
				'general_error'           => __( 'An error occurred, please try again or try an alternate form of payment.', 'woocommerce-gateway-authorize-net-cim' ),
				'capture_context'         => $key_id,
				'number_placeholder'      => '•••• •••• •••• ••••',
				'csc_placeholder'         => '•••',
				'styles'                  => [
					'input' => [
						'font-size'   => '1.5em',
						'font-weight' => '400',
						'color'       => '#43454b',
					]
				]
			];

			if ( $this->get_gateway()->supports_card_types() ) {

				$args['enabled_card_types'] = array_map( [
					Framework\SV_WC_Payment_Gateway_Helper::class,
					'normalize_card_type'
				], $this->get_gateway()->get_card_types() );
			}

			/**
			 * Payment Gateway Payment Form JS Arguments Filter.
			 *
			 * Filter the arguments passed to the Payment Form handler JS class
			 *
			 * @since 2.0.0
			 *
			 * @param array $result {
			 *
			 *   @type string $plugin_id plugin ID
			 *   @type string $id gateway ID
			 *   @type string $id_dasherized gateway ID dasherized
			 *   @type string $type gateway payment type (e.g. 'credit-card')
			 *   @type bool $csc_required true if CSC field display is required
			 *   @type bool $csc_required_for_tokens true if CSC field display is required for saved payment methods
			 *   @type string $general_error general error message
			 *   @type string $capture_context key ID used by CyberSource Flex Microform setup (do not change this)
			 *   @type string $number_placeholder credit card number input placeholder
			 *   @type string $csc_placeholder CSC input placeholder
			 *   @type array $styles styles to be applied to CyberSource Flex Microform (@see https://developer.cybersource.com/api/developer-guides/dita-flex/SAFlexibleToken/FlexMicroform/Styling.html)
			 * }
			 *
			 * @param Payment_Form $this payment form instance
			 */
			$args = apply_filters( 'wc_' . $this->get_gateway()->get_id() . '_payment_form_js_args', $args, $this );
		}

		return $args;
	}


	/**
	 * Prevent rendering JS on checkout if 3D Secure is enabled.
	 *
	 * @inheritDoc
	 *
	 * @since 2.7.1
	 */
	public function maybe_render_js(): void {

		$gateway = $this->get_gateway();

		if ( ! is_add_payment_method_page() && ! is_checkout_pay_page() && is_callable( [ $gateway, 'is_3d_secure_enabled'] ) && $this->get_gateway()->is_3d_secure_enabled() ) {
			return;
		}

		parent::maybe_render_js();
	}


	/**
	 * Renders the payment form JS.
	 *
	 * Overridden to enqueue the hosted CyberSource form handler if using Flex Microform.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_Payment_Form::render_js()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function render_js() {

		parent::render_js();

		if (in_array($this->get_gateway()->get_id(), $this->payment_form_js_rendered, true) && ! empty($this->get_flex_microform_key())) {
			Flex_Helper::addFlexMicroformScriptHooks();

			wp_enqueue_script('wc-cybersource-flex-microform');
		}
	}


}
