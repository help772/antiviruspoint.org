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
 * @codeCoverageIgnore
 */

// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) or exit;

/**
 * Display the product variation tax code field.
 *
 * @type int $loop variation loop number
 * @type string $tax_code stored tax code
 * @type string $default default placeholder tax code
 */
?>
<style>
	.search_result {
		background-color: #fff;
		border: 1px solid #8c8f94;
		border-radius: 5px;
		box-shadow: 0 7px 29px 0 hsla(240, 5%, 41%, .2);
		max-height: 250px;
		position: absolute;
		width: 40%;
		z-index: 10;
	}

	.search_result ul {
		margin-top: 5px;
		max-height: 240px;
		overflow: auto
	}

	.search_result ul li {
		display: block;
		padding: 5px 7px
	}

	.search_result ul li:hover {
		background-color:var(--wc-avatax-color);
		color: #fff;
		cursor: pointer
	}
</style>
<script>
// Only initialize once to avoid duplicate event handlers
if (typeof wc_avatax_variation_initialized === 'undefined') {
	wc_avatax_variation_initialized = true;
	
	jQuery(document).ready(function($) {
		// Handle tax code lookup for variation fields - single event handler for all variations
		$(document).on('keyup', 'input[name*="variable_wc_avatax_code"]', function(e) {
			var $this = $(this);
			var fieldName = $this.attr('name');
			
			// Determine which lookup div to use
			if (fieldName && fieldName.indexOf('variable_wc_avatax_code') !== -1) {
				// For variation fields, use the specific lookup div
				var loopNumber = fieldName.match(/\[(\d+)\]/);
				if (loopNumber && loopNumber[1]) {
					var lookupDivId = '#divPCodeLookup_' + loopNumber[1];
					var $lookupDiv = $(lookupDivId);
				} else {
					var $lookupDiv = $('#divPCodeLookup');
				}
			} else {
				// For regular fields, use the main lookup div
				var $lookupDiv = $('#divPCodeLookup');
			}
			
			if ($this.val().length > 2) {
				var data = {
					nonce: wc_avatax_admin.sync_nonce,
					action: 'wc_avatax_tax_code_lookp',
					type: 'P',
					key: $this.val()
				};
				
				jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
					if (response.success && response.data && response.data.records) {
						$lookupDiv.html(response.data.records);
						
						// Handle lookup item clicks - use data attribute to track which field
						$lookupDiv.find('.lookupItem').off('click').on('click', function(e) {
							var selectedValue = $(this).attr('data-val');
							$this.val(selectedValue); // Update the specific field that triggered the lookup
							$lookupDiv.hide();
						});
						
						$lookupDiv.show();
					} else {
						$lookupDiv.hide();
					}
				});
			} else {
				$lookupDiv.hide();
			}
		});

		// Handle clicks outside lookup containers - check each field's specific lookup div
		$(document).on('mouseup', function(e) {
			// Handle main lookup containers
			var container = $('#divPCodeLookup');
			if (!container.is(e.target) && container.has(e.target).length === 0) {
				var field = $('#wc_avatax_default_product_code');
				if (!field.is(e.target) && field.has(e.target).length === 0) {
					container.hide();
				}
			}

			// Handle variation-specific lookup containers
			$('[id^="divPCodeLookup_"]').each(function() {
				var $container = $(this);
				var containerId = $container.attr('id');
				var loopNumber = containerId.match(/divPCodeLookup_(\d+)/);
				if (loopNumber && loopNumber[1]) {
					var $field = $('input[name="variable_wc_avatax_code[' + loopNumber[1] + ']"]');
					
					if (!$container.is(e.target) && $container.has(e.target).length === 0) {
						if (!$field.is(e.target) && $field.has(e.target).length === 0) {
							$container.hide();
						}
					}
				}
			});

			// Handle shipping code lookup container
			var container2 = $('#divSCodeLookup');
			if (!container2.is(e.target) && container2.has(e.target).length === 0) {
				var field = $('#wc_avatax_shipping_code');
				if (!field.is(e.target) && field.has(e.target).length === 0) {
					container2.hide();
				}
			}
		});
	});
}
</script>

<div>
	<p class="form-row form-row-first">
		<label><?php esc_html_e( 'Tax Code', 'woocommerce-avatax' ); ?></label>
		<input
			type="text"
			name="variable_wc_avatax_code[<?php echo esc_attr($loop); ?>]"
			class="text wc_avatax_code" value="<?php echo esc_attr($tax_code); ?>"
			placeholder="<?php echo esc_attr($default); ?>"
			data-loop="<?php echo esc_attr($loop); ?>"
		/>
	</p>
	<div class="search_result" id="divPCodeLookup_<?php echo esc_attr($loop); ?>" style="display:none;margin-top: 78px;">
		<ul>
			<li>Please wait..</li>
		</ul>
	</div>
</div>
<?php
// @codeCoverageIgnoreEnd
?>