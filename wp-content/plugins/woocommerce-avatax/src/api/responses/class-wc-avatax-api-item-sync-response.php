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
 * @author    Avalara
 * @copyright Copyright (c) 2016-2022, Avalara, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

 /**
 * The CREATE item sync API response.
 *
 * @since 3.1.0
 */
class WC_AvaTax_API_Item_Sync_Response extends \WC_AvaTax_API_Response {
	/** @var string pending classification status */
	const PRODUCT_SYNC_STATUS = 'Error';

    /**
	 * Gets the status.
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	public function get_item_sync_data() {
		return $this->response_data;
	}

	// Add fucntion for handling rros and passing result
	/**
	 * Gets the item ID, returned from API.
	 *
	 * @since 3.1.0
	 *
	 * @return string|null
	 */
	public function get_avatax_item_id() {
		if (isset($this->response_data->result)
			&& is_array($this->response_data->result)
			&& !empty($this->response_data->result)) {
			return $this->response_data->result[0]->itemId ?? null;
		}
		return null;
	}

	/**
	 * Gets sync status, returned from API.
	 *
	 * @since 3.1.0
	 *
	 * @return string|null
	 */
	public function get_avatax_item_sync_status() {

		return ($this->response_data->result->itemEvent === self::PRODUCT_SYNC_STATUS) ? "failed" : "completed";
	}

	/**
	 * Gets sync status if it has errors.
	 *
	 * @since 3.1.0
	 *
	 * @return string|null
	 */
	public function item_sync_has_errors() {
		if (isset($this->response_data->result)) {
			if (is_array($this->response_data->result) && isset($this->response_data->result[0]) && isset($this->response_data->result[0]->errors)) {
				return $this->response_data->result[0]->errors;
			}
			if(isset($this->response_data->result) && isset($this->response_data->result->errors)) {
				return $this->response_data->result->errors;
			}
		}
		return null;
	}

	/**
	 * Gets sync event returned.
	 *
	 * @since 3.1.0
	 *
	 * @return string|null
	 */
	public function item_sync_event() {
		// Check if result is an array with index 0
		if (is_array($this->response_data->result) && isset($this->response_data->result[0]) && isset($this->response_data->result[0]->itemEvent)) {
			return $this->response_data->result[0]->itemEvent ?? null;
		}
		
		if (isset($this->response_data->result) && isset($this->response_data->result->itemEvent)) {
			$this->response_data->result->itemEvent;
		}
		// If result is an object directly
		return  null;

	}

	public function has_errors() {
		// Check for general API errors from parent class
		$general_errors = parent::has_errors();
		$sync_errors = $this->item_sync_has_errors();
		return $general_errors || !empty($sync_errors);
	}
}
