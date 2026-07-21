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

namespace SkyVerge\WooCommerce\Cybersource\Gateway;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Cybersource\Device_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * The base payment form handler.
 *
 * Used by eChecks or credit cards with Flex disabled.
 *
 * @since 2.1.0
 */
class Base_Payment_Form extends Framework\SV_WC_Payment_Gateway_Payment_Form {


	/**
	 * Gets the form handler class name.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_js_handler_class_name() {

		return 'WC_Cybersource_Payment_Form_Handler';
	}


	/**
	 * Renders the payment fields.
	 *
	 * Overridden to add the Test Amount field to the payment form.
	 *
	 * @since 2.1.0
	 */
	public function render_payment_fields() {

		parent::render_payment_fields();

		$input_id = 'wc-' . $this->get_gateway()->get_id_dasherized();

		// display a test amount field for error testing
		if ( ! is_add_payment_method_page() && $this->get_gateway()->is_test_environment() ) : ?>

			<div class="form-row form-row-wide">
				<label for="<?php echo sanitize_html_class( "{$input_id}-test-amount" ); ?>"><?php esc_html_e( 'Test Amount', 'woocommerce-gateway-cybersource' ); ?></label>
				<input type="text" id="<?php echo sanitize_html_class( "{$input_id}-test-amount" ); ?>" name="<?php echo esc_attr( "{$input_id}-test-amount" ); ?>" />
				<div style="font-size: 10px;" class="description"><?php esc_html_e( 'Enter a test amount to trigger a specific error response, or leave blank to use the order total.', 'woocommerce-gateway-cybersource' ); ?></div>
			</div>

		<?php endif;
	}


}
