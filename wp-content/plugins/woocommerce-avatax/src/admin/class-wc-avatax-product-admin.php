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
use SkyVerge\WooCommerce\AvaTax\API\Responses\Nexus_List_Response;

defined( 'ABSPATH' ) or exit;

/**
 * Set up the AvaTax admin.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Product_Admin {


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// display the tax code field
		add_action( 'woocommerce_product_options_tax', array( $this, 'display_tax_code_field' ) );
		add_action( 'woocommerce_product_options_advanced', array( $this, 'display_COM_field' ) );
		add_action( 'woocommerce_product_options_advanced', array( $this, 'display_section232_metal_field' ), 15 );
		// save the product field values
		add_action('woocommerce_process_product_meta', array($this, 'save_meta'), 10, 2);
		add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_section232_metal_on_product_object' ), 99, 1 );

		// display the quick edit fields
		add_action('manage_product_posts_custom_column', array($this, 'add_quick_edit_inline_values'), 10);
		add_action('woocommerce_product_quick_edit_end',  array($this, 'display_quick_edit_fields'));
		// @codeCoverageIgnoreStart
		add_action('woocommerce_product_quick_edit_save', array($this, 'save_bulk_edit_fields')); 
		// @codeCoverageIgnoreEnd

		// display and save the bulk edit fields
		add_action('woocommerce_product_bulk_edit_end', array( $this, 'display_bulk_edit_fields'));
		add_action('woocommerce_product_bulk_edit_save', array( $this, 'save_bulk_edit_fields'));

		// filter the product table query when a specific HTS code is desired
		add_filter('parse_query', array($this, 'filter_by_hts_code'));

		//Adding a HS code custom tab to the Products Metabox
		add_filter('woocommerce_product_data_tabs', array($this, 'addHsCodeTab'), 99, 1);
		//Adding and POPULATING (with data) custom HS code fields in Hs code custom tab for Product Metabox
		add_action('woocommerce_product_data_panels', array($this, 'addHsCodeProductDataFields'));
		//Saving custom HS code fields data of custom HD code products tab metabox
		add_action('woocommerce_process_product_meta', array($this, 'saveProductMeta'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
	}


	/** Tax Code Methods ******************************************************/


	/**
	 * Displays the tax code field.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 */
	public function display_tax_code_field() {
		$tax_code = get_post_meta( get_the_id(), '_wc_avatax_code', "P0000000" );
		include_once(wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-product-tax-code-edit.php');
	}

	/**
	 * Displays the COM Field field.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 */
	public function display_COM_field() {
		$county_of_manufacture = wc_avatax()->wc_avatax_utilities()->get_wc_meta( get_the_id(), '_wc_avatax_county_of_manufacture', true, 'product');
		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-product-country-of-manufacture.php' );
	}

	/**
	 * Section 232 metal percent repeater (advanced product options).
	 *
	 * @since 3.8.1
	 */
	public function display_section232_metal_field() {
		global $product_object;

		$product_id = (int) get_the_ID();

		// Prefer the meta box global so flags match Virtual/Downloadable checkboxes (same as WC panel).
		if ( $product_object instanceof WC_Product && (int) $product_object->get_id() === $product_id ) {
			$product = $product_object;
		} else {
			$product = $product_id ? wc_get_product( $product_id ) : null;
		}

		// Always render markup so toggling Virtual/Downloadable in the browser (before save) can show the fields.
		$section232_initially_hidden = $product instanceof WC_Product
			? ! wc_avatax()->wc_avatax_utilities()->is_section232_metal_enabled_for_product( $product )
			: false;

		$stored = wc_avatax()->wc_avatax_utilities()->get_wc_meta( $product_id, '_wc_avatax_section232_metal_percent', true, 'product' );

		if ( ! is_array( $stored ) && is_string( $stored ) ) {
			$decoded = json_decode( $stored, true );
			$stored  = is_array( $decoded ) ? $decoded : array();
		}
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$section232_metal_rows = array();
		foreach ( $stored as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$section232_metal_rows[] = array(
				'value'   => isset( $row['value'] ) ? (string) $row['value'] : '',
				'unit'    => isset( $row['unit'] ) ? (string) $row['unit'] : '',
				'country' => isset( $row['country'] ) ? (string) $row['country'] : '',
			);
		}

		$material_units = wc_avatax()->wc_avatax_utilities()->get_section232_material_units();
		$country_options = array();
		foreach ( WC()->countries->get_countries() as $code => $name ) {
			$country_options[ $code ] = $code . ' - ' . $name;
		}

		$this->include_section232_metal_percent_view(
			array(
				'section232_initially_hidden' => $section232_initially_hidden,
				'section232_metal_rows'       => $section232_metal_rows,
				'material_units'              => $material_units,
				'country_options'             => $country_options,
			)
		);
	}

	/**
	 * Loads the Section 232 metal percent admin view.
	 *
	 * @since 3.8.1
	 *
	 * @param array $view_vars Variables for {@see html-field-product-section232-metal-percent.php}.
	 */
	protected function include_section232_metal_percent_view( array $view_vars ) {
		extract( $view_vars, EXTR_SKIP );
		include_once wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-product-section232-metal-percent.php';
	}


	/** HTS Code Methods ******************************************************/


	/**
	 * Displays the HTS code field.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 * @deprecated 1.16.0
	 */
	public function display_hts_code_field() {

		wc_deprecated_function( __METHOD__, '1.16.0' );
	}


	/** General Methods *******************************************************/


	/**
	 * Saves the product field values.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param int $post_id product ID
	 */
	public function save_meta($post_id) {

		update_post_meta( $post_id, '_wc_avatax_code', sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( '_wc_avatax_code' ) ) );
		 
		wc_avatax()->wc_avatax_utilities()->update_wc_meta( $post_id, '_wc_avatax_county_of_manufacture', sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( '_wc_avatax_county_of_manufacture' ) ), 'product' );

	}

	/**
	 * Save or clear Section 232 metal meta after WooCommerce applies the submitted product data.
	 *
	 * Uses {@see woocommerce_admin_process_product_object} so virtual/downloadable flags match the
	 * current save (avoids stale meta when checkboxes are unchecked in POST).
	 *
	 * @since 3.8.1
	 *
	 * @param WC_Product $product Product with admin POST data applied.
	 */
	public function save_section232_metal_on_product_object( $product ) {

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$post_id = $product->get_id();

		if ( ! wc_avatax()->wc_avatax_utilities()->is_section232_metal_enabled_for_product( $product ) ) {
			wc_avatax()->wc_avatax_utilities()->update_wc_meta( $post_id, '_wc_avatax_section232_metal_percent', array(), 'product' );
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce product save.
		if ( ! isset( $_POST['_wc_avatax_section232_save'] ) ) {
			return;
		}

		$parsed = wc_avatax()->wc_avatax_utilities()->parse_section232_metal_from_post( wp_unslash( $_POST ) );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( is_wp_error( $parsed ) ) {
			if ( class_exists( '\WC_Admin_Meta_Boxes' ) ) {
				\WC_Admin_Meta_Boxes::add_error( $parsed->get_error_message() );
			} else {
				wc_avatax()->log( $parsed->get_error_message() );
			}
			return;
		}

		wc_avatax()->wc_avatax_utilities()->update_wc_meta( $post_id, '_wc_avatax_section232_metal_percent', $parsed, 'product' );
	}


	/**
	 * Adds markup for the custom meta values so Quick Edit can fill the inputs.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param string $column the current column slug
	 */
	public function add_quick_edit_inline_values($column) {
		global $post;

		$product = is_object( $post ) ? wc_get_product( $post->ID ) : null;

		if ( $product && 'name' === $column ) : ?>

			<div id="wc_avatax_inline_<?php echo esc_attr( $product->get_id() ); ?>" class="hidden">
				<div class="tax_code"><?php echo esc_html( $product->get_meta( '_wc_avatax_code' ) ); ?></div>
			</div>

		<?php endif;
	}


	/**
	 * Displays the quick edit fields.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 */
	public function display_quick_edit_fields() {

		include_once(wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-product-tax-code-quick-edit.php');
	}


	/**
	 * Displays the bulk edit fields.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 */
	public function display_bulk_edit_fields() {

		include_once(wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-product-tax-code-bulk-edit.php');
	}


	/**
	 * Saves the tax code bulk edit field.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Product $product product object
	 */
	public function save_bulk_edit_fields($product) {

		if ( ! empty( $_REQUEST['change_wc_avatax_code'] ) ) {

			$new_code     = sanitize_text_field( $_REQUEST['_wc_avatax_code'] );
			$current_code = $product->get_meta( '_wc_avatax_code' );

			// update to new tax code if different than current tax code
			if ( isset( $new_code ) && $new_code !== $current_code ) {
				update_post_meta( $product->get_id(), '_wc_avatax_code', $new_code );
			}
		}
	}


	/**
	 * Filters the product table query when a specific HTS code is desired.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 *
	 * @param \WP_Query $query query object
	 */
	public function filter_by_hts_code($query) {
		global $typenow;

		if ( 'product' === $typenow && Framework\SV_WC_Helper::get_requested_value( 'wc_avatax_hts_code' ) ) {
			$query->query_vars['meta_value'] = Framework\SV_WC_Helper::get_requested_value( 'wc_avatax_hts_code' );
			$query->query_vars['meta_key']   = '_wc_avatax_hts_code';
		}
	}

	/** HS Code Methods ******************************************************/

	/**
	 * Adds a new HS (Harmonized System) code tab to the product data tabs.
	 *
	 * @since 0.0.0
	 *
	 * @param array $product_data_tabs Existing product data tabs.
	 * @return array Modified product data tabs with the HS code tab added.
	 */
	public function addHsCodeTab($productDataTabs)
	{
		$productDataTabs['hs_code'] = array(
			'label' => __('HS code', 'woocommerce-avatax'), // translatable
			'target' => 'hs_code_product_data', // translatable
		);
		return $productDataTabs;
	}

	/**
	 * Renders the HTML content for the HS code product data panel.
	 *
	 *
	 * @since 0.0.0
	 *
	 * @global WP_Post $post Current post object.
	 * @return void
	 */
	public function addHsCodeProductDataFields() {
        global $post;
        $postId = $post->ID;
        $product = wc_get_product($postId);
        $hsCodeCountries = $product->get_meta("_wc_avatax_hs_countries") ?: array();
        
        try {
            $api = wc_avatax()->get_api();
            $response = $api->get_nexus_list();
            $nexusCountries = $response->get_full_nexus_details();
            
            // Create an array of unique countries
            $uniqueCountries = array();
            foreach ($nexusCountries as $nexus) {
                if (!isset($hsCodeCountries[$nexus->country])) {
                    $uniqueCountries[$nexus->country] = $nexus->jurisName;
                }
            }
        } catch (Exception $e) {
            $uniqueCountries = array();
            wc_avatax()->log("Error: " . $e->getMessage() ."\n Stack trace: ". $e->getTraceAsString());
        }

        /// Include template
		$this->display_hs_code_template($product, $hsCodeCountries, $uniqueCountries);
    }
	/**
 * Display the HS code template
 *
 * @param WC_Product $product
 * @param array $hsCodeCountries
 * @param array $uniqueCountries
 */
public function display_hs_code_template($product, $hsCodeCountries, $uniqueCountries) {
    include_once wc_avatax()->get_plugin_path() . '/src/admin/views/html-product-hs-codes.php';
}
	
	/**
	 * Saves HS code metadata for a product.
	 *
	 * Processes and saves the Harmonized System (HS) codes submitted through the product
	 * edit form. Iterates through the available countries and updates the corresponding
	 * HS code values in the product metadata.
	 *
	 * @since 0.0.0
	 *
	 * @param int $postId The ID of the product being saved.
	 * @return void
	 *
	 * @uses wc_get_product() To get the WC_Product instance.
	 * @uses WC_Product::get_meta() To retrieve existing HS code countries.
	 * @uses WC_Product::set_meta() To update HS code values.
	 *
	 * @global array $_POST Contains the form submission data.
	 */
	public function saveProductMeta($postId) {
		if (!current_user_can('edit_post', $postId)) {
			return;
		}
	
		$product = wc_get_product($postId);
		
		// Get and decode the JSON data containing all HS codes
		$hs_countries_json = isset($_POST['_wc_avatax_hs_countries']) ? 
			stripslashes($_POST['_wc_avatax_hs_countries']) : '{}';
		$hs_countries = json_decode($hs_countries_json, true) ?: array();
		
		// Get existing HS countries to check for removals
		$existing_countries = wc_avatax()->wc_avatax_utilities()->get_wc_meta($postId, '_wc_avatax_hs_countries', true, 'product') ?: array();
		
		// Remove old meta for countries that are no longer present
		foreach ($existing_countries as $old_country) {
			if (!isset($hs_countries[$old_country])) {
				wc_avatax()->wc_avatax_utilities()->delete_wc_meta($postId, '_wc_avatax_hs_' . $old_country, '', 'product');
			}
		}
	
		// Save new HS codes
		$hsCodeCountries = array();
		foreach ($hs_countries as $country => $hsCode) {
			if (!empty($hsCode)) {
				$hsCodeCountries[$country] = $country;
				$meta_key = '_wc_avatax_hs_' . $country;
				wc_avatax()->wc_avatax_utilities()->update_wc_meta($postId, $meta_key, sanitize_text_field($hsCode), 'product');
			}
		}
	
		// Update the countries meta
		wc_avatax()->wc_avatax_utilities()->update_wc_meta($postId, '_wc_avatax_hs_countries', $hsCodeCountries, 'product');
	}
	

	public function enqueue_admin_scripts($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        global $post_type;
        if ('product' !== $post_type) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'wc-avatax-hs-codes',
            wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-hs-codes.css',
            array(),
            \WC_AvaTax::VERSION
        );

        // Enqueue JavaScript
        wp_enqueue_script(
			'wc-avatax-hs-codes',
			wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-hs-codes.js',
			array('jquery'),
			\WC_AvaTax::VERSION,
			true
		);

		$section232_css = '/assets/css/admin/wc-avatax-section232-metal.min.css';
		if ( ! is_readable( wc_avatax()->get_plugin_path() . $section232_css ) ) {
			$section232_css = '/assets/css/admin/wc-avatax-section232-metal.css';
		}
		wp_enqueue_style(
			'wc-avatax-section232-metal',
			wc_avatax()->get_plugin_url() . $section232_css,
			array(),
			\WC_AvaTax::VERSION
		);

		wp_enqueue_script(
			'wc-avatax-section232-metal',
			wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-section232-metal.min.js',
			array( 'jquery' ),
			\WC_AvaTax::VERSION,
			true
		);

		wp_localize_script(
			'wc-avatax-section232-metal',
			'wcAvaTaxSection232Metal',
			array(
				'i18n' => array(
					'valueRequired' => __( 'Enter a value greater than 0 and at most 100.', 'woocommerce-avatax' ),
					'valueRange'    => __( 'Value must be greater than 0 and at most 100.', 'woocommerce-avatax' ),
					'unitRequired'  => __( 'Select a material unit.', 'woocommerce-avatax' ),
					'unitDuplicate' => __( 'Each material unit can only be used in one row.', 'woocommerce-avatax' ),
					'sumAtMost100' => __( 'The sum of all metal values must be at most 100%.', 'woocommerce-avatax' ),
				),
			)
		);
    }
	
}

