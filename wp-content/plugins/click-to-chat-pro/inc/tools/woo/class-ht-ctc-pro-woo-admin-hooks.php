<?php
/**
 * WooCommerce related hooks
 *  position type
 * 
 * @package Click to Chat PRO
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HT_CTC_PRO_Woo_Admin_Hooks' ) ) :

class HT_CTC_PRO_Woo_Admin_Hooks {

    public function __construct() {
        $this->hooks();
    }

    /**
     * Hooks
     */
    public function hooks() {

        // woo after single product settings
        add_action( 'ht_ctc_ah_admin_after_woo_overwrite_single_settings', [$this, 'after_woo_overwrite_single_settings'] );
        add_action( 'ht_ctc_ah_admin_after_woo_settings', [$this, 'after_woo_settings'] );

    }


    // after_woo_overwrite_single_settings
    function after_woo_overwrite_single_settings() {

        $woo_options = get_option('ht_ctc_woo_options');

        $woo_single_header_content = ( isset( $woo_options['woo_single_header_content']) ) ? $woo_options['woo_single_header_content'] : '';
        $woo_single_main_content = ( isset( $woo_options['woo_single_main_content']) ) ? $woo_options['woo_single_main_content'] : '';
        $woo_single_bottom_content = ( isset( $woo_options['woo_single_bottom_content']) ) ? $woo_options['woo_single_bottom_content'] : '';
        $woo_single_g_call_to_action = ( isset( $woo_options['woo_single_g_call_to_action']) ) ? $woo_options['woo_single_g_call_to_action'] : '';
        $greetings_template = ( isset( $woo_options['woo_single_greetings_template']) ) ? esc_attr( $woo_options['woo_single_greetings_template'] ) : '';

        $g_templates = [
            'no' => '-- No Greetings Dialog --',
            'greetings-1' => 'Greetings-1 - Customizable Design',
            'greetings-2' => 'Greetings-2 - Content Specific',
            'greetings-pro-1' => 'Greetings - Form',
            'greetings-pro-2' => 'Multi Agent'
        ];

        $allowed_html = wp_kses_allowed_html( 'post' );

        if ( '' !== $woo_single_header_content ) {
            $woo_single_header_content = html_entity_decode(wp_kses($woo_single_header_content, $allowed_html));
        }

        if ( '' !== $woo_single_main_content ) {
            $woo_single_main_content = html_entity_decode(wp_kses($woo_single_main_content, $allowed_html));
        }

        if ( '' !== $woo_single_bottom_content ) {
            $woo_single_bottom_content = html_entity_decode(wp_kses($woo_single_bottom_content, $allowed_html));
        }


        if ( ! function_exists( 'ctc_meta_tinymce_mce_buttons_2' ) ) {
            function ctc_meta_tinymce_mce_buttons_2( $buttons ) {

                $key = array_search( 'forecolor', $buttons );
                
                // add after forecolor
                if ( $key !== false && is_int( $key ) ) {
                    array_splice( $buttons, $key+1, 0, 'backcolor' );
                }

                // add at first
                array_unshift( $buttons, 'fontselect' );
                array_unshift( $buttons, 'fontsizeselect' );

                return $buttons;
            }
        }
        add_filter( 'mce_buttons_2', 'ctc_meta_tinymce_mce_buttons_2' );

        $args = [
            'textarea_rows' => 10,
            'editor_height' => 250,
            'drag_drop_upload' => true,
            'tinymce'       => array(
                'textarea_rows'=> 10,
                'fontsize_formats' => "6px 8px 10px 12px 13px 14px 15px 16px 18px 20px 24px 28px 32px 36px",
            )
        ];

        ?>
        <p class="description">Overwrite Greetings Settings for WooCommerce single product pages</p>
        <!-- template -->
        <div class="row" style="margin:30px 0px 0px 0px;">
            <p class="description ht_ctc_subtitle"><?php _e( 'Greetings Template', 'click-to-chat-for-whatsapp' ); ?></p>
            <div class="input-field col s12">
                <select name="ht_ctc_woo_options[woo_single_greetings_template]" class="woo_single_select_style select_greetings_template">
                    <option value="" <?= $greetings_template == '' ? 'SELECTED' : ''; ?> >-- Default --</option>
                    <?php
                    foreach ($g_templates as $k => $v) {
                    ?>
                    <option value="<?= $k ?>" <?= $greetings_template == $k ? 'SELECTED' : ''; ?> ><?= $v ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <p class="description ht_ctc_subtitle"><?php _e( 'Header Content', 'click-to-chat-for-whatsapp' ); ?></p>
        <div class="woo_header_content ctc_wp_editor">
        <?php
        $content   = $woo_single_header_content;
        $editor_id = 'header_content';
        $args['textarea_name'] = "ht_ctc_woo_options[woo_single_header_content]";
        wp_editor( $content, $editor_id, $args );
        ?>
        </div>

        <p class="description ht_ctc_subtitle" style="margin-top:30px;"><?php _e( 'Main Content', 'click-to-chat-for-whatsapp' ); ?></p>
        <div class="woo_header_content ctc_wp_editor">
        <?php
        $content   = $woo_single_main_content;
        $editor_id = 'main_content';
        $args['textarea_name'] = "ht_ctc_woo_options[woo_single_main_content]";
        wp_editor( $content, $editor_id, $args );
        ?>
        <p class="description">Variables: {product}, {{price}}, {price}, {regular_price}, {sku}, {site}, {title}, {url}</p>
        </div>

        <p class="description ht_ctc_subtitle" style="margin-top:30px;"><?php _e( 'Bottom Content', 'click-to-chat-for-whatsapp' ); ?></p>
        <div class="woo_header_content ctc_wp_editor">
        <?php
        $content   = $woo_single_bottom_content;
        $editor_id = 'bottom_content';
        $args['textarea_name'] = "ht_ctc_woo_options[woo_single_bottom_content]";
        wp_editor( $content, $editor_id, $args );
        ?>

        <!-- Call to Action -->
        <div class="row" style="margin-top:30px;">
            <div class="input-field col s12 md_tab">
                <input name="ht_ctc_woo_options[woo_single_g_call_to_action]" value="<?= $woo_single_g_call_to_action ?>" id="woo_single_g_call_to_action" type="text" class="input-margin" placeholder="Buy {product} for {price}">
                <label for="woo_single_g_call_to_action"><?php _e( 'Greetings Call to Action', 'click-to-chat-for-whatsapp' ); ?></label>
            </div>
        </div>

        </div>
        <?php

    }
    
    // woo after single product settings
    function after_woo_settings() {

        $woo_options = get_option('ht_ctc_woo_options');
        $chat = get_option('ht_ctc_chat_options');
        $dbrow = 'ht_ctc_woo_options';
        
        $woo_apply_business_hours = ( isset( $woo_options['woo_apply_business_hours']) ) ? esc_attr( $woo_options['woo_apply_business_hours'] ) : '';
        ?>

        <!-- Apply Business hour settings -->
        <div class="row">
            <div class="col s6" style="padding-top: 14px;">
                <p><?php _e( 'Business hours settings', 'click-to-chat-for-whatsapp' ); ?>:</p>
            </div>
            <div class="input-field col s6">
                <label>
                    <input name="<?= $dbrow; ?>[woo_apply_business_hours]" type="checkbox" value="1" <?php checked( $woo_apply_business_hours, 1 ); ?> id="woo_apply_business_hours" />
                    <span><?php _e( "Apply Business hours settings", 'click-to-chat-for-whatsapp' ); ?></span>
                    <p class="description">Apply Business hour settings to the above WooCommerce settings - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/business-hours-for-woocommerce-products/">more info</a></p>
                </label>
            </div>
        </div>

        <?php

    }


}

new HT_CTC_PRO_Woo_Admin_Hooks();

endif; // END class_exists check