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

defined('ABSPATH') or exit;

/**
 * Handle the Business Licensing functionality.
 *
 * @since 3.4.0
 */
class WC_AvaTax_Business_License {

	/**
	 * The URL for registering for sales tax.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	protected $register_sales_tax_url = 'https://buy.avalara.com/avalara-licensing/get-started';

	/**
	 * The URL for viewing registration status.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	protected $view_registration_status_url = 'https://www.businesslicenses.com/filingassist/registrations/statuses';

	/**
	 * Construct the class.
	 *
	 * @since 3.4.0
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Adds handler actions and filters.
	 *
	 * @since 3.4.0
	 * @codeCoverageIgnore
	 */
	protected function add_hooks() {
		// Add any business license specific hooks here if needed in the future
		// For now, the main functionality is handled through the settings page
	}

	/**
	 * Get the register for sales tax URL.
	 *
	 * @since 3.4.0
	 * @return string
	 */
	public function get_register_sales_tax_url() {
		return $this->register_sales_tax_url;
	}

	/**
	 * Get the view registration status URL.
	 *
	 * @since 3.4.0
	 * @return string
	 */
	public function get_view_registration_status_url() {
		return $this->view_registration_status_url;
	}
}