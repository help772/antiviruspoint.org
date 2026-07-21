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

namespace SkyVerge\WooCommerce\Cybersource\Legacy;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Cybersource\API\Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * Gateway class
 *
 * For test transactions:
 *
 * Card Type/Number:
 * Visa       / 4111111111111111
 * MasterCard / 5555555555554444
 * Amex       / 378282246310005
 * Discover   / 6011111111111117
 *
 * Expiration Date: in the future
 * Card Security Code: ignored
 *
 * @since 1.0
 *
 * @deprecated since 2.8.0 this class and its methods are kept only for legacy purposes and shouldn't be relied upon
 */
class Gateway extends Framework\SV_WC_Payment_Gateway_Direct {


	/** @var string transaction endpoint for test mode */
	private $test_endpoint = 'https://ics2wstesta.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.26.wsdl';

	/** @var string transaction endpoint for production mode */
	private $live_endpoint = 'https://ics2wsa.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.26.wsdl';

	/** @var string the account merchant id */
	protected $merchantid;

	/** @var string test transaction key */
	protected $transactionkeytest;

	/** @var string live transaction key */
	protected $transactionkeylive;


	/**
	 * Initialize the payment gateway
	 *
	 * @see WC_Payment_Gateway::__construct()
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

		parent::__construct(
			\SkyVerge\WooCommerce\Cybersource\Plugin::LEGACY_GATEWAY_ID,
			wc_cybersource(),
			[
				'method_title'       => __( 'CyberSource', 'woocommerce-gateway-cybersource' ),
				'method_description' => __( 'CyberSource Simple Order (SOAP) provides a seamless and secure checkout process for your customers', 'woocommerce-gateway-cybersource' ),
				'supports'           => [
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
				],
				'environments' => [
					self::ENVIRONMENT_PRODUCTION => esc_html_x( 'Production', 'software environment', 'woocommerce-gateway-cybersource' ),
					self::ENVIRONMENT_TEST       => esc_html_x( 'Test', 'software environment', 'woocommerce-gateway-cybersource' ),
				],
				'payment_type' => self::PAYMENT_TYPE_CREDIT_CARD,
				'card_types'   => $card_type_options,
			]
		);
	}


	/**
	 * Initialize Settings Form Fields
	 *
	 * Add an array of fields to be displayed
	 * on the gateway's settings screen.
	 *
	 * @see \WC_Settings_API::init_form_fields()
	 */
	public function get_method_form_fields() {

		return [
			'merchantid' => [
				'title'    => __( 'Merchant ID', 'woocommerce-gateway-cybersource' ),
				'type'     => 'text',
				'desc_tip' => __( 'Your CyberSource merchant id.  This is what you use to log into the CyberSource Business Center.', 'woocommerce-gateway-cybersource' ),
			],

			'transactionkeytest' => [
				'title'    => __( 'Test Transaction Security Key', 'woocommerce-gateway-cybersource' ),
				'type'     => 'password',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( "The transaction security key for your test account.  Find this by logging into your Test CyberSource Business Center and going to Account Management &gt; Transaction Security Keys &gt; Security Keys for the SOAP Toolkit API and clicking 'Generate'.", 'woocommerce-gateway-cybersource' ),
			],

			'transactionkeylive' => [
				'title'    => __( 'Live Transaction Security Key', 'woocommerce-gateway-cybersource' ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( "The transaction security key for your live account.  Find this by logging into your Live CyberSource Business Center and going to Account Management &gt; Transaction Security Keys &gt; Security Keys for the SOAP Toolkit API and clicking 'Generate'.", 'woocommerce-gateway-cybersource' ),
			],
		];
	}


	/**
	 * Checks for proper gateway configuration.
	 *
	 * @since 2.0.0
	 */
	public function is_configured() {

		return $this->get_merchant_id() && $this->get_transaction_key();
	}


	/**
	 * Gets the payment form field defaults.
	 *
	 * Adds a test card number default in test mode.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {
			$defaults['account-number'] = '4111111111111111';
		}

		return $defaults;
	}


	/**
	 * Processes the payment and return the result
	 *
	 * @since 1.2
	 *
	 * @see WC_Payment_Gateway::process_payment()
	 *
	 * @param int $order_id the order identifier
	 * @return array|void
 	 * @throws \SoapFault
	 */
	public function process_payment( $order_id ) {

		$order = $this->get_order( $order_id );

		try {

			$request = new \stdClass();

			$request->merchantID = $this->get_merchant_id();

			$request->merchantReferenceCode = ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce-gateway-cybersource' ) );

			$request->clientLibrary        = 'PHP';
			$request->clientLibraryVersion = phpversion();
			$request->clientEnvironment    = php_uname();

			// always authorize
			$cc_auth_service        = new \stdClass();
			$cc_auth_service->run   = 'true';
			$request->ccAuthService = $cc_auth_service;

			// capture?
			if ( $this->perform_credit_card_charge() ) {
				$cc_capture_service        = new \stdClass();
				$cc_capture_service->run   = 'true';
				$request->ccCaptureService = $cc_capture_service;
			}

			$bill_to              = new \stdClass();
			$bill_to->firstName   = $order->get_billing_first_name( 'edit' );
			$bill_to->lastName    = $order->get_billing_last_name( 'edit' );
			$bill_to->company     = $order->get_billing_company( 'edit' );
			$bill_to->street1     = $order->get_billing_address_1( 'edit' );
			$bill_to->street2     = $order->get_billing_address_2( 'edit' );
			$bill_to->city        = $order->get_billing_city( 'edit' );
			$bill_to->state       = $order->get_billing_state( 'edit' );
			$bill_to->postalCode  = $order->get_billing_postcode( 'edit' );
			$bill_to->country     = $order->get_billing_country( 'edit' );
			$bill_to->phoneNumber = $order->get_billing_phone( 'edit' );
			$bill_to->email       = $order->get_billing_email( 'edit' );

			if ( $order->get_user_id() ) {
				$bill_to->customerID = $order->get_user_id();
			}

			$bill_to->ipAddress = $order->get_customer_ip_address( 'edit' );

			$request->billTo = $bill_to;

			$card                  = new \stdClass();
			$card->accountNumber   = $order->payment->account_number;
			$card->expirationMonth = $order->payment->exp_month;
			$card->expirationYear  = $order->payment->exp_year;
			$card->cvNumber        = $order->payment->csc;
			$card->cardType        = Helper::convert_card_type_to_code( $order->payment->card_type );
			$request->card         = $card;

			$purchase_totals                   = new \stdClass();
			$purchase_totals->currency         = $order->get_currency();
			$purchase_totals->grandTotalAmount = number_format( $order->get_total(), 2, '.', '' );
			$request->purchaseTotals           = $purchase_totals;

			$items = [];

			foreach ( Framework\SV_WC_Helper::get_order_line_items( $order ) as $line_item ) {

				$item              = new \stdClass();
				$item->productName = $line_item->name;

				// if we have a product object, add the SKU if available
				if ( $line_item->product instanceof \WC_Product && $line_item->product->get_sku() ) {
					$item->productSKU = $line_item->product->get_sku();
				}

				$item->unitPrice = $line_item->item_total;
				$item->quantity  = $line_item->quantity;
				$item->id        = count( $items );

				$items[] = $item;
			}

			if ( ! empty( $items ) ) {
				$request->item = $items;
			}

			$enable_xdebug = false;

			if ( function_exists( 'xdebug_is_enabled' ) && xdebug_is_enabled() ) {

				$enable_xdebug = true;

				if ( function_exists( 'xdebug_disable' ) ) {
					xdebug_disable();
				}
			}

			/**
			 * Filter the CyberSource API (SoapClient) options array
			 *
			 * @since 1.6.1
			 * @param array $api_options the options array
			 */
			$api_options = apply_filters( 'wc_cybersource_api_options', [] );

			$soap_client = @new API( $this->get_endpoint_url(), $api_options );

			if ( $enable_xdebug && function_exists( 'xdebug_enable' ) ) {
				xdebug_enable();
			}

			$soap_client->set_ws_security( $this->get_merchant_id(), $this->get_transaction_key() );

			// perform the transaction
			if ( $this->debug_log() ) {

				// if logging is enabled, log the transaction request, masking the sensitive account number
				$request->card->accountNumber = $this->mask_account( $request->card->accountNumber );

				wc_cybersource()->log( "Request:\n" . print_r( $request, true ) );

				$request->card->accountNumber = $order->payment->account_number;
			}

			/**
			 * Filter the request object
			 *
			 * @since 1.6.2
			 * @param object $request the request object
			 * @param \WC_Order $order
			 */
			$request = apply_filters( 'wc_cybersource_request_object', $request, $order );

			$response = $soap_client->runTransaction( $request );

			// if debug mode load the cybersource response into the messages object
			if ( $this->debug_checkout() ) {
				$this->response_debug_message( $response );
			}

			if ( $this->debug_log() ) {
				wc_cybersource()->log( "Response:\n" . print_r( $response, true ) );
			}

			// store the payment information in the order, regardless of success or failure
			$order->update_meta_data( '_wc_cybersource_trans_id',         $response->requestID );
			$order->update_meta_data( '_transaction_id',                  $response->requestID );
			$order->update_meta_data( '_wc_cybersource_environment',      $this->get_environment() );
			$order->update_meta_data( '_wc_cybersource_card_type',        isset( $request->card->cardType ) ? Helper::convert_code_to_card_type( $request->card->cardType ) : '' );
			$order->update_meta_data( '_wc_cybersource_account_four',     isset( $request->card->accountNumber ) ? substr( $request->card->accountNumber, -4 ) : '' );
			$order->update_meta_data( '_wc_cybersource_card_expiry_date', isset( $request->card->expirationMonth ) && isset( $request->card->expirationYear ) ? $request->card->expirationYear . '-' . $request->card->expirationMonth : '' );
			$order->update_meta_data( '_wc_cybersource_trans_date',       current_time( 'mysql' ) );

			if ( isset( $response->ccAuthReply->authorizationCode ) ) {
				$order->update_meta_data( '_wc_cybersource_authorization_code', $response->ccAuthReply->authorizationCode );
			}

			if ( 'ACCEPT' === $response->decision ) {

				// Successful payment:
				$order->update_meta_data( '_wc_cybersource_charge_captured', $this->perform_credit_card_charge() ? 'yes' : 'no' );

				$order_note = $this->is_production_environment() ?
								__( 'Credit Card Transaction Approved: %s ending in %s (%s)', 'woocommerce-gateway-cybersource' ) :
								__( 'TEST MODE Credit Card Transaction Approved: %s ending in %s (%s)', 'woocommerce-gateway-cybersource' );
				$order->add_order_note( sprintf( $order_note,
												Framework\SV_WC_Payment_Gateway_Helper::payment_type_to_name( Helper::convert_code_to_card_type( $request->card->cardType ) ), substr( $request->card->accountNumber, -4 ), $request->card->expirationMonth . '/' . $request->card->expirationYear ) );
				$order->payment_complete();

			} elseif ( 'REVIEW' === $response->decision ) {

				// Transaction requires review:

				// admin message
				$error_message = '';

				if ( 230 === (int) $response->reasonCode ) {
					$error_message = __( 'The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the CVN check.  You must log into your CyberSource account and decline or settle the transaction.', 'woocommerce-gateway-cybersource' );
				}

				if ( $error_message ) {
					$error_message = ' - ' . $error_message;
				}

				// Mark on-hold
				$order_note = sprintf( __( 'Code %s %s', 'woocommerce-gateway-cybersource' ), $response->reasonCode, $error_message );

				$this->mark_order_for_review( $order, $order_note );

				// user message:
				// specific messages based on reason code
				if ( 230 === (int) $response->reasonCode ) {
					wc_add_notice( __( 'This order is being placed on hold for review due to an incorrect card verification number. You may contact the store to complete the transaction.', 'woocommerce-gateway-cybersource' ), 'error' );
				}

				// provide some default error message as needed
				if ( 0 === wc_notice_count( 'error' ) ) {
					wc_add_notice( __( 'This order is being placed on hold for review. You may contact the store to complete the transaction.', 'woocommerce-gateway-cybersource' ), 'error' );
				}

			} else {

				// Failure:
				// admin error message, and set status to 'failed'
				$order_note = __( 'CyberSource Credit Card payment failed', 'woocommerce-gateway-cybersource' ) . ' (Reason Code: ' . $response->reasonCode . ').';

				$this->mark_order_as_failed( $order, $order_note );

				// user error message
				switch( $response->reasonCode ) {
					case 202: wc_add_notice( __( 'The provided card is expired, please use an alternate card or other form of payment.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 203: wc_add_notice( __( 'The provided card was declined, please use an alternate card or other form of payment.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 204: wc_add_notice( __( 'Insufficient funds in account, please use an alternate card or other form of payment.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 208: wc_add_notice( __( 'The card is inactivate or not authorized for card-not-present transactions, please use an alternate card or other form of payment.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 210: wc_add_notice( __( 'The credit limit for the card has been reached, please use an alternate card or other form of payment.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 211: wc_add_notice( __( 'The card verification number is invalid, please try again.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 231: wc_add_notice( __( 'The provided card number was invalid, or card type was incorrect.  Please try again.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 232: wc_add_notice( __( 'That card type is not accepted, please use an alternate card or other form of payment.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
					case 240: wc_add_notice( __( 'The card type is invalid or does not correlate with the credit card number.  Please try again or use an alternate card or other form of payment.', 'woocommerce-gateway-cybersource' ), 'error' ); break;
				}

				// provide some default error message
				if ( 0 === wc_notice_count( 'error' ) ) {
					// decision will be ERROR or REJECT
					if ( 'ERROR' === $response->decision ) {
						wc_add_notice( __( 'An error occurred, please try again or try an alternate form of payment', 'woocommerce-gateway-cybersource' ), 'error' );
					} else {
						wc_add_notice( __( 'We cannot process your order with the payment information that you provided.  Please use a different payment account or an alternate payment method.', 'woocommerce-gateway-cybersource' ), 'error' );
					}
				}

				// done, stay on page and display any messages
				return;
			}

			// success or review, empty the cart and redirect to thank-you page
			WC()->cart->empty_cart();

			// Return thank you redirect
			return [
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			];

		} catch( \SoapFault $e ) {

			// always log the error message
			if ( $this->debug_log() ) {
				wc_cybersource()->log( 'Connection error: ' . $e->getMessage() );
			}

			// check if this was a timeout and put the order on hold, as the payment may have actually processed
			// https://softlayer.github.io/blog/phil/how-solve-error-fetching-http-headers/
			if ( 'Error Fetching http headers' === $e->getMessage() ) {

				$this->mark_order_for_review( $order, $e->getMessage() );

				// add an admin notice to suggest increasing default_socket_timeout
				update_option( 'woocommerce_cybersource_show_timeout_notice', 'yes' );

				wc_add_notice( __( 'This order is being placed on hold for review due to a communication error. You may contact the store to complete the transaction.', 'woocommerce-gateway-cybersource' ), 'error' );

				WC()->cart->empty_cart();

				// Return thank you redirect
				return [
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				];

			} else {

				wc_add_notice( sprintf( __( 'Connection error: "%s"', 'woocommerce-gateway-cybersource' ), $e->getMessage() ), 'error' );
			}

			return;
		}

	}


	/** Helper methods ******************************************************/


	/**
	 * Marks the given order as failed, and set the order note.
	 *
	 * @param \WC_Order $order the order
	 * @param string $order_note the order note to set
	 * @param Framework\SV_WC_Payment_Gateway_API_Response optional $response the transaction response object
	 */
	public function mark_order_as_failed( $order, $order_note, $response = null ) {

		if ( ! $order->has_status( 'failed' ) ) {
			$order->update_status( 'failed', $order_note );
		} else {
			// otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
			$order->add_order_note( $order_note );
		}
	}


	/**
	 * Marks an order for review.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Order $order
	 * @param string $message message to append to the order note
	 */
	private function mark_order_for_review( $order, $message = '' ) {

		$order_note = esc_html__( 'Transaction requires review.', 'woocommerce-gateway-cybersource' );

		if ( $message ) {
			$order_note .= ' ' . $message;
		}

		if ( ! $order->has_status( 'on-hold' ) ) {
			$order->update_status( 'on-hold', $order_note );
		} else {
			// otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
			$order->add_order_note( $order_note );
		}
	}


	/**
	 * Mask all but the first and last four digits of the given account
	 * number
	 *
	 * @param string $account_number the account number to mask
	 * @return string the masked account number
	 */
	private function mask_account( $account_number ) {
		return substr( $account_number, 0, 1 ) . str_repeat( '*', strlen( $account_number ) - 5 ) . substr( $account_number, -4 );
	}


	/**
	 * Add the cybersource POST response to the woocommerce message object
	 *
	 * @param array $response response data
	 */
	private function response_debug_message( $response ) {

		$debug_message = '<p>' . __( 'CyberSource Response:', 'woocommerce-gateway-cybersource' ) . '</p><ul>';

		foreach ( $response as $key => $value ) {

			if ( is_object( $value ) ) {
				$debug_message .= '<li>' . $key . ' => ' . print_r( $value, true ) . '</li>';
			} else {
				$debug_message .= '<li>' . $key . ' => ' . $value . '</li>';
			}

		}

		$debug_message .= '</ul>';

		wc_add_notice( $debug_message );
	}


	/** Getter methods ******************************************************/


	/**
	 * Returns the merchant id
	 *
	 * @return string merchant id
	 */
	private function get_merchant_id() {
		return $this->merchantid;
	}


	/**
	 * Returns the WSDL endpoint URL for the current mode (test/live)
	 *
	 * @return string WSDL endpoint URL
	 */
	private function get_endpoint_url() {
		return $this->is_production_environment() ? $this->live_endpoint : $this->test_endpoint;
	}


	/**
	 * Returns the Transaction Key for the current mode (test/live)
	 *
	 * @return string transaction security key
	 */
	private function get_transaction_key() {
		return $this->is_production_environment() ? $this->transactionkeylive : $this->transactionkeytest;
	}


	/**
	 * Returns the CyberSource business center transaction URL for the given order
	 *
	 * @see \WC_Payment_Gateway::get_transaction_url()
	 *
	 * @param \WC_Order $order the order object
	 * @return string cybersource transaction url or empty string
	 */
	public function get_transaction_url( $order ) {

		// build the URL to the test/production environment
		if ( 'test' === $order->get_meta( '_wc_cybersource_environment' ) ) {
			$this->view_transaction_url = 'https://ebctest.cybersource.com/ebctest/transactionsearch/TransactionSearchDetailsLoad.do?requestId=%s';
		} else {
			$this->view_transaction_url = 'https://ebc.cybersource.com/ebc/transactionsearch/TransactionSearchDetailsLoad.do?requestId=%s';
		}

		return parent::get_transaction_url( $order );
	}


	public function get_api() {

		return null;
	}


}
