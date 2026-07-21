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

namespace SkyVerge\WooCommerce\Cybersource\API\Requests\Payer_Authentication;

use SkyVerge\WooCommerce\Cybersource\API\Requests\Payments;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API validate request.
 *
 * TODO: this class is not used at the moment, consider removing in the next release {@itambek 2024-02-15}
 *
 * @since 2.0.0
 */
class Validate extends Payments {


	/**
	 * Validate constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->path   = '/risk/v1/authentication-results';
		$this->method = self::REQUEST_METHOD_POST;
	}


	/**
	 * Sets the order data.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order
	 */
	public function set_order_data( \WC_Order $order ) {

		$this->order = $order;

		$this->data = [
			'clientReferenceInformation' => [
				'code' => Framework\SV_WC_Helper::str_truncate( $order->get_order_number(), 50, '' ),
			],
			'orderInformation' => [
				'amountDetails' => $this->get_amount_details( $order->get_total() ),
			],
			'consumerAuthenticationInformation' => [
				'authenticationTransactionId' => $order->payment->authentication_transaction_id,
			],
		];

		if ( $order->payment->is_transient ) {
			/**
			 * @see https://developer.cybersource.com/api-reference-assets/index.html#payer-authentication_payer-authentication_validate-authentication-results
			 *
			 * jti: TMS Transient Token, 64 hexadecimal id value representing captured payment credentials (including Sensitive Authentication Data, e.g. CVV).
			 */
			$this->data['tokenInformation'] = [
				'jti' => $order->payment->token,
			];
		} else {
			/**
			 * @see https://developer.cybersource.com/api-reference-assets/index.html#payer-authentication_payer-authentication_validate-authentication-results
			 *
			 * customerId: Unique identifier for the customer's card and billing information.
			 *
			 * When you use Payment Tokenization or Recurring Billing and you include this value in
			 * your request, many of the fields that are normally required for an authorization or credit
			 * become optional.
			 *
			 * NOTE When you use Payment Tokenization or Recurring Billing, the value for the Customer ID is actually
			 * the Cybersource payment token for a customer. This token stores information such as the consumer’s card
			 * number, so it can be applied towards bill payments, recurring payments, or one-time payments.
			 * By using this token in a payment API request, the merchant doesn't need to pass in data such as the
			 * card number or expiration date in the request itself.
			 */
			$this->data['paymentInformation'] = [
				'customer' => [
					'customerId' => $order->payment->token,
				],
			];
		}
	}


}
