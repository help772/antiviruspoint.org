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

use SkyVerge\WooCommerce\Cybersource\API\Helper;

defined( 'ABSPATH' ) or exit;

class Electronic_Check_Payment extends Payment {


	/**
	 * Gets the payment method data.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_payment_data() {

		$payment = $this->get_order()->payment;

		return [
			'bank' => [
				'account'       => [
					'number'      => $payment->account_number,
					'type'        => Helper::convert_bank_account_type_to_code( $payment->account_type ),
					'checkNumber' => $payment->check_number,
				],
				'routingNumber' => $payment->routing_number,
			],
		];
	}


	/**
	 * @param bool $settlement_type
	 * @return array
	 */
	protected function get_processing_information( $settlement_type = false ): array {

		$data = parent::get_processing_information( $settlement_type );

		$data['bankTransferOptions'] = [
			'secCode' => 'WEB',
		];

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
		if ( isset( $data['paymentInformation']['bank']['account']['number'] ) ) {

			$number = $data['paymentInformation']['bank']['account']['number'];

			$string = str_replace( $number, str_repeat( '*', strlen( $number ) - 4 ) . substr( $number, -4 ), $string );
		}

		// csc
		if ( isset( $data['paymentInformation']['bank']['routingNumber'] ) ) {

			$number = $data['paymentInformation']['bank']['routingNumber'];

			$string = str_replace( $number, str_repeat( '*', strlen( $number ) ), $string );
		}

		return $string;
	}


}
