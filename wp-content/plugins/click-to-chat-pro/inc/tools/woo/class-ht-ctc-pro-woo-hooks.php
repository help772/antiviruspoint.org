<?php
/**
 * WooCommerce related hooks
 *  position type
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
        add_filter( 'ht_ctc_fh_woo_single_product', array($this, 'woo_single_product') );

        // woo greetings - single product pages
        add_filter( 'ht_ctc_fh_greetings', array($this, 'woo_single_greetings') );
        
        // woo after shop settings
        add_filter( 'ht_ctc_fh_woo_shop', array($this, 'woo_shop') );
    }

    // single greetings.. 
    function woo_single_greetings( $ht_ctc_greetings ) {
        
        
        // if woocommerce single product page
        if ( function_exists( 'is_product' ) && function_exists( 'wc_get_product' )) {
            if ( is_product() ) {
                
                $woo_greetings_options = get_option('ht_ctc_woo_options');

                $name = '';
                $price = '';
                $regular_price = '';
                $sku = '';
                $price_formatted = '';
                
                $product = wc_get_product();

                if ( is_object($product) && method_exists($product, 'get_name') ) {
                    $name = $product->get_name();
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

                $page_id = get_the_ID();
                $ht_ctc_pagelevel = get_post_meta( $page_id, 'ht_ctc_pagelevel', true );

                // // greetings template (if default is selected it will be like blank '' )
                // if ( isset( $woo_greetings_options['woo_single_greetings_template'] ) && '' !== $woo_greetings_options['woo_single_greetings_template'] ) {
                    
                //     $ht_ctc_greetings['greetings_template'] = esc_attr( $woo_greetings_options['woo_single_greetings_template'] );

                //     // if template change, then change path
                //     if ( false !== strpos( $ht_ctc_greetings['greetings_template'], 'pro' ) ) {
                //         $ht_ctc_greetings['path'] = plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) . 'public/greetings/' . $ht_ctc_greetings['greetings_template']. '.php';
                //     } else {
                //         $ht_ctc_greetings['path'] = plugin_dir_path( HT_CTC_PLUGIN_FILE ) . 'new/inc/greetings/' . $ht_ctc_greetings['greetings_template']. '.php';
                //     }
                // }

                // // header_content
                // if ( isset( $woo_greetings_options['woo_single_header_content'] ) && '' !== $woo_greetings_options['woo_single_header_content'] ) {
                //     $ht_ctc_greetings['header_content'] = esc_attr( $woo_greetings_options['woo_single_header_content'] );
                //     $ht_ctc_greetings['header_content'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['header_content'], 'Click to Chat for WhatsApp', 'woo_single_header_content' );
                // }

                // // main content
                // if ( isset( $woo_greetings_options['woo_single_main_content'] ) && '' !== $woo_greetings_options['woo_single_main_content'] ) {
                //     $ht_ctc_greetings['main_content'] = esc_attr( $woo_greetings_options['woo_single_main_content'] );
                //     $ht_ctc_greetings['main_content'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['main_content'], 'Click to Chat for WhatsApp', 'woo_single_main_content' );
                // }

                // // bottom content
                // if ( isset( $woo_greetings_options['woo_single_bottom_content'] ) && '' !== $woo_greetings_options['woo_single_bottom_content'] ) {
                //     $ht_ctc_greetings['bottom_content'] = esc_attr( $woo_greetings_options['woo_single_bottom_content'] );
                //     $ht_ctc_greetings['bottom_content'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['bottom_content'], 'Click to Chat for WhatsApp', 'woo_single_bottom_content' );
                // }

                // // call to action
                // if ( isset( $woo_greetings_options['woo_single_g_call_to_action'] ) && '' !== $woo_greetings_options['woo_single_g_call_to_action'] ) {
                //     $ht_ctc_greetings['call_to_action'] = esc_attr( $woo_greetings_options['woo_single_g_call_to_action'] );
                //     $ht_ctc_greetings['call_to_action'] = apply_filters( 'wpml_translate_single_string', $ht_ctc_greetings['call_to_action'], 'Click to Chat for WhatsApp', 'woo_single_g_call_to_action' );
                // }

                // variables works in default pre_filled also for woo pages.

                if (isset($ht_ctc_greetings['header_content']) && '' !== $ht_ctc_greetings['header_content'] ) {
                    $ht_ctc_greetings['header_content'] = str_replace( array('{product}', '{{price}}', '{price}', '{regular_price}', '{sku}' ),  array( $name, $price_formatted, $price, $regular_price, $sku ), $ht_ctc_greetings['header_content'] );
                }
                if (isset($ht_ctc_greetings['main_content']) && '' !== $ht_ctc_greetings['main_content'] ) {
                    $ht_ctc_greetings['main_content'] = str_replace( array('{product}', '{{price}}', '{price}', '{regular_price}', '{sku}' ),  array( $name, $price_formatted, $price, $regular_price, $sku ), $ht_ctc_greetings['main_content'] );
                }
                if (isset($ht_ctc_greetings['bottom_content']) && '' !== $ht_ctc_greetings['bottom_content'] ) {
                    $ht_ctc_greetings['bottom_content'] = str_replace( array('{product}', '{{price}}', '{price}', '{regular_price}', '{sku}' ),  array( $name, $price_formatted, $price, $regular_price, $sku ), $ht_ctc_greetings['bottom_content'] );
                }
                if (isset($ht_ctc_greetings['call_to_action']) && '' !== $ht_ctc_greetings['call_to_action'] ) {
                    $ht_ctc_greetings['call_to_action'] = str_replace( array('{product}', '{{price}}', '{price}', '{regular_price}', '{sku}' ),  array( $name, $price_formatted, $price, $regular_price, $sku ), $ht_ctc_greetings['call_to_action'] );
                }


            }
        }

        return $ht_ctc_greetings;
    }


    function woo_single_product( $values ) {

        $options = get_option('ht_ctc_chat_options');
        $woo_options = get_option('ht_ctc_woo_options');

        // business hours - enable.. 
        $bh = (isset($options['bh'])) ? esc_attr($options['bh']) : '';

        if ( 'timebase' == $bh && isset( $woo_options['woo_apply_business_hours']) ) {
            $values['single_schedule'] = 'yes';
        }

        return $values;
    }

    function woo_shop( $values ) {

        $options = get_option('ht_ctc_chat_options');
        $woo_options = get_option('ht_ctc_woo_options');
        
        $bh = (isset($options['bh'])) ? esc_attr($options['bh']) : '';

        if ( 'timebase' == $bh && isset( $woo_options['woo_apply_business_hours']) ) {
            $values['shop_schedule'] = 'yes';
        }

        return $values;
    }



}

new HT_CTC_PRO_Woo_Admin_Hooks();

endif; // END class_exists check