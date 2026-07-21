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

namespace SkyVerge\WooCommerce\Cybersource\API\Responses\Payments;

use SkyVerge\WooCommerce\Cybersource\API\Message_Helper;
use SkyVerge\WooCommerce\Cybersource\API\Responses\Payments;
use SkyVerge\WooCommerce\Cybersource\Gateway\Payment_Token;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_API_Exception;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway_API_Customer_Response;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API Abstract Response Class
 *
 * Provides functionality common to all responses.
 *
 * @since 2.0.0
 */
abstract class Payment extends Payments implements SV_WC_Payment_Gateway_API_Customer_Response, SV_WC_Payment_Gateway_API_Create_Payment_Token_Response {


	/**
	 * Checks if the transaction was successful.
	 *
	 * Note that exceptions are handled prior to response "parsing" so there's no
	 * handling for them here.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function transaction_approved() {

		return $this->get_status_code() === self::STATUS_AUTHORIZED;
	}


	/**
	 * Checks if the transaction is pending review.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function transaction_held() {

		return $this->get_status_code() === self::STATUS_AUTHORIZED_PENDING_REVIEW;
	}


	/**
	 * Gets the error message suitable for displaying to the customer. This should
	 * provide enough information to be helpful for correcting customer-solvable
	 * issues (e.g. invalid CVV) but not enough to help nefarious folks phishing
	 * for data.
	 *
	 * @since 2.0.0
	 */
	public function get_user_message() {

		$helper = new Message_Helper( $this );

		return $helper->get_message();
	}


	/**
	 * Returns the customer ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string customer ID returned by the gateway
	 */
	public function get_customer_id() {

		return ! empty( $this->response_data->tokenInformation->customer->id ) ? $this->response_data->tokenInformation->customer->id : '';
	}


	/**
	 * Returns the payment token ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string payment token ID returned by the gateway
	 */
	protected function get_payment_token_id() {

		return ! empty( $this->response_data->tokenInformation->paymentInstrument->id ) ? $this->response_data->tokenInformation->paymentInstrument->id : '';
	}


	/**
	 * Returns the normalized data for a local payment token.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_payment_token_data() {

		return [
			'first_six'             => ! empty( $this->order->payment->first_six ) ? $this->order->payment->first_six : '',
			'last_four'             => ! empty( $this->order->payment->last_four ) ? $this->order->payment->last_four : '',
			'instrument_identifier' => [
				'id'    => ! empty( $this->response_data->tokenInformation->instrumentIdentifier->id ) ? $this->response_data->tokenInformation->instrumentIdentifier->id : '',
				'state' => ! empty( $this->response_data->tokenInformation->instrumentIdentifier->state ) ? $this->response_data->tokenInformation->instrumentIdentifier->state : '',
				'new'   => isset( $this->response_data->tokenInformation->instrumentidentifierNew ) ? $this->response_data->tokenInformation->instrumentidentifierNew : '',
			],
		];
	}


	/**
	 * Returns the payment token object.
	 *
	 * @since 2.3.0
	 *
	 * @return Payment_Token payment token object
 	 * @throws SV_WC_API_Exception
	 */
	public function get_payment_token() {

		$payment_token_id = $this->get_payment_token_id();

		if ( empty( $payment_token_id ) ) {
			throw new SV_WC_API_Exception( 'Invalid payment token ID' );
		}

		return new Payment_Token( $payment_token_id, $this->get_payment_token_data() );
	}


}
