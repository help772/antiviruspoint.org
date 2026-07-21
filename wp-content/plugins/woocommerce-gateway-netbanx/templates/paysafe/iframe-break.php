<?php
/**
 * Iframe break template. Outputs a bit of javascript to break out of the iframe.
 *
 * Don't edit this template directly as it will be overwritten with every plugin update.
 * Override this template by copying it to yourtheme/woocommerce/paysafe/iframe-break.php
 *
 * @since  3.0
 * @author VanboDevelops
 *
 * @var string $redirect_url The URL to redirect to
 */
?>
<script data-cfasync="false" type="text/javascript">window.parent.location.href = '<?php echo esc_url_raw( $redirect_url ); ?>';</script>
