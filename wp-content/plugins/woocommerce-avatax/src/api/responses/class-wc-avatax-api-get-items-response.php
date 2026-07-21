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
class WC_AvaTax_API_Get_Items_Response extends \WC_AvaTax_API_Response {
	/** @var string pending classification status */
	const PRODUCT_SYNC_STATUS = 'Error';

    /**
	 * Gets the status.
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	public function get_and_process_items() {
        // initialize an empty result array
        $result = [];
        if(!$this->response_data || !isset($this->response_data->value)) {
            return [];
        }
        
        $result = $this->response_data->value;

        foreach ( $result  as $item ) {
			if (isset($item->id, $item->itemCode)) {
				$product = wc_get_product($item->itemCode);
                if($product) {
                    $avatax_item_ids = (array) $product->get_meta('_wc_avatax_product_ids');
                    $avatax_item_ids[wc_avatax()->get_company_id()] = $item->id;
                    $product->update_meta_data('_wc_avatax_product_ids', $avatax_item_ids);
                    $product->save();
                }
			}
		}

        $nextLink = $this->get_next_link($this->response_data);

        // If / while nextLink is present in response, enrich the result[]
        while(!empty($nextLink)) {
            $token_array = explode("/v2", $nextLink);
            $paginated_url = end($token_array);
            $paginated_response = wc_avatax()->get_api()->get_and_process_items($paginated_url);
            
            $nextLink = $this -> get_next_link($paginated_response -> response_data);
        }
	}

    public function processItems(){
        $this->get_and_process_items();
    }

    /**
	 * Provides nextLink if available in response, if not then ''
	 *
	 * @param mixed $response_data
	 * @return string
	 */
	protected function get_next_link($response_data) {
		if (isset($response_data ->{'@nextLink'})) {
			return $response_data ->{'@nextLink'};
		}
		return '';
	}
}
