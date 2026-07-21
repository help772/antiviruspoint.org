<?php
/**
 * Reconciliation Support Tab
 *
 * Displays help and support information for the reconciliation utility
 */

// @codeCoverageIgnoreStart

defined('ABSPATH') or exit;
?>

<div class="wc-avatax-reconciliation-support">
	
	<div class="support-section">
		<h4><?php esc_html_e('What is reconciliation?', 'woocommerce-avatax'); ?></h4>
		<p>
			<?php
			esc_html_e(
				'Reconciliation helps you understand the discrepancies in taxes in Avalara and WooCommerce. '
				. 'This lets you maintain data consistency and rectify issues to stay tax compliant.',
				'woocommerce-avatax'
			);
			?>
		</p>

		<h4><?php esc_html_e('What can you do with reconciliation?', 'woocommerce-avatax'); ?></h4>
		<p><?php esc_html_e('Use Reconciliation to do the following:', 'woocommerce-avatax'); ?></p>
		<p>
			<strong><?php esc_html_e('Choose a date range:', 'woocommerce-avatax'); ?></strong>
			<?php esc_html_e('Select the time period you want to review the transactional data. You can select up to 3 months at a time.', 'woocommerce-avatax'); ?>
		</p>
		<p>
			<strong><?php esc_html_e('Select document type:', 'woocommerce-avatax'); ?></strong>
			<?php esc_html_e('Choose whether you would like to see details of sales invoice, return invoice or both.', 'woocommerce-avatax'); ?>
		</p>
		<p>
			<strong><?php esc_html_e('Run reconciliation:', 'woocommerce-avatax'); ?></strong>
			<?php esc_html_e('Fetch data from both Avalara and WooCommerce and get a quick overview of the data.', 'woocommerce-avatax'); ?>
		</p>
		<p>
			<strong><?php esc_html_e('Explore the tabs:', 'woocommerce-avatax'); ?></strong>
			<?php esc_html_e('Check the overview, missing orders or mismatches tabs for detailed information.', 'woocommerce-avatax'); ?>
		</p>
	</div>

	<div class="support-section">
		<h3><?php esc_html_e('Frequently Asked Questions (FAQs)', 'woocommerce-avatax'); ?></h3>

		<h4><?php esc_html_e('Why are some orders missing in Avalara?', 'woocommerce-avatax'); ?></h4>
		<p><?php esc_html_e('Orders may be missing for one or more of the following reasons:', 'woocommerce-avatax'); ?></p>
		<ul>
			<li><?php esc_html_e('AvaTax was not connected when the order was placed.', 'woocommerce-avatax'); ?></li>
			<li><?php esc_html_e('Tax calculation was disabled for specific products or customers.', 'woocommerce-avatax'); ?></li>
			<li><?php esc_html_e('There was a technical error that prevented order submission.', 'woocommerce-avatax'); ?></li>
			<li><?php esc_html_e('Orders were manually created without reporting in AvaTax.', 'woocommerce-avatax'); ?></li>
		</ul>

		<h4><?php esc_html_e('Why is there a mismatch in tax amount?', 'woocommerce-avatax'); ?></h4>
		<p><?php esc_html_e('Mismatch in tax amount can occur due to one or more of the following reasons:', 'woocommerce-avatax'); ?></p>
		<ul>
			<li><?php esc_html_e('Manual tax adjustments in WooCommerce', 'woocommerce-avatax'); ?></li>
			<li><?php esc_html_e('Rounding differences between WooCommerce and AvaTax', 'woocommerce-avatax'); ?></li>
			<li><?php esc_html_e('Tax rate changes between when tax was calculated and when the order was submitted', 'woocommerce-avatax'); ?></li>
			<li><?php esc_html_e('Partial refunds that weren\'t synced properly', 'woocommerce-avatax'); ?></li>
		</ul>

		<h4><?php esc_html_e('How do I fix a mismatch?', 'woocommerce-avatax'); ?></h4>
		<p><?php esc_html_e('To fix a mismatch in tax amount or line item or status, you need to perform the following steps:', 'woocommerce-avatax'); ?></p>
		<ol>
			<li><?php esc_html_e('Note the document code of the order that has discrepancies.', 'woocommerce-avatax'); ?></li>
			<li>
				<?php
				printf(
					/* translators: %s: Avalara > Transactions path */
					esc_html__('Go to %s and select the document code that has discrepancies.', 'woocommerce-avatax'),
					'<strong>' . esc_html__('Avalara > Transactions', 'woocommerce-avatax') . '</strong>'
				);
				?>
			</li>
			<li><?php esc_html_e('Make appropriate changes like editing the tax amount, adding or removing a line item and updating the status.', 'woocommerce-avatax'); ?></li>
		</ol>
		<p>
			<?php
			printf(
				/* translators: %s: Avalara support link */
				esc_html__('If you still see a mismatch or need additional help, contact %s.', 'woocommerce-avatax'),
				'<a href="https://community.avalara.com/" target="_blank" rel="noopener noreferrer">' . esc_html__('Avalara support', 'woocommerce-avatax') . '</a>'
			);
			?>
		</p>
	</div>

	<div class="support-section">
		<h3><?php esc_html_e('More resources', 'woocommerce-avatax'); ?></h3>
		<p>
			<?php
			esc_html_e(
				'If you have any questions about discrepancies in data or tax calculation or anything related to the reconciliation, check the following links:',
				'woocommerce-avatax'
			);
			?>
		</p>
		<ul class="support-links">
			<li>
				<span class="dashicons dashicons-book"></span>
				<a
					href="https://knowledge.avalara.com/bundle/xcz1669121545288_xcz1669121545288/page/pnh1670474357253.html"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?php esc_html_e('WooCommerce AvaTax documentation', 'woocommerce-avatax'); ?>
				</a>
			</li>
			<li>
				<span class="dashicons dashicons-external"></span>
				<a href="https://community.avalara.com/" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e('Avalara community', 'woocommerce-avatax'); ?>
				</a>
			</li>
		</ul>
	</div>

</div>
<?php // @codeCoverageIgnoreEnd ?>
