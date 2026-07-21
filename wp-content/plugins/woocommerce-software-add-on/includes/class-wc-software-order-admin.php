<?php

use Themesquad\WC_Software_Addon\Utilities\Compat_Utils;

/**
 * WC_Software_Order_Admin class.
 */
class WC_Software_Order_Admin extends WC_Software {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		// Ajax
		add_action( 'wp_ajax_woocommerce_delete_license_key', array( $this, 'delete_key' ) );
		add_action( 'wp_ajax_woocommerce_add_license_key', array( $this, 'add_key' ) );
		add_action( 'wp_ajax_woocommerce_toggle_activation', array( $this, 'toggle_activation' ) );

		// Hooks
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'order_save_data' ) );
	}

	/**
	 * Delete a key via ajax
	 */
	function delete_key() {
		global $wpdb;

		check_ajax_referer( 'delete-key', 'security' );

		$key_id = intval( $_POST['key_id'] );

		$wpdb->query(
			"
			DELETE FROM {$wpdb->wc_software_licenses}
			WHERE key_id = $key_id
		"
		);

		$wpdb->query(
			"
			DELETE FROM {$wpdb->wc_software_activations}
			WHERE key_id = $key_id
		"
		);

		die();
	}

	/**
	 * Add a key via ajax
	 */
	function add_key() {

		check_ajax_referer( 'add-key', 'security' );

		global $wpdb;

		if ( ! isset( $_POST['product_id'] ) || ! isset( $_POST['order_id'] ) ) {
			return;
		}

		$product_id = intval( $_POST['product_id'] );
		$order_id   = intval( $_POST['order_id'] );
		$order      = wc_get_order( $order_id );
		$meta       = get_post_custom( $product_id );

		$wpdb->hide_errors();

		$data = array(
			'order_id'            => $order_id,
			'activation_email'    => $order->get_billing_email(),
			'prefix'              => '',
			'license_key'         => ( empty( $meta['_software_license_key_prefix'][0] ) ? '' : $meta['_software_license_key_prefix'][0] ) . $this->generate_license_key(),
			'software_product_id' => empty( $meta['_software_product_id'][0] ) ? '' : $meta['_software_product_id'][0],
			'software_version'    => empty( $meta['_software_version'][0] ) ? '' : $meta['_software_version'][0],
			'activations_limit'   => empty( $meta['_software_activations'][0] ) ? '' : (int) $meta['_software_activations'][0],
		);

		$key_id = $this->save_license_key( $data );

		if ( $key_id ) {
			$data['success'] = 1;
			$data['key_id']  = $key_id;
			wp_send_json( $data );
		}

		die();
	}

	/**
	 * Toggle activation via ajax
	 */
	function toggle_activation() {

		check_ajax_referer( 'toggle-activation', 'security' );

		global $wpdb;

		$activation_id = intval( $_POST['activation_id'] );

		$active = $wpdb->get_var( "SELECT activation_active FROM {$wpdb->wc_software_activations} WHERE activation_id = {$activation_id}" );

		$active = ( $active ) ? 0 : 1;

		$wpdb->query(
			"
			UPDATE {$wpdb->wc_software_activations}
			SET activation_active = {$active}
			WHERE activation_id = $activation_id;
		"
		);

		echo ( $active ) ? __( 'Activated', 'woocommerce-software-add-on' ) : __( 'Deactivated', 'woocommerce-software-add-on' );

		die();
	}

	/**
	 * registers meta boxes
	 *
	 * @since 1.0
	 * @return void
	 */
	function add_meta_boxes() {
		$screen = Compat_Utils::get_order_admin_screen();

		add_meta_box( 'woocommerce-order-license-keys', __( 'Software License Keys', 'woocommerce-software-add-on' ), array( $this, 'license_keys_meta_box' ), $screen, 'normal', 'high' );
		add_meta_box( 'wc_software-activation-data', __( 'Activations', 'woocommerce-software-add-on' ), array( $this, 'activation_meta_box' ), $screen, 'normal', 'high' );
	}

	/**
	 * License keys meta box.
	 *
	 * @param mixed $object Order or post object.
	 */
	public function license_keys_meta_box( $object ) {
		global $wpdb;

		$order_id = ( $object instanceof WC_Order ? $object->get_id() : $object->ID );
		?>
		<div class="order_license_keys wc-metaboxes-wrapper">

			<div class="wc-metaboxes">

				<?php
					$i = -1;

					$license_keys = $wpdb->get_results(
						"
						SELECT * FROM {$wpdb->wc_software_licenses}
						WHERE order_id = $order_id
					"
					);

				if ( $license_keys && sizeof( $license_keys ) > 0 ) {
					foreach ( $license_keys as $license_key ) :
						$i++;

						?>
						<div class="wc-metabox closed">
							<h3 class="fixed">
								<button type="button" rel="<?php echo $license_key->key_id; ?>" class="delete_key button"><?php _e( 'Delete key', 'woocommerce-software-add-on' ); ?></button>
								<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce-software-add-on' ); ?>"></div>
								<strong><?php printf( __( 'Product: %1$s, version %2$s', 'woocommerce-software-add-on' ), $license_key->software_product_id, $license_key->software_version ); ?> &mdash; <?php echo $license_key->license_key; ?></strong>
								<input type="hidden" name="key_id[<?php echo $i; ?>]" value="<?php echo $license_key->key_id; ?>" />
							</h3>
							<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
								<tbody>
									<tr>
										<td>
											<label><?php _e( 'License Key', 'woocommerce-software-add-on' ); ?>:</label>
											<input type="text" class="short" name="license_key[<?php echo $i; ?>]" value="<?php echo $license_key->license_key; ?>" />
										</td>
										<td>
											<label><?php _e( 'Activation Email', 'woocommerce-software-add-on' ); ?>:</label>
											<input type="text" class="short" name="activation_email[<?php echo $i; ?>]" value="<?php echo $license_key->activation_email; ?>" />
										</td>
										<td>
											<label><?php _e( 'Activation Limit', 'woocommerce-software-add-on' ); ?>:</label>
											<input type="text" class="short" name="activations_limit[<?php echo $i; ?>]" value="<?php echo $license_key->activations_limit; ?>" placeholder="<?php _e( 'Unlimited', 'woocommerce-software-add-on' ); ?>" />
										</td>
									</tr>
									<tr>
										<td>
											<label><?php _e( 'Software Product ID', 'woocommerce-software-add-on' ); ?>:</label>
											<input type="text" class="short" name="software_product_id[<?php echo $i; ?>]" value="<?php echo $license_key->software_product_id; ?>" />
										</td>
										<td>
											<label><?php _e( 'Software Version', 'woocommerce-software-add-on' ); ?>:</label>
											<input type="text" class="short" name="software_version[<?php echo $i; ?>]" value="<?php echo $license_key->software_version; ?>" />
										</td>
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</div>
										<?php
					endforeach;
				};
				?>
			</div>

			<div class="toolbar">
				<p class="buttons">
					<select name="add_software_id" class="add_software_id chosen_select_nostd" data-placeholder="<?php _e( 'Choose a software product&hellip;', 'woocommerce' ); ?>">
						<?php
							echo '<option value=""></option>';

							$args     = array(
								'post_type'      => 'product',
								'posts_per_page' => -1,
								'post_status'    => 'publish',
								'order'          => 'ASC',
								'orderby'        => 'title',
								'meta_query'     => array(
									array(
										'key'   => '_is_software',
										'value' => 'yes',
									),
								),
							);
							$products = get_posts( $args );

							if ( $products ) {
								foreach ( $products as $product ) :

									$sku = get_post_meta( $product->ID, '_sku', true );

									if ( $sku ) {
										$sku = ' SKU: ' . $sku;
									}

									echo '<option value="' . $product->ID . '">' . $product->post_title . $sku . ' (#' . $product->ID . '' . $sku . ')</option>';

									$args_get_children = array(
										'post_type'      => array( 'product_variation', 'product' ),
										'posts_per_page' => -1,
										'order'          => 'ASC',
										'orderby'        => 'title',
										'post_parent'    => $product->ID,
									);

									$children_products = get_children( $args_get_children );
									if ( ! empty( $children_products ) ) :

										foreach ( $children_products as $child ) :

											echo '<option value="' . $child->ID . '">&nbsp;&nbsp;&mdash;&nbsp;' . $child->post_title . '</option>';

										endforeach;

																endif;

							endforeach;
							};
							?>
					</select>

					<button type="button" class="button add_key"><?php _e( 'Add License Key', 'woocommerce-software-add-on' ); ?></button>
				</p>
				<div class="clear"></div>
			</div>

		</div>
		<?php
		/**
		 * Javascript
		 */
		ob_start();
		?>
		jQuery(function(){

			jQuery('.order_license_keys').on('click', 'button.add_key', function(){

				var product = jQuery('select.add_software_id').val();

				if ( ! product ) return false;

				jQuery('.order_license_keys').block({message: null, overlayCSS: { background: '#fff', opacity: 0.6 }});

				var data = {
					action: 		'woocommerce_add_license_key',
					product_id: 	product,
					order_id: 		'<?php echo $order_id; ?>',
					security: 		'<?php echo wp_create_nonce( 'add-key' ); ?>'
				};

				jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function( new_software ) {
					var loop = jQuery('.order_license_keys .wc-metabox').length;

					if ( new_software && new_software.success == 1 ) {

						jQuery('.order_license_keys .wc-metaboxes').append('<div class="wc-metabox closed">\
							<h3 class="fixed">\
								<button type="button" rel="' + new_software.key_id + '" class="delete_key button"><?php _e( 'Delete key', 'woocommerce-software-add-on' ); ?></button>\
								<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce-software-add-on' ); ?>"></div>\
								<strong><?php printf( __( 'Product: %1$s, version %2$s', 'woocommerce-software-add-on' ), "' + new_software.software_product_id + '", "' + new_software.software_version + '" ); ?> &mdash; ' + new_software.license_key + '</strong>\
								<input type="hidden" name="key_id[' + loop + ']" value="' + new_software.key_id + '" />\
							</h3>\
							<table cellpadding="0" cellspacing="0" class="wc-metabox-content">\
								<tbody>	\
									<tr>\
										<td>\
											<label><?php _e( 'License Key', 'woocommerce-software-add-on' ); ?>:</label>\
											<input type="text" class="short" name="license_key[' + loop + ']" value="' + new_software.license_key + '" />\
										</td>\
										<td>\
											<label><?php _e( 'Activation Email', 'woocommerce-software-add-on' ); ?>:</label>\
											<input type="text" class="short" name="activation_email[' + loop + ']" value="' + new_software.activation_email + '" />\
										</td>\
										<td>\
											<label><?php _e( 'Activations Remaining', 'woocommerce-software-add-on' ); ?>:</label>\
											<input type="text" class="short" name="activations_limit[' + loop + ']" value="' + new_software.activations_limit + '" placeholder="<?php _e( 'Unlimited', 'woocommerce-software-add-on' ); ?>" />\
										</td>\
									</tr>\
									<tr>\
										<td>\
											<label><?php _e( 'Software Product ID', 'woocommerce-software-add-on' ); ?>:</label>\
											<input type="text" class="short" name="software_product_id[' + loop + ']" value="' + new_software.software_product_id + '" />\
										</td>\
										<td>\
											<label><?php _e( 'Software Version', 'woocommerce-software-add-on' ); ?>:</label>\
											<input type="text" class="short" name="software_version[' + loop + ']" value="' + new_software.software_version + '" />\
										</td>\
										<td>&nbsp;</td>\
									</tr>\
								</tbody>\
							</table>\
						</div>');

					}

					jQuery('.order_license_keys').unblock();

				});

				return false;

			});

			jQuery('.order_license_keys').on('click', 'button.delete_key', function(e){
				e.preventDefault();
				var answer = confirm('<?php _e( 'Are you sure you want to delete this license key?', 'woocommerce-software-add-on' ); ?>');
				if (answer){

					var el = jQuery(this).parent().parent();

					var key_id = jQuery(this).attr('rel');

					if ( key_id > 0 ) {

						jQuery(el).block({message: null, overlayCSS: { background: '#fff', opacity: 0.6 }});

						var data = {
							action: 		'woocommerce_delete_license_key',
							key_id: 		key_id,
							order_id: 		'<?php echo $order_id; ?>',
							security: 		'<?php echo wp_create_nonce( 'delete-key' ); ?>'
						};

						jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function(response) {
							// Success
							jQuery(el).fadeOut('300', function(){
								jQuery(el).remove();
							});
						});

					} else {
						jQuery(el).fadeOut('300', function(){
							jQuery(el).remove();
						});
					}

				}
				return false;
			});

		});
		<?php
		$javascript = ob_get_clean();
		wc_enqueue_js( $javascript );
	}

	/**
	 * License activation meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $object Order or post object.
	 */
	public function activation_meta_box( $object ) {
		global $wpdb;

		$order_id = ( $object instanceof WC_Order ? $object->get_id() : $object->ID );

		$activations = $wpdb->get_results(
			"
			SELECT * FROM {$wpdb->wc_software_activations} as activations
			LEFT JOIN {$wpdb->wc_software_licenses} as licenses ON activations.key_id = licenses.key_id
			WHERE order_id = {$order_id}
		"
		);

		if ( sizeof( $activations ) > 0 ) {

			?>
			<div class="woocommerce_order_items_wrapper">
				<table id="activations-table" class="woocommerce_order_items" cellspacing="0">
					<thead>
						<tr>
							<th><?php _e( 'License Key', 'woocommerce-software-add-on' ); ?></th>
							<th><?php _e( 'Instance', 'woocommerce-software-add-on' ); ?></th>
							<th><?php _e( 'Software ID', 'woocommerce-software-add-on' ); ?></th>
							<th><?php _e( 'Status', 'woocommerce-software-add-on' ); ?></th>
							<th><?php _e( 'Date &amp; Time', 'woocommerce-software-add-on' ); ?></th>
							<th><?php _e( 'Software Version', 'woocommerce-software-add-on' ); ?></th>
							<th><?php _e( 'Platform/OS', 'woocommerce-software-add-on' ); ?></th>
							<th><?php _e( 'Action', 'woocommerce-software-add-on' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1; foreach ( $activations as $activation ) :
							$i++;
							?>
							<tr
							<?php
							if ( $i % 2 == 1 ) {
								echo ' class="alternate"';}
							?>
							>
								<td><?php echo $activation->license_key; ?></td>
								<td><?php echo ( $activation->instance ) ? $activation->instance : _e( 'N/A', 'woocommerce-software-add-on' ); ?></td>
								<td><?php echo $activation->software_product_id; ?></td>
								<td class="activation_active"><?php echo ( $activation->activation_active ) ? __( 'Activated', 'woocommerce-software-add-on' ) : __( 'Deactivated', 'woocommerce-software-add-on' ); ?></td>
								<td><?php echo date( __( 'D j M Y \a\t h:ia', 'woocommerce-software-add-on' ), strtotime( $activation->activation_time ) ); ?></td>
								<td><?php echo $activation->software_version; ?></td>
								<td><?php echo ucwords( $activation->activation_platform ); ?></td>
								<td>
									<button class="button toggle_activation" data-id="<?php echo $activation->activation_id; ?>"><?php _e( 'Toggle Activation', 'woocommerce-software-add-on' ); ?></button>
								</td>
							  </tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php
			/**
			 * Javascript
			 */
			ob_start();
			?>
			jQuery(function(){

				jQuery('#activations-table').on('click', 'button.toggle_activation', function(){

					var $this = jQuery( this );
					var activation = jQuery(this).attr( 'data-id' );

					if ( ! activation ) return;

					jQuery('#activations-table').block({message: null, overlayCSS: { background: '#fff', opacity: 0.6 }});

					var data = {
						action: 		'woocommerce_toggle_activation',
						activation_id: 	activation,
						security: 		'<?php echo wp_create_nonce( 'toggle-activation' ); ?>'
					};

					jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function( result ) {

						$this.closest('tr').find('td.activation_active').html( result );

						jQuery('#activations-table').unblock();

					});

					return false;

				});
			});
			<?php
			$javascript = ob_get_clean();

			wc_enqueue_js( $javascript );

		} else {
			?>
			<p style="padding:0 12px 12px;"><?php _e( 'No activations yet', 'woocommerce-software-add-on' ); ?></p>
			<?php
		}
	}

	/**
	 * saves the data inputed into the order boxes
	 *
	 * @see order_meta_box()
	 * @since 1.0
	 * @return void
	 */
	function order_save_data() {
		global $wpdb;

		$key_id              = isset( $_POST['key_id'] ) ? stripslashes_deep( $_POST['key_id'] ) : array();
		$license_key         = isset( $_POST['license_key'] ) ? stripslashes_deep( $_POST['license_key'] ) : array();
		$activation_email    = isset( $_POST['activation_email'] ) ? stripslashes_deep( $_POST['activation_email'] ) : array();
		$activations_limit   = isset( $_POST['activations_limit'] ) ? stripslashes_deep( $_POST['activations_limit'] ) : array();
		$software_product_id = isset( $_POST['software_product_id'] ) ? stripslashes_deep( $_POST['software_product_id'] ) : array();
		$software_version    = isset( $_POST['software_version'] ) ? stripslashes_deep( $_POST['software_version'] ) : array();
		$key_id_count        = sizeof( $key_id );

		for ( $i = 0; $i < $key_id_count; $i++ ) {
			if ( ! isset( $key_id[ $i ] ) ) {
				continue;
			}

			$data = array(
				'license_key'         => esc_attr( $license_key[ $i ] ),
				'activation_email'    => esc_attr( $activation_email[ $i ] ),
				'activations_limit'   => ( $activations_limit[ $i ] == '' ) ? '' : (int) $activations_limit[ $i ],
				'software_product_id' => esc_attr( $software_product_id[ $i ] ),
				'software_version'    => esc_attr( $software_version[ $i ] ),
			);

			$format = array(
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
			);

			$wpdb->update(
				$wpdb->prefix . 'woocommerce_software_licenses',
				$data,
				array( 'key_id' => $key_id[ $i ] ),
				$format,
				array( '%d' )
			);

		}

	}

}

$GLOBALS['WC_Software_Order_Admin'] = new WC_Software_Order_Admin(); // Init
