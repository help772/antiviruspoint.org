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

namespace SkyVerge\WooCommerce\Cybersource\API;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API Response Message Helper
 *
 * Returns customer-friendly response messages directly from CyberSource
 * and allows to filter them
 *
 * @see SV_WC_Payment_Gateway_API_Response_Message_Helper
 *
 * @since 2.0.0
 */
class Message_Helper extends Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper {


	/**
	 * error reasons from CyberSource
	 * @see PtsV2PaymentsPost201ResponseErrorInformation::setReason
	 */
	const REASON_AVS_FAILED              = 'AVS_FAILED';
	const REASON_CONTACT_PROCESSOR       = 'CONTACT_PROCESSOR';
	const REASON_EXPIRED_CARD            = 'EXPIRED_CARD';
	const REASON_PROCESSOR_DECLINED      = 'PROCESSOR_DECLINED';
	const REASON_INSUFFICIENT_FUND       = 'INSUFFICIENT_FUND';
	const REASON_STOLEN_LOST_CARD        = 'STOLEN_LOST_CARD';
	const REASON_ISSUER_UNAVAILABLE      = 'ISSUER_UNAVAILABLE';
	const REASON_UNAUTHORIZED_CARD       = 'UNAUTHORIZED_CARD';
	const REASON_CVN_NOT_MATCH           = 'CVN_NOT_MATCH';
	const REASON_EXCEEDS_CREDIT_LIMIT    = 'EXCEEDS_CREDIT_LIMIT';
	const REASON_INVALID_CVN             = 'INVALID_CVN';
	const REASON_DECLINED_CHECK          = 'DECLINED_CHECK';
	const REASON_BLACKLISTED_CUSTOMER    = 'BLACKLISTED_CUSTOMER';
	const REASON_SUSPENDED_ACCOUNT       = 'SUSPENDED_ACCOUNT';
	const REASON_PAYMENT_REFUSED         = 'PAYMENT_REFUSED';
	const REASON_CV_FAILED               = 'CV_FAILED';
	const REASON_INVALID_ACCOUNT         = 'INVALID_ACCOUNT';
	const REASON_GENERAL_DECLINE         = 'GENERAL_DECLINE';
	const REASON_DECISION_PROFILE_REJECT = 'DECISION_PROFILE_REJECT';
	const REASON_SCORE_EXCEEDS_THRESHOLD = 'SCORE_EXCEEDS_THRESHOLD';
	const REASON_PENDING_AUTHENTICATION  = 'PENDING_AUTHENTICATION';
	const REASON_INVALID_MERCHANT_CONFIGURATION = 'INVALID_MERCHANT_CONFIGURATION';
	const REASON_INVALID_DATA = 'INVALID_DATA';

	/** @var Responses\Payments\Payment response */
	protected $response;

	/**
	 * @var array statuses returned for known message IDs
	 * @see https://www.cybersource.com/developers/getting_started/test_and_manage/legacy_scmp_api/fdc_err.html
	 */
	protected $message_ids = [

		// declined
		Responses\Payments\Payment::STATUS_DECLINED => [
			// card number
			self::REASON_INVALID_ACCOUNT      => 'card_number_invalid',
			self::REASON_STOLEN_LOST_CARD     => 'card_inactive',

			// CSC & AVS
			self::REASON_INVALID_CVN          => 'csc_invalid',
			self::REASON_CVN_NOT_MATCH        => 'csc_mismatch',
			self::REASON_AVS_FAILED           => 'avs_mismatch',

			// expiration
			self::REASON_EXPIRED_CARD         => 'card_expired',

			// general declines
			self::REASON_EXCEEDS_CREDIT_LIMIT => 'credit_limit_reached',
			self::REASON_INSUFFICIENT_FUND    => 'insufficient_funds',
			self::REASON_GENERAL_DECLINE      => 'card_declined',
			self::REASON_PROCESSOR_DECLINED   => 'card_declined',

			// fallback if we do not have the reason code mapped
			'' => 'card_declined',
		],

		// held
		Responses\Payments\Payment::STATUS_AUTHORIZED_PENDING_REVIEW => [
			self::REASON_CV_FAILED         => 'held_for_incorrect_csc',
			self::REASON_CONTACT_PROCESSOR => 'held_for_review',

			// fallback if we do not have the reason code mapped
			'' => 'held_for_review',
		],

		// rejected
		Responses\Payments\Payment::STATUS_AUTHORIZED_RISK_DECLINED => [
			self::REASON_DECISION_PROFILE_REJECT => 'declined_by_decision_manager',
		],

		// invalid data
		Responses\Payments\Payment::STATUS_INVALID_REQUEST => [
			self::REASON_INVALID_DATA => 'invalid_billing_data',
		],
	];


	/**
	 * Initialize the API response message handler
	 *
	 * @since 2.0.0
	 *
	 * @param Responses\Payments\Payment $response
	 */
	public function __construct( $response ) {

		$this->response = $response;
	}


	/**
	 * Gets the user-facing error/decline message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_message() {

		$response_code        = $this->get_response()->get_status_code();
		$response_reason_code = $this->get_response()->get_reason_code();

		$message_id = 'error';

		if ( isset( $this->message_ids[ $response_code ][ $response_reason_code ] ) ) {
			$message_id = $this->message_ids[ $response_code ][ $response_reason_code ];
		} elseif ( isset( $this->message_ids[ $response_code ][ '' ] ) ) {
			$message_id = $this->message_ids[ $response_code ][ '' ];
		}

		$message = $this->get_user_message( $message_id );

		/**
		 * CyberSource API Response User Message Filter.
		 *
		 * Allow actors to change the message displayed to customers as a result
		 * of a transaction error.
		 *
		 * @since 2.0.0
		 * @param string $message message displayed to customers
		 * @param string $message_id parsed message ID, e.g. 'decline'
		 * @param Message_Helper $this instance
		 */
		return apply_filters( 'wc_cybersource_api_response_user_message', $message, '', $this );
	}


	/**
	 * Gets a user message based on the given message ID.
	 *
	 * @since 2.3.0
	 *
	 * @param string $message_id
	 * @return string|null
	 */
	public function get_user_message( $message_id ) {

		$message = parent::get_user_message( $message_id );

		if ( null == $message ) {

			switch ( $message_id ) {
				case 'declined_by_decision_manager' :
					$message = __( "We're sorry, this order could not be processed due to fraud risks.", 'woocommerce-gateway-cybersource' );
				break;
				case 'invalid_billing_data' :
					$message = __( 'Please verify your billing information and try again.', 'woocommerce-gateway-cybersource' );
				break;
			}
		}

		return $message;
	}


	/**
	 * Return the response object for this user message
	 *
	 * @since 2.0.0
	 *
	 * @return Responses\Payments\Payment
	 */
	public function get_response() {

		return $this->response;
	}


}
