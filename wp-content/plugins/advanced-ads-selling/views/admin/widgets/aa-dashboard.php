<?php
/**
 * Widget for AA Dashboard
 *
 * @package AdvancedAds
 */

?>
<div class="advads-widget-wrapper">
	<div class="section-title">
		<h3><?php esc_html_e( 'Advanced Ads Selling Ads', 'advanced-ads-selling' ); ?></h3>
	</div>

	<?php if ( 0 === count( $draft_ads ) + count( $pending_ads ) ) : ?>
	<p><?php esc_html_e( 'No ad purchases to review', 'advanced-ads-selling' ); ?></p>
	<?php endif; ?>

	<?php if ( count( $draft_ads ) ) : ?>
	<h4 class="m-0"><?php esc_html_e( 'New ad purchases', 'advanced-ads-selling' ); ?></h4>
	<ul>
		<?php foreach ( $draft_ads as $_draft_ad ) : ?>
		<li>
			<a href="<?php echo esc_attr( get_edit_post_link( $_draft_ad->ID ) ); ?>">
				<?php echo esc_html( $_draft_ad->post_title ); ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>

	<?php if ( count( $pending_ads ) ) : ?>
	<h4 class="m-0"><?php esc_html_e( 'Pending ad purchases', 'advanced-ads-selling' ); ?></h4>
	<ul>
		<?php foreach ( $pending_ads as $_pending_ad ) : ?>
		<li>
			<a href="<?php echo esc_attr( get_edit_post_link( $_pending_ad->ID ) ); ?>">
				<?php echo esc_html( $_pending_ad->post_title ); ?>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
