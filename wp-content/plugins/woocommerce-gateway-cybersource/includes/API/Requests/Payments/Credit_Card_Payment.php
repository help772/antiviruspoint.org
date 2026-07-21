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
use SkyVerge\WooCommerce\Cybersource\API\Visa_Checkout\Traits\Can_Add_Visa_Checkout_Request_Data;
use SkyVerge\WooCommerce\Cybersource\Gateway\Credit_Card;
use SkyVerge\WooCommerce\Cybersource\Gateway\ThreeD_Secure\AJAX;
use SkyVerge\WooCommerce\Cybersource\Plugin;

defined( 'ABSPATH' ) or exit;

class Credit_Card_Payment extends Payment {


	use Can_Add_Visa_Checkout_Request_Data;


	/** auth and capture transaction type */
	const AUTHORIZE_AND_CAPTURE = true;

	/** authorize-only transaction type */
	const AUTHORIZE_ONLY = false;


	/**
	 * Creates a credit card charge request.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function create_credit_card_charge( \WC_Order $order ) {

		$this->create_payment( $order, self::AUTHORIZE_AND_CAPTURE );
	}


	/**
	 * Creates a credit card auth request.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function create_credit_card_auth( \WC_Order $order ) {

		$this->create_payment( $order, self::AUTHORIZE_ONLY );
	}


	/**
	 * Sets data to create a payment.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order
	 * @param bool $settlement_type settlement type
	 */
	public function create_payment( \WC_Order $order, bool $settlement_type = true ): void {

		parent::create_payment( $order, $settlement_type );

		if ( ! empty( $consumer_auth_information = $this->get_consumer_authentication_information( $order ) ) ) {
			$this->data['consumerAuthenticationInformation'] = $consumer_auth_information;
		}
	}


	/**
	 * Gets the customer authentication information.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @return array
	 */
	private function get_consumer_authentication_information( \WC_Order $order ): array {

		if (!isset($order->threed_secure) || empty($reference_id = $order->threed_secure->reference_id)) {
			return [];
		}

		$data = $order->threed_secure->consumer_authentication_information ?? (object) [];

		return array_filter( [
			'referenceId'                  => $reference_id,
			// The following fields are required to validate consumer authentication for 3DSecure transactions - these will only
			// be available on the order object if a step-up challenge was required in the check enrollment response.
			// Note that most fields have a 1:1 mapping, except for `specificationVersion`
			'authenticationTransactionId'  => $data->authenticationTransactionId ?? null,
			'ucafCollectionIndicator'      => $data->ucafCollectionIndicator ?? null,
			'ucafAuthenticationData'       => $data->ucafAuthenticationData ?? null,
			'cavv'                         => $data->cavv ?? null,
			'xid'                          => $data->xid ?? null,
			'veresEnrolled'                => $data->veresEnrolled ?? null,
			'directoryServerTransactionId' => $data->directoryServerTransactionId ?? null,
			'paSpecificationVersion'       => $data->specificationVersion ?? null,
			// These fields are not required for payment authentication validation, but we'll include them if available for good measure
			'eciRaw'                       => $data->eciRaw ?? null,
			'paresStatus'                  => $data->paresStatus ?? null,
		] );
	}


	/**
	 * Gets the payment information.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_payment_information() {

		$data = parent::get_payment_information();

		if ( ! empty( $data['customer']['customerId'] ) && ! empty( $this->get_order()->payment->csc ) ) {

			$data['card'] = [
				'securityCode' => $this->get_order()->payment->csc,
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
	protected function get_payment_data() {

		$payment = $this->get_order()->payment;

		if ( $fluid_data = $this->get_visa_checkout_fluid_data( $this->get_order(), 'payment' ) ) {

			$data = [ 'fluidData' => $fluid_data ];

		} elseif ( ! empty( $this->get_order()->payment->apple_pay ) ) {

			$data = [
				'fluidData' => [
					'value' => $this->get_order()->payment->apple_pay,
				],
			];

		} else if ( ! empty( $this->get_order()->payment->google_pay ) ) {

			$data = [
				'fluidData' => [
					'value' => $this->get_order()->payment->google_pay,
				],
			];

		} else {

			$data = [
				'card' => [
					'expirationYear'  => $payment->exp_year,
					'number'          => $payment->account_number,
					'securityCode'    => ! empty( $payment->csc ) ? $payment->csc : '',
					'expirationMonth' => $payment->exp_month,
				],
			];
		}

		return $data;
	}


	/**
	 * Gets the processing information.
	 *
	 * Sets the Apple Pay payment solution if paying with Apple Pay.
	 *
	 * @param bool $settlement_type settlement type
	 * @return array<string, mixed>
	 * @throws Exception
	 * @since 2.0.0
	 *
	 */
	protected function get_processing_information( bool $settlement_type = false ): array {

		$data = parent::get_processing_information( $settlement_type );

		if ( ! empty( $this->get_order()->payment->apple_pay ) ) {

			$data['paymentSolution'] = self::PAYMENT_SOLUTION_APPLE_PAY;

		} elseif ( ! empty( $this->get_order()->payment->google_pay ) ) {

			$data['paymentSolution'] = self::PAYMENT_SOLUTION_GOOGLE_PAY;
		}

		/** @var Credit_Card $gateway */
		$gateway = wc_cybersource()->get_gateway( Plugin::CREDIT_CARD_GATEWAY_ID );

		if (
			$gateway->is_3d_secure_enabled() &&
			! $gateway->is_automatic_renewal( $this->get_order()->get_id() ) &&
			$gateway->is_3d_secure_enabled_for_card_type( (string) $this->get_payment_method_card_type() )
		) {

			/**
			 * @see AJAX::check_enrollment()
			 * @see Credit_Card::get_order()
			 */
			$threed_secure_data = $this->get_order()->threed_secure;

			// ensure we have at least the reference ID and enrollment status available
			if ( empty( $threed_secure_data ) || ! $threed_secure_data->reference_id || ! $threed_secure_data->enrollment_status ) {
				throw new Exception( __( 'Payer Authentication is required for the selected payment method. Please try again or contact the store for further information.', 'woocommerce-gateway-cybersource' ) );
			}

			// set the commerce indicator from check enrollment response, if available
			if ( ! empty( $threed_secure_data->consumer_authentication_information->ecommerceIndicator ) ) {
				$data['commerceIndicator'] = $threed_secure_data->consumer_authentication_information->ecommerceIndicator;
			}

			// Unfortunately, it appears that Decision Manager cannot access the enrollment check results when performed
			// separately from payment authorization, leaving the merchant possibly vulnerable to fraudulent transactions with
			// no liability shift in cases where frictionless authentication was not available (Test Case 2.4, for example).
			// As a workaround,  we will re-check enrollment if enrollment check did not require a step-up challenge - this
			// ensures that the enrollment check results are available in the Decision Manager, allowing the merchant to
			// use the results for fraud screening.
			// TODO: UPDATE 2024-07-01 {@itambek} - unfortunately, while the above is true and ensures data is available
			//  in Decision Manager, it results in the merchant being charged twice for the enrollment check. As such,
			//  we will disable the 2nd enrollment check until we can hopefully combine the enrollment check with the
			//  authorization request in way that will result in a single check per transaction.
//			if ( $threed_secure_data->enrollment_status === 'AUTHENTICATION_SUCCESSFUL' ) {
//				$data['actionList'][] = 'CONSUMER_AUTHENTICATION';
//			}

			// validate consumer authentication for 3DSecure transactions, if a challenge was required when checking enrollment
			if ( $threed_secure_data->enrollment_status === 'PENDING_AUTHENTICATION' ) {
				$data['actionList'][] = 'VALIDATE_CONSUMER_AUTHENTICATION';
			}
		}

		if ( $info = $this->get_visa_checkout_processing_information( $this->get_order(), 'payment' ) ) {
			$data = array_merge( $data, $info );
		}

		return $data;
	}


	/**
	 * Gets the string representation of this request with all sensitive information masked.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function to_string_safe() {

		$string = $this->to_string();
		$data   = $this->get_data();

		// card number
		if ( isset( $data['paymentInformation']['card']['number'] ) ) {

			$number = $data['paymentInformation']['card']['number'];

			$string = str_replace( $number, str_repeat( '*', strlen( $number ) - 4 ) . substr( $number, -4 ), $string );
		}

		// csc
		if ( isset( $data['paymentInformation']['card']['securityCode'] ) ) {

			$csc = $data['paymentInformation']['card']['securityCode'];

			$string = str_replace( $csc, str_repeat( '*', strlen( $csc ) ), $string );
		}

		// fluid data to keep the logs small
		if ( isset( $data['paymentInformation']['fluidData']['value'] ) ) {
			$string = $this->replace_fluid_data( $data['paymentInformation']['fluidData']['value'], str_repeat( '*', 10 ), $string );
		}

		if ( isset( $data['paymentInformation']['fluidData']['key'] ) ) {
			$string = $this->replace_fluid_data( $data['paymentInformation']['fluidData']['key'], str_repeat( '*', 10 ), $string );
		}

		return $string;
	}


	/**
	 * Replaces fluid data values in the given string.
	 *
	 * JSON encoded strings include '/' characters as '\/' making them different from the value
	 * stored in the data array. As a result, str_replace() is unable to find a match.
	 *
	 * @since 2.3.0
	 *
	 * @param string $value value to replace
	 * @param string $replacement replacement string
	 * @param string $string original string
	 * @return string
	 */
	protected function replace_fluid_data( $value, $replacement, $string ) {

		// ensure / characters are escaped so that str_replace() can find a match
		$encoded_value = json_encode( $value );

		if ( JSON_ERROR_NONE === json_last_error() ) {
			// remove double quotes from encoded value to replace the content of the field only
			$encoded_value = trim( $encoded_value, '"' );
		} else {
			// attempt to replace the original value if an encoding error occurred
			$encoded_value = $value;
		}

		return str_replace( $encoded_value, $replacement, $string );
	}

	/**
	 * Gets the card type for the current payment card.
	 *
	 * Determines card type for both new and saved cards.
	 *
	 * @return string|null
	 */
	protected function get_payment_method_card_type() : ?string
	{
		$payment = $this->get_order()?->payment;

		if ($payment && ! empty($payment->token)) {
			$token = wc_cybersource()
				->get_gateway(Plugin::CREDIT_CARD_GATEWAY_ID)
				->get_payment_tokens_handler()
				->get_token(get_current_user_id(), $payment->token);

			return $token?->get_card_type();
		}

		if ($payment->jwt) {
			return Helper::convert_code_to_card_type($this->get_detected_card_code_from_jwt($payment->jwt));
		}

		return null;
	}


}
