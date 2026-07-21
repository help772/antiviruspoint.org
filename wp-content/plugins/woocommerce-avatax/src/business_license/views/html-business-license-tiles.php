<?php

/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

  // @codeCoverageIgnoreStart
defined('ABSPATH') or exit;

/**
 * Displays the Business License tiles.
 *
 * @var string $register_url URL for registering for sales tax
 * @var string $status_url URL for viewing registration status
 */
?>

<tr>
	<th colspan="2"  scope="col">
		<div class="avatax-business-license-container">
			<div class="avatax-business-license-tiles">
				
				<!-- View Registration Status Tile -->
				<div class="avatax-business-license-tile">
					<div class="tile-content">
						<h4>
							<span class="dashicons dashicons-list-view"></span>
							<?php echo esc_html__('View registration status', 'woocommerce-avatax'); ?>
						</h4>
						<p>
							<?php echo esc_html__('Check the status of your sales tax registrations.', 'woocommerce-avatax'); ?>
						</p>
						<a href="<?php echo esc_url($status_url); ?>" target="_blank" rel="noopener" class="button button-primary">
							<?php echo esc_html__('View registration status', 'woocommerce-avatax'); ?>
						</a>
					</div>
				</div>

				<!-- Register for Sales Tax Tile -->
				<div class="avatax-business-license-tile">
					<div class="tile-content">
						<h4>
							<span class="dashicons dashicons-bank" ></span>
							<?php echo esc_html__('Register for sales tax', 'woocommerce-avatax'); ?>
						</h4>
						<p>
							<?php echo esc_html__('Initiate your sales tax registrations with Avalara.', 'woocommerce-avatax'); ?>
						</p>
						<a href="<?php echo esc_url($register_url); ?>" target="_blank" rel="noopener" class="button button-primary">
							<?php echo esc_html__('Purchase registrations', 'woocommerce-avatax'); ?>
						</a>
					</div>
				</div>

			</div>
		</div>
	</th>
</tr>

<style>
/* Business License specific styles */
.avatax-business-license-container {
	margin-top: 20px;
}


.avatax-business-license-tiles {
	display: grid;
	grid-template-columns: 1fr 1fr 1fr;
	gap: 20px;
}

.avatax-business-license-tile {
	background: #ffffff;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 24px;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	text-align: left;
	box-shadow: 0 2px 4px rgba(0,0,0,0.05);
	transition: box-shadow 0.2s ease;
}

.avatax-business-license-tile:hover {
	box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.avatax-business-license-tile .tile-content {
    width: 100%;
}

.avatax-business-license-tile .tile-content h4 {
	margin: 0 0 30px 0;
    color: #23282d;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.avatax-business-license-tile .tile-content p {
	margin: 0 0 20px 0;
    color: #666;
    font-size: 12px;
    line-height: 1.5;
    font-weight: bolder;
}

.avatax-business-license-tile .button {
    margin: 0;
    width: 100%;
    font-size: 16px;
    text-align: center;
}

.tile-content h4 span.dashicons {
	font-size: 24px;
    color: #23282d;
    margin-right: 15px;
    margin-top: -5px;
}

/* Responsive design */
@media (max-width: 768px) {
	.avatax-business-license-tiles {
		grid-template-columns: 1fr;
	}
}

@media (max-width: 1024px) and (min-width: 769px) {
	.avatax-business-license-tiles {
		grid-template-columns: 1fr 1fr;
	}
}
</style>

<script>
jQuery(document).ready(function($) {
	// Hide save button specifically on Business License tab
	if (window.location.href.indexOf('section=avatax-business-license') !== -1) {
		$('.woocommerce-save-button, input[name="save"], .submit input[type="submit"], .woocommerce-settings-save').hide();
	}
});
</script>

<?php
// @codeCoverageIgnoreEnd
?>