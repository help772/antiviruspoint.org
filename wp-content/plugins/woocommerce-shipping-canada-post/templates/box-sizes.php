<?php
/**
 * Box sizes template for Canada Post shipping method.
 *
 * Available template variables:
 *
 * @var WC_Shipping_Canada_Post $shipping_method
 *
 * @package woocommerce-shipping-canada-post
 */

defined( 'ABSPATH' ) || exit;

?>
<tr id="canada_post_box_sizes_table_wrapper">
	<th scope="row" class="titledesc"><?php esc_html_e( 'Box Sizes', 'woocommerce-shipping-canada-post' ); ?></th>
	<td class="forminp">
		<table id="canada_post_box_sizes_table" class="widefat canada-post-settings-table">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
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
					<th>
						<?php esc_html_e( 'Enabled', 'woocommerce-shipping-canada-post' ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="11">
						<a href="#" class="button plus insert"><?php esc_html_e( 'Add Box', 'woocommerce-shipping-canada-post' ); ?></a>
						<a href="#" class="button minus remove"><?php esc_html_e( 'Remove selected box(es)', 'woocommerce-shipping-canada-post' ); ?></a>
					</th>
				</tr>
				<tr>
					<th colspan="11">
						<small class="description"><?php esc_html_e( 'Items will be packed into these boxes depending based on item dimensions and volume. Outer dimensions will be passed to Canada Post, whereas inner dimensions will be used for packing. Items not fitting into boxes will be packed individually.', 'woocommerce-shipping-canada-post' ); ?></small>
					</th>
				</tr>
			</tfoot>
			<tbody id="rates">
				<?php
				// Add custom boxes.
				if ( $shipping_method->boxes ) {
					foreach ( $shipping_method->boxes as $key => $box ) {
						?>
						<tr>
							<td class="check-column"><input type="checkbox" /></td>
							<td><input type="text" size="10" name="<?php echo esc_attr( "boxes_name[$key]" ); ?>" value="<?php echo isset( $box['name'] ) ? esc_attr( $box['name'] ) : ''; ?>" required /></td>
							<td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="<?php echo esc_attr( "boxes_outer_length[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['outer_length'] ); ?>" />cm</td>
							<td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="<?php echo esc_attr( "boxes_outer_width[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['outer_width'] ); ?>" />cm</td>
							<td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="<?php echo esc_attr( "boxes_outer_height[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['outer_height'] ); ?>" />cm</td>
							<td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="<?php echo esc_attr( "boxes_inner_length[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['inner_length'] ); ?>" />cm</td>
							<td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="<?php echo esc_attr( "boxes_inner_width[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['inner_width'] ); ?>" />cm</td>
							<td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="<?php echo esc_attr( "boxes_inner_height[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['inner_height'] ); ?>" />cm</td>
							<td><input type="number" min="0" step="0.001" max="9999" size="5" name="<?php echo esc_attr( "boxes_box_weight[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['box_weight'], 3 ); ?>" />kg</td>
							<td><input type="number" min="0.001" step="0.001" max="9999" size="5" name="<?php echo esc_attr( "boxes_max_weight[$key]" ); ?>" value="<?php $shipping_method->output_formatted_box_measurements( $box['max_weight'], 3 ); ?>" />kg</td>
							<td><input type="checkbox" name="boxes_enabled[<?php echo esc_attr( $key ); ?>]" <?php checked( ! isset( $box['enabled'] ) || wc_string_to_bool( $box['enabled'] ), true ); ?> value="yes" /></td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</td>
</tr>
