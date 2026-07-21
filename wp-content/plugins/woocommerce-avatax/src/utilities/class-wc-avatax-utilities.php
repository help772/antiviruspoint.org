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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_HS_API;
use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

/**
 * WooCommerce AvaTax main plugin class.
 *
 * @since 2.7.0
 */
class WC_AvaTax_Utilities {

	/* ECM Subscription names */
	const TYPE_AVATAX_ECMESSENTIALS = 'ECMEssentials';
	const TYPE_AVATAX_ECMPRO = 'ECMPro';
	const TYPE_AVATAX_ECMPREMIUM = 'ECMPremium';
	const TYPE_AVATAX_LANDED_COST = 'AvaLandedCost';
	const TYPE_AVATAX_ESTIMATED_LANDED_COST = 'AvaEstimatedLandedCost';
	const TYPE_AVATAX_CERTIFIED_DUTIES_TAXES = 'AvaCertifiedDutiesTaxes';
	const TABLE_TYPE_FLAT = 'flat';
    const TABLE_TYPE_EAV = 'eav';
    const TABLE_TYPE_VERTICAL = 'vertical';
    const ARR_ELR_DOCUMENT_TYPE = [
        'order'                         => 'ubl-invoice',
        'refund'                        => 'ubl-creditnote',
        'b2bpayment-ereporting'         => 'xml-b2bpayment-ereporting',
        'b2cpayment-ereporting'         => 'xml-b2cpayment-ereporting',
        'application_response'          => 'ubl-applicationresponse',
        'application_response_outbound' => 'ubl-applicationresponse'
    ];
	protected $MAIN_MAPPER_TABLE = "";
    protected $query ="";
    
	public function __construct(){
		global $wpdb;
		$this->MAIN_MAPPER_TABLE = $wpdb->prefix . "wc_orders";
	}
/**
     * Clears the default setting fields.
     * 
     * @since 2.8.3
     *
     */
	public function clear_default_fields() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_enable_tax_calculation','wc_avatax_record_calculations','wc_avatax_calculate_on_cart','wc_avatax_sku_as_item_code','wc_avatax_company_code','wc_avatax_company_id','wc_avatax_company_name','wc_avatax_company_response','wc_avatax_enable_ecm','wc_avatax_enable_vat','wc_avatax_enable_cross_border_classification','wc_avatax_origin_address','wc_avatax_api_product_countries_sync','wc_avatax_debug','wc_avatax_enable_address_validation','wc_avatax_landed_cost_products_with_sync_errors','wc_avatax_landed_cost_products_with_sync_resolutions','wc_avatax_shipping_code', 'wc_avatax_origin_country')" );
		
		$this->clear_transient();

		$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
		set_transient( 'wc_avatax_connection_status', 'not-connected', $cache_expiration );
	}
    /**
     * Disconnects the connection to AvaTax.
     * 
     * @since 2.7.0
     *
     */
	public function disconnect_avatax($is_update = false) {
		$integration_api = $this->get_integration_api();
		$integration_api->delete_configuration_settings();

		global $wpdb;
		if(!$is_update) {
			update_option("wc_avatax_api_environment", '' );
			update_option("wc_avatax_api_account_number", '');
			update_option("wc_avatax_api_license_key", '');
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_enable_tax_calculation','wc_avatax_record_calculations', 'wc_avatax_calculate_on_cart', 'wc_avatax_sku_as_item_code', 'wc_avatax_company_code','wc_avatax_company_id','wc_avatax_company_response','wc_avatax_company_name','wc_avatax_company_response', 'wc_avatax_enable_ecm', 'wc_avatax_certificate_submit_to_queue', 'wc_avatax_enable_vat', 'wc_avatax_enable_cross_border_classification', 'wc_avatax_origin_address', 'wc_avatax_api_product_countries_sync', 'wc_avatax_debug', 'wc_avatax_enable_address_validation', 'wc_avatax_landed_cost_products_with_sync_errors', 'wc_avatax_landed_cost_products_with_sync_resolutions', 'wc_avatax_shipping_code', 'wc_avatax_landed_cost_supported_countries', 'wc_avatax_full_nexus_details', 'wc_avatax_origin_country')" );
		}
		else {
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_api_account_number','wc_avatax_api_license_key', 'wc_avatax_company_code','wc_avatax_company_id','wc_avatax_company_name','wc_avatax_company_response', 'wc_avatax_enable_ecm', 'wc_avatax_certificate_submit_to_queue', 'wc_avatax_enable_vat', 'wc_avatax_enable_cross_border_classification', 'wc_avatax_origin_address', 'wc_avatax_api_product_countries_sync', 'wc_avatax_enable_address_validation', 'wc_avatax_landed_cost_products_with_sync_errors', 'wc_avatax_landed_cost_products_with_sync_resolutions', 'wc_avatax_landed_cost_supported_countries', 'wc_avatax_full_nexus_details', 'wc_avatax_origin_country' )" );
		}
		
		$this->clear_transient();

		$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
		set_transient( 'wc_avatax_connection_status', 'not-connected', $cache_expiration );
	}

	/**
     * Clears transient data.
     * 
     * @since 2.8.0
     *
     */
	public function clear_transient() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_avatax_%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_wc_avatax_%'" );
	}

	/**
     * Get the subscriptions for current account from AvaTax.
     * 
     * @since 2.7.0
     *
     */
	protected function get_subscriptions() {
		$subscriptions_list = [];
		$subscriptions_list = get_transient( 'wc_avatax_subscriptions_list' );
		if($subscriptions_list == null)
		{
			$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
			$subscriptions_list = wc_avatax()->get_api()->get_subscriptions()->get_subscriptions();
			set_transient( 'wc_avatax_subscriptions_list', $subscriptions_list, $cache_expiration );
		}
		return $subscriptions_list;
	}

	/**
     * Checks whether the subscription is present or not.
     * 
     * @since 2.7.0
	 * @param string[] array of subscription names
	 * @return bool
     */
	public function has_subscription(array $type ) : bool {
		$subscriptions =  wp_list_pluck( get_transient( 'wc_avatax_subscriptions_list'), 'subscriptionDescription' );
		if($subscriptions == null && wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api())
		{
			$subscriptions = $this->get_subscriptions();
			$subscriptions = wp_list_pluck( $subscriptions , 'subscriptionDescription' );
		}
		return count(array_intersect($subscriptions, $type)) ? true : false;
	}
    
	/**
	 * Checks wheter account has ECM subscription or not.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function has_ecm_subscription() {
		return $this->has_subscription( array(self::TYPE_AVATAX_ECMPRO, self::TYPE_AVATAX_ECMESSENTIALS, self::TYPE_AVATAX_ECMPREMIUM));
	}

    /**
	 * Checks wheter account has ECM subscription or not.
	 *
	 * @since 2.8.1
	 *
	 * @return bool
	 */
	public function has_landed_cost_subscription() {
		return $this->has_subscription(
			array(
					self::TYPE_AVATAX_LANDED_COST,
					self::TYPE_AVATAX_ESTIMATED_LANDED_COST,
					self::TYPE_AVATAX_CERTIFIED_DUTIES_TAXES
				));
	}

	/**
	 * Checks wheter account has ELR subscription or not.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function has_elr_subscription() {
		return true;
		//return $this->has_subscription( array(self::TYPE_AVATAX_ECMPRO, self::TYPE_AVATAX_ECMESSENTIALS, self::TYPE_AVATAX_ECMPREMIUM));
	}	/**	
	 * Gets the Nexus enabled countries.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	private function get_supported_countries() : array {
		
		$supported_countries = wc_avatax()->get_landed_cost_handler()->get_supported_countries();
		return $supported_countries;
	}

	/**
	 * Save default fields. Enables all visible settings when connecting to AvaTAx.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function save_default_fields() {

		wc_avatax()->get_company_details('defaultCompany');

		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api())
		{
			update_option('wc_avatax_default_product_code', 'P0000000');
			update_option('wc_avatax_shipping_code', 'FR');
			update_option('wc_avatax_enable_tax_calculation', 'yes');
			update_option('wc_avatax_record_calculations', 'yes');
			update_option('wc_avatax_calculate_on_cart', 'no');
			update_option('wc_avatax_sku_as_item_code', 'no');
			update_option('wc_avatax_debug', 'yes');
			update_option('wc_avatax_enable_address_validation', 'no');
			$this->save_company_default_fields();
			if(wc_avatax()->wc_avatax_utilities()->has_ecm_subscription()) {
				update_option('wc_avatax_enable_ecm', 'yes');
			}

			if($this->has_nexus_outside_countries( ["US"])){
				update_option('wc_avatax_api_product_countries_sync',  $this->get_supported_countries());
			}
		}
	}

	/**	
	 * Checks whether the nexus is outside US/Canada.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function has_nexus_outside_countries($countries = ["US","CA"]) : bool {		
		$supported_countries = $this->get_supported_countries();
		return count($supported_countries) > count(array_intersect($supported_countries, $countries)) ? true : false;
	}


	/**
	 * Save default company fields. Enables all visible settings when connecting to AvaTAx.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function save_company_default_fields()
	{
		$this->update_origin_address();

		if ($this->has_nexus_outside_countries()) {
				update_option('wc_avatax_api_product_countries_sync',  $this->get_supported_countries());
				update_option('wc_avatax_enable_vat', 'yes');
				update_option('wc_avatax_enable_cross_border_classification', 'no');
				update_option('wc_avatax_api_product_countries_sync',  $this->get_supported_countries());
				update_option('wc_avatax_landed_cost_syncing_state', 'off');
				update_option('wc_avatax_landed_cost_full_sync', 'no');
		}
	}

	/**
	 * Updates the origin address
	 * 
	 * @since 2.8.0
	 */
	public function update_origin_address(){
		$this->clear_transient();
		$response = wc_avatax()->get_api()->get_company_location();
			if ($response != null)
			{
				$applicable_address = array(
					'address_1' =>  $response->line1,
					'country'  => $response->country ,
					'state'    => $response->region ,
					'postcode' =>  $response->postalCode ,
					'city'     =>  $response->city
				);
				update_option('wc_avatax_origin_address', $applicable_address);
				$this->sync_origin_country_option();
				wc_avatax()->log("applicable_address" . json_encode(get_option('wc_avatax_origin_address')));
			}
	}

	/**
	 * Checks if HPOS feature is enabled or not.
	 *
	 * @internal
	 *
	 * @since 2.7.1
	 * 
	 */
	public function is_hpos_enabled(){
		return (class_exists( Automattic\WooCommerce\Utilities\OrderUtil::class ) && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled());
	}

	/**
	 * Gets the order meta.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @return string the found value for provided key
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function get_order_meta($order_id, $meta_key, $single = true){
		return $this->get_wc_meta($order_id, $meta_key, $single, 'order');
	}

	/**
	 * Adds the order meta data.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @param string $meta_value a metadata value
	 * @return bool 
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function add_order_meta($order_id, $meta_key, $meta_value){

		return $this->add_wc_meta($order_id, $meta_key, $meta_value, 'order');
	}

	/**
	 * Updates the order meta data.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @param string $meta_value a metadata value
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function update_order_meta($order_id, $meta_key, $meta_value){

		return $this->update_wc_meta($order_id, $meta_key, $meta_value, 'order');
	}

	/**
	 * Deletes the order meta data.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @param string $meta_value a metadata value
	 * @return bool 
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function delete_order_meta($order_id, $meta_key, $meta_value){
		return $this->delete_wc_meta($order_id, $meta_key, $meta_value, 'order');
	}


	/**
	 * Get WooCommerce object (product or order)
	 *
	 * @param int    $object_id   Object ID
	 * @param string $object_type Type of object ('order' or 'product')
	 * @return WC_Data|false
	 */
	private function get_wc_object($object_id, $object_type = 'order') 
	{
		if ($object_type === 'order') {
			return wc_get_order($object_id);
		} elseif ($object_type === 'product') {
			return wc_get_product($object_id);
		}
		return false;
	}

	/**
 * Add meta data for WooCommerce objects (products/orders)
 *
 * @param int    $object_id   Product or Order ID
 * @param string $meta_key    Meta key
 * @param mixed  $meta_value  Meta value
 * @param string $object_type Type of object ('order' or 'product')
 * @return bool
 */
public function add_wc_meta($object_id, $meta_key, $meta_value, $object_type = 'order'): bool 
{
    try {
		if ($this->is_hpos_enabled())
		{
			$object = $this->get_wc_object($object_id, $object_type);
			
			if (!$object) {
				return false;
			}

			$object->add_meta_data($meta_key, $meta_value);
			$object->save();
			
			return true;
		}
		else {
			return add_post_meta($object_id, $meta_key, $meta_value);
		}

    } catch (Exception $e) {
        wc_avatax()->log($e, "Error adding {$object_type} meta");
        return false;
    }
}

/**
 * Update meta data for WooCommerce objects
 *
 * @param int    $object_id   Product or Order ID
 * @param string $meta_key    Meta key
 * @param mixed  $meta_value  Meta value
 * @param string $object_type Type of object ('order' or 'product')
 * @return bool
 */
public function update_wc_meta($object_id, $meta_key, $meta_value, $object_type = 'order'): bool 
{
    try {
		if ($this->is_hpos_enabled())
		{
			$object = $this->get_wc_object($object_id, $object_type);
			
			if (!$object) {
				return false;
			}

			$object->update_meta_data($meta_key, $meta_value);
			$object->save();
			
			return true;
		}
		else {
			return update_post_meta($object_id, $meta_key, $meta_value);
		}

    } catch (Exception $e) {
        wc_avatax()->log($e, "Error updating {$object_type} meta");
        return false;
    }
}

/**
 * Delete meta data for WooCommerce objects
 *
 * @param int    $object_id   Product or Order ID
 * @param string $meta_key    Meta key
 * @param mixed  $meta_value  Meta value
 * @param string $object_type Type of object ('order' or 'product')
 * @return bool
 */
public function delete_wc_meta($object_id, $meta_key, $meta_value = '', $object_type = 'order'): bool 
{
    try {
		if ($this->is_hpos_enabled())
		{
			$object = $this->get_wc_object($object_id, $object_type);
			
			if (!$object) {
				return false;
			}

			$object->delete_meta_data($meta_key);
			$object->save();
			
			return true;
		}
		else {
            // Traditional post meta for non-HPOS orders and products
            return delete_post_meta($object_id, $meta_key, $meta_value);
        }

    } catch (Exception $e) {
        wc_avatax()->log($e, "Error deleting {$object_type} meta");
        return false;
    }
}

/**
 * Get meta data for WooCommerce objects (products/orders)
 *
 * @param int    $object_id   Product or Order ID
 * @param string $meta_key    Meta key
 * @param bool   $single      Whether to return a single value
 * @param string $object_type Type of object ('order' or 'product')
 * @return mixed
 */
public function get_wc_meta($object_id, $meta_key, $single = true, $object_type = 'order') 
{
    try {
		if ($this->is_hpos_enabled())
		{
			$object = $this->get_wc_object($object_id, $object_type);
			
			if (!$object) {
				return false;
			}
            return $object->get_meta($meta_key, $single);
        } else {
            return get_post_meta($object_id, $meta_key, $single);
        }

    } catch (Exception $e) {
        wc_avatax()->log($e, "Error getting {$object_type} meta");
        return null;
    }
}


	/**
	 * Gets the order type.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @return string 
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function get_post_type($order_id){
		if($this->is_hpos_enabled()) {
			return Automattic\WooCommerce\Utilities\OrderUtil::get_order_type( $order_id );
		}
		else{
			return get_post_type( $order_id );
		}
	}

	/**
	 * Hide our custom line item meta from the order admin.
	 *
	 * @internal
	 *
	 * @since 2.7.1
	 *
	 * @param array $hidden_meta The hidden line item keys.
	 * @return array $hidden_meta
	 */
	public function hide_order_item_meta( $hidden_meta ) {

		$hidden_meta[] = '_wc_avatax_code';
		$hidden_meta[] = '_wc_avatax_rate';
		$hidden_meta[] = '_wc_avatax_hs_code';
		$hidden_meta[] = '_wc_avatax_vat_code';
		$hidden_meta[] = '_is_line_exempted';

		return $hidden_meta;
	}

	/**
	 * Syncs the settings to CCS 
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	public function sync_confic_settings() {
		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api()) {
			$this->get_configuration_settings();
		}
	}

	/**
	 * Gets the integration API
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */

	public function get_integration_api($generateElrToken = false)
	{
		$api_account_number  = get_option('wc_avatax_api_account_number');
		$api_license_key     = get_option('wc_avatax_api_license_key');
		$api_environment     = get_option('wc_avatax_api_environment');
		

		return wc_avatax()->get_integration_api($api_account_number, $api_license_key, $api_environment, $generateElrToken);
	}

	/**
	 * Sends configuration setting to CUP
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	public function send_avatax_settings_to_cup($type)
	{
		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api()) {
			$integration_api = $this->get_integration_api();
			$response = $integration_api->send_settings_to_cup($type);
		}
	}

	/**
	 * Sorts a nested array by keys recursively for stable JSON comparison.
	 *
	 * @param array $array Array to sort in place.
	 * @return void
	 */
	private function ksort_recursive( array &$array )
	{
		ksort($array);
		foreach ($array as &$value) {
			if (is_array( $value)) {
				$this->ksort_recursive($value);
			}
		}
	}

	/**
	 * Builds a settings array aligned with pipeline-templates/configuration-schema.json from current WC options.
	 *
	 * @return array
	 */
	private function get_local_configuration_settings_schema_array()
	{
		$rawShipping     = get_option('wc_avatax_shipping_code', 'FR');
		$shippingCode    = is_string($rawShipping) ? trim($rawShipping) : trim((string) $rawShipping);
		if ('' === $shippingCode) {
			$shippingCode = 'FR';
		}

		return array(
			'tax_calculation'                    => array(
				'wc_avatax_enable_tax_calculation' => (get_option('wc_avatax_enable_tax_calculation') === 'yes'),
				'wc_avatax_record_calculations'   => (get_option('wc_avatax_record_calculations') === 'yes'),
				'wc_avatax_calculate_on_cart'     => (get_option('wc_avatax_calculate_on_cart') === 'yes'),
				'wc_avatax_sku_as_item_code'      => (get_option('wc_avatax_sku_as_item_code') === 'yes'),
				'wc_avatax_company_code'          => (string) get_option('wc_avatax_company_code', ''),
				'wc_avatax_company_id'            => (int) get_option('wc_avatax_company_id', 0),
				'wc_avatax_shipping_code'         => $shippingCode,
				'wc_avatax_enable_item_sync'      => (get_option('wc_avatax_landed_cost_syncing_state', 'off') === 'on'),
			),
			'address_validation'               => array(
				'wc_avatax_enable_address_validation' => (get_option('wc_avatax_enable_address_validation') === 'yes'),
			),
			'exemption_certificate_management' => array(
				'wc_avatax_enable_ecm'                   => (get_option('wc_avatax_enable_ecm') === 'yes'),
				'wc_avatax_certificate_submit_to_queue' =>
					(get_option('wc_avatax_certificate_submit_to_queue', 'no') === 'yes'),
			),
			'transactions_outside_the_us'      => array(
				'wc_avatax_enable_vat' => (get_option('wc_avatax_enable_vat') === 'yes'),
			),
			'logs'                             => array(
				'wc_avatax_debug' => (get_option('wc_avatax_debug') === 'yes'),
			),
		);
	}

	/**
	 * Maps a remote configuration object to the same schema-shaped array as
	 * get_local_configuration_settings_schema_array().
	 *
	 * @param object $configuration Configuration from the integration API.
	 * @return array
	 */
	private function normalize_remote_configuration_to_schema_array($configuration)
	{
		$tc = isset($configuration->tax_calculation) ? $configuration->tax_calculation : (object) array();
		$shippingCode = isset($tc->wc_avatax_shipping_code) ? trim((string) $tc->wc_avatax_shipping_code) : '';
		if ('' === $shippingCode) {
			$shippingCode = 'FR';
		}

		$logs = isset($configuration->logs) ? $configuration->logs : (object) array();
		$av   = isset($configuration->address_validation) ? $configuration->address_validation : (object) array();
		$ecm  = isset($configuration->exemption_certificate_management) ?
			$configuration->exemption_certificate_management :
				(object) array(
				'wc_avatax_enable_ecm' => false,
				'wc_avatax_certificate_submit_to_queue' => false,
			);
		$tx   = isset( $configuration->transactions_outside_the_us) ?
			$configuration->transactions_outside_the_us :
				(object) array(
				'wc_avatax_enable_vat' => false,
			);

		return array(
			'tax_calculation'                    => array(
				'wc_avatax_enable_tax_calculation' => ! empty($tc->wc_avatax_enable_tax_calculation),
				'wc_avatax_record_calculations'   => ! empty($tc->wc_avatax_record_calculations),
				'wc_avatax_calculate_on_cart'     => ! empty($tc->wc_avatax_calculate_on_cart),
				'wc_avatax_sku_as_item_code'      => ! empty($tc->wc_avatax_sku_as_item_code),
				'wc_avatax_company_code'          => isset($tc->wc_avatax_company_code) ?
					(string) $tc->wc_avatax_company_code : '',
				'wc_avatax_company_id'            => isset($tc->wc_avatax_company_id) ? (int) $tc->wc_avatax_company_id : 0,
				'wc_avatax_shipping_code'         => $shippingCode,
				'wc_avatax_enable_item_sync'      => ! empty($tc->wc_avatax_enable_item_sync),
			),
			'address_validation'               => array(
				'wc_avatax_enable_address_validation' => ! empty($av->wc_avatax_enable_address_validation),
			),
			'exemption_certificate_management' => array(
				'wc_avatax_enable_ecm'                   => ! empty($ecm->wc_avatax_enable_ecm),
				'wc_avatax_certificate_submit_to_queue' => isset($ecm->wc_avatax_certificate_submit_to_queue) ?
					! empty($ecm->wc_avatax_certificate_submit_to_queue) : false,
			),
			'transactions_outside_the_us'      => array(
				'wc_avatax_enable_vat' => ! empty($tx->wc_avatax_enable_vat),
			),
			'logs'                             => array(
				'wc_avatax_debug' => ! empty($logs->wc_avatax_debug),
			),
		);
	}

	/**
	 * Gets configuration setting from CUP
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	protected function get_configuration_settings()
	{
		$integration_api = $this->get_integration_api();
		$configuration = $integration_api->get_configuration_settings();
		
		if (empty((array) $configuration)) {
			$this->send_avatax_settings_to_cup('POST');
			return;
		}

		$oldSettings = $this->get_local_configuration_settings_schema_array();
		$newSettings = $this->normalize_remote_configuration_to_schema_array( $configuration );
		$this->ksort_recursive($oldSettings);
		$this->ksort_recursive($newSettings);
		$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
		if (json_encode($oldSettings, $jsonFlags) !== json_encode($newSettings, $jsonFlags)) {
			$message = json_encode(array('old' => $oldSettings,'new' => $newSettings,), $jsonFlags);
			wc_avatax()->logger()->log_event('Config updated', 'get_configuration_settings', $message);
		}
		
		$is_company_updated = $this->update_company_if_needed($configuration);
		$this->update_basic_settings($configuration);
		$this->handle_item_sync_setting($configuration, $is_company_updated);
		
		if($is_company_updated) {
			$this->send_avatax_settings_to_cup('PUT');
		} else {
			$this->update_conditional_settings($configuration);
		}
	}

	/**
	 * Updates company settings if needed
	 *
	 * @param object $configuration Configuration settings from API
	 * @return bool Whether company was updated
	 */
	private function update_company_if_needed($configuration)
	{
		if(get_option('wc_avatax_company_code') === $configuration->tax_calculation->wc_avatax_company_code) {
			return false;
		}
		
		wc_avatax()->clear_company_id_cache();
		update_option('wc_avatax_company_code', $configuration->tax_calculation->wc_avatax_company_code);
		wc_avatax()->get_company_details('companyByCode');
		
		$this->save_company_default_fields();
		wc_avatax()->get_api()->get_and_process_items();
		
		return true;
	}

	/**
	 * Updates basic settings from configuration
	 *
	 * @param object $configuration Configuration settings from API
	 */
	private function update_basic_settings($configuration)
	{
		$shipping_code = empty(trim($configuration->tax_calculation->wc_avatax_shipping_code)) ? "FR" : trim($configuration->tax_calculation->wc_avatax_shipping_code);
		
		update_option('wc_avatax_shipping_code', $shipping_code);
		update_option('wc_avatax_enable_tax_calculation', ($configuration->tax_calculation->wc_avatax_enable_tax_calculation ? 'yes' : 'no'));
		update_option('wc_avatax_record_calculations', ($configuration->tax_calculation->wc_avatax_record_calculations ? 'yes' : 'no'));
		update_option('wc_avatax_calculate_on_cart', ($configuration->tax_calculation->wc_avatax_calculate_on_cart ? 'yes' : 'no'));
		update_option('wc_avatax_sku_as_item_code', ($configuration->tax_calculation->wc_avatax_sku_as_item_code ? 'yes' : 'no'));
		update_option('wc_avatax_debug', ($configuration->logs->wc_avatax_debug ? 'yes' : 'no'));
	}

	/**
	 * Handles item sync setting
	 *
	 * @param object $configuration Configuration settings from API
	 * @param bool $is_company_updated Whether company was updated
	 */
	private function handle_item_sync_setting($configuration, $is_company_updated)
	{
		$enable_item_sync = ($configuration->tax_calculation->wc_avatax_enable_item_sync ? 'on' : 'off');
		$current_sync_state = get_option('wc_avatax_landed_cost_syncing_state');
		
		if($is_company_updated) {
			if($enable_item_sync === 'on' && $current_sync_state === 'on' ) {
				update_option('wc_avatax_landed_cost_syncing_state', 'off');
				wc_avatax()->get_landed_cost_sync_handler()->toggle_syncing();
			}
			else if($enable_item_sync === 'on' && $current_sync_state === 'off') {
				wc_avatax()->get_landed_cost_sync_handler()->toggle_syncing();
			}
			else if($enable_item_sync === 'off' && $current_sync_state === 'on') {
				wc_avatax()->get_landed_cost_sync_handler()->stop_syncing();
			}
		}

		if(!$is_company_updated && $enable_item_sync !== $current_sync_state) {
			if($enable_item_sync === 'on') {
				wc_avatax()->get_landed_cost_sync_handler()->toggle_syncing();
			} else {
				wc_avatax()->get_landed_cost_sync_handler()->stop_syncing();
			}
		}
	}

	/**
	 * Updates conditional settings based on subscriptions and nexus
	 *
	 * @param object $configuration Configuration settings from API
	 */
	private function update_conditional_settings($configuration)
	{
		if(wc_avatax()->wc_avatax_utilities()->has_ecm_subscription()) {
			update_option('wc_avatax_enable_ecm', ($configuration->exemption_certificate_management->wc_avatax_enable_ecm ? 'yes' : 'no'));
			if(isset($configuration->exemption_certificate_management->wc_avatax_certificate_submit_to_queue)) {
				update_option('wc_avatax_certificate_submit_to_queue', ($configuration->exemption_certificate_management->wc_avatax_certificate_submit_to_queue ? 'yes' : 'no'));
			}
		}

		if($this->has_nexus_outside_countries(["US"])) {
			update_option('wc_avatax_enable_vat', ($configuration->transactions_outside_the_us->wc_avatax_enable_vat ? 'yes' : 'no'));
		}

		if (isset($configuration->address_validation->wc_avatax_enable_address_validation)) {
			$enabled = $configuration->address_validation->wc_avatax_enable_address_validation;
			update_option('wc_avatax_enable_address_validation', ($enabled ? 'yes' : 'no'));
		}
	}

	/**
	 * Gets the error message from address request response.
	 * 
	 * @since 2.7.1
	 *
	 */
	public function get_address_error_messages($response){
		$message = '';
		foreach($response->messages as $msg){
			
			$message = $message . '<div class="wc-avatax-address-validation-result wc-avatax-address-validation-error">' . $msg->summary . '</div></br>';
		}
		return $message;
	}
	
	public function add_checkout_messages(){
		if ( ! empty( WC()->cart->avatax_messages ) && is_array( WC()->cart->avatax_messages ) ) {

			$has_missing_hs_code_warnings = false;

			foreach ( WC()->cart->avatax_messages as $message ) {

				if ( ! empty( $message->summary ) && ! empty( $message->refersTo ) && 'LandedCost' === $message->refersTo ) {
					return '<p class="wc-avatax-message">' . esc_html( $message->summary ) . '</p>';
				} elseif ( 'MissingHSCodeWarning' === $message->summary ) {
					$has_missing_hs_code_warnings = true;
				}
			}
			foreach ( WC()->cart->avatax_invoice_messages as $message ) {

				if ( ! empty( $message->content && 'No applicable messaging for this line.' != $message->content) ) {
					return '<p class="wc-avatax-message">' . esc_html( $message->content ) . '</p>';
				}
			}

			if ( $has_missing_hs_code_warnings ) {

				$country_code = WC()->customer->get_shipping_country();
				$country = ( new WC_Countries() )->get_countries()[$country_code] ?? $country_code;

				/* translators: Placeholders: %s - country name */
				return '<p class="wc-avatax-message">' . sprintf( esc_html__( "We cannot calculate import duties for %s for some of the products in your cart. By placing the order, you'll need to settle any applicable customs duties and fees with the shipment carrier. You can also contact us to complete your order.", 'woocommerce-avatax' ), $country ) . '</p>';
			}
		}
	}

	/**
	 * Calculate the time difference between two microtime values.
	 *
	 * @since 2.8.1
	 * 
	 * @param float|null $start The starting microtime value
	 * @param float|null $end   The ending microtime value (defaults to current microtime if null)
	 * 
	 * @return float Time difference in seconds with microsecond precision
	 *               Returns 0.0 if start time is not provided
	 */
	public function microtime_diff($start, $end = null)
	{
		if(!$start){
			return 0.0;
		}

		if (!$end) {
			$end = microtime();
		}

		return ($end - $start)/1e+6;
	}

	
	/**
	 * Get an array of European countries.
	 *
	 * @since 2.8.1
	 * 
	 * This function returns an array of European countries based on the installed
	 * version of WooCommerce.
	 *
	 * For WooCommerce versions prior to 4.0.0, it returns the list of countries
	 * that are part of the European Union, specifically for VAT (Value Added Tax)
	 * purposes.
	 *
	 * For WooCommerce versions 4.0.0 and later, it returns the list of all
	 * countries that have VAT regulations.
	 *
	 * @return array An array of European countries or VAT countries.
	 */
	public function get_european_countries()
	{
		return Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt('4.0.0')
			? WC()->countries->get_european_union_countries('eu_vat')
			: WC()->countries->get_vat_countries();
	}

    /**
     * Determines whether a transaction is US Inbound (shipping from outside the US to inside the US).
     *
     * @param array $addresses AvaTax-formatted addresses with 'shipFrom' and 'shipTo' keys.
     * @return bool
     * @since 3.8.1
     *
     */
    public function is_us_inbound(array $addresses): bool
    {

        if (empty($addresses['shipFrom']['country']) || empty($addresses['shipTo']['country'])) {
            return false;
        }

        return $addresses['shipFrom']['country'] !== 'US'
            && $addresses['shipTo']['country'] === 'US';
    }

	/**
	 * Fixed material unit slugs for Section 232 line parameters (API `unit` value).
	 *
	 * @since 3.8.1
	 *
	 * @return array<string, string> slug => admin label
	 */
	public function get_section232_material_units() : array {

		return array(
			'steel'     => __( 'Steel', 'woocommerce-avatax' ),
			'copper'    => __( 'Copper', 'woocommerce-avatax' ),
			'aluminium' => __( 'Aluminium', 'woocommerce-avatax' ),
			'lumber'    => __( 'Lumber', 'woocommerce-avatax' ),
		);
	}

	/**
	 * Parse and validate Section 232 metal rows from product save POST.
	 *
	 * @since 3.8.1
	 *
	 * @param array $post $_POST-like array.
	 * @return array<int, array{value: string, unit: string, country: string}>|\WP_Error
	 */
	public function parse_section232_metal_from_post( array $post ) {

		if ( ! isset( $post['_wc_avatax_section232_value'] ) ) {
			return array();
		}

		$values    = array_map( 'wp_unslash', (array) $post['_wc_avatax_section232_value'] );
		$units     = isset( $post['_wc_avatax_section232_unit'] ) ? array_map( 'wp_unslash', (array) $post['_wc_avatax_section232_unit'] ) : array();
		$countries = isset( $post['_wc_avatax_section232_country'] ) ? array_map( 'wp_unslash', (array) $post['_wc_avatax_section232_country'] ) : array();

		$allowed_units = array_keys( $this->get_section232_material_units() );
		$wc_countries  = WC()->countries->get_countries();
		$rows          = array();
		$units_seen    = array();
		$sum           = 0.0;

		$count = max( count( $values ), count( $units ), count( $countries ) );

		for ( $i = 0; $i < $count; $i++ ) {
			$v = isset( $values[ $i ] ) ? trim( (string) $values[ $i ] ) : '';
			$u = isset( $units[ $i ] ) ? strtolower( trim( (string) $units[ $i ] ) ) : '';
			$c = isset( $countries[ $i ] ) ? strtoupper( trim( (string) $countries[ $i ] ) ) : '';

			if ( '' === $v && '' === $u && '' === $c ) {
				continue;
			}

			if ( '' === $v || ! is_numeric( $v ) ) {
				return new \WP_Error(
					'wc_avatax_section232_value',
					__( 'Each Section 232 metal row must include a numeric value (%).', 'woocommerce-avatax' )
				);
			}

			$vf = (float) $v;
			if ( $vf <= 0 || $vf > 100 ) {
				return new \WP_Error(
					'wc_avatax_section232_range',
					__( 'Section 232 metal value (%) must be greater than 0 and at most 100.', 'woocommerce-avatax' )
				);
			}

			if ( '' === $u || ! in_array( $u, $allowed_units, true ) ) {
				return new \WP_Error(
					'wc_avatax_section232_unit',
					__( 'Each Section 232 metal row must select a material unit.', 'woocommerce-avatax' )
				);
			}

			if ( isset( $units_seen[ $u ] ) ) {
				return new \WP_Error(
					'wc_avatax_section232_unit_dup',
					__( 'Each Section 232 material unit can only be used once.', 'woocommerce-avatax' )
				);
			}
			$units_seen[ $u ] = true;

			if ( '' !== $c && ! array_key_exists( $c, $wc_countries ) ) {
				return new \WP_Error(
					'wc_avatax_section232_country',
					__( 'Section 232 material country of origin is not a valid country code.', 'woocommerce-avatax' )
				);
			}

			$sum += $vf;
			$rows[] = array(
				'value'   => wc_format_decimal( $vf, 4, true ),
				'unit'    => $u,
				'country' => $c,
			);
		}

		if ( round( $sum, 6 ) > 100 ) {
			return new \WP_Error(
				'wc_avatax_section232_sum',
				__( 'The sum of all Section 232 metal values must be at most 100%.', 'woocommerce-avatax' )
			);
		}

		return $rows;
	}

	/**
	 * Whether Section 232 metal fields / line parameters apply (physical goods only).
	 *
	 * @since 3.8.1
	 *
	 * @param \WC_Product $product Product or variation.
	 * @return bool False when product is virtual and/or downloadable.
	 */
	public function is_section232_metal_enabled_for_product( WC_Product $product ) : bool {

		// Must match the Product data header checkboxes (html-product-data-panel.php uses is_virtual /
		// is_downloadable). Using get_virtual( 'edit' ) alone can disagree when a plugin filters
		// woocommerce_is_virtual / woocommerce_is_downloadable so the box is unchecked while meta is still "yes".
		return ! ( $product->is_virtual() || $product->is_downloadable() );
	}

	/**
	 * Stored Section 232 metal rows for a product (for AvaTax line parameters).
	 *
	 * @since 3.8.1
	 *
	 * @param \WC_Product $product Product or variation.
	 * @return array<int, array{value: string, unit: string, country: string}>
	 */
	public function get_section232_metal_percent_entries( WC_Product $product ) : array {

		if ( ! $this->is_section232_metal_enabled_for_product( $product ) ) {
			return array();
		}

		$raw = $this->get_wc_meta( $product->get_id(), '_wc_avatax_section232_metal_percent', true, 'product' );

		if ( ! is_array( $raw ) && is_string( $raw ) ) {
			$decoded = json_decode( $raw, true );
			$raw     = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $raw ) ) {
			$raw = array();
		}

		if ( empty( $raw ) && $product->is_type( 'variation' ) ) {
			$parent = wc_get_product( $product->get_parent_id() );
			if ( $parent instanceof WC_Product ) {
				return $this->get_section232_metal_percent_entries( $parent );
			}
		}

		$allowed_units = array_keys( $this->get_section232_material_units() );
		$wc_countries  = WC()->countries->get_countries();
		$out           = array();

		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$v = isset( $row['value'] ) ? (string) $row['value'] : '';
			$u = isset( $row['unit'] ) ? strtolower( trim( (string) $row['unit'] ) ) : '';
			$c = isset( $row['country'] ) ? strtoupper( trim( (string) $row['country'] ) ) : '';

			if ( '' === $v || ! is_numeric( $v ) ) {
				continue;
			}
			$vf = (float) $v;
			if ( $vf <= 0 || $vf > 100 || ! in_array( $u, $allowed_units, true ) ) {
				continue;
			}
			if ( '' !== $c && ! array_key_exists( $c, $wc_countries ) ) {
				continue;
			}
			$out[] = array(
				'value'   => wc_format_decimal( $vf, 4, true ),
				'unit'    => $u,
				'country' => $c,
			);
		}

		return $out;
	}

	/**
	 * Get the latest selected category for the product
	 *
	 * @param WC_Product $product
	 * @return string
	 */
		/**
	 * Get the latest selected category for the product
	 *
	 * @param WC_Product $product
	 * @return string
	 */
	public function get_product_category($product) {
		$terms = wp_get_object_terms($product->get_id(), 'product_cat', array(
			'orderby' => 'term_id',
			'order' => 'DESC',
			'limit' => 1
		));
		
		if (!empty($terms) && !is_wp_error($terms)) {
			// Get the last category (most recent)
			$category = $terms[0];

			if ($category->name === 'Uncategorized') {
				return 'Uncategorized';
			}
			
			// Initialize category path array with current category
			$category_path = array($category->name);
			// Add safety counter to prevent infinite loops
			$safety_counter = 0;
			$max_iterations = 10; // Maximum depth of category hierarchy

			// Build the path from current category up through its parents
			$current_cat = $category;
			while ($current_cat && $safety_counter < $max_iterations) {
				// Skip the first iteration since we already added the current category
				if ($current_cat->parent) {
					// Get parent category
					$parent_cat = get_term($current_cat->parent, 'product_cat');
				
					// Break if there's an error or we can't get the parent
					if (is_wp_error($parent_cat) || !$parent_cat) {
						break;
					}
				
					// Add parent to the beginning of the array
					array_unshift($category_path, $parent_cat->name);
				
					$current_cat = $parent_cat;
				} else {
					break;
				}
			
				$safety_counter++;
			}

			// Join the path with ' - ' separator
			return !empty($category_path) ? implode(' - ', $category_path) : 'Uncategorized';
				}
		
		return 'Uncategorized';
	}

		/**
 * Get HS code for product based on destination country
 *
 * @param WC_Product $product
 * @param string $destination_country
 * @return string
 */
public function get_product_hs_code($product, $destination_country) {
    // Get HS code for specific country
    $hs_code = get_post_meta($product->get_id(), '_wc_avatax_hs_' . $destination_country, true);
    
    // If no HS code found for country, get the product category
    if (empty($hs_code)) {
        $hs_code = '';
    }
    
    return $hs_code;
}

/**
	 * Get country of manufacture with validation
	 *
	 * @param WC_Product $product Product object
	 * @return string
	 */
	public function get_country_of_manufacture(WC_Product $product): string 
	{
		$country = wc_avatax()->wc_avatax_utilities()->get_wc_meta($product->get_id(), '_wc_avatax_county_of_manufacture', true, 'product');
		
		// Validate country code
		if (!empty($country) && array_key_exists($country, WC()->countries->get_countries())) {
			return $country;
		}

		return apply_filters('wc_avatax_default_country_of_manufacture', 'US');
	}


	/**
	 * Get weight unit
	 *
	 * @return string
	 */
	public function get_weight_unit($unit): string 
	{
		$unit_mapping = [
			'kg' => 'Kilogram',
			'g'  => 'Gram',
			'lbs' => 'Pound',
			'oz' => 'Ounce'
		];

		return $unit_mapping[$unit] ?? '';
	}

	/**
	 * Get weight unit
	 *
	 * @return string
	 */
	public function get_dimensions_unit($unit_dimension): string 
	{
		$unit_mapping_dimension = [
			'm' => 'Meter',
			'cm'  => 'Centimeter',
			'mm' => 'Millimeter',
			'in' => 'Inch',
			'yd' => 'Yard'
		];

		return $unit_mapping_dimension[$unit_dimension] ?? '';
	}

	/**
	 * Clear all AvaTax transients.
	 * 
	 * @since 3.0.0
	 * @return bool
	 */
	public function clear_all_avatax_transients() {
		global $wpdb;
		
		$result = $wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE ((option_name LIKE '_transient_wc_avatax_%' OR option_name LIKE '_transient_timeout_wc_avatax_%') AND option_name NOT LIKE '%wc_avatax_connection_status%' AND option_name NOT LIKE '%wc_avatax_elr_connection_status%') OR option_name in ('wc_avatax_entity_use_codes', 'wc_avatax_landed_cost_supported_countries', 'wc_avatax_full_nexus_details')"
		);
		
		return $result !== false;
	}

	/**
	 * Creates the reconciliation batches table if it does not exist.
	 *
	 * @since 3.8.0
	 */
	public function maybe_create_reconciliation_batches_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wc_avatax_reconciliation_batches';

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name uses prefix
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;

		if ( ! $table_exists ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				session_id varchar(64) NOT NULL,
				missing_order_ids longtext NULL,
				mismatch_order_ids longtext NULL,
				from_date date NULL,
				to_date date NULL,
				document_type varchar(32) NULL,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				orders_in_batch int(11) unsigned NOT NULL DEFAULT 0,
				avalara_in_batch int(11) unsigned NOT NULL DEFAULT 0,
				status varchar(32) NOT NULL,
				PRIMARY KEY  (session_id)
			) $charset_collate;";


			if ( defined( 'WC_ABSPATH' ) ) {
				require_once ABSPATH . '/wp-admin/includes/upgrade.php';
			}
			dbDelta( $sql );

			wc_avatax()->log( 'Created table: ' . $table_name );
		}
	}
/**
 * Persists the origin country from wc_avatax_origin_address into its own option.
 *
 * @since 3.8.3
 */
public function sync_origin_country_option() {
    $origin_address = get_option( 'wc_avatax_origin_address', [] );
    $country = '';

    if ( is_array( $origin_address ) && ! empty( $origin_address['country'] ) ) {
        $country = strtoupper( sanitize_text_field( $origin_address['country'] ) );
    }
    update_option( 'wc_avatax_origin_country', $country );
}
}


