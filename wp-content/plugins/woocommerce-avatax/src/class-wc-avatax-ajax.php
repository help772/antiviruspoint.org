<?php
// nosemgrep: audit.php.lang.security.file.read-write-delete
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
 * Handle the AJAX-specific functionality.
 *
 * @since 1.0.0
 */
class WC_AvaTax_AJAX
{


	/** @var bool $reload_order_notes_after_calculating_taxes whether order notes should be reloaded after calculating order taxes in admin */
	protected $reload_order_notes_after_calculating_taxes = false;


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{

		$this->add_hooks();
	}


	/**
	 * Adds handler actions and filters.
	 *
	 * @since 1.13.0
	 */
	protected function add_hooks()
	{

		// validate the Origin Address settings fields
		add_action('wp_ajax_wc_avatax_validate_origin_address', [$this, 'validate_origin_address']);

		// validate the customer address at checkout
		add_action('wp_ajax_wc_avatax_validate_customer_address', [$this, 'validate_customer_address']);
		add_action('wp_ajax_nopriv_wc_avatax_validate_customer_address', [$this, 'validate_customer_address']);
		add_action('wp_ajax_wc_avatax_revalidate_customer_address_on_addresschange', [$this, 'revalidate_customer_address']);

		// Cross Border ajax callback methods
		add_action('wp_ajax_wc_avatax_resync_error_products', [$this, 'resync_products_with_errors']);

		add_action('wp_ajax_wc_avatax_sync_tax_codes', [$this, 'tax_code_sync']);
		add_action('wp_ajax_wc_avatax_tax_code_lookp', [$this, 'tax_code_lookup']);

		// display and save the product variation tax code field
		add_action('woocommerce_product_after_variable_attributes', [$this, 'display_product_variation_code_fields'], 15, 3);
		add_action('woocommerce_save_product_variation', [$this, 'save_product_variation_code_fields']);

		// save the product tax code quick edit field
		add_action('woocommerce_product_quick_edit_save', [$this, 'save_product_tax_code_quick_edit']);

		// save the tax code field when a new product category is created
		add_action('created_product_cat', [$this, 'save_category_code_fields'], 10, 2);

		// add estimated AvaTax calculations to orders when "Calculate Taxes" is run from the admin
		add_action('woocommerce_saved_order_items', [$this, 'estimate_order_tax']);

		// check for Landed Cost warnings after calculating taxes in admin and possibly reload order notes
		add_action('wc_avatax_after_order_tax_calculated', [$this, 'check_for_landed_cost_warnings'], 10, 2);
		add_action('woocommerce_order_item_add_action_buttons', [$this, 'maybe_trigger_order_notes_reload']);
		add_action('wp_ajax_wc_avatax_get_order_notes', [$this, 'get_order_notes']);
		add_action('wp_ajax_wc_avatax_get_ecommerce_token', [$this, 'get_ecommerce_token']);
		add_action('wp_ajax_wc_avatax_submit_map_perform', [$this, 'submit_map_perform']);
		add_action('wp_ajax_wc_avatax_download_elr_document', [$this, 'handle_elr_document_download']);

		add_action('wp_ajax_wc_avatax_download_certificate', [$this, 'download_certificate']);
		add_action('wp_ajax_wc_avatax_invite_customer_certificate', [$this, 'invite_customer_certificate']);
		add_action('wp_ajax_wc_avatax_unlink_customer_certificate', [$this, 'unlink_customer_certificate']);
		add_action('wp_ajax_wc_avatax_manage_certificate_link', [$this, 'manage_certificate_link']);
		add_action('wp_ajax_wc_avatax_update_alternate_id', [$this, 'update_alternate_id']);
		add_action('wp_ajax_wc_avatax_update_caching_transient_for_customer', [$this, 'update_caching_transient_for_customer']);

		add_action('wp_ajax_wc_avatax_disconnect', [$this, 'disconnect_avatax']);
		add_action('wp_ajax_wc_avatax_update_connection', [$this, 'update_connection']);
		add_action('wp_ajax_wc_avatax_refresh_config', [$this, 'refresh_config']);

		//Hide our custom line item meta from the order admin
		add_filter('woocommerce_hidden_order_itemmeta', array($this, 'hide_order_item_meta'));

		add_action('wp_ajax_wc_avatax_elr_disconnect', [$this, 'disconnect_elr']);
		add_action('wp_ajax_wc_avatax_elr_save_custom_fields', [$this, 'save_custom_fields']);
		add_action('wp_ajax_wc_avatax_refresh_elr_status', [$this, 'refresh_elr_status']);
        add_action('wp_ajax_wc_avatax_run_application_response_sync', [$this, 'run_application_response_sync']);
        add_action('wp_ajax_wc_avatax_send_refund_to_avalara', [$this, 'send_refund_to_avalara']);
		add_action('wp_ajax_wc_avatax_send_order_to_avalara', [$this, 'send_order_to_avalara']);
		add_action('wp_ajax_wc_avatax_report_order_payment_to_avalara', [$this, 'report_order_payment_to_avalara']);
		add_action('wp_ajax_wc_avatax_report_refund_payment_to_avalara', [$this, 'report_refund_payment_to_avalara']);
		add_action('wp_ajax_wc_avatax_refresh_ar_outbound_status', [$this, 'refresh_ar_outbound_status']);
		add_action('wp_ajax_wc_avatax_clear_transients', [$this, 'clear_avatax_transients']);
		add_action('wp_ajax_wc_avatax_clear_entitycodes', [$this, 'clear_avatax_entitycodes']);
		add_action('wp_ajax_wc_avatax_clear_nexuslist', [$this, 'clear_avatax_nexuslist']);
		add_action('wp_ajax_wc_avatax_update_origin_address', [$this, 'update_origin_address']);

	}

	/**
	 * Checks for landed cost warnings in the tax calculation response and sets a flag for later use.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @param int $order_id order ID (unused)
	 * @param WC_AvaTax_API_Tax_Response $response tax calculation response object
	 * @return void
	 */
	public function check_for_landed_cost_warnings($order_id, $response)
	{

		foreach ($response->get_messages() as $message) {

			if ('MissingHSCodeWarning' === $message->summary) {

				$this->reload_order_notes_after_calculating_taxes = true;
				break;
			}
		}
	}


	/**
	 * Triggers admin JS to reload order notes.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @return void
	 */
	public function maybe_trigger_order_notes_reload()
	{

		if ($this->reload_order_notes_after_calculating_taxes) {
			echo '<script>window.wc_avatax_admin.reload_order_notes("' . esc_js(wp_create_nonce('wc_avatax_get_order_notes')) . '")</script>';
		}
	}


	/**
	 * Gets the order notes & sends an AJAX success response with teh rendered notes HTML.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @see \WC_AJAX::save_order_items() - based on this method
	 *
	 * @return void
	 */
	public function get_order_notes()
	{

		check_ajax_referer('wc_avatax_get_order_notes', 'security');

		if (!isset($_REQUEST['order_id']) || !current_user_can('edit_shop_orders')) {
			wp_die(-1);
		}

		wp_send_json_success(['notes_html' => $this->get_order_notes_html(absint($_REQUEST['order_id']))]);
	}


	/**
	 * Gets the order notes HTML for the given order ID.
	 *
	 * @since 1.16.0
	 *
	 * @param int $order_id
	 * @return false|string
	 */
	protected function get_order_notes_html(int $order_id)
	{

		ob_start();
		$notes = wc_get_order_notes(['order_id' => $order_id]);

		if (defined('WC_ABSPATH')) {
			include_once WC_ABSPATH . '/includes/admin/meta-boxes/views/html-order-notes.php';
		}

		return ob_get_clean();
	}


	/**
	 * Validate the Origin Address settings fields.
	 *
	 * @since 1.0.0
	 */
	public function validate_origin_address()
	{

		//Performance log variables
		$execution_start = hrtime(true);
		$api_time = $execution_end = 0.0;
		$response_string = "";

		// No nonce? No go
		check_ajax_referer('wc_avatax_validate_origin_address', 'nonce');

		try {

			/**
			 * Fire before validating the origin address.
			 *
			 * @since 1.0.0
			 */
			do_action('wc_avatax_before_origin_address_validated');

			$response = wc_avatax()->get_api()->validate_address(array(
				'address_1' => Framework\SV_WC_Helper::get_requested_value('line1'),
				'city' => Framework\SV_WC_Helper::get_requested_value('city'),
				'state' => Framework\SV_WC_Helper::get_requested_value('region'),
				'country' => Framework\SV_WC_Helper::get_requested_value('country'),
				'postcode' => Framework\SV_WC_Helper::get_requested_value('postcode'),
			));

			$api_time = $response->get_response_time();
			$response_string = json_encode($response);

			// Documented in `WC_AvaTax_Settings::save_address_field`
			$address = (array) apply_filters('wc_avatax_save_address_field', $response->get_normalized_address());

			// Save the validated address
			update_option('wc_avatax_origin_address', $address);
			wc_avatax()->wc_avatax_utilities()->sync_origin_country_option();

			/**
			 * Fire after validating the origin address.
			 *
			 * @since 1.0.0
			 * @param array $address The validated and normalized address.
			 */
			do_action('wc_avatax_after_origin_address_validated', $address);

			$execution_end = hrtime(true);
			$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
			$connector_time = $execution_time - $api_time;
			wc_avatax()->logger()->log_performance("ValidateAddress", "validate_origin_address", "Validating the address.", "", "", $connector_time, $api_time, [], 0);

			wp_send_json(array(
				'code' => 200,
				'address' => $address,
			));

		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "validate_origin_address", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 2.6.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin(): WC_AvaTax
	{

		return wc_avatax();
	}

	/**
	 * Invite customer to add certificate
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function invite_customer_certificate()
	{

		try {
			$userid = sanitize_text_field(wp_unslash($_POST['userId']));
			$user_data = wc_avatax()->get_user_data($userid);
			if (!empty((array) $user_data['customerCode'])) {

				if (!empty((array) wc_avatax()->check_if_customer_exists_return($user_data['customerCode']))) {
					$certificateInviteResponse = $this->get_plugin()->get_api()->invite_customer_to_add_certificate($user_data['customerCode'], $user_data['emailAddress']);
				} else {
					$isUserAdded = wc_avatax()->add_customer_to_avatax($userid);
					if ($isUserAdded === true) {
						$certificateInviteResponse = $this->get_plugin()->get_api()->invite_customer_to_add_certificate($user_data['customerCode'], $user_data['emailAddress']);
					}
				}
				if (empty($certificateInviteResponse)) {
					wp_send_json(array(
						'code' => 0,
						'message' => "Failed to send invite to " . $user_data['emailAddress'] . ".",
					));
				} else {
					wp_send_json(array(
						'code' => 200,
						'message' => "Invite sent successfully to " . $user_data['emailAddress'] . ".",
					));
				}
			} else {
				wp_send_json(array(
					'code' => 0,
					'message' => "Please entry billing details and save the customer to sent invite.",
				));
			}


		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "invite_customer_certificate", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
				'message' => "Failed to send invite."
			));
		}
	}
	/**
	 * Update alternateId of the customer in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function update_alternate_id()
	{
		$customerCode = sanitize_text_field(wp_unslash($_POST['customerCode']));
		$userId = sanitize_text_field(wp_unslash($_POST['userId']));

		$current_user = wp_get_current_user();
		$user_data = wc_avatax()->get_user_data($userId);
		$alternateId = $user_data['alternateId'] != null ? $user_data['alternateId'] : $current_user->user_email . "_" . $userId;
		$args = array(
			"customerCode" => $customerCode,
			"alternateId" => $alternateId,
		);
		$customer_update_response = wc_avatax()->get_api()->update_customer_alternate_Id($args);

		if (empty($customer_update_response)) {
			wp_send_json(array(
				'code' => 0,
				'message' => "AlternateId Not Updated.",
			));
		} else {
			wp_send_json(array(
				'code' => 200,
				'message' => "AlternateId Updated.",
			));
		}
	}
	/**
	 * Update caching transient of the customer in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function update_caching_transient_for_customer()
	{

		$customerCode = sanitize_text_field(wp_unslash($_POST['customerCode']));
		set_transient("wc_avatax_api_" . $customerCode, true, DAY_IN_SECONDS);
	}
	/**
	 * Unlink certificate from customer Avatax account
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function unlink_customer_certificate()
	{
		try {

			$certid = sanitize_text_field(wp_unslash($_POST['certificateId']));
			$userid = sanitize_text_field(wp_unslash($_POST['userId']));
			$response = wc_avatax()->get_api()->unlink_certificate($certid, $userid);
			if (!$response) {
				wp_send_json(array(
					'code' => 0,
					'message' => "Failed to unlink certificate.",
				));
			} else {
				wp_send_json(array(
					'code' => 200,
					'message' => "Certificate unlinked successfully.",
				));
			}


		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "unlink_customer_certificate", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'message' => 'Failed to unlink certificate.',
				'error' => esc_html($e->getMessage()),
			));
		}
	}

	public function manage_certificate_link()
	{
		$url = (get_permalink(get_option('woocommerce_myaccount_page_id')) . 'tax-certificate');
		wc_avatax()->log("manage_certificate_link called-> " . $url);
		wp_send_json(array(
			'code' => 200,
			'data' => $url,
		));
	}

	/**
	 * Download customer certificate
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function download_certificate()
	{
		try {
			$certid = sanitize_text_field(wp_unslash($_POST['certid']));

			$company_id = (int) wc_avatax()->get_company_id();
			$environment = wc_avatax()->get_api_environment();
			$request_uri = ('production' === $environment) ? 'https://rest.avatax.com/api/v2/' : 'https://sandbox-rest.avatax.com/api/v2/';
			$download_request_uri = $request_uri . "companies/$company_id/certificates/" . urlencode($certid) . "/attachment";

			$account_number = get_option('wc_avatax_api_account_number');
			$license_key = get_option('wc_avatax_api_license_key');
			$authToken = sprintf('Basic %s', base64_encode("{$account_number}:{$license_key}"));

			$args = array(
				'method' => 'GET',
				'headers' => array(
					'Accept-language' => 'en',
					'authorization' => $authToken
				)
			);

			$response = wp_remote_get($download_request_uri, $args);
			$file_content = wp_remote_retrieve_body($response);
			$data = base64_encode($file_content);
			wp_send_json(array(
				'code' => 200,
				'data' => $data,
			));
		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "download_certificate", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}
	/**
	 * Validate the customer address at checkout.
	 *
	 * @since 1.0.0
	 */
	public function revalidate_customer_address()
	{
		WC()->session->set('wc_avatax_address_validated', false);
		wp_send_json(array(
			'code' => 200
		));
	}
	/**
	 * Validate the customer address at checkout.
	 *
	 * @since 1.0.0
	 */
	public function validate_customer_address()
	{

		//Performance log variables
		$execution_start = hrtime(true);
		$api_time = $execution_end = 0.0;
		$response_string = "";

		// No nonce? No go
		if (!wp_verify_nonce(Framework\SV_WC_Helper::get_requested_value('nonce'), 'wc_avatax_validate_customer_address')) {
			wp_die();
		}

		try {

			/**
			 * Fire before validating a customer address.
			 *
			 * @since 1.0.0
			 * @param array $address The validated and normalized address.
			 */
			do_action('wc_avatax_before_customer_address_validated');

			$response = wc_avatax()->get_api()->validate_address(array(
				'address_1' => Framework\SV_WC_Helper::get_posted_value('address_1'),
				'address_2' => Framework\SV_WC_Helper::get_posted_value('address_2'),
				'city' => Framework\SV_WC_Helper::get_posted_value('city'),
				'state' => Framework\SV_WC_Helper::get_posted_value('state'),
				'country' => Framework\SV_WC_Helper::get_posted_value('country'),
				'postcode' => Framework\SV_WC_Helper::get_posted_value('postcode'),
			));

			$api_time = $response->get_response_time();
			$response_string = json_encode($response);

			if ($response->has_errors()) {
				wp_send_json(array(
					'code' => 404,
					'error' => wc_avatax()->wc_avatax_utilities()->get_address_error_messages($response),
				));
			}

			$address = $response->get_normalized_address();

			// Set the shipping address values to the normalized address
			WC()->customer->set_shipping_address($address['address_1']);
			WC()->customer->set_shipping_address_2($address['address_2']);
			WC()->customer->set_shipping_city($address['city']);
			WC()->customer->set_shipping_state($address['state']);
			WC()->customer->set_shipping_country($address['country']);
			WC()->customer->set_shipping_postcode($address['postcode']);

			$type = Framework\SV_WC_Helper::get_posted_value('type');

			// If validating a billing address, set those values too
			if ('billing' === $type) {

				WC()->customer->set_billing_address($address['address_1']);
				WC()->customer->set_billing_address_2($address['address_2']);
				WC()->customer->set_billing_city($address['city']);
				WC()->customer->set_billing_state($address['state']);
				WC()->customer->set_billing_country($address['country']);
				WC()->customer->set_billing_postcode($address['postcode']);
			}

			// Prepend the address type (billing or shipping) to the keys
			foreach ($address as $key => $value) {
				$address[$type . '_' . $key] = $value;
				unset($address[$key]);
			}

			/**
			 * Fire after validating a customer address.
			 *
			 * @since 1.0.0
			 * @param array $address The validated and normalized address.
			 */
			do_action('wc_avatax_after_customer_address_validated', $address);

			WC()->session->set('wc_avatax_address_validated', true);

			$execution_end = hrtime(true);
			$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
			$connector_time = $execution_time - $api_time;
			wc_avatax()->logger()->log_performance("ValidateAddress", "validate_customer_address", "Validating the address call by AJAX", "", "", $connector_time, $api_time, [], 0);

			// Off you go
			wp_send_json(array(
				'code' => 200,
				'address' => $address,
			));

		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "validate_customer_address", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}


	/**
	 * Display the product variation tax code field.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $loop the variation loop key
	 * @param array $variation_data the variation data
	 * @param \WC_Product_Variation $variation the variation object
	 */
	public function display_product_variation_code_fields($loop, $variation_data, $variation)
	{

		$default = get_post_meta($variation->post_parent, '_wc_avatax_code', true);
		$tax_code = get_post_meta($variation->ID, '_wc_avatax_code', true);

		include(wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-product-variation-tax-code.php');
	}


	/**
	 * Save a product variation tax code.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $variation_id the varation ID
	 */
	public function save_product_variation_code_fields($variation_id)
	{

		$tax_code = '';

		if (isset($_POST['variable_post_id']) && (false !== ($i = array_search($variation_id, $_POST['variable_post_id'])))) {

			if (isset($_POST['variable_wc_avatax_code'])) {
				$tax_code = sanitize_text_field(wp_unslash($_POST['variable_wc_avatax_code'][$i]));
			}
		}

		if ('' !== $tax_code) {
			update_post_meta($variation_id, '_wc_avatax_code', wc_clean($tax_code));
		} else {
			delete_post_meta($variation_id, '_wc_avatax_code');
		}
	}


	/**
	 * Save the product tax code quick edit field.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Product $product the product object
	 */
	public function save_product_tax_code_quick_edit($product)
	{

		if (isset($_REQUEST['_wc_avatax_code'])) {
			update_post_meta($product->get_id(), '_wc_avatax_code', sanitize_text_field($_REQUEST['_wc_avatax_code']));
		}
	}


	/**
	 * Saves the tax code fields when a new product category is created.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id new term ID
	 * @param int $tt_id new term taxonomy ID
	 */
	public function save_category_code_fields($term_id, $tt_id)
	{

		$tax_code = sanitize_text_field(Framework\SV_WC_Helper::get_posted_value('wc_avatax_category_tax_code'));

		update_term_meta($term_id, 'wc_avatax_tax_code', $tax_code);
	}


	/**
	 * Add estimated AvaTax calculations to orders when "Calculate Taxes" is run from the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id the order ID
	 * @throws WC_Data_Exception
	 */
	public function estimate_order_tax($order_id)
	{

		// If not otherwise calculating taxes, bail
		if (!doing_action('wp_ajax_woocommerce_calc_line_taxes')) {
			return;
		}

		// If tax calculation is turned off, bail
		if (!wc_avatax()->get_tax_handler()->is_available()) {
			return;
		}

		$order = wc_get_order($order_id);
		$avatax_tax_included = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_tax_included', true);
		if ($avatax_tax_included == "") {

			$tax_included = get_option('woocommerce_prices_include_tax', 'no');
			wc_avatax()->wc_avatax_utilities()->add_order_meta($order->get_id(), '_wc_avatax_tax_included', $tax_included);
		}
		// If order couldn't be fetched, bail
		if (!$order) {
			return;
		}

		// Estimate taxes for the address provided in the request, if available. When an order is not yet saved, address
		// fields won't be set, making tax calculation not possible. The following will ensure we're using the address
		// given in the AJAX request (which is the address entered on the customer billing/shipping address form in admin).
		if (isset($_POST['country'], $_POST['state'])) {

			/** @see \WC_AJAX::calc_line_taxes() */
			$country_code = wc_strtoupper(wc_clean(wp_unslash($_POST['country'])));
			$state = wc_strtoupper(wc_clean(wp_unslash($_POST['state'])));
			$tax_based_on = get_option('woocommerce_tax_based_on', '');
			// temporarily set address fields on order object so that the tax request class can access them
			if ('shipping' === $tax_based_on) {
				$order->set_shipping_country($country_code);
				$order->set_shipping_state($state);
				$order->set_shipping_city(wc_strtoupper(wc_clean(wp_unslash($_POST['city'] ?? ''))));
				$order->set_shipping_postcode(wc_strtoupper(wc_clean(wp_unslash($_POST['postcode'] ?? ''))));
			} elseif ('billing' === $tax_based_on) {
				$order->set_billing_country($country_code);
				$order->set_billing_state($state);
				$order->set_billing_city(wc_strtoupper(wc_clean(wp_unslash($_POST['city'] ?? ''))));
				$order->set_billing_postcode(wc_strtoupper(wc_clean(wp_unslash($_POST['postcode'] ?? ''))));
			}

		} elseif ($order->has_shipping_address()) {
			$country_code = $order->get_shipping_country('edit');
			$state = $order->get_shipping_state('edit');
		} else {
			$country_code = $order->get_billing_country('edit');
			$state = $order->get_billing_state('edit');
		}

		// check that the destination is taxable
		if (!wc_avatax()->get_tax_handler()->is_location_taxable($country_code, $state)) {
			return;
		}

		wc_avatax()->get_order_handler()->estimate_tax($order);
	}


	/**
	 * Process order refunds and get accurate tax refund rates from the AvaTax API.
	 *
	 * Totals passed around this method are mostly negative floats that will _subtract_ from an order's total.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @deprecated 1.15.0
	 *
	 * @param int $order_id The order ID.
	 * @param int $refund_id The refund ID.
	 */
	public function process_refund($order_id, $refund_id)
	{

		wc_deprecated_function(__METHOD__, '1.15.0', 'WC_AvaTax_Order_Handler::process_refund');
	}


	/**
	 * Attempts to sync again all the products that had sync errors in a previous run.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function resync_products_with_errors()
	{

		check_ajax_referer('wc_avatax_resync_error_products', 'nonce');

		wc_avatax()->get_landed_cost_sync_handler()->resync_products_with_errors();
	}

	/**
	 * Makes an api call for getiing ecommerce token.
	 *
	 * @since 2.6.0
	 */
	public function get_ecommerce_token()
	{

		try {
			$custid = sanitize_text_field(wp_unslash($_POST['custid']));
			if ($custid == null) {
				$custid = wc_avatax()->get_user_email();
			}
			$response = wc_avatax()->get_api()->get_ecommerce_token($custid);
			wp_send_json(array(
				'code' => 200,
				'data' => $response,
			));

		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "get_ecommerce_token", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}

	/**
	 * Handles the ELR document download request.
	 *
	 * Triggers file download for a given order and media type.
	 * Falls back to downloading the document from the ELR API if not available locally.
	 *
	 * Expected POST params:
	 * - order_id (int)
	 * - media_type (string, e.g., 'application/xml')
	 */
	public static function handle_elr_document_download()
	{
		try {
			$order_id = absint($_POST['order_id'] ?? 0);
			$media_type = sanitize_text_field($_POST['media_type'] ?? '');
			$document_context = sanitize_text_field($_POST['document_context'] ?? '');
			$redirect_url = wp_get_referer() ?: admin_url('admin.php?page=wc-orders&action=edit&id=' . $order_id);

			// The AR-Outbound (Application Response) document is a separate Avalara
			// document tracked on dedicated order meta keys, so its download must
			// use the AR-Outbound document ID and file cache rather than the order
			// invoice's. Everything else in the flow is identical.
			$is_ar_outbound      = ('ar_outbound' === $document_context);
			$files_meta_key      = $is_ar_outbound ? '_wc_avatax_ar_outbound_downloaded_files' : '_wc_avatax_elr_downloaded_files';
			$document_id_meta_key = $is_ar_outbound ? '_wc_avatax_ar_outbound_document_id' : '_wc_avatax_invoice_id';

			if (empty($order_id) || empty($media_type)) {
				self::log_elr("Invalid download request: missing order ID or media type.");
				self::safe_redirect_and_exit($redirect_url);
			}

			// Retrieve and deserialize stored file metadata
			$stored_files = [];
			$files = maybe_unserialize(
				wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, $files_meta_key, true)
			);

			if (is_array($files)) {
				$stored_files = $files;
			}

			if (empty($files[$media_type])) {
				self::log_elr("No metadata found for media type '{$media_type}' in order ID {$order_id}.");
			}

			// Extract file path and name from metadata
			$file_path = $files[$media_type]['file_path'] ?? '';
			$file_name = $files[$media_type]['file_name'] ?? '';

			if (empty($file_path) || empty($file_name)) {
				self::log_elr("File path or name missing for media type '{$media_type}' in order ID {$order_id}.");
			}

			// Reject any stream-wrapper scheme (phar://, php://, http://, file://,
			// data://, glob://, etc.) before $file_path reaches file_exists() or
			// readfile(). Both functions can trigger PHAR object deserialization on
			// a phar:// URL, which is a known RCE class. Order meta is the source
			// here and is admin-controllable, so this guard runs at every use site.
			//
			// An *empty* path is not "unsafe" — it just means the document has not
			// been cached locally yet (always the case for AR-Outbound, which is
			// never pre-downloaded). In that case fall through to the API fallback
			// below instead of refusing the request outright.
			if ('' !== $file_path && !self::is_safe_local_file_path($file_path)) {
				self::log_elr("Refusing unsafe file path for order ID {$order_id}, media type '{$media_type}'.");
				self::safe_redirect_and_exit($redirect_url);
			}

			// If the file doesn't exist locally, attempt to download via API
			// nosemgrep: audit.php.lang.security.file.phar-deserialization
			if (!file_exists($file_path)) {
				$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, $document_id_meta_key, true);

				if ($invoice_id) {
					self::log_elr("Local file not found. Attempting fallback download from ELR API for order ID {$order_id}, media type '{$media_type}'.");

					$api_response = wc_avatax()->get_elr_api()->download_invoice($invoice_id, $media_type);
					$file_info = wc_avatax()->get_elr_handler()->save_downloaded_elr_document($invoice_id, $media_type, $api_response);
					clearstatcache();

					if (!empty($file_info)) {
						$stored_files[$media_type] = $file_info;

						wc_avatax()->wc_avatax_utilities()->update_order_meta(
							$order_id,
							$files_meta_key,
							$stored_files
						);

						$file_path = $file_info['file_path'];
						$file_name = $file_info['file_name'];

						// Re-validate after the fallback replaces $file_path: even
						// though save_downloaded_elr_document() owns the write, treat
						// its output as untrusted at this sink boundary.
						if (!self::is_safe_local_file_path($file_path)) {
							self::log_elr("Refusing unsafe file path returned by ELR API fallback for order ID {$order_id}.");
							self::safe_redirect_and_exit($redirect_url);
						}

						self::log_elr("File successfully downloaded and saved for order ID {$order_id}, media type '{$media_type}'.");
					} else {
						self::log_elr("Fallback download failed for order ID {$order_id}, media type '{$media_type}'.");
						self::safe_redirect_and_exit($redirect_url);
					}
				}
			}

			// Final file existence check
			// nosemgrep: audit.php.lang.security.file.phar-deserialization
			if (!file_exists($file_path)) {
				self::log_elr("File not found even after fallback for order ID {$order_id}, media type '{$media_type}'. Path attempted: {$file_path}");
				self::safe_redirect_and_exit($redirect_url);
			}

			// Serve the file to the browser
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $media_type);
			header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
			header('Content-Length: ' . filesize($file_path));
			flush();
			// nosemgrep: audit.php.lang.security.file.phar-deserialization,audit.php.lang.security.file.read-write-delete
			readfile($file_path);
			exit;

		} catch (Throwable $e) {
			$order_id = absint($_POST['order_id'] ?? 0);
			$redirect_url = wp_get_referer() ?: admin_url('admin.php?page=wc-orders&action=edit&id=' . $order_id);

			self::log_elr("Exception during ELR document download: " . $e->getMessage());
			self::safe_redirect_and_exit($redirect_url);
		}
	}

	/**
	 * Logs ELR-related messages only if logging is enabled.
	 *
	 * @param string $message
	 */
	private static function log_elr(string $message): void
	{
		if (wc_avatax()->elr_logging_enabled()) {
			wc_avatax()->log_elr($message);
		}
	}

	/**
	 * Whether $path looks like a plain on-disk file path (no stream-wrapper scheme).
	 *
	 * Returns false for empty / non-string values and for any path containing "://"
	 * — that pattern signals a PHP stream wrapper such as phar://, php://, http://,
	 * file://, data://, or glob://. Passing such a value into file_exists(),
	 * filesize(), or readfile() can trigger PHAR object deserialization (a known
	 * RCE class) and is therefore refused before reaching those sinks.
	 *
	 * Identifier-style and relative paths produced by save_downloaded_elr_document()
	 * pass; absolute on-disk paths pass; anything else does not. realpath() is
	 * intentionally not called here because callers use this guard *before*
	 * file_exists(), where the file may legitimately not yet exist.
	 *
	 * @since 1.18.0
	 *
	 * @param mixed $path Candidate file path from order meta or API fallback.
	 * @return bool True if the path is safe to pass to a filesystem function.
	 */
	private static function is_safe_local_file_path($path)
	{
		if (!is_string($path) || '' === $path) {
			return false;
		}
		if (false !== strpos($path, '://')) {
			return false;
		}
		return true;
	}

	/**
	 * Safely redirects the user to a given URL and terminates execution.
	 *
	 * This method wraps WordPress's wp_safe_redirect() followed by exit,
	 * ensuring that redirection happens securely and no code runs after it.
	 *
	 * Used in scenarios like download failures or invalid requests, where
	 * continuing script execution could cause errors or unintended behavior.
	 *
	 * @param string $url The URL to safely redirect to.
	 */
	protected static function safe_redirect_and_exit($url)
	{
		// Redirect to the specified URL using WordPress's safe redirect function
		wp_safe_redirect($url);

		// Immediately terminate script execution after redirection
		exit;
	}

	/**
	 * Map perform function for ELR field mapper UI.
	 *
	 * @since 2.7.2
	 */
	public function submit_map_perform()
	{
		// Restrict to admins who can manage the store. The handler reaches DB read/write
		// operations whose identifiers originate from admin-supplied free-text, so an
		// unauthenticated caller must never be able to invoke it (CWE-862, CWE-89).
		// wp_send_json() normally terminates the request via wp_die(); the explicit return
		// guards against test doubles that do not exit and keeps the contract deterministic.
		if (!current_user_can('manage_woocommerce')) {
			wp_send_json(array(
				'code' => 403,
				'error' => esc_html__('You are not allowed to perform this action.', 'woocommerce-avatax'),
			));
			return;
		}

		// Verify the WordPress nonce sent from the ELR mapper UI to prevent CSRF.
		// `false` keeps wp_die from being called so we can emit a JSON error.
		if (false === check_ajax_referer('wc_avatax_submit_map_perform', 'security', false)) {
			wp_send_json(array(
				'code' => 403,
				'error' => esc_html__('Invalid security token. Please refresh the page and try again.', 'woocommerce-avatax'),
			));
			return;
		}

		try {
			if (isset($_POST['param']) && !empty($_POST['param'])) {
				// Initialize variables with default values
				$response = null;
				$any_error = null;
				$records = null;
				$schema = null;
				$savedSchema = null;
				$mapperTables = null;

				// Extract entity type from string (remove #tab-1 or similar suffixes)
				$entity_raw = isset($_POST['entity'])
					? sanitize_text_field( wp_unslash( $_POST['entity'] ) )
					: ( isset($_POST['entity_type']) ? sanitize_text_field( wp_unslash( $_POST['entity_type'] ) ) : '' );
				$entity = preg_replace('/#.*$/', '', $entity_raw);

				switch ($_POST['param']) {
					case 'table_dependency';
						$response  = wc_avatax()->wc_avatax_elr_utilities()->getTableRefferenceFields(
							sanitize_text_field( wp_unslash( $_POST['tablename'] ) ),
							false,
							true,
							$entity
						);
						break;
					case 'save_mapping';
						$any_error = wc_avatax()->wc_avatax_elr_utilities()->saveEinvoiceMapping(wc_clean(wp_unslash($_POST['filterInfo'])));
						$response = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($entity);
						$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema($entity));
						$savedSchema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema($entity));
						$mapperTables = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($entity);
						break;
					case 'save_schema';
						$response  = json_encode(wc_avatax()->wc_avatax_elr_utilities()->save_and_send_schema(wc_clean( wp_unslash( $_POST['columns'] ) ), $entity));
						break;
					case 'document_ready';
						$response = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($entity);
						$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema($entity));
						$savedSchema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema($entity));
						$mapperTables = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($entity);
						break;
					case 'delete_mapper_record';
						wc_avatax()->wc_avatax_elr_utilities()->deleteMapperRecord(sanitize_text_field(wp_unslash($_POST['mapperid'])));
						$response = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($entity);
						$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema($entity));
						$savedSchema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema($entity));
						$mapperTables = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($entity);
						break;
					case 'delete_conditional_record';
						$response = wc_avatax()->wc_avatax_elr_utilities()->deleteConditionalRecord(sanitize_text_field(wp_unslash($_POST['conditionalId'])), sanitize_text_field(wp_unslash($_POST['filterId'])));
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getConditionalMapperTableRows());
						break;
					case 'ELRData';
						$order_number = sanitize_text_field(wp_unslash($_POST['order_number']));
						$response = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($order_number, $entity));
						break;
					case 'InvoiceMapper';
						$response = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceConditionalMapperRecords();
						break;
					case 'save_filter_data':
						$response = wc_avatax()->wc_avatax_elr_utilities()->InsertFilterData(wc_clean(wp_unslash($_POST['filterInfo'])));
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getConditionalMapperTableRows());
						break;
                    case 'save_application_response_mapping':
                        $raw_mapping = (isset($_POST['arMapping']) && is_array($_POST['arMapping']))
                            ? array_map('sanitize_text_field', wp_unslash($_POST['arMapping']))
                            : array();
                        $allowed_keys = array('RequestedActionCode', 'RequestedAction', 'StatusReasonCode', 'StatusReason');
                        $mapping = array();
                        foreach ($allowed_keys as $key) {
                            $value = isset($raw_mapping[$key]) ? $raw_mapping[$key] : 0;
                            $mapping[$key] = (bool)filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        }
                        $enabled_count = count(array_filter($mapping, static function ($v) {
                            return true === $v;
                        }));
                        if (0 === $enabled_count) {
                            $response = array(
                                'mapping' => $mapping,
                                'ccs_ok' => false,
                            );
                            $any_error = __('Select at least one Application Response field before saving and sending to Avalara.', 'woocommerce-avatax');
                            break;
                        }
                        update_option('wc_avatax_elr_application_response_mapping', $mapping);
                        $ccs_response = wc_avatax()->wc_avatax_elr_utilities()->send_application_response_schema_to_ccs($mapping, 'POST');
                        $ccs_code = (is_object($ccs_response) && method_exists($ccs_response, 'get_response_code'))
                            ? (int)$ccs_response->get_response_code()
                            : 0;
                        $ccs_ok = ($ccs_code >= 200 && $ccs_code < 300) || 0 === $ccs_code; // 0 = mocked / no-op response in tests / ELR disabled

                        $response = array(
                            'mapping' => $mapping,
                            'ccs_code' => $ccs_code,
                            'ccs_ok' => $ccs_ok,
                        );
                        $any_error = $ccs_ok
                            ? __('The selected data fields from WooCommerce are sent successfully to Avalara for mapping.', 'woocommerce-avatax')
                            : sprintf(
                            /* translators: %d: HTTP response code returned by the Avalara CCS schema endpoint. */
                                __('Mapping saved locally, but sending the schema to Avalara failed (HTTP %d). Please retry.', 'woocommerce-avatax'),
                                $ccs_code
                            );
                        break;
					default:
						break;


				}
				wp_send_json(array(
					'code' => 200,
					'data' => $response,
					'records' => $records,
					'schema' => $schema,
					'savedSchema' => $savedSchema,
					'mapperTables' => $mapperTables,
					'error_save' => $any_error,
				));

			}

		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr($e->getMessage());
			}

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}
	/**
	 * Gets the tax codes from AvaTax.
	 * 
	 * @since 2.6.1
	 *
	 */
	public function tax_code_sync()
	{
		$response = wc_avatax()->get_api()->get_tax_codes();

		wp_send_json_success([
			'isSuccess' => $response,
		]);
	}

	/**
	 * Search for the tax codes in database.
	 * 
	 * @since 2.6.1
	 *
	 */
	public function tax_code_lookup()
	{
		global $wpdb;

		$type = sanitize_text_field(wp_unslash($_REQUEST['type']));
		$key = sanitize_key(sanitize_text_field(wp_unslash($_REQUEST['key'])));
		$sql = "";

		$table_name = $wpdb->prefix . "wc_avatax_tax_codes";
		$query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));
		$records = "";

		if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($wpdb->prefix . "wc_avatax_tax_codes"))) === $wpdb->prefix . "wc_avatax_tax_codes") {

			// Build query with table name (safe from $wpdb->prefix) and prepare LIKE patterns
			$like_pattern = '%' . $wpdb->esc_like($key) . '%';
			$results = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM `' . $wpdb->prefix . 'wc_avatax_tax_codes` WHERE taxCode LIKE %s OR description LIKE %s',
					$like_pattern,
					$like_pattern
				)
			);

			if (!empty($results)) {
				$records = "<ul>";
				foreach ($results as $result) {
					$records = $records . "<li class='lookupItem' data-val='" . esc_attr($result->taxCode) . "'>" . esc_html($result->taxCode) . " " . esc_html($result->description) . "</li>";
				}
				$records = $records . "</ul>";
			}
		}

		wp_send_json_success([
			'records' => $records,
		]);
	}

	/**
	 * Disconnects the connection to AvaTax.
	 * 
	 * @since 2.7.0
	 *
	 */
	public function disconnect_avatax()
	{
		//Logging Dissconnect event
		wc_avatax()->logger()->log_event("Disconnect", "disconnect_avatax", "Successfully disconnected the AvaTax account.");

		wc_avatax()->wc_avatax_utilities()->disconnect_avatax(false);

		wp_send_json(array(
			'code' => 200
		));
	}

	/**
	 * Updates the connection to AvaTax.
	 * 
	 * @since 2.7.0
	 *
	 */
	public function update_connection()
	{
		$account_number = sanitize_text_field(wp_unslash($_REQUEST['wc_avatax_api_account_number']));
		$license_key = sanitize_text_field(wp_unslash($_REQUEST['wc_avatax_api_license_key']));
		$environment = sanitize_text_field(wp_unslash($_REQUEST['wc_avatax_api_environment'])) ?? '';

		$api = new WC_AvaTax_API($account_number, $license_key, '', $environment);

		$response = $api->test();

		if (!$response->is_authenticated()) {
			//Logging Update Connection event
			wc_avatax()->logger()->log_event("UpdateConnection", "update_connection", "Account ID (" . $account_number . ") or License key is incorrect. Restored old credentials.");

			wp_send_json(array(
				'code' => 401,
				'data' => $response,
				'message' => 'Account ID or License key is incorrect. Restored old credentials'
			));
		} else {
			wc_avatax()->wc_avatax_utilities()->disconnect_avatax(true);

			//Logging Update Connection event
			wc_avatax()->logger()->log_event("UpdateConnection", "update_connection", "Successfully updated connection");

			wp_send_json(array(
				'code' => 200,
				'data' => $response,
			));
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
	public function hide_order_item_meta($hidden_meta)
	{
		return wc_avatax()->wc_avatax_utilities()->hide_order_item_meta($hidden_meta);
	}

	/**
	 * Syncs the configuration settings with CUP.
	 *
	 * @since 2.8.0
	 *
	 */
	public function refresh_config()
	{
		try {

			wc_avatax()->wc_avatax_utilities()->sync_confic_settings();

			//Logging Refresh Configuration event
			wc_avatax()->logger()->log_event("SynchronizeConfig", "refresh_config", "Synchronizing the configuration by clicking Synchronize config button on UI.");

			wp_send_json(array(
				'code' => 200,
				'message' => 'Connected to AvaTax, Refreshing settings.'
			));
		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "refresh_config", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}

	/**
	 * Disconnects the connection to ELR.
	 * 
	 * @since 3.0.0
	 *
	 */
	public function disconnect_elr()
	{

		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		//Logging Dissconnect event
		wc_avatax()->elr_logger()->log_event("Disconnect", "disconnect_elr", "Successfully disconnected the AvaTax ELR.");

		wc_avatax()->wc_avatax_elr_utilities()->disconnect_elr();

		wp_send_json(array(
			'code' => 200
		));
	}

	/**
	 * Saves the custom fields
	 * 
	 * @since 3.0.0
	 *
	 */
	public function save_custom_fields()
	{
		$response = ['code' => 200];

		try {
			check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

			// Get and decode new fields from AJAX request
			$wc_avatax_elr_custom_fields = html_entity_decode(stripslashes(sanitize_text_field(wp_unslash($_REQUEST['wc_avatax_elr_custom_fields'])) ?? ''));
			$new_fields = json_decode($wc_avatax_elr_custom_fields);

			// Get existing fields
			$already_present_fields = get_option("wc_avatax_elr_custom_fields");

			if ($new_fields && $already_present_fields) {
				// Get counts for comparison
				$existing_count = count((array) $already_present_fields->company) + count((array) $already_present_fields->customer);
				$new_count = count((array) $new_fields->company) + count((array) $new_fields->customer);

				// Only proceed if we have fewer new fields than existing fields
				if ($new_count < $existing_count) {
					// Get all existing field IDs
					$existing_field_ids = [];
					if (!empty($already_present_fields->company)) {
						foreach ($already_present_fields->company as $field) {
							$existing_field_ids[] = $field->field_id;
						}
					}
					if (!empty($already_present_fields->customer)) {
						foreach ($already_present_fields->customer as $field) {
							$existing_field_ids[] = $field->field_id;
						}
					}

					// Get all new field IDs
					$new_field_ids = [];
					if (!empty($new_fields->company)) {
						foreach ($new_fields->company as $field) {
							$new_field_ids[] = $field->field_id;
						}
					}
					if (!empty($new_fields->customer)) {
						foreach ($new_fields->customer as $field) {
							$new_field_ids[] = $field->field_id;
						}
					}

					// Find removed field IDs
					$removed_field_ids = array_diff($existing_field_ids, $new_field_ids);

					// If we found removed fields, process them
					foreach ($removed_field_ids as $field_id) {
						delete_option($field_id);
					}
				}
			}

			// Update with new fields
			update_option("wc_avatax_elr_custom_fields", $new_fields);

		} catch (Exception $e) {
			$response = [
				'code' => 500,
				'message' => $e->getMessage()
			];
		}

		wp_send_json($response);
	}

	/**
	 * Saves the custom fields
	 * 
	 * @since 3.0.0
	 *
	 */
	public function refresh_elr_status()
	{
		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		$order = wc_get_order(sanitize_text_field(wp_unslash($_POST['order_id'])));
		$status_details = wc_avatax()->get_elr_handler()->get_invoice_status_details($order);
		$invoice_status = $status_details['status'];
		$invoice_status_messages = wc_avatax()->get_elr_handler()->get_invoice_status_messages($status_details['messages']);
		$processing_id = $status_details['processing_id'];
		$html = '';
		if ($order instanceof WC_Order_Refund) {
			$html = wc_avatax()->wc_avatax_elr_utilities()->get_elr_refund_status_html($order->get_id(), $invoice_status, $processing_id, $invoice_status_messages);
		} else {
			$html = wc_avatax()->wc_avatax_elr_utilities()->get_elr_status_html($order->get_id(), $invoice_status, $processing_id, $invoice_status_messages);
		}
		wp_send_json(array(
			'code' => 200,
			'data' => $html,
			'element_identifier' => (($order instanceof WC_Refund) ? "refund-" : "order-") . $order->get_id()
		));
	}

    /**
     * Manually triggers the ELR Application Response (inbound documents) sync.
     *
     * For testing only — bypasses the scheduler cadence and runs the same
     * worker synchronously so the caller gets an immediate response with a
     * fresh `last_run_end_date` value.
     *
     * @since 3.8.4
     */
    public function run_application_response_sync()
    {

        check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json(array(
                'code' => 403,
                'message' => __('You do not have permission to run this action.', 'woocommerce-avatax'),
            ));
        }

        $scheduler = wc_avatax()->get_application_response_scheduler();

        if (!$scheduler) {
            wp_send_json(array(
                'code' => 500,
                'message' => __('Application Response scheduler is not initialized.', 'woocommerce-avatax'),
            ));
        }

        try {
            $scheduler->runApplicationResponseSync();

            $last_run_end_date = get_option('wc_avatax_elr_buyer_feedback_last_run_end_date', '');

            wp_send_json(array(
                'code' => 200,
                'message' => __('Application Response sync completed. Check the AvaTax log for details.', 'woocommerce-avatax'),
                'last_run_end_date' => $last_run_end_date,
            ));
        } catch (Exception $e) {

            wp_send_json(array(
                'code' => 500,
                'message' => $e->getMessage(),
            ));
        }
    }

	/**
	 * Send order to Avalara E-invoicing and Live Reporting
	 * 
	 * @since 3.0.0
	 *
	 */
	public function send_order_to_avalara()
	{
		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		$order = new WC_Order(sanitize_text_field(wp_unslash($_POST['order_id'])));
		if (!$order) {
			wp_send_json(array(
				'code' => 400,
				'error' => 'Order not found',
			));
			return;
		}

		wc_avatax()->get_elr_handler()->process_elr($order);

		wp_send_json(array(
			'code' => 200,
		));

	}

	/**
	 * Send refund to Avalara E-invoicing and Live Reporting
	 * 
	 * @since 3.0.0
	 *
	 */
	public function send_refund_to_avalara()
	{
		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		$refund = new WC_Order_Refund(sanitize_text_field(wp_unslash($_POST['order_id'])));
		$order = $refund->get_parent_id();
		if (!$refund) {
			wp_send_json(array(
				'code' => 400,
				'error' => 'Refund not found',
			));
			return;
		}
		if (!$order) {
			wp_send_json(array(
				'code' => 400,
				'error' => 'Order not found',
			));
		}

		wc_avatax()->get_elr_handler()->process_refund_to_elr($refund->get_parent_id(), sanitize_text_field(wp_unslash($_POST['order_id'])));

		wp_send_json(array(
			'code' => 200,
		));

	}

	/**
	 * Report order payment to Avalara E-invoicing and Live Reporting.
	 *
	 * @since 3.8.4
	 *
	 * @return void
	 */
	public function report_order_payment_to_avalara()
	{
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		$order = wc_get_order(sanitize_text_field(wp_unslash($_POST['order_id'])));

		if (!$order) {
			wp_send_json(array(
				'code' => 400,
				'error' => 'Order not found',
			));
		}

		wc_avatax()->get_elr_handler()->reportPaymentToELR($order);

		wp_send_json(array(
			'code' => 200,
		));
	}

	/**
	 * Report refund payment to Avalara E-invoicing and Live Reporting.
	 *
	 * @since 3.8.4
	 *
	 * @return void
	 */
	public function report_refund_payment_to_avalara()
	{
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		$refund = wc_get_order(sanitize_text_field(wp_unslash($_POST['order_id'])));

		if (!$refund) {
			wp_send_json(array(
				'code' => 400,
				'error' => 'Refund not found',
			));
		}

		wc_avatax()->get_elr_handler()->reportPaymentToELR($refund);

		wp_send_json(array(
			'code' => 200,
		));
	}

	/**
	 * Refresh the AR-Outbound (Application Response) status meta box.
	 *
	 * @since 3.8.4
	 *
	 * @return void
	 */
	public function refresh_ar_outbound_status()
	{
		// No nonce? No go
		check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');

		$order = wc_get_order(sanitize_text_field(wp_unslash($_POST['order_id'])));

		if (!$order) {
			wp_send_json(array(
				'code' => 400,
				'error' => 'Order not found',
			));
			return;
		}

		$elr_handler = wc_avatax()->get_elr_handler();
		$status_details = $elr_handler->get_ar_outbound_status_details($order);
		$messages_html = $elr_handler->get_invoice_status_messages($status_details['messages']);

		$html = wc_avatax()->wc_avatax_elr_utilities()->get_ar_outbound_status_html(
			$order->get_id(),
			$status_details['status'],
			$status_details['document_id'],
			$messages_html
		);

		wp_send_json(array(
			'code' => 200,
			'data' => $html,
			'element_identifier' => 'ar-outbound-' . $order->get_id(),
		));
	}

	/**
	 * Clear all AvaTax transients.
	 * 
	 * @since 3.0.0
	 */
	public function clear_avatax_transients()
	{
		try {

			wc_avatax()->wc_avatax_utilities()->clear_all_avatax_transients();

			wp_send_json(array(
				'code' => 200,
				'message' => 'All AvaTax transients cleared successfully.'
			));
		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "clear_avatax_transients", $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}

	/**
	 * Clear EntityCode Separately as this is a billable API.
	 * 
	 * @since 3.7.0
	 */
	public function clear_avatax_entitycodes()
	{
		delete_option('wc_avatax_entity_use_codes');
		wp_send_json(array(
			'code' => 200,
			'message' => 'Entity Code cleared successfully.'
		));
	}

	/**
	 * Clear Nexus List Separately as this is a billable API.
	 * 
	 * @since 3.7.0
	 */
	public function clear_avatax_nexuslist()
	{
		delete_option('wc_avatax_full_nexus_details');
		delete_option('wc_avatax_landed_cost_supported_countries');
		wp_send_json(array(
			'code' => 200,
			'message' => 'Nexus List cleared successfully.'
		));
	}

	/**
	 * Updates the origin address from Avalara via AJAX.
	 *
	 * @since 2.8.0
	 */
	public function update_origin_address()
	{
		try {
			wc_avatax()->wc_avatax_utilities()->update_origin_address();

			wp_send_json(array(
				'code' => 200,
				'message' => __('Origin address has been updated successfully.', 'woocommerce-avatax'),
			));
		} catch (Framework\SV_WC_API_Exception $e) {

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log($e->getMessage());
			}

			wc_avatax()->logger()->log_exception('Ajax', 'update_origin_address', $e->getMessage(), $e->getTraceAsString());

			wp_send_json(array(
				'code' => (int) $e->getCode(),
				'error' => esc_html($e->getMessage()),
			));
		}
	}
}
