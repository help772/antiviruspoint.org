<?php
/**
 * hooks
 *  position type
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HT_CTC_PRO_Admin_Hooks' ) ) :

class HT_CTC_PRO_Admin_Hooks {

    public function __construct() {
        $this->hooks();
    }
    
    /**
     * Hooks
     */
    public function hooks() {

        $this->admin_notice();

        $options = get_option('ht_ctc_chat_options');

        // position type
        add_filter( 'ht_ctc_fh_position_type_values', [$this, 'add_position_type_values'] );

        // editor values
        add_filter( 'ht_ctc_fh_greetings_setting_editor_values', [$this, 'editor_values'] );

        /**
         * woo places
         * 
         * valid upto main version - 3.9.11
         * removed this hook in 3.9.12, added this feature directly in main version itself.
         * 
         * safelty remove this hook and its related function in November 2022. expected that users will update both versions. 
         * (as now: no problem if both versions are not updated, issue will be if one plugin updated and other not)
         * 
         */
        add_filter( 'ht_ctc_fh_admin_woo_places', [$this, 'woo_places'] );

        add_action( 'ht_ctc_ah_admin_chat_bottom_meta_box', array($this, 'chat_meta_box'), 10, 1 );
        // add_action( 'ht_ctc_ah_admin_chat_meta_box_after_number', array($this, 'chat_meta_box_after_number'), 10, 1 );

        add_filter( 'ht_ctc_ah_admin_chat_number', array($this, 'number') );
        add_filter( 'ht_ctc_ah_admin_chat_after_showhide', array($this, 'after_showhide') );
        add_filter( 'ht_ctc_ah_admin_chat_more_options', array($this, 'bh') );

        add_action( 'ht_ctc_ah_admin_header_status_badge', array($this, 'header_badge') );

        // scripts
        // add_action( 'ht_ctc_ah_admin_scripts_end', array($this, 'admin_scripts') );
        add_action( 'ht_ctc_ah_admin_scripts_start', array($this, 'admin_scripts') );

        // wp - admin enqueue scipts
        add_action('admin_enqueue_scripts', array( $this, 'admin_register_scripts' ), 20 );


        // fb conversation api
        // add_action( 'ht_ctc_ah_admin_after_fb_pixel', [$this, 'fb_capi'] );

        // google ads
        add_action( 'ht_ctc_ah_admin_google_ads', [$this, 'google_ads'] );


        // uses from front end. as ajax call, action declared here. (is_admin)
        add_action( 'wp_ajax_ctc_pro_is_user_logged_in', [$this, 'isUserLoggedIn'] );
        add_action( 'wp_ajax_nopriv_ctc_pro_is_user_logged_in', [$this, 'isUserLoggedIn'] );

        // updates
        // add_action( 'wp_ajax_ctc_pro_updates', [$this, 'plugin_updates'] );

        // ajax call for function update_r_sqx - for sqx
        $r_num_type = ( isset($options['r_num_type']) ) ? esc_attr( $options['r_num_type'] ) : '';
        if ( 'sequential' === $r_num_type ) {
            add_action( 'wp_ajax_ctc_pro_update_r_sqx', [$this, 'update_r_sqx'] );
            add_action( 'wp_ajax_nopriv_ctc_pro_update_r_sqx', [$this, 'update_r_sqx'] );

            // ajax call for function get_r_sqx - for sqx
            add_action( 'wp_ajax_ctc_pro_get_r_sqx', [$this, 'get_r_sqx'] );
            add_action( 'wp_ajax_nopriv_ctc_pro_get_r_sqx', [$this, 'get_r_sqx']  );
        }

        // greetings
        add_filter( 'ht_ctc_fh_greetings_register', array($this, 'greetings_register') );
        add_filter( 'ht_ctc_fh_greetings_templates', array($this, 'greetings_templates') );
        add_filter( 'ht_ctc_fh_greetings_setting_values', array($this, 'greetings_settings') );

        add_filter( 'ht_ctc_fh_greetings_setting_meta_editor', array($this, 'greetings_meta_editor') );

        // Admin - demo - add greetings templates
        // add_filter( 'ht_ctc_fh_admin_demo_greetings_templates', array($this, 'demo_greetings_templates') );


        add_filter( 'ht_ctc_fh_url_structure_d_list', [$this, 'url_structure_d_list'] );
        add_filter( 'ht_ctc_fh_url_structure_m_list', [$this, 'url_structure_m_list'] );

        add_action( 'ht_ctc_ah_url_structure_desktop', [$this, 'custom_url_desktop'] );
        add_action( 'ht_ctc_ah_url_structure_mobile', [$this, 'custom_url_mobile'] );
        
        $g1_pro_options = get_option( 'ht_ctc_greetings_pro_1' );
        $raw_email = ( isset($g1_pro_options['email']) ) ? esc_attr($g1_pro_options['email']) : '';

        // greetings form data hanlde - ajax hook works only if valid email is added.. 
        // && is_email($email)
        if ( '' !== $raw_email ) {

            $email_list = explode( ',', $raw_email );

            // if is array and not empty
            if ( is_array( $email_list ) && ! empty( $email_list ) ) {
                // trim each email
                $email_list = array_map( 'trim', $email_list );

                // filter only valid emails
                $valid_emails = array_filter( $email_list, 'is_email' );

                // if valid emails
                if ( ! empty( $valid_emails ) ) {
                    // add action for greetings form data
                    add_action( 'wp_ajax_ctc_pro_greetings_form', [$this, 'greetings_form_data'] );
                    add_action( 'wp_ajax_nopriv_ctc_pro_greetings_form', [$this, 'greetings_form_data'] );
                }
            }

        }

        // greetings localization
        add_action( 'ht_ctc_ah_admin_localization_greetings_page', array($this, 'localization_greetings_page'), 10, 1 );

        // multi agent localization (hook: after multi agent greetings updates)
        // it mightbe better to change this hook if this values are need to user in other places..
        add_action( 'update_option_ht_ctc_greetings_pro_2', array($this, 'localization_strings') );

    }


    // header_badge
    function header_badge() {

        $options = get_option('ht_ctc_greetings_options');

        $g_header_offline_status_color = isset( $options['g_header_offline_status_color'] ) ? esc_attr( $options['g_header_offline_status_color'] ) : '#06e376';

        ?>
        <div class="input-field col s6" style="margin-bottom:2px;">
            <p calss="description" style="margin-bottom: 5px;">Offline Status Badge Color</p>
            <input class="ht-ctc-color g_header_offline_status_color" name="ht_ctc_greetings_options[g_header_offline_status_color]" data-default-color="#06e376" value="<?= $g_header_offline_status_color ?>" type="text">
        </div>
        <?php
    }

    // Greetings form data handle
    /**
     * AJAX handler for submitting the Greetings Form in Click to Chat Pro.
     *
     * This function:
     * - Verifies nonce for security.
     * - Retrieves configured form options from plugin settings.
     * - Parses and sanitizes form data submitted by the user.
     * - Constructs and sends an email to the configured admin email.
     * - Sends a JSON success response back to the frontend.
     *
     * @return void
     */
    function greetings_form_data() {
        // Security: Check for valid nonce before processing
        check_ajax_referer( 'ht_ctc_pro_greetings_nonce', 'nonce' );

        // Get plugin options for Greetings Form (from `ht_ctc_greetings_pro_1`)
        $g1_pro_options = get_option( 'ht_ctc_greetings_pro_1' );

        // Get and sanitize admin email from options
        // sanitize_email
        $raw_email = ( isset($g1_pro_options['email']) ) ? esc_attr($g1_pro_options['email']) : '';

        // Proceed only if admin email is not empty
        //  && is_email($email)
        if ( '' !== $raw_email ) {

            // Sanitize submitted form data from POST request
            // Expects data in the format: [{name: "field_1", value: "Some Name"}, ...]
            $form_data = ( $_POST && isset($_POST['form_data']) ) ? map_deep( $_POST['form_data'], 'sanitize_text_field' ) : '';
            // $form_data = ($_POST && $_POST['form_data']) ? map_deep( $_POST['form_data'], 'esc_attr' ) : '';

            // Get admin-configured field references (field_1, field_4, etc. - admin filed names might not be in order)
            $admin_fields = ( isset($g1_pro_options['fields']) ) ? array_map( 'esc_attr', $g1_pro_options['fields'] ) : '';

            // Get email subject from options or fallback to default
            $subject = ( isset($g1_pro_options['email_subject']) ) ? esc_attr($g1_pro_options['email_subject']) : '';
            if ( '' == $subject ) {
                $subject = "Click to Chat - Greetings Form";
            }
            
            // Initialize variables for email message and reply-to address
            $message = '';
            $reply_email = '';
            $i = 0;

            // Loop through form data and build the email message
            foreach ( $form_data as $field ) {

                if ( isset( $admin_fields[$i] )) {
                    
                    // Get field name (field_1, field_4, etc.)
                    $s1 = $admin_fields[$i];

                    // Get type of field from admin settings
                    $type = $g1_pro_options[$s1]['type'];

                    // Get display name of field from admin settings
                    $name = $g1_pro_options[$s1]['name'];

                    // Get field value from sent data
                    $value = esc_attr($field['value']);

                    // If it's an email field and the value is valid, use it as reply-to
                    if ( 'email' === $type && is_email( $value ) ) {
                        $reply_email = $value;
                    }

                    // Add field label and value to message body
                    $message .= "$name: $value, <br>";

                    // Replace {field_1}, {field_2}, etc., in subject line with user input values
                    $subject = str_replace( '{field_' . ($i + 1) . '}', $value, $subject );
                }
                $i++;
            }

            // Add additional metadata to email (like date and referring page)
            if ( isset($_SERVER) ) {
                $message .= "<br><br><br>";

                // Current date & time in WP timezone
                $date = date("F j, Y, g:i a", current_time( 'timestamp', 0 ));
                $message .= "Date (based on your WordPress Timezone): $date <br>";

                // Page where the form was submitted (HTTP referrer)
                if ( $_SERVER['HTTP_REFERER'] ) {
                    $v = esc_attr($_SERVER['HTTP_REFERER']);

                    // Decode URL if it has percent-encoded characters (if special chars)
                    if ( preg_match('/%[a-z][0-9]%/i', $v) ) {
                        $v = urldecode($v);
                    }

                    $message .= "Page: $v <br>";
                }

                // if ($_SERVER['REMOTE_ADDR']) {
                //     $v = $_SERVER['REMOTE_ADDR'];
                //     $message .= "IP: $v <br>";
                // }

                // if ($_SERVER['HTTP_USER_AGENT']) {
                //     $v = $_SERVER['HTTP_USER_AGENT'];
                //     $message .= "User Agent: $v <br>";
                // }
            }
            
            // HTML headers for email
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
            );

            // Add reply-to header if a user email was captured
            if ( '' !== $reply_email ) {
                $headers[] = 'Reply-To: ' . $reply_email;
            }

            // html entity decode 
            $subject = html_entity_decode($subject);
            
            // email_list - multiple emails
            $email_list = explode( ',', $raw_email );

            // explode give array, but still check if array and not empty to use array_map
            if ( is_array( $email_list ) && ! empty( $email_list ) ) {
                
                // trim each email
                $email_list = array_map( 'trim', $email_list );

                // check if email is valid, loop through emails
                foreach ( $email_list as $e ) {
                    if ( is_email( $e ) ) {
                        
                        $to = sanitize_email($e);

                        // Send the email to admin with form data
                        wp_mail( $to, stripslashes($subject), stripslashes($message), $headers );

                    }
                }
            }

            
        }

        // Send a success response
        $m = array(
            'works' => 'thanks',
        );
        wp_send_json_success( $m );

        // Properly terminate the AJAX call (ends early, this wont runs)
        wp_die();
    }

    // greetings localization
    function localization_greetings_page( $new_input ) {

        foreach ($new_input as $key => $value) {

            // greetings form filed - localization
            if ( false !== strpos( $key, 'field_' ) && false === strpos( $key, 'field_count' ) ) {

                $name = isset($value['name']) ? $value['name'] : '';
                $placeholder = isset($value['placeholder']) ? $value['placeholder'] : '';
                $selectvalues = isset($value['selectvalues']) ? $value['selectvalues'] : '';

                do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "greetings_form_$key" . "_name", $name );
                do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "greetings_form_$key" . "_placeholder", $placeholder );

                if ( !empty($selectvalues) ) {
                    do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "greetings_form_$key" . "_selectvalues", $selectvalues );
                }
                
            }

            // grettings multi agent - localization
            if ( false !== strpos( $key, 'agent_' ) && false === strpos( $key, 'agent_count' ) && false === strpos( $key, 'agent_offline' ) ) {
                $number = isset($value['number']) ? $value['number'] : '';
                $title = isset($value['title']) ? $value['title'] : '';
                $description = isset($value['description']) ? $value['description'] : '';
                $pre_filled = isset($value['pre_filled']) ? $value['pre_filled'] : '';

                // multi_agent_1_number    key: agent_1 ...
                do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "multi_$key" . "_number", $number );
                do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "multi_$key" . "_title", $title );
                do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "multi_$key" . "_description", $description );
                do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "multi_$key" . "_pre_filled", $pre_filled );
            }

        }

    }

    /**
     * multilingual - general strings
     * @uses greetings multi agent feature..
     */
    function localization_strings() {

        $values = [
            'ctc_hour' => 'Hour',
            'ctc_hours' => 'Hours',
            'ctc_minute' => 'Minute',
            'ctc_minutes' => 'Minutes',
            'ctc_day' => 'Day',
            'ctc_days' => 'Days',
        ];

        foreach ($values as $key => $value) {
            do_action( 'wpml_register_single_string', 'Click to Chat for WhatsApp', "$key", $value );
        }

    }


    // is_user_logged_in
    function isUserLoggedIn() {
        $is_logged_in = ( is_user_logged_in() ) ? 'yes' : 'no';
        wp_send_json_success($is_logged_in);
    }

    
    /**
     * AJAX handler to update the sequential index `r_sqx` in the database.
     *
     * - Validates the nonce to ensure the request is secure.
     * - Sanitizes and stores the provided `r_sqx` value in the options table.
     * - Returns a JSON response indicating success or failure.
     *
     * @return void Outputs a JSON response and terminates the script.
     */
    public function update_r_sqx() {

        // Check nonce
        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'ht_ctc_nonce') ) {
            wp_send_json_error('Invalid or expired nonce.');
            return;
        }

        try {
            // Sanitize r_sqx by using esc_attr
            $r_sqx = isset($_POST['r_sqx']) ? sanitize_text_field($_POST['r_sqx']) : '';
            // $r_sqx = esc_attr($r_sqx);

            // Check if r_sqx is a number, convert to integer
            if ( !is_numeric($r_sqx) ) {
                $r_sqx = '0';
            }

            update_option('ht_ctc_r_sqx', $r_sqx);
            wp_send_json_success('r_sqx updated');

        } catch ( Throwable $e ) {
            wp_send_json_error('Exception in update_r_sqx');
        }

        wp_send_json_success('r_sqx');
        // Properly terminate the AJAX call (ends early at try or catch, this wont runs)

        wp_die();

    }

    /**
     * AJAX handler to fetch the current value of `r_sqx` from the database.
     *
     * - Validates the nonce to ensure the request is secure.
     * - Retrieves the `r_sqx` option from the database (defaults to 0 if not set).
     * - Returns the value as a JSON response.
     *
     * @return void Outputs a JSON response and terminates the script.
     */
    public function get_r_sqx() {

        // Check nonce for security
        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'ht_ctc_nonce') ) {
            wp_send_json_error('Invalid or expired nonce.');
            return;
        }

        try {

            $r_sqx = get_option('ht_ctc_r_sqx', '');
            $r_sqx = esc_attr($r_sqx);

            if ( !is_numeric($r_sqx) ) {
                $r_sqx = '0';
            }

            // plan not to touch db with get fn.
            // update_option('ht_ctc_r_sqx', $r_sqx);

            // Send JSON response and terminate properly
            wp_send_json_success($r_sqx);
            
        } catch ( Throwable $e ) {
            wp_send_json_error('Exception in get_r_sqx');
        }
        
        wp_send_json_success('get_r_sqx');

        // Properly terminate the AJAX call (ends early at try or catch, this wont runs)
        wp_die();
    }

    /**
     * fb conversion api - server side event
     */
    function fb_capi() {

        $options = get_option('ht_ctc_othersettings');
        $dbrow = 'ht_ctc_othersettings';

        $fb_conversion_checkbox = ( isset( $options['fb_conversion']) ) ? esc_attr( $options['fb_conversion'] ) : '';
        $pixel_id = isset($options['pixel_id']) ? esc_attr( $options['pixel_id'] ) : '';
        $token = isset($options['token']) ? esc_attr( $options['token'] ) : '';

        ?>
        <p>
            <label>
                <input name="<?= $dbrow; ?>[fb_conversion]" type="checkbox" value="1" <?php checked( $fb_conversion_checkbox, 1 ); ?> id="fb_conversion" />
                <span><?php _e( 'Facebook - Conversion Api - Server side Event', 'click-to-chat-for-whatsapp' ); ?></span>
            </label>
        </p>
        <p class="description"><strong>(Beta feature)</strong> please share your suggestions for improvements.</p>
        <p class="description">If you are using cache plugins, set cache lifespan to less than 8 hours</p>
        <br>

        <div class="row">
            <div class="input-field col s6">
                <p class="description"><?php _e( 'Facebook Pixel ID', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
            <div class="input-field col s6">
                <input name="<?= $dbrow; ?>[pixel_id]" value="<?= $pixel_id ?>" id="pixel_id" type="text" class="input-margin">
                <label for="pixel_id"><?php _e( 'Pixel ID', 'click-to-chat-for-whatsapp' ); ?></label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6">
                <p class="description"><?php _e( 'Access Token', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
            <div class="input-field col s6">
                <!-- single line input only - so santize as text works here -->
                <textarea name="<?= $dbrow; ?>[token]" id="token" class="materialize-textarea"><?= $token ?></textarea>
                <label for="token"><?php _e( 'Access Token', 'click-to-chat-for-whatsapp' ); ?></label>
            </div>
        </div>

        <br>
        <br>
        <?php
    }

    /**
     * google ads
     * conversion id, label
     * @since 1.4 - checkbox.. compatible with v3.8
     * @updated 2.5 added google ads params..
     */
    function google_ads() {

        $options = get_option('ht_ctc_othersettings');
        $dbrow = 'ht_ctc_othersettings';

        $ga_ads_checkbox = ( isset( $options['ga_ads']) ) ? esc_attr( $options['ga_ads'] ) : '';
        $gads_conversion_id = isset($options['gads_conversion_id']) ? esc_attr( $options['gads_conversion_id'] ) : '';
        $gads_conversion_label = isset($options['gads_conversion_label']) ? esc_attr( $options['gads_conversion_label'] ) : '';

        $g_ads_params = (isset($options['g_ads_params']) && is_array($options['g_ads_params']) ) ? array_map( 'esc_attr', $options['g_ads_params'] ) : '';

        // count of g_ads params.. used for adding new params.. always raises..
        $g_ads_param_order = ( isset( $options['g_ads_param_order']) ) ? esc_attr( $options['g_ads_param_order'] ) : 1; 
        // $key_gen = 1;
        ?>

        <p>
            <label class="ctc_checkbox_label">
                <input name="<?= $dbrow; ?>[ga_ads]" type="checkbox" value="1" <?php checked( $ga_ads_checkbox, 1 ); ?> id="ga_ads" />
                <span><?php _e( 'Google Ads Conversion', 'click-to-chat-for-whatsapp' ); ?></span>
            </label>
        </p>

        <div class="ctc_g_ads_values ctc_init_display_none">
            <div class="row ctc_side_by_side">
                <div class="input-field col s6">
                    <p class="description"><?php _e( 'Conversion ID', 'click-to-chat-for-whatsapp' ); ?></p>
                </div>
                <div class="input-field col s6">
                    <input name="<?= $dbrow; ?>[gads_conversion_id]" value="<?= $gads_conversion_id ?>" id="gads_conversion_id" type="text" class="input-margin">
                    <label for="gads_conversion_id"><?php _e( 'Conversion ID', 'click-to-chat-for-whatsapp' ); ?></label>
                </div>
            </div>
            <div class="row ctc_side_by_side">
                <div class="input-field col s6">
                    <p class="description"><?php _e( 'Conversion Label', 'click-to-chat-for-whatsapp' ); ?></p>
                </div>
                <div class="input-field col s6">
                    <input name="<?= $dbrow; ?>[gads_conversion_label]" value="<?= $gads_conversion_label ?>" id="gads_conversion_label" type="text" class="input-margin">
                    <label for="gads_conversion_label"><?php _e( 'Conversion Label', 'click-to-chat-for-whatsapp' ); ?></label>
                </div>
            </div>

            <div class="row ctc_an_params ctc_g_ads_params ctc_sortable">

                <?php

                $num = '';

                if ( is_array($g_ads_params) && isset($g_ads_params[0]) ) {

                    foreach ($g_ads_params as $param ) {

                        $param_options = ( isset($options[$param]) && is_array($options[$param]) ) ? map_deep( $options[$param], 'esc_attr' ) : '';

                        $key = ( isset( $param_options['key']) ) ? esc_attr( $param_options['key'] ) : '';
                        $value = ( isset( $param_options['value']) ) ? esc_attr( $param_options['value'] ) : '';

                        // if key and value not empty..
                        if ( !empty($key) && !empty($value) ) {
                            ?>
                            <div class="ctc_an_param g_ads_param row" style="margin-bottom:5px; display:flex; gap:5px; justify-content:center;">

                                <input style="display: none;" name="ht_ctc_othersettings[g_ads_params][]" type="text" class="g_ads_param_order_ref_number" value="<?= $param ?>">

                                <div class="input-field">
                                    <input name="ht_ctc_othersettings[<?= $param ?>][key]" value="<?= $key ?>" id="<?= $param .'_key'?>" type="text" class="ht_ctc_g_ads_param_key input-margin">
                                    <label for="<?= $param .'_key' ?>"><?php _e( 'Event Parameter', 'click-to-chat-for-whatsapp' ); ?></label>
                                </div>

                                <div class="input-field">
                                    <input name="ht_ctc_othersettings[<?= $param ?>][value]" value="<?= $value ?>" id="<?= $param ?>" type="text" class="ht_ctc_g_ads_param_value input-margin">
                                    <label for="<?= $param ?>"><?php _e( 'Value', 'click-to-chat-for-whatsapp' ); ?></label>
                                </div>

                                <div class="input-field">
                                    <span style="color:#ddd; margin-left:auto; cursor:pointer;" class="an_param_remove dashicons dashicons-no-alt" title="Remove Parameter"></span>
                                </div>


                            </div>
                            <?php
                        }
                    
                        // $key_gen++;
                    }
                    
                    
                }
                
                ?>

                <!-- new fileds - for adding -->
                <!-- <div class="ctc_new_g_ads_param">
                </div> -->

                <!-- Add parameter - button -->
                <!-- <div style="text-align:center;">
                    <div class="ctc_add_g_ads_param_button" style="display:inline-flex; margin: 10px 0px; cursor:pointer; font-size:16px; font-weight:500; padding: 8px; justify-content:center;">
                        <span style="color: #039be5;" class="dashicons dashicons-plus-alt2" ></span>
                        <span style="color: #039be5;">Add Parameter</span>
                    </div>
                </div> -->
                
            </div>
        </div>

        <!-- snippets -->
        <div class="ctc_g_ads_param_snippets" style="display: none;">

            <!-- g_ads_param order. next key. (uses from js, saves in db) -->
            <input type="text" name="ht_ctc_othersettings[g_ads_param_order]" class="g_ads_param_order" value="<?= $g_ads_param_order ?>">

            
            <!-- snippet: add g_ads_param -->
            <div class="ctc_an_param g_ads_param ht_ctc_g_ads_add_param">

                <div class="row" style="display:flex; gap:5px; justify-content:center;">

                    <input style="display: none;" type="text" class="g_ads_param_order_ref_number" value="<?= $g_ads_param_order ?>">

                    <div class="input-field">
                        <input type="text" placeholder="click" class="ht_ctc_g_ads_add_param_key input-margin">
                        <label><?php _e( 'Event Parameter', 'click-to-chat-for-whatsapp' ); ?></label>
                    </div>

                    <div class="input-field">
                        <input type="text" placeholder="chat" class="ht_ctc_g_ads_add_param_value input-margin">
                        <label><?php _e( 'Value', 'click-to-chat-for-whatsapp' ); ?></label>
                    </div>

                    <div class="input-field">
                        <span style="color:#ddd; margin-left:auto; cursor:pointer;" class="an_param_remove dashicons dashicons-no-alt" title="Remove Parameter"></span>
                    </div>
                    
                </div>

            </div>
            
        </div>
        
        <!-- todo:l we can add - click count, date, username, if woo... add product details, .... -->
        <!-- <p class="description" style="margin:0px 10px;">Variables: {title}, {url}, {number} replace page title, url, and number that are assigned to the widget.</p> -->

        <p class="description"><?php _e( 'Google Ads Conversion', 'click-to-chat-for-whatsapp' ); ?> - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/google-ads-conversion/"><?php _e( 'more info', 'click-to-chat-for-whatsapp' ); ?></a> </p>

        <?php
    }


    // Admin notices
    function admin_notice() {

        if ( defined( 'HT_CTC_VERSION' ) ) {

            if ( version_compare( HT_CTC_VERSION, HT_CTC_PRO_CTC_REQUIRED_VERSION, '<' ) ) {
                
                if ( version_compare( HT_CTC_VERSION, HT_CTC_PRO_CTC_REQUIRED_VERSION_TOWORK, '<' ) ) {
                    // high priority notice - functions may stop working based on this
                    add_action('admin_notices', array( $this, 'ctc_update_notice_important') );
                } else {
                    // a plain notice - plugin wont stop working
                    add_action('admin_notices', array( $this, 'ctc_update_notice') );
                }
            }
        }
    }

    function ctc_update_notice() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e( "Please update the 'Click to Chat' plugin to the latest version, Ignore this message if already updated", 'click-to-chat-for-whatsapp' ); ?>.</p>
        </div>
        <?php
    }

    function ctc_update_notice_important() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( "Please update the 'Click to Chat' plugin to the latest version, its important for some features to work, Ignore this message if already updated", 'click-to-chat-for-whatsapp' ); ?>.</p>
        </div>
        <?php
    }

    // number
    function number() {
        
        $options = get_option('ht_ctc_chat_options');
        
        $number = ( isset( $options['number']) ) ? esc_attr( $options['number'] ) : '';

        // random numbers (array)
        $r_nums = (isset($options['r_nums'])) ? $options['r_nums'] : '' ;
        $count = 1;
        $num = '';
        $r_num_type = ( isset($options['r_num_type']) ) ? esc_attr( $options['r_num_type'] ) : '';

        if ( is_array($r_nums) ) {
            // Filter out empty values from the array
            $r_nums = array_filter($r_nums);
            // Re-index the array to ensure sequential keys
            $r_nums = array_values($r_nums);
            // $r_nums = array_map('esc_attr', $r_nums );
            $count = count($r_nums);
        }
        ?>

            <div class="ht_ctc_numbers">
                <?php

                // Additional numbers
                if ( isset( $r_nums[0] ) ) {
                    ?>
                    <div class="ctc_sortable">
                    <?php
                    for ($i=0; $i < $count ; $i++) {
                        $dbrow = "ht_ctc_chat_options[r_nums][$i]";
                        $num = esc_attr( $r_nums[$i] );

                        if ( class_exists( 'HT_CTC_Formatting' ) && method_exists( 'HT_CTC_Formatting', 'wa_number' ) ) {
                            $num = HT_CTC_Formatting::wa_number( $num );
                            $num = "+$num";
                        }

                        ?>
                        <div class="additional-number" style="margin-bottom: 15px;">
                            <p class="handle" style="display: flex;">
                                <input type="text" name="<?php echo $dbrow; ?>" data-name="<?php echo $dbrow; ?>" class="intl_number browser-default" value="<?= $num ?>">
                                <span style="color: #039be5; cursor: pointer;" class="remove_number dashicons dashicons-no-alt"></span>
                            </p>
                        </div>
                        <?php
                    }
                    ?>
                    </div>
                    <?php
                }
                
                ?>

            </div>

            <div style="display:flex; align-items:center; width:100%; gap:10px; line-height:1.5;">
                <div class="add_number" style="display:flex; align-items:center; cursor:pointer;">
                    <span class="dashicons dashicons-plus-alt2" style="font: size 20px; color:#039be5;"></span>
                    <span style="color:#039be5;font-size:16px; font-weight:500; margin-left:5px;"><?php _e( 'Add Random Number', 'click-to-chat-for-whatsapp' ); ?></span>
                </div>
                <span style="font-size:12px; white-space:nowrap; vertical-align:middle; line-height:1;">(<a target="_blank" href="https://holithemes.com/plugins/click-to-chat/random-number/">more info</a>)</span>
            </div>

             <!-- Random Number Type Selection -->
             <details class="ctc_details" style="margin: 12px 25px; font-size: 0.85em;">
                <summary style="cursor:pointer;">Random Number Type</summary>
                <div class="ctc_details_content">
                    <p style="margin-bottom:12px;">
                        <label for="r_num_type">
                            <span class="description">How random numbers should be distributed.</span>
                        </label><br>
                        <select name="ht_ctc_chat_options[r_num_type]" class="select_r_num_type" style="margin-top: 6px;">
                            <option value="pure" <?php echo $r_num_type == 'pure' ? 'SELECTED' : ''; ?>>Pure Random</option>
                            <option value="sequential" <?php echo $r_num_type == 'sequential' ? 'SELECTED' : ''; ?>>Sequence</option>
                        </select>
                    </p>
                    <div style="font-size: 13px; line-height: 1.6; color: #555;">
                        <p><strong>Pure Random:</strong> Completely random selection with no predefined order or pattern.</p>
                        <p><strong>Sequence:</strong> Numbers are selected in order to maintain a balanced distribution over time. <br> Triggers the server to fetch and update the latest value after the page load and user clicks to chat.</p>
                    </div>
                </div>
            </details>

            <p class="description" style="margin-top:12px; font-size:14px; color:#555;">Form filling and Multi-Agent features are on the <a target="_blank" href="<?= admin_url('admin.php?page=click-to-chat-greetings'); ?>" style="color: #039be5; font-weight: 500; text-decoration: none;">Greetings page</a></p>
            <div class="ctc_random_number_snippets ctc_init_display_none" style="display:none;">
                <div class="additional-number col s12" style="display:flex; padding-left:0px;">
                    <div class="input-field col s12" style="margin:5px 0px; padding-left:0px;">
                        <input type="text" class="browser-default ctc_add_auto_country_code" value="">
                        <span style="cursor: pointer; color:#039be5;" class="remove_number dashicons dashicons-no-alt"></span>
                    </div>
                </div>
            </div>

        <?php
    }


    // position_type_values - filter hook..
    public function add_position_type_values($position_type_values) {

        $position_type_values['absolute'] = 'Absolute';

        return $position_type_values;
    }

    /**
     * all pages editor can add safely..
    */
    function editor_values($values) {

        $editor_values = [
            'woo_single_header_content',
            'woo_single_main_content',
            'woo_single_bottom_content'
        ];

        $values = array_merge( $values, $editor_values );

        return $values;
    }

    // woocommerce single product page - places
    function woo_places($woo_places) {
        
        $add_woo_places = [
            'woocommerce_before_main_content' => 'Before Main Content',
            'woocommerce_before_single_product' => 'Before Product',
            'woocommerce_before_single_product_summary' => 'Before Product Summary',
            'woocommerce_single_product_summary' => 'Product Summary',
            'woocommerce_before_add_to_cart_form' => 'Before Add to Cart Form',
            'woocommerce_before_add_to_cart_button' => 'Before Cart Button',
            'woocommerce_after_add_to_cart_button' => 'After Cart Button',
            'woocommerce_after_add_to_cart_form' => 'After Add to Cart Form',
            'woocommerce_after_single_product' => 'After Product',
        ];

        $woo_places = array_merge( $woo_places, $add_woo_places );
        
        return $woo_places;
    }


    /**
     * meta box
     *  position
     */
    public function chat_meta_box( $current_post ) {
        $ht_ctc_pagelevel = get_post_meta( $current_post->ID, 'ht_ctc_pagelevel', true );
        
        $dbrow = "ht_ctc_pagelevel";
        $timedelay = ( isset($ht_ctc_pagelevel['timedelay']) ) ? esc_attr($ht_ctc_pagelevel['timedelay']) : '';
        $scroll = ( isset($ht_ctc_pagelevel['scroll']) ) ? esc_attr($ht_ctc_pagelevel['scroll']) : '';
        $style_desktop = ( isset($ht_ctc_pagelevel['style_desktop']) ) ? esc_attr($ht_ctc_pagelevel['style_desktop']) : '';
        $style_mobile = ( isset($ht_ctc_pagelevel['style_mobile']) ) ? esc_attr($ht_ctc_pagelevel['style_mobile']) : '';
        $enable_greetings = ( isset($ht_ctc_pagelevel['enable_greetings']) ) ? esc_attr( $ht_ctc_pagelevel['enable_greetings'] ) : '';

        $g_templates = [
            'no' => '-- No Greetings Dialog --',
            'greetings-1' => 'Greetings-1 - Customizable Design',
            'greetings-2' => 'Greetings-2 - Content Specific',
            'greetings-pro-1' => 'Greetings - Form',
            'greetings-pro-2' => 'Multi Agent'
        ];

        $greetings_template = ( isset( $ht_ctc_pagelevel['greetings_template']) ) ? esc_attr( $ht_ctc_pagelevel['greetings_template'] ) : '';

        ?>

        <!-- time delay -->
        <div class="row">
			<label for="ctc_time_delay"><?php _e( 'Time Delay', 'click-to-chat-for-whatsapp' ); ?></label><br>
			<input name="ht_ctc_pagelevel[timedelay]" value="<?= $timedelay ?>" id="ctc_time_delay" type="number">
		</div>

        <!-- scroll delay -->
        <div class="row">
			<label for="ctc_scroll"><?php _e( 'Scroll Delay', 'click-to-chat-for-whatsapp' ); ?></label><br>
			<input name="ht_ctc_pagelevel[scroll]" value="<?= $scroll ?>" id="ctc_scroll" type="number">
		</div>

        <!-- Select Style: Desktop -->
        <p class="description ht_ctc_admin_desktop ht_ctc_subtitle"><?php _e( 'Select Style (Desktop)', 'click-to-chat-for-whatsapp' ); ?>:</p class="description">
        <div class="row ht_ctc_admin_desktop">
            <div class="input-field col s12 m12">
                <select name="<?php echo $dbrow ?>[style_desktop]" class="chat_select_style select_style_desktop">
                    <option value="" <?php echo $style_desktop == '' ? 'SELECTED' : ''; ?> ><?php _e( 'Select style', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="1" <?php echo $style_desktop == 1 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-1', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="2" <?php echo $style_desktop == 2 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-2', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="3" <?php echo $style_desktop == 3 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-3', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="3_1" <?php echo $style_desktop == '3_1' ? 'SELECTED' : ''; ?> >&emsp;<?php _e( 'Style-3 Extend', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="4" <?php echo $style_desktop == 4 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-4', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="5" <?php echo $style_desktop == 5 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-5', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="6" <?php echo $style_desktop == 6 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-6', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="7" <?php echo $style_desktop == 7 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-7', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="7_1" <?php echo $style_desktop == '7_1' ? 'SELECTED' : ''; ?> >&emsp;<?php _e( 'Style-7 Extend', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="8" <?php echo $style_desktop == 8 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-8', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="99" <?php echo $style_desktop == 99 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-99 (Own Image/GIF)', 'click-to-chat-for-whatsapp' ); ?></option>
                </select>
            </div>
        </div>

        <!-- Select Style: Mobile -->
        <p class="description ht_ctc_admin_mobile ht_ctc_subtitle"><?php _e( 'Select Style (Mobile)', 'click-to-chat-for-whatsapp' ); ?>:</p class="description">
        <div class="row ht_ctc_admin_mobile">
            <div class="input-field col s12 m12">
                <select name="<?php echo $dbrow ?>[style_mobile]" class="chat_select_style select_style_mobile">
                    <option value="" <?php echo $style_mobile == '' ? 'SELECTED' : ''; ?> ><?php _e( 'Select style', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="1" <?php echo $style_mobile == 1 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-1', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="2" <?php echo $style_mobile == 2 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-2', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="3" <?php echo $style_mobile == 3 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-3', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="3_1" <?php echo $style_mobile == '3_1' ? 'SELECTED' : ''; ?> >&emsp;<?php _e( 'Style-3 Extend', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="4" <?php echo $style_mobile == 4 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-4', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="5" <?php echo $style_mobile == 5 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-5', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="6" <?php echo $style_mobile == 6 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-6', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="7" <?php echo $style_mobile == 7 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-7', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="7_1" <?php echo $style_mobile == '7_1' ? 'SELECTED' : ''; ?> >&emsp;<?php _e( 'Style-7 Extend', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="8" <?php echo $style_mobile == 8 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-8', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="99" <?php echo $style_mobile == 99 ? 'SELECTED' : ''; ?> ><?php _e( 'Style-99 (Own Image/GIF)', 'click-to-chat-for-whatsapp' ); ?></option>
                </select>
            </div>
        </div>


        <br>
		<p class="description"><?php _e( 'Greetings Settings', 'click-to-chat-for-whatsapp' ); ?>:</p>

        <!-- enable greetings settings at page level -->
        <div class="row">
			<label for="enable_greetings">
                <input name="ht_ctc_pagelevel[enable_greetings]" <?php checked( $enable_greetings, 1 ); ?> value="1" id="enable_greetings" type="checkbox">
                <?php _e( 'Enable page level Greetings settings', 'click-to-chat-for-whatsapp' ); ?>
			</label>
			<p class="description">Update and Refresh the page - <a style="text-decoration: none" target="_blank" href="https://holithemes.com/plugins/click-to-chat/greetings-page-level-settings/">more info</a></p>
		</div>
        <br>
        <?php

        // load greetings page level settings
        if ( '1' == $enable_greetings ) {

            $allowed_html = wp_kses_allowed_html( 'post' );

            $header_content = ( isset($ht_ctc_pagelevel['header_content']) ) ? $ht_ctc_pagelevel['header_content'] : '';
            if ( '' !== $header_content ) {
                $header_content = html_entity_decode(wp_kses($header_content, $allowed_html));
            }
            $main_content = ( isset($ht_ctc_pagelevel['main_content']) ) ? $ht_ctc_pagelevel['main_content'] : '';
            if ( '' !== $main_content ) {
                $main_content = html_entity_decode(wp_kses($main_content, $allowed_html));
            }
            $bottom_content = ( isset($ht_ctc_pagelevel['bottom_content']) ) ? $ht_ctc_pagelevel['bottom_content'] : '';
            if ( '' !== $bottom_content ) {
                $bottom_content = html_entity_decode(wp_kses($bottom_content, $allowed_html));
            }

            $g_call_to_action = ( isset($ht_ctc_pagelevel['g_call_to_action']) ) ? esc_attr($ht_ctc_pagelevel['g_call_to_action']) : '';

            ?>
            <!-- Select greetings template -->
            <p class="description ht_ctc_admin_select_greetings ht_ctc_subtitle"><?php _e( 'Greetings Template', 'click-to-chat-for-whatsapp' ); ?>:</p class="description">
            <div class="row ht_ctc_admin_select_greetings">
                <div class="input-field col s12">
                    <select name="<?php echo $dbrow ?>[greetings_template]" class="chat_select_style select_greetings_template">
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
            <br>
            <?php

            /**
             * visual part issue .. 
             * https://wordpress.org/support/topic/how-to-show-focus-in-wp_editor-of-metabox/
             */

            
            if ( ! function_exists( 'ctc_meta_tinymce_mce_buttons_2' ) ) {
                function ctc_meta_tinymce_mce_buttons_2( $buttons ) {

                    $key = array_search( 'forecolor', $buttons );
                    
                    // add after forecolor
                    if ( $key !== false && is_int( $key ) ) {
                        array_splice( $buttons, $key+1, 0, 'backcolor' );
                    }

                    // add at first
                    array_unshift( $buttons, 'fontselect' );
                    array_push( $buttons, 'fontsizeselect' );

                    return $buttons;
                }
            }
            add_filter( 'mce_buttons_2', 'ctc_meta_tinymce_mce_buttons_2' );


            $args = [
                    'textarea_rows' => 5,
                    'media_buttons' => false,
                    // 'tinymce' => true,
                    'tinymce'       => array(
                            'textarea_rows'=> 10,
                            'fontsize_formats' => "6px 8px 10px 12px 13px 14px 15px 16px 18px 20px 24px 28px 32px 36px",
                    ),
                    // 'quicktags' => true,
                    // 'quicktags' => array( 'buttons' => 'em,strong,link' ),
                    // 'teeny'         => true,
                    // 'editor_height' => 150,
                    // 'drag_drop_upload' => true,
                ];
            
            ?>
            <!-- header greetings -->
            <div class="row">
                <label for=""><?php _e( 'Greetings - Header Content', 'click-to-chat-for-whatsapp' ); ?></label><br>
                <?php
                $content   = $header_content;
                $editor_id = 'ctc_header_content';

                $args['textarea_name'] = "ht_ctc_pagelevel[header_content]";
                wp_editor( $content, $editor_id, $args );
                ?>
            </div>
            <br>

            <!-- main greetings -->
            <div class="row">
                <label for=""><?php _e( 'Greetings - Main Content', 'click-to-chat-for-whatsapp' ); ?></label><br>
                <?php
                $content   = $main_content;
                $editor_id = 'ctc_main_content';
                $args['textarea_name'] = "ht_ctc_pagelevel[main_content]";
                wp_editor( $content, $editor_id, $args );
                ?>
            </div>
            <br>

            <!-- bottom greetings -->
            <div class="row">
                <label for=""><?php _e( 'Greetings - Bottom Content', 'click-to-chat-for-whatsapp' ); ?></label><br>
                <?php
                $content   = $bottom_content;
                $editor_id = 'ctc_bottom_content';
                $args['textarea_name'] = "ht_ctc_pagelevel[bottom_content]";
                wp_editor( $content, $editor_id, $args );
                ?>
            </div>
            <br>

            <!-- call to action - greetings -->
            <div class="row">
                <label for="g_call_to_action"><?php _e( 'Greetings - Call to Action', 'click-to-chat-for-whatsapp' ); ?></label><br>
                <input name="ht_ctc_pagelevel[g_call_to_action]" value="<?= $g_call_to_action ?>" id="g_call_to_action" type="text">
            </div>


            <script>
                setTimeout(() => {
                    try {
                        tinyMCE.get('ctc_header_content').dom.setStyle(tinyMCE.get('ctc_header_content').getBody(), 'backgroundColor', '#26a69a');
                        tinyMCE.get('ctc_main_content').dom.setStyle(tinyMCE.get('ctc_main_content').getBody(), 'backgroundColor', '#26a69a');
                        tinyMCE.get('ctc_bottom_content').dom.setStyle(tinyMCE.get('ctc_bottom_content').getBody(), 'backgroundColor', '#26a69a');
                    } catch (e) {}
                }, 5000);
            </script>

            <?php
        }

    }


    /**
     * metabox: custom link
     * 
     * commented while calling. (planning to relese in later version)
     */
    public function chat_meta_box_after_number( $current_post ) {
        $ht_ctc_pagelevel = get_post_meta( $current_post->ID, 'ht_ctc_pagelevel', true );
        
        $dbrow = "ht_ctc_pagelevel";
        $custom_url_d = ( isset($ht_ctc_pagelevel['custom_url_d']) ) ? esc_attr($ht_ctc_pagelevel['custom_url_d']) : '';
        $custom_url_m = ( isset($ht_ctc_pagelevel['custom_url_m']) ) ? esc_attr($ht_ctc_pagelevel['custom_url_m']) : '';

        $ph_custom_link = 'todo';

        ?>
        <div class="row">
            <label for="custom_url_d"><?php _e( 'Custom Link: Desktop', 'click-to-chat-for-whatsapp' ); ?></label><br>
            <input name="ht_ctc_pagelevel[custom_url_d]" value="<?= $custom_url_d ?>" id="custom_url_d" type="text" placeholder="<?= $ph_custom_link ?>">
        </div>
        <div class="row">
            <label for="custom_url_m"><?php _e( 'Custom Link: Mobile', 'click-to-chat-for-whatsapp' ); ?></label><br>
            <input name="ht_ctc_pagelevel[custom_url_m]" value="<?= $custom_url_m ?>" id="custom_url_m" type="text" placeholder="<?= $ph_custom_link ?>">
            <p class="description"><a style="text-decoration: none" target="_blank" href="https://holithemes.com/plugins/click-to-chat/todo/">Custom Link</a></p>
        </div>
        
        <?php
    }


    /**
     * after_showhide - above show/hide display settings
     * 
     * @since 1.1 - logged_in_only
     * @updated 1.3 - all_users, logged_in, logged_out
     */
    function after_showhide() {
        $options = get_option('ht_ctc_chat_options');
        $dbrow = 'ht_ctc_chat_options';
        $type = 'chat';

        $timedelay = ( isset( $options['timedelay']) ) ? esc_attr( $options['timedelay'] ) : '';
        $scroll = ( isset( $options['scroll']) ) ? esc_attr( $options['scroll'] ) : '';

        $logged_in_only = ( isset( $options['logged_in_only']) ) ? esc_attr( $options['logged_in_only'] ) : '';

        // since 1.3 
        $display_user_base = ( isset( $options['display_user_base']) ) ? esc_attr( $options['display_user_base'] ) : 'all_users';

        // previous version compatibility
        if ( isset( $options['logged_in_only']) ) {
            $display_user_base = 'logged_in';
        }
        ?>

        <p class="description ht_ctc_pro_subtitle" style="margin-bottom: 10px; border-color: #8fbc8f;">PRO Settings: </p>

        <!-- display only to log in users -->
        <div class="row ctc_side_by_side">
            <div class="col s6">
                <p><?php _e( 'Display based on User Login Status', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
            <div class="input-field col s6" style="margin-top: 0px;">
                <select name="<?php echo $dbrow ?>[display_user_base]" class="display_select_user_base">
                    <option value="all_users" <?php echo $display_user_base == "all_users" ? 'SELECTED' : ''; ?> ><?php _e( 'All Users', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="logged_in" <?php echo $display_user_base == "logged_in" ? 'SELECTED' : ''; ?> ><?php _e( 'Logged-in Users only', 'click-to-chat-for-whatsapp' ); ?></option>
                    <option value="logged_out" <?php echo $display_user_base == "logged_out" ? 'SELECTED' : ''; ?> ><?php _e( 'Logged-out Users only (not logged-in)', 'click-to-chat-for-whatsapp' ); ?></option>
                </select>
                <p class="description">Display based on website visitor login Status</p>
                <p class="description">All Users: Logged-in and not-logged-in users</p>
            </div>
        </div>

        <!-- time delay -->
        <div class="row ctc_side_by_side">
            <div class="col s6">
                <p><?php _e( 'Time Delay', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
            <div class="input-field col s6">
                <input name="<?php echo $dbrow ?>[timedelay]" value="<?php echo $timedelay ?>" id="timedelay" type="number" min="0" class="" >
                <label for="timedelay"><?php _e( 'Time Delay', 'click-to-chat-for-whatsapp' ); ?></label>
                <p class="description"><?php _e( 'Display After this number of seconds. E.g. Add 10 to display after 10 seconds', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
        </div>

        <!-- after page scroll percentage -->
        <div class="row ctc_side_by_side">
            <div class="col s6">
                <p><?php _e( 'Scroll', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
            <div class="input-field col s6">
                <input name="<?php echo $dbrow ?>[scroll]" value="<?php echo $scroll ?>" id="scroll" type="number" min="0" class="" >
                <label for="scroll"><?php _e( 'Scroll Delay', 'click-to-chat-for-whatsapp' ); ?></label>
                <p class="description"><?php _e( 'Display After user scrolled this percentage of page. E.g. Add 10 to display after user scrolled 10% of page', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
        </div>


        <?php

        /**
         * display based on country code
         * 
         * @var display_countries select all/selected
         * @var display_countries_list selected countries list..
         * 
         * 
         * 
         * @since 2.6
         */

        // include countries
        include_once HT_CTC_PRO_PLUGIN_DIR .'inc/tools/ht-ctc-pro-commons.php';
        $countries = [];

        // if HT_CTC_PRO_Commons::countries exists..
        if ( class_exists( 'HT_CTC_PRO_Commons' ) && method_exists( 'HT_CTC_PRO_Commons', 'countries_list' ) ) {
            $countries = HT_CTC_PRO_Commons::countries_list();
        }

        // all / selected
        $display_countries = ( isset( $options['display_countries']) ) ? esc_attr( $options['display_countries'] ) : 'all';
        
        // retun array. uses to check if selected.
        $display_countries_list = ( isset( $options['display_countries_list']) ) ? $options['display_countries_list'] : [];
        $display_countries_list = ( is_array($display_countries_list) && !empty($display_countries_list) ) ? array_map('esc_attr', $display_countries_list ) : [];
        ?>

        <!-- Countries -->
        <div class="row ctc_side_by_side">

            <div class="col s6">
                <p>Display based on country <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/display-based-on-country/"><span class="dashicons dashicons-external"></span></a></p>
            </div>

            <div class="col s6">

                <div class="">
                    <select name="<?php echo $dbrow ?>[display_countries]" class="select_display_countries">
                        <option value="all" <?php echo $display_countries == "all" ? 'SELECTED' : ''; ?> >All Countries</option>
                        <option value="selected" <?php echo $display_countries == "selected" ? 'SELECTED' : ''; ?> >Only selected Countries</option>
                    </select>
                </div>

                <div class="ctc_display_countries_base" style="margin: 20px 0px;">
                    <select name="<?php echo $dbrow ?>[display_countries_list][]" class="display_countries_list browser-default" multiple id="">
                        <?php
                        foreach ($countries as $k => $v) {
                            ?>
                            <option value="<?= $k ?>" <?= in_array($k, $display_countries_list) ? 'SELECTED' : ''; ?> ><?= $v ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

            </div>

        </div>
    
    <?php
    }


    /**
     * Business Hours (bh)
     * 
     */
    function bh() {
        $options = get_option('ht_ctc_chat_options');
        $dbrow = 'ht_ctc_chat_options';
        $type = 'chat';

        $off_num = ( isset( $options['off_num']) ) ? esc_attr( $options['off_num'] ) : '';
        $off_cta = ( isset( $options['off_cta']) ) ? esc_attr( $options['off_cta'] ) : '';
        
        // default/main values
        $d_number =  ( isset( $options['number']) ) ? esc_attr( $options['number'] ) : '';
        $d_cta =  ( isset( $options['call_to_action']) ) ? esc_attr( $options['call_to_action'] ) : '';

        $bh = ( isset( $options['bh']) ) ? esc_attr( $options['bh'] ) : '';
        $monday = ( isset( $options['monday']) ) ? esc_attr( $options['monday'] ) : '';
        $tuesday = ( isset( $options['tuesday']) ) ? esc_attr( $options['tuesday'] ) : '';
        $wednesday = ( isset( $options['wednesday']) ) ? esc_attr( $options['wednesday'] ) : '';
        $thursday = ( isset( $options['thursday']) ) ? esc_attr( $options['thursday'] ) : '';
        $friday = ( isset( $options['friday']) ) ? esc_attr( $options['friday'] ) : '';
        $saturday = ( isset( $options['saturday']) ) ? esc_attr( $options['saturday'] ) : '';
        $sunday = ( isset( $options['sunday']) ) ? esc_attr( $options['sunday'] ) : '';

        ?>

        <ul class="collapsible" id="ht_ctc_bh">
        <li class="active">
        <div class="collapsible-header"><?php _e( 'PRO Settings: Business hours - Offline/Online', 'click-to-chat-for-whatsapp' ); ?>
        <span class="right_icon dashicons dashicons-arrow-down-alt2"></span>
        </div>
        <div class="collapsible-body">

            <!-- business hours -->
            <div class="row ctc_side_by_side">
                <div class="col s6" style="padding-top: 14px;">
                    <p><?php _e( 'Business Hours', 'click-to-chat-for-whatsapp' ); ?> <br> (<?php _e( 'Online/Offline', 'click-to-chat-for-whatsapp' ); ?>)</p>
                </div>
                <div class="input-field col s6">
                    <select name="<?php echo $dbrow ?>[bh]" class="select_bh">
                        <option value="always" <?php echo $bh == "always" ? 'SELECTED' : ''; ?> ><?php _e( 'Always Open (Online)', 'click-to-chat-for-whatsapp' ); ?></option>
                        <option value="timebase" <?php echo $bh == "timebase" ? 'SELECTED' : ''; ?> ><?php _e( 'Selected Days, Hours', 'click-to-chat-for-whatsapp' ); ?></option>
                    </select>
                </div>
            </div>

            <p class="description bh_time">Current Site Time: <code><?php echo current_time( 'mysql' ); ?></code> ( Settings -> General - Timezone )</p>
            <p class="description bh_time">Start Time and End Time: leave blank for 24 hours online</p>
            <p class="description bh_time" style="margin: 15px 0px; text-align:center; font: bold;">After set the Business Hours - please check <a href="#basedon_business_hours">Settings based on Business Hours</a> (hide styles, change number, ..)</p>
            <br class="bh_time">

            <table class="bh_time">
                <thead>
                <tr>
                    <th>Week</th>
                    <th>Offline/Online</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
                </thead>

                <tbody>

                    <?php
                    $days = array(
                        'monday',
                        'tuesday',
                        'wednesday',
                        'thursday',
                        'friday',
                        'saturday',
                        'sunday',
                    );

                    foreach ($days as $day ) {
                        
                        $st = $day.'_st';
                        $et = $day.'_et';

                        $checked = '';
                        $time_style = 'visibility: hidden;';
                        if ( isset($options[$day]) ) {
                            $checked = 'checked';
                            $time_style = '';
                        }

                        $start_time = ( isset( $options[$st] ) ) ? esc_attr( $options[$st] ) : '';
                        $end_time = ( isset( $options[$et] ) ) ? esc_attr( $options[$et] ) : '';
                        
                        ?>

                        <tr>
                            <td><?php echo ucfirst($day) ?></td>
                            <td>
                                <div class="switch">
                                    <label>
                                    <span class="switch_off"><?php _e( 'Off', 'click-to-chat-for-whatsapp' ); ?></span>
                                    <input name="<?php echo $dbrow ?>[<?php echo $day ?>]" type="checkbox" class="ctc_day" data-day="<?php echo $day ?>" <?php echo $checked ?> >
                                    <span class="lever"></span>
                                    <span class="switch_on"><?php _e( 'On', 'click-to-chat-for-whatsapp' ); ?></span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="input-field ctc_time_<?php echo $day ?>" style="<?php echo $time_style ?>">
                                    <input name="<?php echo $dbrow ?>[<?php echo $st ?>]" value="<?php echo $start_time ?>" id="<?php echo $st ?>" type="text" class="timepicker_start">
                                    <label for="<?php echo $st ?>">Start time</label>
                                </div>
                            </td>
                            <td>
                                <div class="input-field ctc_time_<?php echo $day ?>" style="<?php echo $time_style ?>">
                                    <input name="<?php echo $dbrow ?>[<?php echo $et ?>]" value="<?php echo $end_time ?>" id="<?php echo $et ?>" type="text" class="timepicker_end">
                                    <label for="<?php echo $et ?>">End time</label>
                                </div>
                            </td>
                        </tr>


                        <?php
                    
                    }
                    ?>
                    


                </tbody>
            </table>
            
            <p class="bh_time" style="margin-bottom: 59px"></p>

            <hr class="bh_time" id="basedon_business_hours" style="margin-bottom: 59px;">

            <!-- offline - Hide -->
            <div class="row bh_time">
                <div class="col s6">
                    <p class="description"><?php _e( 'Hide when Offline', 'click-to-chat-for-whatsapp' ); ?></p>
                </div>
                
                <div class="col s6">

                    <?php
                    if ( isset( $options['off_hide'] ) ) {
                        ?>
                        <p>
                            <label>
                                <input name="<?php echo $dbrow ?>[off_hide]" type="checkbox" value="1" <?php checked( $options['off_hide'], 1 ); ?> id="off_hide" class="off_hide"/>
                                <span><?php _e( 'Hide WhatsApp When Offline', 'click-to-chat-for-whatsapp' ); ?></span>
                            </label>
                        </p>
                        <?php
                    } else {
                        ?>
                        <p>
                            <label>
                                <input name="<?php echo $dbrow ?>[off_hide]" type="checkbox" value="1" id="off_hide" class="off_hide"/>
                                <span><?php _e( 'Hide WhatsApp When Offline', 'click-to-chat-for-whatsapp' ); ?></span>
                            </label>
                        </p>
                        <?php
                    }
                    ?>
                <p class="description"><?php _e( 'Hide WhatsApp When offline', 'click-to-chat-for-whatsapp' ); ?> - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/offline-hide/">more info</a> </p>
                </div>
            </div>
            
            <!-- offline - number change -->
            <div class="row bh_time offline_hide">
                <div class="input-field col s12">
                    <input name="<?php echo $dbrow ?>[off_num]" value="<?php echo $off_num ?>" id="off_num" type="text" placeholder="<?php echo $d_number ?>" class="input-margin tooltipped" data-position="top" data-tooltip="Country Code + Number">
                    <label for="off_num"><?php _e( 'Offline: Number', 'click-to-chat-for-whatsapp' ); ?></label>
                    <p class="description"><?php _e( 'Change Number when offline, leave blank to not change WhatsApp number when offline', 'click-to-chat-for-whatsapp' ); ?> </p>
                    <!-- - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/offline-number/">more info</a> -->
                </div>
            </div>


            <!-- Offline - Call to Action -->
            <div class="row bh_time offline_hide">
                <div class="input-field col s12">
                    <input name="<?php echo $dbrow ?>[off_cta]" value="<?php echo $off_cta ?>" id="off_cta" type="text" placeholder="<?php echo $d_cta ?>" class="input-margin">
                    <label for="off_cta"><?php _e( 'Offline: Call to Action', 'click-to-chat-for-whatsapp' ); ?></label>
                    <p class="description"><?php _e( 'Change Call to Action when offline, leave blank to not change call to action when offline', 'click-to-chat-for-whatsapp' ); ?> </p>
                    <!-- - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/offline-call-to-action/">more info</a> -->
                </div>
            </div>
            

            <p class="description" style="margin-bottom: 40px;">Settings based on Business Hours - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/business-hours-online-offline/">more info</a></p>


        </div>
        </li>
        </ul>

        <?php
    }

    /**
     * admin scripts hook
     */
    public function admin_scripts() {

        $os = get_option('ht_ctc_othersettings');
        
        // js
        $js = 'admin_pro.js';

        if ( defined('HT_CTC_PRO_DEBUG_MODE')  ) {
            $js = 'admin_pro.dev.js';
        }

        if ( defined( 'HT_CTC_PRO_PLUGIN_FILE' ) ) {
            wp_enqueue_script( 'ctc_admin_pro_js', plugins_url( "admin/admin_assets/js/$js", HT_CTC_PRO_PLUGIN_FILE ), array( 'jquery' ), HT_CTC_PRO_VERSION, true );
        }
    }

    public function admin_register_scripts($hook) {
        
        
        if ( defined( 'HT_CTC_PRO_PLUGIN_FILE' ) ) {

            if ('click-to-chat_page_click-to-chat-greetings' == $hook) {
                // wp media uploader
                wp_enqueue_media();

                // jQuery timepicker
                // should not load in main settings - where md timepicker is using..
                wp_enqueue_style('ctc_admin_pro_timepicker_css', plugins_url( 'admin/admin_assets/lib/timepicker/jquery.timepicker.min.css', HT_CTC_PRO_PLUGIN_FILE ) , '', HT_CTC_PRO_VERSION );
                wp_enqueue_script( 'ctc_admin_pro_timepicker_js', plugins_url( 'admin/admin_assets/lib/timepicker/jquery.timepicker.min.js', HT_CTC_PRO_PLUGIN_FILE ), array( 'jquery' ), HT_CTC_PRO_VERSION, true );

                /**
                 * intl input - for multi agent 
                 * registered at main plugin - class-ht-ctc-admin-scripts
                 * 
                 * intlTelInput files are at main plugin
                 */
                wp_enqueue_style('ctc_admin_intl_css');
                wp_enqueue_script('ctc_admin_intl_js');

                $args = true;
                global $wp_version;
                // if wp version is not null and is greater than 6.3
                if ( !$wp_version && version_compare( $wp_version, '6.3', '>=' ) ) {
                    $args = array(
                        'in_footer' => true,
                        'strategy' => 'defer',
                    );
                }

                wp_enqueue_script( 'ctc_pro_admin_greetings_js', plugins_url( 'admin/admin_assets/js/greetings.js', HT_CTC_PRO_PLUGIN_FILE ), array( 'jquery' ), HT_CTC_PRO_VERSION, $args );

            }


            if ('toplevel_page_click-to-chat' == $hook) {

                // sumoselect
                wp_enqueue_style('ctc_admin_sumoselect_css', plugins_url( 'admin/admin_assets/lib/sumoselect/sumoselect.min.css', HT_CTC_PRO_PLUGIN_FILE ) , '', HT_CTC_PRO_VERSION );
                wp_enqueue_script('ctc_admin_sumoselect_js', plugins_url( 'admin/admin_assets/lib/sumoselect/jquery.sumoselect.min.js', HT_CTC_PRO_PLUGIN_FILE ), array( 'jquery' ), HT_CTC_PRO_VERSION, true );
                
            }


        }
        
    }


    /**
     * Greetings Dialog
     * 
     * actions
     *  Time
     *  scroll
     */
    function greetings_settings( $values ) {

        $options = get_option('ht_ctc_chat_options');
        $pro_path = HT_CTC_PRO_PLUGIN_DIR ."admin/components";

        $inputs = [
            [
                'template' => 'heading',
                'title' => 'Actions',
                'parent_class' => 'greetings_actions ctc_greetings_settings ctc_g_1 ctc_g_2',
            ],
            [
                'template' => 'content',
                'description' => '<div style="margin:0px 14px 0px 14px;"><p class="description"><a href="https://holithemes.com/plugins/click-to-chat/greetings-actions/">Greetings Actions:</a> Displays greetings based on specific triggers</p><p class="description"><strong style="font-weight: 500;">Click:</strong> Clicked on any element with Class name: \'ctc_greetings\'</p><p class="description"><strong style="font-weight: 500;">Viewport [PRO]:</strong> An element is in/reached viewport (25% margin) with Class name: \'ctc_greetings_now\'</p></div>',
                'parent_class' => 'pr_g_time_action greetings_actions ctc_greetings_settings ctc_g_1 ctc_g_2',
            ],
            [
                'title' => 'Time',
                'db' => 'g_time_action',
                'template' => 'number',
                'min' => '0',
                'label' => 'Time Action',
                'description' => 'Automatically displays after this number of seconds',
                'parent_class' => 'pr_g_time_action greetings_actions ctc_greetings_settings ctc_g_1 ctc_g_2 ctc_no_demo',
            ],
            [
                'title' => 'Scroll',
                'db' => 'g_scroll_action',
                'template' => 'number',
                'min' => '0',
                'label' => 'Scroll Action',
                'description' => 'Automatically displays after this percentage of page scroll',
                'parent_class' => 'pr_g_scroll_action greetings_actions ctc_greetings_settings ctc_g_1 ctc_g_2 ctc_no_demo',
            ],
            [
                'title' => "If the user closes Greetings, Don't reopen based on time, scroll",
                'db' => 'g_no_reopen',
                'template' => 'checkbox',
                'label' => "Disable time, scroll Actions",
                'description' => "If User closes the greetings dialog on any page, then greetings won't reopen based on time, scroll action",
                'parent_class' => 'pr_g_scroll_action greetings_actions ctc_greetings_settings ctc_g_1 ctc_g_2 ctc_no_demo',
            ]
        ];

        foreach ($inputs as $input) {
            // array_push($values['main']['inputs'], $input);
            array_push($values['greetings_settings']['inputs'], $input);
        }

        $admin_email = get_option('admin_email');

        $g_form_fallback_values = array(
            'empty' => '1',
            'fields' => [
                'field_1',
                'field_2'
            ],
            'field_1' => [
                'type' => 'text',
                'name' => 'Name',
                'placeholder' => 'Name',
                'add_to_prefilled' => '1',
            ],
            'field_2' => [
                'type' => 'email',
                'name' => 'Email',
                'placeholder' => 'Email',
                'add_to_prefilled' => '1',
            ],
            'field_count' => '4',
            'email' => $admin_email,
            'webhook' => '',
            'header_bg_color' => '#075e54',
            'main_bg_color' => '#ece5dd',
            'message_box_bg_color' => '',
            'cta_style' => '7_1',
        );


        $g_multiagent_fallback_values = array(
            'empty' => '1',
            'header_bg_color' => '#00a884', // #075e54
            'main_bg_color' => '#ece5dd',
            'message_box_bg_color' => '#dcf8c6',
            'cta_style' => '7_1',
        );

        $main_number = (isset($options['number'])) ? esc_attr($options['number']) : '';

        // if main number added then add to agent fallback
        if ('' !== $main_number) {
            $g_multiagent_fallback_values['agents'] = [
                'agent_1'
            ];
            $g_multiagent_fallback_values['agent_1'] = [
                'fallback_values' => 'yes',
                'ref_name' => '',
                'enable' => '1',
                'number' => $main_number,
                'title' => '',
                'description' => '',
                'pre_filled' => '',
                'agent_image_id' => '',
                'agent_image_url' => '',
                'timings' => 'always'
            ];
            $g_multiagent_fallback_values['agent_count'] = '2';
        }




        // Greetings Dialog PRO - 1 - Form
        $g_pro_1 = [
            'greetings_pro_1' => [
                'id' => 'ht_ctc_greetings_pro_1',
                'title' => 'Greetings - Form',
                'dbrow' => 'ht_ctc_greetings_pro_1',
                'class' => 'ctc_greetings_settings pr_ht_ctc_greetings_pro_1',
                'fallback_values' => $g_form_fallback_values,
                'inputs' => [
                    [
                        'template' => 'collapsible_start',
                        'title' => 'Greetings Dialog - Form',
                    ],
                    [
                        'db' => 'empty',
                        'template' => 'empty',
                    ],
                    [
                        'title' => '',
                        'db' => '',
                        'template' => 'admin-greetings-pro-1',
                        'path' => $pro_path,
                        'description' => '',
                    ],
                    [
                        'title' => 'Email',
                        'db' => 'email',
                        'template' => 'text',
                        'label' => 'Email Notification',
                        'placeholder' => "e.g. admin@example.com , admin2@example.com",
                        'description' => 'Get notification at these email addresses when the user send the form - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/greetings-form/#email_notification">more info</a> <br> If you are using cache plugins, set cache lifespan to less than 8 hours or use <strong>webhooks feature</strong>',
                    ],
                    [
                        'title' => 'Webhook',
                        'db' => 'webhook',
                        'template' => 'text',
                        'label' => 'Webhook address',
                        'description' => 'Triggers this Webhook URL with form data when the user send the form - <a target="_blank" href="https://holithemes.com/plugins/click-to-chat/greetings-form/#webhooks">more info</a>',
                        'parent_class' => 'pr_g_p_1_webhook',
                    ],
                    [
                        'title' => 'Header - Background Color',
                        'db' => 'header_bg_color',
                        'template' => 'color',
                        'default_color' => '#075e54',
                        'description' => 'Header - Background Color',
                        'parent_class' => 'pr_g_p_1_header_bg_color',
                    ],
                    [
                        'title' => 'Form - Background Color',
                        'db' => 'main_bg_color',
                        'template' => 'color',
                        'default_color' => '#ece5dd',
                        'description' => 'Form - Background Color',
                        'parent_class' => 'pr_g_p_1_main_bg_color',
                    ],
                    [
                        'title' => 'Message Box - Background Color',
                        'db' => 'message_box_bg_color',
                        'template' => 'color',
                        'default_color' => '#dcf8c6',
                        'description' => 'Main Content as a Message Box with Background Color',
                        'parent_class' => 'pr_g_p_1_message_box_bg_color',
                    ],
                    [
                        'title' => 'Email Notification Subject, Call to Action - button type',
                        'template' => 'element_details_start',
                    ],
                    [
                        'title' => 'Email Notification: Subject',
                        'db' => 'email_subject',
                        'template' => 'text',
                        'label' => 'Email Notification: Subject',
                        'placeholder' => 'Click to Chat - Greetings Form',
                        'description' => 'If blank: Click to Chat - Greetings Form. Use placeholders like {field_1}, {field_2} , etc.. to insert user input into the subject. ',
                    ],
                    [
                        'title' => __( 'Call to Action - button type', 'click-to-chat-for-whatsapp'),
                        'db' => 'cta_style',
                        'template' => 'select',
                        'description' => "Call to Action - button type (Color settings at Click to Chat -> Customize)",
                        'list' => [
                            '1' => 'Themes Button (style-1)',
                            '7_1' => 'Button with WhatsApp Icon (style-7 Extend)',
                        ],
                        'parent_class' => 'pr_g_p_1_cta_style',
                    ],
                    [
                        'template' => 'element_details_end',
                    ],
                    [
                        'template' => 'collapsible_end',
                        'description' => "<a href='https://holithemes.com/plugins/click-to-chat/greetings-form/' target='_blank'>Greetings Form</a>",
                    ],
                ]
            ]
        ];

        $g_pro_2 = [
            'greetings_pro_2' => [
                'id' => 'ht_ctc_greetings_pro_2',
                'title' => 'Greetings - Multi Agent',
                'dbrow' => 'ht_ctc_greetings_pro_2',
                'class' => 'ctc_greetings_settings pr_ht_ctc_greetings_pro_2',
                'fallback_values' => $g_multiagent_fallback_values,
                'inputs' => [
                    [
                        'template' => 'collapsible_start',
                        'title' => 'Greetings Dialog - Multi Agent',
                    ],
                    [
                        'db' => 'empty',
                        'template' => 'empty',
                    ],
                    [
                        'title' => '',
                        'db' => '',
                        'template' => 'admin-greetings-pro-2',
                        'path' => $pro_path,
                        'description' => '',
                    ],
                    [
                        'title' => __( 'Offline Agents', 'click-to-chat-for-whatsapp'),
                        'db' => 'agent_offline',
                        'template' => 'select',
                        'list' => [
                            'chat' => 'Chat when offline (Displays agent with offline message)',
                            'nochat' => 'Disable chat (Displays agent with offline message)',
                            'hide' => 'Hide',
                        ],
                        'parent_class' => 'pr_g_p_2_offline_agents',
                    ],
                    [
                        'title' => __( 'Initial Display', 'click-to-chat-for-whatsapp'),
                        'db' => 'initial_display',
                        'template' => 'select',
                        'list' => [
                            'agents' => 'Agents',
                            'content' => 'Greetings content',
                        ],
                        'description' => '',
                        'parent_class' => 'pr_g_p_2_inital_display',
                    ],
                    [
                        'title' => 'Header - Background Color',
                        'db' => 'header_bg_color',
                        'template' => 'color',
                        'default_color' => '#075e54',
                        'description' => 'Header - Background Color',
                        'parent_class' => 'pr_g_p_2_header_bg_color',
                    ],
                    [
                        'title' => 'Main Content - Background Color',
                        'db' => 'main_bg_color',
                        'template' => 'color',
                        'default_color' => '#ece5dd',
                        'description' => 'Main Content - Background Color',
                        'parent_class' => 'pr_g_p_2_main_bg_color ctc_init_display_none',
                    ],
                    [
                        'title' => 'Message Box - Background Color',
                        'db' => 'message_box_bg_color',
                        'template' => 'color',
                        'default_color' => '#dcf8c6',
                        'description' => 'Main Content as a Message Box with Background Color',
                        'parent_class' => 'pr_g_p_2_message_box_bg_color ctc_init_display_none',
                    ],
                    [
                        'title' => __( 'Call to Action - button type', 'click-to-chat-for-whatsapp'),
                        'db' => 'cta_style',
                        'template' => 'select',
                        'description' => "Call to Action - button type (Color settings at Click to Chat -> Customize)",
                        'list' => [
                            '1' => 'Themes Button (style-1)',
                            '7_1' => 'Button with WhatsApp Icon (style-7 Extend)',
                        ],
                        'parent_class' => 'pr_g_p_1_cta_style',
                    ],
                    [
                        'template' => 'collapsible_end',
                        'description' => "<a href='https://holithemes.com/plugins/click-to-chat/multi-agent/' target='_blank'>Greetings - Multi Agent</a>",
                    ],
                ]
            ]
        ];


        // Greetings Dialog PRO - 1 - Form
        
        // $values = $values + $g_pro_1;

        // -- if printr($values) the greetings g_pro_1 - greetings_pro_1 is added as [0] or so.. instead of key..

        array_splice($values, -1, 0, $g_pro_1);
        array_splice($values, -1, 0, $g_pro_2);

        return $values;
    }


    /**
     * Admin demo - greetings templates
     */
    // function demo_greetings_templates( $values ) {

    //     $g_templates = [
    //         'greetings-pro-1' => plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) . 'public/greetings/greetings-pro-1.php',
    //         'greetings-pro-2' => plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) . 'public/greetings/greetings-pro-2.php',
    //     ];

    //     if ( is_array($values) ) {
    //         $values = array_merge( $values, $g_templates );
    //     }

    //     return $values;
    // }

    function greetings_meta_editor( $values ) {

        $values = [
            'header_content',
            'main_content',
            'bottom_content'
        ];

        return $values;
    }


    // Register Settings.. greetings page
    function greetings_register( $values ) {

        $pro_values = [
            'ht_ctc_greetings_pro_1',
            'ht_ctc_greetings_pro_2'
        ];

        $values = array_merge( $values, $pro_values );

        return $values;
    }

    // greetings templates..
    function greetings_templates( $values ) {
        
        // keys are like the file names (inlude 'pro' keyword in the key)
        $pro_values = [
            'greetings-pro-1' => 'Greetings - Form',
            'greetings-pro-2' => 'Multi Agent'
        ];

        $values = array_merge( $values, $pro_values );

        return $values;
    }

    // custom URL: Desktop
    function url_structure_d_list($values) {
        
        $pro_values = [
            'custom_url' => __( 'Custom URL', 'click-to-chat-for-whatsapp' )
        ];

        $values = array_merge( $values, $pro_values );

        return $values;
    }

    // custom URL: Mobile
    function url_structure_m_list($values) {
        
        $pro_values = [
            'custom_url' => __( 'Custom URL', 'click-to-chat-for-whatsapp' )
        ];

        $values = array_merge( $values, $pro_values );

        return $values;
    }

    // custom URL: Desktop
    function custom_url_desktop() {

        $options = get_option('ht_ctc_chat_options');
        $dbrow = 'ht_ctc_chat_options';

        $custom_url_d = ( isset( $options['custom_url_d']) ) ? esc_attr( $options['custom_url_d'] ) :'';
        ?>

        <div class="row custom_url_desktop ctc_init_display_none">
            <div class="col s6">
                <p><?php _e( 'Desktop', 'click-to-chat-for-whatsapp' ); ?>: <?php _e( 'Custom URL', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
            <div class="input-field col s6">
                <input placeholder="" name="<?= $dbrow; ?>[custom_url_d]" value="<?= $custom_url_d ?>" id="custom_url_d" type="text">
                <label><?php _e( 'Desktop', 'click-to-chat-for-whatsapp' ); ?>: <?php _e( 'Custom URL', 'click-to-chat-for-whatsapp' ); ?></label>
                <p class="description"><?php _e( 'Add Full URL for Desktop', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
        </div>
        
        <?php
    }

    // custom URL: Mobile
    function custom_url_mobile() {

        $options = get_option('ht_ctc_chat_options');
        $dbrow = 'ht_ctc_chat_options';

        $custom_url_m = ( isset( $options['custom_url_m']) ) ? esc_attr( $options['custom_url_m'] ) :'';
        ?>

        <div class="row custom_url_mobile ctc_init_display_none">
            <div class="col s6">
                <p><?php _e( 'Mobile', 'click-to-chat-for-whatsapp' ); ?>: <?php _e( 'Custom URL', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
            <div class="input-field col s6">
                <input placeholder="" name="<?= $dbrow; ?>[custom_url_m]" value="<?= $custom_url_m ?>" id="custom_url_m" type="text">
                <label><?php _e( 'Mobile', 'click-to-chat-for-whatsapp' ); ?>: <?php _e( 'Custom URL', 'click-to-chat-for-whatsapp' ); ?></label>
                <p class="description"><?php _e( 'Add Full URL for Mobile', 'click-to-chat-for-whatsapp' ); ?></p>
            </div>
        </div>
        
        <?php
    }

    // // plugins updates
    // function plugin_updates() {

    //     // check_ajax_referer( 'ht_ctc_pro_nonce', 'ht_ctc_pro_nonce' );

    //     // $url = "https://holithemes.com/shop/?edd_action=get_version&item_id=5502&license=82e81b1969fa05cf12d9fd111d9ee48b&url=https://s1.techponder.com/";
	// 	// $response = wp_remote_post( $url );
        
    //     $license = trim( get_option( 'ht_ctc_pro_license_key' ) );

    //     $api_params = array(
	// 		'edd_action' => 'get_version',
	// 		'license'    => $license,
	// 		'item_id'    => HT_CTC_PRO_SL_ITEM_ID,
    //         'url'        => home_url()
	// 	);

	// 	$response = wp_remote_post( HT_CTC_PRO_SL_STORE_URL, array( 'timeout' => 45, 'sslverify' => false, 'body' => $api_params ) );
        
    //     $license_data = $response;

    //     $installed_version = HT_CTC_PRO_VERSION;
    //     $latest_version = '';

    //     // make sure the response came back okay
	// 	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

	// 		if ( is_wp_error( $response ) ) {
	// 			$message = $response->get_error_message();
	// 		} else {
	// 			$message = __( 'An error occurred, please try again. (not 200)' );
	// 		}

	// 	} else {

	// 		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// 		if ( false === $license_data->success ) {

	// 			switch( $license_data->error ) {

	// 				case 'expired' :

	// 					$message = sprintf(
	// 						__( 'Your license key expired on %s.' ),
	// 						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
	// 					);
	// 					break;

	// 				case 'disabled' :
	// 				case 'revoked' :

	// 					$message = __( 'Your license key has been disabled.' );
	// 					break;

	// 				case 'missing' :

	// 					$message = __( 'Invalid license.' );
	// 					break;

	// 				case 'invalid' :
	// 				case 'site_inactive' :

	// 					$message = __( 'Your license is not active for this URL.' );
	// 					break;

	// 				case 'item_name_mismatch' :

	// 					$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), HT_CTC_PRO_SL_ITEM_ID );
	// 					break;

	// 				case 'no_activations_left':

	// 					$message = __( 'Your license key has reached its activation limit.' );
	// 					break;

	// 				default :

	// 					$message = __( 'An error occurred, please try again. (default) 1. might be another product license key' );
	// 					break;
	// 			}

	// 		} else {
    //             if ($license_data->new_version) {
    //                 $latest_version = $license_data->new_version;
    //             }
    //         }
            
	// 	}
        
    //     // if not an error/issue
    //     if ( empty( $message ) ) {

    //         $message = "your plugin is up to date: $latest_version";
    //         if ('' == $latest_version) {
    //             $message = "unable to get the latest version, please contact plugin support";
    //         }
    //         if ($latest_version !== $installed_version) {
    //             $message = "Please update the plugin";
    //         }

	// 	}

    //     $r = array(
    //         'message' => $message,
    //         'license_data' => $license_data,
    //     );

    //     // $data = 'test';
    //     wp_send_json_success($r);

    //     // ends early, this wont runs
    //     wp_die();

    // }



}

new HT_CTC_PRO_Admin_Hooks();

endif; // END class_exists check