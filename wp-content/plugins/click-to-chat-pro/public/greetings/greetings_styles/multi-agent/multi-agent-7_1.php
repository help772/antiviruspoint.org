<?php
/**
 * Greetings call to action - style - 7 Extend
 * 
 * <input class="ht_ctc_chat_greetings_box_link" type="submit" style="" value="<?= $ht_ctc_greetings['call_to_action'] ?>">
 * 
 * class name: ctc_chat - as custom element
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$s7_1_options = get_option( 'ht_ctc_s7_1' );
$s7_1_options = apply_filters( 'ht_ctc_fh_s7_1_options', $s7_1_options );

$s7_icon_size = (isset( $s7_1_options['s7_icon_size'])) ? esc_attr( $s7_1_options['s7_icon_size'] ) : '';
$s7_icon_color = (isset( $s7_1_options['s7_icon_color'])) ? esc_attr( $s7_1_options['s7_icon_color'] ) : '';
$s7_icon_color_hover = (isset( $s7_1_options['s7_icon_color_hover'])) ? esc_attr( $s7_1_options['s7_icon_color_hover'] ) : '';
$s7_bgcolor = (isset( $s7_1_options['s7_bgcolor'])) ? esc_attr( $s7_1_options['s7_bgcolor'] ) : '';
$s7_bgcolor_hover = (isset( $s7_1_options['s7_bgcolor_hover'])) ? esc_attr( $s7_1_options['s7_bgcolor_hover'] ) : '';
$s7_border_size = (isset( $s7_1_options['s7_border_size'])) ? esc_attr( $s7_1_options['s7_border_size'] ) : '';

// Call to action 
$s7_1_cta_font_size = (isset( $s7_1_options['cta_font_size'])) ? esc_attr( $s7_1_options['cta_font_size'] ) : '';

$s7_1_cta_font_size = ('' !== $s7_1_cta_font_size) ? "font-size: $s7_1_cta_font_size" : "";

// Call to action - Order
$s7_cta_order = "1";
// $s7_show_cta_padding_css = "padding:5px;";
$s7_show_cta_padding_css = "padding:5px 0;";

if ( isset($side_2) && 'right' == $side_2) {
    // if side_2 is right then cta is left
    $s7_cta_order = "0";
}

$rtl_css = "";
if ( function_exists('is_rtl') && is_rtl() ) {
    $rtl_css = "flex-direction:row-reverse;";
}

// $s7_n1_styles = "display:flex;justify-content:center;align-items:center;$rtl_css ";
$s7_n1_styles = "display:flex;align-items:center;$rtl_css ";
$s7_cta_css = "$s7_1_cta_font_size; ";
$s7_icon_padding_css = "";
$s7_cta_class = "ht-ctc-cta ";
$s7_hover_styles = "";

$s7_n1_styles .= "$s7_show_cta_padding_css background-color:$s7_bgcolor;border-radius:25px; cursor: pointer;";
$s7_cta_css .= "padding:1px 0px; color:$s7_icon_color; border-radius:10px; margin:0 10px;";
$s7_icon_padding_css .= "";
$s7_hover_styles = ".ht-ctc .agent_g_ctc_s_7_1:hover{background-color:$s7_bgcolor_hover !important;}.ht-ctc .agent_g_ctc_s_7_1:hover .agent_g_ctc_s_7_1_cta{color:$s7_icon_color_hover !important;}";


$type = 'g_cta';
// svg values
$ht_ctc_svg_css = "pointer-events:none; display:block; height:18px; width:18px;";
$s7_svg_attrs = array(
    'color' => "$s7_icon_color",
    'icon_size' => "18px",
    'type' => "greetings_chat",
    'ht_ctc_svg_css' => "$ht_ctc_svg_css",
);

// from greetings-pro-2.php
$number = (isset($number)) ? $number : '';
$description = (isset($description)) ? $description : '';

$title = (isset($title)) ? $title : '';
$title = ('' !== $title) ? $title : $ht_ctc_greetings['call_to_action'];

$pre_filled = (isset($pre_filled)) ? $pre_filled : '';
$data_pre_filled = ('' !== $pre_filled) ? "data-pre_filled='$pre_filled'" : "";

$image_url = (isset($image_url)) ? $image_url : '';


$img_size = '48px';
$type = "g_multi_agent_$agent_loop_count";
$ht_ctc_svg_css = "bottom:0px; right:-2px; pointer-events:none; display: block; height:$img_size; width:$img_size;";

include_once HT_CTC_PRO_PLUGIN_DIR .'inc/assets/img/ht-ctc-svg-images.php';
include_once HT_CTC_PLUGIN_DIR .'new/inc/assets/img/ht-ctc-svg-images.php';


/**
 * Styles
 * transform:scale(1.01);
 * opacity: 0.9;
 * box-shadow: 0px 0px 10px rgba(0,0,0,.2);
 */
?>
<style id="ht-ctc-s7_1">
<?= $s7_hover_styles ?>
.agent_style_1:hover {
    transform:translateY(-1px);
}
</style>

<div class="ctc-analytics ctc_chat agent_g_ctc_s_7_1 agent_style_1 agent_style" style="display:flex; align-items:center; background-color: #25d366; border-radius: 25px; cursor:pointer; box-shadow: 0px 0px 7px rgba(0,0,0,.2);" data-number="<?= $number ?>" <?= $data_pre_filled ?>>
    <div class="image" style="margin-left: -1px;">
        <?php
        if ('' !== $image_url) {
            ?>
            <img style="width:48px; height:48px; border-radius:50%;" src="<?= $image_url ?>" alt="<?= $title ?>">
            <?php
        } else {
            echo ht_ctc_round( $img_size, $type, $ht_ctc_svg_css );
        }
        ?>
    </div>
    <div class="calltoaction"  style="order: <?= $s7_cta_order ?>; line-height:1; padding: 0 10px;">
        <p class="line1" style="line-height:1; font-size: 14px; font-weight: 500; color: #ffffff;"><?= $title ?></p>
        <p class="line2" style="line-height:1; font-size: 10px; color: #ffffff;"><?= $description ?></p>
    </div>
    <div style="margin-left:auto; order: 2; padding-right: 18px; display: flex; align-items: center;">
        <?php
        if ('' !== $image_url) {
            echo ht_ctc_singlecolor( $s7_svg_attrs );
        }
        ?>
        <!-- <span style="font-size: 12px; margin-left: 4px;">online</span> -->
    </div>
</div>