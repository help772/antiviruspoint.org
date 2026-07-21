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
 * @copyright  Copyright (c) 2016 Avalara, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
namespace SkyVerge\WooCommerce\AvaTax\API\Requests;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;
use WC_AvaTax_API_Request;
defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax REST API utility request class.
 *
 * This is used when testing connectivity, etc...
 *
 * @since 3.1.0
 */
class WC_AvaTax_API_Item_Sync_Request extends WC_AvaTax_API_Request {

	/**
	 * Prepares the request data.
	 *
	 * @since 3.1.0
	 *
	 * @param array $data
	 */
    public function prepare_request($item, $type) {

		//get item code from product category, if path has 2 param pick it else pass the default
		$product_category_arr = explode('-',wc_avatax()->wc_avatax_utilities()->get_product_category($item));
		$item_group = isset($product_category_arr[1]) ? $product_category_arr[1] : $product_category_arr[0];

		$productSyncStatus = $item->get_meta('_wc_avatax_product_ids');
		$id =  (is_array($productSyncStatus) && isset($productSyncStatus[wc_avatax()->get_company_id()]))
			? $productSyncStatus[wc_avatax()->get_company_id()]
			: 0;

		//prepare json request for item sync api call 
		$params = [
			'itemid' 				   => $id,
			'itemCode'                 => $item->get_id(),
			'description'              => $item->get_title(),
			'taxCode'  				   => wc_avatax()->wc_avatax_utilities()->get_wc_meta($item->get_id(), '_wc_avatax_code', true, 'product'),
			'summary'				   => $item->get_description(),
			'itemGroup'                => $item_group,
			'category' 				   => wc_avatax()->wc_avatax_utilities()->get_product_category($item),
			'source' 				   => "WooCommerce_" . wc_avatax()->get_company_id(),
			"sourceEntityId"		   => "WooCommerce_" . wc_avatax()->get_company_id(),
			// Classification will be array here
			'classifications'          => $this->getClassification($item),
			'parameters'          	   => $this->getParameters($item)
		];

		$this->path = '/companies/' . wc_avatax()->get_company_id() . '/itemcatalogue';
		$this->method = 'POST';

		// Assign produtc prepared object to this->data which get called from perform request
		$this->data =  $params;
	}
	
	/**
	 * Prepare classfications
	 *
	 * @since 3.1.0
	 *
	 * @return array
	 */
	public function getClassification($item) {
		//define array for calssification
		$classification = array();
		// Get existing HS countries to check for removals
		$hs_countries = wc_avatax()->wc_avatax_utilities()->get_wc_meta($item->get_id(), '_wc_avatax_hs_countries', true, 'product');
		// $hs_countries = $item->get_meta('_wc_avatax_hs_countries') ?: array();
		
		// prepare classification array as per hscode and countries exist
		$i = 0;
		foreach ($hs_countries as $hs_country) {
			if (isset($hs_countries[$hs_country])) {
				$classification[$i]['id'] = $i;
				$meta_key = '_wc_avatax_hs_'.$hs_country;
				$classification[$i]['productCode'] = wc_avatax()->wc_avatax_utilities()->get_wc_meta($item->get_id(), $meta_key, true, 'product'); 
				$classification[$i]['systemCode'] = wc_avatax()->get_api()->get_product_classification_system_code($hs_country);
				$classification[$i]['country'] = $hs_country;
				$classification[$i]['isPremium'] = "false"; // Always set as false as per requirement 
			}
			$i ++;
		}
		return $classification;
	}

	/**
	 * Prepare parameters
	 *
	 * @since 3.1.0
	 *
	 * @return array
	 */
	public function getParameters($item) {
		$parameters = array();
		$product_id = $item->get_id();
		$id = 0;
		
		$this->add_summary_parameter($parameters, $id, $item, $product_id);
		$this->add_sku_parameter($parameters, $id, $item, $product_id);
		
		if (wc_avatax()->wc_avatax_utilities()->has_landed_cost_subscription()) {
			$this->add_country_of_manufacture_parameter($parameters, $id, $product_id);
			$this->add_weight_parameter($parameters, $id, $product_id);
			$this->add_dimension_parameters($parameters, $id, $product_id);
		}
		
		return $parameters;
	}

	/**
	 * Add summary parameter if description exists
	 *
	 * @since 3.1.0
	 * @param array $parameters Parameters array passed by reference
	 * @param int $id Current ID counter passed by reference
	 * @param object $item The item object
	 * @param int $product_id The product ID
	 */
	private function add_summary_parameter(&$parameters, &$id, $item, $product_id) {
		if (!empty($item->get_description())) {
			$parameters[$id] = [
				'id' => $id,
				'name' => 'Summary',
				'value' => $item->get_description(),
				'unit' => "",
				'itemId' => $product_id
			];
			$id++;
		}
	}

	/**
	 * Add SKU parameter if SKU exists
	 *
	 * @since 3.1.0
	 * @param array $parameters Parameters array passed by reference
	 * @param int $id Current ID counter passed by reference
	 * @param object $item The item object
	 * @param int $product_id The product ID
	 */
	private function add_sku_parameter(&$parameters, &$id, $item, $product_id) {
		if (!empty($item->get_sku())) {
			$parameters[$id] = [
				'id' => $id,
				'name' => 'SKU',
				'value' => $item->get_sku(),
				'unit' => "",
				'itemId' => $product_id
			];
			$id++;
		}
	}

	/**
	 * Add country of manufacture parameter if available
	 *
	 * @since 3.1.0
	 * @param array $parameters Parameters array passed by reference
	 * @param int $id Current ID counter passed by reference
	 * @param int $product_id The product ID
	 */
	private function add_country_of_manufacture_parameter(&$parameters, &$id, $product_id) {
		$county_of_manufacture = wc_avatax()->wc_avatax_utilities()->get_wc_meta($product_id, '_wc_avatax_county_of_manufacture', true, 'product');
		
		if ($county_of_manufacture) {
			$parameters[$id] = [
				'id' => $id,
				'name' => 'CountryOfManufacture',
				'value' => $county_of_manufacture,
				'unit' => ""
			];
			$id++;
		}
	}

	/**
	 * Add weight parameter if unit is valid
	 *
	 * @since 3.1.0
	 * @param array $parameters Parameters array passed by reference
	 * @param int $id Current ID counter passed by reference
	 * @param int $product_id The product ID
	 */
	private function add_weight_parameter(&$parameters, &$id, $product_id) {
		$weight_unit = get_option('woocommerce_weight_unit', '');
		$valid_weight_units = ['kg', 'g', 'lbs', 'oz'];
		
		if (!in_array($weight_unit, $valid_weight_units)) {
			return;
		}
		
		$weight = wc_avatax()->wc_avatax_utilities()->get_wc_meta($product_id, '_weight', true, 'product');
		
		if ($weight) {
			$parameters[$id] = [
				'id' => $id,
				'name' => 'NetWeight',
				'value' => $weight,
				'unit' => wc_avatax()->wc_avatax_utilities()->get_weight_unit($weight_unit)
			];
			$id++;
		}
	}

	/**
	 * Add dimension parameters (length, width, height) if unit is valid
	 *
	 * @since 3.1.0
	 * @param array $parameters Parameters array passed by reference
	 * @param int $id Current ID counter passed by reference
	 * @param int $product_id The product ID
	 */
	private function add_dimension_parameters(&$parameters, &$id, $product_id) {
		$dimension_unit = get_option('woocommerce_dimension_unit', '');
		$valid_dimension_units = ['m', 'cm', 'mm', 'in', 'yd'];
		
		if (!in_array($dimension_unit, $valid_dimension_units)) {
			return;
		}
		
		$mapped_dimension_unit = wc_avatax()->wc_avatax_utilities()->get_dimensions_unit($dimension_unit);
		$dimensions = [
			'_length' => 'NetLength',
			'_width' => 'Width',
			'_height' => 'Height'
		];
		
		foreach ($dimensions as $meta_key => $param_name) {
			$value = wc_avatax()->wc_avatax_utilities()->get_wc_meta($product_id, $meta_key, true, 'product');
			
			if ($value) {
				$parameters[$id] = [
					'id' => $id,
					'name' => $param_name,
					'value' => $value,
					'unit' => $mapped_dimension_unit
				];
				$id++;
			}
		}
	}

}