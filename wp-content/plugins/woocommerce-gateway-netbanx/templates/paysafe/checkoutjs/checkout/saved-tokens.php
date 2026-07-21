<?php
/**
 * Saved Tokens Template
 *
 * Override this template by copying it to yourtheme/woocommerce/paysafe/checkoutjs/saved-tokens.php
 *
 * @var array                   $tokens
 * @var \WcPaysafe\Payment_Form $form This is the form class
 *
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Make sure we have the vital variables
$tokens = isset( $tokens ) && is_array( $tokens ) ? $tokens : array();

$gateway_id = $form->get_gateway()->id;

?>
<div class="woocommerce-<?php echo esc_attr( $gateway_id ); ?>-SavedPaymentMethods-wrapper">
	<?php if ( ! empty( $tokens ) ) { ?>
		<span class="paysafe_manage_tokens help" style="text-align: right; display: block;">
			<a href="<?php echo esc_url( \WcPaysafe\Tokens\Manage_Tokens::manage_tokens_url() ); ?>" class="button">
				<?php echo esc_html( __( 'Manage tokens', 'wc_paysafe' ) ); ?>
			</a>
		</span>
	<?php } ?>
	<ul class="woocommerce-<?php echo esc_attr( $gateway_id ); ?>-SavedPaymentMethods-token-wrapper wc-saved-payment-methods" data-count="<?php echo esc_attr( count( $tokens ) ) ?>">
		<?php
		/**
		 * @var \WC_Payment_Token_Paysafe_CC $token
		 */
		$keys        = array_keys( $tokens );
		$set_checked = false;
		foreach ( $tokens as $n => $token ) { ?>
			<li class="woocommerce-<?php echo esc_attr( $gateway_id ); ?>-SavedPaymentMethods-token" data-type="<?php echo esc_attr( $token->get_token_type() ); ?>">
				<label for="wc-<?php echo esc_attr( $gateway_id ); ?>-payment-token-<?php echo esc_attr( $token->get_id() ); ?>">
					<input id="wc-<?php echo esc_attr( $gateway_id ); ?>-payment-token-<?php echo esc_attr( $token->get_id() ); ?>"
					       type="radio"
					       name="wc-<?php echo esc_attr( $gateway_id ); ?>-payment-token"
					       value="<?php echo esc_attr( $token->get_id() ); ?>"
					       style="width:auto;"
					       class="woocommerce-<?php echo esc_attr( $gateway_id ); ?>-SavedPaymentMethods-tokenInput <?php echo esc_attr( $token->get_token_type() ); ?>"
						<?php
						// Needed some solution for legacy tokens because we did not previously have is_default
						if ( $token->get_is_default() || ( end( $keys ) == $n && ! $set_checked ) ) {
							$set_checked = true;
							checked( true );
						} ?>
					/>
					<?php echo wp_kses( $token->get_display_name(), apply_filters( 'wc_paysafe_label_allowed_elements', array(
						'strong' => array(),
						'em'     => array(),
						'b'      => array(),
						'i'      => array( 'class' => array() ),
						'span'   => array( 'class' => array() ),
						'img'    => array(
							'src' => array(),
							'alt' => array(),
						),
					) ) ) ?>
				</label>
			</li>
		<?php } ?>
		<li class="woocommerce-<?php echo esc_attr( $gateway_id ); ?>-SavedPaymentMethods-token-new">
			<label for="wc-<?php echo esc_attr( $gateway_id ); ?>-payment-token-new">
				<input id="wc-<?php echo esc_attr( $gateway_id ); ?>-payment-token-new"
				       type="radio"
				       name="wc-<?php echo esc_attr( $gateway_id ); ?>-payment-token"
				       value="new"
				       style="width:auto;"
				       class="woocommerce-<?php echo esc_attr( $gateway_id ); ?>-SavedPaymentMethods-tokenInput"
					<?php checked( $set_checked, false ); ?>
				/>
				<?php echo esc_html( __( 'Use a new payment method', 'wc_paysafe' ) ); ?>
			</label>
		</li>
	</ul>

	<div class="clear"></div>
</div>