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

use SkyVerge\WooCommerce\Cybersource\API\Helper;
use SkyVerge\WooCommerce\Cybersource\Gateway;

defined( 'ABSPATH' ) or exit;

/**
 * Base payment token response class.
 *
 * @since 2.0.0
 */
abstract class Payment_Token extends Transaction {


	/**
	 * Gets the payment token.
	 *
	 * @since 2.0.0
	 *
	 * @return \SkyVerge\WooCommerce\Cybersource\Gateway\Payment_Token
	 */
	public function get_payment_token() {

		if ( 'credit_card' === $this->get_payment_type() ) {

			$data = [
				'type'      => 'credit_card',
				'card_type' => $this->get_type(),
				'exp_month' => $this->get_expiry_month(),
				'exp_year'  => '20' . $this->get_expiry_year(),
			];

		} else {

			$data = [
				'type'      => 'echeck',
				'card_type' => $this->get_type(),
			];
		}

		$data['first_six'] = $this->get_first_six();
		$data['last_four'] = $this->get_last_four();

		if ( $identifier = $this->get_instrument_identifier() ) {

			$data['instrument_identifier'] = [
				'id'    => $identifier->id,
				'state' => $identifier->state,
				'new'   => 'Y' === $identifier->new,
			];
		}

		return new Gateway\Payment_Token( $this->get_token_id(), $data );
	}


	/**
	 * Gets the tokenized ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	abstract public function get_token_id();


	/**
	 * Gets the returned type (visa, checking, etc...).
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	abstract public function get_type();


	/**
	 * Gets the first six digits of the card number.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_first_six() {

		return substr( $this->get_account_number(), 0, 6 );
	}


	/**
	 * Gets the last four digits of the card number.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_last_four() {

		return substr( $this->get_account_number(), -4 );
	}


	/**
	 * Gets the returned account number.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	abstract public function get_account_number();


	/**
	 * Gets the expiration month, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_expiry_month() {

		return '';
	}


	/**
	 * Gets the expiration year, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_expiry_year() {

		return '';
	}


	/**
	 * Gets the instrument identifier data, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return \stdClass|null
	 */
	public function get_instrument_identifier() {

		return ! empty( $this->response_data->instrumentIdentifier ) ? $this->response_data->instrumentIdentifier : null;
	}


	/**
	 * Determines if the transaction was successful.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function transaction_approved() {

		return $this->get_token_id();
	}


}
