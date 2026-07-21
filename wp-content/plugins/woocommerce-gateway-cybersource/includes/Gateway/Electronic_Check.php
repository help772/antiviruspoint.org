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

use SkyVerge\WooCommerce\Cybersource\Blocks\Electronic_Check_Checkout_Block_Integration;
use SkyVerge\WooCommerce\Cybersource\Gateway;
use SkyVerge\WooCommerce\Cybersource\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource Electronic Check Gateway Class
 *
 * @since 2.0.0
 */
#[\AllowDynamicProperties]
class Electronic_Check extends Gateway {


	/** @var string the authorization message displayed at checkout */
	protected $authorization_message = '';

	/** @var string the authorization message displayed at checkout for subscriptions */
	protected $recurring_authorization_message = '';

	/** @var bool whether the authorization message should be displayed at checkout */
	protected $authorization_message_enabled;

	/** @var Electronic_Check_Checkout_Block_Integration|null */
	protected ?Electronic_Check_Checkout_Block_Integration $electronic_check_checkout_block_integration = null;


	/**
	 * Constructs the gateway.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		/**
		 * Filters the CyberSource eCheck gateway method title.
		 *
		 * @since 2.3.0
		 *
		 * @param string $method_title method title
		 */
		$method_title = (string) apply_filters( 'wc_cybersource_echeck_method_title', __( 'CyberSource eCheck', 'woocommerce-gateway-cybersource' ) );

		/**
		 * Filters the CyberSource eCheck gateway method description.
		 *
		 * @since 2.3.0
		 *
		 * @param string $method_description method description
		 */
		$method_description = (string) apply_filters( 'wc_cybersource_echeck_method_description', __( 'Allow customers to securely pay using their checking/savings accounts with CyberSource.', 'woocommerce-gateway-cybersource' ) );

		parent::__construct(
			Plugin::ECHECK_GATEWAY_ID,
			wc_cybersource(),
			[
				'method_title'       => $method_title,
				'method_description' => $method_description,
				'supports'           => [
					self::FEATURE_PRODUCTS,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					// self::FEATURE_TOKENIZATION,
					// self::FEATURE_ADD_PAYMENT_METHOD,
					//self::FEATURE_REFUNDS,
					// self::FEATURE_VOIDS,
				],
				'environments'       => [
					self::ENVIRONMENT_PRODUCTION => esc_html_x( 'Production', 'software environment', 'woocommerce-gateway-cybersource' ),
					self::ENVIRONMENT_TEST       => esc_html_x( 'Test', 'software environment', 'woocommerce-gateway-cybersource' ),
				],
				'payment_type'       => self::PAYMENT_TYPE_ECHECK,
				'shared_settings'    => $this->shared_settings_names,
			]
		);

		// add check number field to the payment form
		add_filter( 'wc_' . $this->get_id() . '_payment_form_default_echeck_fields', [ $this, 'add_payment_form_fields' ], 5 );

		// display the authorization message at checkout
		if ( $this->is_authorization_message_enabled() ) {
			add_action( 'wc_' . $this->get_id() . '_payment_form_end', [ $this, 'display_authorization_message' ] );
		}

		// adjust the recurring authorization message placeholders for subscriptions
		add_filter( 'wc_' . $this->get_id() . '_authorization_message_placeholders', [ $this, 'adjust_subscriptions_placeholders' ], 10, 2 );
	}


	/**
	 * Gets the checkout block integration instance.
	 *
	 * @since 2.8.0
	 *
	 * @return Electronic_Check_Checkout_Block_Integration
	 */
	public function get_checkout_block_integration_instance(): ?Framework\Payment_Gateway\Blocks\Gateway_Checkout_Block_Integration {

		if ( null === $this->electronic_check_checkout_block_integration ) {

			require_once( $this->get_plugin()->get_plugin_path() . '/includes/Blocks/Electronic_Check_Checkout_Block_Integration.php' );

			$this->electronic_check_checkout_block_integration = new Electronic_Check_Checkout_Block_Integration( $this->get_plugin(), $this );
		}

		return $this->electronic_check_checkout_block_integration;
	}


	/**
	 * Gets the IDs of sibling gateways that this gateway can inherit settings from.
	 *
	 * The eCheck gateway can inherit settings from the Credit Card gateway only.
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


	/** Payment Form methods **************************************************/


	/**
	 * Adds check number field to the payment form.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields
	 * @return array
	 */
	public function add_payment_form_fields( $fields ) {

		if ( ! $this->is_check_number_enabled() ) {
			return $fields;
		}

		// add check number
		$fields['check-number'] = [
			'type'              => 'text',
			'label'             => __( 'Check Number', 'woocommerce-gateway-cybersource' ),
			'id'                => 'wc-' . $this->get_id_dasherized() . '-check-number',
			'name'              => 'wc-' . $this->get_id_dasherized() . '-check-number',
			'required'          => $this->is_check_number_required(),
			'input_class'       => [ 'wc-' . $this->get_id_dasherized() . '-check-number' ],
			'maxlength'         => 8,
			'custom_attributes' => [ 'autocomplete' => 'off' ],
			'value'             => $this->get_payment_method_defaults()['check-number'] ?? '',
		];

		return $fields;
	}


	/**
	 * Determines whether the check number field should be required.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function is_check_number_required() : bool {

		return $this->get_option( 'check_number_mode' ) === 'required';
	}


	/**
	 * Determines whether the check number field is enabled.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function is_check_number_enabled() : bool {

		return $this->get_option( 'check_number_mode' ) !== 'off';
	}


	/**
	 * Displays the authorization message.
	 *
	 * @since 2.0.0
	 */
	public function display_authorization_message() {

		// do not show authorization message on My Payment Methods page
		if ( is_account_page() ) {
			return;
		}

		/**
		 * Filters the authorization message HTML displayed at checkout.
		 *
		 * @since 2.0.0
		 * @param string $html the message HTML
		 * @param Electronic_Check $gateway the gateway object
		 */
		$html = apply_filters( 'wc_' . $this->get_id() . '_authorization_message_html', '<p class="wc-' . $this->get_id_dasherized() . '-authorization-message">' . $this->get_authorization_message() . '</p>', $this );

		echo wp_kses_post( $html );
	}


	/**
	 * Returns the default values for this payment method, used to pre-fill
	 * a valid test account number when in testing mode.
	 *
	 * @since 2.0.0
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_payment_method_defaults()
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {

			$defaults['routing-number'] = '071923284';
			$defaults['account-number'] = '41001111';
			$defaults['check-number']   = '123456';
		}

		return $defaults;
	}


	/**
	 * Gets the authorization message displayed at checkout.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_authorization_message() {

		if ( $this->supports_subscriptions() && ( \WC_Subscriptions_Cart::cart_contains_subscription() || \WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment ) ) {

			if ( $this->recurring_authorization_message ) {
				$raw_message = $this->recurring_authorization_message;
			} else {
				$raw_message = $this->get_default_recurring_authorization_message();
			}

		} else {

			if ( $this->authorization_message ) {
				$raw_message = $this->authorization_message;
			} else {
				$raw_message = $this->get_default_authorization_message();
			}
		}

		/**
		 * Filters the authorization message displayed at checkout, before replacing the placeholders.
		 *
		 * @since 2.0.0
		 * @param string $message the raw authorization message text
		 * @param Electronic_Check $gateway the gateway object
		 */
		$raw_message = apply_filters( 'wc_' . $this->get_id() . '_raw_authorization_message', $raw_message, $this );

		$order_total = ( $order = wc_get_order( $this->get_checkout_pay_page_order_id() ) ) ? $order->get_total() : WC()->cart->total;

		/**
		 * Filters the authorization message placeholders.
		 *
		 * @since 2.0.0
		 * @param array $placeholders the authorization message placeholders
		 * @param Electronic_Check $gateway the gateway object
		 */
		$placeholders = apply_filters( 'wc_' . $this->get_id() . '_authorization_message_placeholders', [
			'{merchant_name}' => get_bloginfo( 'name' ),
			'{order_total}'   => wc_price( $order_total ),
			'{order_date}'    => date_i18n( wc_date_format() ),
		], $this );

		$message = str_replace( array_keys( $placeholders ), $placeholders, $raw_message );

		/**
		 * Filters the authorization message displayed at checkout.
		 *
		 * @since 2.0.0
		 * @param string $message the authorization message text
		 * @param Electronic_Check $gateway the gateway object
		 */
		return apply_filters( 'wc_' . $this->get_id() . '_authorization_message', $message, $this );
	}


	/**
	 * Adjust the recurring authorization message placeholders for subscriptions.
	 *
	 * Mainly changing the authorization date to match if on the Change Payment screen.
	 *
	 * @since 2.0.0
	 *
	 * @param array $placeholders the authorization message placeholders
	 * @param Electronic_Check $gateway the gateway object
	 * @return array
	 */
	public function adjust_subscriptions_placeholders( $placeholders, $gateway ) {
		global $wp;

		if ( ! $gateway->supports_subscriptions() || ! \WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment ) {
			return $placeholders;
		}

		$subscription = wcs_get_subscription( absint( $wp->query_vars['order-pay'] ) );

		$placeholders['{order_date}'] = $subscription->get_date_to_display( 'next_payment' );

		return $placeholders;
	}


	/**
	 * Gets the default authorization message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_default_authorization_message() {

		return sprintf(
			/** translators: Placeholders: %1$s - the {merchant_name} placeholder, %2$s - the {order_date} placeholder, %3$s - the {order_total} placeholder */
			__( 'By clicking the button below, I authorize %1$s to charge my bank account on %2$s for the amount of %3$s.', 'woocommerce-gateway-cybersource' ),
			'{merchant_name}',
			'{order_date}',
			'{order_total}'
		);
	}


	/**
	 * Gets the default recurring authorization message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_default_recurring_authorization_message() {

		return sprintf(
			/** translators: Placeholders: %1$s - the {merchant_name} placeholder, %2$s - the {order_total} placeholder, %3$s - the {order_date} placeholder */
			__( 'By clicking the button below, I authorize %1$s to charge my bank account for the amount of %2$s on %3$s, then according to the above recurring totals thereafter.', 'woocommerce-gateway-cybersource' ),
			'{merchant_name}',
			'{order_total}',
			'{order_date}'
		);
	}


	/**
	 * Determines if the authorization message should be displayed at checkout.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_authorization_message_enabled() {

		/**
		 * Filters whether the authorization message should be displayed at checkout.
		 *
		 * @since 2.0.0
		 * @param bool $enabled
		 */
		return (bool) apply_filters( 'wc_' . $this->get_id() . '_authorization_message_enabled', 'yes' === $this->authorization_message_enabled );
	}


	/**
	 * Returns true if the posted echeck fields are valid, false otherwise.
	 *
	 * We need to override this because account number minimum length is different than the default.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $is_valid true if the fields are valid, false otherwise
	 * @return bool
	 */
	protected function validate_check_fields( $is_valid ) {

		$account_number = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-account-number' );
		$routing_number = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-routing-number' );
		$check_number   = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-check-number' );

		// routing number exists?
		if ( empty( $routing_number ) ) {

			Framework\SV_WC_Helper::wc_add_notice( esc_html__( 'Routing Number is missing', 'woocommerce-gateway-cybersource' ), 'error' );
			$is_valid = false;

		} else {

			// routing number digit validation
			if ( ! ctype_digit( $routing_number ) ) {
				Framework\SV_WC_Helper::wc_add_notice( esc_html__( 'Routing Number is invalid (only digits are allowed)', 'woocommerce-gateway-cybersource' ), 'error' );
				$is_valid = false;
			}

			// routing number length validation
			if ( 9 !== strlen( $routing_number ) ) {
				Framework\SV_WC_Helper::wc_add_notice( esc_html__( 'Routing number is invalid (must be 9 digits)', 'woocommerce-gateway-cybersource' ), 'error' );
				$is_valid = false;
			}

		}

		// account number exists?
		if ( empty( $account_number ) ) {

			Framework\SV_WC_Helper::wc_add_notice( esc_html__( 'Account Number is missing', 'woocommerce-gateway-cybersource' ), 'error' );
			$is_valid = false;

		} else {

			// account number digit validation
			if ( ! ctype_digit( $account_number ) ) {
				Framework\SV_WC_Helper::wc_add_notice( esc_html__( 'Account Number is invalid (only digits are allowed)', 'woocommerce-gateway-cybersource' ), 'error' );
				$is_valid = false;
			}

			// account number length validation
			if ( strlen( $account_number ) < 4 || strlen( $account_number ) > 17 ) {
				Framework\SV_WC_Helper::wc_add_notice( esc_html_x( 'Account number is invalid (must be between 4 and 17 digits)', 'Bank account', 'woocommerce-gateway-cybersource' ), 'error' );
				$is_valid = false;
			}
		}

		// check number validation
		if ( empty( $check_number ) && $this->is_check_number_required() ) {
			Framework\SV_WC_Helper::wc_add_notice( esc_html_x( 'Check Number is missing', 'Bank check (noun)', 'woocommerce-gateway-cybersource' ), 'error' );
			$is_valid = false;
		}

		if ( ! empty( $check_number ) && $this->is_check_number_enabled() ) {

			// check number digit validation
			if ( ! ctype_digit( $check_number ) ) {
				Framework\SV_WC_Helper::wc_add_notice( esc_html_x( 'Check Number is invalid (only digits are allowed)', 'Bank check (noun)', 'woocommerce-gateway-cybersource' ), 'error' );
				$is_valid = false;
			}

			// check number length validation
			if ( strlen( $check_number ) > 8 ) {
				Framework\SV_WC_Helper::wc_add_notice( esc_html_x( 'Check number is invalid (must be 8 digits or less)', 'Bank check (noun)', 'woocommerce-gateway-cybersource' ), 'error' );
				$is_valid = false;
			}
		}

		/**
		 * Direct Payment Gateway Validate eCheck Fields Filter.
		 *
		 * Allow actors to filter the eCheck field validation.
		 *
		 * @since 2.0.0
		 * @param bool $is_valid true for validation to pass
		 * @param Framework\SV_WC_Payment_Gateway_Direct $this direct gateway class instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_validate_echeck_fields', $is_valid, $this );
	}


	/** Admin methods *********************************************************/


	/**
	 * Returns an array of form fields specific for this method.
	 *
	 * @see SV_WC_Payment_Gateway::get_method_form_fields()
	 *
	 * @since 2.0.0
	 *
	 * @return array of form fields
	 */
	protected function get_method_form_fields() {

		// shared fields
		$fields = parent::get_method_form_fields();

		$fields['check_number_mode'] = [
			'title'    => __( 'Check Number Field', 'woocommerce-gateway-cybersource' ),
			'type'     => 'select',
			'options'  => [
				'off'      => __( 'Hidden', 'woocommerce-gateway-cybersource' ),
				'optional' => __( 'Optional', 'woocommerce-gateway-cybersource' ),
				'required' => __( 'Required', 'woocommerce-gateway-cybersource' ),
			],
			'default'  => 'required',
			'desc_tip' => __( 'Control whether a Check Number field is hidden, shown, or required during checkout.', 'woocommerce-gateway-cybersource' ),
		];

		$fields['authorization_message_enabled'] = [
			'title'   => __( 'Authorization', 'woocommerce-gateway-cybersource' ),
			'label'   => esc_html__( 'Display an authorization confirmation message at checkout', 'woocommerce-gateway-cybersource' ),
			'type'    => 'checkbox',
			'default' => 'yes',
		];

		$fields['authorization_message'] = [
			'title'       => __( 'Authorization Message', 'woocommerce-gateway-cybersource' ),
			'description' => sprintf(
				/** translators: Placeholders: %1$s - <code> tag, %2$s - </code> tag */
				esc_html__( 'Use these tags to customize your message: %1$s{merchant_name}%2$s, %1$s{order_date}%2$s, and %1$s{order_total}%2$s', 'woocommerce-gateway-cybersource' ),
				'<code>',
				'</code>'
			),
			'type'    => 'textarea',
			'class'   => 'authorization-message-field',
			'default' => $this->get_default_authorization_message(),
		];

		if ( $this->get_plugin()->is_subscriptions_active() && $this->supports_tokenization() ) {

			$new_fields['recurring_authorization_message'] = [
				'title'   => __( 'Recurring Authorization Message', 'woocommerce-gateway-cybersource' ),
				'description' => sprintf(
					/** translators: Placeholders: %1$s - <code> tag, %2$s - </code> tag */
					esc_html__( 'Use these tags to customize your message: %1$s{merchant_name}%2$s, %1$s{order_date}%2$s, and %1$s{order_total}%2$s', 'woocommerce-gateway-cybersource' ),
					'<code>',
					'</code>'
				),
				'type'    => 'textarea',
				'class'   => 'authorization-message-field',
				'default' => $this->get_default_recurring_authorization_message(),
			];
		}

		return $fields;
	}


	/**
	 * Adds some inline JS to show/hide the authorization message settings fields.
	 *
	 * @since 2.0.0
	 *
	 * @see WC_Settings_API::admin_options()
	 */
	public function admin_options() {

		parent::admin_options();

		// add inline javascript to show/hide any shared settings fields as needed
		ob_start();
		?>
			$( '#woocommerce_<?php echo sanitize_html_class( $this->get_id() ); ?>_authorization_message_enabled' ).change( function() {

				var enabled = $( this ).is( ':checked' );

				if ( enabled ) {
					$( '.authorization-message-field' ).closest( 'tr' ).show();
				} else {
					$( '.authorization-message-field' ).closest( 'tr' ).hide();
				}

			} ).change();
		<?php

		wc_enqueue_js( ob_get_clean() );

	}


}
