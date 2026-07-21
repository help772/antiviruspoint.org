<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Order handling logic
 *
 * @package AdvancedAds\SellingAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.5.0
 */

use AdvancedAds\Constants;
use AdvancedAds\Importers\XML_Importer;

/**
 * Ad order handling logic
 */
class Advanced_Ads_Selling_Order {
	/**
	 * Post meta used as marker for the checkout process
	 *
	 * @var string
	 */
	const ORDER_META = 'advanced_ads_selling_processed_order';

	/**
	 * Constructor
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'wp_plugins_loaded' ] );
	}

	/**
	 * Get the instance of this class
	 *
	 * @return Advanced_Ads_Selling_Order
	 */
	public static function get_instance() {
		static $instance;

		// If the single instance hasn't been set, set it now.
		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Load actions and filters
	 */
	public function wp_plugins_loaded() {
		add_action( 'woocommerce_store_api_checkout_order_processed', [ $this, 'process_order' ] );

		// Used by old versions of WooCommerce, checkout not submitted to the REST API.
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'process_order_back_compat' ] );
	}

	/**
	 * Process order - backward compatibility
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function process_order_back_compat( $order_id ): void {
		$this->process_order( wc_get_order( $order_id ) );
	}

	/**
	 * Process the order, create the draft ad for it
	 *
	 * @param WC_Order $order order object.
	 *
	 * @return void
	 */
	public function process_order( $order ): void {
		$order_id    = $order->get_id();
		$items       = $order->get_items();
		$_item_key   = 1;
		$has_ad_item = false; // Flag if at least one item is an ad product.

		foreach ( $items as $_item_id => $_item ) {
			// Compatibility with WP Global Cart, https://wpglobalcart.com/documentation/loop-though-the-cart-items/.
			do_action( 'woocommerce/cart_loop/start', $_item ); // phpcs:ignore

			$product = wc_get_product( $_item['product_id'] );
			if ( $product->is_type( 'advanced_ad' ) ) {
				$xml = $this->create_import_xml( $_item, $order, $_item_id, $_item_key );
				( new XML_Importer() )->import_content( $xml );
				$has_ad_item = true;
			}

			++$_item_key;

			// Compatibility with WP Global Cart, https://wpglobalcart.com/documentation/loop-though-the-cart-items/.
			do_action( 'woocommerce/cart_loop/end', $_item ); // phpcs:ignore
		}

		// Save hash for ad setup page.
		if ( $has_ad_item ) {
			update_post_meta( $order_id, 'advanced_ads_selling_setup_hash', wp_generate_password( 48, false ) );
		}

		// Notify client after the purchase.
		Advanced_Ads_Selling_Notifications::notify_client_after_purchase( $order_id );
	}

	/**
	 * Create import xml for the ad
	 *
	 * @param array  $params   Order item params.
	 * @param object $order    WP_Order.
	 * @param int    $item_id  Id of the item in the order.
	 * @param int    $item_key Index of the item in the order.
	 *
	 * @return $xml import xml
	 */
	private function create_import_xml( $params, $order, $item_id, $item_key ) {
		$type       = 'plain';
		$product_id = wc_get_order_item_meta( $item_id, '_product_id', true );
		$sales_type = get_post_meta( $product_id, '_ad_sales_type', true );

		// Add item index if more than one ad is in the order.
		$item_key_text = $item_key > 1 ? ' / ' . $item_key : '';

		// Get order ID based on version of WooCommerce.
		$order_id = $order->id;
		if ( Advanced_Ads_Selling_Plugin::version_check() ) {
			$order_id = $order->get_id();
		}

		$xml_array[] = '<ads type="array">';
		$xml_array[] = '<item key="0" type="array">';
		$xml_array[] = '<ID type="string">' . $order_id . '</ID>';
		$xml_array[] = '<post_status>draft</post_status>';
		$xml_array[] = '<post_title>Order #' . $order_id . $item_key_text . '</post_title>';
		$xml_array[] = '<meta_input type="array">';
		$xml_array[] = '<advanced_ads_ad_options type="array">';

		// Add impression limit.
		$limit = wc_get_order_item_meta( $item_id, '_ad_pricing_option', true );

		if ( defined( 'AAT_VERSION' ) && in_array( $sales_type, [ 'impressions', 'clicks' ], true ) && $limit ) {
			$xml_array[] = '<tracking type="array">';
			switch ( $sales_type ) :
				case 'impressions':
					$xml_array[] = '<impression_limit type="numeric">' . absint( $limit ) . '</impression_limit>';
					break;
				case 'clicks':
					$xml_array[] = '<click_limit type="numeric">' . absint( $limit ) . '</click_limit>';
					break;
			endswitch;
			$xml_array[] = '</tracking>';
		}
		$xml_array[] = '<type type="string">' . $type . '</type>';
		$xml_array[] = '</advanced_ads_ad_options>';
		$xml_array[] = '<advanced_ads_selling_order>' . $order_id . '</advanced_ads_selling_order>';
		$xml_array[] = '<advanced_ads_selling_order_item>' . $item_id . '</advanced_ads_selling_order_item>';
		$xml_array[] = '</meta_input>';
		$xml_array[] = '</item>';
		$xml_array[] = '</ads>';

		return '<advads-export>' . implode( '', $xml_array ) . '</advads-export>';
	}

	/**
	 * Convert order item id into ad id
	 *
	 * @param int $item_id Id of the order item.
	 *
	 * @return int|bool $ad_id Id of the ad created from that order; false if no ad was found
	 */
	public static function order_item_id_to_ad_id( $item_id ) {
		$item_id = absint( $item_id );
		if ( ! $item_id ) {
			return false;
		}

		$args = [
			'post_type'      => Constants::POST_TYPE_AD,
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'meta_query'     => [ // phpcs:ignore
				[
					'key'   => 'advanced_ads_selling_order_item',
					'value' => $item_id,
				],
			],
		];

		$ads = new WP_Query( $args );

		return $ads->have_posts() ? $ads->posts[0]->ID : false;
	}

	/**
	 * Check if an order contains ad products
	 * by checking for the existence of the setup page hash
	 *
	 * @param int $order_id post Id of the order.
	 *
	 * @return bool True, if order contains ad products or false if not
	 */
	public static function has_ads( $order_id = 0 ) {
		return get_post_meta( $order_id, 'advanced_ads_selling_setup_hash', true );
	}

	/**
	 * Gets All Customer Orders
	 *
	 * @param int $user_id User ID.
	 *
	 * @return stdClass|WC_Order[]
	 */
	public static function get_customer_orders( $user_id ) {
		return wc_get_orders(
			[
				'customer_id' => $user_id,
				'limit'       => -1,
			]
		);
	}

	/**
	 * Return array of order ids for any customer
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array
	 */
	public static function get_customer_order_ids( $user_id ) {
		$user_id         = $user_id ?? get_current_user_id();
		$customer_orders = static::get_customer_orders( $user_id );

		$ids = [];
		foreach ( $customer_orders as $orders ) {
			$ids[] = $orders->get_id();
		}

		return $ids;
	}

	/**
	 * Check if customer has ads purchased in any of the order
	 *
	 * @param int $user_id User ID.
	 *
	 * @return bool
	 */
	public function customer_has_ads( $user_id ) {
		$user_id = isset( $user_id ) ? $user_id : get_current_user_id();

		$has_ads        = false;
		$order_id_array = static::get_customer_order_ids( $user_id );

		foreach ( $order_id_array as $order_id ) {
			if ( static::has_ads( $order_id ) ) {
				$has_ads = true;
				break;
			}
		}
		return $has_ads;
	}
}
