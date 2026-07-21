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

use SkyVerge\WooCommerce\Cybersource\CaptureContextRetriever;
use SkyVerge\WooCommerce\Cybersource\Device_Data;
use SkyVerge\WooCommerce\Cybersource\Flex_Helper;
use SkyVerge\WooCommerce\Cybersource\Gateway\Credit_Card;
use SkyVerge\WooCommerce\Cybersource\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\Payment_Gateway\Blocks\Gateway_Checkout_Block_Integration;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_API_Exception;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway_Plugin;

/**
 * Checkout block integration for the credit card gateway.
 *
 * @since 2.8.0
 *
 * @property Plugin $plugin
 * @property Credit_Card $gateway
 */
class Credit_Card_Checkout_Block_Integration extends Gateway_Checkout_Block_Integration {


	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 *
	 * @param Plugin $plugin
	 * @param Credit_Card $gateway
	 */
	public function __construct( SV_WC_Payment_Gateway_Plugin $plugin, SV_WC_Payment_Gateway $gateway ) {

		parent::__construct( $plugin, $gateway );

		if ( $gateway->is_decision_manager_enabled() && ! is_admin() ) {

			$this->add_main_script_dependency( "wc-cybersource-device-data" );
		}

		$this->add_main_script_dependency( 'wc-cybersource-flex-microform' );
	}

	/** @inheritDoc */
	protected function add_hooks() : void
	{
		parent::add_hooks();

		Flex_Helper::addFlexMicroformScriptHooks();
	}


	/**
	 * Adds payment method data.
	 *
	 * @see Gateway_Checkout_Block_Integration::get_payment_method_data()
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @param array<string, mixed> $payment_method_data
	 * @param Credit_Card $gateway
	 * @return array<string, mixed>
	 */
	public function add_payment_method_data( array $payment_method_data, SV_WC_Payment_Gateway $gateway ) : array {

		$data = array_merge( [
			'number_placeholder'      => '•••• •••• •••• ••••',
			'csc_placeholder'         => '•••',
			'styles'                  => [
				'input' => [
					'font-size'   => '16px',
					'line-height' => '1em',
					'color'       => '#2b2d2f',
				],
				// ensure placeholder is initially hidden, opacity is toggled via JS when focusing on the field
				'::placeholder' => [
					'opacity' => 0,
				],
				'invalid' => [
					'color' => 'rgb(204, 24, 24)',
				],
			],
		], $payment_method_data );

		try {

			$data['capture_context'] = CaptureContextRetriever::getCaptureContext();

		} catch ( SV_WC_API_Exception $exception ) {

			$gateway->add_debug_message( 'Error generating transaction specific public key used to initiate the Flex Microform: ' . $exception->getMessage() );
		}

		return $data;
	}


}
