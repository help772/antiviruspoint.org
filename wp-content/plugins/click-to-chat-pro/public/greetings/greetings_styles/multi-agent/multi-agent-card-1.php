<?php
/**
 * Multi-agent card 1 template.
 * 
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit;


// from greetings-pro-2.php
$number = (isset($number)) ? $number : '';
$description = (isset($description)) ? $description : '';

$title = (isset($title)) ? $title : '';

if ('' == $title && '' == $description) {
    $title = $ht_ctc_greetings['call_to_action'];
}

$pre_filled = (isset($pre_filled)) ? $pre_filled : '';
$data_pre_filled = ('' !== $pre_filled) ? "data-pre_filled='$pre_filled'" : "";

$image_url = (isset($image_url)) ? $image_url : '';

$img_size = '54px';
$type = 'g_multi_agent';

//  position:absolute; right:0; is removed to replace the margin-right/left: auto 
$g_agent_tags_css = "display:flex; align-items:center; font-size:12px; padding:0 15px; color:#25D366;";

// @var $rtl_page from parent page
if ( isset($rtl_page) &&'yes' == $rtl_page ) {
    $g_agent_tags_css .= " margin-right:auto;";
} else {
    $g_agent_tags_css .= " margin-left:auto;"; 
}

$ht_ctc_svg_css = "pointer-events:none; display: block; height:$img_size; width:$img_size;";

$plain_svg_css = "";
$plain_icon = array(
    'color' => "#25D366",
    'icon_size' => "16px",
    'type' => "greetings_multi",
    'ht_ctc_svg_css' => "$plain_svg_css",
);

include_once HT_CTC_PLUGIN_DIR .'new/inc/assets/img/ht-ctc-svg-images.php';
// include_once HT_CTC_PRO_PLUGIN_DIR .'inc/assets/img/ht-ctc-svg-images.php';

// styles added at greetings-pro-2.php

/**
 * ctc_chat - if optin is enabled and initial_display is agents, then add class name add_ctc_chat and at js based on optin add ctc_chat
 */
$ctc_chat = 'ctc_chat';
if (isset($initial_display) && 'agents' == $initial_display && isset($ht_ctc_greetings['is_opt_in']) && '' !== $ht_ctc_greetings['is_opt_in']) {
    $ctc_chat = 'add_ctc_chat multi_optin_direct_agent';
}


?>
<div class="<?= $ctc_chat ?> agent_g_ctc_card_1 agent_style_card_1 agent_style" data-number="<?= $number ?>" <?= $data_pre_filled ?> style="">
    <div class="g_multi_box" style=" display:flex; cursor:pointer; padding: 5px; background-color:#ffffff; margin:0; align-items:center; border-radius: 5px;">
        <div class="ctc_g_agent_image" style="display:flex;">
            <?php
            if ('' !== $image_url) {
                ?>
                <img style="width:<?= $img_size ?>; height:<?= $img_size ?>; border-radius:50%;" src="<?= $image_url ?>" alt="<?= $title ?>">
                <?php
            } else {
                echo ht_ctc_style_3_svg( $img_size, $type, $ht_ctc_svg_css );
            }
            ?>
        </div>
        <div class="ctc_g_agent_content" style="padding: 0 10px;">
            <p class="ctc_g_title" style="font-size:16px; font-weight:500; line-height:1.3; color:#232b2b;"><?= $title ?></p>
            <p class="ctc_g_description" style="font-size: 11px; line-height:1.3; color:#000000;"><?= $description ?></p>
        </div>
        <div class="ctc_g_agent_tags" style="<?= $g_agent_tags_css ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
            </svg>
            <span class="ctc_agent_next_time" style=" display:none; padding:0px 4px;"></span>
        </div>
    </div>
</div>