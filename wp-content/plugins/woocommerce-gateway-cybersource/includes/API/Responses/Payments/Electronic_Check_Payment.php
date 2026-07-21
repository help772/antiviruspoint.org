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

use CyberSource\Model\PtsV2PaymentsPost201Response;
use SkyVerge\WooCommerce\Cybersource\Gateway;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API Electronic Check Payments Response Class
 *
 * Handles parsing e-check transaction responses
 *
 * @see https://developer.cybersource.com/api-reference-assets/index.html#Payments
 *
 * @since 2.0.0
 *
 * @method PtsV2PaymentsPost201Response get_response_object()
 */
class Electronic_Check_Payment extends Payment {


	/**
	 * Checks if the debit request was successful.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function transaction_approved() {

		return $this->get_status_code() === self::STATUS_PENDING;
	}


	/**
	 * Gets the response payment type.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_payment_type() {

		return Gateway::PAYMENT_TYPE_ECHECK;
	}


}
