<?php
/**
 * WooCommerce CyberSource
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce CyberSource to newer
 * versions in the future. If you wish to customize WooCommerce CyberSource for your
 * needs please refer to http://docs.woocommerce.com/document/cybersource-payment-gateway/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Cybersource\Gateway\Credit_Card;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.9.0
 *
 * @method Plugin get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since \WC_Cybersource 1.9.2
	 *
	 * @param $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.1.1',
			'1.1.2',
			'1.2',
			'2.0.0',
			'2.3.0',
			'2.5.0',
		];
	}


	/**
	 * Performs installation tasks.
	 *
	 * @since 2.0.0
	 */
	protected function install() {

		$migrate_from_sop = get_option( 'wc_cybersource_migrate_from_sop', false );

		// if migrating directly
		if ( $migrate_from_sop ) {

			// overwrite existing (probably stale) settings
			$this->migrate_sop_settings( true );

			delete_option( 'wc_cybersource_migrate_from_sop' );

			// otherwise, check for installed settings and migrate
		} elseif ( $this->is_sop_installed() ) {

			$this->migrate_sop_settings();
		}
	}


	/**
	 * Checks if CyberSource SA SOP is installed.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function is_sop_installed() {

		return ( get_option( 'wc_cybersource_sa_sop_version', false ) );
	}


	/**
	 * Migrates settings from CyberSource SA SOP.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $overwrite_existing whether to force overwrite existing settings
	 */
	private function migrate_sop_settings( $overwrite_existing = false ) {

		$sop_credit_card_settings = get_option( 'woocommerce_cybersource_sa_sop_credit_card_settings', [] );
		$sop_echeck_settings      = get_option( 'woocommerce_cybersource_sa_sop_echeck_settings', [] );

		// use SOP credit card settings if configured
		if ( ! empty( $sop_credit_card_settings ) ) {

			$existing_settings = get_option( 'woocommerce_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_settings', [] );

			if ( ! $overwrite_existing && ! empty( $existing_settings ) ) {

				$this->get_plugin()->log( 'Existing credit card settings found. Skipping settings migration.' );

			} else {

				$new_credit_card_settings = $sop_credit_card_settings;

				update_option( 'woocommerce_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_settings', $new_credit_card_settings );
			}
		}

		// use SOP e-check settings if configured
		if ( ! empty( $sop_echeck_settings ) ) {

			$existing_settings = get_option( 'woocommerce_' . Plugin::ECHECK_GATEWAY_ID . '_settings', [] );

			if ( ! $overwrite_existing && ! empty( $existing_settings ) ) {

				$this->get_plugin()->log( 'Existing e-check settings found. Skipping settings migration.' );

			} else {

				$new_echeck_settings = [
					'check_number_mode' => isset( $sop_echeck_settings['check_number_mode'] ) ? $sop_echeck_settings['check_number_mode'] : 'required',
				];

				$new_echeck_settings = array_merge( $sop_echeck_settings, $new_echeck_settings );

				update_option( 'woocommerce_' . Plugin::ECHECK_GATEWAY_ID . '_settings', $new_echeck_settings );
			}
		}

		// set option to display migrated notice and enable migrate orders button
		update_option( 'wc_' . Plugin::PLUGIN_ID . '_migrated_from_sop', 'yes' );
	}


	/**
	 * Updates to version 1.1.1
	 *
	 * Standardizes debug_mode setting.
	 *
	 * @since 1.9.2
	 */
	protected function upgrade_to_1_1_1() {

		$settings = get_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', [] );

		if ( $settings ) {

			// previous settings
			$log_enabled   = isset( $settings['log'] )   && 'yes' === $settings['log'];
			$debug_enabled = isset( $settings['debug'] ) && 'yes' === $settings['debug'];

			// logger -> debug_mode
			if ( $log_enabled && $debug_enabled ) {
				$settings['debug_mode'] = 'both';
			} elseif ( ! $log_enabled && ! $debug_enabled ) {
				$settings['debug_mode'] = 'off';
			} elseif ( $log_enabled ) {
				$settings['debug_mode'] = 'log';
			} else {
				$settings['debug_mode'] = 'checkout';
			}

			unset( $settings['log'], $settings['debug'] );

			// set the updated options array
			update_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', $settings );
		}
	}


	/**
	 * Updates to version 1.1.2
	 *
	 * Standardizes enable_csc setting.
	 *
	 * @since 1.9.2
	 */
	protected function upgrade_to_1_1_2() {

		$settings = get_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', [] );

		if ( $settings ) {

			$settings['enable_csc'] = ! isset( $settings['cvv'] ) || 'yes' === $settings['cvv'] ? 'yes' : 'no';

			unset( $settings['cvv'] );

			// set the updated options array
			update_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', $settings );
		}
	}


	/**
	 * Updates to version 1.2
	 *
	 * Standardizes order meta names and values (with the exception of the cc expiration, which just isn't worth the effort at the moment).
	 *
	 * @since 1.9.2
	 */
	protected function upgrade_to_1_2() {
		global $wpdb;

		$settings = get_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', [] );

		if ( $settings ) {

			// testmode -> environment
			if ( isset( $settings['testmode'] ) && 'yes' === $settings['testmode'] ) {
				$settings['environment'] = 'test';
			} else {
				$settings['environment'] = 'production';
			}
			unset( $settings['testmode'] );

			// cardtypes -> card_types
			if ( isset( $settings['cardtypes'] ) ) {
				$settings['card_types'] = $settings['cardtypes'];
			}
			unset( $settings['cardtypes'] );

			// salemethod -> transaction_type, 'AUTH_ONLY' -> 'authorization', 'AUTH_CAPTURE' -> 'charge'
			if ( isset( $settings['salemethod'] ) ) {
				$settings['transaction_type'] = 'AUTH_ONLY' === $settings['salemethod'] ? 'authorization' : 'charge';
			}
			unset( $settings['salemethod'] );

			// set the updated options array
			update_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', $settings );
		}

		if( Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ){

			// Update existing meta values before we migrate the keys
			$values_to_update = [
				'_cybersource_orderpage_environment' => [
					'TEST'       => 'test',
					'PRODUCTION' => 'production',
				],
				'_cybersource_card_type' => [
					'Visa'             => 'visa',
					'MasterCard'       => 'mc',
					'American Express' => 'amex',
					'Discover'         => 'disc',
				],
			];

			foreach( $values_to_update as $meta_key ){
				foreach ( $meta_key as $old => $new ){
					$orders = wc_get_orders(
						array(
							'meta_query' => array(
								array(
									'key' => $meta_key,
									'value' => $old,
								),
							),
						)
					);

					foreach( $orders as $order ){
						$order->update_meta_data( $meta_key, $new );
						$order->save_meta_data();
					}
				}
			}

			// Add _wc prefix to order meta keys
			$keys_to_update = [
				'_cybersource_request_id'            => '_wc_cybersource_trans_id',
				'_cybersource_orderpage_environment' => '_wc_cybersource_environment',
				'_cybersource_card_type'             => '_wc_cybersource_card_type',
				'_cybersource_card_last4'            => '_wc_cybersource_account_four',
				'_cybersource_card_expiration'       => '_wc_cybersource_card_expiry_date'
			];

			foreach ( $keys_to_update as $old => $new ){
				$orders = wc_get_orders(
					array(
						'meta_query' => array(
							array(
								'key' => $old,
							),
						),
					)
				);

				foreach( $orders as $order ){
					$order->add_meta_data( $new, $order->get_meta( $old ) );
					$order->delete_meta_data( $old );
					$order->save_meta_data();
				}
			}

		} else {

			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key='_wc_cybersource_trans_id'         WHERE meta_key='_cybersource_request_id'" );

			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value='test'                           WHERE meta_key='_cybersource_orderpage_environment' AND meta_value='TEST'" );
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value='production'                     WHERE meta_key='_cybersource_orderpage_environment' AND meta_value='PRODUCTION'" );
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key='_wc_cybersource_environment'      WHERE meta_key='_cybersource_orderpage_environment'" );

			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value='visa'                           WHERE meta_key='_cybersource_card_type' AND meta_value='Visa'" );
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value='mc'                             WHERE meta_key='_cybersource_card_type' AND meta_value='MasterCard'" );
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value='amex'                           WHERE meta_key='_cybersource_card_type' AND meta_value='American Express'" );
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value='disc'                           WHERE meta_key='_cybersource_card_type' AND meta_value='Discover'" );
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key='_wc_cybersource_card_type'        WHERE meta_key='_cybersource_card_type'" );

			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key='_wc_cybersource_account_four'     WHERE meta_key='_cybersource_card_last4'" );

			// older entries will be in the form MM/YYYY
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key='_wc_cybersource_card_expiry_date' WHERE meta_key='_cybersource_card_expiration'" );
		}
	}


	/**
	 * Upgrades to version 2.0.0.
	 *
	 * @since 2.0.0
	 */
	protected function upgrade_to_2_0_0() {

		$legacy_settings   = get_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', [] );
		$legacy_configured = ! empty( $legacy_settings );

		if ( $legacy_configured ) {

			// set option to allow the user to revert to legacy if it was ever enabled
			update_option( 'wc_' . Plugin::PLUGIN_ID . '_legacy_active', 'yes' );

			// map card types
			$card_types      = [];
			$card_id_to_type = [
				'001' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA,
				'002' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD,
				'003' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX,
				'004' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER,
				'005' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB,
				'007' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB,
			];

			foreach ( $legacy_settings['card_types'] as $card_id ) {

				if ( isset( $card_id_to_type[ $card_id ] ) ) {
					$card_types[] = $card_id_to_type[ $card_id ];
				}
			}

			$legacy_settings['card_types'] = $card_types;

			// update the legacy gateway with new card types
			update_option( 'woocommerce_' . Plugin::PLUGIN_ID . '_settings', $legacy_settings );

			// use legacy credit card settings
			$new_credit_card_settings = [
				'enabled'          => isset( $legacy_settings['enabled'] ) ? $legacy_settings['enabled'] : 'no',
				'title'            => isset( $legacy_settings['title'] ) ? $legacy_settings['title'] : __( 'Credit Card', 'woocommerce-gateway-cybersource' ),
				'description'      => isset( $legacy_settings['description'] ) ? $legacy_settings['description'] : __( 'Pay securely using your credit card.', 'woocommerce-gateway-cybersource' ),
				'enable_csc'       => isset( $legacy_settings['enable_csc'] ) ? $legacy_settings['enable_csc'] : 'no',
				'transaction_type' => isset( $legacy_settings['transaction_type'] ) ? $legacy_settings['transaction_type'] : Framework\SV_WC_Payment_Gateway::TRANSACTION_TYPE_CHARGE,
				'environment'      => isset( $legacy_settings['environment'] ) ? $legacy_settings['environment'] : Framework\SV_WC_Payment_Gateway::ENVIRONMENT_PRODUCTION,
				'merchant_id'      => isset( $legacy_settings['merchantid'] ) ? $legacy_settings['merchantid'] : '',
				'test_merchant_id' => isset( $legacy_settings['merchantid'] ) ? $legacy_settings['merchantid'] : '',
				'debug_mode'       => isset( $legacy_settings['debug_mode'] ) ? $legacy_settings['debug_mode'] : Framework\SV_WC_Payment_Gateway::DEBUG_MODE_OFF,
				'card_types'       => $card_types,
			];

			update_option( 'woocommerce_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_settings', $new_credit_card_settings );
		}
	}


	/**
	 * For all database records with a given meta key, inserts new database records
	 * with the same post ID and meta value and a new meta key.
	 *
	 * @param $old_meta_key
	 * @param $new_meta_key
	 *
	 * @return bool|int number of inserted rows
	 */
	private function copy_metadata_with_new_key( $old_meta_key, $new_meta_key ) {

		global $wpdb;

		$inserted_rows = 0;

		$rows_to_insert = $wpdb->get_results( $wpdb->prepare(

			"
				SELECT post_id, %s, meta_value
				FROM $wpdb->postmeta
				WHERE meta_key = %s
				",
			$new_meta_key,
			$old_meta_key ), ARRAY_N );

		foreach ( $rows_to_insert as $row_to_insert ) {

			$inserted_rows += $wpdb->query( $wpdb->prepare(
				"
					INSERT INTO $wpdb->postmeta
					( post_id, meta_key, meta_value )
					VALUES ( %d, %s, %s )
				", $row_to_insert ) );
		}

		return $inserted_rows;
	}


	/**
	 * Migrates legacy credit card orders.
	 *
	 * @since 2.0.0
	 */
	public function migrate_legacy_cc_orders() {

		global $wpdb;

		/** Copy order meta values for transaction information */

		$legacy_meta_keys = [
			'_wc_cybersource_account_four',
			'_wc_cybersource_authorization_amount',
			'_wc_cybersource_authorization_code',
			'_wc_cybersource_card_expiry_date',
			'_wc_cybersource_card_type',
			'_wc_cybersource_charge_captured',
			'_wc_cybersource_environment',
			'_wc_cybersource_retry_count',
			'_wc_cybersource_trans_date',
			'_wc_cybersource_trans_id',
		];

		foreach ( $legacy_meta_keys as $legacy_meta_key ) {

			$new_meta_key  = str_replace( 'cybersource', 'cybersource_credit_card', $legacy_meta_key );
			$inserted_rows = $this->copy_metadata_with_new_key( $legacy_meta_key, $new_meta_key );
			$this->get_plugin()->log( sprintf( 'Meta %s copied to %s for %d CyberSource Legacy orders', $legacy_meta_key, $new_meta_key, $inserted_rows ) );
		}

		/** Update meta values for order payment method & recurring payment method */

		$payment_method_meta_keys = [
			'_payment_method',
			'_recurring_payment_method',
		];

		foreach ( $payment_method_meta_keys as $meta_key ) {

			// old value: cybersource
			// new value: cybersource_credit_card
			$data  = [ 'meta_value' => 'cybersource_credit_card' ];
			$where = [ 'meta_key' => $meta_key, 'meta_value' => 'cybersource' ];
			$rows  = $wpdb->update( $wpdb->postmeta, $data, $where );

			$this->get_plugin()->log( sprintf( '%d CyberSource Legacy orders updated for %s meta', $rows, $meta_key ) );
		}
	}


	/**
	 * Migrates historical SOP credit card orders.
	 *
	 * @since 2.0.0
	 */
	public function migrate_sop_cc_orders() {

		global $wpdb;

		/** Copy order meta values for transaction information */

		$sop_meta_keys = [
			'_wc_cybersource_sa_sop_credit_card_account_four',
			'_wc_cybersource_sa_sop_credit_card_authorization_amount',
			'_wc_cybersource_sa_sop_credit_card_authorization_code',
			'_wc_cybersource_sa_sop_credit_card_card_expiry_date',
			'_wc_cybersource_sa_sop_credit_card_card_type',
			'_wc_cybersource_sa_sop_credit_card_charge_captured',
			'_wc_cybersource_sa_sop_credit_card_environment',
			'_wc_cybersource_sa_sop_credit_card_retry_count',
			'_wc_cybersource_sa_sop_credit_card_trans_date',
			'_wc_cybersource_sa_sop_credit_card_trans_id',
		];

		foreach ( $sop_meta_keys as $sop_meta_key ) {

			$new_meta_key  = str_replace( 'cybersource_sa_sop', 'cybersource', $sop_meta_key );
			$inserted_rows = $this->copy_metadata_with_new_key( $sop_meta_key, $new_meta_key );
			$this->get_plugin()->log( sprintf( 'Meta %s copied to %s for %d CyberSource SA SOP Credit Card orders', $sop_meta_key, $new_meta_key, $inserted_rows ) );
		}

		/** Update meta values for order payment method & recurring payment method */

		$payment_method_meta_keys = [
			'_payment_method',
			'_recurring_payment_method',
		];

		foreach ( $payment_method_meta_keys as $meta_key ) {

			// old value: cybersource_sa_sop_credit_card
			// new value: cybersource_credit_card
			$data  = [ 'meta_value' => 'cybersource_credit_card' ];
			$where = [ 'meta_key' => $meta_key, 'meta_value' => 'cybersource_sa_sop_credit_card' ];
			$rows  = $wpdb->update( $wpdb->postmeta, $data, $where );

			$this->get_plugin()->log( sprintf( '%d CyberSource SA SOP Credit Card orders updated for %s meta', $rows, $meta_key ) );
		}
	}


	/**
	 * Migrates historical e-check orders (SOP).
	 *
	 * @since 2.0.0
	 */
	public function migrate_sop_echeck_orders() {

		global $wpdb;

		/** Copy order meta values for transaction information */

		$sop_meta_keys = [
			'_wc_cybersource_sa_sop_echeck_account_four',
			'_wc_cybersource_sa_sop_echeck_account_type',
			'_wc_cybersource_sa_sop_echeck_check_number',
			'_wc_cybersource_sa_sop_echeck_environment',
			'_wc_cybersource_sa_sop_echeck_retry_count',
			'_wc_cybersource_sa_sop_echeck_trans_date',
			'_wc_cybersource_sa_sop_echeck_trans_id',
		];

		foreach ( $sop_meta_keys as $sop_meta_key ) {

			$new_meta_key  = str_replace( 'cybersource_sa_sop', 'cybersource', $sop_meta_key );
			$inserted_rows = $this->copy_metadata_with_new_key( $sop_meta_key, $new_meta_key );
			$this->get_plugin()->log( sprintf( 'Meta %s copied to %s for %d CyberSource SA SOP eCheck orders', $sop_meta_key, $new_meta_key, $inserted_rows ) );
		}

		/** Update meta values for order payment method & recurring payment method */

		$payment_method_meta_keys = [
			'_payment_method',
			'_recurring_payment_method',
		];

		foreach ( $payment_method_meta_keys as $meta_key ) {

			// old value: cybersource_sa_sop_echeck
			// new value: cybersource_credit_card
			$data  = [ 'meta_value' => 'cybersource_credit_card' ];
			$where = [ 'meta_key' => $meta_key, 'meta_value' => 'cybersource_sa_sop_echeck' ];
			$rows  = $wpdb->update( $wpdb->postmeta, $data, $where );

			$this->get_plugin()->log( sprintf( '%d CyberSource SA SOP eCheck orders updated for %s meta', $rows, $meta_key ) );
		}
	}


	/**
	 * Updates to version 2.4.0
	 *
	 * @since 2.3.0
	 */
	protected function upgrade_to_2_3_0() {

		// bail if legacy is active
		if ( 'yes' === get_option( 'wc_' . Plugin::PLUGIN_ID . '_legacy_active' ) ) {
			return;
		}

		/** @var Credit_Card $gateway */
		$gateway = $this->get_plugin()->get_gateway( Plugin::CREDIT_CARD_GATEWAY_ID );

		if ( is_callable( [ $gateway, 'is_flex_form_enabled' ] ) ) {

			update_option( 'woocommerce_cybersource_show_flex_microform_toggle', wc_bool_to_string( ! $gateway->is_flex_form_enabled() ) );
		}
	}


	/**
	 * Updates to version 2.5.0
	 *
	 * @since 2.5.0
	 */
	protected function upgrade_to_2_5_0() {

		// get Credit Card Gateway settings
		$credit_card_settings = $this->get_plugin()->get_gateway_settings( Plugin::CREDIT_CARD_GATEWAY_ID );

		// if FlexForm not enabled and if the user can toggle it
		if ( ! wc_string_to_bool( $credit_card_settings['enable_flex_form'] ?? false ) && get_option( 'woocommerce_cybersource_show_flex_microform_toggle' ) ) {

			// automatically enable Flex Microform instead
			update_option( 'woocommerce_cybersource_flex_microform_automatically_enabled', 'yes' );
		}

		// do not allow user to toggle Flex Microform off
		delete_option( 'woocommerce_cybersource_show_flex_microform_toggle' );
	}


}
