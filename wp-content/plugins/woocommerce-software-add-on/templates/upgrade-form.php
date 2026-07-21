<div class="upgrade_license_key">

	<h3><?php _e( 'Upgrading from an old version?', 'woocommerce-software-add-on' ); ?></h3>

	<p><?php printf( __( 'Upgrade from %1$s for just %2$s by entering your details below.', 'woocommerce-software-add-on' ), $upgradable_product, wc_price( $price ) ); ?></p>

	<p class="form-row form-row-wide">
		<label for="activation_email"><?php _e( 'Activation email:', 'woocommerce-software-add-on' ); ?></label>
		<input name="activation_email" class="input-text" id="activation_email" value="" placeholder="<?php esc_attr_e( 'Enter your email address', 'woocommerce-software-add-on' ); ?>" />
	</p>

	<p class="form-row form-row-wide">
		<label for="license_key"><?php _e( 'License Key:', 'woocommerce-software-add-on' ); ?></label>
		<input name="license_key" class="input-text" id="license_key" value="<?php echo esc_attr( $prefix ? $prefix : '' ); ?>" placeholder="<?php esc_attr_e( 'Enter your license key', 'woocommerce-software-add-on' ); ?>" />
	</p>

	<p>
		<input type="submit" class="button" name="upgrade_software" value="<?php esc_attr_e( 'Upgrade', 'woocommerce-software-add-on' ); ?>" />
		<input type="hidden" name="software_product_id" value="<?php echo esc_attr( $software_product_id ); ?>" />
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" />
	</p>
</div>
