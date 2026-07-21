<?php // phpcs:ignore WordPress.Files.FileName

use AdvancedAds\Constants;
use AdvancedAds\Framework\Utilities\Params;

/**
 * Process ordered placements
 */
class Advanced_Ads_Selling_Admin_Placements {

	/**
	 * The Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'wp_admin_plugins_loaded' ] );
	}

	/**
	 * Load actions and filters
	 */
	public function wp_admin_plugins_loaded() {
		add_action( 'post_submitbox_start', [ $this, 'show_placement_item_and_warning' ], 11 );
		add_action( 'post_updated', [ $this, 'add_ad_to_placement' ], 10, 2 );
	}

	/**
	 * Show a warning above the 'Publish' button on the ad edit screen
	 */
	public function show_placement_item_and_warning() {
		global $post;

		// Exit if post status is not 'pending'.
		if ( Constants::POST_TYPE_AD !== $post->post_type || ! in_array( $post->post_status, [ 'pending', 'draft' ], true ) ) {
			return;
		}

		$order_item = get_post_meta( $post->ID, 'advanced_ads_selling_order_item', true );
		if ( ! $order_item ) {
			return;
		}

		$slug = wc_get_order_item_meta( (int) $order_item, '_ad_placement' );

		if ( ! $slug ) {
			return;
		}

		$warning   = '';
		$do_import = false;
		$placement = wp_advads_get_placement_by_slug( $slug );

		if ( empty( $placement->get_item_type() ) || ! $placement->get_item_object() ) {
			$warning = sprintf(
			/* translators: %s: placement title */
				__( 'When you press “Publish”, this ad will be published in the <strong>%2$s</strong> placement.', 'advanced-ads-selling' ),
				null,
				$placement->get_title()
			);
			$do_import = true;
		} elseif ( 'ad' === $placement->get_item_type() ) {
			$warning = sprintf(
			/* translators: %1$s: item title, %2$s: placement title */
				__( 'When you press “Publish”, this ad will be swapped against the currently assigned ad (<strong>%1$s</strong>) in the <strong>%2$s</strong> placement.', 'advanced-ads-selling' ),
				$placement->get_item_object()->get_title(),
				$placement->get_title()
			);
			$do_import = true;
		} elseif ( 'group' === $placement->get_item_type() ) {
			if ( ! has_term( $placement->get_item_object()->get_id(), Constants::TAXONOMY_GROUP, $post->ID ) ) {
				$warning = sprintf(
				/* translators: %1$s: item title, %2$s: placement title */
					__( 'When you press “Publish”, this ad will be added to the group <strong>%1$s</strong> in the <strong>%2$s</strong> placement.', 'advanced-ads-selling' ),
					$placement->get_item_object()->get_title(),
					$placement->get_title()
				);
				$do_import = true;
			}
		}

		// Show expiry date warning.
		$expiry_days = wc_get_order_item_meta( $order_item, '_ad_pricing_option' );
		if ( 'days' === wc_get_order_item_meta( $order_item, '_ad_sales_type' ) && $expiry_days ) {
			$warning .= sprintf(
				'<br/>%s',
				sprintf(
					/* translators: amount of days before expiration of the ad. */
					__( 'This ad is going to <strong>expire %d days</strong> after being published.', 'advanced-ads-selling' ),
					$expiry_days
				)
			);
		}

		include AA_SELLING_ABSPATH . '/admin/views/ad-publish-meta-box.php';
	}

	/**
	 * Add the ad to the ordered placement after publishing.
	 *
	 * @param int     $post_id    Post ID.
	 * @param WP_Post $post_after Post object following the update.
	 */
	public function add_ad_to_placement( $post_id, $post_after ) {
		// Don’t do this on revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$slug = sanitize_text_field( Params::post( 'advads-selling-add-to-placement', '' ) );

		if ( ! $slug || 'publish' !== $post_after->post_status ) {
			return;
		}

		$placement = wp_advads_get_placement_by_slug( $slug );

		if ( ! $placement || ! $placement->is_status( 'publish' ) ) {
			return;
		}

		$item = $placement->get_item_object();

		if ( ! $item || is_an_ad( $item ) ) {
			$placement->set_item( "ad_{$post_id}" );
			$placement->save();
		}

		if ( is_a_group( $item ) ) {
			$weights = $item->get_ad_weights();
			$item->set_ad_weights( $weights + [ $post_id => 10 ] );
			$item->save();
		}
	}
}
