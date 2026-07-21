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

use SkyVerge\WooCommerce\Cybersource\API\Requests\Payments;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API Authorization Reversal Request Class
 *
 * Handles reversal requests
 *
 * @since 2.0.0
 */
class Authorization_Reversal extends Payments {


	/**
	 * Creates an authorization reversal for the order.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function create_authorization_reversal( \WC_Order $order ) {

		$this->order  = $order;
		$this->method = self::REQUEST_METHOD_POST;
		$this->path  .= $order->refund->trans_id . '/reversals';

		$this->data = [
			'reversalInformation' => $this->get_reversal_information(),
		];

		$this->data = $this->maybe_add_visa_checkout_processing_information( $this->data, $order, 'refund' );
	}


	/**
	 * Gets the reversal information (amount details).
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_reversal_information() {

		return [
			'amountDetails' => $this->get_amount_details( $this->get_order()->refund->amount ),
		];
	}


}
