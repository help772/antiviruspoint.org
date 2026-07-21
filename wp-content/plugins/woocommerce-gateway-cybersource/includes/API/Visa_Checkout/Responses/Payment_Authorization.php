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

namespace SkyVerge\WooCommerce\Cybersource\API\Visa_Checkout\Responses;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Visa Checkout response data for an authorized payment.
 *
 * @since 2.3.0
 */
class Payment_Authorization extends Framework\SV_WC_API_JSON_Response {


	/**
	 * Gets the authorization transaction ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_transaction_id() {

		return (string) $this->callid;
	}


	/**
	 * Gets the encrypted key used to decrypt the payment data.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_encrypted_key() {

		return (string) $this->encKey;
	}


	/**
	 * Gets the encrypted consumer and payment data that can be used to process the transaction.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_encrypted_payment_data() {

		return (string) $this->encPaymentData;
	}


	/**
	 * Gets the authorized card type.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_card_type() {

		$card_type = ! empty( $this->partialPaymentInstrument->paymentType->cardBrand ) ? strtolower( $this->partialPaymentInstrument->paymentType->cardBrand ) : 'card';

		return Framework\SV_WC_Payment_Gateway_Helper::normalize_card_type( $card_type );
	}


	/**
	 * Gets the last four digits of the authorized card.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_last_four() {

		if ( ! empty( $this->partialPaymentInstrument->lastFourDigits ) ) {
			return substr( $this->partialPaymentInstrument->lastFourDigits, -4 );
		}

		return '';
	}


}
