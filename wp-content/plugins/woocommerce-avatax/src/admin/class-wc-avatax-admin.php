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

/**
 * Set up the AvaTax admin.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Admin {


	/** @var \WC_AvaTax_Settings settings handler */
	public $settings;

	/** @var \WC_AvaTax_Elr_Settings settings handler */
	public $elr_settings;

	/** @var \WC_AvaTax_Business_License_Settings settings handler */
	public $business_license_settings;

	/** @var \WC_AvaTax_Reconciliation reconciliation section handler */
	public $reconciliation;

	/** @var \WC_AvaTax_Product_Admin product handler */
	public $product;


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->includes();

		$this->add_hooks();
	}


	/**
	 * Adds action and filter hooks.
	 *
	 * @since 3.8.0
	 */
	private function add_hooks()
	{

		// load admin scripts and styles
		add_action('admin_enqueue_scripts', array($this,'enqueue_scripts_styles'));

		// load modal template
		add_action('admin_footer', [ $this,'output_sync_modal_template']);
		add_action('admin_footer', [ $this,'output_disconnect_modal_template']);
		add_action('admin_footer', [ $this,'output_confirmation_modal_template']);

		// add the product category tax code fields
		add_action('product_cat_add_form_fields',  array($this,'add_category_code_fields'));
		add_action('product_cat_edit_form_fields', array($this,'edit_category_code_fields'));

		// save the product category tax code fields
		// the same is done when creating a new category from WC_AvaTax_AJAX::save_category_tax_code_field
		add_action('edit_product_cat', array($this,'save_category_code_fields'));

		// Add product category tax code column
		add_filter('manage_edit-product_cat_columns',  array($this,'add_category_code_columns'));
		add_filter('manage_product_cat_custom_column', array($this,'display_category_code_columns'), 10, 3);

		// Add the VAT ID information to the order billing information
		add_action('woocommerce_admin_billing_fields', array($this,'add_admin_order_vat_id'));

		// Add the Order Invoice message information to the order billing information section
		add_action('woocommerce_admin_billing_fields', array($this,'add_admin_order_invoice_messages'));

		// Hide our custom line item meta from the order admin
		add_filter('woocommerce_hidden_order_itemmeta', array($this,'hide_order_item_meta'));

		// Add the item tax rate input to the order admin
		add_action('woocommerce_admin_order_item_values', array($this,'add_order_item_tax_rate'), 10, 3);

		// add a hidden input to the order items form to indicate landed cost for an order
		add_action('woocommerce_order_item_add_action_buttons', array($this,'add_order_calculated_field'));

		// Add a "Send to Avalara" action to the order action options if calculation is enabled
		if (wc_avatax()->get_tax_handler()->is_available()) {
			add_action('woocommerce_order_actions', array($this,'add_order_action'));
		}

		// Add and save the customer tax settings fields
		add_action('show_user_profile', array($this,'add_tax_meta_fields'), 15, 1);
		add_action('edit_user_profile', array($this,'add_tax_meta_fields'), 15, 1);

		if (get_option('wc_avatax_enable_ecm','no') == 'yes') {
			add_action('show_user_profile', array($this,'add_certificate_table'), 15, 1);
			add_action('edit_user_profile', array($this,'add_certificate_table'), 15, 1);
		}
		add_action('personal_options_update', array($this,'save_tax_meta_fields'));
		add_action('edit_user_profile_update', array($this,'save_tax_meta_fields'));
		add_action('edit_user_profile_update', array($this,'save_customer_avatax'));
		add_action('personal_options_update', array($this,'save_customer_avatax'));

		// Adds custom styles to match toggle button color with admin button colors
		add_action('admin_head', array($this,'custom_styles'), 100);
	}


	/**
	 * Include the admin files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// settings handler
		require_once( wc_avatax()->get_plugin_path() . '/src/admin/class-wc-avatax-settings.php' );
		$this->settings = new WC_AvaTax_Settings;

		// elr settings handler
		require_once( $this->get_plugin()->get_plugin_path() . '/src/e-invoicing/class-wc-avatax-elr-settings.php' );
		$elr_settings = new WC_AvaTax_Elr_Settings();

		// business license settings handler
		// Include view template (not a class, so require_once is appropriate)
		// @SuppressWarnings(php:S4833) - Template files must be included, not imported via namespace
		require_once(
			$this->get_plugin()->get_plugin_path()
			. '/src/business_license/class-wc-avatax-business-license-settings.php'
		);
		$this->business_license_settings = new WC_AvaTax_Business_License_Settings();

		// reconciliation section handler
		require_once(
			$this->get_plugin()->get_plugin_path()
			. '/src/reconciliation/class-wc-avatax-reconciliation.php'
		);
		$this->reconciliation = new WC_AvaTax_Reconciliation();

		// product handler
		require_once( wc_avatax()->get_plugin_path() . '/src/admin/class-wc-avatax-product-admin.php' );
		$this->product = new WC_AvaTax_Product_Admin;

	}


	/**
	 * Load the admin scripts and styles.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current screen suffix
	 */
	public function enqueue_scripts_styles( $hook_suffix ) {
		if ( $this->is_avalara_tab() ) {
			wp_enqueue_style(
				'wc-avatax-avalara-common',
				wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-avalara-common.css',
				[],
				WC_AvaTax::VERSION
			);
		}
		if ( $this->should_enqueue_main_admin_assets( $hook_suffix ) ) {
			$this->enqueue_main_admin_assets( $hook_suffix );
		}
		if ( wc_avatax()->is_plugin_elr_settings() ) {
			$this->enqueue_elr_settings_assets();
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && wc_get_page_screen_id( 'shop_order' ) === $screen->id ) {
			$this->enqueue_shop_order_elr_assets();
		}
		if ( $this->should_enqueue_plugins_page_assets( $hook_suffix ) ) {
			$this->enqueue_plugins_page_assets();
		}
	}

	/**
	 * Whether to enqueue main admin scripts/styles (settings, user, product, order screens).
	 *
	 * @param string $hook_suffix The current screen suffix.
	 * @return bool
	 */
	private function should_enqueue_main_admin_assets( $hook_suffix ) {
		$is_user_screen    = ( 'user-edit.php' === $hook_suffix || 'profile.php' === $hook_suffix );
		$post_type         = get_post_type();
		$is_product_screen = ( 'product' === $post_type
			&& in_array( $hook_suffix, [ 'edit.php', 'post.php' ], true ) );
		$is_order_screen   = ( 'shop_order' === $post_type
			&& in_array( $hook_suffix, [ 'post.php', 'post-new.php' ], true ) );

		return wc_avatax()->is_plugin_settings() || $is_user_screen || $is_product_screen || $is_order_screen;
	}

	/**
	 * Enqueue scripts and styles for main admin screens (settings, user, product, order).
	 *
	 * @param string $hook_suffix The current screen suffix.
	 */
	private function enqueue_main_admin_assets( $hook_suffix ) {
		parse_str( (string) parse_url( wp_get_referer(), PHP_URL_QUERY ), $args );
		parse_str( (string) sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ?? '' ) ), $array );

		wp_enqueue_script( 'wc-backbone-modal', null, [ 'backbone' ] );


		$this->enqueue_main_admin_elr_scripts();

		if ( 'yes' === get_option( 'wc_avatax_enable_ecm', 'no' ) ) {
			$this->enqueue_main_admin_ecm_scripts();
		}

		$ecm_enabled = ( 'yes' === get_option( 'wc_avatax_enable_ecm', 'no' ) );
		$is_user_hook = in_array( $hook_suffix, [ 'user-edit.php', 'profile.php' ], true );
		$exposure_zones = ( $ecm_enabled && $is_user_hook ) ? wc_avatax()->get_exposure_zones() : [];
		wp_localize_script( 'wc-avatax-admin', 'wc_avatax_admin', [
			'address_nonce'       => wp_create_nonce( 'wc_avatax_validate_origin_address' ),
			'certificate_nonce'  => wp_create_nonce( 'wc_avatax_invite_customer_certificate' ),
			'assets_url'         => esc_url( wc_avatax()->get_framework_assets_url() ),
			'ajax_url'           => admin_url( 'admin-ajax.php' ),
			'sync_state'         => wc_avatax()->get_landed_cost_sync_handler()->is_syncing_active() ? 'on' : 'off',
			'sync_nonce'         => wp_create_nonce( 'wc_avatax_toggle_cross_border_sync' ),
			'resync_nonce'       => wp_create_nonce( 'wc_avatax_resync_products_with_errors' ),
			'refund_ays'         => __(
				'Heads up! AvaTax does not support tax-rate-based refunds. If taxes are refunded partially, '
				. 'the amount will be distributed across all tax rates.',
				'woocommerce-avatax'
			),
			'product_sync_modal' => [ 'exposure_zones' => $exposure_zones ],
		] );
		wp_localize_script( 'wc-avatax-admin-elr', 'wc_avatax_admin_elr', [
			'ajax_url'           => admin_url( 'admin-ajax.php' ),
			'nonce'              => wp_create_nonce( 'wc_avatax_elr_disconnect' ),
			'submit_map_nonce'   => wp_create_nonce('wc_avatax_submit_map_perform'),
			'ndi_portal_url'     => wc_avatax()->get_ndi_handler()->get_ndi_portal_url(),
		] );
		$plugin_url = wc_avatax()->get_plugin_url();
		wp_enqueue_style(
			'wc-avatax-admin',
			$plugin_url . \WC_AvaTax::ADMIN_CSS_PATH,
			WC_AvaTax::VERSION
		);
	}

	/**
	 * Enqueue ELR-related scripts for main admin screens when ELR is enabled.
	 */
	private function enqueue_main_admin_elr_scripts() {
		$url   = wc_avatax()->get_plugin_url();
		$deps  = [ 'jquery', 'wc-backbone-modal' ];
		wp_enqueue_script( 'wc-avatax-admin', $url . '/assets/js/admin/wc-avatax-admin.min.js', $deps, \WC_AvaTax::VERSION, true );
		wp_enqueue_script( 'wc-avatax-admin-elr', $url . '/assets/js/admin/wc-avatax-admin-elr.js', $deps, \WC_AvaTax::VERSION, true );
		if ( wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled() ) {
			wp_enqueue_script(
				'wc-avatax-admin-jsontree',
				$url . '/assets/js/admin/wc-avatax-admin-jsontree.min.js',
				$deps,
				\WC_AvaTax::VERSION,
				true
			);
			wp_enqueue_style(
				'wc-avatax-admin-jsontree',
				$url . '/assets/css/admin/wc-avatax-admin-jsontree.min.css',
				WC_AvaTax::VERSION
			);
			wp_localize_script( 'wc-avatax-admin-jsontree', 'wc_avatax_admin_jsontree', [
				'schema'     => json_encode( wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema() ),
				'savedSchema' => json_encode( wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema() ),
			] );
		}
	}

	/**
	 * Enqueue ECM (certificate) scripts for main admin screens when ECM is enabled.
	 */
	private function enqueue_main_admin_ecm_scripts() {
		$env        = get_option( 'wc_avatax_api_environment' );
		$gencert_url = ( 'development' === $env ) ? 'https://sbx.certcapture.com/gencert2/js' : 'https://app.certcapture.com/gencert2/js';
		$deps       = [ 'jquery', 'wc-backbone-modal' ];
		wp_enqueue_script( 'wc-avatax-admin-gencert', $gencert_url, $deps, \WC_AvaTax::VERSION, true );
		$misc_url = wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin-misc.min.js';
		wp_enqueue_script( 'wc-avatax-admin-misc', $misc_url, $deps, \WC_AvaTax::VERSION, true );
		wp_localize_script( 'wc-avatax-admin-misc', 'wc_avatax_admin_misc', [
			'select_zone'                       => __( 'Please select exposure zone.', 'woocommerce' ),
			'enter_billing_address'              => __(
				'Please enter billing email address and save the details.',
				'woocommerce'
			),
			'enter_billing_address_different'    => __(
				'Entered billing email address is different from the billing email currently saved in DB. '
				. 'Press OK to proceed or Cancel to change the billing email address.',
				'woocommerce'
			),
			'gencert_generic_error'              => __(
				"The page you're looking for couldn't be found. Please contact Avalara Support.",
				'woocommerce'
			),
			'enter_billing_address_confirmation' => __(
				'Please enter billing address details and save. There might be possibility that you have entered '
				. 'the details but not it is not saved.',
				'woocommerce'
			),
			'confirm_invalidate_certificate'     => __(
				"Are you sure you'd like to invalidate this certificate?",
				'woocommerce'
			),
			'submit_to_stack'                    => 'yes' === get_option( 'wc_avatax_certificate_submit_to_queue', 'no' ),
		] );
	}

	/**
	 * Enqueue scripts and styles for the ELR settings screen.
	 */
	private function enqueue_elr_settings_assets() {
		$url  = wc_avatax()->get_plugin_url();
		$deps = [ 'jquery', 'wc-backbone-modal' ];
		if ( wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled() ) {
			wp_enqueue_script(
				'wc-avatax-admin-jsontree',
				$url . '/assets/js/admin/wc-avatax-admin-jsontree.min.js',
				$deps,
				\WC_AvaTax::VERSION,
				true
			);
			wp_enqueue_style(
				'wc-avatax-admin-jsontree',
				$url . '/assets/css/admin/wc-avatax-admin-jsontree.min.css',
				WC_AvaTax::VERSION
			);
		}
		wp_enqueue_script( 'wc-backbone-modal', null, [ 'backbone' ] );
		wp_enqueue_script( 'wc-avatax-admin', $url . '/assets/js/admin/wc-avatax-admin.min.js', $deps, \WC_AvaTax::VERSION, true );
		wp_enqueue_script( 'wc-avatax-admin-elr', $url . '/assets/js/admin/wc-avatax-admin-elr.js', $deps, \WC_AvaTax::VERSION, true );
		wp_enqueue_script( 'wc-avatax-admin-elr1', $url . '/assets/js/admin/wc-avatax-elr.min.js', $deps, \WC_AvaTax::VERSION, true );
		wp_localize_script( 'wc-avatax-admin-elr', 'wc_avatax_admin_elr', [
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'disconnect_nonce' => wp_create_nonce( 'wc_avatax_elr_disconnect' ),
			'submit_map_nonce' => wp_create_nonce('wc_avatax_submit_map_perform'),
			'ndi_portal_url'   => wc_avatax()->get_ndi_handler()->get_ndi_portal_url(),
		] );
		wp_enqueue_style( 'wc-avatax-admin-elr', $url . \WC_AvaTax::ADMIN_CSS_ELR_PATH, WC_AvaTax::VERSION );
		wp_enqueue_style( 'wc-avatax-admin-elr', $url . \WC_AvaTax::ADMIN_CSS_PATH, WC_AvaTax::VERSION );
	}

	/**
	 * Enqueue ELR scripts and styles for the shop order screen.
	 */
	private function enqueue_shop_order_elr_assets() {
		$order_id   = isset( $_REQUEST['id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) : '';
		$plugin_url = wc_avatax()->get_plugin_url();
		$elr_script_url = $plugin_url . '/assets/js/admin/wc-avatax-elr.min.js';
		wp_enqueue_script( 'wc-avatax-admin-elr', $elr_script_url, [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
		wp_localize_script( 'wc-avatax-admin-elr', 'wc_avatax_admin_elr', [
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'disconnect_nonce' => wp_create_nonce( 'wc_avatax_elr_disconnect' ),
			'submit_map_nonce' => wp_create_nonce('wc_avatax_submit_map_perform'),
			'ndi_portal_url'   => wc_avatax()->get_ndi_handler()->get_ndi_portal_url(),
			'order_id'         => $order_id,
		] );
		wp_enqueue_style( 'wc-avatax-admin-elr', $plugin_url . \WC_AvaTax::ADMIN_CSS_ELR_PATH, WC_AvaTax::VERSION );
		wp_enqueue_style( 'wc-avatax-admin-elr', $plugin_url . \WC_AvaTax::ADMIN_CSS_PATH, WC_AvaTax::VERSION );
	}

	/**
	 * Whether to enqueue assets on the plugins page (deactivate alert when ELR is configured).
	 *
	 * @param string $hook_suffix The current screen suffix.
	 * @return bool
	 */
	private function should_enqueue_plugins_page_assets( $hook_suffix ) {
		return 'plugins.php' === $hook_suffix && wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api();
	}

	/**
	 * Enqueue scripts and styles for the plugins page (deactivate alert).
	 */
	private function enqueue_plugins_page_assets() {
		$plugin_url = wc_avatax()->get_plugin_url();
		wp_enqueue_script( 'wc-backbone-modal', null, [ 'backbone' ] );
		$deactivate_url = $plugin_url . '/assets/js/admin/deactivate-alert.js';
		wp_enqueue_script( 'deactivate-alert', $deactivate_url, [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
		wp_enqueue_style( 'wc-avatax-admin-elr', $plugin_url . \WC_AvaTax::ADMIN_CSS_ELR_PATH, WC_AvaTax::VERSION );
	}

	/**
	 * Adds custom styles to match toggle button color with admin button colors
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function custom_styles()
	{
		global $_wp_admin_css_colors;
		$color_scheme = get_user_option( 'admin_color' );
		$color = $_wp_admin_css_colors[ $color_scheme ]->colors[2];
		echo '<style>  :root {--wc-avatax-color: '.esc_attr($color).';}</style>';
	}

	/**
	 * Includes an export modal template in the plugin settings pages.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function output_sync_modal_template() {

		if ( ! wc_avatax()->is_plugin_settings() ) {
			return;
		}

		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-sync-modal.php' );
	}

	/**
	 * Includes an export modal template in the plugin settings pages.
	 *
	 * @internal
	 *
	 * @since 2.10.0
	 */
	public function output_disconnect_modal_template() {
		// Get the current screen
		$screen = get_current_screen();

		if ( wc_avatax()->is_plugin_elr_settings() || ($screen->id === 'plugins' && wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api())) {
			include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-elr-disconnect-confirm-modal.php' );
		}
	}

	/**
	 * Includes an elr schema send confirmation modal template in the elr config pages.
	 *
	 * @internal
	 *
	 * @since 2.10.0
	 */
	public function output_confirmation_modal_template() {

		if ( ! wc_avatax()->is_plugin_elr_settings() ) {
			return;
		}

		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-elr-confirmation-modal.php' );
	}


	/**
	 * Adds landed costs settings.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 * @deprecated 1.16.0
	 *
	 * @param array|mixed $settings
	 * @return array|mixed
	 */
	public function add_settings_pages( $settings ) {

		wc_deprecated_function( __METHOD__, '1.16.0' );

		return $settings;
	}


	/**
	 * Display the tax code fields on the add product category screen.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_category_code_fields() {

		// tax code
		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-add-category-tax-code.php' );
	}


	/**
	 * Display the tax code fields on the edit product category screen.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param object $term current term object
	 */
	public function edit_category_code_fields( $term ) {

		$tax_code = get_term_meta( $term->term_id, 'wc_avatax_tax_code', true );

		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-edit-category-tax-code.php' );
	}


	/**
	 * Save the category tax code fields.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id current term ID
	 */
	public function save_category_code_fields( $term_id ) {

		$tax_code = sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'wc_avatax_category_tax_code' ) );

		update_term_meta( $term_id, 'wc_avatax_tax_code', $tax_code );
	}


	/**
	 * Add the tax code columns to category admin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns existing category columns
	 * @return array $columns
	 */
	public function add_category_code_columns( $columns ) {

		$columns['tax_code'] = __( 'Tax Code', 'woocommerce-avatax' );

		return $columns;
	}


	/**
	 * Display the tax code in its column.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $content column content
	 * @param string $column current column slug
	 * @param int $id category ID
	 * @return string $columns amended column content
	 */
	public function display_category_code_columns( $content, $column, $id ) {

		if ( 'tax_code' === $column ) {
			$content .= get_term_meta( $id, 'wc_avatax_tax_code', true );
		}

		return $content;
	}


	/**
	 * Add the VAT ID information to the order billing information.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields The existing billing fields
	 * @return array
	 */
	public function add_admin_order_vat_id( $fields ) {

		$fields['wc_avatax_vat_id'] = array(
			'label' => __( 'VAT ID', 'woocommerce-avatax' ),
		);

		return $fields;
	}


	/**
	 * Add the invoice messages to the order billing information.
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 *
	 * @param array $fields The existing billing fields
	 * @return array
	 */
	public function add_admin_order_invoice_messages( $fields ) {
		$fields['wc_avatax_order_messages'] = array(
			'label' => __( 'VAT message', 'woocommerce-avatax' ),
			'custom_attributes' => array( 'disabled' => true)
		);
		return $fields;
	}

	/**
	 * Hide our custom line item meta from the order admin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $hidden_meta The hidden line item keys.
	 * @return array $hidden_meta
	 */
	public function hide_order_item_meta( $hidden_meta ) {

		return wc_avatax()->wc_avatax_utilities()->hide_order_item_meta($hidden_meta);
	}


	/**
	 * Add the item tax rate input to the order admin.
	 *
	 * @since 1.0.0
	 * @param WC_Product $product The product object.
	 * @param array $item The item meta.
	 * @param int $item_id The item ID.
	 */
	public function add_order_item_tax_rate( $product, $item, $item_id ) {

		// Only add this value if a tax rate was set for the item
		if ( ( ! is_array( $item ) && ! $item instanceof WC_Order_Item_Tax ) || empty( $item['wc_avatax_rate'] ) ) {
			return;
		}

		echo '<input
				class="wc_avatax_refund_line_rate"
				name="wc_avatax_refund_line_rate[' . absint( $item_id ) . ']"
				value="' . (float) $item['wc_avatax_rate'] . '"
				type="hidden"
			/>';
	}


	/**
	 * Adds a hidden input to the order items form to indicate AvaTax calculation for an order.
	 *
	 * This primarily used to display a warning to users trying to partially refund AvaTax transactions, as that's currently not supported.
	 *
	 * @internal
	 *
	 * @since 1.6.4
	 *
	 * @param \WC_Order $order order object
	 */
	public function add_order_calculated_field( $order ) {

		?>
		<input name="wc_avatax_calculated" type="hidden" value="<?php echo wc_avatax()->get_order_handler()->is_order_posted( $order ) ? 'yes' : 'no'; ?>"/>
		<?php
	}


	/**
	 * Add a "Send to Avalara" action to the order action options.
	 *
	 * @since 1.0.0
	 * @global WC_Order $theorder The current order object.
	 * @param array $actions The available order actions.
	 * @return array $actions
	 */
	public function add_order_action( $actions ) {
		global $theorder;

		// Only add the action if the order is ready for sending
		if ( wc_avatax()->get_order_handler()->is_order_ready( $theorder ) ) {
			$actions['wc_avatax_send'] = __( 'Send to Avalara', 'woocommerce-avatax' );
		}

		return $actions;
	}


	/**
	 * Adds the customer tax settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_User $user user object
	 *
	 * @codeCoverageIgnore This method cannot be effectively unit tested in isolation due to:
	 *                     1. Static array initialization with translation function calls (__())
	 *                     2. PHP template includes that contain additional executable code
	 *                     3. Tight coupling with WordPress rendering functions
	 *                     The business logic (caching, API calls, error handling) is tested
	 *                     via integration tests. Pure unit test coverage would require
	 *                     architectural refactoring to separate business logic from presentation.
	 */
	public function add_tax_meta_fields( $user ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// base entity/use codes and their descriptions
		// below we try and get the same codes from the API, but this acts as a fallback in case there is a failure
		// note: O is intentionally absent
		$entity_use_codes = array(
			'A' => __( 'Federal government', 'woocommerce-avatax' ),
			'B' => __( 'State government', 'woocommerce-avatax' ),
			'C' => __( 'Tribe / Status Indian / Indian Band', 'woocommerce-avatax' ),
			'D' => __( 'Foreign diplomat', 'woocommerce-avatax' ),
			'E' => __( 'Charitable or benevolent organization', 'woocommerce-avatax' ),
			'F' => __( 'Religious organization', 'woocommerce-avatax' ),
			'G' => __( 'Resale', 'woocommerce-avatax' ),
			'H' => __( 'Commercial agricultural production', 'woocommerce-avatax' ),
			'I' => __( 'Industrial production / manufacturer', 'woocommerce-avatax' ),
			'J' => __( 'Direct pay permit', 'woocommerce-avatax' ),
			'K' => __( 'Direct mail', 'woocommerce-avatax' ),
			'L' => __( 'Other', 'woocommerce-avatax' ),
			'M' => __( 'Educational organization', 'woocommerce-avatax' ),
			'N' => __( 'Local government', 'woocommerce-avatax' ),
			'P' => __( 'Commercial aquaculture', 'woocommerce-avatax' ),
			'Q' => __( 'Commercial Fishery', 'woocommerce-avatax' ),
			'R' => __( 'Non-resident', 'woocommerce-avatax' ),
			'MED1' => __( 'US MDET with exempt sales tax', 'woocommerce-avatax' ),
			'MED2' => __( 'US MDET with taxable sales tax', 'woocommerce-avatax' ),
		);

		// Get cached entity use codes from wp_options
		$cached_api_codes = get_option( 'wc_avatax_entity_use_codes', '' );
		$api_codes = array();

		// If option is blank, not present, or not an array, make API call and cache the result
		if ( empty( $cached_api_codes ) || ! is_array( $cached_api_codes ) ) {
			try {

				$response = wc_avatax()->get_api()->get_entity_use_codes();
				$api_codes = $response->get_codes();

				// Cache the API response in wp_options
				update_option( 'wc_avatax_entity_use_codes', $api_codes );

			} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

				if ( wc_avatax()->logging_enabled() ) {
					wc_avatax()->log( $exception->getMessage() );
				}

				//Logging error
				wc_avatax()->logger()->log_exception("Admin", "add_tax_meta_fields", $exception->getMessage(), $exception->getTraceAsString());
			}
		} else {
			// Use cached codes
			$api_codes = $cached_api_codes;
		}

		// Append the official code name to the nice label if found, otherwise just add to the list as-is
		foreach ( $api_codes as $code => $name ) {

			$label = isset( $entity_use_codes[ $code ] ) ? "{$entity_use_codes[ $code ]} ({$name})" : $name;

			$entity_use_codes[ $code ] = $label;
		}

		/**
		 * Filters the customer usage types.
		 *
		 * @since 1.0.0
		 *
		 * @param array $entity_use_codes entity/use codes, formatted as $code => $description
		 */
		$entity_use_codes = apply_filters( 'wc_avatax_customer_usage_types', $entity_use_codes );

		$selected_code = get_user_meta( $user->ID, 'wc_avatax_tax_exemption', true );

		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-edit-user-tax-fields.php' );

		/**
		 * Field wc_avatax_user_ior added hold seller importer of record data for user
		 *
		 * @since 2.3.0
		 *
		 */

		
		$selected_ior = get_user_meta( $user->ID, 'wc_avatax_user_ior', true );
		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-edit-user-ior-fields.php' );
	}

	/**
	 *Add certificate table
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	public function add_certificate_table( $user ) {
		if (! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		try {
			$userId = $user->ID;
			$user_data = wc_avatax()->get_user_data($userId);
			if(!empty($userId)){
				$certificateslist = wc_avatax()->get_certificate_options($user_data);
				$db_billing_email = $user_data['customerCode'];
				$isAdmin = "true";
				include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-certificate-details.php' );
				include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/Exemption/html-account-add-exemption.php' );
				include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-sync-modal.php' );
			}
		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $exception->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Admin", "add_certificate_table", $exception->getMessage(), $exception->getTraceAsString());
		}
	}
	/**
	 * Validate custoemer data
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function validation_of_fields($user_id){
		$user     = new stdClass();
		$user_id  = (int) $user_id;
		if ( $user_id ) {
			$update           = true;
			$user->ID         = $user_id;
			$userdata         = get_userdata( $user_id );
			$user->user_login = wp_slash( $userdata->user_login );
		} else {
			$update = false;
		}

		if ( ! $update && isset( $_POST['user_login'] ) ) {
			$user->user_login = sanitize_user( wp_unslash( $_POST['user_login'] ), true );
		}

		$pass1 = '';
		$pass2 = '';
		if ( isset( $_POST['pass1'] ) ) {
			$pass1 = trim( sanitize_text_field(wp_unslash($_POST['pass1'])) );
		}
		if ( isset( $_POST['pass2'] ) ) {
			$pass2 = trim( sanitize_text_field(wp_unslash($_POST['pass2'])) );
		}

		if ( isset( $_POST['role'] ) && current_user_can( 'promote_users' ) && ( ! $user_id || current_user_can( 'promote_user', $user_id ) ) ) {
			$new_role = sanitize_text_field( $_POST['role'] );

			// If the new role isn't editable by the logged-in user die with error.
			$editable_roles = get_editable_roles();
			if ( ! empty( $new_role ) && empty( $editable_roles[ $new_role ] ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to give users that role.' ), 403 );
			}
		}

		if ( isset( $_POST['email'] ) ) {
			$user->user_email = sanitize_text_field( wp_unslash( $_POST['email'] ) );
		}
		if ( isset( $_POST['nickname'] ) ) {
			$user->nickname = sanitize_text_field( $_POST['nickname'] );
		}

		//add_action('user_profile_update_errors',array( $this, 'get_user_errrors' ));
		$errors = new WP_Error();

		/* checking that username has been typed */
		if ( '' === $user->user_login ) {
			$errors->add( 'user_login', __( '<strong>Error:</strong> Please enter a username.' ) );
		}

		/* checking that nickname has been typed */
		if ( $update && empty( $user->nickname ) ) {
			$errors->add( 'nickname', __( '<strong>Error:</strong> Please enter a nickname.' ) );
		}

		// Check for blank password when adding a user.
		if ( ! $update && empty( $pass1 ) ) {
			$errors->add( 'pass', __( '<strong>Error:</strong> Please enter a password.' ), array( 'form-field' => 'pass1' ) );
		}

		// Check for "\" in password.
		if ( false !== strpos( wp_unslash( $pass1 ), '\\' ) ) {
			$errors->add( 'pass', __( '<strong>Error:</strong> Passwords may not contain the character "\\".' ), array( 'form-field' => 'pass1' ) );
		}

		// Checking the password has been typed twice the same.
		if ( ( $update || ! empty( $pass1 ) ) && $pass1 != $pass2 ) {
			$errors->add( 'pass', __( '<strong>Error:</strong> Passwords do not match. Please enter the same password in both password fields.' ), array( 'form-field' => 'pass1' ) );
		}

		if ( ! empty( $pass1 ) ) {
			$user->user_pass = $pass1;
		}

		if ( ! $update && isset( $_POST['user_login'] ) && ! validate_username( sanitize_text_field(wp_unslash($_POST['user_login']))	 ) ) {
			$errors->add( 'user_login', __( '<strong>Error:</strong> This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
		}

		if ( ! $update && username_exists( $user->user_login ) ) {
			$errors->add( 'user_login', __( '<strong>Error:</strong> This username is already registered. Please choose another one.' ) );
		}

		/** This filter is documented in wp-includes/user.php */
		$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

		if ( in_array( strtolower( $user->user_login ), array_map( 'strtolower', $illegal_logins ), true ) ) {
			$errors->add( 'invalid_username', __( '<strong>Error:</strong> Sorry, that username is not allowed.' ) );
		}

		/* checking email address */
		if ( empty( $user->user_email ) ) {
			$errors->add( 'empty_email', __( '<strong>Error:</strong> Please enter an email address.' ), array( 'form-field' => 'email' ) );
		} elseif ( ! is_email( $user->user_email ) ) {
			$errors->add( 'invalid_email', __( '<strong>Error:</strong> The email address is not correct.' ), array( 'form-field' => 'email' ) );
		} else {
			$owner_id = email_exists( $user->user_email );
			if ( $owner_id && ( ! $update || ( $owner_id != $user->ID ) ) ) {
				$errors->add( 'email_exists', __( '<strong>Error:</strong> This email is already registered. Please choose another one.' ), array( 'form-field' => 'email' ) );
			}
		}
		if ( $errors->has_errors() ) {
			return $errors;
		}
	}

	/**
	 * Save customer in Avtax
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 */
	public function save_customer_avatax( $user_id ) 
	{
		try 
		{
			$errors = $this->validation_of_fields($user_id);
			if(empty($errors))
			{
				if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api()) {
					$user_data = wc_avatax()->get_user_data($user_id);
					if(!empty($user_data['customerCode']))
					{
						$customer = wc_avatax()->check_if_customer_exists_return($user_data['customerCode']);
						if(!empty((array) $customer))
						{
							if(strval(sanitize_text_field(wp_unslash($_POST['email'])))==strval($customer->emailAddress) &&
							strval(sanitize_text_field(wp_unslash($_POST['first_name'])). " " . sanitize_text_field(wp_unslash($_POST['last_name']))) == strval($customer->name) &&
							strval(sanitize_text_field(wp_unslash($_POST['billing_address_1'])))==strval($customer->line1) &&
							strval(sanitize_text_field(wp_unslash($_POST['billing_address_2'])))==strval($customer->line2) &&
							strval(sanitize_text_field(wp_unslash($_POST['billing_city'])))==strval($customer->city) &&
							strval(sanitize_text_field(wp_unslash($_POST['billing_postcode'])))==strval($customer->postalCode) &&
							strval(sanitize_text_field(wp_unslash($_POST['billing_country'])))==strval($customer->country) &&
							strval(sanitize_text_field(wp_unslash($_POST['billing_state'])))==strval($customer->region) &&
							strval(sanitize_text_field(wp_unslash($_POST['billing_email'])))==strval($customer->customerCode) &&
							strval(sanitize_text_field(wp_unslash($_POST['email']))."_".$user_id)==strval($customer->alternateId)){
							wc_avatax()->log("All values are similar");
							}
							else
							{	
								$this->get_plugin()->get_api()->update_customer_object_to_avatax($user_data['customerCode'],array(
									'id' => $user_id,
									'customerCode' => strval(sanitize_text_field(wp_unslash($_POST['billing_email']))),
									'emailAddress' => strval(sanitize_text_field(wp_unslash($_POST['email']))),
									'name' => strval(sanitize_text_field(wp_unslash($_POST['first_name'])). " " . sanitize_text_field(wp_unslash($_POST['last_name']))),
									'line1'      => strval(sanitize_text_field(wp_unslash($_POST['billing_address_1']))),
									'line2'     => strval(sanitize_text_field(wp_unslash($_POST['billing_address_2']))),
									'city'   => strval(sanitize_text_field(wp_unslash($_POST['billing_city']))),
									'postalCode'  => strval(sanitize_text_field(wp_unslash($_POST['billing_postcode']))),
									'country'  => strval(sanitize_text_field(wp_unslash($_POST['billing_country']))),
									'region'  => strval(sanitize_text_field(wp_unslash($_POST['billing_state']))),
									'alternateId'  =>strval(sanitize_text_field(wp_unslash($_POST['email'])))."_".$user_id,
								));
								wc_avatax()->log("All values are not similar");
							}
						}		
					}
				}
			}
		}
		catch ( Framework\SV_WC_API_Exception $e ) 
		{

			//Logging error
			wc_avatax()->logger()->log_exception("Admin", "save_customer_avatax", $e->getMessage(), $e->getTraceAsString());

			wp_die( esc_html__( 'Error while updating data in avatax.' ), 403 );
			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}
		}
	}

	/**
	 * Save the customer tax settings.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public function save_tax_meta_fields( $user_id ) {

		// Save the tax exemption code
		update_user_meta( $user_id, 'wc_avatax_tax_exemption', wc_clean( $_POST['wc_avatax_user_exemption'] ) );

		/**
		 * Field wc_avatax_user_ior added hold seller importer of record data for user.
		 *
		 * @since 2.3.0
		 *
		 */
		update_user_meta( $user_id, 'wc_avatax_user_ior', wc_clean( $_POST['wc_avatax_user_ior'] ) );

		// Save the delivery terms
		update_user_meta( $user_id, 'wc_avatax_user_deliveryterms', wc_clean( $_POST['wc_avatax_user_deliveryterms'] ) );

		// Save the buyers agent field
    	update_user_meta( $user_id, 'wc_avatax_user_buyers_agent', wc_clean( $_POST['wc_avatax_user_buyers_agent'] ) );
	}
	/**
	 * Checks whether the current admin page is an Avalara tab (settings, ELR, reconciliation, etc.).
	 *
	 * @since 2.6.0
	 *
	 * @return bool
	 */
	public function is_avalara_tab() : bool {
		if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ?? '' ) );

		return 'woocommerce_page_wc-settings' === $screen->id && 'avatax' === $tab;
	}

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 2.6.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin() : WC_AvaTax {

		return wc_avatax();
	}
}
