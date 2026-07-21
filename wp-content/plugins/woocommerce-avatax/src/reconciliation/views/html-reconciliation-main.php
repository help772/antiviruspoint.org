<?php
/**
 * Reconciliation Main View with Tabs
 *
 * @var string $currentTab Current active tab
 */

// @codeCoverageIgnoreStart

defined('ABSPATH') or exit;

// Define available tabs (Guide content moved to header accordion)
$tabs = [
	'overview' => __('Overview', 'woocommerce-avatax'),
	'missing-orders' => __('Missing orders', 'woocommerce-avatax'),
	'mismatches' => __('Mismatches', 'woocommerce-avatax'),
];

// Get filter parameters (shared across all tabs)
$fromDate = isset($_GET['from_date'])
	? sanitize_text_field(wp_unslash($_GET['from_date']))
	: date('Y-m-01');
$toDate = isset($_GET['to_date'])
	? sanitize_text_field(wp_unslash($_GET['to_date']))
	: date('Y-m-d');
$documentType = isset($_GET['document_type']) 
	? sanitize_text_field(wp_unslash($_GET['document_type'])) 
	: 'SalesInvoice';

// Build base URL for tab links
$baseUrl = admin_url('admin.php?page=wc-settings&tab=avalara&section=avatax-reconciliation');
?>

<?php
if (!isset($currentTab)) {
	$currentTab = 'overview';
}
?>
<div class="wc-avatax-reconciliation-wrapper">
	<!-- Header Section (accordion head: title + Guide toggle) -->
	<div class="wc-avatax-reconciliation-header">
		<h2><?php esc_html_e('WooCommerce ↔ Avalara Reconciliation', 'woocommerce-avatax'); ?></h2>
		<button type="button" class="button button-link reconciliation-guide-toggle" id="reconciliation-guide-toggle" aria-expanded="false" aria-controls="reconciliation-guide-accordion">
			<span class="reconciliation-guide-toggle-label"><?php esc_html_e('Guide', 'woocommerce-avatax'); ?></span>
			<span class="dashicons dashicons-arrow-down-alt2 reconciliation-guide-toggle-icon" aria-hidden="true"></span>
		</button>
	</div>
	<!-- Guide accordion panel (content from former Support/Guide tab) -->
	<div class="wc-avatax-reconciliation-guide-accordion" id="reconciliation-guide-accordion" role="region" aria-labelledby="reconciliation-guide-toggle" hidden>
		<?php
		require_once(
			$this->get_plugin()->get_plugin_path()
			. '/src/reconciliation/views/html-reconciliation-support.php'
		);
		?>
	</div>

	<!-- Common Filters Section (applies to all tabs) -->
	<div class="wc-avatax-reconciliation-filters">
		<div class="scope-filters-title">
			<span class="dashicons dashicons-filter"></span>
			<strong><?php esc_html_e('Scope & Filters', 'woocommerce-avatax'); ?></strong>
		</div>
		<p class="description">
			<?php
			esc_html_e(
				'Choose a date range and filters. Results use Avalara read-only APIs (List/Get/Report).',
				'woocommerce-avatax'
			);
			?>
		</p>

		<div class="reconciliation-filter-form">
			<div class="filter-row">
				<!-- Date Range & Document Type Group -->
				<div class="filter-group-container">
					<div class="filter-group">
						<label for="from_date"><?php esc_html_e('From', 'woocommerce-avatax'); ?></label>
						<input type="date" id="from_date" name="from_date" value="<?php echo esc_attr($fromDate); ?>" />
					</div>

					<div class="filter-group">
						<label for="to_date"><?php esc_html_e('To', 'woocommerce-avatax'); ?></label>
						<input type="date" id="to_date" name="to_date" value="<?php echo esc_attr($toDate); ?>" />
					</div>

					<div class="filter-group">
						<label for="document_type">
							<?php esc_html_e('Document Type', 'woocommerce-avatax'); ?>
						</label>
						<select id="document_type" name="document_type">
						<option value="All" <?php selected($documentType, 'All'); ?>>
							<?php esc_html_e('All', 'woocommerce-avatax'); ?>
						</option>
						<option value="SalesInvoice" <?php selected($documentType, 'SalesInvoice'); ?>>
							<?php esc_html_e('Sales Invoice', 'woocommerce-avatax'); ?>
						</option>
						<option value="ReturnInvoice" <?php selected($documentType, 'ReturnInvoice'); ?>>
							<?php esc_html_e('Return Invoice', 'woocommerce-avatax'); ?>
						</option>
						</select>
					</div>
				</div>

				<!-- Clear and Run Buttons -->
				<div class="filter-actions">
					<button type="button" class="button reconciliation-clear-filters" id="reconciliation_clear_btn">
						<span class="dashicons dashicons-no-alt"></span>
						<?php esc_html_e('Clear', 'woocommerce-avatax'); ?>
					</button>
					<button type="button" class="button button-primary">
						<span class="dashicons dashicons-search"></span>
						<?php esc_html_e('Run', 'woocommerce-avatax'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Previous Runs Section -->
	<div class="wc-avatax-reconciliation-previous-runs">
		<div class="previous-runs-title">
			<span class="dashicons dashicons-backup"></span>
			<strong><?php esc_html_e('Previous Runs', 'woocommerce-avatax'); ?></strong>
			<button type="button" class="button button-small previous-runs-refresh">
				<span class="dashicons dashicons-update"></span>
				<?php esc_html_e('Refresh', 'woocommerce-avatax'); ?>
			</button>
		</div>
		<table class="wc-avatax-reconciliation-table widefat striped" id="previous-runs-table">
			<thead>
				<tr>
					<th><?php esc_html_e('#', 'woocommerce-avatax'); ?></th>
					<th><?php esc_html_e('Date Range', 'woocommerce-avatax'); ?></th>
					<th><?php esc_html_e('Document Type', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('WooCommerce Orders', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Avalara Transactions', 'woocommerce-avatax'); ?></th>
				<th><?php esc_html_e('Missing Orders', 'woocommerce-avatax'); ?></th>
					<th><?php esc_html_e('Mismatches', 'woocommerce-avatax'); ?></th>
					<th><?php esc_html_e('Actions', 'woocommerce-avatax'); ?></th>
					<th><?php esc_html_e('Status', 'woocommerce-avatax'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="previous-runs-loading">
					<td colspan="9">
						<span class="dashicons dashicons-update reconciliation-spinner"></span>
						<?php esc_html_e('Loading previous runs…', 'woocommerce-avatax'); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="previous-runs-empty" style="display:none;">
			<span class="dashicons dashicons-info"></span>
			<?php esc_html_e('No previous runs found. Run a reconciliation to see results here.', 'woocommerce-avatax'); ?>
		</p>
		<p class="previous-runs-info-text" style="display:none;">
			<span class="dashicons dashicons-info-outline"></span>
			<?php esc_html_e('Only the 10 most recent runs are displayed.', 'woocommerce-avatax'); ?>
		</p>
	</div>

	<!-- Tabs Navigation (hidden by default, shown by JS when viewing a run) -->
	<ul class="subsubsub avalara-subsubsub wc-avatax-reconciliation-tabs" style="display:none;">
		<?php foreach ($tabs as $tabKey => $tabLabel) : ?>
			<li>
				<a href="#"
				   class="reconciliation-tab-link <?php echo ($currentTab === $tabKey) ? 'current' : ''; ?>"
				   data-tab="<?php echo esc_attr($tabKey); ?>">
					<?php echo esc_html($tabLabel); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<br class="clear" />

	<!-- Tab Content Container (hidden by default, shown by JS when viewing a run) -->
	<div class="wc-avatax-reconciliation-content" style="display:none;">
		<!-- Overview Tab -->
		<div
			class="reconciliation-tab-content <?php echo ($currentTab === 'overview') ? 'active' : ''; ?>"
			data-tab-content="overview"
		>
			<?php
			require_once(
				$this->get_plugin()->get_plugin_path()
				. '/src/reconciliation/views/html-reconciliation-overview.php'
			);
			?>
		</div>

		<!-- Missing Orders Tab -->
		<div
			class="reconciliation-tab-content <?php echo ($currentTab === 'missing-orders') ? 'active' : ''; ?>"
			data-tab-content="missing-orders"
		>
			<?php
			require_once(
				$this->get_plugin()->get_plugin_path()
				. '/src/reconciliation/views/html-reconciliation-missing-orders.php'
			);
			?>
		</div>

		<!-- Mismatches Tab -->
		<div
			class="reconciliation-tab-content <?php echo ($currentTab === 'mismatches') ? 'active' : ''; ?>"
			data-tab-content="mismatches"
		>
			<?php
			require_once(
				$this->get_plugin()->get_plugin_path()
				. '/src/reconciliation/views/html-reconciliation-mismatches.php'
			);
			?>
		</div>
	</div>
</div>
<?php // @codeCoverageIgnoreEnd ?>
