<?php // phpcs:ignoreFile

use AdvancedAds\Tracking\Public_Ad;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$customer_id     = get_current_user_id();
$customer_orders = Advanced_Ads_Selling_Order::get_customer_orders( $customer_id );
?>

<table>
	<tr>
		<td>
			<strong>
				<?php esc_html_e( 'Ad Title - Product Name', 'advanced-ads-selling' ); ?>
			</strong>
		</td>
		<td>
			<strong>
				<?php esc_html_e( 'Statistics', 'advanced-ads-selling' ); ?>
			</strong>
		</td>
	</tr>
	<?php
	if ( empty( $customer_orders ) ) {
		esc_html_e( 'No orders found', 'advanced-ads-selling' );
	} else {
		foreach ( $customer_orders as $order_key => $order ) {
			if ( Advanced_Ads_Selling_Order::has_ads( $order->get_id() ) ) {
				foreach ( $order->get_items() as $item_id => $item ) {
					$item_id      = $item->get_id();
					$product_name = $item->get_name();
					$product_id   = $item['product_id'];

					$ad_id = Advanced_Ads_Selling_Order::order_item_id_to_ad_id( $item_id );

					if ( class_exists( Public_Ad::class ) ) {
						$public_ad = new Public_Ad( $ad_id );
						?>
						<tr>
							<td>
								<?php echo esc_html( get_post( $ad_id )->post_title ) . ' - ' . esc_html( $product_name ); ?>
							</td>
							<td>
								<?php
								if ( $public_ad->has_tracking() ) :
									?>
									<a style="text-decoration: none; border-bottom: 0;" href="<?php echo esc_url( $public_ad->get_url() ); ?>">
										<?php esc_html_e( 'Link', 'advanced-ads-selling' ); ?>
									</a>
								<?php endif; ?>
							</td>
						</tr>
						<?php
					}
				}
			}
		}
	}
	?>
</table>
