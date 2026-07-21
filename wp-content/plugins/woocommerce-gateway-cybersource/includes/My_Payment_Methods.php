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

namespace SkyVerge\WooCommerce\Cybersource;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * My payment methods handler.
 *
 * @since 2.1.0
 */
class My_Payment_Methods extends Framework\SV_WC_Payment_Gateway_My_Payment_Methods {


	/**
	 * Maybe enqueue the styles and scripts.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public function maybe_enqueue_styles_scripts() {

		parent::maybe_enqueue_styles_scripts();

		if ( $this->has_tokens ) {
			wp_enqueue_script( 'wc-cybersource-my-payment-methods', $this->get_plugin()->get_plugin_url() . '/assets/js/frontend/wc-cybersource-my-payment-methods.min.js', [ 'jquery', 'sv-wc-payment-gateway-my-payment-methods-v5_15_11' ], $this->get_plugin()->get_version() );
		}
	}


	/**
	 * Gets the form handler class name.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_js_handler_class_name() {

		return 'WC_Cybersource_My_Payment_Methods_Handler';
	}


}
