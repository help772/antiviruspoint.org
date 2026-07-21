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

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API Get Settings request class.
 *
 * @since 3.6.2
 */
class WC_AvaTax_API_Css_Heartbeat_Request extends WC_AvaTax_API_Request {

	/**
	 * Get Woocommerce Settings from customer account in Avatax
	 *
	 * @since 3.6.2
	 *
	 */
	public function __construct($connector) {
		if ($connector !== 'ELR') {
			$applicationId = wc_avatax()::CONNECTOR_ID;
			$websiteId = get_option("wc_avatax_website_id");

			$this->path = "/active-applications/".$applicationId."/configs/" . $websiteId ."/heartbeat";
		} else {
			$applicationId = wc_avatax()::ELR_CONNECTOR_ID;
			$websiteId = get_option("wc_avatax_website_id") . "_elr";

			$this->path = "/active-applications/".$applicationId."/configs/" . $websiteId ."/heartbeat";
		}
		
		$this->method = 'PUT';
	}
}
