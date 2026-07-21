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

namespace SkyVerge\WooCommerce\Cybersource\API\Requests\Payments;

use Exception;
use SkyVerge\WooCommerce\Cybersource\API\Helper;
use SkyVerge\WooCommerce\Cybersource\API\Requests\Payments;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API transaction request.
 *
 * @since 2.0.0
 */
abstract class Payment extends Payments {


	/**
	 * Creates a new payment.
	 *
	 * @since 2.0.0
	 *
	 * Some data may be overwritten or merged with 3D Secure data later:
	 * @see \SkyVerge\WooCommerce\Cybersource\API\Requests\Payments\Credit_Card_Payment::create_payment()
	 *
	 * @param \WC_Order $order order object
	 * @param bool $settlement_type
	 */
	public function create_payment( \WC_Order $order, bool $settlement_type = true ) : void {

		$this->method = self::REQUEST_METHOD_POST;
		$this->order  = $order;

		$this->data = [
			'clientReferenceInformation' => $this->get_client_reference_information(),
			'orderInformation'           => $this->get_order_information(),
			'paymentInformation'         => $this->get_payment_information(),
			'processingInformation'      => $this->get_processing_information( $settlement_type ),
			'buyerInformation'           => $this->get_buyer_information(),
			'deviceInformation'          => $this->get_device_information(),
			'tokenInformation'           => $this->get_token_information( $order ),
		];
	}


	/**
	 * Gets order information (amount, items and billing details).
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_order_information(): array {

		$data = [
			'amountDetails' => $this->get_amount_details( $this->get_order()->payment_total ),
			'billTo'        => $this->get_billing_information(),
		];

		if ( $this->get_order()->has_shipping_address() ) {
			$data['shipTo'] = $this->get_shipping_information();
		}

		$data['lineItems'] = $this->get_items_information();

		if ( $this->get_order() ) {
			$data['shippingDetails'] = $this->get_shipping_details( $this->get_order() );
		}

		return $data;
	}


	/**
	 * Gets billing information.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_billing_information() {

		return $this->get_order_address_data( $this->get_order() );
	}


	/**
	 * Gets billing information.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_shipping_information() {

		$address_data = $this->get_order_address_data( $this->get_order(), 'shipping' );

		if ( $this->get_order() ) {
			$address_data['method'] = $this->get_shipping_method( $this->get_order() );
		}

		return $address_data;
	}


	/**
	 * Gets the transaction's shipping details.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @return array
	 */
	protected function get_shipping_details( \WC_Order $order ) {

		return [
			'shippingMethod' => $this->get_shipping_method( $order ),
		];
	}


	/**
	 * Gets the CyberSource shipping method name from the order's shipping data.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @return string
	 */
	private function get_shipping_method( \WC_Order $order ) {

		$cybersource_method = $order->has_shipping_address() ? 'other' : 'none';
		$shipping_methods   = $order->get_shipping_methods();

		if ( 1 === count( $shipping_methods ) ) {

			$shipping_method = current( $shipping_methods );

			if ( Framework\SV_WC_Helper::str_starts_with( $shipping_method->get_method_id(), 'local_pickup' ) ) {
				$cybersource_method = 'local';
			}
		}

		return $cybersource_method;
	}


	/**
	 * Gets items information.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_items_information() {

		$items = [];

		if ( $order = $this->get_order() ) {

			foreach ( Framework\SV_WC_Helper::get_order_line_items( $order ) as $line_item ) {

				$item = [
					'productName'    => $line_item->item->get_name(),
					'unitPrice'      => $line_item->item_total,
					'quantity'       => $line_item->quantity,
					'taxAmount'      => $line_item->item->get_total_tax(),
				];

				// if we have a product object, add the SKU if available
				if ( $line_item->product instanceof \WC_Product && $line_item->product->get_sku() ) {
					$item['productSku'] = $line_item->product->get_sku();
				}

				$items[] = $item;
			}

			foreach ( $order->get_shipping_methods() as $shipping_method ) {

				$items[] = [
					'productCode' => 'shipping_and_handling',
					'productName' => $shipping_method->get_name(),
					'productSku'  => $shipping_method->get_method_id(),
					'unitPrice'   => $shipping_method->get_total(),
					'quantity'    => 1,
					'taxAmount'   => $shipping_method->get_total_tax(),
				];
			}

			foreach ( $order->get_fees() as $fee ) {

				$items[] = [
					'productName' => $fee->get_name(),
					'unitPrice'   => $fee->get_total(),
					'quantity'    => 1,
					'taxAmount'   => $fee->get_total_tax(),
				];
			}
		}

		// sanitize dynamic values: quotes, question marks and other characters could trigger an API error
		foreach ( $items as $key => $item ) {

			$items[ $key ]['productName'] = $this->sanitize_item_name( $item['productName'] );

			if ( ! empty( $item['productSku'] ) ) {
				$items[ $key ]['productSku'] = $this->sanitize_item_name( $item['productSku'] );
			}
		}

		return $items;
	}


	/**
	 * Sanitizes an item name or SKU for API use.
	 *
	 * @see Payment::get_items_information()
	 *
	 * @since 2.0.6
	 *
	 * @param string $original_name original string
	 * @return string
	 */
	private function sanitize_item_name( $original_name ) {

		$sanitized_name = $original_name;

		// strip unsupported characters
		$unsupported_characters = [ '?', '"' ];

		foreach ( $unsupported_characters as $character ) {

			$sanitized_name = str_replace( $character, '', $sanitized_name );
		}

		// convert special characters to HTML entities
		$sanitized_name = htmlentities( $sanitized_name );

		// trim down to max 255 characters
		return Framework\SV_WC_Helper::str_truncate( trim( $sanitized_name ), 255 );
	}


	/**
	 * Gets the payment information.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_payment_information() {

		$payment     = $this->get_order()->payment;
		$customer_id = $this->get_order()->customer_id;

		// if this is a saved token, use it for the payment data
		if ( ! empty( $payment->token ) ) {
			return $this->get_tokenized_payment_data( $this->get_order() );
		}

		$data = [];

		// set the customer ID if we have one
		if ( ! empty( $customer_id ) ) {

			$data['customer'] = [
				'id' => $customer_id,
			];
		}

		// if this is a direct payment (no Flex), add regular CC data
		if ( empty( $payment->jwt ) ) {
			$data = array_merge( $data, $this->get_payment_data() );
		} else {

			// extract the card code/type from JWT instead of from the payment directly,
			// as multiple codes map to same card types (024 and 042 are both `maestro` for example)
			$card_code = $this->get_detected_card_code_from_jwt( $payment->jwt );

			// REST API does not seem to extract card type properly from tokenInformation.transientTokenJwt, if
			// generated using Microform v2 (it does seem to work with v0.11 tokens) and triggers an `INVALID_ACCOUNT`
			// error. We'll provide the card type manually to work around this issue.
			$data['card'] = [
				'type' => $card_code,
			];
		}

		return $data;
	}


	/**
	 * Gets the data for a tokenized payment.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @return mixed
	 */
	private function get_tokenized_payment_data( \WC_Order $order ) {

		// if there is no separate customer ID, use the token from Flex v0.4
		if ( empty( $order->customer_id ) ) {

			$data['customer'] = [
				'customerId' => $order->payment->token,
			];

		// otherwise we have a Flex v0.11 token
		} else {

			$data['paymentInstrument'] = [
				'id' => $order->payment->token,
			];
		}

		return $data;
	}


	/**
	 * Gets the payment method data.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	abstract protected function get_payment_data();


	/**
	 * Gets processing information.
	 *
	 * Sets special CIT or MIT transaction data if this is a recurring order.
	 *
	 * @see https://developer.cybersource.com/api/developer-guides/dita-payments/MITs/MIT_usecases/MIT_recurringpayment.html
	 *
	 * Some of the information may later be overwritten by 3D Secure data:
	 * @see \SkyVerge\WooCommerce\Cybersource\API\Requests\Payments\Credit_Card_Payment::create_payment()
	 *
	 * @since 2.0.0
	 *
	 * @param bool $settlement_type true = auth/capture, false = auth-only
	 * @return array<string, mixed>
	 */
	protected function get_processing_information( bool $settlement_type = false ): array {

		if ( $settlement_type ) {

			$processing_information = [
				'capture'           => true,
				'commerceIndicator' => 'internet',
			];

		} else {

			$processing_information = [
				'commerceIndicator' => 'internet',
			];
		}

		$payment = $this->get_order()->payment;

		// if paying with a stored credential for a recurring payment
		if ( ! empty( $payment->token ) && ! empty( $payment->recurring ) ) {

			// if this was from Checkout, it's the first payment
			if ( $this->get_order() instanceof \WC_Order && 'checkout' === $this->get_order()->get_created_via() ) {

				$processing_information['authorizationOptions'] = [
					'initiator' => [
						'credentialStoredOnFile' => 'merchant',
					],
				];

			// otherwise, try and find the original order
			} elseif ( ! empty( $payment->subscriptions ) ) {

				// CyberSource does not support multiple subscription data
				$subscription_details = current( $payment->subscriptions );

				if ( $subscription = wcs_get_subscription( $subscription_details->id ) ) {

					// get the subscription's original order
					if ( $order = $subscription->get_parent() ) {

						$processing_information['commerceIndicator'] = 'recurring';

						/**
						 * Set the original order's transaction ID.
						 *
						 * The documentation indicates this can be either the previous renewal or the original order
						 */
						$processing_information['authorizationOptions'] = [
							'initiator' => [
								'merchantInitiatedTransaction' => [
									'previousTransactionID' => $order->get_meta( '_wc_cybersource_credit_card_trans_id' ),
								],
							],
						];
					}
				}
			}
		}

		$processing_information['actionList'] = [];

		// skip any fraud checking if decision manager is disabled
		if ( empty( $this->get_order()->use_decision_manager ) ) {
			$processing_information['actionList'][] = 'DECISION_SKIP';
		}

		if ( ! empty( $this->get_order()->create_token ) ) {

			$processing_information['actionList'][] = 'TOKEN_CREATE';

			$processing_information['actionTokenTypes'] = [
				'paymentInstrument',
			];

			if ( empty( $this->get_order()->customer_id ) ) {
				$processing_information['actionTokenTypes'][] = 'customer';
			}
		}

		return $processing_information;
	}


	/**
	 * Gets buyer information.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_buyer_information() {

		$buyer_information = [];

		if ( $this->get_order() && ! empty( $this->get_order()->merchant_customer_id ) ) {

			$buyer_information = [
				'merchantCustomerId' => Framework\SV_WC_Helper::str_truncate( $this->get_order()->merchant_customer_id, 100, '' ),
			];
		}

		return $buyer_information;
	}


	/**
	 * Gets device information.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_device_information(): array {

		$data = array_merge( [
			'ipAddress'              => $this->get_order()->get_customer_ip_address(),
			'userAgentBrowserValue'  => $this->get_order()->get_customer_user_agent(),
			'httpAcceptBrowserValue' => $_SERVER['HTTP_ACCEPT'],
			'httpAcceptContent'      => $_SERVER['HTTP_ACCEPT'],
		], $this->get_order()->payment->device_data ?? [] );

		if ( ! empty( $this->get_order()->decision_manager_session_id ) ) {
			$data['fingerprintSessionId'] = $this->get_order()->decision_manager_session_id;
		}

		return $data;
	}


	/**
	 * Gets the token information.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order the order object
	 * @return array
	 */
	protected function get_token_information( \WC_Order $order ): array {

		$token_information = [];

		if ( ! empty( $order->payment->jwt ) ) {
			$token_information['jti']               = $order->payment->jti;
			$token_information['transientTokenJwt'] = $order->payment->jwt;
		}

		return $token_information;
	}


	/**
	 * Decodes the JWT.
	 *
	 * @since 2.8.0
	 *
	 * @param string $jwt
	 * @return array
	 */
	protected function decode_jwt( string $jwt ): array {
		$jwt_parts = explode( '.', $jwt );

		$header    = json_decode( base64_decode( $jwt_parts[0] ), true );
		$payload   = json_decode( base64_decode( $jwt_parts[1] ), true );
		$signature = $jwt_parts[2];

		return [ $header, $payload, $signature ];
	}


	/**
	 * Gets the card object from JWT.
	 *
	 * @since 2.8.0
	 *
	 * @param string $jwt
	 * @return array|null
	 */
	protected function get_card_from_jwt( string $jwt ) :?array {

		[ , $payload ] = $this->decode_jwt( $jwt );

		return ! empty( $payload['content']['paymentInformation']['card'] ) ? $payload['content']['paymentInformation']['card'] : null;
	}


	/**
	 * Gets the detected card code (type) from JWT token.
	 *
	 * @see Helper::$card_type_map
	 *
	 * @since 2.8.0
	 *
	 * @param string $jwt
	 * @return string|null
	 */
	protected function get_detected_card_code_from_jwt( string $jwt ) :?string {

		$card = $this->get_card_from_jwt( $jwt );

		return ! empty( $card['number']['detectedCardTypes'] ) ? $card['number']['detectedCardTypes'][0] : null;
	}


}
