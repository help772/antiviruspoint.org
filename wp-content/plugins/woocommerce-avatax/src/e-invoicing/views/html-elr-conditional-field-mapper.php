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
    // Code to get payload condition from transient
    $conditional_payload = get_transient('wc_avatax_elr_condition_payload');
?>

<div id="ConditionalFieldMapper" class="tabcontent" >
    <table class="tbl_conditional_field">
        <tbody>
            <tr >
                <td width="75%" colspan="2">
                    <div class="divForm">
                        <label>
                            <?php echo esc_html(__("Conditional parameters", 'woocommerce-avatax')); ?>
                            <span class="wc-avatax-help-tip" tabindex="0" data-tip="<?php
                                echo esc_attr(__(
                                    "The conditional fields selected here will be used to create ".
                                    "the execution rule on the Avalara e-Invoicing portal.".
                                    "The same execution rules are used to select the mandates.",
                                    'woocommerce-avatax'
                                ));
                            ?>">i</span>
                        </label>
                        <select name="cond_params" id="cond_params">
                            <?php
                                //foreach loop to set options for dropdown from $conditional_payload
                                foreach ($conditional_payload as $key => $value) {
                                    echo '<option value="' . esc_attr($key) . '">' .
                                         esc_html($key) .
                                         '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="divForm">
                        <label><?php echo esc_html(__("Select source table", 'woocommerce-avatax')); ?></label>
                        <select name="mapped_table" id="mapped_table">
                            <option value=''>
                                <?php echo esc_html(__("Select source table", 'woocommerce-avatax')); ?>
                            </option>
                        </select>
   					</div>
                    <div class="divForm">
                        <label><?php echo esc_html(__("Select data field", 'woocommerce-avatax')); ?></label>
                        <select name="mapper_table_field" id="mapper_table_field">
                            <option value="">
                                <?php echo esc_html(__("Select data field", 'woocommerce-avatax')); ?>
                            </option>
                        </select>
   					</div>

                    </td>
            </tr>
            <tr>
                <td width="75%" colspan="2" style="font-weight: 400;">
                    <?php
                        echo wp_kses_post(__(
                            "<strong>Note:</strong>" .
                            " The conditional fields selected here will be used to".
                            " create the execution rule on Avalara E-Invoicing.".
                            " The same execution rules are used to select the mandates.",
                            'woocommerce-avatax'
                        ));
                    ?>
                </td>
            </tr>
            <tr >
            <td width="75%" colspan="2">
                <div class="filter-fields divForm">
                    <label style="margin-right: 0px;">
                        <?php
                            echo esc_html(__(
                                "Set filter conditions in case of multiple records",
                                'woocommerce-avatax'
                            ));
                        ?>
                        <span class="wc-avatax-help-tip" tabindex="0" style="margin-left: 2px;" data-tip="<?php
                            echo esc_attr(__(
                                "These fields are visible as you have selected".
                                " the �Has multiple records� option under the Data selector tab.",
                                'woocommerce-avatax'
                            ));
                        ?>">i</span>
                    </label>
                    <label><?php echo esc_html(__("Filter by source table field", 'woocommerce-avatax')); ?></label>
                    <select name="mapper_table_filter_field" class="filter-fields-field" id="mapper_table_filter_field">
                        <option value="">
                            <?php echo esc_html(__("Select source table fields", 'woocommerce-avatax')); ?>
                        </option>
                    </select>
                </div>

                <div class="filter-fields  divForm">
                    <label>&nbsp;</label>
                    <label><?php echo esc_html(__("Enter filter value", 'woocommerce-avatax')); ?></label>
                    <input type="text" name="mapper_table_filter_data" class="filter-fields-data" id="mapper_table_filter_data" maxlength="255">
                </div>

                <div class="filter-fields divForm">
                <label>&nbsp;</label>
                <label>&nbsp;</label>
                    <span class="icon-plus rowfy-addrow" id="add_filter" style="background-color: blue;color: white;padding:3px 9px">+</span>
                </div>
            </td>
            </tr>
            <tr>
                <td width="75%"><label id="saveConditionalInfo" style="color:red"></label></td>
                <td>
                    <button class="button-primary actionButton" id="btnSubmitConditional" style="float:right;">
                        <?php echo esc_html(__("Save", 'woocommerce-avatax')); ?>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
   
    <table id="tbl_conditional_mapper" class="avatax striped">
        <thead>
            <th style="display:none;"><?php echo esc_html(__("ID", 'woocommerce-avatax')); ?></th>
            <th style="display:none;"><?php echo esc_html(__("Filter ID", 'woocommerce-avatax')); ?></th>
            <th><?php echo esc_html(__("Conditional parameters", 'woocommerce-avatax')); ?></th>
            <th><?php echo esc_html(__("Source table", 'woocommerce-avatax')); ?></th>
            <th><?php echo esc_html(__("Source table field", 'woocommerce-avatax')); ?></th>
            <th><?php echo esc_html(__("Source filter field", 'woocommerce-avatax')); ?></th>
            <th><?php echo esc_html(__("Source filter value", 'woocommerce-avatax')); ?></th>
            <th><?php echo esc_html(__("Action", 'woocommerce-avatax')); ?></th>
        </thead>
        <tbody>
			<?php
			// $conditionalRecords is built internally by getConditionalMapperTableRows()
			// and emits two leading hidden cells (<td style="display:none;">) holding
			// conditional_mapper_id and filter_id for the JS click handler.
			// wp_kses_post() shares the same safecss_filter_attr() allowlist as
			// wp_kses(), and that allowlist omits `display` (it's restricted because
			// UGC could use it for clickjacking). We allowlist `display` for the
			// duration of this single echo so the ID columns stay hidden, then
			// remove the filter so the relaxation doesn't leak to other wp_kses()
			// callers later in this request. Mirrors the pattern used in
			// html-elr-field-mapper.php.
			$wc_avatax_allow_display = static function ( $allowed ) {
				$allowed[] = 'display';
				return $allowed;
			};
			add_filter( 'safe_style_css', $wc_avatax_allow_display );
			echo wp_kses_post( $conditionalRecords );
			remove_filter( 'safe_style_css', $wc_avatax_allow_display );
			?>
		</tbody>
	</table>
</div>
<?php
// @codeCoverageIgnoreEnd
?>