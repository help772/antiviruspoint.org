<table class="widefat wp-list-table">
	<thead>
		<tr>
			<th><?php _e( 'Row', 'woocommerce-software-add-on' ); ?></th>
			<th><?php _e( 'Result', 'woocommerce-software-add-on' ); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $result['items'] as $index => $row ) : ?>
		<tr>
			<td><?php echo esc_html( $index + 1 ); ?></td>
			<td><?php echo esc_html( $row['message'] ); ?></td>
			<td>
			<?php if ( ! empty( $row['key_id'] ) ) : ?>
				<span class="woocommerce-licence-key-imported">Imported</span>
			<?php else : ?>
				<span class="woocommerce-licence-key-skipped">Skipped</span>
			<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
