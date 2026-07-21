<?php
/**
 * Update payment method form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/paysafe/checkoutjs/myaccount/update-payment-method.php.
 *
 * @package WcPaysafe/Templates
 * @version 3.3.0
 * @var \WC_Payment_Token_Paysafe_CC|\WC_Payment_Token_Paysafe_DD $token
 * @var \WcPaysafe\Payment_Form                                   $form
 */

defined( 'ABSPATH' ) || exit;

/**
 * This is the payment method generation form. Output:
 * 0. Provide a review of the payment method: Card with ending on ...1234 exp: 12/2018
 * 1. The billing address of the customer.
 * 1.1. Notify the customer that the payment method will be
 * updated with the billing address and provide a link in case they want to change the billing address first
 * 2. Provide a small payment form with a button to click and open the payment form Iframe.
 */
?>

<div class="wc-paysafe-update-payment-method-wrapper">
	<div class="wc-paysafe-card-wrapper">

		<h3><?php echo esc_html( __( 'Method to update', 'wc_paysafe' ) ); ?></h3>
		<p><?php echo \WcPaysafe\Helpers\Formatting::kses_form_html( $token->get_display_name() ); ?></p>
	</div>
	<div class="wc-paysafe-address-wrapper">
		<h3><?php echo esc_html( __( 'Billing Address', 'wc_paysafe' ) ); ?></h3>
		<small><?php echo esc_html( __( 'This is the address your card will be updated with. Please change your billing address, if it does not match your card data.', 'wc_paysafe' ) ); ?></small>
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>" class="edit"><?php echo esc_html( __( 'Edit', 'wc_paysafe' ) ); ?></a>
		<address>
			<?php
			$address = wc_get_account_formatted_address( 'billing' );
			echo $address ? wp_kses_post( $address ) : wp_kses_post( __( 'You have not set up this type of address yet.', 'wc_paysafe' ) );
			?>
		</address>
	</div>
</div>