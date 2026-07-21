<?php
/**
 * Iframe HTML template.
 *
 * Don't edit this template directly as it will be overwritten with every plugin update.
 * Override this template by copying it to yourtheme/woocommerce/netbanx/iframe.php
 *
 * @deprecated This template will be removed, please use the templates in "templates\paysafe" folder
 * @since      2.0
 * @author     VanboDevelops
 */
?>
<iframe
	name="paysafe-iframe"
	id="paysafe-iframe"
	src="<?php echo esc_attr( $location ); ?>"
	frameborder="0"
	width="<?php echo esc_attr( $width ); ?>"
	height="<?php echo esc_attr( $height ); ?>"
	scrolling="<?php echo esc_attr( $scroll ); ?>"
>
	<p>
		<?php echo \WcPaysafe\Helpers\Formatting::kses_form_html( sprintf(
			__(
				'Your browser does not support iframes.
			 %sClick Here%s to get redirected to Paysafe payment page. ', 'wc_paysafe'
			),
			'<a href="' . esc_url_raw( $location ) . '">',
			'</a>'
		) ); ?>
	</p>
</iframe>