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

namespace SkyVerge\WooCommerce\Cybersource\API\Requests\Payer_Authentication;

use SkyVerge\WooCommerce\Cybersource\API\Request;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API check enrollment request.
 *
 * @since 2.0.0
 */
class Setup extends Request {


	/**
	 * Payments constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->path   = '/risk/v1/authentication-setups';
		$this->method = self::REQUEST_METHOD_POST;
	}


	/**
	 * Sets the order data for 3D Secure setup.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 */
	public function set_order_data( \WC_Order $order ) {

		$this->data = [
			'clientReferenceInformation' => [
				'code' => Framework\SV_WC_Helper::str_truncate( $order->get_order_number(), 50, '' ),
			]
		];

		if ( $order->payment->is_transient ) {
			$this->data['tokenInformation'] = [
				'transientToken' => $order->payment->token,
			];
		} else {
			$this->data['paymentInformation'] = [
				'customer' => [
					'customerId' => $order->payment->token,
				],
			];
		}

	}


}
