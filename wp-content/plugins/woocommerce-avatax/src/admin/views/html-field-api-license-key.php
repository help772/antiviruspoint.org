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
defined('ABSPATH') or exit;
$ccs_error = 'yes' === get_transient('wc_avatax_ccs_error', 'no');
if ($ccs_error) {
	$connected    = false;
} else {
	$connected    = 'connected' === get_transient('wc_avatax_connection_status', "not-connected");
}
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

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="wc_avatax_api_license_key">License Key </label>
	</th>
	<td class="forminp forminp-text">
		<input name="wc_avatax_api_license_key" id="wc_avatax_api_license_key" type="password" style="min-width:300px;" value="<?php echo esc_attr($value) ?>" class="wc-avatax-connection-field" placeholder="" data-wc-avatax-connection-status="<?php echo esc_attr($connected ? 'connected' : 'not-connected') ?>">
		<p class="description">Can't find the license key?
			<a href="https://knowledge.avalara.com/bundle/dqa1657870670369_dqa1657870670369/page/Get_your_license_key.html" target="_blank" rel="noopener noreferrer">
				Learn how you can get a new license key
			</a>
		</p>
	</td>
</tr>
<?php
if (!$connected) {
?>
	<tr class="divSync">
		<th scope="col" colspan="2">
			<button id="wc_avatax_connect_production" class="button-primary avatax_connect" data-environment="production">Connect to Production</button>
			<button id="wc_avatax_connect_sandbox" class="button-secondary avatax_connect" data-environment="development">Connect to Sandbox</button>
			<br /><br />
			<p class="description">Not sure which environment to select?
				<a href="https://knowledge.avalara.com/bundle/dqa1657870670369_dqa1657870670369/page/AvaTax_sandbox_accounts.html" target="_blank" rel="noopener noreferrer">
					Learn more about Sandbox and Production accounts
				</a>
			</p>
            <p><?php echo wp_kses_post($message) ?></p>
		</th>
	</tr>

<?php
} else {
?>
	<tr class="divSync">
		<th scope="col" colspan="2">
			<button id="wc_avatax_disconnect" readonly class="button-primary avatax_connect">Disconnect from <?php echo $env == 'production' ? 'Production' : 'Sandbox' ?></button>
			<button id="wc_avatax_refresh_config" class="button-secondary avatax_refresh_config">Sync settings</button>
			<button id="wc_avatax_update_connection" disabled class="button-secondary avatax_connect">Update connection</button>
			<p class="description" style="margin: 0px; padding-top: 15px;">Not sure which environment to select?
				<a href="https://knowledge.avalara.com/bundle/dqa1657870670369_dqa1657870670369/page/AvaTax_sandbox_accounts.html" target="_blank" rel="noopener noreferrer">
					Learn more about Sandbox and Production accounts
				</a>
			</p>
		</th>
	</tr>
	<tr>
		<th scope="col" colspan="2">
			<div class="avatax-notice " style="">
                <?php echo wp_kses_post($connected_message) ?>
			</div>
		</th>
	</tr>
	<tr>
		<th scope="col"  colspan="2" style="width: 100% !important; padding-bottom: 8px !important;">
			<div class="avatax-notice warning" style="">
			<span class="warning-icon dashicons"></span>
				<p style="display: inline-block;vertical-align: middle;">To fine-tune tax calculation, go to
					<b><a href="<?php echo esc_url($edit_config_link) ?>" target="_blank" rel="noopener noreferrer">
						Advanced settings
					</a></b>
					in Avalara.
				</p>
				
			</div>
		</th>
	</tr>
	<tr>
		<th scope="col" colspan="2" style="width: 100% !important; padding-bottom: 8px !important;">
			<button type="button" id="wc_avatax_tools_toggle" class="button button-secondary wc-avatax-tools-toggle" style="float: right; margin-left: 10px;">
				Tools
			</button>
		</th>
	</tr>
    <tr id="wc_avatax_tools_section" class="wc-avatax-tools-section" style="display: none;">
        <th colspan="2" scope="colgroup">
            <div class="wc-avatax-tools-container">
                <table class="widefat">
                    <caption class="screen-reader-text"><?php esc_html_e('AvaTax Tools', 'woocommerce-avatax'); ?></caption>
                    <tbody>
                    <tr>
                        <td>
                            <strong><?php esc_html_e('Nexus', 'woocommerce-avatax'); ?></strong>
                            <p class="description"><?php esc_html_e('Clear the list of nexus jurisdictions where your company has tax obligations from Avalara.', 'woocommerce-avatax'); ?></p>
                        </td>
                        <td>
                            <button type="button" id="wc_avatax_clear_nexuslist"
                                    class="button button-secondary avatax_tool" data-tool="Refresh Nexus List">
                                <?php esc_html_e('Clear Nexus', 'woocommerce-avatax'); ?>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php esc_html_e('Entity Code', 'woocommerce-avatax'); ?></strong>
                            <p class="description"><?php esc_html_e('Clear entity use code of your Avalara account. Visit the User profile page to refresh entity use code.', 'woocommerce-avatax'); ?></p>
                        </td>
                        <td>
                            <button type="button" id="wc_avatax_clear_entitycodes"
                                    class="button button-secondary avatax_tool" data-tool="Refresh Tax Codes">
                                <?php esc_html_e('Clear Entity Code', 'woocommerce-avatax'); ?>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php esc_html_e('Clear Cache', 'woocommerce-avatax'); ?></strong>
                            <p class="description"><?php esc_html_e('Remove all cached AvaTax data to ensure fresh retrieval from Avalara.', 'woocommerce-avatax'); ?></p>
                        </td>
                        <td>
                            <button type="button" id="wc_avatax_cache_clean" class="button button-secondary avatax_tool"
                                    data-tool="clear_cache">
                                <?php esc_html_e('Clear Cache', 'woocommerce-avatax'); ?>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php esc_html_e('Update Address', 'woocommerce-avatax'); ?></strong>
                            <p class="description"><?php esc_html_e('If you’ve updated your primary business address in Avalara, update it in WooCommerce.', 'woocommerce-avatax'); ?></p>
                        </td>
                        <td>
                            <button type="button" id="wc_avatax_update_origin_address" class="button button-secondary avatax_tool"
                                    data-tool="update_origin_address">
                                <?php esc_html_e('Update Address', 'woocommerce-avatax'); ?>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </th>
    </tr>
<?php
}
?>
<?php
// @codeCoverageIgnoreEnd
?>