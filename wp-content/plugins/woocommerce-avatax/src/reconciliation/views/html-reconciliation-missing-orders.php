<?php
/**
 * Reconciliation Missing Orders Tab
 *
 * Displays orders that exist in WooCommerce but not in Avalara
 * Note: Filter variables are inherited from html-reconciliation-main.php
 */

// @codeCoverageIgnoreStart

defined('ABSPATH') or exit;

$missingOrders = [];
?>

<div class="wc-avatax-reconciliation-missing-orders">
	
	<!-- Results Header -->
	<div class="reconciliation-results-header">
		<p class="description">
			<?php esc_html_e('Here are the orders that are reported in WooCommerce but
				absent in the Avalara company connected to WooCommerce.', 'woocommerce-avatax');
			?>
		</p>	
		<div class="results-actions">
			<span class="results-count">
			<?php
			printf(
				/* translators: %d: number of missing orders */
				esc_html__('%d orders found', 'woocommerce-avatax'),
				count($missingOrders)
			);
			?>
			</span>
			<button type="button" class="button" style="display: none;">
				<span class="dashicons dashicons-download"></span>
				<?php esc_html_e('Download', 'woocommerce-avatax'); ?>
			</button>
		</div>
	</div>

	<!-- Results Table (always rendered; JS populates tbody) -->
	<table class="wp-list-table widefat fixed striped wc-avatax-reconciliation-table" aria-label="<?php esc_attr_e('Missing Orders', 'woocommerce-avatax'); ?>">
		<thead>
			<tr>
				<th><?php esc_html_e('Order ID', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Document Code', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Date', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Customer', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Total', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Tax', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Status', 'woocommerce-avatax'); ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

	<div class="tablenav bottom wc-avatax-reconciliation-tablenav" data-pagination-tab="missing-orders" style="display: none;">
		<div class="tablenav-pages wc-avatax-reconciliation-pagination">
			<span class="displaying-num"></span>
			<span class="pagination-links"></span>
		</div>
	</div>

	<div class="reconciliation-no-results" style="display: none;">
		<span class="dashicons dashicons-yes-alt"></span>
		<p>
			<?php
			esc_html_e(
				'All good here! There are no missing orders.',
				'woocommerce-avatax'
			);
			?>
		</p>
	</div>

</div>
<?php // @codeCoverageIgnoreEnd ?>
