<?php
/**
 * View - Update notice.
 *
 * @since 1.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var string $update_url The update URL.
 */
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><strong><?php esc_html_e( 'Software Add-On for WooCommerce', 'woocommerce-software-add-on' ); ?></strong> &#8211; <?php echo esc_html_x( 'We need to update your store database to the latest version.', 'admin notice', 'woocommerce-software-add-on' ); ?></p>
	<p class="submit">
		<a href="<?php echo esc_url( $update_url ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Run the updater', 'woocommerce-software-add-on' ); ?>
		</a>
	</p>
</div>
<script type="text/javascript">
	jQuery( '.wc-update-now' ).on( 'click', function() {
		return window.confirm( '<?php echo esc_js( _x( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'admin notice', 'woocommerce-software-add-on' ) ); ?>' ); // jshint ignore:line
	});
</script>
