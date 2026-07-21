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

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API Invite Certificate response class.
 *
 * @since 3.1.0
 */
class WC_AvaTax_API_Product_Classification_System_Response extends WC_AvaTax_API_Response {


	/**
	 * Gets the Certificate Invite from response body.
	 *
	 * @since 3.1.0
	 *
	 * @return string
	 */
	public function get_system_code($country) {
		$productclassificationresponse = $this->response_data;
		$current_date = date('Y-m-d H:i:s');
		$current_date_obj = new DateTime($current_date);
		$applicable_system = '';
	
		// First check for specific system codes (excluding AVATAXCODE)
		foreach ($productclassificationresponse->value as $system) {
			// Skip AVATAXCODE as it's the default fallback
			if ($system->systemCode === 'AVATAXCODE') {
				continue;
			}
	
			// Filter country records for the specific country
			$country_records = array_filter($system->countries, function($country_data) use ($country, $current_date_obj) {
				$end_date = new DateTime($country_data->endDate);
				$start_date = new DateTime($country_data->effDate);
				
				return $country_data->country === $country 
					&& $end_date > $current_date_obj 
					&& $current_date_obj >= $start_date;
			});
	
			if (!empty($country_records)) {
				$applicable_system = $system->systemCode;
				break;
			}
		}
	
		// If no specific system is applicable, check if AVATAXCODE exists in response
		if (empty($applicable_system)) {
			$avatax_system = array_filter($productclassificationresponse->value, function($system) {
				return $system->systemCode === 'AVATAXCODE';
			});
	
			if (!empty($avatax_system)) {
				$avatax_system = reset($avatax_system); // Get the first (and only) AVATAXCODE system
				
				// Filter country records for AVATAXCODE
				$avatax_country_records = array_filter($avatax_system->countries, function($country_data) use ($country, $current_date_obj) {
					$end_date = new DateTime($country_data->endDate);
					$start_date = new DateTime($country_data->effDate);
					
					return $country_data->country === $country 
						&& $end_date > $current_date_obj 
						&& $current_date_obj >= $start_date;
				});
	
				if (!empty($avatax_country_records)) {
					$applicable_system = 'AVATAXCODE';
				}
			}
		}
	
		return $applicable_system;
	}

}
