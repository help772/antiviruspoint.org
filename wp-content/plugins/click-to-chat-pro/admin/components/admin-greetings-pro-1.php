<?php
/**
 *  ** template/component **
 * 
 * Greetings dialog PRO - 1 - settings.. form fields..
 * 
 * @subpackage PRO Admin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$safe_fallback_values = [];
if (isset( $fallback_values )) {
    $safe_fallback_values = $fallback_values;
}

$g1_pro_options = get_option( 'ht_ctc_greetings_pro_1', $safe_fallback_values );

$fields = ( isset($g1_pro_options['fields']) ) ? array_map( 'esc_attr', $g1_pro_options['fields'] ) : '';

$is_load_intltelinput = ( isset($g1_pro_options['is_load_intltelinput']) ) ? esc_attr( $g1_pro_options['is_load_intltelinput'] ) : 'n';
$load_type_intltel_files = ( isset($g1_pro_options['load_type_intltel_files']) ) ? esc_attr( $g1_pro_options['load_type_intltel_files'] ) : 'delay_2';
$intl_separate_dialcode_checkbox = ( isset($g1_pro_options['intl_separate_dialcode']) ) ? esc_attr( $g1_pro_options['intl_separate_dialcode'] ) : '';
$intl_language = ( isset($g1_pro_options['intl_language']) ) ? esc_attr( $g1_pro_options['intl_language'] ) : '';
$intl_initial_country = ( isset($g1_pro_options['intl_initial_country']) ) ? esc_attr( $g1_pro_options['intl_initial_country'] ) : 'auto';


$field_count = ( isset($g1_pro_options['field_count']) ) ? esc_attr( $g1_pro_options['field_count'] ) : 1;
$key_gen = 1;
?>

<div class="ht_ctc_pro_form">

    <!-- display fileds -->
    <div class="ctc_display_fields ctc_sortable">

        <?php
    
        if ( is_array($fields) && isset($fields[0]) ) {
            foreach ($fields as $field) {
                $field_options = ( isset($g1_pro_options[$field]) ) ? array_map( 'esc_attr', $g1_pro_options[$field] ) : '';
                $select_field_type = ( isset( $field_options['type']) ) ? $field_options['type'] : '';
                $field_name = ( isset( $field_options['name']) ) ? $field_options['name'] : '';
                $field_placeholder = ( isset( $field_options['placeholder']) ) ? $field_options['placeholder'] : '';
                $required = ( isset( $field_options['required']) ) ? $field_options['required'] : '';
                $add_to_prefilled = ( isset( $field_options['add_to_prefilled']) ) ? $field_options['add_to_prefilled'] : '';
                ?>
                <div class="ht_ctc_pro_field" style="border: 1px solid #dddddd; max-width: 800px; padding:20px; margin-bottom:15px;">

                    <div class="row">
                        <span class="dashicons dashicons-editor-justify handle" style="color:#ddd; cursor:move; margin-right:11px;"></span>
                        <span style="color:#039be5; float:right; cursor:pointer;" class="ht_ctc_pro_form_remove_field_link dashicons dashicons-no-alt" title="Remove field"></span>
                    </div>
                    
                    <div class="row">
                        <!-- field number - ht_ctc_greetings_pro_1[fields][<field_count>] -->
                        <input name="ht_ctc_greetings_pro_1[fields][]" style="display: none;" type="text" class="ht_ctc_pro_form_field_number" value="<?= $field ?>">

                        <!-- select field type -->
                        <div class="input-field col s6">
                            <!-- ht_ctc_greetings_pro_1[<field_count>][type] -->
                            <select name="ht_ctc_greetings_pro_1[<?= $field ?>][type]" class="ht_ctc_pro_form_select_field_type browser-default">
                                <option value="text" <?= $select_field_type == 'text' ? 'SELECTED' : ''; ?> >Text </option>
                                <option value="email" <?= $select_field_type == 'email' ? 'SELECTED' : ''; ?>>Email</option>
                                <option value="textarea" <?= $select_field_type == 'textarea' ? 'SELECTED' : ''; ?>>TextArea (large field)</option>
                                <option value="checkbox" <?= $select_field_type == 'checkbox' ? 'SELECTED' : ''; ?>>Checkbox</option>
                                <option value="select" <?= $select_field_type == 'select' ? 'SELECTED' : ''; ?>>Select</option>
                                <option value="number" <?= $select_field_type == 'number' ? 'SELECTED' : ''; ?>>Number (intl tel input)</option>
                                <option value="hidden" <?= $select_field_type == 'hidden' ? 'SELECTED' : ''; ?>>Hidden</option>
                            </select>
                        </div>
                        
                        <!-- field - name, placeholder, required -->
                        <div class="input-field col s6">
                            <?php
                            $select_textarea_placeholder = "option 1, \noption 2, \noption 3, \n...";

                            // this way.. while saving options for the next time. not saving the select values if the field type is not select. (especially when changing the field type from select to text or email..)
                            $field_selectvalues = '';
                            $selectvalues_display_css = 'display: none;';
                            if ( 'select' == $select_field_type ) {
                                $field_selectvalues = ( isset( $field_options['selectvalues']) ) ? $field_options['selectvalues'] : '';
                                $selectvalues_display_css = '';
                            }
                            // hiddenvalue
                            $hiddenvalue = '';
                            $hiddenvalue_display_css = 'display: none;';
                            if ( 'hidden' == $select_field_type ) {
                                $hiddenvalue = ( isset( $field_options['hiddenvalue']) ) ? $field_options['hiddenvalue'] : '';
                                $hiddenvalue_display_css = '';
                            }
                            ?>

                            <div class="g_form_field_type_select" style="<?= $selectvalues_display_css ?> margin-bottom: 12px;">
                                <label>Select options (required)</label>
                                <textarea name="ht_ctc_greetings_pro_1[<?= $field ?>][selectvalues]" placeholder="<?= $select_textarea_placeholder ?>" class="ht_ctc_pro_form_select_field_selectvalues" style="min-height: 88px;"><?= $field_selectvalues ?></textarea>
                                <p class="description">Add each select option in different line or separate with comma(,).</p>
                            </div>
                            <div class="g_form_field_type_name">
                                <label for=""><?php _e( 'Field Name', 'click-to-chat-for-whatsapp' ); ?></label>
                                <input name="ht_ctc_greetings_pro_1[<?= $field ?>][name]" class="ht_ctc_pro_form_select_field_name" type="text" value="<?= $field_name ?>">
                            </div>
                            <div class="g_form_field_type_placeholder">
                                <label for=""><?php _e( 'Placeholder', 'click-to-chat-for-whatsapp' ); ?></label>
                                <input name="ht_ctc_greetings_pro_1[<?= $field ?>][placeholder]" class="ht_ctc_pro_form_select_field_placeholder" type="text" value="<?= $field_placeholder ?>">
                            </div>
                            <div class="g_form_field_type_hidden" style="<?= $hiddenvalue_display_css ?>">
                                <label for="">Value</label>
                                <input name="ht_ctc_greetings_pro_1[<?= $field ?>][hiddenvalue]" class="ht_ctc_pro_form_select_field_hiddenvalue" type="text" value="<?= $hiddenvalue ?>" placeholder="[gclid]">
                            </div>
                            <div class="g_form_field_type_checkboxes">
                                <div class="g_form_field_type_required">
                                    <label for="ctc_required_checkbox_<?= $key_gen ?>">
                                        <input type="checkbox" name="ht_ctc_greetings_pro_1[<?= $field ?>][required]" <?php checked( $required, 1 ); ?> id="ctc_required_checkbox_<?= $key_gen ?>" value="1">
                                        <span>Required</span>
                                    </label>
                                </div>
                                <div class="g_form_field_type_add_to_prefilled">
                                    <label for="ctc_add_to_prefilled_checkbox_<?= $key_gen ?>">
                                        <input type="checkbox" name="ht_ctc_greetings_pro_1[<?= $field ?>][add_to_prefilled]" <?php checked( $add_to_prefilled, 1 ); ?> id="ctc_add_to_prefilled_checkbox_<?= $key_gen ?>" value="1">
                                        <span>Add to Prefilled message</span>
                                    </label>
                                </div>
                            </div>

                            <!-- display for checkbox field -->
                             <div class="ctc_init_display_none g_form_element g_form_element_checbox">
                                 <p class="description"><a target="_blank" href="https://holithemes.com/plugins/click-to-chat/greetings-form/#checkbox">Add links</a>: e.g. [privacy](https://...)</p>
                            </div>
                            
                            <!-- for number field -->
                            <div class="ctc_init_display_none g_form_element g_form_element_number">
                                <p class="description"><a href="#intltelinput_settings">Number Field (IntltelInput) settings.</a></p>
                                <?php
                                if ( defined( 'HT_CTC_FILES_PLUGIN_FILE' ) ) {
                                    // click to chat files plugin is active
                                    ?>
                                    <p class="description" style="font-size: 0.8em;">plugin loads Intltel library from the plugin: 'Click to Chat Files' (installed on this website)</p>
                                    <?php
                                } else {
                                    // load from github using jsDelivr cdn.  (click to chat files plugin is not active)
                                    ?>
                                    <p class="description" style="font-size: 0.8em;">plugin loads Intltel library hosted at GitHub using jsDelivr cdn</p>
                                    <?php
                                }
                                ?>
                            </div>

                            <!-- for hidden field -->
                            <div class="ctc_init_display_none g_form_element g_form_element_hidden">
                                <details style="margin:7px 0px;">
                                    <summary>Dynamic Variables: cookie: [[]], URL parameters: []</summary>
                                    <p class="description" style="margin:8px 10px 10px 10px;">
                                        <strong>Get value from url parameters:</strong> Text with in single square brackets <code>[]</code> will get value from url parameters. if not exists, return blank
                                        e.g. <code>[gclid]</code>, <code>[utm_source]</code> 
                                        <br>
                                        <strong>Get value from cookies:</strong> Text with in double square brackets <code>[[]]</code> will get value from cookies. if not exists, return blank.
                                        <br> e.g. <code>[[_ga]]</code> 
                                    </p>
                                    <a style="margin:15px 10px;" target="_blank" href="https://holithemes.com/plugins/click-to-chat/greetings-form/#hidden">Hidden Field</a>
                                    </details>
                                <!-- todo: at docs.. add id to make this link works .. and uncomment here. -->
                                <!-- <a target="_blank" href="#hiddenfield">Hidden filed Values</a> -->
                            </div>
                            

                        </div>
                    </div>


                </div>
                <?php
                $key_gen++;
            }
        }

        ?>

        <!-- using js, this will display after color settings .pr_g_p_1_message_box_bg_color  -->
        <details id="intltelinput_settings" class="intltelinput_settings" open style="margin: 12px 0px;">
            <summary>Number (intl tel input) Settings</summary>

            <div style="margin:0 12px;">

                <input type="hidden" name="ht_ctc_greetings_pro_1[is_load_intltelinput]" class="ht_ctc_pro_form_fields ctc_is_load_intltelinput" value="<?= $is_load_intltelinput ?>">

                <p class="description" style="margin: 14px 0;">The number field loads the Intl-tel-input library
                <?php
                if ( defined( 'HT_CTC_FILES_PLUGIN_FILE' ) ) {
                    // click to chat files plugin is active
                    ?>
                    from the plugin: 'Click to Chat Files' (installed on this website)
                    <?php
                } else {
                    // load from github using jsDelivr cdn.  (click to chat files plugin is not active)
                    ?>
                    hosted on <a target="_blank" href="https://github.com/holithemes/click-to-chat-files">GitHub</a> using jsDelivr CDN (a recommended way for easy updates).
                    If you do not wish to load from an external source, 
                    please install and activate the <a href="https://holithemes.com/shop/downloads/click-to-chat-files/" target="_blank">Click to Chat Files</a> plugin.
                    <!-- <a href="https://holithemes.com/plugins/click-to-chat/todo" target="_blank">(more info)</a> -->
                    <?php
                }
                ?>
                </p>
                

                <div class="row">
                    <div class="col s6">
                        <p class="description">When to load the intl tel library</p>
                    </div>
                    <div class="input-field col s6">
                        <select name="ht_ctc_greetings_pro_1[load_type_intltel_files]" class="">
                            <option value="nodelay" <?= $load_type_intltel_files == 'nodelay' ? 'SELECTED' : ''; ?> >No Delay</option>
                            <option value="delay_1" <?= $load_type_intltel_files == 'delay_1' ? 'SELECTED' : ''; ?>>Idle Time</option>
                            <option value="delay_2" <?= $load_type_intltel_files == 'delay_2' ? 'SELECTED' : ''; ?>>After user interaction with the Widget</option>
                        </select>
                        <!-- <p class="description"><a href="todo" target="_blank">more info</a></p> -->
                    </div>
                </div>

                <!-- intl_separate_dialcode -->
                <div class="row">
                    <div class="col s6">
                        <p>Seperate Dial Code</p>
                    </div>
                    <div class="col s6">
                        <label>
                            <input name="ht_ctc_greetings_pro_1[intl_separate_dialcode]" type="checkbox" value="1" <?php checked( $intl_separate_dialcode_checkbox, 1 ); ?> />
                            <span>Seperate Dial Code</span>
                            <p class="description">if checked. displays country dial code, next to the country flag.</p>
                        </label>
                    </div>
                </div>


                <!-- localization -->
                <div class="row">
                    <div class="col s6">
                        <p>Localization</p>
                    </div>
                    <div class="col s6">
                        <!-- add options from greetins.js -->
                        <select name="ht_ctc_greetings_pro_1[intl_language]" class="browser-default select_intl_language" data-selected="<?= $intl_language ?>">
                        </select>
                        <p class="description">Auto : Current page language</p>
                    </div>
                </div>

                <!-- intl_initial_country -->
                <div class="row">
                    <div class="col s6">
                        <p>Initial Country</p>
                    </div>
                    <div class="col s6">
                        <!-- add options from greetins.js -->
                        <select name="ht_ctc_greetings_pro_1[intl_initial_country]" class="browser-default select_intl_initial_country" data-selected="<?= $intl_initial_country ?>">
                        </select>
                        <p class="description">Auto:  Website vistor country (using ipinfo.io)</p>
                    </div>
                </div>

            </div>

        </details>

    </div>

    <!-- new fileds - while adding -->
    <div class="ctc_new_fields">
    </div>

    <!-- Add Field - button -->
    <div class="ctc_add_field_button" style="display:inline-block; margin:10px 0px; cursor:pointer; font-size:16px; border: 1px solid orange; padding: 15px; border-radius:25px;">
        <span style="color: #039be5;" class="dashicons dashicons-plus-alt2" ></span>
        <span style="color: #039be5;">Add Field</span>
    </div>



    <!-- snippets, .... -->
    <div class="ctc_form_snippets" style="display: none;">

        <!-- filed count - field_1 field_2 ... -->
        <input type="text" name="ht_ctc_greetings_pro_1[field_count]" class="ht_ctc_pro_form_field_count" value="<?= $field_count ?>">

        <!-- snippet: add field -->
        <div class="ht_ctc_pro_field">

            <div class="row">
                <span style="color:#039be5; float:right; cursor:pointer;" class="ht_ctc_pro_form_remove_field_link dashicons dashicons-no-alt" title="Remove Page"></span>
            </div>
            
            <div class="row">
                
                <!-- name: ht_ctc_greetings_pro_1[fields][<field_count>] -->
                <input style="display: none;" type="text" class="ht_ctc_pro_form_field_number" value="<?= $field_count ?>">

                <div class="input-field col s6">
                     <select class="ht_ctc_pro_form_select_field_type browser-default">
                        <option value="text">Text </option>
                        <option value="email">Email</option>
                        <option value="textarea">TextArea (large field)</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="select">Select</option>
                        <option value="number">Number (intl tel input)</option>
                        <option value="hidden">Hidden</option>
                    </select>
                </div>
                <div class="input-field col s6">
                    <div class="g_form_field_type_select" style="margin-bottom: 12px; display:none;">
                        <label>Select options (required)</label>
                        <textarea class="ht_ctc_pro_form_select_field_selectvalues" placeholder="option 1, option 2, option 3, ..." style="min-height: 88px;"></textarea>
                        <p class="description">Add each select option in different line or separate with comma(,).</p>
                    </div>
                    <div class="g_form_field_type_name">
                        <label for=""><?php _e( 'Field Name', 'click-to-chat-for-whatsapp' ); ?></label>
                        <input class="ht_ctc_pro_form_select_field_name" type="text" placeholder="Name:, Email:, .... ">
                    </div>
                    <div class="g_form_field_type_placeholder">
                        <label for=""><?php _e( 'Placeholder', 'click-to-chat-for-whatsapp' ); ?></label>
                        <input class="ht_ctc_pro_form_select_field_placeholder" type="text" placeholder="Name, Email, .... ">
                    </div>
                    <div class="g_form_field_type_hidden" style="<?= $hiddenvalue_display_css ?>">
                        <label for="">Value</label>
                        <input class="ht_ctc_pro_form_select_field_hiddenvalue" type="text" placeholder="[gclid]">
                    </div>
                    <div class="g_form_field_type_checkboxes">
                        <div class="g_form_field_type_required">
                            <label>
                                <input class="ht_ctc_pro_form_select_field_required" type="checkbox" value="1">
                                <span>Required</span>
                            </label>
                        </div>
                        <div class="g_form_field_type_add_to_prefilled">
                            <label>
                                <input class="ht_ctc_pro_form_select_field_add_to_prefilled" type="checkbox" checked value="1">
                                <span>Add to Prefilled message</span>
                            </label>
                        </div>
                    </div>

                    <!-- display for checkbox field -->
                    <div class="ctc_init_display_none g_form_element g_form_element_checbox">
                        <p class="description"><a target="_blank" href="https://holithemes.com/plugins/click-to-chat/greetings-form/#checkbox">Add links</a>: e.g. [privacy](https://...)</p>
                    </div>
                
                    <!-- for number field -->
                    <div class="ctc_init_display_none g_form_element g_form_element_number">
                        <p class="description"><a href="#intltelinput_settings">Number Field (IntltelInput) settings.</a></p>
                        <?php
                        if ( defined( 'HT_CTC_FILES_PLUGIN_FILE' ) ) {
                            // click to chat files plugin is active
                            ?>
                            <p class="description" style="font-size: 0.8em;">plugin loads Intltel library from the plugin: 'Click to Chat Files' (installed on this website)</p>
                            <?php
                        } else {
                            // load from github using jsDelivr cdn.  (click to chat files plugin is not active)
                            ?>
                            <p class="description" style="font-size: 0.8em;">plugin loads Intltel library hosted at GitHub using jsDelivr cdn</p>
                            <?php
                        }
                        ?>
                    </div>

                    <!-- for hidden field -->
                    <div class="ctc_init_display_none g_form_element g_form_element_hidden">
                        
                        <details style="margin:7px 0px;">
                            <summary>Dynamic Variables: cookie: [[]], URL parameters: []</summary>
                            <p class="description" style="margin:8px 10px 10px 10px;">
                                <strong>Get value from url parameters:</strong> Text with in single square brackets <code>[]</code> will get value from url parameters. if not exists, return blank
                                e.g. <code>[gclid]</code>, <code>[utm_source]</code> 
                                <br>
                                <strong>Get value from cookies:</strong> Text with in double square brackets <code>[[]]</code> will get value from cookies. if not exists, return blank.
                                <br> e.g. <code>[[_ga]]</code> 
                            </p>
                            <a style="margin:15px 10px;" target="_blank" href="https://holithemes.com/plugins/click-to-chat/greetings-form/#hidden">Hidden Field</a>
                        </details>
                        <!-- todo: at docs.. add id to make this link works .. and uncomment here. -->
                        <!-- <a target="_blank" href="#hiddenfield">Hidden filed Values</a> -->
                    </div>

                    <br>
                </div>
                
            </div>
        </div>


    </div>
    <!-- #END snippets -->

</div>

<br><br>