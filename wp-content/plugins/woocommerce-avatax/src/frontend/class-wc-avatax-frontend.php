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
require_once( wc_avatax()->get_plugin_path() . '/src/admin/class-wc-avatax-settings.php' );

defined( 'ABSPATH' ) or exit;

/**
 * Set up the AvaTax front-end.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Frontend {


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load front end assets
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_action( 'woocommerce_edit_account_form', [ $this, 'display_custom_fields'] );
		add_action( 'woocommerce_save_account_details', [ $this, 'save_custom_fields'] );

		if ( $this->address_validation_enabled() ) {

			// Add an address validation button below each address form at checkout.
			add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'add_validate_address_button' ) );
			add_action( 'woocommerce_after_checkout_shipping_form', array( $this, 'add_shipping_validate_address_button' ) );

			// Validate the customer address at checkout when JavaScript is disabled.
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_address' ) );
		}

		if ( wc_avatax()->get_tax_handler()->is_available() ) {

			// Display a "pending calculation" message on the cart page
			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_cart_calculation_message' ) );
			} else {
				add_filter( 'woocommerce_cart_totals_taxes_total_html', array( $this, 'adjust_single_tax_total_html' ) );
			}

			// Add the VAT field if enabled
			if ( apply_filters( 'wc_avatax_enable_vat', ( 'yes' === get_option( 'wc_avatax_enable_vat' ) ) ) ) {
				add_filter( 'woocommerce_billing_fields', array( $this, 'add_checkout_vat_field' ) );
			}
			if(get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes')
			{
				add_filter( 'woocommerce_checkout_order_review', array( $this, 'display_ecm_links' ) );
				add_filter( 'woocommerce_checkout_shipping', array( $this, 'display_ecm_message' ) );
				$this->display_ecm_table();
			}
		}
	}

	/**
	 * Display the custom fields.
	 *
	 * @since 3.0.0
	 * @codeCoverageIgnore
	 */
	public function display_custom_fields() {
		$user = wp_get_current_user();

		// Backfill newly introduced ELR customer fields on upgraded sites before rendering.
		$plugin = wc_avatax();
		if ($plugin && method_exists($plugin, 'wc_avatax_elr_utilities')) {
			$elr_utilities = $plugin->wc_avatax_elr_utilities();
			if ($elr_utilities && method_exists($elr_utilities, 'is_elr_enabled') && method_exists($elr_utilities, 'add_default_custom_fields') && $elr_utilities->is_elr_enabled()) {
				$elr_utilities->add_default_custom_fields();
			}
		}

		$field_list = get_option('wc_avatax_elr_custom_fields', array());

		if (empty($field_list) || !is_object($field_list) || !isset($field_list->customer)) {
			return;
		}

		foreach ((array) $field_list->customer as $field) {
			$stored_value = get_user_meta($user->ID, $field->field_id, true);

			if ('boolean' === $field->data_type) {
				$this->render_customer_boolean_field($field, $stored_value);
			} else {
				$this->render_customer_text_field($field, $stored_value);
			}
		}
	}

	/**
	 * Render a customer-level boolean custom field as a checkbox.
	 *
	 * Booleans are persisted as WC's canonical 'yes' / 'no' string — see
	 * {@see WC_AvaTax_Elr::determineEntityType()}, which compares against
	 * 'yes' directly. A real checkbox is required (not a text/number input)
	 * so the unchecked state can be captured by save_custom_fields(): an
	 * unchecked checkbox is absent from $_POST entirely.
	 *
	 * @since 1.16.0
	 * @codeCoverageIgnore
	 *
	 * @param object $field        Field definition from `wc_avatax_elr_custom_fields`.
	 * @param mixed  $stored_value Current value from user meta ('yes' / 'no' / '').
	 */
	private function render_customer_boolean_field($field, $stored_value) {
		?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="<?php echo esc_attr($field->field_id) ?>"><?php echo esc_html($field->field_name); ?></label>
			<input class="woocommerce-Input" type="checkbox" name="<?php echo esc_attr($field->field_id) ?>" id="<?php echo esc_attr($field->field_id) ?>" value="yes" <?php checked('yes', $stored_value); ?> />
		</p>
		<?php
	}

	/**
	 * Render a customer-level text/number/date custom field.
	 *
	 * @since 1.16.0
	 * @codeCoverageIgnore
	 *
	 * @param object $field        Field definition from `wc_avatax_elr_custom_fields`.
	 * @param mixed  $stored_value Current value from user meta.
	 */
	private function render_customer_text_field($field, $stored_value) {
		$type = ($field->data_type == 'string' ? 'text' : ($field->data_type == 'date' ? 'date' : 'number'));
		?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="<?php echo esc_attr($field->field_id) ?>"><?php echo esc_html($field->field_name); ?></label>
			<input class="woocommerce-Input woocommerce-Input--text input-text" <?php echo $type == 'text' ? 'maxlength="100"' : '' ?> type="<?php echo esc_attr($type) ?>" name="<?php echo esc_attr($field->field_id) ?>" id="<?php echo esc_attr($field->field_id) ?>" value="<?php echo esc_attr($stored_value) ?>" />
		</p>
		<?php
	}

	/**
	 * Save the custom fields.
	 *
	 * @since 3.0.0
	 */
	public function save_custom_fields( $user_id ) {
		$plugin = wc_avatax();
		if ($plugin && method_exists($plugin, 'wc_avatax_elr_utilities')) {
			$elr_utilities = $plugin->wc_avatax_elr_utilities();
			if ($elr_utilities && method_exists($elr_utilities, 'is_elr_enabled') && method_exists($elr_utilities, 'add_default_custom_fields') && $elr_utilities->is_elr_enabled()) {
				$elr_utilities->add_default_custom_fields();
			}
		}

		$field_list = get_option('wc_avatax_elr_custom_fields', array());

		if (empty($field_list) || !is_object($field_list) || !isset($field_list->customer)) {
			return;
		}

		foreach ((array) $field_list->customer as $field) {
			if ('boolean' === $field->data_type) {
				// Unchecked checkboxes are absent from $_POST entirely, so an
				// isset() guard alone would never flip a stored 'yes' back to
				// 'no'. Always write one or the other, using WC's canonical
				// string representation (matches what determineEntityType()
				// compares against in class-wc-avatax-elr.php).
				$value = isset($_POST[$field->field_id]) ? 'yes' : 'no';
				update_user_meta($user_id, $field->field_id, $value);
				continue;
			}

			if (isset($_POST[$field->field_id])) {
				update_user_meta($user_id, $field->field_id, sanitize_text_field($_POST[$field->field_id]));
			}
		}
	}

	public function display_ecm_message() {
			echo '<tr class="ecm-exemptmessage">';
				echo '<th>' . "AvaTax uses this email ID for tax exemption. To receive tax exemption for the order, ensure that the email ID you enter here is applicable for tax exemption." . '</th>';
			echo '</tr>';
	}

	/**
	 * Get localization data for blocks checkout ECM
	 *
	 * @since 1.16.0
	 * @return array Localization data array
	 */
	private function get_blocks_checkout_localization_data() {
		$current_user = wp_get_current_user();
		$user_meta_data = get_metadata_raw('user', get_current_user_id());
		$email = empty($user_meta_data['billing_email']) ? "" : $user_meta_data['billing_email'][0];
		
		return [
			'ajax_url'                     		=> admin_url( 'admin-ajax.php' ),
			'user_email'				   		=> $email != null ? $email : $current_user->user_email,
			'user_id'					   		=> get_current_user_id(),
			'is_checkout'				   		=> is_checkout(),
			'select_zone'                       => __( 'Please select exposure zone.', 'woocommerce' ),
			'enter_billing_address'				=> __( 'Please enter billing email address and save the details.', 'woocommerce' ),
			'gencert_generic_error'				=> __( "The page you're looking for couldn't be found. Please contact Avalara Support.", 'woocommerce' ),
			'confirm_invalidate_certificate'	=> __( "Are you sure you'd like to invalidate this certificate?", 'woocommerce' ),
			'is_checkout_block'					=> $this->is_checkout_block(),
			'myaccount_url'						=> rtrim(get_permalink(get_option('woocommerce_myaccount_page_id')), "/"),
			'checkout_url'						=> rtrim(get_permalink(get_option('woocommerce_checkout_page_id')), "/"),
			'submit_to_stack'					=> (get_option('wc_avatax_certificate_submit_to_queue', 'no') === 'yes'),
		];
	}

	/**
	 * Get the certificate SDK URL based on environment
	 *
	 * @since 1.16.0
	 * @return string SDK URL
	 * @codeCoverageIgnore
	 */
	private function get_certificate_sdk_url() {
		return get_option('wc_avatax_api_environment') === 'development' 
			? "https://sbx.certcapture.com/gencert2/js"
			: "https://app.certcapture.com/gencert2/js";
	}

	/**
	 * Loads front-end assets.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function load_scripts() {

		$current_user = wp_get_current_user();
		// Load ECM scripts for account pages and classic checkout
		if((is_account_page() || (is_checkout() && !$this->is_checkout_block())) && get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes')
		{
			$user_meta_data = get_metadata_raw('user',get_current_user_id());
			$email = empty($user_meta_data['billing_email'])?"":$user_meta_data['billing_email'][0];
			wp_enqueue_script( 'wc-avatax-frontend-misc', wc_avatax()->get_plugin_url() . '/assets/js/frontend/wc-avatax-frontend-misc.min.js', array( 'jquery' ) );
			wp_localize_script( 'wc-avatax-frontend-misc', 'wc_avatax_frontend_misc', [

				'ajax_url'                     		=> admin_url( 'admin-ajax.php' ),
				'user_email'				   		=> $email!=null ? $email : $current_user->user_email,
				'user_id'					   		=> get_current_user_id(),
				'is_checkout'				   		=>is_checkout(),
				'select_zone'                       => __( 'Please select exposure zone.', 'woocommerce' ),
				'enter_billing_address'				=> __( 'Please enter billing email address and save the details.', 'woocommerce' ),
				'gencert_generic_error'				=> __( "The page you're looking for couldn't be found. Please contact Avalara Support.", 'woocommerce' ),
				'confirm_invalidate_certificate'	=> __( "Are you sure you'd like to invalidate this certificate?", 'woocommerce' ),
				'is_checkout_block'					=>  $this->is_checkout_block(),
				'myaccount_url'						=> rtrim(get_permalink(get_option('woocommerce_myaccount_page_id') ), "/"),
				'checkout_url'						=> rtrim(get_permalink(get_option('woocommerce_checkout_page_id') ), "/"),
				'submit_to_stack'					=> (get_option('wc_avatax_certificate_submit_to_queue', 'no') === 'yes'),

			] );
			wp_enqueue_script( 'wc-avatax-gencert', get_option('wc_avatax_api_environment') === 'development' ? "https://sbx.certcapture.com/gencert2/js":"https://app.certcapture.com/gencert2/js", array( 'jquery' ));

		}

	// For blocks checkout, we still need to provide the localization data and certificate SDK but without the old script
	// @codeCoverageIgnoreStart
	// WordPress enqueue functions cannot be properly unit tested with WP_Mock
	// These are framework API calls with no business logic
	if(is_checkout() && $this->is_checkout_block() && get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes')
	{
		// Create a dummy script just to localize the data for blocks checkout
		wp_register_script( 'wc-avatax-frontend-misc-blocks', '', array( 'jquery' ) );
		wp_enqueue_script( 'wc-avatax-frontend-misc-blocks' );
		wp_localize_script( 'wc-avatax-frontend-misc-blocks', 'wc_avatax_frontend_misc', $this->get_blocks_checkout_localization_data() );

		// Load the certificate capture SDK and frontend misc script for blocks checkout
		wp_enqueue_script( 'wc-avatax-gencert', $this->get_certificate_sdk_url(), array( 'jquery' ));
		wp_enqueue_script( 'wc-avatax-frontend-misc', wc_avatax()->get_plugin_url() . '/assets/js/frontend/wc-avatax-frontend-misc.js', array( 'jquery', 'wc-avatax-gencert' ), WC_AvaTax::VERSION, true );

	// Load the ECM block CSS for blocks checkout
	wp_enqueue_style( 'wc-avatax-ecm-block', wc_avatax()->get_plugin_url() . '/src/blocks/ECM/styles/style.css', [], WC_AvaTax::VERSION );

	// Load guest handler script for blocks checkout
	wp_enqueue_script( 'wc-avatax-blocks-guest-handler', wc_avatax()->get_plugin_url() . '/assets/js/frontend/wc-avatax-blocks-guest-handler.js', array( 'jquery', 'wc-avatax-frontend-misc' ), WC_AvaTax::VERSION, true );
}
	// @codeCoverageIgnoreEnd
		// load styles
		wp_enqueue_style( 'wc-avatax-frontend', wc_avatax()->get_plugin_url() . '/assets/css/frontend/wc-avatax-frontend.min.css', [], WC_AvaTax::VERSION );
		// the frontend JS is also needed apart from address validation
		wp_enqueue_script( 'wc-avatax-frontend', wc_avatax()->get_plugin_url() . '/assets/js/frontend/wc-avatax-frontend.min.js', array( 'jquery' ), WC_AvaTax::VERSION, true );




		wp_localize_script( 'wc-avatax-frontend', 'wc_avatax_frontend', [

			'ajax_url'                     => admin_url( 'admin-ajax.php' ),
			'address_validation_nonce'     => wp_create_nonce( 'wc_avatax_validate_customer_address' ),
			'address_validation_countries' => $this->address_validation_enabled() ? $this->get_address_validation_countries() : "",
			'is_checkout'					  => is_checkout(),
			'i18n' => [
				'address_validated' => __( 'Address validated.', 'woocommerce-avatax' ),
			],
			'tax_based_on'						=> get_option( 'woocommerce_tax_based_on', '' ),
			'collect_vat_id_enabled'			=> $this->collect_vat_enabled(),
			'user_id'					   		=> get_current_user_id(),
			'myaccount_url'						=> rtrim(get_permalink(get_option('woocommerce_myaccount_page_id') ), "/"),
			'checkout_url'						=> rtrim(get_permalink(get_option('woocommerce_checkout_page_id') ), "/"),
			'cart_contains_only_virtual_zero' => $this->cart_contains_only_virtual_products_with_zero_amount()
		] );

		if ( !(is_checkout() || is_cart())) {
			return;
		}
	}


	/**
	 * Add an address validation button at checkout.
	 *
	 * @since 1.0.0
	 */
	public function add_validate_address_button() {

		// Skip address validation button if cart contains only virtual products with $0 amount
		if ( $this->cart_contains_only_virtual_products_with_zero_amount() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method already escapes output
		echo $this->get_validate_address_button();
	}


	/**
	 * Add an address validation button at checkout.
	 *
	 * @since 1.1.1
	 */
	public function add_shipping_validate_address_button() {

		// Skip address validation button if cart contains only virtual products with $0 amount
		if ( $this->cart_contains_only_virtual_products_with_zero_amount() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method already escapes output
		echo $this->get_validate_address_button( 'shipping' );
	}


	/**
	 * Gets the address validation button markup.
	 *
	 * @since 1.1.1
	 *
	 * @param string $type button type, either shipping or billing
	 * @return string button HTML
	 */
	protected function get_validate_address_button( $type = 'billing' ) {

		/**
		 * Filters the address validation button label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $label the address validation button label
		 */
		$label = (string) apply_filters( 'wc_avatax_validate_address_button_label', __( 'Validate Address', 'woocommerce-avatax' ) );

		return '<button class="wc_avatax_validate_address button" data-address-type="' . esc_attr( $type ) . '">' . esc_html( $label ) . '</button>';
	}


	/**
	 * Validate the customer address at checkout when JavaScript is disabled.
	 *
	 * @since 1.0.0
	 */
	public function validate_address() {

		// Skip address validation if cart contains only virtual products with $0 amount
		if ( $this->cart_contains_only_virtual_products_with_zero_amount() ) {
			return;
		}

		// If the address validation button was not pressed, bail
		if ( ! Framework\SV_WC_Helper::get_posted_value( 'woocommerce_checkout_update_totals' ) ) {
			return;
		}

		// Skip shipping if not needed
		if ( Framework\SV_WC_Helper::get_posted_value( 'ship_to_different_address' ) ) {
			$type = 'shipping';
		} else {
			$type = 'billing';
		}

		$response = wc_avatax()->get_api()->validate_address( array(
			'address_1' => Framework\SV_WC_Helper::get_posted_value( $type . '_address_1' ),
			'address_2' => Framework\SV_WC_Helper::get_posted_value( $type . '_address_2' ),
			'city'      => Framework\SV_WC_Helper::get_posted_value( $type . '_city' ),
			'state'     => Framework\SV_WC_Helper::get_posted_value( $type . '_state' ),
			'country'   => Framework\SV_WC_Helper::get_posted_value( $type . '_country' ),
			'postcode'  => Framework\SV_WC_Helper::get_posted_value( $type . '_postcode' ),
		) );

		$address = $response->get_normalized_address();

		// Set the shipping address values to the normalized address
		$_POST[ $type . '_address_1' ] = $address['address_1'];
		$_POST[ $type . '_address_2' ] = $address['address_2'];
		$_POST[ $type . '_city' ]      = $address['city'];
		$_POST[ $type . '_state' ]     = $address['state'];
		$_POST[ $type . '_country' ]   = $address['country'];
		$_POST[ $type . '_postcode' ]  = $address['postcode'];

		wc_add_notice( __( 'Address validated.', 'woocommerce-avatax' ), 'success' );
	}


	/**
	 * Display a "pending calculation" message on the cart page when displaying a single tax total.
	 *
	 * @since 1.2.1
	 * @param string $html the tax total HTML
	 * @return string
	 */
	public function adjust_single_tax_total_html( $html ) {

		$cart  = WC()->cart;
		$taxes = $cart->get_cart_contents_taxes();

		if ( empty( $taxes ) && wc_avatax()->get_tax_handler()->override_wc_rates() ) {

			if ( is_cart() ) {
				$html = esc_html( $this->get_cart_calculation_message() );
			} elseif ( is_checkout() && $this->address_validation_required() && ! WC()->session->get( 'wc_avatax_address_validated', false ) ) {
				$html = esc_html__( 'Taxes will be calculated after you validate your address', 'woocommerce-avatax' );
			}
		}

		return $html;
	}


	/**
	 * Display a "pending calculation" message on the cart page when taxes are itemized.
	 *
	 * @since 1.2.1
	 */
	public function display_cart_calculation_message() {

		$taxes = Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_cart_contents_taxes() : WC()->cart->taxes;

		if ( ! is_cart() || ! wc_avatax()->get_tax_handler()->override_wc_rates() || ! empty( $taxes ) ) {
			return;
		}

		/** This filter is documented in woocommerce-avatax/woocommerce-avatax.php */
		$title = apply_filters( 'wc_avatax_tax_label', WC()->countries->tax_or_vat() );

		echo '<tr class="tax-total">';
			echo '<th>' . esc_html( $title ) . '</th>';
			echo '<td data-title="' . esc_attr( $title ) . '">' . esc_html( $this->get_cart_calculation_message() ) . '</td>';
		echo '</tr>';
	}


	/**
	 * Get the "pending calculation" message for the cart page.
	 *
	 * @since 1.2.1
	 * @return string
	 */
	protected function get_cart_calculation_message() {

		/**
		 * Filter the cart pending tax calculation message.
		 *
		 * @since 1.2.1
		 * @param string $message
		 */
		return apply_filters( 'wc_avatax_cart_message', __( 'Taxes will be calculated at checkout', 'woocommerce-avatax' ) );
	}


	/**
	 * Add the VAT field to the checkout billing fields.
	 *
	 * @since 1.0.0
	 * @param array $fields The existing checkout fields.
	 * @return array $fields The checkout fields.
	 */
	public function add_checkout_vat_field( $fields ) {

		/**
		 * Filter the VAT ID checkout field label.
		 *
		 * @since 1.0.0
		 * @param string $label The VAT ID checkout field label.
		 */
		$label = apply_filters( 'wc_avatax_vat_id_field_label', __( 'VAT ID', 'woocommerce-avatax' ) );

		$fields['billing_wc_avatax_vat_id'] = [
			'label' => $label,
			'class' => [ 'form-row-wide' ],
		];

		return $fields;
	}


	/**
	 * Determines if address validation is required.
	 *
	 * @since 1.6.4
	 *
	 * @return bool
	 */
	public function address_validation_required() {

		// Skip address validation if cart contains only virtual products with $0 amount
		if ( $this->cart_contains_only_virtual_products_with_zero_amount() ) {
			return false;
		}

		/**
		 * Filters whether address validation is required.
		 *
		 * @since 1.6.4
		 *
		 * @param bool $required whether address validation is required
		 */
		return $this->address_validation_available() && (bool) apply_filters( 'wc_avatax_address_validation_required', ( 'yes' === get_option( 'wc_avatax_enable_address_validation' ) ) );
	}


	/**
	 * Determine if address validation is available at checkout.
	 *
	 * @since 1.0.0
	 *
	 * @return bool $enabled Whether address validation is available at checkout.
	 */
	public function address_validation_available() {

		$countries = $this->get_address_validation_countries();

		/**
		 * Filters whether address validation is available.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $required whether address validation is available
		 */
		return $this->address_validation_enabled() && (bool) apply_filters( 'wc_avatax_address_validation_available', in_array( WC()->customer->get_shipping_country(), $countries ) );
	}


	/**
	 * Determine if address validation is enabled.
	 *
	 * @since 1.0.0
	 * @return bool $enabled Whether address validation is enabled.
	 */
	public function address_validation_enabled() {

		/**
		 * Filter whether address validation is enabled.
		 *
		 * @since 1.0.0
		 * @param bool $enabled Whether address validation is enabled.
		 */
		return (bool) apply_filters( 'wc_avatax_enable_address_validation', ( 'yes' === get_option( 'wc_avatax_enable_address_validation' ) ) );
	}

	/**
	 * Determine if Collect business VAT ID is enabled.
	 *
	 * @since 1.0.0
	 * @return bool $enabled Whether Collect business VAT ID is enabled.
	 */
	public function collect_vat_enabled() {

		/**
		 * Filter whether Collect business VAT ID is enabled.
		 *
		 * @since 1.0.0
		 * @param bool $enabled Whether Collect business VAT ID is enabled.
		 */
		return (bool) apply_filters( 'wc_avatax_enable_vat', ( 'yes' === get_option( 'wc_avatax_enable_vat', 'no' ) ) );
	}

    /**
     * Determines if the cart contains only virtual products with zero amount.
     *
     * This checks both the final line total (after discounts) and the original
     * line subtotal (before discounts). If either has a value > 0, address
     * validation is required.
     *
     * Returns false before the wp_loaded action or when the cart is unavailable, so
     * WooCommerce is not asked for the cart too early.
     *
     * @since 1.0.0
     * @return bool $is_virtual_zero Whether cart contains only virtual products with $0 amount.
     */
	public function cart_contains_only_virtual_products_with_zero_amount() {

		if ( ! did_action( 'wp_loaded' ) || ! function_exists( 'WC' ) || ! WC() || ! WC()->cart ) {
			return false;
		}

		$cart = WC()->cart;

		if ( $cart->is_empty() ) {
			return false;
		}

		$cart_contents = $cart->get_cart_contents();

		if ( empty( $cart_contents ) ) {
			return false;
		}

		$has_non_virtual = false;
		$has_non_zero_amount = false;

		foreach ( $cart_contents as $item ) {
			$product = $item['data'] ?? null;

			if ( $product instanceof WC_Product ) {
				// Check if product is non-virtual
				if ( ! $product->is_virtual() ) {
					$has_non_virtual = true;
					break;
				}

				// Check if product has non-zero amount (either final total or original subtotal)
				$line_total = $item['line_total'] ?? 0;
				$line_subtotal = $item['line_subtotal'] ?? 0;
				if ( $line_total > 0 || $line_subtotal > 0 ) {
					$has_non_zero_amount = true;
					break;
				}
			}
		}

		// Return true only if cart has no non-virtual products AND no non-zero amounts
		return ! $has_non_virtual && ! $has_non_zero_amount;
	}



	/**
	 * Get the countries that support address validation at checkout.
	 *
	 * @since 1.0.0
	 * @return array Countries that support address validation.
	 */
	public function get_address_validation_countries()
	{
		$nexusCountries = wc_avatax()->get_landed_cost_handler()->get_supported_countries();
		// Only include US/CA if they are actually present in the nexus
		$countries = array_values(array_intersect( ['US', 'CA'], $nexusCountries));

		/**
		 * Filter the countries that support address validation.
		 *
		 * @since 1.0.0
		 * @param array $countries The countries that support address validation.
		 */
		return (array) apply_filters('wc_avatax_address_validation_countries', $countries);
	}

	/**
	 * Display ECM links
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 */

    public function display_ecm_links()
    {
        echo '<tr class="ecm-purchase-link">';
        echo '<th>' . '<b>' . "Tax Exemptions" . '</b>' . '<br>' . wp_kses_post($this->get_purchase_tax_exmpt_link()) . '<br>' . wp_kses_post($this->get_manage_certificate_link()) . '</th>';
        echo '</tr>';
    }

	protected function get_purchase_tax_exmpt_link() {

		/**
		 * Get Purchase tax exempt link.
		 *
		 * @since 2.6.0
		 * @param string $message
		 */
		$user_id = get_current_user_id();
		$exposure_zones = wc_avatax()->get_exposure_zones();
		if($user_id ==0)
		{
			$myaccounturl = rtrim(get_permalink(get_option('woocommerce_myaccount_page_id') ), "/");
			$checkouturl = rtrim(get_permalink(get_option('woocommerce_checkout_page_id') ), "/");
			$url = $myaccounturl."?redirect_to=".$checkouturl;
			return apply_filters( 'wc_avatax_ecm_links', sprintf( __( '%1$sAdd Certificates%2$s', 'woocommerce-avatax' ),
			'<a style="display:block;" href='.$url.'>',
			'</a>'
			) );
		}
		else
		{
			include_once(wc_avatax()->get_plugin_path() . '/src/admin/views/html-certificate-details-popup.php');
			return apply_filters( 'wc_avatax_ecm_links', sprintf( __( '%1$sAdd Certificates%2$s', 'woocommerce-avatax' ),
			'<a style="display:block;" id ="cert_link" href="#">',
			'</a>'
			) );

		}

	}
	/**
	 * Get manage certificate link
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	protected function get_manage_certificate_link() {

		/**
		 * Get Purchase manage certificates link.
		 *
		 * @since  2.6.0
		 * @param string $message
		 */
		$url = (get_permalink( get_option('woocommerce_myaccount_page_id') ). 'tax-certificate');
		return apply_filters( 'wc_avatax_ecm_links', sprintf( __( '%1$sManage existing certificates%2$s', 'woocommerce-avatax' ),
		'<a href='.$url.'>',
		'</a>','<br>'
	) );
	}

	/**
	 * Display certificate table
	 *
	 * @since 2.6.0
	 * @codeCoverageIgnore
	 * @return array
	 */
	public function display_ecm_table()
	{
		add_action( 'init', 'register_tax_certificate_endpoint');
			/**
			 * Register New Endpoint.
			 *
			 * @return void.
			 */
			function register_tax_certificate_endpoint() {
				add_rewrite_endpoint( 'tax-certificate', EP_ROOT | EP_PAGES );

				$rules = get_option( 'rewrite_rules' );

				//Check if rewrite rule exists
				$rule_exists = false;
				foreach ($rules AS $key => $value) {
					if (stristr($value, 'tax-certificate') === FALSE) {
						continue;
					} else {
						$rule_exists = true;
					}
				}

				//if rule not exists flush the rewrite rules
				if ( !$rule_exists) {
					flush_rewrite_rules();
				}
			}
			add_filter( 'query_vars', 'tax_certificate_query_vars' );
			/**
			 * Add new query var.
			 *
			 * @param array $vars vars.
			 *
			 * @return array An array of items.
			 */
			function tax_certificate_query_vars( $vars ) {

				$vars[] = 'tax-certificate';
				return $vars;
			}
			add_filter( 'woocommerce_account_menu_items', 'add_tax_certificate_tab' );
			/**
			 * Add New tab in my account page.
			 *
			 * @param array $items myaccount Items.
			 *
			 * @return array Items including New tab.
			 */
			function add_tax_certificate_tab( $items ) {

				$items['tax-certificate'] = 'Tax Certificate';
				return $items;
			}

			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			if ( ! empty( $request_uri ) && str_contains( $request_uri, 'tax-certificate' ) )
			{
				wp_enqueue_style( 'dashicons' );
				$userId = get_current_user_id();
				$user_data = wc_avatax()->get_user_data($userId);
				$customer_certificates = wc_avatax()->get_certificate_options($user_data);
				$has_certificates = (!empty($customer_certificates)) ? true : false;
				$exposure_zones = wc_avatax()->get_exposure_zones();
				add_action( 'woocommerce_account_tax-certificate_endpoint', function() use ($customer_certificates,$has_certificates,$exposure_zones,$userId ) {
					include_once(wc_avatax()->get_plugin_path() . '/src/frontend/templates/certificates.php');
					include_once(wc_avatax()->get_plugin_path() . '/src/admin/views/html-certificate-details-popup.php');
				} );

				function wc_get_account_certificates_columns() {
					/**
					 * Filters the array of My Account > Certificates columns.
					 *
					 * @since 2.6.0
					 * @param array $columns Array of column labels keyed by column IDs.
					 */
					return apply_filters(
						'wc_get_account_certificates_columns',
						array(
							'certificate-state'  => __( 'State', 'woocommerce' ),
							'certificate-signedDate'    => __( 'SignedDate', 'woocommerce' ),
							'certificate-expirationDate'  => __( 'ExpirationDate', 'woocommerce' ),
							'certificate-status'   => __( 'Status', 'woocommerce' ),
							'certificate-ecm-validity' => __( 'Validity', 'woocommerce-avatax' ),
							'certificate-view' => __( 'View', 'woocommerce' ),
							'certificate-invalidate' => __( 'Invalidate', 'woocommerce' ),
						)
					);
				}
			}
	}
	/**
	 * Determines if classic checkout or checkout block  is required.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function is_checkout_block() {
		return WC_Blocks_Utils::has_block_in_page( wc_get_page_id('checkout'), 'woocommerce/checkout');
	}

	/**
	 * Determines if classic cart or cart block  is required.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_cart_block() {
		return WC_Blocks_Utils::has_block_in_page(wc_get_page_id('cart'), 'woocommerce/cart');
	}

}
