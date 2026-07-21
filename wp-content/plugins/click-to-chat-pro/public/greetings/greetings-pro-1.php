<?php
/**
 * PRO Greetings - template - 1  - Form
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$g1_pro_options = get_option( 'ht_ctc_greetings_pro_1' );
$greetings = get_option('ht_ctc_greetings_options');

$os = get_option('ht_ctc_othersettings');


$is_number_field = 'n';
$is_select_placeholder = 'n';

$ht_ctc_greetings['main_content'] = do_shortcode( $ht_ctc_greetings['main_content'] );

$header_css = 'padding: 12px 19px 12px 19px;';
$main_css = 'padding: 18px 19px 5px 19px;';
$message_box_css = '';
// sticky call to action -  position:sticky; bottom:-9px;
$send_css = 'text-align:center; padding: 11px 19px 9px 19px;';
$bottom_css = 'padding: 2px 19px 2px 19px; text-align:center; font-size:12px; background-color:#ffffff;';

// css
$header_bg_color = ( isset($g1_pro_options['header_bg_color']) ) ? esc_attr( $g1_pro_options['header_bg_color'] ) : '';
if ( '' == $header_bg_color ) {
    $header_bg_color = '#ffffff';
}
$main_bg_color = ( isset($g1_pro_options['main_bg_color']) ) ? esc_attr( $g1_pro_options['main_bg_color'] ) : '';
if ( '' == $main_bg_color ) {
    $main_bg_color = '#ffffff';
}
$message_box_bg_color = ( isset($g1_pro_options['message_box_bg_color']) ) ? esc_attr( $g1_pro_options['message_box_bg_color'] ) : '';

$header_css .= "background-color:$header_bg_color;";

if ('' !== $main_bg_color) {
    $main_css .= "background-color:$main_bg_color;";
}

if ('' !== $message_box_bg_color) {
    $message_box_css .= "padding:6px 8px;border-radius:5px;margin-bottom:5px;background-color:$message_box_bg_color;";
} else {
    $message_box_css .= 'margin-bottom:5px;';
}

$rtl_page = "";
if ( function_exists('is_rtl') && is_rtl() ) {
    $rtl_page = "yes";
}

$g_header_image_css = "border-radius:50%;height:50px; width:50px;";
if ('yes' == $rtl_page) {
    $g_header_image_css .= "margin-left:9px;";
} else {
    $g_header_image_css .= "margin-right:9px;";
}

$cta_style = ( isset($g1_pro_options['cta_style']) ) ? esc_attr( $g1_pro_options['cta_style'] ) : '7_1';

$g_cta_path = plugin_dir_path( HT_CTC_PLUGIN_FILE ) . 'new/inc/greetings/greetings_styles/g-cta-' . $cta_style. '.php';
// $g_cta_path = plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) . 'public/greetings/greetings_styles/g-cta-' . $cta_style. '.php';

$fields = ( isset($g1_pro_options['fields']) ) ? array_map( 'esc_attr', $g1_pro_options['fields'] ) : '';
$key_gen = 1;

// fileds css.. 
$fileds_pr_css = "margin-bottom:8px; display:flex; flex-wrap: wrap; flex-direction: column;";
$filed_label_css = "margin:0px; padding:0px;";
$filed_input_css = "border-radius:4px; background-color: #ffffff; width:100%; padding: 9px;";

// removed this.. if main plugin 3.28 or later added in main.css
// $filed_input_css .= "box-sizing:border-box;";

    // filed checkbox..
$filed_checbox_pr_css = "margin-bottom: 4px; display:flex; flex-direction:row; align-items:center;";
$filed_checbox_input_css = "margin:0;";
$filed_checbox_label_css = "margin: 0px; padding: 0 4px;";

$filed_hidden_pr_css = "display:none;";

// intl number field css..
$filed_input_css_intl = "border-radius:4px; background-color: #ffffff; width:100%; box-sizing:border-box;";

$filed_input_css_intl .= "padding: 9px;";
// $filed_input_css_intl .= "padding-top: 9px; padding-bottom: 9px;";


$g_header_image = ( isset($greetings['g_header_image']) ) ? esc_attr( $greetings['g_header_image'] ) : '';

if ('' !== $g_header_image) {
    $header_css .= "line-height:1.1;";
} else {
    $header_css .= "line-height:1.3;";
}

?>
<div>
<?php
if ( '' !== $ht_ctc_greetings['header_content'] ) {
    if (!empty($g_header_image)) {
        // if header image is added
        ?>
        <div class="ctc_g_heading" style="<?= $header_css ?>">
            <div style="display: flex; align-items: center;">
                <div class="greetings_header_image" style="<?= $g_header_image_css ?>">
                    <?php
                    try {
                        $filename_without_extension = pathinfo($g_header_image, PATHINFO_FILENAME);
                    } catch (Exception $e) {
                        $filename_without_extension = 'header-image'; // Fallback value
                    }
                    ?>
                    <img style="display:inline-block; border-radius:50%; height:50px; width:50px;" src="<?= $g_header_image ?>" alt="<?= $filename_without_extension ?>">
                    <?php
                    if ( isset($greetings['g_header_online_status']) ) {
                        $g_header_online_status_color = ( isset($greetings['g_header_online_status_color']) ) ? esc_attr( $greetings['g_header_online_status_color'] ) : '';
                        if ('' == $g_header_online_status_color) {
                            $g_header_online_status_color = '#06e376';
                        }
                        ?>
                        <span class="for_greetings_header_image_badge" style="display:none; border: 2px solid <?= $header_bg_color ?>; background-color: <?= $g_header_online_status_color ?>;"></span>
                        <?php
                    }
                    ?>
                </div>
                <div class="ctc_g_header_content">
                    <?= wpautop($ht_ctc_greetings['header_content']) ?>
                </div>
            </div>
        </div>
        <?php
    } else {
        // if header image is not added
        ?>
        <div class="ctc_g_heading ctc_g_header_content" style="<?= $header_css ?>">
            <?= wpautop($ht_ctc_greetings['header_content']) ?>
        </div>
        <?php
    }
}
?>
    <div class="ctc_g_content" style="<?= $main_css ?>">
        <?php 
        if ( '' !== $ht_ctc_greetings['main_content'] ) { 
        ?>
        <div class="ctc_g_message_box" style="<?= $message_box_css ?>"><?= wpautop( $ht_ctc_greetings['main_content'] ) ?></div>
        <?php 
        }
        ?>
        <div class="ctc_g_form">
            <form id="ctc_pro_greetings_form" class="ctc_pro_greetings_form" name="ctc_pro_greetings_form">
                <div class="ctc_g_form_keys" style="display: none;">
                    <?php wp_nonce_field('ht_ctc_pro_greetings_nonce', 'ht_ctc_pro_greetings_nonce'); ?>
                </div>
                <?php
                if ( is_array($fields) && isset($fields[0]) ) {
                    foreach ($fields as $field) {
                        $name = "field_$key_gen";
                        $id_gen = "ht_ctc_g_form_$key_gen";
                        $field_options = ( isset($g1_pro_options[$field]) ) ? array_map( 'esc_attr', $g1_pro_options[$field] ) : '';
                        $type = ( isset( $field_options['type']) ) ? $field_options['type'] : '';
                        $required = ( isset( $field_options['required']) ) ? ' required' : '';
                        $add_to_prefilled = ( isset( $field_options['add_to_prefilled']) ) ? ' ctc_g_field_add_to_prefilled' : '';

                        $hiddenvalue = ( isset( $field_options['hiddenvalue']) ) ? $field_options['hiddenvalue'] : '';

                        $field_name = ( isset( $field_options['name']) ) ? $field_options['name'] : '';
                        $field_name = apply_filters( 'wpml_translate_single_string', $field_name, 'Click to Chat for WhatsApp', "greetings_form_$field" . "_name" );

                        $placeholder = ( isset( $field_options['placeholder']) ) ? $field_options['placeholder'] : '';
                        $placeholder = apply_filters( 'wpml_translate_single_string', $placeholder, 'Click to Chat for WhatsApp', "greetings_form_$field" . "_placeholder" );

                        if ('text' == $type) {
                            ?>
                            <div class="ctc_form_field <?= $id_gen ?>" style="<?= $fileds_pr_css ?>">
                                <label style="<?= $filed_label_css ?>" for="<?= $id_gen ?>"><?= $field_name ?></label>
                                <input type="<?= $type ?>" style="<?= $filed_input_css ?>" class="ht_ctc_g_form_field <?= $add_to_prefilled ?>" id="<?= $id_gen ?>" name="<?= $name ?>" <?= $required ?> data-name="<?= $field_name ?>" style="width:100%;" placeholder="<?= $placeholder ?>">
                            </div>
                            <?php
                        } else if ('textarea' == $type) {
                            ?>
                            <div class="ctc_form_field <?= $id_gen ?>" style="<?= $fileds_pr_css ?>">
                                <label style="<?= $filed_label_css ?>" for="<?= $id_gen ?>"><?= $field_name ?></label>
                                <textarea name="<?= $name ?>" style="<?= $filed_input_css ?>" class="ht_ctc_g_form_field <?= $add_to_prefilled ?>" id="<?= $id_gen ?>" <?= $required ?> data-name="<?= $field_name ?>" placeholder="<?= $placeholder ?>" style="height: 69px; width:100%;"></textarea>
                            </div>
                            <?php
                        } else if ('checkbox' == $type) {
                            $checkbox_label_field_name = preg_replace('/\[(.*?)\]\s*\(((?:http:\/\/|https:\/\/)(?:.+))\)/', '<a target="_blank" href="$2">$1</a>', $field_name);
                            $checkbox_data_name_field_name = preg_replace('/\[(.*?)\]\s*\(((?:http:\/\/|https:\/\/)(?:.+))\)/', '$1', $field_name);
                            ?>
                            <div class="ctc_form_field <?= $id_gen ?>" style="<?= $filed_checbox_pr_css ?>">
                                <input type="hidden" class="ht_ctc_g_form_field ctc_g_hidden_for_checkbox" value="-" data-name="<?= $checkbox_data_name_field_name ?>" name="<?= $name ?>">
                                <input type="<?= $type ?>" style="<?= $filed_checbox_input_css ?>" class="ht_ctc_g_form_field ctc_g_its_checkbox <?= $add_to_prefilled ?>" id="<?= $id_gen ?>" <?= $required ?> data-name="<?= $checkbox_data_name_field_name ?>" name="<?= $name ?>">
                                <label style="<?= $filed_checbox_label_css ?>" for="<?= $id_gen ?>"><?= $checkbox_label_field_name ?></label>
                            </div>
                            <?php
                        } else if ('hidden' == $type) {
                            ?>
                            <div class="ctc_form_field <?= $id_gen ?>" style="<?= $filed_hidden_pr_css ?>">
                                <input type="hidden" class="ht_ctc_g_form_field_hidden ht_ctc_g_form_field <?= $add_to_prefilled ?>" id="<?= $id_gen ?>" name="<?= $name ?>" data-name="<?= $field_name ?>" data-orginal="<?= $hiddenvalue ?>" value="<?= $hiddenvalue ?>">
                            </div>
                            <?php
                        } else if ('select' == $type) {
                            // Note: if not empty then only display select field.

                            $selectvalues = ( isset( $field_options['selectvalues']) ) ? $field_options['selectvalues'] : '';
                            $selectvalues = apply_filters( 'wpml_translate_single_string', $selectvalues, 'Click to Chat for WhatsApp', "greetings_form_$field" . "_selectvalues" );


                            if ( is_string($selectvalues) && !empty($selectvalues) ) {

                                // create array from string with comma separated values and new line. 
                                // (comma, new line both added, it can consider as multiple fileds.if not empyty is called at select field. to avoid empty select field.)
                                $selectvalues = preg_split( "/\r\n|\n|\r|,/", $selectvalues );
                                // $selectvalues = preg_split( "/\n|,/", $selectvalues );
                                ?>

                                <div class="ctc_form_field <?= $id_gen ?>" style="<?= $fileds_pr_css ?>">
                                    <label style="<?= $filed_label_css ?>" for="<?= $id_gen ?>"><?= $field_name ?></label>
                                    <select name="<?= $name ?>" style="<?= $filed_input_css ?>" class="ht_ctc_g_form_field <?= $add_to_prefilled ?>" id="<?= $id_gen ?>" <?= $required ?> data-name="<?= $field_name ?>" style="width:100%;">
                                        <?php
                                        // if placeholder not empty..
                                        if ( '' !== $placeholder ) {
                                            $is_select_placeholder = 'y';
                                            ?>
                                            <option value="" style="color:gray"><?= $placeholder ?></option>
                                            <?php
                                        }
                                        if ( is_array($selectvalues) && isset($selectvalues[0]) ) {
                                            foreach ($selectvalues as $select_value) {
                                                $select_value = trim($select_value);
                                                if ( '' !== $select_value ) {
                                                    ?>
                                                    <option value="<?= $select_value ?>"><?= $select_value ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                            }
                        } else if ('number' == $type) {
                            /**
                             * intl number field
                             * 
                             * note: field value: full number with country code. only getting if number is valid like.. if only a few digits is added its not getting country code.
                             * 
                             * if is_number_field is y. then add styles related to intl number field. styles added after this loop..
                             * 
                             * add ht_ctc_g_form_field class name to the hidden field created by intltel. and similar ctc_g_field_add_to_prefilled if required.
                             */
                            $is_number_field = 'y';
                            ?>
                            <div class="ctc_form_field <?= $id_gen ?> ctc_form_intl_input" style="<?= $fileds_pr_css ?>" >
                                <label style="<?= $filed_label_css ?>" for="<?= $id_gen ?>"><?= $field_name ?></label>
                                <input type="text" class="ctc_intl_number ht_ctc_default ctc_number_padding <?= $add_to_prefilled ?>" style="<?= $filed_input_css_intl ?>" id="<?= $id_gen ?>" name="<?= $name ?>" <?= $required ?> data-name="<?= $field_name ?>" style="width:100%;" placeholder="<?= $placeholder ?>">
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="ctc_form_field <?= $id_gen ?>" style="<?= $fileds_pr_css ?>">
                                <label style="<?= $filed_label_css ?>" for="<?= $id_gen ?>"><?= $field_name ?></label>
                                <input type="<?= $type ?>" style="<?= $filed_input_css ?>" class="ht_ctc_g_form_field <?= $add_to_prefilled ?>" id="<?= $id_gen ?>" name="<?= $name ?>" <?= $required ?> data-name="<?= $field_name ?>" style="width:100%;" placeholder="<?= $placeholder ?>">
                            </div>
                            <?php
                        }
                    $key_gen++;
                    }

                    // is_number_field is y.
                    if ( 'y' == $is_number_field ) {

                        $z_index = (isset($os['zindex'])) ? esc_attr($os['zindex']) : '999999995';
                        $z_index = intval($z_index) + 1;
                        
                        ?>
                        <style id="greetings_form_number">
                            .iti {
                                z-index: <?= $z_index ?>;
                            }
                            .ctc_number_padding,
                            .iti__search-input {
                                padding: 9px;
                            }
                            [dir="rtl"] .iti__dropdown-content {
                                left: 0;
                                /* right: auto !important; */
                            }
                        </style>
                        <?php
                    }

                    // is_select_placeholder is y.
                    if ( 'y' == $is_select_placeholder ) {
                        // add styles, scripts for select field placeholder.
                        ?>
                        <style>
                            .ctc_form_select_one,
                            .ctc_form_select_default {
                                color: gray;
                            }
                        </style>

                        <script>
                            if (document.querySelector('.ctc_form_field select')) {
                                document.querySelectorAll('.ctc_form_field select').forEach( function (select) {
                                    if (select.value === "") {
                                        select.classList.add('ctc_form_select_one');
                                    }
                                    select.addEventListener('click', function () {
                                        select.classList.remove('ctc_form_select_one');
                                    });
                                    select.addEventListener('change', function () {
                                        if (select.value === "") {
                                            select.classList.add('ctc_form_select_default');
                                        } else {
                                            select.classList.remove('ctc_form_select_default');
                                        }
                                    });
                                });
                            }
                        </script>
                        <?php
                    }



                }
                // opt-in field..
                $opt_in = 'Privacy Policy';
                if ( isset( $ht_ctc_greetings) && isset( $ht_ctc_greetings['is_opt_in']) && '' !== $ht_ctc_greetings['is_opt_in'] && isset( $ht_ctc_greetings['opt_in']) ) {
                    $opt_in = $ht_ctc_greetings['opt_in'];
                    ?>
                    <div class="ctc_form_field ctc_opt_g_form" style="<?= $filed_checbox_pr_css ?>">
                        <input type="checkbox" style="<?= $filed_checbox_input_css ?>" class="ht_ctc_g_form_field ctc_g_its_checkbox" id="ctc_opt_g_form" data-name="ctc_opt_g_form" name="">
                        <label style="<?= $filed_checbox_label_css ?>" for="ctc_opt_g_form"><?= $opt_in ?></label>
                    </div>
                    <?php
                }
                ?>
                

                <div class="ctc_g_sentbutton" style="<?= $send_css ?>">
                    <div class="ht_ctc_chat_greetings_for_forum_link ctc-analytics">
                    <?php
                    if ( is_file( $g_cta_path ) ) {
                        include $g_cta_path;
                    }
                    ?>
                    </div>
                    <input hidden class="ht_ctc_chat_greetings_forum_link" type="submit" style="display:none;">
                </div>
            </form> 
        </div>
    </div>
    <?php
    if ( '' !== $ht_ctc_greetings['bottom_content'] ) {
    ?>
    <div class="ctc_g_bottom" style="<?= $bottom_css ?>">
        <?= wpautop( $ht_ctc_greetings['bottom_content'] ) ?>
    </div>
    <?php
    }
    ?>
    
</div>