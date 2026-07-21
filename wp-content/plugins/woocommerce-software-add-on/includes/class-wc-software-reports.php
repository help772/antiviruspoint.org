<?php
/**
 * WC_Software_Reports class.
 *
 * Shows reports related to software in the woocommerce backend
 */
class WC_Software_Reports {

	/**
	 * Constructor
	 */
	public function __construct( $debug = false ) {
		add_filter( 'woocommerce_reports_charts', array( $this, 'reports_tab' ) );
	}

	/**
	 * reports_tab function.
	 *
	 * @access public
	 */
	public function reports_tab( $reports ) {
		$reports['software'] = array(
			'title'  => __( 'Software', 'woocommerce' ),
			'charts' => array(
				array(
					'title'       => __( 'Overview', 'woocommerce' ),
					'description' => '',
					'hide_title'  => true,
					'function'    => array( $this, 'generate_report' ),
				),
				array(
					'title'       => __( 'Activations', 'woocommerce' ),
					'description' => '',
					'function'    => array( $this, 'generate_report' ),
				),
			),
		);
		return $reports;
	}

	/**
	 * generate_report function.
	 */
	public function generate_report() {
		$chart = ( empty( $_GET['report'] ) ) ? 0 : absint( $_GET['report'] );

		if ( $chart == 0 ) {
			$this->sales();
		} else {
			$this->activations();
		}
	}

	/**
	 * sales
	 */
	public function sales() {
		global $wpdb;

		$license_keys_count = $wpdb->get_var(
			"
			SELECT COUNT(key_id) FROM {$wpdb->wc_software_licenses}
		"
		);

		$activations_count = $wpdb->get_var(
			"
			SELECT COUNT(activation_id) FROM {$wpdb->wc_software_activations}
		"
		);

		$activations_active_count = $wpdb->get_var(
			"
			SELECT COUNT(activation_id) FROM {$wpdb->wc_software_activations}
			WHERE activation_active = 1
		"
		);

		$order_ids = wc_get_orders(
			array(
				'type'   => 'shop_order',
				'status' => array( 'wc-on-hold', 'wc-processing', 'wc-completed' ),
				'limit'  => -1,
				'return' => 'ids',
			)
		);

		$software_sales = 0;
		$software_items = 0;

		foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );

			$order_items = $order->get_items();

			foreach ( $order_items as $item ) {
				if ( ! $item instanceof WC_Order_Item_Product ) {
					continue;
				}

				$product = $item->get_product();

				if ( ! $product || 'yes' !== $product->get_meta( '_is_software' ) ) {
					continue;
				}

				$software_items += $item->get_quantity();
				$software_sales += (float) $item->get_total() + (float) $item->get_total_tax();
			}
		}

		?>
		<div id="poststuff" class="woocommerce-reports-wrap">
			<div class="woocommerce-reports-sidebar">
				<div class="postbox">
					<h3><span><?php _e( 'Total software sales', 'woocommerce-software-add-on' ); ?></span></h3>
					<div class="inside">
						<p class="stat">
						<?php
						if ( $software_sales > 0 ) {
							echo wc_price( $software_sales );
						} else {
							/* translators: No total sales to display in reporting */
							_e( 'n/a', 'woocommerce-software-add-on' );
						}
						?>
						</p>
					</div>
				</div>
				<div class="postbox">
					<h3><span><?php _e( 'Total software sold', 'woocommerce' ); ?></span></h3>
					<div class="inside">
						<p class="stat">
						<?php
						if ( $software_items > 0 ) {
							echo $software_items;
						} else {
							_e( 'n/a', 'woocommerce' );
						}
						?>
						</p>
					</div>
				</div>
				<div class="postbox">
					<h3><span><?php _e( 'Total license keys', 'woocommerce' ); ?></span></h3>
					<div class="inside">
						<p class="stat">
						<?php
						if ( $license_keys_count > 0 ) {
							echo (int) $license_keys_count;
						} else {
							_e( 'n/a', 'woocommerce' );
						}
						?>
						</p>
					</div>
				</div>
				<div class="postbox">
					<h3><span><?php _e( 'Total activations', 'woocommerce' ); ?></span></h3>
					<div class="inside">
						<p class="stat">
						<?php
						if ( $activations_count > 0 ) {
							echo $activations_count . ' (' . $activations_active_count . ' ' . __( 'active', 'woocommerce-software-add-on' ) . ')';
						} else {
							_e( 'n/a', 'woocommerce' );
						}
						?>
						</p>
					</div>
				</div>
			</div>
			<div class="woocommerce-reports-main">
				<div class="postbox">
					<h3><span><?php _e( 'Recent Activations', 'woocommerce' ); ?></span></h3>
					<div>
						<?php
							$activations = $wpdb->get_results(
								"
								SELECT * FROM {$wpdb->wc_software_activations} as activations
								LEFT JOIN {$wpdb->wc_software_licenses} as licenses ON activations.key_id = licenses.key_id
								ORDER BY activations.activation_time DESC
								LIMIT 50
							"
							);

						if ( sizeof( $activations ) > 0 ) {

							?>
								<div class="woocommerce_order_items_wrapper">
									<table id="activations-table" class="woocommerce_order_items" cellspacing="0">
										<thead>
											<tr>
												<th><?php _e( 'Order', 'woocommerce-software-add-on' ); ?></th>
												<th><?php _e( 'Software ID', 'woocommerce-software-add-on' ); ?></th>
												<th><?php _e( 'License Key', 'woocommerce-software-add-on' ); ?></th>
												<th><?php _e( 'Status', 'woocommerce-software-add-on' ); ?></th>
												<th><?php _e( 'Date &amp; Time', 'woocommerce-software-add-on' ); ?></th>
												<th><?php _e( 'Software Version', 'woocommerce-software-add-on' ); ?></th>
												<th><?php _e( 'Platform/OS', 'woocommerce-software-add-on' ); ?></th>
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
													<td>
													<?php
													if ( $activation->order_id ) :
														?>
														<a href="<?php echo admin_url( 'post.php?post=' . $activation->order_id . '&action=edit' ); ?>"><?php echo $activation->order_id; ?></a>
														<?php
else :
														_e( 'N/A', 'woocommerce-software-add-on' );
endif;
?>
</td>
													<td><?php echo $activation->software_product_id; ?></td>
													<td><?php echo $activation->license_key; ?></td>
													<td><?php echo ( $activation->activation_active ) ? __( 'Activated', 'woocommerce-software-add-on' ) : __( 'Deactivated', 'woocommerce-software-add-on' ); ?></td>
													<td><?php echo date( __( 'D j M Y \a\t h:ia', 'woocommerce-software-add-on' ), strtotime( $activation->activation_time ) ); ?></td>
													<td><?php echo $activation->software_version; ?></td>
													<td><?php echo ucwords( $activation->activation_platform ); ?></td>
												  </tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<?php
						} else {
							?>
								<p><?php _e( 'No activations yet', 'woocommerce-software-add-on' ); ?></p>
								<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php

	}

	function activations() {
		global $wpdb;

		$start_date = isset( $_POST['start_date'] ) ? $_POST['start_date'] : '';
		$end_date   = isset( $_POST['end_date'] ) ? $_POST['end_date'] : '';

		if ( ! $start_date ) {
			$start_date = date( 'Ymd', strtotime( date( 'Ym', current_time( 'timestamp' ) ) . '01' ) );
		}

		if ( ! $end_date ) {
			$end_date = date( 'Ymd', current_time( 'timestamp' ) );
		}

		$start_date = strtotime( $start_date );
		$end_date   = strtotime( $end_date );

		?>
		<form method="post" action="">
			<p>
				<label for="from"><?php _e( 'From:', 'woocommerce-software-add-on' ); ?></label>
				<input type="text" name="start_date" id="from" class="range_datepicker from" readonly="readonly" value="<?php echo esc_attr( date( 'Y-m-d', $start_date ) ); ?>" />

				<label for="to"><?php _e( 'To:', 'woocommerce-software-add-on' ); ?></label>
				<input type="text" name="end_date" id="to" class="range_datepicker to" readonly="readonly" value="<?php echo esc_attr( date( 'Y-m-d', $end_date ) ); ?>" />

				<input type="submit" class="button" value="<?php _e( 'Show', 'woocommerce' ); ?>" />
			</p>
		</form>
		<?php

		$activations = $wpdb->get_results(
			"
			SELECT * FROM {$wpdb->wc_software_activations} as activations
			LEFT JOIN {$wpdb->wc_software_licenses} as licenses ON activations.key_id = licenses.key_id
			WHERE date_format( activation_time ,'%Y%m%d') >= '" . date( 'Ymd', $start_date ) . "'
			AND date_format( activation_time ,'%Y%m%d') <= '" . date( 'Ymd', $end_date ) . "'
			ORDER BY activation_time DESC
			LIMIT 50
		"
		);

		if ( sizeof( $activations ) > 0 ) {

			?>
			<table id="activations-table" class="widefat" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e( 'Order', 'woocommerce-software-add-on' ); ?></th>
						<th><?php _e( 'Instance', 'woocommerce-software-add-on' ); ?></th>
						<th><?php _e( 'Software ID', 'woocommerce-software-add-on' ); ?></th>
						<th><?php _e( 'License Key', 'woocommerce-software-add-on' ); ?></th>
						<th><?php _e( 'Status', 'woocommerce-software-add-on' ); ?></th>
						<th><?php _e( 'Date &amp; Time', 'woocommerce-software-add-on' ); ?></th>
						<th><?php _e( 'Software Version', 'woocommerce-software-add-on' ); ?></th>
						<th><?php _e( 'Platform/OS', 'woocommerce-software-add-on' ); ?></th>
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
							<td>
							<?php
							if ( $activation->order_id ) :
								?>
								<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $activation->order_id . '&action=edit' ) ); ?>"><?php echo esc_html( $activation->order_id ); ?></a>
								<?php
else :
								_e( 'N/A', 'woocommerce-software-add-on' );
endif;
?>
</td>
							<td>
							<?php
							if ( $activation->instance ) :
								?>
								<?php echo $activation->instance; ?>
								<?php
else :
								_e( 'N/A', 'woocommerce-software-add-on' );
endif;
?>
</td>
							<td><?php echo $activation->software_product_id; ?></td>
							<td><?php echo $activation->license_key; ?></td>
							<td><?php echo ( $activation->activation_active ) ? __( 'Activated', 'woocommerce-software-add-on' ) : __( 'Deactivated', 'woocommerce-software-add-on' ); ?></td>
							<td><?php echo date( __( 'D j M Y \a\t h:ia', 'woocommerce-software-add-on' ), strtotime( $activation->activation_time ) ); ?></td>
							<td><?php echo $activation->software_version; ?></td>
							<td><?php echo ucwords( $activation->activation_platform ); ?></td>
						  </tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		} else {
			?>
			<p><?php _e( 'No activations found.', 'woocommerce-software-add-on' ); ?></p>
			<?php
		}
	}
}

$GLOBALS['WC_Software_Reports'] = new WC_Software_Reports();
