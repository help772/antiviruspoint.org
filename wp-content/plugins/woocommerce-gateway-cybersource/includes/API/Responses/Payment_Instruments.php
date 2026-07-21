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
class Payment_Instruments extends Payment_Token {


	/**
	 * Payment_Instruments constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $raw_response_json raw response data
	 */
	public function __construct( $raw_response_json ) {

		parent::__construct( $raw_response_json );

		// normalize the instrument identifier data from the API response
		if ( ! empty( $this->response_data->_embedded->instrumentIdentifier ) ) {
			$this->response_data->instrumentIdentifier = $this->response_data->_embedded->instrumentIdentifier;
		}
	}


	/**
	 * Gets the transaction status code.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_status_code() {

		return '';
	}


	/**
	 * Gets the transaction status message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_status_message() {

		return implode( '. ', $this->get_errors() );
	}


	/**
	 * Gets any API errors.
	 *
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	public function get_errors() {

		$errors = [];

		if ( ! empty( $this->response_data->errors ) && is_array( $this->response_data->errors ) ) {

			foreach ( $this->response_data->errors as $error ) {

				if ( ! empty( $error->message ) ) {
					$errors[] = $error->message;
				}
			}
		}

		return $errors;
	}


	/**
	 * Gets the returned token ID, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_token_id() {

		return ! empty( $this->response_data->id ) ? $this->response_data->id : '';
	}


	/**
	 * Gets the returned type (visa, checking, etc...).
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_type() {

		if ( 'credit_card' === $this->get_payment_type() ) {
			$type = $this->get_payment()->type;
		} else {
			$type = $this->get_payment()->type;
		}

		return $type;
	}


	/**
	 * Gets the returned account number.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_account_number() {

		$account_number = '';
		$instrument_id  = $this->get_instrument_identifier();

		if ( $instrument_id ) {

			if ( 'credit_card' === $this->get_payment_type() && ! empty( $instrument_id->card->number ) ) {
				$account_number = $instrument_id->card->number;
			} elseif ( ! empty( $instrument_id->bankAccount->number ) ) {
				$account_number = $instrument_id->bankAccount->number;
			}
		}

		return $account_number;
	}


	/**
	 * Gets the expiration month, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_expiry_month() {

		return ! empty( $this->get_payment()->expirationMonth ) ? $this->get_payment()->expirationMonth : '';
	}


	/**
	 * Gets the expiration year, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_expiry_year() {

		return ! empty( $this->get_payment()->expirationYear ) ? $this->get_payment()->expirationYear : '';
	}


	/**
	 * Gets the payment type.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_payment_type() {

		return ! empty( $this->response_data->card ) ? 'credit_card' : 'echeck';
	}


	/**
	 * Gets the payment information.
	 *
	 * @since 2.0.0
	 *
	 * @return \stdClass|null
	 */
	public function get_payment() {

		$payment = null;

		if ( ! empty( $this->response_data->card ) ) {
			$payment = $this->response_data->card;
		} elseif ( ! empty( $this->response_data->bankAccount ) ) {
			$payment = $this->response_data->bankAccount;
		}

		return $payment;
	}


	/**
	 * Gets the transaction ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_transaction_id() {}


}
