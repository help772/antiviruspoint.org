<?php
/**
 * Advanced Ad product add to cart
 *
 * @package AdvancedAds\SellingAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 *
 * @var array $ad_types       selected ad types on the product edit page.
 * @var array $prices         product price data.
 * @var array $placements     selected placements on the product page.
 * @var array $placements_raw all available placements.
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Early bail!!
	exit;
}

global $product;

if ( ! $product->is_purchasable() || $product->get_type() !== 'advanced_ad' ) {
	return;
}

$product_id = $product->get_id();

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" enctype='multipart/form-data'>

		<?php if ( is_array( $prices ) && count( $prices ) ) : ?>
			<ul id="advads-selling-option-ad-price">
				<?php
				$first = true;
				foreach ( $prices as $_price ) {
					printf(
						'<li><label><input type="radio" name="option_ad_price" value="%1$s" %2$s>&nbsp;%3$s, %4$s</label></li>',
						esc_attr( $_price['value'] ),
						$first ? ' checked="checked"' : '',
						esc_html( $_price['label'] ),
						wc_price( $_price['price'] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
					$first = false;
				}
				?>
			</ul>
		<?php endif; ?>

		<?php if ( is_array( $placements ) && 1 < count( $placements ) ) : ?>
			<p id="advads_selling_option_headline"><?php esc_html_e( 'Select a placement', 'advanced-ads-selling' ); ?></p>
			<ul id="advads_selling_option_placements">
				<?php
				$first = true;
				foreach ( $placements as $slug ) {
					$placement = wp_advads_get_placement_by_slug( $slug );
					printf(
						'<li><label><input type="radio" name="option_ad_placement" value="%s"%s>&nbsp;%s</label></li>',
						esc_attr( $placement->get_slug() ),
						$first ? ' checked="checked"' : '',
						esc_html( $placement->get_title() )
					);
					$first = false;
				}
				?>
			</ul>
		<?php elseif ( 1 === count( $placements ) ) : ?>
			<input type="hidden" name="option_ad_placement" value="<?php echo esc_attr( $placements[0] ); ?>">
		<?php endif; ?>

		<p><?php esc_html_e( 'Available ad types', 'advanced-ads-selling' ); ?></p>

		<ul>
			<?php
			foreach ( wp_advads_get_ad_types() as $ad_type ) {
				if ( ! in_array( $ad_type->get_id(), $ad_types, true ) ) {
					continue;
				}
				printf( '<li>%s</li>', esc_html( $ad_type->get_title() ) );
			}
			?>
		</ul>

		<p><?php esc_html_e( 'You will be able to submit the ad content after the purchase.', 'advanced-ads-selling' ); ?></p>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>"/>
		<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
