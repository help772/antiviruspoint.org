<?php
/**
 * Checkout JS iframe HTML template.
 *
 * Don't edit this template directly as it will be overwritten with every plugin update.
 * Override this template by copying it to yourtheme/woocommerce/paysafe/checkoutjs/pay/payment-block.php
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 * @var \WcPaysafe\Payment_Form $form
 * @var string                  $button_text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form method="post" id="paysafe_checkout_payment_form">
	<?php do_action( 'wc_paysafe_checkoutjs_before_submit_button' ); ?>
	<input type="submit" class="button-alt" id="submit_paysafe_payment_form" value="<?php echo esc_attr( $button_text ); ?>" />
	<?php do_action( 'wc_paysafe_checkoutjs_after_submit_button' ); ?>
	
	<?php $form->output_save_to_account_field(); ?>
</form>

