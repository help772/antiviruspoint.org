<?php
/**
 * CVC field template
 *
 * Override this template by copying it to yourtheme/woocommerce/paysafe/checkoutjs/cvv-field.php
 *
 * @var \WcPaysafe\Payment_Form $form This is the form class
 *
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gateway_id = $form->get_gateway()->id;

?>
<div class="woocommerce-<?php echo esc_attr( $gateway_id ); ?>-cvv-wrap">
	<p class="form-row paytrace-cvv-wrapper">
		<label for="<?php echo esc_attr( $gateway_id ) ?>-card-cvv" title="Code on the back of the card">
			<?php echo esc_html( __( "CVV", 'wc_paysafe' ) ); ?>
			<span class="required">*</span>
		</label>
		<input id="<?php echo esc_attr( $gateway_id ) ?>-card-cvv" class="input-text wc-credit-card-form-card-cvv" type="tel" inputmode="numeric" autocomplete="off" autocorrect="no" autocapitalize="no" spellcheck="no" maxlength="4" placeholder="<?php echo esc_attr( __( 'CVV', 'wc_paysafe' ) ) ?>" name="<?php echo esc_attr( $gateway_id ) ?>-card-cvv" />
	</p>
	<div class="clear"></div>
</div>