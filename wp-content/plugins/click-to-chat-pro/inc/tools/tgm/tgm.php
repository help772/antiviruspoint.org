<?php
/**
 * Required plugins
 *  Click to Chat - https://wordpress.org/plugins/click-to-chat-for-whatsapp/
 */


if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HT_CTC_PRO_TGM' ) ) :

class HT_CTC_PRO_TGM {

    public function __construct() {
        $this->tgm();
    }


	/**
	 * call tgm only if required plugins is not active (checking using constants)
	 * or
	 * if not minimum required version
	 */
	function tgm() {
		
		// load tgm only if required pluging are not active or not update with required version
		if ( !defined('HT_CTC_VERSION') ) {
			$this->call_tgm();
		} else {
			// if defined (click to chat plugin is active ), but not min required version
			if ( version_compare( HT_CTC_VERSION, HT_CTC_PRO_CTC_REQUIRED_VERSION, '<' ) ) {
				$this->call_tgm();
			}
		}

	}

	// TGM start
	function call_tgm() {
		add_action( 'tgmpa_register', array($this, 'ht_ctc_pro_register_required_plugins') );
		include_once HT_CTC_PRO_PLUGIN_DIR .'inc/tools/tgm/class-tgm-plugin-activation.php';
	}




	function ht_ctc_pro_register_required_plugins() {

		$plugins = array(

			array(
				'name'      => 'Click to Chat',
				'slug'      => 'click-to-chat-for-whatsapp',
				'required'  => true,
				'version'  => HT_CTC_PRO_CTC_REQUIRED_VERSION,
			),

		);

		$config = array(
			'id'           => 'click-to-chat-pro',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'ctc-pro-required-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			
		);

		tgmpa( $plugins, $config );
	}


}

new HT_CTC_PRO_TGM();

endif; // END class_exists check