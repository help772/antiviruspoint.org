<?php // phpcs:ignore

use AdvancedAds\Constants;

/**
 * Render a meta box on the ad edit screen to display order information
 */
class Advanced_Ads_Selling_Admin_Ad_Order_Meta_Box {

	/**
	 * THe constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'wp_admin_plugins_loaded' ] );
	}

	/**
	 * Load actions and filters
	 */
	public function wp_admin_plugins_loaded() {
		add_action( 'add_meta_boxes', [ $this, 'add_order_meta_box' ] );
	}

	/**
	 * Load meta box with order information
	 */
	public function add_order_meta_box() {
		global $post;
		if ( isset( $post->ID ) && Advanced_Ads_Selling_Plugin::is_ordered_ad( $post->ID ) ) {
			add_meta_box(
				'advanced-ads-selling-ad-order-data',
				__( 'Order Data', 'advanced-ads-selling' ),
				[ $this, 'show_order_data' ],
				Constants::POST_TYPE_AD,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Show order data on order edit screen
	 *
	 * @param obj $post Order post.
	 */
	public function show_order_data( $post ) {
		$order_id = get_post_meta( $post->ID, 'advanced_ads_selling_order', true );
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		$item_id    = get_post_meta( $post->ID, 'advanced_ads_selling_order_item', true );
		$product_id = wc_get_order_item_meta( $item_id, '_product_id' );
		$product    = wc_get_product( $product_id );

		$hash = get_post_meta( $order_id, 'advanced_ads_selling_setup_hash', true );

		include AA_SELLING_ABSPATH . '/admin/views/ad-order-meta-box.php';
	}
}
