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

namespace SkyVerge\WooCommerce\Cybersource\API\Responses;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource Payments API response.
 *
 * @since 2.0.0
 */
abstract class Payments extends Transaction {


	/**
	 * status values from CyberSource
	 * @see PtsV2PaymentsPost201Response::setStatus
	 */
	const STATUS_AUTHORIZED                = 'AUTHORIZED';
	const STATUS_PARTIAL_AUTHORIZED        = 'PARTIAL_AUTHORIZED';
	const STATUS_AUTHORIZED_PENDING_REVIEW = 'AUTHORIZED_PENDING_REVIEW';
	const STATUS_AUTHORIZED_RISK_DECLINED  = 'AUTHORIZED_RISK_DECLINED';
	const STATUS_DECLINED                  = 'DECLINED';
	const STATUS_INVALID_REQUEST           = 'INVALID_REQUEST';
	const STATUS_SERVER_ERROR              = 'SERVER_ERROR';
	const STATUS_PENDING                   = 'PENDING';
	const STATUS_REVERSED                  = 'REVERSED';


	/**
	 * Gets the transaction ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_transaction_id() {

		return ! empty( $this->response_data->id ) ? $this->response_data->id : '';
	}


	/**
	 * Gets the reconciliation ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_reconciliation_id() {

		return ! empty( $this->response_data->reconciliationId ) ? $this->response_data->reconciliationId : '';
	}


	/**
	 * Gets the processor transaction ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_processor_transaction_id() {

		return ! empty( $this->response_data->processorInformation->transactionId ) ? $this->response_data->processorInformation->transactionId : '';
	}


	/**
	 * Gets teh transaction status code.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_status_code() {

		return ! empty( $this->response_data->status ) ? $this->response_data->status : '';
	}


	/**
	 * Gets the transaction status message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_status_message() {

		$message    = '';
		$error_info = $this->get_error_information();

		if ( $error_info && ! empty( $error_info->message ) ) {

			$message = $error_info->message;

			if ( ! empty( $cardholder_message = $this->get_cardholder_message() ) ) {
				$message .= ' (' . $cardholder_message . ')';
			}

		} elseif ( ! empty( $this->response_data->message ) ) {

			$message = $this->response_data->message;
		}

		return $message;
	}


	/**
	 * Gets the response reason code.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_reason_code() {

		$reason     = '';
		$error_info = $this->get_error_information();

		if ( $error_info && ! empty( $error_info->reason ) ) {

			$reason = $error_info->reason;

		} elseif ( ! empty( $this->response_data->reason ) ) {

			$reason = $this->response_data->reason;
		}

		return $reason;
	}


	/**
	 * Gets the error information, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return \stdClass|null
	 */
	public function get_error_information() {

		return ! empty( $this->response_data->errorInformation ) ? $this->response_data->errorInformation : null;
	}


	/**
	 * Gets the response payment type.
	 *
	 * @since 2.0.0
	 */
	public function get_payment_type() { }


	/**
	 * Gets the message for the cardholder.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_cardholder_message(): string {

		return ! empty( $this->response_data->consumerAuthenticationInformation->cardholderMessage ) ? $this->response_data->consumerAuthenticationInformation->cardholderMessage : '';
	}


}
