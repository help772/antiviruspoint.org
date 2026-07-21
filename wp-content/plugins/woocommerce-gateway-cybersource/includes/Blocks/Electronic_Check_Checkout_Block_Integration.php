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

namespace SkyVerge\WooCommerce\Cybersource\Blocks;

use SkyVerge\WooCommerce\Cybersource\Gateway\Electronic_Check;
use SkyVerge\WooCommerce\Cybersource\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\Payment_Gateway\Blocks\Gateway_Checkout_Block_Integration;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway_Plugin;

/**
 * Checkout block integration for the electronic check gateway.
 *
 * @since 2.8.0
 *
 * @property Electronic_Check $gateway
 */
class Electronic_Check_Checkout_Block_Integration extends Gateway_Checkout_Block_Integration {


	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 *
	 * @param Plugin $plugin
	 * @param Electronic_Check $gateway
	 */
	public function __construct( SV_WC_Payment_Gateway_Plugin $plugin, SV_WC_Payment_Gateway $gateway ) {

		parent::__construct( $plugin, $gateway );

		if ( $gateway->is_decision_manager_enabled() && ! is_admin() ) {

			$this->add_main_script_dependency( "wc-cybersource-device-data" );
		}
	}


	/**
	 * @inheritDoc
	 */
	public function add_hooks(): void {

		parent::add_hooks();

		// TODO: consider moving this to the FW {@itambek 2024-01-31}
		add_filter( 'sv_wc_payment_gateway_payment_form_localized_script_params', [ $this, 'add_payment_form_localized_script_params' ], 10, 2 );
	}


	/**
	 * Adds payment method data.
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @param array $payment_method_data
	 * @param Electronic_Check $gateway
	 * @return array<string, mixed>
	 */
	public function add_payment_method_data( array $payment_method_data, SV_WC_Payment_Gateway $gateway ) : array {

		$payment_method_data['flags'] = array_merge(
			$payment_method_data['flags'] ?: [],
			[
				'authorization_message_enabled' => $gateway->is_authorization_message_enabled(),
			],
		);

		$payment_method_data['gateway'] = array_merge(
			$payment_method_data['gateway'] ?: [],
			[
				'authorization_message' => wp_kses_post( $gateway->get_authorization_message() ),
				'merchant_name'         => get_bloginfo( 'name' ),
				'check_number_field'    => $gateway->get_option( 'check_number_mode' ),
			],
		);

		return $payment_method_data;
	}


	/**
	 * Adds gateway-specific payment form localized script params.
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @param array<string, string> $params
	 * @param SV_WC_Payment_Gateway $gateway (note: param not typed in signature to avoid TypeError when multiple gateways are active)
	 * @return array<string, string>
	 */
	public function add_payment_form_localized_script_params( array $params, $gateway ) : array {

		if ( $gateway->get_id() !== $this->gateway->get_id() ) {
			return $params;
		}

		return array_merge( $params, [
			'check_number_missing'        => esc_html_x( 'Check Number is missing', 'Bank check (noun)', 'woocommerce-gateway-cybersource' ),
			'check_number_digits_invalid' => esc_html_x( 'Check Number is invalid (only digits are allowed)', 'Bank check (noun)', 'woocommerce-gateway-cybersource' ),
			'check_number_length_invalid' => esc_html_x( 'Check number is invalid (must be 8 digits or less)', 'Bank check (noun)', 'woocommerce-gateway-cybersource' ),
		] );
	}


}
