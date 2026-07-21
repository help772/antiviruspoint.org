<?php
/**
 * WooCommerce AvaTax
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Response from POST /documents/{documentId}/$download (inbound mapper).
 *
 * Expected shape: { "payload": { "wp_wc_orders": { ... } }, "metadata": { ... } }
 *
 * @since 3.8.4
 */
class WC_AvaTax_Elr_API_Inbound_Mapper_Download_Response extends \WC_AvaTax_Elr_API_Response {


	/**
	 * Returns the mapped WooCommerce order fields from `payload.wp_wc_orders`.
	 *
	 * @since 3.8.4
	 *
	 * @return array<string, mixed>
	 */
	public function get_wp_wc_orders() : array {

		return (array) ( $this->response_data->payload->wp_wc_orders ?? array() );
	}
}
