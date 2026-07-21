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

/**
 * Display the user certificate fields.
 *
 * @type array $certificateslist usage types for exemption, as `$code => $label`
 */
?>
<?php
// Get the entity parameter from URL
$entity = isset($_GET['entity']) ? sanitize_text_field($_GET['entity']) : '';

// AR-Inbound mode: when the seller selects "AR-Inbound" as
// the document type, the standard WC -> Avalara schema mapping UI is hidden and
// only a four-row checkbox panel is shown so the seller can confirm which AR
// fields to extract from the inbound CDAR XML.
$is_application_response       = ('application_response' === $entity);
$standard_mapper_display_style = $is_application_response ? 'display:none;' : '';
$application_response_display_style = $is_application_response ? '' : 'display:none;';

// Application Response target fields. These are the four AR-specific fields
// the seller can opt in/out of for order-meta persistence; the actual
// UBL→ERP mapping is owned by ELR Studio's JSONata, not this view.
$application_response_fields = array(
    'RequestedActionCode',
    'RequestedAction',
    'StatusReasonCode',
    'StatusReason',
);

// Persisted opt-in flags. Default to all-enabled on a fresh install so the
// seller does not need to re-tick every row to keep behavior.
$saved_application_response_mapping = (array)get_option(
    'wc_avatax_elr_application_response_mapping',
    array_fill_keys($application_response_fields, true)
);
?>
<div id="FieldMapper" >
	<table class="tbl_field_mapper">
		<tbody>
			<tr>
				<td width="75%" colspan="2">
					<div class="divTblType divForm">
						<label><?php echo esc_html__("Document type", 'woocommerce-avatax') ?></label>
						<select name="entity_type" id="entity_type">
							<?php foreach ( wc_avatax()->wc_avatax_elr_utilities()->get_elr_entity_type_options() as $entity_type_value => $entity_type_label ) : ?>
								<option value="<?php echo esc_attr( $entity_type_value ); ?>" <?php selected( $entity, $entity_type_value ); ?>><?php echo esc_html( $entity_type_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</td>
			</tr>
            <tr class="wc-avatax-application-response-row"
                style="<?php echo esc_attr($application_response_display_style); ?>">
                <td colspan="2">
                    <?php
                    ?>
                    <style>
                        .application-response-card .checkbox_lable {
                            display: none !important;
                        }

                        .application-response-card input[type="checkbox"]:focus:not(:focus-visible) {
                            outline: none;
                            box-shadow: none;
                        }

                        #btnSubmitMapper.actionButton:before {
                            float: none;
                            vertical-align: middle;
                            font-size: 14px;
                            line-height: 1;
                            padding: 0;
                            margin-right: 6px;
                            position: static;
                        }
                    </style>
                    <label><?php echo esc_html__('Application response fields', 'woocommerce-avatax'); ?></label>
                    <p class="description" style="margin:0 0 12px;">
                        <?php echo esc_html__('Select the application response fields from the buyers you want to send to Avalara.', 'woocommerce-avatax'); ?>
                    </p>
                    <fieldset class="application-response-fields-grid"
                              style="border:0;padding:0;margin:0;display:flex;flex-wrap:nowrap;gap:12px;align-items:stretch;">
                        <legend class="screen-reader-text">
                            <?php echo esc_html__('Application Response (CDAR) fields available for extraction from inbound XML. Toggle each card to include or exclude that field from the WooCommerce order meta written by the inbound mapper.', 'woocommerce-avatax'); ?>
                        </legend>
                        <?php
                        $ar_active_label = esc_html__('Active', 'woocommerce-avatax');
                        $ar_inactive_label = esc_html__('Inactive', 'woocommerce-avatax');
                        foreach ($application_response_fields as $ar_field_name) :
                            $is_checked = !empty($saved_application_response_mapping[$ar_field_name]);
                            $field_id = 'application_response_field_' . $ar_field_name;
                            $card_state = $is_checked ? 'is-active' : 'is-inactive';
                            $card_style = $is_checked
                                ? 'flex:1 1 0;min-width:0;position:relative;border:1px solid #2271b1;border-radius:6px;padding:14px 16px;background:#f6f8fb;box-shadow:0 0 0 1px #2271b1 inset;'
                                : 'flex:1 1 0;min-width:0;position:relative;border:1px solid #dcdcde;border-radius:6px;padding:14px 16px;background:#ffffff;';
                            $status_style = $is_checked
                                ? 'display:block;margin-top:4px;font-size:12px;color:#2271b1;'
                                : 'display:block;margin-top:4px;font-size:12px;color:#787c82;';
                            $status_label = $is_checked ? '&#10003; ' . $ar_active_label : $ar_inactive_label;
                            ?>
                            <label for="<?php echo esc_attr($field_id); ?>"
                                   class="application-response-card <?php echo esc_attr($card_state); ?>"
                                   style="<?php echo esc_attr($card_style); ?>cursor:pointer;">
                                <?php if ($is_checked) : ?>
                                    <span class="application-response-card__dot"
                                          aria-hidden="true"
                                          style="position:absolute;top:10px;right:12px;width:6px;height:6px;border-radius:50%;background:#2271b1;"></span>
                                <?php endif; ?>
                                <span style="display:flex;align-items:center;gap:10px;">
                                    <input
                                            type="checkbox"
                                            class="application-response-field-checkbox"
                                            data-field="<?php echo esc_attr($ar_field_name); ?>"
                                            id="<?php echo esc_attr($field_id); ?>"
                                            style="margin:0;"
                                        <?php checked($is_checked); ?>
                                    />
                                    <span style="font-weight:600;color:#1d2327;">
                                        <?php echo esc_html($ar_field_name); ?>
                                    </span>
                                </span>
                                <span class="application-response-card__status"
                                      style="<?php echo esc_attr($status_style); ?>">
                                    <?php echo wp_kses_post($status_label); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                </td>
			</tr>
			<tr class="wc-avatax-standard-mapper-row" style="<?php echo esc_attr( $standard_mapper_display_style ); ?>">
				<td width="75%" colspan="2">
					<div class="divTblType divForm">
						<label><?php echo esc_html__("Table format", 'woocommerce-avatax') ?></label>
						<select name="table_type" id="table_type">
						<option value="flat"> <?php echo esc_html__("Flat", 'woocommerce-avatax') ?> </option>
						<option value="eav"> <?php echo esc_html__("EAV", 'woocommerce-avatax') ?> </option>
						<option value="vertical"><?php echo esc_html__("Vertical", 'woocommerce-avatax') ?></option>
						</select>
						<a href="#"><?php echo esc_html__("Learn more about these selections and how to configure them", 'woocommerce-avatax') ?></a>
					</div>
				</td>
			</tr>
			<tr class="wc-avatax-standard-mapper-row" style="<?php echo esc_attr( $standard_mapper_display_style ); ?>">
				<td width="75%" colspan="2">
					<div class="divForm">
						<label style="display:inline-block;"><?php echo esc_html__("Source table", 'woocommerce-avatax') ?>
								<span class="wc-avatax-help-tip" tabindex="0" data-tip="<?php echo esc_html__("One of the tables with the relevant data related to the order. <a href='#'> Learn more about the source table </a>", 'woocommerce-avatax') ?>">i</span>
						</label>
						<input type="text" name="main_table" id="main_table" maxlength="255" placeholder="Search source table" >
					</div>
				
					<div class="divForm vertical-fieldset-na">
						<label><?php echo esc_html__("Source table column", 'woocommerce-avatax') ?></label>
						<select name="main_table_ref_field" id="main_table_ref_field" placeholder="<?php echo esc_html__("Select source table column", 'woocommerce-avatax') ?>" >
							<option value=""><?php echo esc_html__("Select source table column", 'woocommerce-avatax') ?></option>
						</select>
					</div>
					<div class="eav-fieldset vertical-fieldset divForm">
						<label><?php echo esc_html__("Select column data key", 'woocommerce-avatax') ?></label>
						<select name="eav_key_field" id="eav_key_field">
							<option value=""><?php echo esc_html__("Select column data key", 'woocommerce-avatax') ?></option>
						</select>
					</div>
					<div class="eav-fieldset vertical-fieldset divForm">
						<label><?php echo esc_html__("Select column data value", 'woocommerce-avatax') ?></label>
						<select name="eav_value_field" id="eav_value_field">
							<option value=""><?php echo esc_html__("Select column data value", 'woocommerce-avatax') ?></option>
							
						</select>
					</div>
				
					<div class="divForm vertical-fieldset-na">
						<label><?php echo esc_html__("Reference table", 'woocommerce-avatax') ?></label>
						<select name="secondary_table" id="secondary_table" placeholder="<?php echo esc_html__("Select reference table", 'woocommerce-avatax') ?>">
						<option value=""><?php echo esc_html__("Select reference table", 'woocommerce-avatax') ?></option>
					</select>
					</div>
				
					<div class="divForm vertical-fieldset-na">
						<label><?php echo esc_html__("Reference table field", 'woocommerce-avatax') ?></label>
						<select name="secondary_table_ref_field" id="secondary_table_ref_field" placeholder="<?php echo esc_html__("Select reference table field", 'woocommerce-avatax') ?>">
							<option value=""><?php echo esc_html__("Select reference table field", 'woocommerce-avatax') ?></option>
							
						</select>
					</div>
					<div class="divForm cbx">
						<label><?php echo esc_html__("Has multiple records?", 'woocommerce-avatax') ?></label>
						<label style="display: inline-block;"><input type="radio" class="main_table_isarray" name="main_table_isarray" value="on" style="width:auto !important;" > <?php echo esc_html__("Yes", 'woocommerce-avatax') ?> </label>
						<label style="display: inline-block;"><input type="radio" class="main_table_isarray" name="main_table_isarray" value="off" style="width:auto !important;" > <?php echo esc_html__("No", 'woocommerce-avatax') ?>  </label>
					</div>
				</td>
			</tr>
            <tr>
                <td colspan="2">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                        <div style="flex: 1;">
                            <label id="mapper_message"
                                   style="color:red; display:none;"><?php echo esc_html__('', 'woocommerce-avatax') ?></label>
                        </div>
                        <div>
                            <?php
                            $submit_mapper_label = $is_application_response
                                ? __('Save and send to Avalara', 'woocommerce-avatax')
                                : __('Save', 'woocommerce-avatax');
                            ?>
                            <button class="button-primary actionButton" id="btnSubmitMapper"
                                    type="submit"><?php echo esc_html($submit_mapper_label); ?></button>
                        </div>
                    </div>
                </td>
            </tr>
		</tbody>
	</table>
	<!-- <hr style="border-top: 1px solid gray;"> -->
    <table id="tbl_mapper" class="avatax striped wc-avatax-standard-mapper-row"
           style="<?php echo esc_attr($standard_mapper_display_style); ?>">
        <caption class="screen-reader-text">
            <?php echo esc_html__('Saved field mapping rows for the selected document type. Each row shows the source table, source column and any reference / EAV linkage used to populate one outbound schema field.', 'woocommerce-avatax'); ?>
        </caption>
		<thead>
			<th style="display:none;"><?php echo esc_html(__("ID", 'woocommerce-avatax')); ?></th>
			<th><?php echo esc_html__("Table format", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("Source table", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("Source table column", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("Reference table", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("Reference table field", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("EAV key", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("EAV value", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("Has multiple records?", 'woocommerce-avatax') ?></th>
			<th><?php echo esc_html__("Action", 'woocommerce-avatax') ?></th>
		</thead>
		<tbody>
			<?php
			// $records is built internally by getMapperTableRows() and emits a
			// hidden first cell (<td style="display:none;">) holding the mapper id
			// for the JS click handler. wp_kses() permits the `style` attribute
			// here, but its second pass via safecss_filter_attr() runs the
			// `safe_style_css` allowlist, which omits `display` (it's restricted
			// because UGC could use it for clickjacking). We allowlist `display`
			// for the duration of this single echo so the ID column stays hidden,
			// then immediately remove the filter so the relaxation doesn't leak
			// to other wp_kses() callers in this request.
			$wc_avatax_allow_display = static function ( $allowed ) {
				$allowed[] = 'display';
				return $allowed;
			};
			add_filter( 'safe_style_css', $wc_avatax_allow_display );
			echo wp_kses(
				$records,
				array(
					'tr' => array(),
					'td' => array(
						'style' => array(),
					),
					'a'  => array(
						'href'  => array(),
						'class' => array(),
						'id'    => array(),
					),
				)
			);
			remove_filter( 'safe_style_css', $wc_avatax_allow_display );
			?>
		</tbody>
	</table>
	<div class="schemaHeader wc-avatax-standard-mapper-row" style="<?php echo esc_attr( $standard_mapper_display_style ); ?>">
		<input type="search" id="treeNodeSearch" placeholder="Search by data fields">
		<button class="button-primary" id="btnSchemaSelectAll" type="button">
			<span><?php echo esc_html__("Select all", 'woocommerce-avatax') ?></span>
		</button>
		<button class="button-primary" id="btnSchemaUnSelectAll" type="button">
			<span><?php echo esc_html__("Clear all", 'woocommerce-avatax') ?></span>
		</button>
		<button class="button-primary actionButton" id="btnSchemaSave" type="button">
			<span><?php echo esc_html__("Save and send to Avalara", 'woocommerce-avatax') ?></span>
		</button>
	</div>
	<div class="divSchema wc-avatax-standard-mapper-row" style="<?php echo esc_attr( $standard_mapper_display_style ); ?>">
		<ul id="ulSchema"></ul>
	</div>
</div>

<?php
// @codeCoverageIgnoreEnd
?>
