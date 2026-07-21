<?php
/**
 * PRO Plugin
 *  
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HT_CTC_PRO' ) ) :

class HT_CTC_PRO {

    /**
     * singleton instance
     *
     * @var HT_CTC_PRO 
     */
    private static $instance = null;
    
    /**
     * main instance - HT_CTC
     *
     * @return HT_CTC_PRO instance
     * @since 1.0
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'click-to-chat-for-whatsapp' ), '1.0' );
    }
    
    public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'click-to-chat-for-whatsapp' ), '1.0' );
    }

    /**
     * constructor 
     * 
     * basic(), includes() -> include files
     * hooks()  -> run hooks 
     */
    public function __construct() {

        // dont load any thing before define_constants. even this plugin or other plugin hooks.
        $this->define_constants();

        $this->basic();
        $this->hooks();
    }


    /**
     * Define Constants
     */
    private function define_constants() {


        $os = get_option('ht_ctc_othersettings');


        /**
         * click to chat files repository tag/branch
         * release branch 1.  a few changes to ctc files will push changes to branches.. 
         *  use r1, r2, r3, r4, etc. 
         * if major changes to avoid cache issue create tag 1.1, 1.2, 1.3 etc and add. 
         */
        $ctc_files_tag = 'r1';
        if ( defined('HT_CTC_PRO_DEBUG_MODE')  ) {
            $ctc_files_tag = 'dev';
        }

        // Click to Chat Files - tag
        $this->define( 'HT_CTC_PRO_LOAD_FILES_TAG', $ctc_files_tag );

        // Click to Chat plugin - required version
        $this->define( 'HT_CTC_PRO_CTC_REQUIRED_VERSION', '4.4' );
        // some functions may stop working if not match
        $this->define( 'HT_CTC_PRO_CTC_REQUIRED_VERSION_TOWORK', '3.33' );

		// $this->define( 'HT_CTC_PRO_WP_MIN_VERSION', '4.6' );

		$this->define( 'HT_CTC_PRO_PLUGIN_BASENAME', plugin_basename( HT_CTC_PRO_PLUGIN_FILE ) );

	}

	/**
     * @uses this->define_constants
     * @param string $name Constant name
     * @param string.. $value Constant value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
	}

    /**
     * add the basic things
     * 
     * calling this before include, initilize other 
     * 
     * include, initilize files that needed before init
     * 
     * because this things may useful before  other things
     * 
     *  e.g. include, initialize files based on device, user settings
     */
    private function basic() {

        // include_once HT_CTC_PRO_PLUGIN_DIR .'new/inc/commons/class-ht-ctc-ismobile.php';
        
    }
    
    /**
     * Register hooks - when plugin activate, deactivate, uninstall
     * commented deactivation, uninstall hook - its not needed as now
     * 
     * plugins_loaded  - Check Diff - uses when plugin updates.
     * 
     * 
     * @note: Add at init - if 'values->HT_CTC_Values' is needed and works if load at init.
     */
    private function hooks() {

        include_once HT_CTC_PRO_PLUGIN_DIR .'inc/class-ht-ctc-pro-register.php';
        register_activation_hook( HT_CTC_PRO_PLUGIN_FILE, array( 'HT_CTC_PRO_Register', 'activate' )  );
        register_deactivation_hook( HT_CTC_PRO_PLUGIN_FILE, array( 'HT_CTC_PRO_Register', 'deactivate' )  );
        register_uninstall_hook(HT_CTC_PRO_PLUGIN_FILE, array( 'HT_CTC_PRO_Register', 'uninstall' ) );
        
        // init
        add_action( 'init', array( $this, 'init' ), 0 );
        add_action( 'ht_ctc_ah_init_before', array( $this, 'ctc_init' ), 0 );

        // when plugin updated - check version diff
        add_action('plugins_loaded', array( 'HT_CTC_PRO_Register', 'version_check' ) );
        
        // settings page link
        if ( defined( 'HT_CTC_VERSION' ) ) {
            add_filter( 'plugin_action_links_' . HT_CTC_PRO_PLUGIN_BASENAME, array( 'HT_CTC_PRO_Register', 'plugin_action_links' ) );
        }


    }


    /**
     * Init
     * if anything to work before init call at this->basic()
     */
    public function init() {
        
        if ( is_admin() ) {
            // TGM:
            include_once HT_CTC_PRO_PLUGIN_DIR .'inc/tools/tgm/tgm.php';
        } else {
        }
    }


    /**
     * CTC Init
     * @uses this->hooks() - using ht_ctc_ah_init_before hook - priority 0
     */
    public function ctc_init() {
        
        if ( is_admin() ) {
            include_once HT_CTC_PRO_PLUGIN_DIR .'admin/admin.php';
        } else {
            include_once HT_CTC_PRO_PLUGIN_DIR .'public/class-ht-ctc-pro-hooks.php';
        }

        // woo
        include_once HT_CTC_PRO_PLUGIN_DIR .'inc/tools/woo/woo.php';


    }

}

endif; // END class_exists check