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

namespace SkyVerge\WooCommerce\Cybersource\API\Responses\Payer_Authentication;

use SkyVerge\WooCommerce\Cybersource\API\Response;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;
use stdClass;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API payer authentication enrollment response.
 *
 * @since 2.3.0
 */
class Check_Enrollment extends Response {


	/**
	 * Gets the response status.
	 *
	 * @since 2.3.0
	 *
	 * @return string 'AUTHENTICATION_SUCCESSFUL' or 'AUTHENTICATION_FAILED' or 'PENDING_AUTHENTICATION' or 'INVALID_REQUEST' or ''
	 */
	public function get_status() : string {

		return $this->response_data->status ?? '';
	}


	/**
	 * Gets the consumer authentication information.
	 *
	 * @since 2.8.0
	 *
	 * @return stdClass|null
	 */
	public function get_consumer_authentication_information() : ?stdClass {

		return $this->response_data->consumerAuthenticationInformation ?? null;
	}


	/**
	 * Gets URL of Access Control Server, if present.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_acs_url(): string {

		return ! empty( $this->response_data->consumerAuthenticationInformation->acsUrl ) ? $this->response_data->consumerAuthenticationInformation->acsUrl : '';
	}


	/**
	 * Gets the step-up URL, if present.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_step_up_url(): string {

		return ! empty( $this->response_data->consumerAuthenticationInformation->stepUpUrl ) ? $this->response_data->consumerAuthenticationInformation->stepUpUrl : '';
	}


	/**
	 * Gets the consumer authentication access token.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_access_token(): string {

		return ! empty( $this->response_data->consumerAuthenticationInformation->accessToken ) ? $this->response_data->consumerAuthenticationInformation->accessToken : '';
	}


	/**
	 * Gets the JSON Web Token.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_payload(): string {

		return ! empty( $this->response_data->consumerAuthenticationInformation->pareq ) ? $this->response_data->consumerAuthenticationInformation->pareq : '';
	}


	/**
	 * Gets the authentication transaction ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_transaction_id(): string {

		return ! empty( $this->response_data->consumerAuthenticationInformation->authenticationTransactionId ) ? $this->response_data->consumerAuthenticationInformation->authenticationTransactionId : '';
	}


	/**
	 * Gets the directory server transaction ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_directory_server_transaction_id() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->directoryServerTransactionId ) ? $this->response_data->consumerAuthenticationInformation->directoryServerTransactionId : '';
	}


	/**
	 * Gets the CAVV value.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_cavv() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->cavv ) ? $this->response_data->consumerAuthenticationInformation->cavv : '';
	}


	/**
	 * Gets the AAV value.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_aav() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->ucafAuthenticationData ) ? $this->response_data->consumerAuthenticationInformation->ucafAuthenticationData : '';
	}


	/**
	 * Gets the AAV value indicator.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_aav_indicator() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->ucafCollectionIndicator ) ? $this->response_data->consumerAuthenticationInformation->ucafCollectionIndicator : '';
	}


	/**
	 * Gets the enrolled value.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_enrolled() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->veresEnrolled ) ? $this->response_data->consumerAuthenticationInformation->veresEnrolled : '';
	}


	/**
	 * Gets the CAVV algorithm.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_cavv_algorithm() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->cavvAlgorithm ) ? $this->response_data->consumerAuthenticationInformation->cavvAlgorithm : '';
	}


	/**
	 * Gets the ECI value
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_eci() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->eci ) ? $this->response_data->consumerAuthenticationInformation->eci : '';
	}


	/**
	 * Gets the raw ECI value
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_eci_raw() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->eciRaw ) ? $this->response_data->consumerAuthenticationInformation->eciRaw : '';
	}


	/**
	 * Gets the PARes status.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_pares_status() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->paresStatus ) ? $this->response_data->consumerAuthenticationInformation->paresStatus : '';
	}


	/**
	 * Gets the XID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_xid() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->xid ) ? $this->response_data->consumerAuthenticationInformation->xid : '';
	}


	/**
	 * Gets the 3D Secure specification version.
	 *
	 * This represents either v1 or v2.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_specification_version() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->specificationVersion ) ? $this->response_data->consumerAuthenticationInformation->specificationVersion : '';
	}


	/**
	 * Gets the 3D Secure commerce indicator.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_commerce_indicator() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->ecommerceIndicator ) ? $this->response_data->consumerAuthenticationInformation->ecommerceIndicator : '';
	}


	/**
	 * Gets the card type
	 *
	 * @since 2.7.1
	 *
	 * @return 'JCB'|'AMERICAN EXPRESS'|'VISA'|'DINERS'|'DINERS CLUB'|'MASTERCARD'|'DISCOVER'|''
	 */
	public function get_card_type(): string {

		return ! empty( $this->paymentInformation->card->type ) ? $this->paymentInformation->card->type : '';
	}


	/**
	 * Gets the error information message.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_error_message(): string {

		return ! empty( $this->response_data->errorInformation->message ) ? $this->response_data->errorInformation->message : '';
	}


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
