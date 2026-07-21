<?php
/**
 * 
 * WooCommerce related things - Entry point
 * 
 * @package Click to Chat PRO
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// init
add_action('init', function() {
    
    // if woocommece plugin in active (checking this way works as now)
    if ( class_exists( 'WooCommerce' ) ) {

        if ( is_admin() ) {
            include_once HT_CTC_PRO_PLUGIN_DIR .'inc/tools/woo/class-ht-ctc-pro-woo-admin-hooks.php';
        } else {
            // woo public
            include_once HT_CTC_PRO_PLUGIN_DIR .'inc/tools/woo/class-ht-ctc-pro-woo-hooks.php';
        }
    }

});