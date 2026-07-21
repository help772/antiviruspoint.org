<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php if ( sizeof( $keys ) > 0 ) : ?>

	<?php foreach ( $keys as $key ) : ?>

		<h3><?php echo esc_html( $key->software_product_id ); ?> <?php
		if ( $key->software_version ) {
			printf( __( 'Version %s', 'woocommerce-software-add-on' ), esc_html( $key->software_version ) );}
		?>
		</h3>

		<ul>
			<li><?php _e( 'License Email:', 'woocommerce-software-add-on' ); ?> <strong><?php echo esc_html( $key->activation_email ); ?></strong></li>
			<li><?php _e( 'License Key:', 'woocommerce-software-add-on' ); ?> <strong><?php echo esc_html( $key->license_key ); ?></strong></li>
			<?php
			if ( $remaining = $GLOBALS['wc_software']->activations_remaining( $key->key_id ) ) {
				echo '<li>' . sprintf( __( '%d activations remaining', 'woocommerce-software-add-on' ), $remaining ) . '</li>';}
			?>
		</ul>

	<?php endforeach; ?>

<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
