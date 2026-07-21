<?php
/**
 * Flat rate box sizes template for Canada Post shipping method.
 *
 * Available template variables:
 *
 * @var WC_Shipping_Canada_Post $shipping_method
 * @var bool                    $flat_rate_enabled
 * @var array                   $flat_rate_boxes
 * @var array                   $flat_rate_boxes_enabled
 *
 * @package woocommerce-shipping-canada-post
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $flat_rate_boxes ) ) :
	$maybe_hide = $flat_rate_enabled ? '' : 'style="display:none;"';
	?>
	<tr id="canada_post_flat_rate_box_sizes_table_wrapper" <?php echo esc_attr( $maybe_hide ); ?>>
		<th scope="row" class="titledesc"><?php esc_html_e( 'Flat Rate Box Sizes', 'woocommerce-shipping-canada-post' ); ?></th>
		<td class="forminp">
			<table id="canada_post_flat_rate_box_sizes_table" class="widefat canada-post-settings-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Outer Length', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Outer Width', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Outer Height', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Inner Length', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Inner Width', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Inner Height', 'woocommerce-shipping-canada-post' ); ?></th>
						<th>
							<?php esc_html_e( 'Weight of Box', 'woocommerce-shipping-canada-post' ); ?>
							<img class="help_tip" width="16" height="16" data-tip="<?php esc_attr_e( 'Weight of the actual box and will be added to the weight of the contents. This will increase the cost of shipping.', 'woocommerce-shipping-canada-post' ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" />
						</th>
						<th>
							<?php esc_html_e( 'Max Weight', 'woocommerce-shipping-canada-post' ); ?>
							<img class="help_tip" width="16" height="16" data-tip="<?php esc_attr_e( 'Maximum weight your box can hold. This includes contents weight and box weight.', 'woocommerce-shipping-canada-post' ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" />
						</th>
						<th><?php esc_html_e( 'Cost', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Enabled', 'woocommerce-shipping-canada-post' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $flat_rate_boxes as $box_id => $box ) {
						$name = str_replace( ' Flat Rate Box', '', $box['name'] );
						?>
						<tr class="flat-rate-box">
							<td><input type="text" size="10" value="<?php echo esc_attr( $name ); ?>" disabled="disabled" /></td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['length'] ); ?>" disabled="disabled" />cm</td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['width'] ); ?>" disabled="disabled" />cm</td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['height'] ); ?>" disabled="disabled" />cm</td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['length'] ); ?>" disabled="disabled" />cm</td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['width'] ); ?>" disabled="disabled" />cm</td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['height'] ); ?>" disabled="disabled" />cm</td>
							<td><input type="text" size="5" value="0" disabled="disabled" />kg</td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['max_weight'] ); ?>" disabled="disabled" />kg</td>
							<td><input type="text" size="5" value="<?php echo esc_attr( $box['cost'] ); ?>" disabled="disabled" />CAD</td>
							<td><input type="checkbox" name="boxes_enabled[<?php echo esc_attr( $box_id ); ?>]" <?php checked( ! isset( $flat_rate_boxes_enabled[ $box_id ] ) || wc_string_to_bool( $flat_rate_boxes_enabled[ $box_id ] ), true ); ?> /></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="11">
							<small class="description"><?php esc_html_e( 'Items will be packed into the enabled flat rate box sizes from this table when flat rate services are enabled. Only enabled boxes will be used for packing.', 'woocommerce-shipping-canada-post' ); ?></small>
						</th>
					</tr>
				</tfoot>
			</table>
		</td>
	</tr>
<?php endif; ?>
