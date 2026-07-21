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

use SkyVerge\WooCommerce\Cybersource\API\Visa_Checkout\Responses\Payment_Authorization;
use SkyVerge\WooCommerce\Cybersource\Gateway;
use SkyVerge\WooCommerce\Cybersource\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource Visa Checkout Gateway Class
 *
 * @since 2.3.0
 */
#[\AllowDynamicProperties]
class Visa_Checkout extends Gateway {


	/** Visa Checkout payment type */
	const PAYMENT_TYPE_VISA_CHECKOUT = 'visa_checkout';

	/** @var string URL for the Sandbox version of the Visa Checkout JS SDK */
	const JS_SDK_URL_SANDBOX = 'https://sandbox-assets.secure.checkout.visa.com/checkout-widget/resources/js/integration/v1/sdk.js';

	/** @var string URL for the Production version of the Visa Checkout JS SDK */
	const JS_SDK_URL_PRODUCTION = 'https://assets.secure.checkout.visa.com/checkout-widget/resources/js/integration/v1/sdk.js';


	/**
	 * Constructs the gateway.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {

		/**
		 * Filters the CyberSource Visa Checkout gateway method title.
		 *
		 * @since 2.4.1
		 *
		 * @param string $method_title method title
		 */
		$method_title = (string) apply_filters( 'wc_cybersource_visa_checkout_method_title', __( 'CyberSource Visa Checkout', 'woocommerce-gateway-cybersource' ) );

		/**
		 * Filters the CyberSource Visa Checkout gateway method description.
		 *
		 * @since 2.4.1
		 *
		 * @param string $method_description method description
		 */
		$method_description = (string) apply_filters( 'wc_cybersource_visa_checkout_method_description', __( 'Allow customers to securely pay using their Visa Checkout digital wallet and CyberSource.', 'woocommerce-gateway-cybersource' ) );

		parent::__construct(
			Plugin::VISA_CHECKOUT_GATEWAY_ID,
			wc_cybersource(),
			[
				'method_title'       => $method_title,
				'method_description' => $method_description,
				'supports'           => [
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_REFUNDS,
					self::FEATURE_VOIDS,
				],
				'environments' => [
					self::ENVIRONMENT_PRODUCTION => esc_html_x( 'Production', 'software environment', 'woocommerce-gateway-cybersource' ),
					self::ENVIRONMENT_TEST       => esc_html_x( 'Test', 'software environment', 'woocommerce-gateway-cybersource' ),
				],
				'payment_type' => self::PAYMENT_TYPE_VISA_CHECKOUT,
				'card_types'   => $this->get_cart_type_options( [
					Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA,
					Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD,
					Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX,
					Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER,
				] ),
				'currencies'   => [
					'ARS',
					'AUD',
					'BRL',
					'CAD',
					'CNY',
					'CLP',
					'COP',
					'EUR',
					'HKD',
					'INR',
					'KWD',
					'MYR',
					'MXN',
					'NZD',
					'PEN',
					'PLN',
					'QAR',
					'SAR',
					'SGD',
					'ZAR',
					'AED',
					'UAH',
					'GBP',
					'USD',
				],
				'shared_settings'    => $this->shared_settings_names,
			]
		);
	}


	/**
	 * Gets the IDs of sibling gateways that this gateway can inherit settings from.
	 *
	 * The Visa Checkout gateway can inherit settings from the Credit Card gateway only.
	 *
	 * This returns an empty array if no Credit Card gateway is loaded.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_ids_of_gateways_to_inherit_settings_from() {

		return array_intersect( $this->get_plugin()->get_gateway_ids(), [ Plugin::CREDIT_CARD_GATEWAY_ID ] );
	}


	/**
	 * Gets a list of supported card types and names.
	 *
	 * This assign a card type name to each one of the given card types.
	 *
	 * @since 2.3.0
	 *
	 * @param array $supported_card_types a list of supported card types
	 * @return array
	 */
	protected function get_cart_type_options( $supported_card_types ) {

		$card_type_options = [];

		foreach ( $supported_card_types as $type ) {
			$card_type_options[ $type ] = Framework\SV_WC_Payment_Gateway_Helper::payment_type_to_name( $type );
		}

		return $card_type_options;
	}


	/**
	 * Gets the default payment method title.
	 *
	 * The title is configurable within the admin and displayed on checkout.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_default_title() {

		return esc_html__( 'Visa Checkout', 'woocommerce-gateway-cybersource' );
	}


	/**
	 * Gets the default payment method description.
	 *
	 * The description is configurable within the admin and displayed on checkout.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_default_description() {

		return esc_html__( 'Click the Visa Checkout button below to sign into your Visa Checkout account and pay securely.', 'woocommerce-gateway-cybersource' );
	}


	/**
	 * Gets the gateway icon markup.
	 *
	 * Overridden to show Visa Checkout acceptance logo.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_icon() {

		return '<img src="https://assets.secure.checkout.visa.com/VCO/images/acc_99x34_wht01.png" />';
	}


	/**
	 * Determines whether this gateway can do authorization transactions.
	 *
	 * Overridden to remove the verification that checks whether this is a credit card gateway.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function supports_credit_card_authorization() {

		return $this->supports( self::FEATURE_CREDIT_CARD_AUTHORIZATION );
	}


	/**
	 * Determines whether this gateway can do charge transactions.
	 *
	 * Overridden to remove the verification that checks whether this is a credit card gateway.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function supports_credit_card_charge() {

		return $this->supports( self::FEATURE_CREDIT_CARD_CHARGE );
	}


	/**
	 * Determines whether this gateway can charge virtual-only orders.
	 *
	 * Overridden to remove the verification that checks whether this is a credit card gateway.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function supports_credit_card_charge_virtual() {

		return $this->supports( self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL );
	}


	/**
	 * Determines whether this gateway supports card types.
	 *
	 * Overridden to remove the verification that checks whether this is a credit card gateway.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function supports_card_types() {

		return $this->supports( self::FEATURE_CARD_TYPES );
	}


	/**
	 * Enqueues the Visa Checkout JS SDK.
	 *
	 * @since 2.3.0
	 */
	protected function enqueue_gateway_assets() {

		parent::enqueue_gateway_assets();

		wp_enqueue_script(
			"wc-{$this->get_id_dasherized()}-js-sdk",
			$this->get_js_sdk_url(),
			[ $this->get_gateway_js_handle() ],
			$this->get_plugin()->get_version(),
			true
		);
	}


	/**
	 * Gets the gateway-specifics JS script handle.
	 *
	 * This is used for:
	 *
	 * + enqueuing the script
	 * + the localized JS script param object name
	 *
	 * The default is 'wc-<plugin ID dasherized>' so we overwrite it here to use
	 * a specific handle for Visa Checkout.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_gateway_js_handle() {

		return 'wc-' . $this->get_plugin()->get_id_dasherized() . '-visa-checkout';
	}


	/**
	 * Gets the URL for the Visa Checkout JS SDK for the configured environment.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	protected function get_js_sdk_url() : string {

		if ( $this->is_test_environment() ) {
			return self::JS_SDK_URL_SANDBOX;
		}

		return self::JS_SDK_URL_PRODUCTION;
	}


	/**
	 * Initializes the payment form instance.
	 *
	 * @since 2.3.0
	 *
	 * @return Visa_Checkout\Payment_Form
	 */
	protected function init_payment_form_instance() {

		return new Visa_Checkout\Payment_Form( $this );
	}


	/**
	 * Gets an array of form fields for Visa Checkout.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_method_form_fields() {

		$fields = parent::get_method_form_fields();

		$fields = Framework\SV_WC_Helper::array_insert_after( $fields, 'api_shared_secret', [
			'visa_checkout_api_key' => [
				'title'    => __( 'Visa Checkout API Key', 'woocommerce-gateway-cybersource' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'The Visa Checkout API key for your CyberSource account', 'woocommerce-gateway-cybersource' ),
			],
		] );

		$fields = Framework\SV_WC_Helper::array_insert_after( $fields, 'test_api_shared_secret', [
			'test_visa_checkout_api_key' => [
				'title'    => __( 'Test Visa Checkout API Key', 'woocommerce-gateway-cybersource' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'The Visa Checkout API key for your CyberSource sandbox account', 'woocommerce-gateway-cybersource' ),
			],
		] );

		return $fields;
	}


	/**
	 * Adds the card types form fields.
	 *
	 * Allows merchants to configure the accepted card brands for Visa Checkout.
	 *
	 * @since 2.3.0
	 *
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields
	 */
	protected function add_card_types_form_fields( $form_fields ) {

		$form_fields = parent::add_card_types_form_fields( $form_fields );

		$form_fields['card_types']['title'] = esc_html__( 'Accepted Card Brands', 'woocommerce-gaetway-cybersource' );

		$form_fields['card_types']['description'] = sprintf(
			/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
			esc_html__( 'This setting %1$schanges%2$s which card types Visa Checkout will accept.', 'woocommerce-gaetway-cybersource' ),
			'<strong>',
			'</strong>'
		);

		if ( isset( $form_fields['card_types']['desc_tip'] ) ) {
			unset( $form_fields['card_types']['desc_tip'] );
		}

		return $form_fields;
	}


	/**
	 * Validates that a Visa Checkout payment response was submitted.
	 *
	 * @since 2.3.0
	 *
	 * @param bool $is_valid whether fields passed validation so far
	 * @return bool
	 */
	protected function validate_visa_checkout_fields( $is_valid ) {

		if ( ! Framework\SV_WC_Helper::get_posted_value( "wc_{$this->get_id()}_payment_response" ) ) {

			Framework\SV_WC_Helper::wc_add_notice( esc_html__( 'Visa Checkout payment response is missing', 'woocommerce-gateway-cybersource' ), 'error' );

			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * Creates a transaction for a Visa Checkout order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @return Framework\SV_WC_Payment_Gateway_API_Response
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	protected function do_visa_checkout_transaction( \WC_Order $order ) {

		return $this->do_credit_card_transaction( $order );
	}


	/**
	 * Gets an order with payment data added.
	 *
	 * @since 2.3.0
	 *
	 * @param int $order_id order ID
	 * @return \WC_Order $order order object
	 */
	public function get_order( $order_id ) {

		$order = parent::get_order( $order_id );

		if ( $response = $this->get_visa_checkout_payment_response() ) {
			return $this->prepare_visa_checkout_order_for_payment( $order, $response );
		}

		return $order;
	}


	/**
	 * Gets the submitted Visa Checkout payment response.
	 *
	 * @since 2.3.0
	 *
	 * @return Payment_Authorization
	 */
	protected function get_visa_checkout_payment_response() {

		return $this->build_visa_checkout_payment_response(
			stripslashes( Framework\SV_WC_Helper::get_posted_value( "wc_{$this->get_id()}_payment_response" ) )
		);
	}


	/**
	 * Adds Visa Checkout payment data to an order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @param Payment_Authorization $response Visa Checkout payment response
	 * @return \WC_Order;
	 */
	protected function prepare_visa_checkout_order_for_payment( \WC_Order $order, Payment_Authorization $response ) {

		$order->payment->account_number = $response->get_last_four();
		$order->payment->card_type      = $response->get_card_type();

		$order->payment->visa_checkout = (object) [
			'callid'           => $response->get_transaction_id(),
			'enc_key'          => $response->get_encrypted_key(),
			'enc_payment_data' => $response->get_encrypted_payment_data(),
		];

		return $order;
	}


	/**
	 * Gets an order object with payment data added for use in credit card capture transactions.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @param float $amount capture amount
	 * @return \WC_Order
	 */
	public function get_order_for_capture( $order, $amount = null ) {

		return $this->prepare_visa_checkout_order_for_capture(
			parent::get_order_for_capture( $order, $amount ),
			$amount
		);
	}


	/**
	 * Adds Visa Checkout transaction data to capture property of the given order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @param float $amount capture amount
	 * @return \WC_Order
	 */
	protected function prepare_visa_checkout_order_for_capture( \WC_Order $order, $amount ) {

		$order->capture->visa_checkout = (object) $this->get_visa_checkout_transaction_data( $order );

		return $order;
	}


	/**
	 * Gets Visa Checkout transaction data from the given order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @return array
	 */
	protected function get_visa_checkout_transaction_data( \WC_Order $order ) {

		return [
			'callid' => $this->get_order_meta( $order, 'callid' ),
		];
	}


	/**
	 * Gets an order object with payment data added for use in credit card refund transactions.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @param float $amount refund amount
	 * @param string $reason refund reason
	 * @return \WC_Order
	 */
	public function get_order_for_refund( $order, $amount, $reason ) {

		return $this->prepare_visa_checkout_order_for_refund(
			parent::get_order_for_refund( $order, $amount, $reason ),
			$amount,
			$reason
		);
	}


	/**
	 * Adds Visa Checkout transaction data to the refund property of the given order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @param float $amount refund amount
	 * @param string $reason refund reason
	 * @return \WC_Order
	 */
	protected function prepare_visa_checkout_order_for_refund( \WC_Order $order, $amount, $reason ) {

		$order->refund->visa_checkout = (object) $this->get_visa_checkout_transaction_data( $order );

		return $order;
	}


	/**
	 * Creates a new Visa Checkout payment response object with the given data.
	 *
	 * @since 2.3.0
	 *
	 * @return Payment_Authorization
	 */
	protected function build_visa_checkout_payment_response( $data ) {

		return new Payment_Authorization( $data );
	}


	/**
	 * Adds any gateway-specific transaction data to the order
	 *
	 * @see SV_WC_Payment_Gateway_Direct::add_transaction_data()
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order the order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Customer_Response $response the transaction response
	 */
	public function add_payment_gateway_transaction_data( $order, $response ) {

		parent::add_payment_gateway_transaction_data( $order, $response );

		if ( $response instanceof Framework\SV_WC_Payment_Gateway_API_Authorization_Response ) {

			$this->add_authorization_transaction_data( $order, $response );

			// parent method may set charge_captured meta for authorization transactions that are pending review
			if ( ! $this->get_order_meta( $order, 'charge_captured' ) ) {
				$this->add_charge_captured_transaction_data( $order, $response );
			}
		}

		$this->add_card_type_transaction_data( $order );
		$this->add_visa_checkout_transaction_data( $order );
	}


	/**
	 * Adds authorization transaction meta data to the order.
	 *
	 * @see \SV_WC_Payment_Gateway::add_transaction_data()
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order the order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Authorization_Response $response the transaction response
	 */
	protected function add_authorization_transaction_data( \WC_Order $order, Framework\SV_WC_Payment_Gateway_API_Authorization_Response $response ) {

		$this->update_order_meta( $order, 'authorization_amount', $order->payment_total );

		if ( $response->get_authorization_code() ) {
			$this->update_order_meta( $order, 'authorization_code', $response->get_authorization_code() );
		}
	}


	/**
	 * Marks the order as captured if the gateway performed a credit card charge.
	 *
	 * @see \SV_WC_Payment_Gateway::add_transaction_data()
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order the order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Authorization_Response $response the transaction response
	 */
	protected function add_charge_captured_transaction_data( \WC_Order $order, Framework\SV_WC_Payment_Gateway_API_Authorization_Response $response ) {

		if ( $order->payment_total > 0 ) {

			// mark as captured
			$this->update_order_meta(
				$order,
				'charge_captured',
				wc_bool_to_string( $this->perform_credit_card_charge( $order ) )
			);
		}
	}


	/**
	 * Adds authorization transaction meta data to the order.
	 *
	 * @see \SV_WC_Payment_Gateway::add_transaction_data()
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order the order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Authorization_Response $response the transaction response
	 */
	protected function add_card_type_transaction_data( \WC_Order $order ) {

		if ( isset( $order->payment->card_type ) && $order->payment->card_type ) {
			$this->update_order_meta( $order, 'card_type', $order->payment->card_type );
		}
	}


	/**
	 * Adds Visa Checkout transaction meta data to the order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order the order object
	 */
	protected function add_visa_checkout_transaction_data( \WC_Order $order ) {

		if ( ! empty( $order->payment->visa_checkout->callid ) ) {
			$this->update_order_meta( $order, 'callid', $order->payment->visa_checkout->callid );
		}
	}


	/**
	 * Gets the Visa Checkout API Key.
	 *
	 * @since 2.3.0
	 *
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string
	 */
	public function get_visa_checkout_api_key( $environment_id = null ) {

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return self::ENVIRONMENT_PRODUCTION === $environment_id ? $this->visa_checkout_api_key : $this->test_visa_checkout_api_key;
	}


	/**
	 * Gets the settings for Visa Checkout JS.
	 *
	 * @since 2.3.0
	 *
	 * @param array $args {
	 *     Optional. The settings args.
	 *
	 *     @type string $merchant_name  Merchant name. Defaults to the shop name.
	 *     @type string $merchant_url   Merchant URL. Defaults to the shop URL.
	 * }
	 * @return array
	 */
	public function get_visa_checkout_settings( $args = [] ) {

		$args = wp_parse_args( $args, [
			'merchant_name'         => get_bloginfo( 'name', 'display' ),
			'merchant_url'          => get_home_url(),
		] );

		$settings = [
			'displayName'  => $args['merchant_name'],
			'websiteUrl'   => $args['merchant_url'],
			'shipping'     => [
				'collectShipping' => false,
			],
			'review'       => [
				'buttonAction' => 'Pay',
			],
			'payment'      => [
				'cardBrands' => $this->get_visa_checkout_accepted_card_brands(),
			],
			'dataLevel'    => 'FULL',
		];

		return $settings;
	}


	/**
	 * Gets the list of card brands that Visa Checkout will accept.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public function get_visa_checkout_accepted_card_brands() {

		return array_map( 'strtoupper', $this->get_card_types() );
	}


	/**
	 * Gets the payment request for Visa Checkout for the current page.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public function get_payment_request() {

		if ( $order = wc_get_order( $this->get_checkout_pay_page_order_id() ) ) {
			return $this->get_payment_request_for_order( $order );
		}

		return $this->get_payment_request_for_cart( WC()->cart );
	}


	/**
	 * Gets a payment request for Visa Checkout based on order data.
	 *
	 * @since 2.3.0
	 *
	 * @param WC_Cart $cart
	 * @return array
	 */
	protected function get_payment_request_for_order( \WC_Order $order ) {

		$order->calculate_totals();

		$request = $this->build_payment_request( $order->get_total(), [
			'line_totals' => $this->get_order_totals( $order ),
		] );

		/**
		 * Filters the Visa Checkout payment request for an order.
		 *
		 * @since 2.3.0
		 *
		 * @param array $request payment request
		 * @param \WC_Order $order order object
		 */
		return apply_filters( "wc_{$this->get_id()}_order_payment_request", $request, $order );
	}


	/**
	 * Gets the line totals for an order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @return array
	 */
	protected function get_order_totals( \WC_Order $order ) {

		return [
			'subtotal' => $order->get_subtotal(),
			'discount' => $order->get_discount_total(),
			'shipping' => $order->get_shipping_total(),
			'fees'     => $order->get_total_fees(),
			'taxes'    => $order->get_total_tax(),
		];
	}


	/**
	 * Gets a payment request for Visa Checkout based on cart data.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Cart $cart cart object
	 * @return array
	 */
	public function get_payment_request_for_cart( \WC_Cart $cart ) {

		$cart->calculate_totals();

		$request = $this->build_payment_request( $cart->total, [
			'line_totals' => $this->get_cart_totals( $cart ),
		] );

		/**
		 * Filters the Visa Checkout cart JS payment request.
		 *
		 * @since 2.3.0
		 *
		 * @param array $request the cart JS payment request
		 * @param \WC_Cart $cart the cart object
		 */
		return apply_filters( "wc_{$this->get_id()}_cart_payment_request", $request, $cart );
	}


	/**
	 * Gets the line totals for a cart.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Cart $cart cart object
	 * @return array
	 */
	protected function get_cart_totals( \WC_Cart $cart ) {

		$cart->calculate_totals();

		return [
			'subtotal' => $cart->subtotal_ex_tax,
			'discount' => $cart->get_cart_discount_total(),
			'shipping' => $cart->shipping_total,
			'fees'     => $cart->fee_total,
			'taxes'    => $cart->tax_total + $cart->shipping_tax_total,
		];
	}


	/**
	 * Builds the payment request for Visa Checkout JS.
	 *
	 * @since 2.3.0
	 *
	 * @param array $args {
	 *     Optional. The payment request args.
	 *
	 *     @type string $currency_code  Payment currency code. Defaults to the shop currency.
	 *     @type array  $line_totals    request line totals (@see Visa_Checkout::build_payment_request_totals())
	 * }
	 * @return array
	 */
	protected function build_payment_request( $amount, $args = [] ) {

		$args = wp_parse_args( $args, [
			'currency_code' => get_woocommerce_currency(),
			'line_totals'   => [],
		] );

		$request = [
			'currencyCode' => $args['currency_code'],
		];

		if ( is_array( $args['line_totals'] ) ) {
			$request = array_merge( $request, $this->build_payment_request_totals( $args['line_totals'] ) );
		}

		$request['total'] = $this->format_amount( $amount );

		return $request;
	}


	/**
	 * Builds payment request totals for Visa Checkout JS.
	 *
	 * Visa Checkout chooses to show the order total only, but the integration
	 * accepts additional values to indicate the breakdown of that total:
	 *
	 * + Subtotal
	 * + Discounts (a positive amount to be deducted from the total)
	 * + Shipping
	 * + Fees
	 * + Taxes
	 *
	 * @since 2.3.0
	 *
	 * @param array $totals {
	 *     Payment totals.
	 *
	 *     @type float $subtotal items subtotal
	 *     @type float $discount discounts total
	 *     @type float $shipping shipping total
	 *     @type float $fees     fees total
	 *     @type float $taxes    tax total
	 * }
	 * @return array
	 */
	protected function build_payment_request_totals( $totals ) {

		$totals = wp_parse_args( $totals, array(
			'subtotal' => 0.00,
			'discount' => 0.00,
			'shipping' => 0.00,
			'fees'     => 0.00,
			'taxes'    => 0.00,
		) );

		return array_filter( [
			'subtotal'         => $this->format_amount( $totals['subtotal'] ),
			'shippingHandling' => $this->format_amount( $totals['shipping'] ),
			'tax'              => $this->format_amount( $totals['taxes'] ),
			'discount'         => $this->format_amount( $totals['discount'] ),
			'misc'             => $this->format_amount( $totals['fees'] ),
		], 'floatval' );
	}


	/**
	 * Formats a total amount for use with Visa Checkout.
	 *
	 * @since 2.3.0
	 *
	 * @param string|float $amount the amount to format
	 * @return string
	 */
	protected function format_amount( $amount ) {

		return number_format( $amount, 2 );
	}


}
