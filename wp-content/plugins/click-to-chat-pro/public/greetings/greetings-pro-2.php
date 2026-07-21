<?php
/**
 * PRO Greetings - template - 2  - Multi Agent
 * 
 * 
 * @uses $agent_loop_count at multi agent styles. 7_1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$g2_pro_options = get_option( 'ht_ctc_greetings_pro_2' );
$greetings = get_option('ht_ctc_greetings_options');
$greetings_settings = get_option('ht_ctc_greetings_settings');

$ht_ctc_greetings['main_content'] = do_shortcode( $ht_ctc_greetings['main_content'] );

$cta_style = ( isset($g2_pro_options['cta_style']) ) ? esc_attr( $g2_pro_options['cta_style'] ) : '7_1';
$g_cta_path = plugin_dir_path( HT_CTC_PLUGIN_FILE ) . 'new/inc/greetings/greetings_styles/g-cta-' . $cta_style. '.php';

$agent_loop_count = 1;

$initial_display = ( isset($g2_pro_options['initial_display']) ) ? esc_attr( $g2_pro_options['initial_display'] ) : 'agents';

$agent_style = '7_1';
$agent_style = 'card-1';
$g_cta_multi_agent_path = plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) . 'public/greetings/greetings_styles/multi-agent/multi-agent-' . $agent_style. '.php';

$g_optin_path = plugin_dir_path( HT_CTC_PLUGIN_FILE ) . 'new/inc/greetings/greetings_styles/opt-in.php';

// css
$header_bg_color = ( isset($g2_pro_options['header_bg_color']) ) ? esc_attr( $g2_pro_options['header_bg_color'] ) : '';
if ('' == $header_bg_color) {
    $header_bg_color = '#ffffff';
}
$main_bg_color = ( isset($g2_pro_options['main_bg_color']) ) ? esc_attr( $g2_pro_options['main_bg_color'] ) : '';
$message_box_bg_color = ( isset($g2_pro_options['message_box_bg_color']) ) ? esc_attr( $g2_pro_options['message_box_bg_color'] ) : '';

// ..no admin settings..
$agent_box_bg_color = ( isset($g2_pro_options['agent_box_bg_color']) ) ? esc_attr( $g2_pro_options['agent_box_bg_color'] ) : '#f8f8f8';


$header_css = "color:#ffffff; background-color:$header_bg_color;";
$main_css = '';
$message_box_css = 'margin: 8px 5px;';

$send_css = 'text-align:center; padding: 11px 25px 9px 25px; cursor:pointer; background-color:#ffffff;';

$bottom_css = 'padding: 2px 25px 2px 25px; text-align:center; font-size:12px; background-color:#ffffff;';


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

// page url, title, woo .. 
$page_url = get_permalink();
$post_title = esc_html( get_the_title() );

if ( is_home() || is_front_page() ) {
    // is home page
    $page_url = home_url('/');
    // if home page is a loop then return site name.. (instead of getting the last post title in that loop)
    $post_title = HT_CTC_BLOG_NAME;

    // if home page is a page then return page title.. (if not {site} and {title} will be same )
    if ( is_page() ) {
        $post_title = esc_html( get_the_title() );
    }
} elseif ( is_singular() ) {
    // is singular
    $page_url = get_permalink();
    $post_title = esc_html( get_the_title() );
} elseif ( is_archive() ) {

    if ( isset($_SERVER['HTTP_HOST']) && $_SERVER['REQUEST_URI'] ) {
        $protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 'https' : 'http';
        $page_url = $protocol . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    if ( is_category() ) {
        $post_title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $post_title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $post_title = get_the_author();
    } elseif ( is_post_type_archive() ) {
        $post_title = post_type_archive_title( '', false );
    } elseif ( function_exists( 'is_tax') && function_exists( 'single_term_title') && is_tax() ) {
        $post_title = single_term_title( '', false );
    } else {
        if ( function_exists('get_the_archive_title') ) {
            $post_title = get_the_archive_title();
        }
    }

}

$product_name = '';
$price = '';
$regular_price = '';
$sku = '';
$price_formatted = '';
if ( class_exists( 'WooCommerce' ) && function_exists( 'is_product' ) && function_exists( 'wc_get_product' )) {
    if ( is_product() ) {
        $product = wc_get_product();

        $product_name = $product->get_name();
        // $title = $product->get_title();
        $price = $product->get_price();
        $regular_price = $product->get_regular_price();
        $sku = $product->get_sku();

        // $price_formatted - get thousand separator, decimal separator, currency symbol
        if ( function_exists( 'wc_price' ) ) {
            // $price_formatted = strip_tags( wc_price( $price ) );
            $price_formatted = html_entity_decode( strip_tags( wc_price( $price ) ));
        } else {
            $price_formatted = $price;
        }


    }
}

$g_size = ( isset($greetings_settings['g_size']) ) ? esc_attr( $greetings_settings['g_size'] ) : 's';
$main_bg_image = ( isset($g2_pro_options['main_bg_image']) ) ? 'yes' : '';
$main_padding_bottom = ('yes' == $main_bg_image) ? '72px' : '40px';

$message_box_minus_width = '20px';
if ('s' == $g_size) {
    $message_box_minus_width = '15px';
} else if ( 'm' == $g_size ) {
    // $main_padding_bottom = '98px';
    $main_padding_bottom = '78px';
    $message_box_minus_width = '30px';
} else if ( 'l' == $g_size ) {
    // $main_padding_bottom = '108px';
    $main_padding_bottom = '88px';
    $message_box_minus_width = '40px';
}

/**
 * based on Agent style
 */
$send_agent_css = '';
if ( '7_1' == $agent_style ) {

    if ('' !== $message_box_bg_color) {
        $message_box_css .= "padding:6px 8px 8px 9px;background-color:$message_box_bg_color;";
        $main_css .= ('yes' == $rtl_page) ? "padding: 18px 18px $main_padding_bottom 24px;" : "padding: 18px 24px $main_padding_bottom 18px;" ;
    } else {
        $main_css .= 'padding: 18px 19px 30px 19px;';
        // $main_css .= 'padding: 18px 0px 9px 0px;';
    }

    $send_agent_css = 'padding: 7px 5px 7px 5px;';

} else if ( 'card-1' == $agent_style ) {

    if ('' !== $message_box_bg_color) {
        $message_box_css .= "padding:6px 8px 8px 9px;background-color:$message_box_bg_color;";
        $main_css .= ('yes' == $rtl_page) ? "padding: 18px 18px $main_padding_bottom 24px;" : "padding: 18px 24px $main_padding_bottom 18px;" ;
    }
    
    $send_agent_css = 'padding: 2.4px 5px 2.4px 5px;';
}

$ctc_g_agents_css = '';

if ('content' == $initial_display) { 
    $main_css .= "background-color:$main_bg_color;";
    $ctc_g_agents_css .= "display:none;";
    $header_css .= "font-size:16px; padding: 12px 25px 12px 25px;";
} else {
    // direct agent
    $main_css .= "padding: 1px 0px 2.4px 0px; background-color:$agent_box_bg_color;";
    $header_css .= "font-size:17px; padding: 12px 25px 14px 25px;";
}


$agents = ( isset($g2_pro_options['agents']) ) ? array_map( 'esc_attr', $g2_pro_options['agents'] ) : '';
$key_gen = 1;

$g_header_image = ( isset($greetings['g_header_image']) ) ? esc_attr( $greetings['g_header_image'] ) : '';

if ('' !== $g_header_image) {
    $header_css .= "line-height:1.1;";
} else {
    $header_css .= "line-height:1.3;";
}

$days = array(
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            );
?>
<style>
<?php
// message_box_css - box like bg color.
if ('content' == $initial_display && '' !== $message_box_bg_color) {
?>
.ctc_g_message_box {
    position: relative;
    box-shadow: 0 1px 0.5px 0 rgba(0,0,0,.14);
    max-width: calc(100% - <?= $message_box_minus_width ?>);
}
.ctc_g_message_box:before {
  content: "";
  position: absolute;
  top: 0px;
  height: 18px;
  width: 9px;
  background-color: <?= $message_box_bg_color ?>;
}
<?php
if ('yes' == $rtl_page) {
?>
.ctc_g_message_box {
    border-radius: 7px 0px 7px 7px;
}
.ctc_g_message_box:before {
  left: 100%;
  clip-path: polygon(0% 0%, 0% 50%, 100% 0%);
    -webkit-clip-path: polygon(0% 0%, 0% 50%, 100% 0%);
}
<?php
} else {
?>
.ctc_g_message_box {
    border-radius: 0px 7px 7px 7px;
}
.ctc_g_message_box:before {
  right: 99.7%;
  clip-path: polygon(0% 0%, 100% 0%, 100% 50%);
    -webkit-clip-path: polygon(0% 0%, 100% 0%, 100% 50%);
}
<?php
}
}

if ( 'card-1' == $agent_style ) {
?>
.g_multi_box {
    box-shadow: 0 2px 2px 0 rgb(0 0 0 / 14%), 0 3px 1px -2px rgb(0 0 0 / 12%), 0 1px 5px 0 rgb(0 0 0 / 20%);
}
.g_multi_box:hover {
    /* box-shadow: 0px 0px 7px rgba(0,0,0,.1); */
    box-shadow: 0 3px 3px 0 rgb(0 0 0 / 14%), 0 1px 7px 0 rgb(0 0 0 / 12%), 0 3px 1px -1px rgb(0 0 0 / 20%);
    /* box-shadow: 0px 2px 3px rgb(0 0 0 / 18%); */
    transform:scale(1.014);
}
<?php
}

?>
</style>

<div>
    <?php
    if ( '' !== $ht_ctc_greetings['header_content'] ) {
        if ('agents' == $initial_display) {
            // direct agents display, main content also add at header.. - and header no image like...
            // if initial display is 'content' then js will hanlde main content change, agents display and styles..
            ?>
            <div class="ctc_g_heading ctc_g_header_content" style="<?= $header_css ?>">
                <?php
                if (!empty($g_header_image)) {
                    ?>
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
                        <div class="g_heading">
                            <?= wpautop($ht_ctc_greetings['header_content']) ?>
                        </div>
                    </div>
                    <?php
                    if ('' !== $ht_ctc_greetings['main_content']) {
                        ?>
                        <div class="ctc_g_heading_for_main_content" style="color:#ffffff; font-size:12px;">
                            <div style="padding:4px 0px; text-align: inherit;"><?= wpautop( $ht_ctc_greetings['main_content'] ) ?></div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="g_heading">
                        <?= wpautop($ht_ctc_greetings['header_content']) ?>
                    </div>
                    <?php
                    if ('' !== $ht_ctc_greetings['main_content']) {
                        ?>
                        <div class="ctc_g_heading_for_main_content" style="color:#ffffff; font-size:12px;">
                            <div style="padding:4px 0px; text-align: inherit;"><?= wpautop( $ht_ctc_greetings['main_content'] ) ?></div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        } else {
            if (!empty($g_header_image)) {
                // if header image is added
                ?>
                <div class="ctc_g_heading" style="<?= $header_css ?>">
                    <div style="display: flex; align-items: center;">
                        <div class="greetings_header_image" style="border-radius:50%;height:50px; width:50px; margin-right:9px;">
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
                        <div>
                            <?= wpautop($ht_ctc_greetings['header_content']) ?>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                // if header image is not added
                ?>
                <div class="ctc_g_heading" style="<?= $header_css ?>">
                    <?= wpautop($ht_ctc_greetings['header_content']) ?>
                    <div class="ctc_g_heading_for_main_content" style="color:#ffffff; font-size:12px;"></div>
                </div>
                <?php
            }
        }
    }

    // $main_css .= "background-image: url('bg.png'); background-size: cover;";
    ?>
    <div class="ctc_g_content" style="<?= $main_css ?>" data-agentstyle = <?= $agent_style ?>>
        <?php
        // main content display at main box if initial display is content..
        if (( 'content' == $initial_display ) && ( '' !== $ht_ctc_greetings['main_content'] )) {
            if ('yes' == $main_bg_image) {
                // if bg image is added
                ?>
                <div class="ctc_g_content_for_bg_image">
                    <div class="ctc_g_message_box ctc_g_message_box_width" style="<?= $message_box_css ?>"><?= wpautop( $ht_ctc_greetings['main_content'] ) ?></div>
                </div>
                <?php
            } else {
                // if bg image is not added
                ?>
                <div class="ctc_g_message_box ctc_g_message_box_width" style="<?= $message_box_css ?>"><?= wpautop( $ht_ctc_greetings['main_content'] ) ?></div>
                <?php
            }
        }
        ?>
        <div class="ctc_g_agents" style="<?= $ctc_g_agents_css ?>">
            <?php
            // if initial_display is agent - then optin adds here
            if ( 'agents' == $initial_display && isset($ht_ctc_greetings['is_opt_in']) && '' !== $ht_ctc_greetings['is_opt_in'] && is_file( $g_optin_path ) ) {
                $opt_in_id = 'ctc_opt_multi';
                include $g_optin_path;
            }
            ?>
            <div class="ctc_greetings_agents" style="display:flex; flex-direction:column;">
                <?php
                if ( is_array($agents) && isset($agents[0]) ) {
                    foreach ($agents as $agent) {
                        $name = "field_$key_gen";
                        $id_gen = "ht_ctc_g_form_$key_gen";
                        $agent_options = ( isset($g2_pro_options[$agent]) ) ? map_deep( $g2_pro_options[$agent], 'esc_attr' ) : '';

                        $enable = ( isset($agent_options['enable']) ) ? $agent_options['enable'] : '';
                        $number = ( isset( $agent_options['number']) ) ? $agent_options['number'] : '';
                        $number = apply_filters( 'wpml_translate_single_string', $number, 'Click to Chat for WhatsApp', "multi_$agent" . "_number" );

                        // if not enabled or if number not added. then skip that agent..
                        if ('' == $enable || '' == $number) {
                            continue;
                        }

                        if ( class_exists( 'HT_CTC_Formatting' ) && method_exists( 'HT_CTC_Formatting', 'wa_number' ) ) {
                            $number = HT_CTC_Formatting::wa_number( $number );
                        }

                        $title = ( isset( $agent_options['title']) ) ? $agent_options['title'] : '';
                        $title = apply_filters( 'wpml_translate_single_string', $title, 'Click to Chat for WhatsApp', "multi_$agent" . "_title" );

                        $description = ( isset( $agent_options['description']) ) ? $agent_options['description'] : '';
                        $description = apply_filters( 'wpml_translate_single_string', $description, 'Click to Chat for WhatsApp', "multi_$agent" . "_description" );

                        $pre_filled = ( isset( $agent_options['pre_filled']) ) ? $agent_options['pre_filled'] : '';
                        $pre_filled = apply_filters( 'wpml_translate_single_string', $pre_filled, 'Click to Chat for WhatsApp', "multi_$agent" . "_pre_filled" );

                        // multiple agent - pre_filled variables
                        $pre_filled = str_replace( array('{url}', '{title}', '{site}' ),  array( $page_url, $post_title, HT_CTC_BLOG_NAME ), $pre_filled );
                        $pre_filled = str_replace( array('{product}', '{{price}}', '{price}', '{regular_price}', '{sku}' ),  array( $product_name, $price_formatted, $price, $regular_price, $sku ), $pre_filled );


                        $agent_image_id = ( isset( $agent_options['agent_image_id']) ) ? $agent_options['agent_image_id'] : '';

                        $image_url = '';
                        if ('' !== $agent_image_id) {

                            $image_src = wp_get_attachment_image_src( $agent_image_id, 'medium' );

                            if ( false !== $image_src && isset( $image_src[0] ) ) {
                                $image_url = $image_src[0];
                            }
                            
                        }


                        $multi = [];
                        $i = 1;

                        // timing - set/away
                        $multi['timings'] = ( isset( $agent_options['timings']) ) ? esc_attr($agent_options['timings']) : 'always';

                        // if time is set then send timings for enable days.
                        if ('set' == $multi['timings']) {
                            foreach ($days as $day ) {

                                $day_times = $day.'_times';

                                $d = "d$i";
                                $st = $day.'_st';
                                $et = $day.'_et';
                                $d_st = 'd'.$i.'_st';
                                $d_et = 'd'.$i.'_et';

                                $n = (isset($agent_options[$day])) ? esc_attr($agent_options[$day]) : '';
                                $times = (isset($agent_options[$day_times])) ? ($agent_options[$day_times]) : '';


                                if ('' !== $n) {
                                    
                                    $c = $i-1;
                                    if ( is_array($times) ) {
                                        
                                        foreach ($times as $time_set ) {
                                            
                                            $n1 = (isset($time_set['st'])) ? esc_attr($time_set['st']) : '';
                                            $n2 = (isset($time_set['et'])) ? esc_attr($time_set['et']) : '';

                                            $n1 = ('' == $n1) ? '0: 00' : $n1;
                                            $n2 = ('' == $n2) ? '24: 00' : $n2;

                                            // if end time is 00:00 then set it to 24:00
                                            $n2 = ('0: 00' == $n2) ? '24: 00' : $n2;

                                            $n1_explode = explode(': ', $n1);
                                            $n1_h = (isset($n1_explode[0])) ? $n1_explode[0] : 0;
                                            $n1_m = (isset($n1_explode[1])) ? $n1_explode[1] : 00;
                                            
                                            $n2_explode = explode(': ', $n2);
                                            $n2_h = (isset($n2_explode[0])) ? $n2_explode[0] : 24;
                                            $n2_m = (isset($n2_explode[1])) ? $n2_explode[1] : 00;

                                            if ( is_numeric($n1_h) && is_numeric($n1_m) && is_numeric($n2_h) && is_numeric($n2_m) ) {
                                                $m1 = ((($c*24)+$n1_h)*60)+$n1_m;
                                                $m2 = ((($c*24)+$n2_h)*60)+$n2_m;

                                                $multi['time_sets'][] = ['stm' => $m1, 'etm' => $m2];
                                            }

                                        }

                                    } else {
                                        // day is checked - but no time sets is added. so 24 hours online.
                                        $m1 = ((($c*24)+0)*60);
                                        $m2 = ((($c*24)+24)*60);

                                        $multi['time_sets'][] = ['stm' => $m1, 'etm' => $m2];
                                    }

                                }

                                $i++;
                            }
                        }


                        $ht_ctc_multi = htmlspecialchars(json_encode($multi), ENT_QUOTES, 'UTF-8');
                        // localize script
                        wp_localize_script( 'ht_ctc_app_js', "ht_ctc_multi_$agent", $multi );

                        ?>
                        <div class="ht_ctc_multi_agent <?= "ht_ctc_multi_$agent" ?>" data-key="<?= "ht_ctc_multi_$agent" ?>" style="<?= $send_agent_css ?>">
                            <div class="ht_ctc_chat_greetings_multi_agent ctc-analytics">
                            <?php
                            if ( is_file( $g_cta_multi_agent_path ) && '' !== $number ) {
                                include $g_cta_multi_agent_path;
                                $agent_loop_count++;
                            }
                            ?>
                            </div>
                            <span class="ht_ctc_multi_agent_data <?= "ht_ctc_multi_{$agent}_data" ?>" 
                                data-settings="<?= $ht_ctc_multi ?>" 
                            ></span>
                        </div>
                        <?php
                    $key_gen++;
                    }
                } else {
                    ?>
                    <div class="no_agent" style="text-align:center; padding:20px; background-color:#000000; color:#ffffff;">No Agent is Created! <br> <span class="ctc_chat" style="cursor:pointer;"><span style="text-decoration:underline;">WhatsApp</span> <span style="font-size:0.8rem;">(Default Number)</span></span></div>
                    <?php
                }

                $multi_agent = [];
                $multi_agent['agent_offline'] = ( isset( $g2_pro_options['agent_offline']) ) ? esc_attr($g2_pro_options['agent_offline']) : 'always';

                // multilingual..
                $multi_agent['ctc_minute'] = apply_filters( 'wpml_translate_single_string', "Minute", 'Click to Chat for WhatsApp', 'ctc_minute' );
                $multi_agent['ctc_minutes'] = apply_filters( 'wpml_translate_single_string', "Minutes", 'Click to Chat for WhatsApp', 'ctc_minutes' );
                $multi_agent['ctc_hour'] = apply_filters( 'wpml_translate_single_string', "Hour", 'Click to Chat for WhatsApp', 'ctc_hour' );
                $multi_agent['ctc_hours'] = apply_filters( 'wpml_translate_single_string', "Hours", 'Click to Chat for WhatsApp', 'ctc_hours' );
                $multi_agent['ctc_day'] = apply_filters( 'wpml_translate_single_string', "Day", 'Click to Chat for WhatsApp', 'ctc_day' );
                $multi_agent['ctc_days'] = apply_filters( 'wpml_translate_single_string', "Days", 'Click to Chat for WhatsApp', 'ctc_days' );

                $ht_ctc_multi = htmlspecialchars(json_encode($multi_agent), ENT_QUOTES, 'UTF-8');
                // localize script
                wp_localize_script( 'ht_ctc_app_js', "ht_ctc_multi_agent_main", $multi_agent );
                ?>
                <span class="ht_ctc_multi_agent_main_data" 
                    data-settings="<?= $ht_ctc_multi ?>" 
                ></span>
            </div>
        </div>
    </div>

    <?php
        // main content display at main box if initial display is content..
    if ('content' == $initial_display) {
    ?>
    <div class="ctc_g_sentbutton" style="<?= $send_css ?>">
        <?php
        if ( isset($ht_ctc_greetings['is_opt_in']) && '' !== $ht_ctc_greetings['is_opt_in'] && is_file( $g_optin_path ) ) {
            $opt_in_id = 'ctc_opt_multi';
            include $g_optin_path;
        }
        ?>
        <div class="ht_ctc_chat_greetings_box_link_multi ctc-analytics">
        <?php
        if ( is_file( $g_cta_path ) ) {
            include $g_cta_path;
        }
        ?>
        </div>
    </div>
    <?php
    }
    ?>
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