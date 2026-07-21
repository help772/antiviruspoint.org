<?php
/**
 * Reconciliation Mismatches Tab
 *
 * Displays orders with differences between WooCommerce and Avalara
 * Note: Filter variables are inherited from html-reconciliation-main.php
 */

// @codeCoverageIgnoreStart

defined('ABSPATH') or exit;

$mismatches = [];
?>

<div class="wc-avatax-reconciliation-mismatches">
	
	<!-- Results Header -->
	<div class="reconciliation-results-header">
		<p class="description">
			<?php esc_html_e('Here are the differences in tax amounts and line items.', 'woocommerce-avatax'); ?>
		</p>
		<div class="results-actions">
			<span class="results-count">
				<?php
				printf(
					/* translators: %d: number of mismatches */
					esc_html__('%d mismatches found', 'woocommerce-avatax'),
					count($mismatches)
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
	<table class="wp-list-table widefat fixed striped wc-avatax-reconciliation-table" aria-label="<?php esc_attr_e('Mismatches', 'woocommerce-avatax'); ?>">
		<thead>
			<tr>
				<th><?php esc_html_e('Order ID', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Document Code', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Date', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('WooCommerce', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Avalara', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Difference Type', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Details', 'woocommerce-avatax'); ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

	<div class="tablenav bottom wc-avatax-reconciliation-tablenav" data-pagination-tab="mismatches" style="display: none;">
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
				'All good here! There are no mismatches in your transactional data.',
				'woocommerce-avatax'
			);
			?>
		</p>
	</div>

</div>
<?php // @codeCoverageIgnoreEnd ?>
