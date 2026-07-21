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

use SkyVerge\WooCommerce\Cybersource\API\Helper;
use SkyVerge\WooCommerce\Cybersource\Gateway;
use SkyVerge\WooCommerce\Cybersource\Gateway\ThreeD_Secure\Frontend;
use SkyVerge\WooCommerce\Cybersource\Plugin;
use SkyVerge\WooCommerce\Cybersource\Blocks\Credit_Card_Checkout_Block_Integration;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway_Apple_Pay_Payment_Response;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource Credit Card Gateway Class
 *
 * @since 3.0.0
 */
class Credit_Card extends Gateway {


	/** @var string whether hosted tokenization (Flex Microform) is enabled, 'yes' or 'no' */
	protected $hosted_tokenization;


	/** @var string whether 3D Secure is enabled, 'yes' or 'no' */
	protected $enable_threed_secure = 'no';


	/** @var string[] 3D Secure card types (property not typed due to WC setting empty values to strings) */
	protected $threed_secure_card_types = [];


	/** @var ThreeD_Secure|null 3D Secure handler */
	private ?ThreeD_Secure $threed_secure = null;

	/** @var Credit_Card_Checkout_Block_Integration|null */
	protected ?Credit_Card_Checkout_Block_Integration $credit_card_checkout_block_integration = null;


	/**
	 * Constructs the gateway.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$card_type_options    = [];
		$supported_card_types = [
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MAESTRO,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB,
		];

		foreach ( $supported_card_types as $type ) {
			$card_type_options[ $type ] = Framework\SV_WC_Payment_Gateway_Helper::payment_type_to_name( $type );
		}

		/**
		 * Filters the CyberSource Credit Card gateway method title.
		 *
		 * @since 2.3.0
		 *
		 * @param string $method_title method title
		 */
		$method_title = (string) apply_filters( 'wc_cybersource_credit_card_method_title', __( 'CyberSource Credit Card', 'woocommerce-gateway-cybersource' ) );

		/**
		 * Filters the CyberSource Credit Card gateway method description.
		 *
		 * @since 2.3.0
		 *
		 * @param string $method_description method description
		 */
		$method_description = (string) apply_filters( 'wc_cybersource_credit_card_method_description', __( 'Allow customers to securely pay using their credit cards with CyberSource.', 'woocommerce-gateway-cybersource' ) );

		parent::__construct(
			Plugin::CREDIT_CARD_GATEWAY_ID,
			wc_cybersource(),
			[
				'method_title'       => $method_title,
				'method_description' => $method_description,
				'supports'           => [
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_FLEX_FORM,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_REFUNDS,
					self::FEATURE_VOIDS,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_CUSTOMER_ID,
					self::FEATURE_ADD_PAYMENT_METHOD,
					self::FEATURE_TOKEN_EDITOR,
					self::FEATURE_APPLE_PAY,
					self::FEATURE_GOOGLE_PAY,
				],
				'environments' => [
					self::ENVIRONMENT_PRODUCTION => esc_html_x( 'Production', 'software environment', 'woocommerce-gateway-cybersource' ),
					self::ENVIRONMENT_TEST       => esc_html_x( 'Test', 'software environment', 'woocommerce-gateway-cybersource' ),
				],
				'payment_type' => self::PAYMENT_TYPE_CREDIT_CARD,
				'card_types'   => $card_type_options,
			]
		);

		$this->init_threed_secure_handler();

		// this is done in the parent constructor, but it's too early and we need the settings to be available
		$this->order_button_text = $this->get_order_button_text();
	}


	/**
	 * Gets the checkout block integration instance.
	 *
	 * @since 2.8.0
	 *
	 * @return Credit_Card_Checkout_Block_Integration
	 */
	public function get_checkout_block_integration_instance(): ?Framework\Payment_Gateway\Blocks\Gateway_Checkout_Block_Integration {

		if ( null === $this->credit_card_checkout_block_integration ) {

			require_once( $this->get_plugin()->get_plugin_path() . '/includes/Blocks/Credit_Card_Checkout_Block_Integration.php' );

			$this->credit_card_checkout_block_integration = new Credit_Card_Checkout_Block_Integration( $this->get_plugin(), $this );
		}

		return $this->credit_card_checkout_block_integration;
	}


	/**
	 * Gets the gateway form fields.
	 *
	 * Adds 3D Secure settings.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_method_form_fields() {

		$form_fields = parent::get_method_form_fields();

		// TODO: finalize labels {CW 2020-10-09}
		$form_fields['enable_threed_secure'] = [
			'title'       => __( '3D Secure', 'woocommerce-gateway-cybersource' ),
			'label'       => __( 'Enable 3D Secure', 'woocommerce-gateway-cybersource' ),
			'description' => __( 'Your merchant account must have this optional service enabled.', 'woocommerce-gateway-cybersource' ),
			'type'        => 'checkbox',
			'default'     => 'no',
		];

		// TODO: finalize labels {CW 2020-10-09}
		$form_fields['threed_secure_card_types'] = [
			'title'   => __( 'Card types', 'woocommerce-gateway-cybersource' ),
			'type'    => 'multiselect',
			'options' => ThreeD_Secure::get_supported_card_types(),
			'default' => array_keys( ThreeD_Secure::get_supported_card_types() ),
			'class'   => 'wc-enhanced-select threed-secure-field',
		];

		return $form_fields;
	}


	/**
	 * Displays the admin options.
	 *
	 * Overridden to add a little extra JS for toggling dependant settings.
	 *
	 * @since 2.3.0
	 */
	public function admin_options() {

		parent::admin_options();

		ob_start();

		?>

		$( '#woocommerce_<?php echo esc_js( $this->get_id() ); ?>_enable_threed_secure' ).change( function() {

			var enabled             = $( this ).is( ':checked' );
			var conditionalSettings = $( '.threed-secure-field' ).closest( 'tr' );

			if ( enabled ) {
				conditionalSettings.show();
			} else {
				conditionalSettings.hide();
			}

		} ).change();

		<?php

		wc_enqueue_js( ob_get_clean() );
	}


	/**
	 * Gets the order with Apple Pay data attached.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @param SV_WC_Payment_Gateway_Apple_Pay_Payment_Response $response
	 * @return \WC_Order
	 */
	public function get_order_for_apple_pay( \WC_Order $order, SV_WC_Payment_Gateway_Apple_Pay_Payment_Response $response ) {

		$order = parent::get_order_for_apple_pay( $order, $response );

		$order->payment->apple_pay = base64_encode( json_encode( $response->get_payment_data() ) );

		return $order;
	}


	/**
	 * Initializes the payment form instance.
	 *
	 * @since 2.1.0
	 *
	 * @return Payment_Form|Base_Payment_Form
	 */
	protected function init_payment_form_instance() {

		return $this->is_flex_form_enabled() ? new Payment_Form( $this ) : parent::init_payment_form_instance();
	}


	/**
	 * Gets the payment method defaults.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_payment_method_defaults(): array {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {
			$defaults['account-number'] = '4111111111111111';
		}

		return $defaults;
	}


	/**
	 * Validates the credit card number.
	 *
	 * Bypass credit card number validation if Flex Microform is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param string $account_number account number to validate
	 * @return bool
	 */
	protected function validate_credit_card_account_number( $account_number ) {

		if ( $this->is_flex_form_enabled() ) {
			$is_valid = (bool) Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-flex-token' );
		} else {
			$is_valid = parent::validate_credit_card_account_number( $account_number );
		}

		return $is_valid;
	}


	/**
	 * Gets an order with payment data added.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order $order_id WooCommerce order ID or object*
	 * @return \WC_Order $order order object
	 */
	public function get_order( $order_id ) {

		$order = parent::get_order( $order_id );

		if (
			$this->is_3d_secure_enabled() &&
			! $this->is_automatic_renewal($order->get_id()) &&
			! empty($threed_secure_reference_id = $this->threed_secure->get_reference_id())
		) {
			$order->threed_secure = (object) [
				'reference_id'                        => $threed_secure_reference_id,
				'enrollment_status'                   => $this->threed_secure->get_enrollment_status(),
				'consumer_authentication_information' => $this->threed_secure->get_consumer_authentication_information(),
			];
		}

		// if testing and a specific amount was set
		if ( $this->is_test_environment() && $test_amount = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-test-amount' ) ) {
			$order->payment_total = Framework\Helpers\NumberHelper::format($test_amount);
		}

		return $order;
	}


	/**
	 * Adds credit card data to the order's payment property.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param array $payload tokenization payload from CyberSource
	 * @return \WC_Order
	 */
	public function get_flex_form_order( \WC_Order $order, $payload = [] ) {

		$order = parent::get_flex_form_order( $order, $payload );

		if ( ! empty( $data = $payload['content']['paymentInformation']['card'] ) ) {
			$order = $this->get_order_with_flex_card_number( $order, $data );
			$order = $this->get_order_with_flex_card_type( $order, $data );
			$order = $this->get_order_with_flex_card_expiration( $order, $data );
		}

		return $order;
	}


	/**
	 * Gets the order object with card number data attached.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param array $data card tokenization data from CyberSource
	 * @return \WC_Order
	 */
	private function get_order_with_flex_card_number( \WC_Order $order, array $data ) {

		if ( ! empty( $data['number'] ) ) {
			$order->payment->account_number = $data['number']['maskedValue'];
			$order->payment->last_four      = substr( $order->payment->account_number, -4 );
			$order->payment->first_six      = $data['number']['bin'];
		}

		return $order;
	}


	/**
	 * Gets the order object with card type data attached.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param array $data card tokenization data from CyberSource
	 * @return \WC_Order
	 */
	private function get_order_with_flex_card_type( \WC_Order $order, array $data ) {

		if ( ! empty( $type = $data['number']['detectedCardTypes'][0] ) ) {
			$order->payment->card_type = Helper::convert_code_to_card_type( $type );
		}

		return $order;
	}


	/**
	 * Gets the order object with card expiration data attached.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param array $data card tokenization data from CyberSource
	 * @return \WC_Order
	 */
	private function get_order_with_flex_card_expiration( \WC_Order $order, array $data ) {

		if ( ! empty( $month = $data['expirationMonth']['value'] ) ) {
			$order->payment->exp_month = $month;
		}

		if ( ! empty( $year = $data['expirationYear']['value'] ) ) {
			$order->payment->exp_year = substr( $year, -2 );
		}

		return $order;
	}


	/** 3D Secure feature methods *************************************************************************************/


	/**
	 * Builds the 3D Secure handler instance.
	 *
	 * @since 2.3.0
	 */
	protected function init_threed_secure_handler(): void {

		$this->threed_secure = new ThreeD_Secure();

		$this->threed_secure
			->set_gateway( $this )
			->set_enabled( $this->is_3d_secure_enabled() )
			->set_enabled_card_types( $this->get_3d_secure_card_types() )
			->set_test_mode( $this->is_test_environment() )
			->init();
	}


	/**
	 * Renders the payment fields.
	 *
	 * Overridden to output "Continue to Payment" on the checkout page if 3D Secure is enabled.
	 *
	 * @since 2.3.0
	 */
	public function payment_fields() {

		// TODO: skipping 3DS handling on the "add payment method page" but we need to return to implement this later {JS - 2023-07-27}
		if ( ! is_add_payment_method_page() && ! is_checkout_pay_page() && $this->is_3d_secure_enabled() ) {

			$description = $this->get_description();

			if ( $description ) {
				echo wpautop( wptexturize( $description ) );
			}

			echo '<style type="text/css">#payment ul.payment_methods li label[for="payment_method_' . esc_attr( $this->get_id() ) . '"] img:nth-child(n+2) { margin-left:1px; }</style>';

		} else {

			parent::payment_fields();
		}
	}


	/**
	 * Outputs the payment form on the standalone payment page.
	 *
	 * @since 2.3.0
	 *
	 * @param int $order_id order ID
	 */
	public function payment_page( $order_id ) {

		$order_button_text = $this->is_hosted_gateway() && ! is_checkout_pay_page()
			? __( 'Continue to Payment', 'woocommerce-gateway-cybersource' )
			: __( 'Place order', 'woocommerce-gateway-cybersource' );

		?>
		<form id="order_review" method="post">
			<div id="payment">
				<div class="payment_box payment_method_<?php echo esc_attr( $this->get_id() ); ?>">
					<?php $this->get_payment_form_instance()->render(); ?>
				</div>
			</div>
			<button type="submit" id="place_order" class="button alt"><?php echo esc_html( $order_button_text ); ?></button>
			<input type="radio" name="payment_method" value="<?php echo esc_attr( $this->get_id() ); ?>" />
			<input type="hidden" name="woocommerce_pay" value="1" />
			<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
		</form>
		<?php
	}


	/**
	 * Validates the payment fields.
	 *
	 * Overridden to skip checkout field validation when 3D Secure is enabled.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function validate_fields(): bool {

		// skip validation on shortcode-based checkout page if 3D Secure is being used
		if (  ! is_checkout_pay_page() && ! $this->is_processing_block_checkout() && $this->is_3d_secure_enabled() ) {
			return true;
		}

		return parent::validate_fields();
	}


	/**
	 * Validates the posted CSC value.
	 *
	 * Bypassed with Flex form is enabled.
	 *
	 * @since 2.3.0
	 *
	 * @param string $csc CSC value
	 * @return bool
	 */
	protected function validate_csc( $csc ) {

		if ( $this->is_flex_form_enabled() ) {
			return true;
		}

		return parent::validate_csc( $csc );
	}


	/**
	 * Processes a payment.
	 *
	 * If 3D Secure is enabled, either redirect to the Pay Page or handle 3DS processing.
	 *
	 * @since 2.3.0
	 *
	 * @param int $order_id WooCommerce order ID
	 * @return array
	 */
	public function process_payment( $order_id ): array {

		if ( $this->should_redirect_to_pay_page( (int) $order_id ) ) {

			$order = wc_get_order( $order_id );

			// trigger WooCommerce to redirect to the Pay Page
			return [
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			];
		}

		// process normally
		$result = parent::process_payment( $order_id );

		if ( $result['result'] === 'success' && $this->is_3d_secure_enabled() ) {
			$this->threed_secure->clear_session_data();
		}

		return $result;
	}


	/**
	 * Determines whether the payment should be processed on the Pay Page.
	 *
	 * Payment should be processed on the Pay Page if:
	 * - 3D Secure is enabled
	 * - and the order is not a renewal
	 * - and the payment is not being made via the block checkout
	 * - and we're not already in the middle of the 3DS flow
	 *
	 * @since 2.8.0
	 *
	 * @param int $order_id
	 * @return bool
	 */
	protected function should_redirect_to_pay_page( int $order_id ): bool {

		return   $this->is_3d_secure_enabled()
			&& ! $this->is_automatic_renewal( $order_id ) // 3D Secure can't be used for automatic subscription renewals
			&& ! $this->is_processing_block_checkout()
			&& ! is_checkout_pay_page()
			&& ! $this->is_external_checkout( $order_id )
			&& ! $this->is_tokenized_checkout( $order_id );
	}


	/**
	 * Checks whether the current payment is an automatic renewal.
	 *
	 * @since 2.8.0
	 *
	 * @param int $order_id
	 * @return bool
	 */
	public function is_automatic_renewal( int $order_id ): bool {

		return function_exists( 'wcs_order_contains_renewal' )
			&& wcs_order_contains_renewal( $order_id )
			&& did_action( 'woocommerce_scheduled_subscription_payment' );
	}


	/**
	 * Determines whether the CSC field is enabled for saved payment methods.
	 *
	 * This should be false if the flex form is enabled, as it's a hosted field  in Flex v0.11.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function csc_enabled_for_tokens() {

		return ! $this->is_flex_form_enabled() && parent::csc_enabled_for_tokens();
	}


	/**
	 * Determines whether this is a "hosted" gateway.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_hosted_gateway(): bool {

		// in the blocks checkout flow we can perform 3DS validation directly in the checkout without sending the user to the pay page
		return ! Framework\Blocks\Blocks_Handler::is_checkout_block_in_use() && $this->is_3d_secure_enabled();
	}


	/**
	 * Determines whether an order is from an external checkout.
	 *
	 * @since 2.5.4
	 *
	 * @param int $order_id WooCommerce order ID
	 * @return bool
	 */
	public function is_external_checkout( $order_id ): bool {

		$order = wc_get_order( $order_id );

		return $order && ( self::FEATURE_GOOGLE_PAY === $order->get_created_via() || self::FEATURE_APPLE_PAY === $order->get_created_via() );
	}


	public function is_tokenized_checkout( $order_id ): bool {

		$order = wc_get_order( $order_id );

		return $order && $order->get_created_via() === 'store-api' && ! empty( SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id() . '-payment-token' ) );
	}


	/**
	 * Determines whether 3D secure is enabled.
	 *
	 * @since 2.7.1
	 *
	 * @return bool
	 */
	public function is_3d_secure_enabled(): bool {

		return 'yes' === $this->enable_threed_secure;
	}


	/**
	 * Gets a list of enabled card types for 3DS.
	 *
	 * @since 2.8.1
	 *
	 * @return string[]
	 */
	public function get_3d_secure_card_types(): array {

		return is_array( $this->threed_secure_card_types ) ? $this->threed_secure_card_types : [];
	}


	/**
	 * Determines whether 3D Secure is enabled for a specific card type.
	 *
	 * @since 2.8.2
	 *
	 * @param string $type
	 * @return bool
	 */
	public function is_3d_secure_enabled_for_card_type(string $type) : bool
	{
		return in_array($type, $this->get_3d_secure_card_types(), true);
	}


	/**
	 * Enqueues the gateway assets.
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	protected function enqueue_gateway_assets() {

		parent::enqueue_gateway_assets();

		$assets_version = $this->get_plugin()->get_version();

		wp_register_script( Frontend::THREED_SECURE_SCRIPT_HANDLE, wc_cybersource()->get_plugin_url() . '/assets/js/frontend/wc-cybersource-threed-secure.min.js', [ 'jquery' ], $assets_version );
	}


}
