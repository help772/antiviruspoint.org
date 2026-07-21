<?php
/**
 * demo
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HT_CTC_PRO_Hooks' ) ) :

class HT_CTC_PRO_Hooks {

    // Click to Chat Files - github released version / branch: update value at HT_CTC_PRO_LOAD_FILES_TAG at inc/class-ht-ctc-pro.php
    public $ctc_files_version = '1.0';

    public $ctc_files_base_url = '';

    // intl input js (at ctc files)
    public $intl_init_js = '';

    public function __construct() {


        if ( defined( 'HT_CTC_PRO_LOAD_FILES_TAG' ) ) {
            $this->ctc_files_version = HT_CTC_PRO_LOAD_FILES_TAG;
        }

        $this->defaults();
        
        $this->hooks();
    }

    /**
     * defaults
     */
    public function defaults() {

        $os = get_option('ht_ctc_othersettings');
        
        if ( defined('HT_CTC_FILES_PLUGIN_FILE') ) {
            // click-to-chat-files plugin is installed.
            $this->ctc_files_base_url = plugins_url( '/', HT_CTC_FILES_PLUGIN_FILE );
        } else {
            // click-to-chat-files plugin - github.
            $this->ctc_files_base_url = "https://cdn.jsdelivr.net/gh/holithemes/click-to-chat-files@{$this->ctc_files_version}/";
        }

        // js
        $this->intl_init_js = 'intl-init.js';

        if ( defined('HT_CTC_PRO_DEBUG_MODE')  ) {
            // version (release)
            $this->intl_init_js = 'intl-init.dev.js';
        }
        
    }
    
    /**
     * Hooks
     */
    public function hooks() {
        
        add_filter( 'ht_ctc_fh_chat', array($this, 'chat_settings') );
        add_filter( 'ht_ctc_fh_ctc', array($this, 'ctc_settings') );
        add_filter( 'ht_ctc_fh_variables', array($this, 'ctc_variables') );

        add_filter( 'ht_ctc_ah_scripts_before', array($this, 'scripts') );
        // add_filter( 'ht_ctc_ah_scripts_after', array($this, 'scripts_after') );

        add_filter( 'ht_ctc_fh_greetings_start', [$this, 'greetings_dialog_page_level'] );
        // add_filter( 'ht_ctc_fh_greetings', [$this, 'greetings_dialog_page_level'] );


        add_filter( 'ht_ctc_fh_greetings', [$this, 'greetings_dialog'] );

        // c/g/s
        add_filter( 'ht_ctc_fh_position_type', array($this, 'position_type') , 10, 2 );
        add_filter( 'ht_ctc_fh_position_type_mobile', array($this, 'position_type_mobile') , 10, 2 );

        // add_filter( 'the_content', array($this, 'after_content') );

    }


    // function after_content( $content ) {
    //     // $style = 2;
    //     // $sc = "[ht-ctc-chat style=$style]";
    //     $sc = "[ht-ctc-chat]";
    //     // before post content
    //     $content = $sc . $content;
    //     // after post content
    //     $content .= $sc;
    //     return $content;
    // }


    /**
     * greetings dialog: overwrites.
     * - woo single page template check alone. (only template update. if need path changes does later)
     * - page level settings
     */
    function greetings_dialog_page_level( $ht_ctc_greetings ) {

        $page_id = get_the_ID();
        // $page_id = get_queried_object_id();

        // $object_id = get_queried_object_id();
        // if (0 == $object_id || '' == $object_id) {
        //     $page_id = get_the_ID();
        // } else {
        //     $page_id = $object_id;
        // }

        // is shop page
        if ( class_exists( 'WooCommerce' ) && function_exists( 'is_shop') && function_exists( 'wc_get_page_id') && is_shop() ) {
            $page_id = wc_get_page_id( 'shop' );
        }

        // woocommerce single product pages
        if ( function_exists( 'is_product' ) && function_exists( 'wc_get_product' )) {
            if ( is_product() ) {
                $woo_greetings_options = get_option('ht_ctc_woo_options');

                $name = '';
                $price = '';
                $regular_price = '';
                $sku = '';
                
                $product = wc_get_product();

                if ( is_object($product) && method_exists($product, 'get_name') ) {
                    $name = $product->get_name();
                    // $title = $product->get_title();
                    $price = $product->get_price();
                    $regular_price = $product->get_regular_price();
                    $sku = $product->get_sku();
                }

                // greetings template (if default is selected it will be like blank '' )
                if ( isset( $woo_greetings_options['woo_single_greetings_template'] ) && '' !== $woo_greetings_options['woo_single_greetings_template'] ) {
                    $ht_ctc_greetings['greetings_template'] = esc_attr( $woo_greetings_options['woo_single_greetings_template'] );

                    // if template change, then change path
                    if ( false !== strpos( $ht_ctc_greetings['greetings_template'], 'pro' ) ) {
                        $ht_ctc_greetings['path'] = plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) . 'public/greetings/' . $ht_ctc_greetings['greetings_template']. '.php';
                    } else {
                        $ht_ctc_greetings['path'] = plugin_dir_path( HT_CTC_PLUGIN_FILE ) . 'new/inc/greetings/' . $ht_ctc_greetings['greetings_template']. '.php';
                    }
                }

                // header_content
                if ( isset( $woo_greetings_options['woo_single_header_content'] ) && '' !== $woo_greetings_options['woo_single_header_content'] ) {
                    $ht_ctc_greetings['header_content'] = esc_attr( $woo_greetings_options['woo_single_header_content'] );
                    $ht_ctc_greetings['header_content'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['header_content'], 'Click to Chat for WhatsApp', 'woo_single_header_content' );
                }

                // main content
                if ( isset( $woo_greetings_options['woo_single_main_content'] ) && '' !== $woo_greetings_options['woo_single_main_content'] ) {
                    $ht_ctc_greetings['main_content'] = esc_attr( $woo_greetings_options['woo_single_main_content'] );
                    $ht_ctc_greetings['main_content'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['main_content'], 'Click to Chat for WhatsApp', 'woo_single_main_content' );
                }

                // bottom content
                if ( isset( $woo_greetings_options['woo_single_bottom_content'] ) && '' !== $woo_greetings_options['woo_single_bottom_content'] ) {
                    $ht_ctc_greetings['bottom_content'] = esc_attr( $woo_greetings_options['woo_single_bottom_content'] );
                    $ht_ctc_greetings['bottom_content'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['bottom_content'], 'Click to Chat for WhatsApp', 'woo_single_bottom_content' );
                }

                // call to action
                if ( isset( $woo_greetings_options['woo_single_g_call_to_action'] ) && '' !== $woo_greetings_options['woo_single_g_call_to_action'] ) {
                    $ht_ctc_greetings['call_to_action'] = esc_attr( $woo_greetings_options['woo_single_g_call_to_action'] );
                    $ht_ctc_greetings['call_to_action'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['call_to_action'], 'Click to Chat for WhatsApp', 'woo_single_g_call_to_action' );
                }

            }
        }


        // page level settings
        $ht_ctc_pagelevel = get_post_meta( $page_id, 'ht_ctc_pagelevel', true );

        if ( isset( $ht_ctc_pagelevel['enable_greetings']) ) {
            $ht_ctc_greetings['greetings_template'] = (isset($ht_ctc_pagelevel['greetings_template'])) ? esc_attr($ht_ctc_pagelevel['greetings_template']) : $ht_ctc_greetings['greetings_template'];
            $ht_ctc_greetings['header_content'] = (isset($ht_ctc_pagelevel['header_content'])) ? esc_attr($ht_ctc_pagelevel['header_content']) : $ht_ctc_greetings['header_content'];
            $ht_ctc_greetings['main_content'] = (isset($ht_ctc_pagelevel['main_content'])) ? esc_attr($ht_ctc_pagelevel['main_content']) : $ht_ctc_greetings['main_content'];
            $ht_ctc_greetings['bottom_content'] = (isset($ht_ctc_pagelevel['bottom_content'])) ? esc_attr($ht_ctc_pagelevel['bottom_content']) : $ht_ctc_greetings['bottom_content'];
            $ht_ctc_greetings['call_to_action'] = (isset($ht_ctc_pagelevel['g_call_to_action'])) ? esc_attr($ht_ctc_pagelevel['g_call_to_action']) : $ht_ctc_greetings['call_to_action'];
        }

        return $ht_ctc_greetings;
    }


    /**
     * greetings dialog - ht_ctc_greetings
     * 
     * path update.. if selected pro template
     */
    function greetings_dialog( $ht_ctc_greetings ) {

        // $greetings = get_option('ht_ctc_greetings_options');

        // change path if the template is in pro (if the template have pro)
        if ( false !== strpos( $ht_ctc_greetings['greetings_template'], 'pro' ) ) {
            $ht_ctc_greetings['path'] = plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) . 'public/greetings/' . $ht_ctc_greetings['greetings_template']. '.php';
        }

        return $ht_ctc_greetings;
    }


    /**
     * ht_ctc_chat
     */
    function chat_settings( $ht_ctc_chat ) {
        
        
        $options = get_option('ht_ctc_chat_options');
        $page_id = get_the_ID();

        // page level
        $ht_ctc_pagelevel = get_post_meta( $page_id, 'ht_ctc_pagelevel', true );

        // change style - Desktop
        if( isset($ht_ctc_pagelevel['style_desktop']) ) {
            $ht_ctc_chat['style_desktop'] = esc_attr( $ht_ctc_pagelevel['style_desktop'] );
        }

        // change style - Mobile
        if( isset($ht_ctc_pagelevel['style_mobile']) ) {
            $ht_ctc_chat['style_mobile'] = esc_attr( $ht_ctc_pagelevel['style_mobile'] );
        }


        return $ht_ctc_chat;
    }

    /**
     * change directly at ctc
     * ht_ctc_chat_var
     */
    function ctc_settings( $ctc ) {
        
        $options = get_option('ht_ctc_chat_options');
        $os = get_option('ht_ctc_othersettings');
        $greetings = get_option('ht_ctc_greetings_options');
        $greetings_settings = get_option('ht_ctc_greetings_settings');
        $g1_pro_options = get_option( 'ht_ctc_greetings_pro_1' );
        
        // todo: will remove this.. 
        $add_ajaxurl = 'yes';

        // page level
        $page_id = get_the_ID();

        // is shop page
        if ( class_exists( 'WooCommerce' ) && function_exists( 'is_shop') && function_exists( 'wc_get_page_id') && is_shop() ) {
            $page_id = wc_get_page_id( 'shop' );
        }

        $ht_ctc_pagelevel = get_post_meta( $page_id, 'ht_ctc_pagelevel', true );

        // random number (array)
        $r_nums = (isset($options['r_nums'])) ? $options['r_nums'] : '' ;

        if ( is_array($r_nums) ) {
            
            if ( isset($ht_ctc_pagelevel['number']) && '' !== $ht_ctc_pagelevel['number'] ) {
                // as page level number is added. then no need to check for random number.
                $ctc['r_nums_overwrite'] = 'page';
            } else {
                // if page level number is not added. then only check for random number.

                // add main number
                $r_nums['main'] = $ctc['number'];

                $r_nums = array_filter($r_nums);
                $r_nums = array_values($r_nums);
                $r_nums = array_map('esc_attr', $r_nums );

                if ( isset($r_nums[0]) ) {

                    $count = count($r_nums);
                    for ($i=0; $i < $count ; $i++) {
                        if ( class_exists( 'HT_CTC_Formatting' ) && method_exists( 'HT_CTC_Formatting', 'wa_number' ) ) {
                            $r_nums[$i] = HT_CTC_Formatting::wa_number( $r_nums[$i] );
                        } else {
                            //fallback if main plugin not updated.
                            $r_nums[$i] = preg_replace('/\D/', '', $r_nums[$i] );
                            $r_nums[$i] = ltrim( $r_nums[$i], '0' );
                        }
                    }

                    // $ctc - random numbers
                    $ctc['r_nums'] = $r_nums;

                    //if r_num_type is not set then default is random.
                    $r_num_type = (isset($options['r_num_type'])) ? esc_attr($options['r_num_type']) : '';

                    // if r_num_type == "sequence" then set the sequence number.
                    if ('sequential' === $r_num_type) {
                        // Fetch r_sqx directly from DB instead of relying on stale $options
                        $r_sqx = get_option('ht_ctc_r_sqx', 0);
                        $r_sqx = esc_attr($r_sqx);
                        
                        $ctc['r_sqx'] = is_numeric($r_sqx) ? $r_sqx : '0';
                    }
                    
                }
            }
            

        }


        // z index
        $z_index = (isset($os['zindex'])) ? esc_attr($os['zindex']) : '99999999';
        $ctc['z_index'] = $z_index;

        // timezone from WordPress general settings.
        $ctc['tz'] = get_option('gmt_offset');

        $ctc['bh'] = (isset($options['bh'])) ? esc_attr($options['bh']) : '';

        $ctc['timedelay'] = (isset($options['timedelay'])) ? esc_attr($options['timedelay']) : '';
        $ctc['timedelay'] = (isset($ht_ctc_pagelevel['timedelay'])) ? esc_attr($ht_ctc_pagelevel['timedelay']) : $ctc['timedelay'];

        $ctc['scroll'] = (isset($options['scroll'])) ? esc_attr($options['scroll']) : '';
        $ctc['scroll'] = (isset($ht_ctc_pagelevel['scroll'])) ? esc_attr($ht_ctc_pagelevel['scroll']) : $ctc['scroll'];


        /**
         * if display_countries is selected but if display_countries_list is empty, then it will display to all countries.
         */
        $country_schedule = '';
        $display_countries = ( isset( $options['display_countries']) ) ? esc_attr( $options['display_countries'] ) : '';
        if ( 'selected' == $display_countries ) {
            $display_countries_list = ( isset( $options['display_countries_list']) ) ? $options['display_countries_list'] : [];
            // is array and not empty
            if ( is_array($display_countries_list) && !empty($display_countries_list) ) {
                $country_schedule = 'yes';
            }
        }


        // scheduled based on week_days, time_range, time_dealy, page_scroll
        if ( 'timebase' == $ctc['bh'] || '' !== $ctc['timedelay'] || '' !== $ctc['scroll'] || 'yes' == $country_schedule) {
            $ctc['schedule'] = 'yes';
        }

        // Display based on user base
        $display_user_base = ( isset( $options['display_user_base']) ) ? esc_attr( $options['display_user_base'] ) : 'all_users';
        
        // previous version compatibility
        if ( isset($options['logged_in_only']) ) {
            $display_user_base = 'logged_in';
        }

        if ( 'logged_in' == $display_user_base || 'logged_out' == $display_user_base ) {
            $ctc['schedule'] = 'yes';
            $ctc['display_user_base'] = $display_user_base;
            $add_ajaxurl = 'yes';
        }

        /**
         * header offline status badge.
         * if no color is added. dont display the badge when offline.
         */
        if ( isset($greetings['g_header_offline_status_color']) && '' !== $greetings['g_header_offline_status_color'] ) {
            $ctc['offline_badge_color'] = esc_attr($greetings['g_header_offline_status_color']);
        }


        $url_structure_d = ( isset( $options['url_structure_d'] ) ) ? esc_attr($options['url_structure_d']) : '';
        $url_structure_m = ( isset( $options['url_structure_m'] ) ) ? esc_attr($options['url_structure_m']) : '';

        $custom_url_d = ( isset( $options['custom_url_d'] ) ) ? esc_attr($options['custom_url_d']) : '';
        $custom_url_m = ( isset( $options['custom_url_m'] ) ) ? esc_attr($options['custom_url_m']) : '';

        // $custom_url_d = (isset($ht_ctc_pagelevel['custom_url_d'])) ? esc_attr($ht_ctc_pagelevel['custom_url_d']) : $custom_url_d;
        // $custom_url_m = (isset($ht_ctc_pagelevel['custom_url_m'])) ? esc_attr($ht_ctc_pagelevel['custom_url_m']) : $custom_url_m;
        
        /**
         * custom url: page level settings - if at page level settings custom link is added then - url_structure have to be custom link
         * if page level whatsapp number is added. then it will give high priority to whatsapp number then custom url.
         * 
         * custom link: priority: 
         *  page level - whatsapp number
         *  page level - custom link
         *  global - custom link
         *  global - whatsapp number
         */
        if (!isset($ht_ctc_pagelevel['number']) && isset($ht_ctc_pagelevel['custom_url_d']) && '' !== $ht_ctc_pagelevel['custom_url_d'] ) {
            $custom_url_d = esc_attr($ht_ctc_pagelevel['custom_url_d']);
            $url_structure_d = 'custom_url';
        }

        if (!isset($ht_ctc_pagelevel['number']) && isset($ht_ctc_pagelevel['custom_url_m']) && '' !== $ht_ctc_pagelevel['custom_url_m'] ) {
            $custom_url_m = esc_attr($ht_ctc_pagelevel['custom_url_m']);
            $url_structure_m = 'custom_url';
        }


        // custom url - desktop
        if ( 'custom_url' == $url_structure_d && '' !== $custom_url_d ) {
            if ( function_exists('wp_http_validate_url') && wp_http_validate_url($custom_url_d) ) {
                $ctc['custom_url_d'] = $custom_url_d;
            } else {
                // if not valid url
                $ctc['custom_url_d'] = '';
            }
        }

        // custom url - mobile
        if ( 'custom_url' == $url_structure_m && '' !== $custom_url_m ) {
            if ( function_exists('wp_http_validate_url') && wp_http_validate_url($custom_url_m) ) {
                $ctc['custom_url_m'] = $custom_url_m;
            } else {
                $ctc['custom_url_m'] = '';
            }
        }





        // gads conversation
        $gads_conversion_id = (isset($os['gads_conversion_id'])) ? esc_attr($os['gads_conversion_id']) : '' ;
        $gads_conversion_label = (isset($os['gads_conversion_label'])) ? esc_attr($os['gads_conversion_label']) : '' ;

        if ( '' !== $gads_conversion_id && '' !== $gads_conversion_label ) {

            // if AW- is added then remove it. 
            $gads_conversion_id = str_replace('AW-', '', $gads_conversion_id);

            $ctc['gads_conversation'] = "AW-$gads_conversion_id/$gads_conversion_label";

            // unset callback gtag_report_conversion
            unset( $ctc['ads'] );
        }

        // fb conversation api 
        if ( isset( $os['fb_conversion'] )) {
            $ctc['fb_conversion'] = 'y';
            $add_ajaxurl = 'yes';
        }

        // greetings
        $g_time_action = (isset($greetings_settings['g_time_action'])) ? esc_attr($greetings_settings['g_time_action']) : '' ;
        $g_scroll_action = (isset($greetings_settings['g_scroll_action'])) ? esc_attr($greetings_settings['g_scroll_action']) : '' ;
        $g_no_reopen = (isset($greetings_settings['g_no_reopen'])) ? esc_attr($greetings_settings['g_no_reopen']) : '' ;
        $greetings_template = (isset($greetings['greetings_template'])) ? esc_attr($greetings['greetings_template']) : '' ;
        
        if ('' !== $g_time_action) {
            $ctc['g_time_action'] = $g_time_action;
        }

        if ('' !== $g_scroll_action) {
            $ctc['g_scroll_action'] = $g_scroll_action;
        }

        if ('' !== $g_no_reopen) {
            $ctc['g_no_reopen'] = '1';
        }

        // Greetings dialog - pro - 1 - Form
        if ('greetings-pro-1' == $greetings_template) {

            $form_email = (isset($g1_pro_options['email'])) ? esc_attr($g1_pro_options['email']) : '' ;
            $form_webhook = (isset($g1_pro_options['webhook'])) ? esc_attr($g1_pro_options['webhook']) : '' ;

            if ('' !== $form_email) {
                $ctc['g1_form_email'] = 'y';
                $add_ajaxurl = 'yes';
            }

            if ('' !== $form_webhook) {
                $ctc['g1_form_webhook'] = $form_webhook;
            }

            


        }

        

        /**
         * bh - always / timebase
         *  always - always online. no need to set any value.
         *  timebase - set values.
         */
        if ( 'timebase' == $ctc['bh'] ) {
            
            $i = 1;
            $days = array(
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday',
            );

            /**
             * d1 monday, .. 
             * if d1 not set - monday is offline
             * d1 is 'on' - monday is online. 
             *  if d1_st, d1_et is not set (no time set) then 24hours open.
             *  d1_st monday start time
             *  d1_et monday end time
             * 
             * if bh is 'always' then 24*7 online
             * if bh is 'timebase' then
             *  if days are not set (d1, d2, .. ) - offline on that day
             *  if days are set (d1, d2, ..) and time is not set (d1_st, d1_et, ..) - then its 24 hours online on that day
             */
            foreach ($days as $day ) {

                $d = "d$i";
                $st = $day.'_st';
                $et = $day.'_et';
                $d_st = 'd'.$i.'_st';
                $d_et = 'd'.$i.'_et';

                $n = (isset($options[$day])) ? esc_attr($options[$day]) : '';
                $n1 = (isset($options[$st])) ? esc_attr($options[$st]) : '';
                $n2 = (isset($options[$et])) ? esc_attr($options[$et]) : '';

                if ( '' !== $n ) {
                    $ctc[$d] = $n;
                }

                if ( '' !== $n1 ) {
                    $ctc[$d_st] = $n1;
                }

                if ( '' !== $n2 ) {
                    $ctc[$d_et] = $n2;
                }

                $i++;
            }


            // offline - hide, call to action, number

            if(isset($options['off_hide'])) {
                // offline hided.
                $ctc['off_hide'] = 'y';
            } else {
                // if not hided
                
                // offline number
                $off_num = (isset($options['off_num'])) ? esc_attr($options['off_num']) : '';
                if ( '' !== $off_num ) {
                    $off_num = preg_replace('/\D/', '', $off_num );
                    $off_num = ltrim( $off_num, '0' );

                    $ctc['off_num'] = "$off_num";
                }

                // offline call to action
                $off_cta = (isset($options['off_cta'])) ? esc_attr($options['off_cta']) : '';
                if ( '' !== $off_cta ) {
                    $ctc['off_cta'] = "$off_cta";
                }


            }
        }

        // add ajaxurl
        // if ('yes' == $add_ajaxurl) {
            $ctc['ajaxurl'] = admin_url('admin-ajax.php');
        // }

        // nonce
        $nonce = wp_create_nonce('ht_ctc_nonce');
        $ctc['nonce'] = $nonce;

        return $ctc;
    }


    /**
     * ctc variables
     * 
     * localize scripts: ht_ctc_variables
     */
    function ctc_variables( $values ) {
        
        $options = get_option('ht_ctc_chat_options');
        $os = get_option('ht_ctc_othersettings');
        $g_ads_params = ( isset($os['g_ads_params']) && is_array($os['g_ads_params']) ) ? array_map( 'esc_attr', $os['g_ads_params'] ) : '';

        $g1_pro_options = get_option( 'ht_ctc_greetings_pro_1' );

        // google ads conversion params
        if ( is_array($g_ads_params) && isset($g_ads_params[0]) ) {

            foreach ($g_ads_params as $param ) {
                $param_options = ( isset($os[$param]) ) ? $os[$param] : [];
                $key = ( isset($param_options['key']) ) ? esc_attr($param_options['key']) : '';
                $value = ( isset($param_options['value']) ) ? esc_attr($param_options['value']) : '';
                
                if ( !empty($key) && !empty($value) ) {
                    $values['g_ads_params'][] = $param;
                    $values[$param] = [
                        'key' => $key,
                        'value'=> $value,
                    ];
                }

            }
        }

        // display based on country code.
        $display_countries = ( isset( $options['display_countries']) ) ? esc_attr( $options['display_countries'] ) : 'all';
        if ( 'selected' == $display_countries ) {
            $display_countries_list = ( isset( $options['display_countries_list']) ) ? $options['display_countries_list'] : [];

            // is array and not empty - if display_countries is selected but if display_countries_list is empty, then it will display to all countries.
            if ( is_array($display_countries_list) && !empty($display_countries_list) ) {
                $display_countries_list = array_map('esc_attr', $display_countries_list);
                $values['display_countries_list'] = $display_countries_list;
            }
        }

        /**
         * number.js loads if intl input is added. (added using wp_enqueue_script)
         * if greetings form is added.
         *  check if in greetings form. intl number filed is added. if added then add intlTelInput js and css file paths.
         *  (isset greetings dialog is not checking. main or at page level greetings form might be added.)
         * 
         * 
         * todo: at docs.. add like intl input library will load only if intl input field is added in the form.. and that too we can add delay to load the intl input library.
         * 
         * load_type_intltel_files: how to load the intl files. (intlTelInput.min.css, intlTelInput.min.js)
         *  nodelay: load directly using wp_enqueue_script..
         *  delay_1: load using number.js file. after page loaded.( after ideal time and wait for another 5 seconds OR if user interacted the form. )
         *  delay_2: load using number.js after user interacted the form.
         * 
         * intltel_links: intltel input files: might be from click-to-chat-files plugin if active or from this click-to-chat-files github using jsdelivr.
         *  utils.js
         *  intlTelInput.min.css
         *  intlTelInput.min.js
         * 
         * initialise the intlTelInput are from click-to-chat-files
         */
        if ( isset($g1_pro_options['is_load_intltelinput']) && 'y' == esc_attr($g1_pro_options['is_load_intltelinput']) ) {

            // intl_separate_dialcode. is true then add.
            if (isset($g1_pro_options['intl_separate_dialcode'])) {
                $values['intl_separate_dialcode'] = 'y';
            }

            $load_type_intltel_files = ( isset($g1_pro_options['load_type_intltel_files']) ) ? esc_attr($g1_pro_options['load_type_intltel_files']) : 'delay_2';

            $values['intl_files_load_type'] = $load_type_intltel_files;

            // intl language
            $intl_language = ( isset($g1_pro_options['intl_language']) ) ? esc_attr($g1_pro_options['intl_language']) : '';

            if ( 'auto' == $intl_language && function_exists('get_locale') ) {
                // get current page language..
                $current_lang = get_locale();
                $current_lang = substr($current_lang, 0, 2);
                $intl_language = $current_lang;
            }

            // if not empty and not default
            if ( '' !== $intl_language && 'en' !== $intl_language && 'default' !== $intl_language ) {
                $values['intl_language'] = $intl_language;
                if ( defined('HT_CTC_FILES_PLUGIN_FILE') ) {
                    $values['intl_language_path'] =  plugins_url( "tools/intl/js/i18n/{$intl_language}/index.js", HT_CTC_FILES_PLUGIN_FILE );
                } else {
                    $values['intl_language_path'] = "{$this->ctc_files_base_url}tools/intl/js/i18n/{$intl_language}/index.js";
                }
            }

            // intl initial country
            $intl_initial_country = ( isset($g1_pro_options['intl_initial_country']) ) ? esc_attr($g1_pro_options['intl_initial_country']) : '';
            if ( '' !== $intl_initial_country ) {
                // if 'auto' get the country code from js.
                $values['intl_initial_country'] = strtolower($intl_initial_country);
            }

            if ( isset($g1_pro_options['load_type_intltel_files']) && 'nodelay' !== esc_attr($g1_pro_options['load_type_intltel_files']) ) {
                // intltel_links: if dealy.. then add the paths.
                $values['intl_css'] = "{$this->ctc_files_base_url}tools/intl/css/intlTelInput.min.css";
                $values['intl_init_js'] = "{$this->ctc_files_base_url}inc/assets/js/{$this->intl_init_js}";
                $values['intl_js'] = "{$this->ctc_files_base_url}tools/intl/js/intlTelInput.min.js";
            }

            // intltel_links: utils.js. required if no dealy or any delay.
            $values['intl_utils_js'] = "{$this->ctc_files_base_url}tools/intl/js/utils.js";
        }

        
        return $values;
    }


    /**
     * chat position type for Desktop
     * 
     * @param string $position_type - fixed/absolute - default: 'fixed' 
     * @param array $options - db values - ht_ctc_chat_options
     */
    public function position_type( $position_type, $options ) {

        // fixed / absolute
        $position_type = ( isset( $options['position_type']) ) ? esc_attr( $options['position_type'] ) : 'fixed';
        
        return $position_type;

    }

    /**
     * chat position type for Mobile
     */
    public function position_type_mobile( $position_type_mobile, $options ) {

        if (isset($options['same_settings'])) {
            // same as dekstop position
            $position_type_mobile = ( isset( $options['position_type']) ) ? esc_attr( $options['position_type'] ) : 'fixed';
        } else {
            $position_type_mobile = ( isset( $options['position_type_mobile']) ) ? esc_attr( $options['position_type_mobile'] ) : 'fixed';
        }

        return $position_type_mobile;
    }




    /**
     * scripts hook
     */
    public function scripts() {
        
        // if ( HT_CTC_VERSION < HT_CTC_PRO_CTC_REQUIRED_VERSION_TOWORK ) {
        //     return;
        // }
        
        $os = get_option('ht_ctc_othersettings');
        $g1_pro_options = get_option( 'ht_ctc_greetings_pro_1' );

        
        // true/false
        $load_app_js_bottom = apply_filters( 'ht_ctc_fh_load_app_js_bottom', true );

        // js
        $js = 'pro.js';
        $country_js = 'country.js';
        $number_js = 'number.js';

        if ( defined('HT_CTC_PRO_DEBUG_MODE')  ) {
            $js = 'pro.dev.js';
            $country_js = 'country.dev.js';
            $number_js = 'number.dev.js';
        }


        if ( defined( 'HT_CTC_PRO_PLUGIN_FILE' ) ) {


            $pro_js_depends = array( 'jquery' );

            
            // country js. only if selected countries and selected countries list is not empty.
            $options = get_option('ht_ctc_chat_options');
            $display_countries = ( isset( $options['display_countries']) ) ? esc_attr( $options['display_countries'] ) : 'all';
            if ( 'selected' == $display_countries ) {
                $display_countries_list = ( isset( $options['display_countries_list']) ) ? $options['display_countries_list'] : [];
                // is array and not empty
                if ( is_array($display_countries_list) && !empty($display_countries_list) ) {
                    wp_enqueue_script( 'ctc_pro_country_js', plugins_url( "public/assets/js/$country_js", HT_CTC_PRO_PLUGIN_FILE ), array( 'jquery' ), HT_CTC_PRO_VERSION, $load_app_js_bottom );
                }
            }


            // intl input
            $is_load_intltelinput = ( isset($g1_pro_options['is_load_intltelinput']) ) ? esc_attr($g1_pro_options['is_load_intltelinput']) : 'no';

            if ( 'y' == $is_load_intltelinput ) {

                $load_type_intltel_files = ( isset($g1_pro_options['load_type_intltel_files']) ) ? esc_attr($g1_pro_options['load_type_intltel_files']) : 'delay_2';

                $number_js_depends = array( 'jquery' );

                if ( 'nodelay' == $load_type_intltel_files ) {

                    // intltel_links: no delay. enqueue directly.
                    wp_enqueue_style( 'ctc_pro_intl_css', "{$this->ctc_files_base_url}tools/intl/css/intlTelInput.min.css");
                    wp_enqueue_script( 'ctc_pro_intl_js', "{$this->ctc_files_base_url}tools/intl/js/intlTelInput.min.js", '', $this->ctc_files_version, true );
                    wp_enqueue_script( 'ctc_pro_intl_init_js', "{$this->ctc_files_base_url}inc/assets/js/{$this->intl_init_js}", array( 'jquery' ), $this->ctc_files_version, true );

                    // $number_js_depends[] = 'intl_init_js';
                    $number_js_depends[] = 'ctc_pro_intl_js';
                }

                // intl js
                wp_enqueue_script( 'ctc_pro_number_js', plugins_url( "public/assets/js/$number_js", HT_CTC_PRO_PLUGIN_FILE ), $number_js_depends, HT_CTC_PRO_VERSION, true );

            }



            // pro js
            wp_enqueue_script( 'ctc_pro_js', plugins_url( "public/assets/js/$js", HT_CTC_PRO_PLUGIN_FILE ), $pro_js_depends, HT_CTC_PRO_VERSION, $load_app_js_bottom );


        }
    }

    /**
     * scripts after hook
     */
    // public function scripts_after() {

    // }



}

new HT_CTC_PRO_Hooks();

endif; // END class_exists check