<?php
/*
Plugin Name: Click to Chat PRO
Plugin URI:  http://holithemes.com/plugins/click-to-chat/pro/
Description: PRO plugin - Addon for Click to Chat
Version:     2.13
Author:      HoliThemes
Author URI:  https://holithemes.com/plugins/click-to-chat/
Text Domain: click-to-chat-pro
GitHub Plugin URI:  https://github.com/holithemes/click-to-chat-pro/
*/

if ( ! defined( 'WPINC' ) ) {
	die('dont try to call this directly');
}

// ctc - Version - update version at readme 'Stable tag'
if ( ! defined( 'HT_CTC_PRO_VERSION' ) ) {
	define( 'HT_CTC_PRO_VERSION', '2.13' );
}

// define HT_CTC_PRO_PLUGIN_FILE
if ( ! defined( 'HT_CTC_PRO_PLUGIN_FILE' ) ) {
	define( 'HT_CTC_PRO_PLUGIN_FILE', __FILE__ );
}

// define HT_CTC_PRO_PLUGIN_DIR
if ( ! defined( 'HT_CTC_PRO_PLUGIN_DIR' ) ) {
	define( 'HT_CTC_PRO_PLUGIN_DIR', plugin_dir_path( HT_CTC_PRO_PLUGIN_FILE ) );
}

// added to ht-ctc-pro.php. later remove this. 
if ( ! defined( 'HT_CTC_PRO_CTC_REQUIRED_VERSION' ) ) {
	define( 'HT_CTC_PRO_CTC_REQUIRED_VERSION', '4.4' );
}
if ( ! defined( 'HT_CTC_PRO_CTC_REQUIRED_VERSION_TOWORK' ) ) {
	define( 'HT_CTC_PRO_CTC_REQUIRED_VERSION_TOWORK', '3.33' );
}


include_once HT_CTC_PRO_PLUGIN_DIR .'inc/class-ht-ctc-pro.php';

// create instance for the main file - HT_CTC_PRO
if ( class_exists( 'HT_CTC_PRO') ) {
	function ht_ctc_pro() {
		return HT_CTC_PRO::instance();
	}
	ht_ctc_pro();
}