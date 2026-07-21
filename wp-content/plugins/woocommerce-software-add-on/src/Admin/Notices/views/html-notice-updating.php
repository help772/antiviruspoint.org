<?php
/**
 * View - Updating notice.
 *
 * @since 1.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $force_update_url The force update URL.
 */
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p>
		<strong><?php esc_html_e( 'Software Add-On for WooCommerce', 'woocommerce-software-add-on' ); ?></strong> &#8211; <?php echo esc_html_x( 'Your database is being updated in the background.', 'admin notice', 'woocommerce-software-add-on' ); ?>
		<a href="<?php echo esc_url( $force_update_url ); ?>">
			<?php esc_html_e( 'Taking a while? Click here to run it now.', 'woocommerce-software-add-on' ); ?>
		</a>
	</p>
</div>
