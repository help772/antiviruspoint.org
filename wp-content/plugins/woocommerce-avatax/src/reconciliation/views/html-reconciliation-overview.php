<?php
/**
 * Reconciliation Overview Tab
 *
 * Displays summary cards with reconciliation statistics
 * Note: Filter variables are inherited from html-reconciliation-main.php
 */

// @codeCoverageIgnoreStart

defined('ABSPATH') or exit;
?>

<div class="wc-avatax-reconciliation-overview" id="overview-tab-content">
	
	<!-- Summary Section Title -->
	<div class="overview-section-title">
		<p class="description">
			<?php esc_html_e('Here is the summary of orders in WooCommerce, total transactions in Avalara, the number of missing orders, and the number of mismatches in order details.', 'woocommerce-avatax'); ?>
		</p>
	</div>

	<!-- Summary Cards -->
	<div class="wc-avatax-reconciliation-cards" id="overview-cards">
		
		<!-- WooCommerce Orders Card -->
		<div class="reconciliation-card">
			<div class="card-icon">
				<span class="dashicons dashicons-wordpress"></span>
			</div>
			<div class="card-content">
				<h4><?php esc_html_e('WooCommerce orders', 'woocommerce-avatax'); ?></h4>
				<div class="card-value">0</div>
			</div>
		</div>

		<!-- Avalara Transactions Card -->
		<div class="reconciliation-card">
			<div class="card-icon">
				<span class="dashicons dashicons-cloud"></span>
			</div>
			<div class="card-content">
				<h4><?php esc_html_e('Avalara transactions', 'woocommerce-avatax'); ?></h4>
				<div class="card-value">0</div>
			</div>
		</div>

		<!-- Missing in Avalara Card -->
		<div class="reconciliation-card card-warning">
			<div class="card-icon">
				<span class="dashicons dashicons-warning"></span>
			</div>
			<div class="card-content">
				<h4><?php esc_html_e('Missing in Avalara', 'woocommerce-avatax'); ?></h4>
				<div class="card-value">0</div>
				<p class="card-description">
					<?php esc_html_e('Orders not reported to Avalara', 'woocommerce-avatax'); ?>
				</p>
			</div>
		</div>

		<!-- Mismatches Card -->
		<div class="reconciliation-card card-error">
			<div class="card-icon">
				<span class="dashicons dashicons-dismiss"></span>
			</div>
			<div class="card-content">
				<h4><?php esc_html_e('Mismatches', 'woocommerce-avatax'); ?></h4>
				<div class="card-value">0</div>
				<p class="card-description">
					<?php esc_html_e('Tax/total amount differences', 'woocommerce-avatax'); ?>
				</p>
			</div>
		</div>

	</div>

</div>
<?php // @codeCoverageIgnoreEnd ?>
