<?php

namespace WcPaysafe\Compatibility\WPML;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  3.5.2
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Translations {
	
	public function hooks() {
		add_filter( 'wcml_gateway_text_keys_to_translate', array( $this, 'add_supported_settings_keys' ), 11 );
	}
	
	public function add_supported_settings_keys( $text_keys ) {
		$text_keys[] = 'save_card_text';
		
		return $text_keys;
	}
}