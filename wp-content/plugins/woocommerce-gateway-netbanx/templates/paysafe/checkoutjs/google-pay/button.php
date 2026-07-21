<?php
/**
 * @since  4.0.0
 * @author VanboDevelops
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="<?php echo esc_attr( $gateway->id ); ?>_wrapper" style="margin-top: 1em;clear:both;display: none;">
	<div id="<?php echo esc_attr( $gateway->id ); ?>-button" style="display: flex; justify-content: center;">
	</div>
</div>

<p id="<?php echo esc_attr( $gateway->id ); ?>-request-button-separator" style="margin-top:1.5em;text-align:center;display: none;">
	&mdash; <?php esc_html_e( 'OR', 'wc_paysafe' ); ?> &mdash;
</p>