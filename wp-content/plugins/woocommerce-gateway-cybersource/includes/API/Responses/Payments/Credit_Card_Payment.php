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

use SkyVerge\WooCommerce\Cybersource\Gateway;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API Credit Card Payments Response Class
 *
 * Handles parsing credit card transaction responses
 *
 * @see https://developer.cybersource.com/api-reference-assets/index.html#Payments
 *
 * @since 2.0.0
 */
class Credit_Card_Payment extends Payment implements Framework\SV_WC_Payment_Gateway_API_Authorization_Response {


	const CSC_MATCH = 'M';


	/**
	 * Gets the authorization code.
	 *
	 * @since 2.0.0
	 *
	 * @return string 6 character credit card authorization code
	 */
	public function get_authorization_code() {

		return ! empty( $this->response_data->processorInformation->approvalCode ) ? $this->response_data->processorInformation->approvalCode : '';
	}


	/**
	 * Gets the result of the AVS check.
	 *
	 * @link https://apps.cybersource.com/library/documentation/dev_guides/CC_Svcs_SCMP_API/html/wwhelp/wwhimpl/js/html/wwhelp.htm#href=app_AVS_codes.14.1.html
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_avs_result() {

		return ! empty( $this->response_data->processorInformation->avs->code ) ? $this->response_data->processorInformation->avs->code : '';
	}


	/**
	 * Gets the result of the CSC check.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_csc_result() {

		return ! empty( $this->response_data->processorInformation->cardVerification->resultCode ) ? $this->response_data->processorInformation->cardVerification->resultCode : '';
	}


	/**
	 * Determines if the CSC check was successful.
	 *
	 * @link https://apps.cybersource.com/library/documentation/dev_guides/CC_Svcs_SCMP_API/html/wwhelp/wwhimpl/js/html/wwhelp.htm#href=app_CVN_codes.html#1042779
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function csc_match() {

		return $this->get_csc_result() === self::CSC_MATCH;
	}


	/**
	 * Gets the response payment type.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_payment_type() {

		return Gateway::PAYMENT_TYPE_CREDIT_CARD;
	}


	/**
	 * Returns the normalized data for a local payment token.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function get_payment_token_data() {

		$token_data = parent::get_payment_token_data();

		$token_data['type']      = 'credit_card';
		$token_data['card_type'] = ! empty( $this->order->payment->card_type ) ? $this->order->payment->card_type : '';
		$token_data['exp_month'] = ! empty( $this->order->payment->exp_month ) ? $this->order->payment->exp_month : '';
		$token_data['exp_year']  = ! empty( $this->order->payment->exp_year ) ? substr( '20' . $this->order->payment->exp_year, - 4 ) : '';

		return $token_data;
	}


	/**
	 * Converts the response to a string with sensitive details masked.
	 *
	 * @since 2.5.1
	 *
	 * @return string
	 */
	public function to_string_safe() {
		$string = $this->to_string();

		$transaction_id = $this->response_data->consumerAuthenticationInformation->authenticationTransactionId ?? '';

		if ( $transaction_id ) {
			$string = str_replace( $transaction_id, str_repeat( '*', strlen( $transaction_id ) ), $string );
		}

		return $string;
	}


}
