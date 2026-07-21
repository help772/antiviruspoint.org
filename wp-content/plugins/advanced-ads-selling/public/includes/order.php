<?php // phpcs:ignore WordPress.Files.FileName

use AdvancedAds\Framework\Utilities\Params;

/**
 * Logic to handle order in the frontend
 */
class Advanced_Ads_Selling_Public_Order {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'wp_plugins_loaded' ] );
	}

	/**
	 * Load actions and filters
	 */
	public function wp_plugins_loaded(): void {
		// Early bail!!
		if ( ! class_exists( 'Advanced_Ads_Selling', false ) ) {
			return;
		}

		// save and show order item meta in frontend.
		if ( Advanced_Ads_Selling_Plugin::version_check() ) {
			add_action( 'woocommerce_new_order_item', [ $this, 'add_order_item_meta' ], 99, 2 );
		} else {
			add_action( 'woocommerce_add_order_item_meta', [ $this, 'add_order_item_meta' ], 99, 2 );
		}

		add_filter( 'woocommerce_get_item_data', [ $this, 'render_meta_on_cart_and_checkout' ], 99, 2 );
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'save_cart_ad_options' ], 99, 2 );
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'calculate_ad_price' ], 999 );
		add_filter( 'woocommerce_product_tabs', [ $this, 'woocommerce_remove_reviews_tab' ], 98 );
		add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'add_to_cart_link' ] );

		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'woocommerce_remove_cart_item_quantity' ], 10, 2 );
		add_filter( 'woocommerce_is_sold_individually', [ $this, 'sold_individually' ], 10, 2 );
	}

	/**
	 * Remove Review tab on single product page if product is advanced_ad type
	 *
	 * @param array $tabs product tabs.
	 */
	public function woocommerce_remove_reviews_tab( $tabs ): array {
		global $post;

		$product = wc_get_product( $post->ID );

		if ( $product->is_type( 'advanced_ad' ) ) {
			unset( $tabs['reviews'] );
		}

		return $tabs;
	}

	/**
	 * Remove Quantity field from cart if product is advanced_ad type (WC < 8.3)
	 *
	 * @param string $product_quantity product quantity input markup.
	 * @param string $cart_item_key    cart item key.
	 *
	 * @return mixed
	 */
	public function woocommerce_remove_cart_item_quantity( $product_quantity, $cart_item_key ) {
		global $woocommerce;

		foreach ( $woocommerce->cart->cart_contents as $cart_item_loop_key => $cart_item_loop_val ) {
			if ( $cart_item_key === $cart_item_loop_key ) { // Check current_item_key with item_key we get.
				$product = wc_get_product( $cart_item_loop_val['product_id'] );
				if ( $product->is_type( 'advanced_ad' ) ) { // If product is advanced_ad type then return.
					return $cart_item_loop_val['quantity'];
				}
			}
		}

		return $product_quantity;
	}

	/**
	 * Remove Quantity field from cart if product is advanced_ad type (WC > 8.3)
	 *
	 * @param bool       $ret  return value.
	 * @param WC_Product $product product object.
	 *
	 * @return bool|void
	 */
	public function sold_individually( $ret, $product ) {
		return 'advanced_ad' === $product->get_type() ? true : $ret;
	}

	/**
	 * Render custom option in cart and checkout
	 *
	 * @param array $cart_data cart data.
	 * @param array $cart_item cart item.
	 *
	 * @return array
	 */
	public function render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ): array {
		$meta_items = [];
		/* Woo 2.4.2 updates */
		if ( ! empty( $cart_data ) ) {
			$meta_items = $cart_data;
		}

		$prices = Advanced_Ads_Selling_Plugin::get_prices( $cart_item['product_id'] );

		if ( isset( $cart_item['option_ad_price'] ) ) {
			$label        = $prices[ $cart_item['option_ad_price'] ]['label'];
			$name         = __( 'Price option', 'advanced-ads-selling' );
			$meta_items[] = [
				'name'  => $name,
				'value' => $label,
			];
		}

		return $meta_items;
	}

	/**
	 * Save public custom fields in the cart
	 *
	 * @param array $cart_item_data cart item data.
	 * @param int   $product_id     product id.
	 *
	 * @return array
	 */
	public function save_cart_ad_options( $cart_item_data, $product_id ): array {
		// save the price option.

		$price = Params::post( 'option_ad_price' );

		if ( $price ) {
			$price  = sanitize_text_field( $price );
			$prices = Advanced_Ads_Selling_Plugin::get_prices( $product_id );
			if ( isset( $prices[ $price ] ) ) {
				$cart_item_data['option_ad_price'] = $price;
			}
		}

		$placement_slug = Params::post( 'option_ad_placement' );

		if ( $placement_slug ) {
			$placement_slug = sanitize_text_field( $placement_slug );

			if ( wp_advads_get_placement_by_slug( $placement_slug ) ) {
				$cart_item_data['option_ad_placement'] = $placement_slug;
			}
		}

		// save the ad types retrieved from the product directly.
		$ad_type_value = get_post_meta( $product_id, '_ad_types', true );
		if ( $ad_type_value ) {
			$cart_item_data['option_ad_types'] = $ad_type_value;
		}

		// save the ad sales type retrieved from the product directly.
		$sale_value = get_post_meta( $product_id, '_ad_sales_type', true );

		if ( $sale_value ) {
			$cart_item_data['option_ad_sales_type'] = $sale_value;
		}

		return $cart_item_data;
	}

	/**
	 * Add to cart Validation for "Number of days" field
	 *
	 * @param bool $valid      is valid.
	 * @param int  $product_id product id.
	 *
	 * @return bool
	 * @deprecated since version 1.0.0 after we switched to fixed selection of days.
	 */
	public function add_to_cart_validation( $valid, $product_id ): bool {
		$product           = wc_get_product( $product_id );
		$product_sale_type = $product->get_meta( '_ad_sales_type' );

		$expiry_days  = Params::post( 'option_ad_expiry_days', [], FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
		$product_days = $expiry_days[ $product_id ] ?? 0;

		if ( $product->is_type( 'advanced_ad' ) && 'days' === $product_sale_type && 0 === $product_days ) {
			wc_add_notice( '<p class="advanced-ads-selling-days-error">' . __( 'Please enter a positive number of days.', 'advanced-ads-selling' ) . '</p>', 'error' );
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Add item meta to order, when created
	 *
	 * @param int    $item_id item id.
	 * @param object $values  values.
	 *
	 * @return void
	 */
	public function add_order_item_meta( $item_id, $values ): void {
		$prices = Advanced_Ads_Selling_Plugin::get_prices( $values['product_id'] );

		// added compatibility with WC version 3.0, even though this is a legacy fix.
		if ( Advanced_Ads_Selling_Plugin::version_check() ) {
			$values = $values->legacy_values;
		}

		// save pricing label.
		if ( isset( $values['option_ad_price'] ) ) {
			$label = $prices[ $values['option_ad_price'] ]['label'];
			wc_add_order_item_meta( $item_id, '_ad_pricing_label', $label );
		}

		// save price value.
		if ( isset( $values['option_ad_price'] ) ) {
			wc_add_order_item_meta( $item_id, '_ad_pricing_option', $values['option_ad_price'] );
		}

		// save sales type (e.g. days, flat, impressions).
		if ( isset( $values['option_ad_sales_type'] ) ) {
			wc_add_order_item_meta( $item_id, '_ad_sales_type', $values['option_ad_sales_type'] );
		}

		// save placements.
		if ( isset( $values['option_ad_placement'] ) ) {
			wc_add_order_item_meta( $item_id, '_ad_placement', $values['option_ad_placement'] );
		}

		// save ad types retrieved from the original product.
		$value = get_post_meta( $values['product_id'], '_ad_types', true );
		if ( $value ) {
			wc_add_order_item_meta( $item_id, '_ad_types', implode( ', ', $value ) );
		}
	}

	/**
	 * Calculate price if product is of Ad type and "Number of days" is set
	 *
	 * @param object $cart_object cart object.
	 *
	 * @return void
	 */
	public function calculate_ad_price( $cart_object ): void {
		if ( ! WC()->session->__isset( 'reload_checkout' ) ) {
			foreach ( $cart_object->cart_contents as $key => $value ) {
				if ( isset( $value['option_ad_price'] ) ) {
					$prices = Advanced_Ads_Selling_Plugin::get_prices( $value['data']->get_id() );
					$value['data']->set_price( floatval( $prices[ $value['option_ad_price'] ]['price'] ) );
				}
			}
		}
	}

	/**
	 * Convert add-to-cart button to link to static page.
	 *
	 * @param string $link The add to cart link.
	 *
	 * @return string
	 */
	public function add_to_cart_link( $link ): string {
		global $product;

		// If product type is of 'advanced_ad' and product sale type is of 'advanced_ad' and days are set.
		if ( $product && $product->get_type() === 'advanced_ad' ) {
			$link = sprintf(
				'<a href="%s" data-product_id="%s" data-product_sku="%s" data-quantity="1" class="button add_to_cart_button product_type_advanced_ad">%s</a>',
				get_permalink(),
				$product->get_id(),
				$product->get_sku(),
				__( 'Details', 'advanced-ads-selling' )
			);
		}

		return $link ?? '';
	}
}
