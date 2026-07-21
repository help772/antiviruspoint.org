<?php // phpcs:ignore WordPress.Files.FileName

use AdvancedAds\Framework\Utilities\Params;

/**
 * Initialize the ad product type and ad product page
 */
class Advanced_Ads_Selling_Admin_Ad_Product {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'wp_admin_plugins_loaded' ] );
	}

	/**
	 * Load actions and filters
	 */
	public function wp_admin_plugins_loaded() {
		// show price and other product data fields.
		add_action( 'admin_footer', [ $this, 'ad_product_custom_js' ] );
		// hide some product data tabs for ad product type.
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'hide_ad_data_panel' ] );
		// add custom product tab for ad type and all the needed logic.
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'custom_product_tab' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'ad_options_product_tab_content' ] );
		add_action( 'woocommerce_process_product_meta_advanced_ad', [ $this, 'save_ad_option_fields' ] );
	}

	/**
	 * Hide some product data panels for ad type
	 *
	 * @param array $tabs product tabs.
	 */
	public function hide_ad_data_panel( $tabs ) {
		// Other default values for 'attribute' are; general, inventory, shipping, linked_product, variations, advanced.
		$tabs['attribute']['class'][] = 'hide_if_advanced_ad';
		$tabs['shipping']['class'][]  = 'hide_if_advanced_ad';
		$tabs['advanced']['class'][]  = 'hide_if_advanced_ad';

		return $tabs;
	}

	/**
	 * Handle product data fields for ad product.
	 */
	public function ad_product_custom_js() {
		// Early bail!!
		if ( 'product' !== get_post_type() ) {
			return;
		}
		?>
		<script type='text/javascript'>
			jQuery( document ).ready( function () {
				jQuery( '.options_group.pricing' ).addClass( 'show_if_advanced_ad' ).show();
			} );
		</script>
		<?php
	}

	/**
	 * Add a custom product tab for ad product type
	 *
	 * @param array $tabs product tabs.
	 */
	public function custom_product_tab( $tabs ) {
		$tabs['ad_options'] = [
			'label'  => __( 'Ad Options', 'advanced-ads-selling' ),
			'target' => 'ad_options',
			'class'  => [ 'show_if_advanced_ad' ],
		];

		return $tabs;
	}

	/**
	 * Show content of custom product tab in admin area
	 */
	public function ad_options_product_tab_content() {
		global $post;

		?>
		<div id='ad_options' class='panel woocommerce_options_panel'>
			<div class='options_group'>
				<?php
				// Selectable ad types keys must match an Advanced Ads ad type.
				$ad_types = apply_filters(
					'advanced-ads-selling-product-tab-ad-types',
					[
						'plain' => _x( 'html', 'ad type', 'advanced-ads-selling' ),
						'image' => _x( 'image', 'ad type', 'advanced-ads-selling' ),
					],
					$post
				);

				$this->woocommerce_wp_multiselect(
					[
						'id'      => '_ad_types',
						'name'    => '_ad_types[]',
						'label'   => __( 'Available ad types', 'advanced-ads-selling' ),
						'options' => $ad_types,
					]
				);
				woocommerce_wp_select(
					[
						'id'      => '_ad_sales_type',
						'label'   => __( 'Customer can buy per …', 'advanced-ads-selling' ),
						'options' => Advanced_Ads_Selling_Plugin::get_instance()->sale_types,
					]
				);
				woocommerce_wp_textarea_input(
					[
						'id'          => '_ad_prices',
						'label'       => __( 'Price options', 'advanced-ads-selling' ),
						'placeholder' => __( 'define label, value and price separated by |, e.g. 100.000 impressions|100000|20.00', 'advanced-ads-selling' ),
						'desc_tip'    => 'true',
						'description' => __( 'define label, value and price separated by |, e.g. 100.000 impressions|100000|20.00', 'advanced-ads-selling' ),
					]
				);
				$this->woocommerce_wp_multiselect(
					[
						'id'      => '_ad_placements',
						'name'    => '_ad_placements[]',
						'label'   => __( 'Available placements', 'advanced-ads-selling' ),
						'options' => $this->get_placements_for_select(),
					]
				);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Output for a multiselect field since WooCommerce does not have it
	 *
	 * Derived from /woocommerce/includes/admin/wc-meta-box-functions.php::woocommerce_wp_select version 2.6.2
	 *
	 * @param array $field field data.
	 */
	private function woocommerce_wp_multiselect( $field ) {
		global $thepostid, $post;

		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

		printf(
			'<p class="form-field %1$s_field %2$s">
				<label for="%1$s">%3$s</label>
				<select id="%1$s" name="%4$s" class="%5$s" style="%6$s" multiple="multiple">',
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_class'] ),
			wp_kses_post( $field['label'] ),
			esc_attr( $field['name'] ),
			esc_attr( $field['class'] ),
			esc_attr( $field['style'] )
		);

		foreach ( $field['options'] as $key => $value ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				( is_array( $field['value'] ) && in_array( $key, $field['value'], true ) ) ? 'selected="selected"' : '',
				esc_html( $value )
			);
		}

		echo '</select>';

		if ( ! empty( $field['description'] ) ) {
			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				echo wc_help_tip( $field['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		}
		echo '</p>';
	}

	/**
	 * Flatten multidimensional placements array for select
	 *
	 * @return array $placements as a one-dimensional array
	 */
	private function get_placements_for_select() {
		$placements = [];
		foreach ( wp_advads_get_published_placements() as $placement ) {
			$placements[ $placement->get_slug() ] = $placement->get_title();
		}

		return $placements;
	}

	/**
	 * Save custom fields.
	 *
	 * @param int $post_id post ID.
	 */
	public function save_ad_option_fields( $post_id ) {
		$ad_type_option = Params::post( '_ad_types', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $ad_type_option ) ) {
			$ad_type_option = [ 'plain' ];
		}
		update_post_meta( $post_id, '_ad_types', $ad_type_option );

		$sales_type_option = '' !== Params::post( '_ad_sales_type' ) ? Params::post( '_ad_sales_type' ) : 'flat';
		update_post_meta( $post_id, '_ad_sales_type', $sales_type_option );

		if ( '' !== Params::post( '_ad_prices' ) ) {
			update_post_meta( $post_id, '_ad_prices', esc_attr( Params::post( '_ad_prices' ) ) );
		}

		$placements = Params::post( '_ad_placements', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $placements ) ) {
			delete_post_meta( $post_id, '_ad_placements' );
		} else {
			update_post_meta( $post_id, '_ad_placements', $placements );
		}

		/**
		 * The next line forces the product to be virtual.
		 * Simple products have an option for this, but we don’t need a choice here, since ads are always virtual
		 * at the time we added this, we didn’t find a programmatic way, e.g., when registering the ad type to accomplish this
		 * ads that were created before this change have to be resaved
		 */
		update_post_meta( $post_id, '_virtual', true );

		// Override product price with first price.
		$prices      = Advanced_Ads_Selling_Plugin::get_prices( $post_id );
		$first_price = current( $prices );
		update_post_meta( $post_id, '_price', $first_price['price'] );
	}
}
