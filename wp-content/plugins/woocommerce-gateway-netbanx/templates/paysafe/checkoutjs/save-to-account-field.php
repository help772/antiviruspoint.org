<?php
/**
 * PhpStorm
 *
 * @since  3.3.0
 * @author VanboDevelops
 * @var \WcPaysafe\Payment_Form $form
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="<?php echo esc_attr( $form->get_gateway()->id ); ?>-create-account-wrapper">
	<p class="form-row form-row-wide">
		<input id="wc-<?php echo esc_attr( $form->get_gateway()->id ); ?>-save-to-account"
		       name="wc-<?php echo esc_attr( $form->get_gateway()->id ); ?>-save-to-account"
		       type="checkbox"
		       class="wc-<?php echo esc_attr( $form->get_gateway()->id ); ?>-save-to-account"
			<?php checked( $form->get_gateway()->save_card_checked_by_default, 'yes' ) ?>
		       value="true"
		       style="width:auto;"
		/>
		<label for="wc-<?php echo esc_attr( $form->get_gateway()->id ); ?>-save-to-account" style="display:inline;">
			<?php echo wp_kses( apply_filters( 'wc_paysafe_save_to_account_text', $form->get_gateway()->save_card_text, $form->get_gateway() ),
				array(
					'a' => array(
						'href'  => array(),
						'title' => array(),
					),
				) ); ?>
			<span class="required">*</span>
		</label>
	</p>
</div>
