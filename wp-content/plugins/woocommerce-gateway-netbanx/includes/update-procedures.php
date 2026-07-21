<?php

namespace WcPaysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update procedure
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Update_Procedures {
	
	public $version;
	
	public function __construct() {
		$this->version = get_option( 'wc_paysafe_version', '3.2.1' );
	}
	
	/**
	 * Loads the hooks
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'check_version_and_update' ), 0 );
	}
	
	/**
	 * Checks the version and runs the update procedure
	 *
	 * @since 2.0
	 */
	public function check_version_and_update() {
		if ( ! defined( 'IFRAME_REQUEST' ) && $this->version !== WC_PAYSAFE_PLUGIN_VERSION ) {
			$this->update();
		}
	}
	
	/**
	 * Performs needed updates for the plugin for moving through version
	 *
	 * @since 2.0
	 */
	public function update() {
		
		// Since in 3.3.0 we changed the gateway ID to 'paysafe', we want to copy the plugin settings to the new gateway id.
		if ( version_compare( $this->version, '3.3.0', '<' ) && version_compare( WC_PAYSAFE_PLUGIN_VERSION, '3.3.0', '>=' ) ) {
			$current_settings = get_option( 'woocommerce_netbanx_settings', [] );
			
			if ( '' == $current_settings['api_key'] ) {
				$current_settings['integration'] = 'checkoutjs';
			}
			
			// Split the API Key credentials
			if ( 'hosted' == $current_settings['integration'] ) {
				$key = $current_settings['api_key'];
				
				if ( false !== strpos( $key, ':', 1 ) ) {
					$explode = explode( ':', $key );
					
					$current_settings['api_user_name'] = Paysafe::get_field( 0, $explode );
					$current_settings['api_password']  = Paysafe::get_field( 1, $explode );
				}
			}
			
			// TODO: Change the woocommerce_netbanx_settings into woocommerce_paysafe_settings when you change the gateway id
			update_option( 'woocommerce_netbanx_settings', $current_settings );
		}
		
		if ( version_compare( $this->version, '3.4.0', '<' ) && version_compare( WC_PAYSAFE_PLUGIN_VERSION, '3.4.0', '>=' ) ) {
			$current_settings = get_option( 'woocommerce_netbanx_settings' );
			
			$card_account_id = Paysafe::get_field('account_id', $current_settings, '');
			$dd_account_id = Paysafe::get_field('dd_account_id', $current_settings, '');
			
			$current_settings['card_accounts'][] = array(
				'account_currency' => get_woocommerce_currency(),
				'account_id' => $card_account_id
			);
			
			$current_settings['direct_debit_accounts'][] = array(
				'account_currency' => get_woocommerce_currency(),
				'account_id' => $dd_account_id
			);
			
			update_option( 'woocommerce_netbanx_settings', $current_settings );
		}
		
		// Save the current version
		update_option( 'wc_paysafe_version', WC_PAYSAFE_PLUGIN_VERSION );
	}
}