<?php
/**
 * Iframe break template. Outputs a bit of javascript to break out of the iframe.
 *
 * Don't edit this template directly as it will be overwritten with every plugin update.
 * Override this template by copying it to yourtheme/woocommerce/netbanx/iframe-break.php
 *
 * @deprecated This template will be removed, please use the templates in "templates\paysafe" folder
 * @since      2.3
 * @author     VanboDevelops
 *
 * @var string $redirect_url The URL to redirect to
 */
?>
<script data-cfasync="false" type="text/javascript">window.parent.location.href = '<?php echo esc_url_raw( $redirect_url ); ?>';</script>
