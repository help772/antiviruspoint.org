<?php
/**
 * Default Values
 * 
 *  set the default values
 *  which stores in database options table
 *
 * @package ctc
 * @since 2.0
 * @from ht-ccw-register.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HT_CTC_PRO_DB' ) ) :

class HT_CTC_PRO_DB {


    public function __construct() {
        $this->db();
    }
    
    
    /**
     * based on condition.. update the db .. 
     *
     */
    public function db() {
       
        $this->ht_ctc_pro_plugin_details();

    }


    /**
     * name: ht_ctc_pro_plugin_details
     * 
     * don't preseve already existing values
     *  Always use update_option - override new values .. 
     * 
     * Add plugin Details to db 
     * Add plugin version to db - useful while updating plugin
     * 
     * 
     * v_  v3  3.1 as v3_1
     * 
     * first_install_time @since v1.4
     * 
     */
    public function ht_ctc_pro_plugin_details() {

        $time = time();

        // plugin details 
        $values = array(
            'version' => HT_CTC_PRO_VERSION,
            'first_install_time' => $time,
            'v1_2' => $time,
            'v1_4' => $time,
            'v2_5' => $time,
        );

        $db_values = get_option( 'ht_ctc_pro_plugin_details', array() );
        
        // extra safe instead of directly merge.
        $update_values = $values;
        if (is_array($db_values)) {
            $update_values = array_merge($values, $db_values);
        }

        /**
         * IMP: have to update version number.. 
         * (always use the latest value)
         */
        $update_values['version'] = HT_CTC_PRO_VERSION;

        update_option( 'ht_ctc_pro_plugin_details', $update_values );
    }




}

new HT_CTC_PRO_DB();

endif; // END class_exists check