<form id="wc_lost_license_form" method="post">

	<div class="form-row form-row-first">
		<p><?php _e( 'Please tell us the email address used during the purchase. Your license along with the order receipt will be sent by email.', 'woocommerce-software-add-on' ); ?></p>
		<p><?php _e( 'If your email address has changed, please contact us.', 'woocommerce-software-add-on' ); ?></p>
	</div>

	<div class="form-row form-row-last">
		<noscript><p class="woocommerce-error"><?php _e( 'Javascript must be enabled to use this form.', 'woocommerce-software-add-on' ); ?></p></noscript>
		<p><label for="wc_email"><?php _e( 'Your email address', 'woocommerce-software-add-on' ); ?>:</label> <input type="text" class="input-text" id="wc_email" name="wc_email" /></p>
		<p><input type="submit" class="button-alt" name="wc_lost_license_btn" id="wc_lost_license_btn" value="<?php esc_attr_e( 'Email License Keys', 'woocommerce-software-add-on' ); ?>" /></p>
	</div>

	<div class="clear"></div>
</form>

<script type="text/javascript">

	jQuery(function(){
		jQuery('#wc_lost_license_form').on( 'submit', function(){

			$form = jQuery('#wc_lost_license_form');
			$form.block({message: null, overlayCSS: { background: '#fff', opacity: 0.6 }});

			if ( ! $form.hasClass('loading') ) {

				$form.addClass('loading');

				jQuery('.woocommerce-error, .woocommerce-message').fadeOut('fast', function(){
					jQuery(this).remove();
				});

				var data = {
					action: 			'woocommerce_lost_license',
					security: 			'<?php echo wp_create_nonce( 'wc-lost-license' ); ?>',
					email: 				jQuery('input[name="wc_email"]').val()
				};

				jQuery.post( "<?php echo esc_url( admin_url( 'admin-ajax.php', 'relative' ) ); ?>", data, function( response ) {

					$form.removeClass('loading');
					$form.unblock();

					if ( response.success ) {

						$form.prepend( '<div class="woocommerce-message">' + response.message + '</div>' ).fadeIn();

					} else {
						if ( response.success === false ) {
							$form.prepend( '<div class="woocommerce-error">' + response.message + '</div>' ).fadeIn();
						} else {
							$form.prepend( '<div class="woocommerce-error">' + '<?php _e( 'Error processing request', 'woocommerce-software-add-on' ); ?>' + '</div>' ).fadeIn();
						}
					}

				}, "json" );
			}

			return false;
		});
	});
</script>
