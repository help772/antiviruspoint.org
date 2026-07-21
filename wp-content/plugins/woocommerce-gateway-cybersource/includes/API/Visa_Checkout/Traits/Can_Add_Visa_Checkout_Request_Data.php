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

namespace SkyVerge\WooCommerce\Cybersource\API\Visa_Checkout\Traits;

defined( 'ABSPATH' ) or exit;

/**
 * Helper methods to add Visa Checkout data for various transaction requests.
 *
 * @since 2.3.0
 */
trait Can_Add_Visa_Checkout_Request_Data {


	/**
	 * Adds Visa Checkout processing information to the given data array.
	 *
	 * If processing information already exists, it merges it with the Visa Checkout data.
	 *
	 * @since 2.3.0
	 *
	 * @param array $data request data to update
	 * @param \WC_Order $order order object
	 * @param string $prop name of a property in the order object that could hold Visa Checkout data
	 * @return array
	 */
	protected function maybe_add_visa_checkout_processing_information( $data, \WC_Order $order, $prop ) {

		if ( $info = $this->get_visa_checkout_processing_information( $order, $prop ) ) {

			if ( isset( $data['processingInformation'] ) && is_array( $data['processingInformation'] ) ) {
				$data['processingInformation'] = array_merge( $data['processingInformation'], $info );
			} else {
				$data['processingInformation'] = $info;
			}
		}

		return $data;
	}


	/**
	 * Gets Visa Checkout processing information from the given order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $prop name of a property in the order object that could hold Visa Checkout data
	 * @return array
	 */
	protected function get_visa_checkout_processing_information( \WC_Order $order, $prop ) {

		if ( empty( $order->$prop->visa_checkout->callid ) ) {
			return [];
		}

		return [
			'visaCheckoutId'  => $order->$prop->visa_checkout->callid,
			'paymentSolution' => 'visacheckout',
		];
	}


	/**
	 * Gets Visa Checkout fluid data from the given order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $prop name of a property in the order object that could hold Visa Checkout data
	 * @return array
	 */
	protected function get_visa_checkout_fluid_data( \WC_Order $order, $prop ) {

		if ( empty( $order->$prop->visa_checkout->enc_key ) ) {
			return [];
		}

		return [
			'value' => $order->$prop->visa_checkout->enc_payment_data,
			'key'   => $order->$prop->visa_checkout->enc_key,
		];
	}


}
