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

defined('ABSPATH') or exit;

/**
 * The NDI API List Batch Search response class.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API_List_Batch_Search_Response extends \WC_AvaTax_NDI_API_Response {

	/**
	 * Gets the batch search list results.
	 *
	 * @since 3.6.0
	 *
	 * @return array The batch search results in the expected frontend format.
	 */
	public function get_batch_search_results() {
		$response_data = $this->response_data;
		$raw_data = array();
		
		// Handle both object and array response formats
		if (is_object($response_data) && isset($response_data->value)) {
			$raw_data = $response_data->value;
		} elseif (is_array($response_data) && isset($response_data['value'])) {
			$raw_data = $response_data['value'];
		}

		$formatted_results = array();

		if (is_array($raw_data)) {
			foreach ($raw_data as $batch_item) {
				$formatted_item = $this->format_batch_search_item($batch_item);
				if (! empty($formatted_item)) {
					$formatted_results[] = $formatted_item;
				}
			}
		}

		return $formatted_results;
	}

	/**
	 * Formats a single batch search item.
	 *
	 * @since 3.6.0
	 *
	 * @param object|array $batch_item Raw batch search item from API
	 * @return array Formatted batch search item
	 */
	private function format_batch_search_item($batch_item) {
		$formatted_item = array();

		// Handle both object and array formats
		$id = $this->get_item_property($batch_item, 'id');
		$name = $this->get_item_property($batch_item, 'name');
		$created_by = $this->get_item_property($batch_item, 'createdBy');
		$created = $this->get_item_property($batch_item, 'created');
		$last_modified = $this->get_item_property($batch_item, 'lastModified');
		$status = $this->get_item_property($batch_item, 'status');

		if (! empty($id)) {
			$formatted_item['id'] = sanitize_text_field($id);
		}

		if (! empty($name)) {
			$formatted_item['name'] = sanitize_text_field($name);
		}

		if (! empty($created_by)) {
			$formatted_item['created_by'] = sanitize_text_field($created_by);
		}

		if (! empty($created)) {
			$formatted_item['created'] = sanitize_text_field($created);
			// Format the date for display
			$formatted_item['created_formatted'] = $this->format_date($created);
		}

		if (! empty($last_modified)) {
			$formatted_item['last_modified'] = sanitize_text_field($last_modified);
			// Format the date for display
			$formatted_item['last_modified_formatted'] = $this->format_date($last_modified);
		}

		if (! empty($status)) {
			$formatted_item['status'] = sanitize_text_field($status);
		}

		return $formatted_item;
	}

	/**
	 * Gets the total record count.
	 *
	 * @since 3.6.0
	 *
	 * @return int The total number of batch searches
	 */
	public function get_record_set_count() {
		$response_data = $this->response_data;
		
		// Handle both object and array response formats
		if (is_object($response_data) && isset($response_data->recordSetCount)) {
			return intval($response_data->recordSetCount);
		} elseif (is_array($response_data) && isset($response_data['recordSetCount'])) {
			return intval($response_data['recordSetCount']);
		}

		return 0;
	}

	/**
	 * Gets the next page information.
	 *
	 * @since 3.6.0
	 *
	 * @return array|null The next page info or null if no more pages
	 */
	public function get_next_page_info() {
		$response_data = $this->response_data;
		
		// Handle both object and array response formats
		if (is_object($response_data) && isset($response_data->nextPageInfo)) {
			return (array) $response_data->nextPageInfo;
		} elseif (is_array($response_data) && isset($response_data['nextPageInfo'])) {
			return $response_data['nextPageInfo'];
		}

		return null;
	}

	/**
	 * Gets the formatted response for frontend consumption.
	 *
	 * @since 3.6.0
	 *
	 * @param int $current_page The current page number (optional)
	 * @param int $per_page The number of items per page (optional)
	 * @return array The formatted response
	 */
	public function get_formatted_response($current_page = 1, $per_page = 10) {
		$batch_searches = $this->get_batch_search_results();
		$total_count = $this->get_record_set_count();
		$next_page_info = $this->get_next_page_info();

		// Calculate pagination info
		$current_skip = 0;
		$current_top = intval($per_page) ?: 10;
		
		// Use the passed current page or default to 1
		$current_page = intval($current_page) ?: 1;
		$total_pages = $total_count > 0 ? ceil($total_count / $current_top) : 1;

		return array(
			'data' => $batch_searches,
			'pagination' => array(
				'current_page' => $current_page,
				'per_page' => $current_top,
				'total_results' => $total_count,
				'total_pages' => $total_pages,
				'next_page_info' => $next_page_info
			)
		);
	}

	/**
	 * Helper method to get property from item (handles both object and array).
	 *
	 * @since 3.6.0
	 *
	 * @param object|array $item The item to get property from
	 * @param string $property The property name
	 * @return mixed The property value or null
	 */
	private function get_item_property($item, $property) {
		if (is_object($item) && isset($item->$property)) {
			return $item->$property;
		} elseif (is_array($item) && isset($item[ $property ])) {
			return $item[ $property ];
		}
		return null;
	}

	/**
	 * Formats a date string for display.
	 *
	 * @since 3.6.0
	 *
	 * @param string $date_string The ISO date string
	 * @return string The formatted date
	 */
	private function format_date($date_string) {
		try {
			$date = new DateTime($date_string);
			return $date->format('M j, Y g:i A');
		} catch (Exception $e) {
			return $date_string; // Return original if parsing fails
		}
	}
}
