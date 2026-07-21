<?php
/**
 * Setup page setting markup
 *
 * @package AdvancedAds\SellingAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.5.0
 *
 * @var WP_Post[] $pages list of static pages.
 * @var int|false $public_page_id ID of the ad setup page if any, `false` otherwise.
 * @var bool $permalink_warning `true` if the ad setup page is generated automatically and permalink setting is set to `plain` (which does not work).
 */

?>
<select name="<?php echo esc_attr( Advanced_Ads_Selling_Plugin::OPTION_KEY ); ?>[setup-page-id]">
	<option value="" <?php selected( false, $public_page_id ); ?>><?php esc_html_e( '(default)', 'advanced-ads-selling' ); ?></option>
	<?php foreach ( $pages as $page ) : // phpcs:ignore ?>
	<option value="<?php echo esc_attr( $page->ID ); ?>"<?php selected( $page->ID, $public_page_id ); ?>><?php echo esc_html( $page->post_title ); ?></option>
	<?php endforeach; ?>
</select>
<p class="description"><?php esc_html_e( 'Choose the page on which you want to show the ad setup for the client after the purchase. Leave blank for the default layout.', 'advanced-ads-selling' ); ?></p>

<?php if ( $permalink_warning ) : ?>
<div class="notice error advads-notice inline">
	<p><?php esc_html_e( 'The default setup page does not work with the default plain permalink setting.', 'advanced-ads-selling' ); ?></p>
</div>
<?php endif; ?>
