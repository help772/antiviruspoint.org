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

namespace SkyVerge\WooCommerce\Cybersource\API\Requests;

use SkyVerge\WooCommerce\Cybersource\API\Helper;
use SkyVerge\WooCommerce\Cybersource\Gateway;

defined( 'ABSPATH' ) or exit;

/**
 * Payment Instrument API request
 *
 * @since 2.0.0
 */
class Payment_Instruments extends Transaction {


	public function __construct() {

		$this->path = '/tms/v1/paymentinstruments';
	}


	/**
	 * Creates a payment instrument.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function set_create_payment_instrument( \WC_Order $order ) {

		$this->method = self::REQUEST_METHOD_POST;
		$this->order  = $order;

		$data = [];

		if ( 'credit_card' === $order->payment->type ) {

			$data['card'] = [
				'expirationMonth' => $order->payment->exp_month,
				'expirationYear'  => '20' . $order->payment->exp_year,
				'type'            => $order->payment->card_type, // their docs indicate a type code, but only accepts slugs in reality
			];

			$data['billTo'] = $this->get_order_address_data( $order );

			$data['instrumentIdentifier'] = [
				'card' => [
					'number' => $order->payment->account_number,
				],
			];

		} elseif ( Gateway::PAYMENT_TYPE_ECHECK === $order->payment->type ) {

			$data['bankAccount'] = [
				'type' => $order->payment->account_type,
			];

			$data['instrumentIdentifier'] = [
				'bankAccount' => [
					'number'        => $order->payment->account_number,
					'routingNumber' => $order->payment->routing_number,
				],
			];
		}

		$this->data = $data;
	}


	/**
	 * Updates a payment instrument.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function set_update_payment_instrument( \WC_Order $order ) {

		$this->order  = $order;
		$this->method = self::REQUEST_METHOD_PATCH;
		$this->path  .= '/' . $order->payment->token;

		$data = [
			'billTo' => $this->get_order_address_data( $order ),
		];

		if ( Gateway::PAYMENT_TYPE_CREDIT_CARD === $order->payment->type ) {

			$data['card'] = [
				'expirationMonth' => $order->payment->exp_month,
				'expirationYear'  => '20' . $order->payment->exp_year,
				'type'            => Helper::convert_card_type_to_code( $order->payment->card_type ),
			];

		} elseif ( Gateway::PAYMENT_TYPE_ECHECK === $order->payment->type ) {

			$data['bankAccount'] = [
				'type' => $order->payment->account_type,
			];
		}

		$this->data = $data;
	}


	/**
	 * Gets a payment instrument.
	 *
	 * @since 2.0.0
	 *
	 * @param string $id payment instrument ID
	 */
	public function set_get_payment_instrument( $id ) {

		$this->method = self::REQUEST_METHOD_GET;
		$this->path  .= '/' . $id;
	}


	/**
	 * Sets the data to delete a payment instrument.
	 *
	 * @since 2.0.0
	 *
	 * @param string $id payment instrument ID
	 */
	public function set_delete_payment_instrument( $id ) {

		$this->method = self::REQUEST_METHOD_DELETE;
		$this->path  .= '/' . $id;
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
		if ( isset( $data['instrumentIdentifier']['card']['number'] ) ) {

			$number = $data['instrumentIdentifier']['card']['number'];

			$string = str_replace( $number, str_repeat( '*', strlen( $number ) - 4 ) . substr( $number, -4 ), $string );
		}

		// echeck number
		if ( isset( $data['instrumentIdentifier']['bankAccount']['number'] ) ) {

			$number = $data['instrumentIdentifier']['bankAccount']['number'];

			$string = str_replace( $number, str_repeat( '*', strlen( $number ) ), $string );
		}

		return $string;
	}


}
