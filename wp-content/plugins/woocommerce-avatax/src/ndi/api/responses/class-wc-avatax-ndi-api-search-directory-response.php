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
 * The NDI API Search Directory response class.
 *
 * @since 3.6.0
 */
class WC_AvaTax_NDI_API_Search_Directory_Response extends \WC_AvaTax_NDI_API_Response {

    /**
     * Gets the search directory results.
     *
     * @since 3.6.0
     *
     * @return array The search results in the expected frontend format.
     */
    public function get_search_results() {
        // Handle new combined API response structure
        $response_data = $this->response_data;
        $raw_data = array();
        
        // Extract search results from the new structure
        // Handle both object and array response formats
        if (is_object($response_data) && isset($response_data->searchResult) && isset($response_data->searchResult->value)) {
            $raw_data = $response_data->searchResult->value;
        } elseif (is_array($response_data) && isset($response_data['searchResult']) && isset($response_data['searchResult']['value'])) {
            $raw_data = $response_data['searchResult']['value'];
        } elseif (is_object($response_data) && isset($response_data->value)) {
            // Fallback for old structure (object)
            $raw_data = $response_data->value;
        } elseif (is_array($response_data) && isset($response_data['value'])) {
            // Fallback for old structure (array)
            $raw_data = $response_data['value'];
        }
        
        $results = array();

        foreach ($raw_data as $item) {
            // Convert array to object for consistent handling
            if (is_array($item)) {
                $item = (object) $item;
            }
            
            $company_name = $this->extract_company_name($item);
            $network = isset($item->network) ? $item->network : '';
            $primary_identifier = $this->extract_primary_identifier($item);
            $country = $this->extract_country($item);
            $registration_date = isset($item->registrationDate) ? $item->registrationDate : '';

            $results[] = array(
                'id' => isset($item->id) ? $item->id : '',
                'name' => $company_name,
                'network' => $network,
                'registrationDate' => $registration_date,
                'identifiers' => isset($item->identifiers) ? $item->identifiers : array(),
                'addresses' => isset($item->addresses) ? $item->addresses : array(),
                'supportedDocumentTypes' => isset($item->supportedDocumentTypes) ? $item->supportedDocumentTypes : array(),
                'consents' => isset($item->consents) ? $item->consents : new stdClass(),
                'extensions' => isset($item->extensions) ? $item->extensions : array(),
           );
        }

        return $results;
    }

    /**
     * Extracts the best available company name from the item.
     *
     * @since 3.6.0
     *
     * @param object $item The directory item.
     * @return string The company name.
     */
    private function extract_company_name($item) {
        // Handle both array and object formats
        $name = '';
        if (is_object($item) && isset($item->name)) {
            $name = trim($item->name);
        } elseif (is_array($item) && isset($item['name'])) {
            $name = trim($item['name']);
        }
        
        // If name is empty or just whitespace, try to get English name from extensions
        if (empty($name)) {
            $extensions = null;
            if (is_object($item) && isset($item->extensions)) {
                $extensions = $item->extensions;
            } elseif (is_array($item) && isset($item['extensions'])) {
                $extensions = $item['extensions'];
            }
            
            if (is_array($extensions)) {
                foreach ($extensions as $extension) {
                    $extension_obj = is_array($extension) ? (object) $extension : $extension;
                    if (isset($extension_obj->key) && $extension_obj->key === 'tradingPartnerInEnglish') {
                        if (isset($extension_obj->values) && is_array($extension_obj->values) && count($extension_obj->values) > 0) {
                            $name = trim($extension_obj->values[0]);
                            break;
                        }
                    }
                }
            }
        }
        
        // If still empty, use a placeholder
        if (empty($name)) {
            $name = '[No Name Available]';
        }
        
        return $name;
    }

    /**
     * Extracts the primary identifier from the identifiers array.
     *
     * @since 3.6.0
     *
     * @param object $item The directory item.
     * @return string The primary identifier value.
     */
    private function extract_primary_identifier($item) {
        $identifiers = null;
        if (is_object($item) && isset($item->identifiers)) {
            $identifiers = $item->identifiers;
        } elseif (is_array($item) && isset($item['identifiers'])) {
            $identifiers = $item['identifiers'];
        }
        
        if (! is_array($identifiers) || count($identifiers) === 0) {
            return '';
        }

        // Return the first identifier's value
        $first_identifier = $identifiers[0];
        if (is_array($first_identifier) && isset($first_identifier['value'])) {
            return $first_identifier['value'];
        } elseif (is_object($first_identifier) && isset($first_identifier->value)) {
            return $first_identifier->value;
        }
        
        return '';
    }

    /**
     * Extracts the country from the addresses array.
     *
     * @since 3.6.0
     *
     * @param object $item The directory item.
     * @return string The country name.
     */
    private function extract_country($item) {
        $addresses = null;
        if (is_object($item) && isset($item->addresses)) {
            $addresses = $item->addresses;
        } elseif (is_array($item) && isset($item['addresses'])) {
            $addresses = $item['addresses'];
        }
        
        if (! is_array($addresses) || count($addresses) === 0) {
            return '';
        }

        $first_address = $addresses[0];
        if (is_array($first_address) && isset($first_address['country'])) {
            return $first_address['country'];
        } elseif (is_object($first_address) && isset($first_address->country)) {
            return $first_address->country;
        }
        
        return '';
    }

    /**
     * Gets the pagination information.
     *
     * @since 3.6.0
     *
     * @return array Pagination details.
     */
    public function get_pagination_info() {
        $total_records = $this->get_record_set_count();
        $next_link = $this->get_next_link();
        
        return array(
            'total' => $total_records,
            'has_next' => ! empty($next_link),
            'next_link' => $next_link,
       );
    }

    /**
     * Transforms the API response into the expected frontend format.
     *
     * @since 3.6.0
     *
     * @param int $page Current page number.
     * @param int $per_page Records per page.
     *
     * @return array Formatted response for the frontend.
     */
    public function get_formatted_response($page = 1, $per_page = 10) {
        $results = $this->get_search_results();
        $pagination = $this->get_pagination_info();
        $facets = $this->get_facets();
        
        $total_pages = $pagination['total'] > 0 ? ceil($pagination['total'] / $per_page) : 1;

        $response = array(
            'success' => true,
            'data' => array(
                '@recordSetCount' => $pagination['total'],
                '@nextLink' => $pagination['next_link'],
                'value' => $results,
            'pagination' => array(
                    'current_page' => $page,
                'per_page' => $per_page,
                    'total_results' => $pagination['total'],
                    'total_pages' => $total_pages
               )
           )
       );
        
        // Include facets if available
        if (! empty($facets)) {
            $response['facets'] = $facets;
        }
        
        return $response;
    }

    /**
     * Gets the facets from the API response.
     *
     * @since 3.6.0
     *
     * @return array The facets data.
     */
    public function get_facets() {
        $response_data = $this->response_data;
        
        // Extract facets from the new combined response structure (object format)
        if (is_object($response_data) && isset($response_data->facetsResult) && is_array($response_data->facetsResult)) {
            return $response_data->facetsResult;
        }
        
        // Extract facets from the new combined response structure (array format)
        if (is_array($response_data) && isset($response_data['facetsResult']) && is_array($response_data['facetsResult'])) {
            return $response_data['facetsResult'];
        }
        
        return array();
    }

    /**
     * Gets the record set count from the new combined API response.
     *
     * @since 3.6.0
     *
     * @return int The total record count.
     */
    public function get_record_set_count() {
        $response_data = $this->response_data;
        
        // Handle new combined response structure (object format)
        if (is_object($response_data) && isset($response_data->searchResult) && isset($response_data->searchResult->recordSetCount)) {
            return (int) $response_data->searchResult->recordSetCount;
        }
        
        // Handle new combined response structure (array format)
        if (is_array($response_data) && isset($response_data['searchResult']) && isset($response_data['searchResult']['recordSetCount'])) {
            return (int) $response_data['searchResult']['recordSetCount'];
        }
        
        // Fallback to old structure (with @ sign) - object format
        if (is_object($response_data) && isset($response_data->{'@recordSetCount'})) {
            return (int) $response_data->{'@recordSetCount'};
        }
        
        // Fallback to old structure (with @ sign) - array format
        if (is_array($response_data) && isset($response_data['@recordSetCount'])) {
            return (int) $response_data['@recordSetCount'];
        }
        
        // Fallback to new structure (without @ sign) - object format
        if (is_object($response_data) && isset($response_data->recordSetCount)) {
            return (int) $response_data->recordSetCount;
        }
        
        // Fallback to new structure (without @ sign) - array format
        if (is_array($response_data) && isset($response_data['recordSetCount'])) {
            return (int) $response_data['recordSetCount'];
        }
        
        return 0;
    }

    /**
     * Gets the next link from the new combined API response.
     *
     * @since 3.6.0
     *
     * @return string The next link URL.
     */
    public function get_next_link() {
        $response_data = $this->response_data;
        
        // Handle new combined response structure (object format)
        if (is_object($response_data) && isset($response_data->searchResult) && isset($response_data->searchResult->nextLink)) {
            return $response_data->searchResult->nextLink;
        }
        
        // Handle new combined response structure (array format)
        if (is_array($response_data) && isset($response_data['searchResult']) && isset($response_data['searchResult']['nextLink'])) {
            return $response_data['searchResult']['nextLink'];
        }
        
        // Fallback to old structure (with @ sign) - object format
        if (is_object($response_data) && isset($response_data->{'@nextLink'})) {
            return $response_data->{'@nextLink'};
        }
        
        // Fallback to old structure (with @ sign) - array format
        if (is_array($response_data) && isset($response_data['@nextLink'])) {
            return $response_data['@nextLink'];
        }
        
        // Fallback to new structure (without @ sign) - object format
        if (is_object($response_data) && isset($response_data->nextLink)) {
            return $response_data->nextLink;
        }
        
        // Fallback to new structure (without @ sign) - array format
        if (is_array($response_data) && isset($response_data['nextLink'])) {
            return $response_data['nextLink'];
        }
        
        return '';
    }
}

