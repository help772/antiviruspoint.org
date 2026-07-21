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

use SkyVerge\WooCommerce\Cybersource\API\Requests\Payments\Payment;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API check enrollment request.
 *
 * @since 2.3.0
 */
class Check_Enrollment extends Payment {


	/**
	 * Check_Enrollment constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {

		$this->path   = '/risk/v1/authentications';
		$this->method = self::REQUEST_METHOD_POST;
	}


	/**
	 * Sets the order data.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order
	 */
	public function set_order_data( \WC_Order $order ): void {

		$this->order = $order;

		$this->data = [
			'buyerInformation'           => $this->get_buyer_information(),
			'clientReferenceInformation' => $this->get_client_reference_information(),
			'deviceInformation'          => $this->get_device_information(),
			'orderInformation'           => $this->get_order_information(),
		];

		// We might not always have this data available. We need to check if it's not empty before adding
		// because if we supply empty values, the CyberSource SDK will complain. Instead they want us to omit them
		// if we don't have them.
		$cardData = array_filter([
			'expirationMonth' => $order->payment->expiration_month ?? null,
			'expirationYear'  => $order->payment->expiration_year ?? null,
		]);

		if (! empty($cardData)) {
			$this->data['paymentInformation'] = [
				'card' => $cardData,
			];
		}

		if ( $order->payment->is_transient ) {
			/**
			 * @see https://developer.cybersource.com/api-reference-assets/index.html#payer-authentication_payer-authentication_check-payer-auth-enrollment
			 *
			 * transientToken: A temporary ID that represents the customer's payment data (which is securely stored in Visa Data Centers).
			 * Flex Microform generates this ID and sets it to expire within 15 minutes from when the ID is generated or
			 * until the first payment authorization is carried out (whichever occurs first).
			 */
			$this->data['tokenInformation'] = [
				'transientToken' => $order->payment->token,
			];
		} else {
			/**
			 * @see https://developer.cybersource.com/api-reference-assets/index.html#payer-authentication_payer-authentication_check-payer-auth-enrollment
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

		// include the reference ID if there is one
		if ( ! empty( $order->payment->reference_id ) ) {

			$this->data['consumerAuthenticationInformation'] = [
				'referenceId' => $order->payment->reference_id,
				'returnUrl'   => $order->payment->step_up_return_url,
			];
		}
	}


	/**
	 * Gets the payment data.
	 *
	 * Purposefully empty.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_payment_data() {

		return [];
	}


}
