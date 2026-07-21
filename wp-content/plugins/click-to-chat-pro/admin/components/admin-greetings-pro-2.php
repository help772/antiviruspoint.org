<?php
/**
 *  ** template/component **
 * 
 * Greetings dialog PRO - 2 - settings.. Multi Agent
 * 
 * @subpackage PRO Admin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$safe_fallback_values = [];
if (isset( $fallback_values )) {
    $safe_fallback_values = $fallback_values;
}

$gtm_offset = esc_attr( get_option('gmt_offset') );

$g2_pro_options = get_option( 'ht_ctc_greetings_pro_2', $safe_fallback_values );

$agents = ( isset($g2_pro_options['agents']) ) ? array_map( 'esc_attr', $g2_pro_options['agents'] ) : '';

$agent_count = ( isset($g2_pro_options['agent_count']) ) ? esc_attr( $g2_pro_options['agent_count'] ) : 1;
$key_gen = 1;


$pre_filled_placeholder = "Hello {site} \nLike to know more information about {title}, {url}";

$days = array(
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        );

$fallback_img_url = plugins_url( 'inc/assets/img/wa-round-64.png', HT_CTC_PRO_PLUGIN_FILE );
?>
 
<div class="ht_ctc_pro_agent">

    <!-- display fileds -->
    <div class="ctc_display_agents ctc_sortable">
        <?php
    
        if ( is_array($agents) && isset($agents[0]) ) {
            foreach ($agents as $agent) {
                
                $agent_options = ( isset($g2_pro_options[$agent]) ) ? map_deep( $g2_pro_options[$agent], 'esc_attr' ) : '';

                $enable_checked = ( isset($agent_options['enable']) ) ? '1' : '';

                $number = ( isset( $agent_options['number']) ) ? $agent_options['number'] : '';
                if ( class_exists( 'HT_CTC_Formatting' ) && method_exists( 'HT_CTC_Formatting', 'wa_number' ) ) {
                    $number = HT_CTC_Formatting::wa_number( $number );
                }
                if ('' !== $number) {
                    $number = "+$number";
                }

                $col_header_styles = "";
                if ('' == $enable_checked) {
                    $col_header_styles = "opacity:0.5;";
                }

                $title = ( isset( $agent_options['title']) ) ? $agent_options['title'] : '';
                $description = ( isset( $agent_options['description']) ) ? $agent_options['description'] : '';
                $pre_filled = ( isset( $agent_options['pre_filled']) ) ? $agent_options['pre_filled'] : '';

                $agent_image_id = ( isset( $agent_options['agent_image_id']) ) ? $agent_options['agent_image_id'] : '';
                $agent_image_url = ( isset( $agent_options['agent_image_url']) ) ? $agent_options['agent_image_url'] : '';

                $img_url = '';
                $hide_img = "";
                $hide_fallback_img = "";
                $hide_remove_img_button = "";

                if ('' !== $agent_image_id) {
                    // wp_get_attachment_image_url return false if image is not available
                    $img_url = wp_get_attachment_image_url( $agent_image_id, 'medium' );
                }

                if (false !== $img_url && '' !== $img_url) {
                    // image is available
                    $hide_fallback_img = "display:none;";
                } else {
                    $hide_img = "display:none;";
                    $hide_remove_img_button = "display:none;";

                    $img_url = $fallback_img_url;
                }


                $agent_header = ( '' !== $title ) ? $title : 'Agent';
                $ref_name = ( isset( $agent_options['ref_name']) ) ? $agent_options['ref_name'] : '';

                $header_ref_name = '';
                if ('' !== $ref_name) {
                    $header_ref_name = ": $ref_name";
                }

                // timings/schedule - always, set
                $timings = ( isset( $agent_options['timings']) ) ? $agent_options['timings'] : 'always';
                $display_schedule = ( 'set' == $timings ) ? "" : "display:none;";
                $current_site_time = ( 'set' == $timings ) ? "" : "ctc_init_display_none";

                $agent_active_class = ( isset( $agent_options['fallback_values']) ) ? 'active' : '';


                ?>

                <div class="ht_ctc_pro_agent">
                    <div class="agent">
                        <ul class="collapsible coll_active <?= $agent ?>" data-coll_active = "<?= $agent ?>" >
                        <li class="<?= $agent_active_class ?>">
                        <div class="collapsible-header" style="display:flex; <?= $col_header_styles ?>">
                            <span class="left_icon dashicons dashicons-editor-justify handle" style="color:#ddd; cursor:move;"></span>
                            <span class="agent_header" style="line-height:1.4; margin: 0 11px;"><span class="header_agent_name"><?= $agent_header ?></span><span class="header_ref_name"><?= $header_ref_name ?></span></span>
                            <span style="color:#039be5; cursor:pointer;" class="ht_ctc_pro_agent_remove_agent_link right_icon dashicons dashicons-no-alt" title="Remove Agent"></span>
                        </div>
                        <div class="collapsible-body">

                        <div class="row">

                            <!-- agent number - ht_ctc_greetings_pro_2[agents][<agent_count>] -->
                            <input name="ht_ctc_greetings_pro_2[agents][]" style="display: none;" type="text" class="ht_ctc_pro_agent_number" value="<?= $agent ?>">
                        
                            <!-- name: ht_ctc_greetings_pro_2[agents][<agent_count>] -->
                            <input style="display: none;" type="text" class="ht_ctc_pro_agent_field_ref_number" value="<?= $agent_count ?>">

                            <!-- reference name - float right -->
                            <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][ref_name]" value="<?= $ref_name ?>" placeholder="Reference name" id="<?= $agent .'_ref_name' ?>" type="text" class="ht_ctc_pro_agent_field_ref_name ref_name browser-default input-margin" style="float:right; border:0; background-color:inherit; margin-top:-19px; width:auto; max-width: 130px; color: darkgrey;">

                            <div class="col s12" style="margin-bottom:24px;">
                                <label>
                                    <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][enable]" value="1" <?php checked( $enable_checked, 1 ); ?> id="<?= $agent .'_enable' ?>" type="checkbox" class="ht_ctc_pro_agent_field_enable">
                                    <span><?php _e( 'Enable Agent', 'click-to-chat-for-whatsapp' ); ?></span>
                                </label>
                            </div>

                            <div class="input-field col s12">
                                <label style="margin-top:-5px;"><?php _e( 'WhatsApp Number', 'click-to-chat-for-whatsapp' ); ?></label>
                                <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][number]" data-name="ht_ctc_greetings_pro_2[<?= $agent ?>][number]" value="<?= $number ?>" id="<?= $agent .'_wa_number' ?>" type="text" class="ht_ctc_pro_agent_field_number intl_number browser-default">
                            </div>
                            <div class="input-field col s12">
                                <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][title]" value="<?= $title ?>" placeholder="" id="<?= $agent .'_title' ?>" type="text" class="ht_ctc_pro_agent_field_title input-margin">
                                <label><?php _e( 'Title (first line)', 'click-to-chat-for-whatsapp' ); ?></label>
                            </div>
                            <div class="input-field col s12">
                                <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][description]" value="<?= $description ?>" placeholder="" id="<?= $agent .'_description' ?>" type="text" class="ht_ctc_pro_agent_field_description input-margin">
                                <label><?php _e( 'Description (second line)', 'click-to-chat-for-whatsapp' ); ?></label>
                            </div>
                            <div class="input-field col s12">
                                <textarea name="ht_ctc_greetings_pro_2[<?= $agent ?>][pre_filled]" style="min-height: 64px;" placeholder="<?= $pre_filled_placeholder ?>" id="<?= $agent .'_pre_filled' ?>" class="ht_ctc_pro_agent_field_pre_filled materialize-textarea input-margin"><?= $pre_filled ?></textarea>
                                <label><?php _e( 'Pre-filled message', 'click-to-chat-for-whatsapp' ); ?></label>
                                <p class="description"><?php _e( "Variables {site}, {url}, {title} to replace with site name, current webpage URL, Post title", 'click-to-chat-for-whatsapp' ); ?> - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/pre-filled-message/"><?php _e( 'more info', 'click-to-chat-for-whatsapp' ); ?></a> </p>
                            </div>
                            <div class="col s12 pr_agent_image <?= 'image_' . $agent ?>" style="display:flex; align-items:center;">
                                <img class="multi_agent_preview_image" style="width:48px; height:48px; border-radius:50%; <?= $hide_img ?>" src="<?= $img_url ?>" />
                                <img class="multi_agent_preview_fallback_image" style="width:48px; height:48px; border-radius:50%; <?= $hide_fallback_img ?>" src="<?= $fallback_img_url ?>" />
                                <div class="input-field">
                                    <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][agent_image_id]" value="<?= $agent_image_id ?>" placeholder="" id="<?= $agent .'_agent_image_id' ?>" type="hidden" class="ht_ctc_pro_agent_field_agent_image_id">
                                    <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][agent_image_url]" value="<?= $agent_image_url ?>" placeholder="" id="<?= $agent .'_agent_image_url' ?>" type="hidden" class="ht_ctc_pro_agent_field_agent_image_url">
                                    <input type='button' style="margin: 0 7px;" class="button-primary greetings_multi_agent_image" value="Agent Image" data-agent="<?= $agent ?>"/>
                                    <input type='button' style="margin: 0 1px; <?= $hide_remove_img_button ?>" class="button-secondary greetings_multi_agent_remove_image" value="Remove Image" data-agent="<?= $agent ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="row multi_timings">
                            <p class="col">
                                <label>
                                <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][timings]" value="always" type="radio" <?php checked( 'always' == $timings ); ?> class="with-gap"/>
                                <span><?php _e( '24x7 Online', 'click-to-chat-for-whatsapp' ); ?></span>
                                </label>
                            </p>
                            <p class="col">
                                <label>
                                <input name="ht_ctc_greetings_pro_2[<?= $agent ?>][timings]" value="set" type="radio" <?php checked( 'set' == $timings ); ?> class="with-gap"/>
                                <span><?php _e( 'Set timings', 'click-to-chat-for-whatsapp' ); ?></span>
                                </label>
                            </p>
                        </div>

                        <p class="description current_site_time <?= $current_site_time ?>" style="margin-bottom:12px;font-size:12px;">Current Site Time: <code><?php echo current_time( 'mysql' ); ?></code> <span style="font-size:11px;">( Settings -> General - Timezone (<?= $gtm_offset ?>) )</span></p>
                        <?php
                        
                        foreach ($days as $day ) {
                            
                            $st = $day.'_st';
                            $et = $day.'_et';

                            $day_checked = ( isset($agent_options[$day]) ) ? '1' : '';
                            $day_times = ( isset( $agent_options[$day . '_times'] ) ) ? $agent_options[$day . '_times'] : '';

                            $add_time_here_class = ( '1' == $day_checked ) ? '' : 'ctc_init_display_none';
                            ?>

                            <div class="multi_schedule" style="<?= $display_schedule ?> margin-bottom: 12px; border: 1px solid #dddddd; padding: 20px;" data-agent="<?= $agent ?>" data-day="<?= $day ?>">
                                <div class="flex" style="display:flex; align-items: center; gap: 12px;">

                                    <div class="input-field" style="min-width: 130px; margin: 1px 0px;">
                                        <p>
                                            <label>
                                                <input class="day_checkbox" name="<?= $dbrow; ?>[<?= $agent ?>][<?= $day ?>]" type="checkbox" value="1" <?php checked( $day_checked, 1 ); ?> id="<?= $agent .'_'. $day ?>">
                                                <span><?= ucfirst($day) ?></span>
                                            </label>
                                        </p>
                                    </div>

                                    <div style="display: flex; flex-direction: column;">
                                    <?php
                                    if (is_array($day_times) ) { // && $day_times[0]
                                        ?>
                                        <div class="add_time_here <?= $add_time_here_class ?> ">
                                        <?php
                                        $set_count = 1;
                                        foreach ($day_times as $set ) {

                                            $start_time = ( isset( $set['st'] ) ) ? esc_attr( $set['st'] ) : '';
                                            $end_time = ( isset( $set['et'] ) ) ? esc_attr( $set['et'] ) : '';

                                            /**
                                             * is start or time is set then only end..
                                             * and if only one is added then other one is 0: 00;
                                             */
                                            if ('' == $start_time && '' == $end_time) {
                                                continue;
                                            }
                                            // by above if - alteast one is added....

                                            if ('' == $start_time || '' == $end_time) {
                                                $start_time = ('' !== $start_time) ? $start_time : '0: 00';
                                                $end_time = ('' !== $end_time) ? $end_time : '0: 00';
                                            }
                                            ?>
                                            <div class="add_time" style="display: flex; flex-direction: column;">
                                                <div style="display: flex;">
                                                    <div class="input-field ctc_time_<?= $day ?>" style="min-width: 130px; margin: 1px 0px;">
                                                        <input name="<?= $dbrow ?>[<?= $agent ?>][<?= $day ."_times" ?>][set_<?= $set_count ?>][st]" placeholder="" value="<?= $start_time ?>" id="<?= $agent .'_'. $st ?>" type="text" class="ctc_j_timepicker_start">
                                                        <span class="helper-text">Start time</span>
                                                    </div>
                                                    <div class="input-field ctc_time_<?= $day ?>" style="min-width: 130px; margin: 1px 10px;">
                                                        <input name="<?= $dbrow ?>[<?= $agent ?>][<?= $day ."_times" ?>][set_<?= $set_count ?>][et]" placeholder="" value="<?= $end_time ?>" id="<?= $agent .'_'. $et ?>" type="text" class="ctc_j_timepicker_end">
                                                        <span class="helper-text">End time</span>
                                                    </div>
                                                    <span style="color:#dddddd;float:right;cursor:pointer;" class="ht_ctc_pro_agent_remove_time_set_link dashicons dashicons-no-alt" title="Remove Agent"></span>
                                                </div>
                                                <!-- <span class="agent_set_time_range" style="color:#aeaeae;">Time set for __H __M from __:__ to __:__ </span> -->
                                            </div>
                                            <?php
                                            $set_count++;
                                        }
                                        ?>
                                            <div class="add_time_set" style="display: flex; flex-direction: column;">
                                            </div>
                                            <div class="today_schedule"><span class="online_24_content" style="display: none;">24 Hours online&emsp;(or)&emsp;</span><span class="click_to_add_time" style="color: #039be5; cursor: pointer; float: right;">Add Time</span></div>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="add_time_here <?= $add_time_here_class ?>">
                                            <div class="add_time_set" style="display: flex; flex-direction: column;">
                                            </div>
                                            <div class="today_schedule"><span class="online_24_content">24 Hours online&emsp;(or)&emsp;</span><span class="click_to_add_time" style="color: #039be5; cursor: pointer; float: right;">Add Time</span></div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>


                            <?php

                        }
                        ?>

                        <div>
                            <p class="unique_id description">Unique Id: <span><?= $agent ?></span> (useful at translation plugins, .. )</p>
                        </div>

                        </div>
                        </li>
                        </ul>
                    </div>
                </div>


                <?php
                $key_gen++;
            }
        }

        ?>    
    </div>

    <!-- new fileds - while adding -->
    <div class="ctc_new_agents">
    </div>

    <!-- Add Agent - button -->
    <div class="ctc_add_agent_button" style="display: inline-block; margin: 10px 0px; cursor:pointer; font-size:16px; font-weight:500; border: 2px solid #2196f3; padding: 8px; border-radius:25px;">
        <span style="color: #039be5;" class="dashicons dashicons-plus-alt2" ></span>
        <span style="color: #039be5;">Add Agent</span>
    </div>



    <!-- snippets, .... -->
    <div class="ctc_agent_snippets" style="display: none;">

        <!-- filed count - agent_1 agent_2 ... -->
        <input type="text" name="ht_ctc_greetings_pro_2[agent_count]" class="ht_ctc_pro_agent_count" value="<?= $agent_count ?>">

        <!-- snippet: add agent -->
        <div class="ht_ctc_pro_agent">

            <div class="agent">
                <ul class="collapsible">
                <li class="active">

                <div class="collapsible-header">
                    <span class="agent_header"><span class="header_agent_name">Agent</span><span class="header_ref_name"></span></span>
                    <span style="color:#039be5; margin-left:auto; cursor:pointer;" class="ht_ctc_pro_agent_remove_agent_link dashicons dashicons-no-alt" title="Remove Agent"></span>
                </div>
                <div class="collapsible-body">

                <div class="row">

                    <!-- name: ht_ctc_greetings_pro_2[agents][<agent_count>] -->
                    <input style="display: none;" type="text" class="ht_ctc_pro_agent_field_ref_number" value="<?= $agent_count ?>">

                    <input value="" placeholder="Reference name" id="ref_name" type="text" class="ht_ctc_pro_agent_field_ref_name ref_name browser-default input-margin" style="float:right; border:0; background-color:inherit; margin-top:-19px; width:auto; max-width: 130px; color: darkgrey;">

                    <input value="1" id="enable" checked="checked" type="checkbox" style="display:none;" class="ht_ctc_pro_agent_field_enable hide">

                    <div class="input-field col s12" style="margin-top:0;">
                        <p class="description" style="margin-bottom:5px;"><?php _e( 'WhatsApp Number', 'click-to-chat-for-whatsapp' ); ?></p>
                        <input value="" id="whatsapp_number" type="text" class="browser-default ht_ctc_pro_agent_field_number input-margin">
                    </div>
                    <div class="input-field col s12">
                        <input value="" placeholder="Name, Department, .." id="agent_title" type="text" class="ht_ctc_pro_agent_field_title input-margin">
                        <label for="agent_title"><?php _e( 'Title', 'click-to-chat-for-whatsapp' ); ?></label>
                    </div>
                    <div class="input-field col s12">
                        <input value="" placeholder="Name, Department, .." id="agent_description" type="text" class="ht_ctc_pro_agent_field_description input-margin">
                        <label for="agent_description"><?php _e( 'Description', 'click-to-chat-for-whatsapp' ); ?></label>
                    </div>
                    <div class="input-field col s12">
                        <textarea style="min-height: 64px;" placeholder="<?= $pre_filled_placeholder ?>" id=" pre_filled " class="ht_ctc_pro_agent_field_pre_filled materialize-textarea input-margin"></textarea>
                        <label for="pre_filled"><?php _e( 'Pre-filled message', 'click-to-chat-for-whatsapp' ); ?></label>
                        <p class="description"><?php _e( "Variables {site}, {url}, {title} to replace with site name, current webpage URL, Post title", 'click-to-chat-for-whatsapp' ); ?> - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/pre-filled-message/"><?php _e( 'more info', 'click-to-chat-for-whatsapp' ); ?></a> </p>
                    </div>
                    <div class="col s12 pr_agent_image" style="display:flex; align-items:center;">
                        <img class="multi_agent_preview_image" style="width:48px; height:48px; border-radius:50%; display:none;"/>
                        <img class="multi_agent_preview_fallback_image" style="width:48px; height:48px; border-radius:50%;" src="<?= $fallback_img_url ?>" />
                        <div class="input-field">
                            <input placeholder="" id="agent_image_id" type="hidden" class="ht_ctc_pro_agent_field_agent_image_id">
                            <input placeholder="" id="agent_image_url" type="hidden" class="ht_ctc_pro_agent_field_agent_image_url">
                            <input type='button' style="margin: 0 7px;" class="button-primary greetings_multi_agent_image" value="Agent Image"/>
                            <input type='button' style="margin: 0 1px; display:none;" class="button-secondary greetings_multi_agent_remove_image" value="Remove Image"/>
                        </div>
                    </div>
                </div>

                <div class="row multi_timings">
                    <p class="col">
                        <label>
                        <input checked value="always" type="radio" data-key="timings" checked class="ht_ctc_pro_agent_schedule with-gap"/>
                        <span><?php _e( '24x7 Online', 'click-to-chat-for-whatsapp' ); ?></span>
                        </label>
                    </p>
                    <p class="col">
                        <label>
                        <input value="set" type="radio" data-key="timings" class="ht_ctc_pro_agent_schedule with-gap"/>
                        <span><?php _e( 'Set timings', 'click-to-chat-for-whatsapp' ); ?></span>
                        </label>
                    </p>
                </div>
                
                <p class="description current_site_time ctc_init_display_none" style="margin-bottom:12px;font-size:12px;">Current Site Time: <code><?php echo current_time( 'mysql' ); ?></code> <span style="font-size:11px;">( Settings -> General - Timezone (<?= $gtm_offset ?>) )</span></p>
                <?php

                foreach ($days as $day ) {
                    
                    $st = $day.'_st';
                    $et = $day.'_et';
                    ?>
                                        
                    <div class="multi_schedule" style="display:none; margin-bottom: 12px; border: 1px solid #dddddd; padding: 20px;"">
                        <div class="flex" style="display:flex; align-items: center; gap: 12px;">

                            <div class="input-field" style="min-width: 130px; margin: 1px 0px;">
                                <p>
                                    <label>
                                        <input class="day_checkbox ht_ctc_pro_agent_schedule" type="checkbox" value="1" id="<?= $day ?>" data-key="<?= $day ?>">
                                        <span><?= ucfirst($day) ?></span>
                                    </label>
                                </p>
                            </div>

                            <div style="display: flex; flex-direction: column;">
                                <div class="add_time_here ctc_init_display_none">
                                    <div class="add_time_set" style="display: flex; flex-direction: column;">
                                    </div>
                                    <div class="today_schedule"><span class="online_24_content">24 Hours online&emsp;(or)&emsp;</span><span class="click_to_add_time" style="color: #039be5; cursor: pointer; float: right;">Add Time</span></div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php
                }
                ?>

                <div>
                    <p class="unique_id description">Unique Id: <span></span> (useful at translation plugins, .. )</p>
                </div>


                </div>
                </li>
                </ul>
            </div>
            
        </div>

        <!-- snippet: add time -->
        <div class="add_time" style="display: flex;">
            <div class="input-field" style="min-width: 130px; margin: 1px 0px;">
                <input placeholder="" type="text" class="ctc_j_timepicker_start add_time_st">
                <span class="helper-text">Start time</span>
            </div>
            <div class="input-field" style="min-width: 130px; margin: 1px 10px;">
                <input placeholder="" type="text" class="ctc_j_timepicker_end add_time_et">
                <span class="helper-text">End time</span>
            </div>
            <span style="color:#dddddd; float:right; cursor:pointer;" class="ht_ctc_pro_agent_remove_time_set_link dashicons dashicons-no-alt" title="Remove Agent"></span>
        </div>


    </div>
    <!-- #END snippets -->

</div>


<br><br>