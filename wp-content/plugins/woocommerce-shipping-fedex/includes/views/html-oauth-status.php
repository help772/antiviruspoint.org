<?php
/**
 * OAuth field template file.
 *
 * @var WC_Shipping_Fedex $shipping_method The FedEx shipping method instance.
 *
 * @package WC_Shipping_Fedex
 */

if ( empty( $field_key ) || empty( $data ) || ! $shipping_method instanceof WC_Shipping_Fedex ) {
	return;
}
?>
<tr valign="top" id="fedex_oauth_status">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $shipping_method->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
	</th>
	<td class="forminp">
		<div <?php echo $shipping_method->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			$has_credentials = ! empty( $shipping_method->get_client_id() ) && ! empty( $shipping_method->get_client_secret() );
			$is_authenticated = $shipping_method->fedex_oauth->is_authenticated();
			?>
			<?php if ( $is_authenticated ) : ?>
				<p class="fedex-oauth-status-success">
					<span class="dashicons dashicons-yes-alt fedex-oauth-status-icon"></span>
					<?php esc_html_e( 'Authenticated', 'woocommerce-shipping-fedex' ); ?>
				</p>
				<p class="fedex-oauth-status-description">
					<?php esc_html_e( 'Your FedEx REST API connection is working correctly.', 'woocommerce-shipping-fedex' ); ?>
				</p>
			<?php elseif ( $has_credentials ) : ?>
				<p class="fedex-oauth-status-error">
					<span class="dashicons dashicons-warning fedex-oauth-status-icon"></span>
					<?php esc_html_e( 'Authentication Failed', 'woocommerce-shipping-fedex' ); ?>
				</p>
				<p class="fedex-oauth-status-error-detail">
					<?php esc_html_e( 'Unable to authenticate with FedEx. Please verify your Client ID and Client Secret are correct.', 'woocommerce-shipping-fedex' ); ?>
				</p>
				<p class="fedex-oauth-status-description">
					<?php
					printf(
						/* translators: %s: WooCommerce logs URL */
						esc_html__( 'Check the %s for detailed error information.', 'woocommerce-shipping-fedex' ),
						'<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs' ) ) . '" target="_blank">' . esc_html__( 'WooCommerce logs', 'woocommerce-shipping-fedex' ) . '</a>'
					);
					?>
				</p>
			<?php else : ?>
				<p class="fedex-oauth-status-error">
					<span class="dashicons dashicons-dismiss fedex-oauth-status-icon"></span>
					<?php esc_html_e( 'Not Authenticated', 'woocommerce-shipping-fedex' ); ?>
				</p>
				<p class="fedex-oauth-status-description">
					<?php esc_html_e( 'Enter your FedEx Client ID and Client Secret and click "Save changes".', 'woocommerce-shipping-fedex' ); ?>
				</p>
			<?php endif; ?>
		</div>
	</td>
</tr>

