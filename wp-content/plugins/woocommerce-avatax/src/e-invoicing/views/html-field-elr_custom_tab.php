<?php
// @codeCoverageIgnoreStart

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

defined( 'ABSPATH' ) or exit;

$connected    = 'connected' === get_transient( 'wc_avatax_elr_connection_status', "not-connected" );
/**
 * Displays the address settings fields.
 *
 * @type string $id input ID
 * @type string $label input label
 * @type string $connected connection status
 * @type string $value of field
 * @type string $company_name name of selected company
 * @type string $env name of the selected environment
 * @type string $message message to be displayed 
 * @type string $connected_message message to be displayed when status connected
 */
?>
<tr class="divSync border_bottom">
		<th colspan="2" scope="colgroup">
			<div class="wc-avatax-elr-tools-dropdown" style="display: inline-block; float: right; margin-left: 10px;">
				<button type="button" id="wc_avatax_elr_tools_toggle" class="button-primary actionButton wc-avatax-tools-toggle" aria-haspopup="true" aria-expanded="false" style="display: inline-flex; align-items: center; gap: 4px;">
					<?php esc_html_e( 'Tools', 'woocommerce-avatax' ); ?>
					<span class="dashicons dashicons-arrow-down-alt2" style="font-size: 16px; width: 16px; height: 16px;"></span>
				</button>
			</div>
			<button type="button" id="wc_avatax_elr_disconnect" class="button-primary actionButton" ><?php echo esc_html( __(("Disconnect from " . ($env == 'production' ? 'Production' : 'Sandbox') ), 'woocommerce-avatax') ) ?></button>
			<button type="button" id="wc_avatax_save" class="button-primary actionButton" ><?php echo esc_html( __( 'Save', 'woocommerce-avatax' ) ) ?></button>
		</th>
	</tr>
	<tr id="wc_avatax_elr_tools_section" class="wc-avatax-tools-section border_bottom" style="display: none;">
		<th colspan="2" scope="colgroup">
			<div style="display:flex; align-items:center; justify-content:space-between; gap:20px;">
				<div>
					<strong><?php esc_html_e( 'Buyer feedback manual sync', 'woocommerce-avatax' ); ?></strong>
					<p class="description" style="margin:4px 0 0;">
						<?php esc_html_e( 'Let Avalara check buyer feedback right now instead of the scheduled frequency and update buyer feedback fields.', 'woocommerce-avatax' ); ?><br>
						<?php esc_html_e( 'If you request to check it now, your next check will still be carried out as per your scheduled frequency.', 'woocommerce-avatax' ); ?>
					</p>
				</div>
				<div style="white-space:nowrap; text-align:right;">
					<button type="button" id="wc_avatax_run_application_response_sync" class="button button-secondary">
						<span class="wc-avatax-btn-text"><?php echo esc_html( __( 'Request to check now', 'woocommerce-avatax' ) ) ?></span>
						<span class="wc-avatax-inline-spinner" aria-hidden="true" style="display:none;"></span>
					</button>
				</div>
			</div>
			<span id="wc_avatax_run_application_response_sync_status" style="display:block; margin:8px 0 0 auto; max-width:360px; text-align:right; white-space:normal; overflow-wrap:anywhere; word-break:break-word;"></span>
		</th>
	</tr>
	<style>
		#wc_avatax_run_application_response_sync .wc-avatax-inline-spinner {
			display:inline-block; width:14px; height:14px; margin-left:8px; vertical-align:-3px;
			border:2px solid currentColor; border-right-color:transparent; border-radius:50%;
			animation: wc-avatax-ar-spin 0.6s linear infinite;
		}
		@keyframes wc-avatax-ar-spin { to { transform: rotate(360deg); } }
	</style>
	<?php // Inline test-only handler: triggers the manual cursor-driven sync AJAX and surfaces the result on the page. ?>
	<script>
	(function($){
		$(document).on('click', '#wc_avatax_elr_tools_toggle', function(e){
			e.preventDefault();
			e.stopPropagation();
			var $panel = $('#wc_avatax_elr_tools_section');
			var isOpen = $panel.is(':visible');
			$panel.toggle();
			$(this).attr('aria-expanded', isOpen ? 'false' : 'true');
		});

		// Close the Tools dropdown when clicking outside of it.
		$(document).on('click', function(e){
			if (!$(e.target).closest('.wc-avatax-elr-tools-dropdown, #wc_avatax_elr_tools_section').length) {
				$('#wc_avatax_elr_tools_section').hide();
				$('#wc_avatax_elr_tools_toggle').attr('aria-expanded', 'false');
			}
		});

		$(document).on('click', '#wc_avatax_run_application_response_sync', function(e){
			e.preventDefault();

			var $btn    = $(this);
			var $status = $('#wc_avatax_run_application_response_sync_status');

			if (typeof wc_avatax_admin_elr === 'undefined') {
				$status.css('color', 'red').text('<?php echo esc_js( __( 'AvaTax admin script not loaded.', 'woocommerce-avatax' ) ); ?>');
				return;
			}

			$btn.prop('disabled', true);
			$btn.find('.wc-avatax-inline-spinner').show();
			$status.css('color', '').text('<?php echo esc_js( __( 'Running…', 'woocommerce-avatax' ) ); ?>');

			// The ELR nonce is localized under different keys depending on the screen:
			// `disconnect_nonce` on the ELR settings tab, `nonce` on order/product screens.
			var elrNonce = wc_avatax_admin_elr.disconnect_nonce || wc_avatax_admin_elr.nonce || '';

			if (!elrNonce) {
				$status.css('color', 'red').text('<?php echo esc_js( __( 'ELR nonce missing on this screen; please reload the page.', 'woocommerce-avatax' ) ); ?>');
				$btn.prop('disabled', false);
				return;
			}

			$.post(wc_avatax_admin_elr.ajax_url, {
				action: 'wc_avatax_run_application_response_sync',
				nonce:  elrNonce
			}).done(function(resp){
				if (resp && resp.code === 200) {
					$status.css('color', 'green').html('<?php echo esc_js( __( 'Buyer feedback checked and fields are updated.', 'woocommerce-avatax' ) ); ?>' + '<br>' + '<?php echo esc_js( __( 'Check AvaTax logs for details.', 'woocommerce-avatax' ) ); ?>');
				} else {
					$status.css('color', 'red').text(
						(resp && resp.message) ? resp.message : '<?php echo esc_js( __( 'Sync failed.', 'woocommerce-avatax' ) ); ?>'
					);
				}
			}).fail(function(xhr){
				$status.css('color', 'red').text(
					'<?php echo esc_js( __( 'Request failed:', 'woocommerce-avatax' ) ); ?> ' + xhr.status + ' ' + xhr.statusText
				);
			}).always(function(){
				$btn.prop('disabled', false);
				$btn.find('.wc-avatax-inline-spinner').hide();
			});
		});
	})(jQuery);
	</script>
<tr>
	<th colspan="2" scope="colgroup">
		<div class="elr_container tabs">
			<nav>
				<a><?php echo esc_html( __("Custom fields", 'woocommerce-avatax') ) ?></a>
				<a><?php echo esc_html( __("Data selector", 'woocommerce-avatax') ) ?></a>
				<a class="tablinks ConditionalFieldMapper"><?php echo esc_html( __("Conditional fields", 'woocommerce-avatax') ) ?></a>
				<a><?php echo esc_html( __("Preview data", 'woocommerce-avatax') ) ?></a>
				<a><?php echo esc_html( __("Avalara Network Directory", 'woocommerce-avatax') ) ?></a>
			</nav>
			<div class="content">
				<?php include_once( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-custom-fields.php' ); ?>
			</div>
			<div class="content">
				<?php 
					$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
					include_once( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-field-mapper.php' );
				?>
			</div>
			<div class="content">
				<?php 
				$main_table_list = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList();
				$conditionalRecords = wc_avatax()->wc_avatax_elr_utilities()->getConditionalMapperTableRows();

				include_once( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-conditional-field-mapper.php' ); ?>
			</div>
			<div class="content">
				<?php include_once( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-preview-data.php' ); ?>
			</div>
			<div class="content">
				<?php include_once( wc_avatax()->get_plugin_path() . '/src/ndi/views/html-ndi-search.php' ); ?>
			</div>
		</div>
	</th>
</tr>

<?php
// @codeCoverageIgnoreEnd
?>