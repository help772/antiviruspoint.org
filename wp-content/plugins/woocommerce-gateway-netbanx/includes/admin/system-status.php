<?php

namespace WcPaysafe\Admin;

use WcPaysafe\Helpers\Factories;
use WcPaysafe\Paysafe;

/**
 * Plugin System Status
 *
 * @since      3.5.0
 */
class System_Status {
	
	public $gateway;
	public $gateway_id;
	public $plugin_license_id;
	
	public function __construct( $gateway_id, $plugin_license_id = 0 ) {
		$this->gateway_id        = $gateway_id;
		$this->plugin_license_id = $plugin_license_id;
	}
	
	/**
	 * Attach callbacks
	 *
	 * @since 3.5.0
	 */
	public function hooks() {
		add_filter( 'woocommerce_system_status_report', array( $this, 'render_system_status_items' ), 100 );
	}
	
	public function get_gateway() {
		if ( null == $this->gateway ) {
			$this->gateway = Factories::get_gateway( $this->gateway_id );
		}
		
		return $this->gateway;
	}
	
	/**
	 * Renders the Subscription information in the WC status page
	 *
	 * @since 3.5.0
	 */
	public function render_system_status_items() {
		
		$status_data['wc_paysafe_integration'] = array(
			'name'      => _x( 'Integration type', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
			'label'     => 'Integration type',
			'note'      => Paysafe::get_field( $this->get_gateway()->integration, $this->get_gateway()->get_integration_options(), 'not set' ),
			'mark'      => 'yes',
			'mark_icon' => '',
		);
		
		if ( 'checkoutjs' == $this->get_gateway()->integration ) {
			$status_data['wc_paysafe_3d_enabled'] = array(
				'name'      => _x( '3D Secure', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label'     => '3D Secure',
				'note'      => 'yes' == $this->get_gateway()->get_option( 'use_layover_3ds' ) ? __( 'Yes', 'wc_paysafe' ) : __( 'No', 'wc_paysafe' ),
				'mark'      => 'yes' == $this->get_gateway()->get_option( 'use_layover_3ds' ) ? 'yes' : 'error',
				'mark_icon' => '',
			);
			
			$status_data['wc_paysafe_3d2_enabled'] = array(
				'name'      => _x( '3D Secure 2', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label'     => '3D Secure 2',
				'note'      => 'yes' == $this->get_gateway()->get_option( 'use_layover_3ds2' ) ? __( 'Yes', 'wc_paysafe' ) : __( 'No', 'wc_paysafe' ),
				'mark'      => 'yes' == $this->get_gateway()->get_option( 'use_layover_3ds2' ) ? 'yes' : 'error',
				'mark_icon' => '',
			);
			
			$status_data['wc_paysafe_3d2_preference'] = array(
				'name'      => _x( '3D Secure 2: Challenge Preference', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label'     => '3D Secure 2',
				'note'      => $this->get_gateway()->get_option( 'threeds2_challenge_preference' ),
				'mark'      => 'yes',
				'mark_icon' => '',
			);
			
			$status_data['wc_paysafe_save_cards'] = array(
				'name'      => _x( 'Allow Payments via Saved Cards', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label'     => 'Allow Payments via Saved Cards',
				'note'      => $this->get_gateway()->saved_cards ? __( 'Yes', 'wc_paysafe' ) : __( 'No', 'wc_paysafe' ),
				'mark'      => $this->get_gateway()->saved_cards ? 'yes' : 'error',
				'mark_icon' => '',
			);
			
			$status_data['wc_paysafe_card_accounts'] = array(
				'name'  => _x( 'Merchant Accounts for Cards', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label' => 'Merchant Accounts for Cards',
				'data'  => $this->get_accounts_for_currencies( $this->get_gateway()->get_option( 'card_accounts', array() ) ),
			);
			
			$status_data['wc_paysafe_dd_accounts'] = array(
				'name'  => _x( 'Merchant Accounts for DirectDebit', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label' => 'Merchant Accounts for DirectDebit',
				'data'  => $this->get_accounts_for_currencies( $this->get_gateway()->get_option( 'direct_debit_accounts', array() ) ),
			);
		} else {
			$status_data['wc_paysafe_use_iframe'] = array(
				'name'      => _x( 'Using iFrame', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label'     => 'Using iFrame',
				'note'      => 'yes' == $this->get_gateway()->use_iframe ? __( 'Yes', 'wc_paysafe' ) : __( 'No', 'wc_paysafe' ),
				'mark'      => 'yes' == $this->get_gateway()->use_iframe ? 'yes' : 'error',
				'mark_icon' => '',
			);
			
			$status_data['wc_paysafe_send_ip'] = array(
				'name'      => _x( 'Sending IP', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
				'label'     => 'Sending IP',
				'note'      => 'yes' == $this->get_gateway()->send_ip_address ? __( 'Yes', 'wc_paysafe' ) : __( 'No', 'wc_paysafe' ),
				'mark'      => 'yes' == $this->get_gateway()->send_ip_address ? 'yes' : 'error',
				'mark_icon' => '',
			);
		}
		
		$testmode = 'yes' == $this->get_gateway()->testmode;
		
		$status_data['wc_paysafe_testmode'] = array(
			'name'      => _x( 'Gateway Mode', 'Label on WooCommerce -> System Status page', 'wc_paysafe' ),
			'label'     => 'Gateway Mode',
			'note'      => $testmode ? __( 'Test', 'wc_paysafe' ) : __( 'Live', 'wc_paysafe' ),
			'success'   => $testmode ? 0 : 1,
			'mark'      => $testmode ? 'error' : 'yes',
			'mark_icon' => '',
		);
		
		$system_status_sections = array(
			array(
				'title'   => __( 'Paysafe Gateway', 'wc_paysafe' ),
				'tooltip' => __( 'System information about WooCommerce Paysafe plugin.', 'wc_paysafe' ),
				'data'    => apply_filters( 'wc_paysafe_system_status', $status_data ),
			),
		);
		
		foreach ( $system_status_sections as $section ) {
			$section_title   = $section['title'];
			$section_tooltip = $section['tooltip'];
			$debug_data      = $section['data'];
			
			include( Paysafe::plugin_path() . '/includes/admin/views/system-status.php' );
		}
	}
	
	/**
	 * Return the currency codes that we have set merchant accounts for
	 *
	 * @param $accounts
	 *
	 * @return array
	 */
	public function get_accounts_for_currencies( $accounts ) {
		$currencies = array();
		foreach ( $accounts as $data ) {
			if ( '' == $data['account_id'] ) {
				continue;
			}
			$currencies[] = $data['account_currency'];
		}
		
		return $currencies;
	}
}
