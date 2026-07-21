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

namespace SkyVerge\WooCommerce\Cybersource\Gateway\ThreeD_Secure;

use SkyVerge\WooCommerce\Cybersource\API\Helper;
use SkyVerge\WooCommerce\Cybersource\Gateway\Credit_Card;
use SkyVerge\WooCommerce\Cybersource\Gateway\ThreeD_Secure;
use SkyVerge\WooCommerce\Cybersource\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Frontend 3D Secure handler.
 *
 * @since 2.3.0
 */
class Frontend {

	/** @var string 3D Secure script handle */
	public const THREED_SECURE_SCRIPT_HANDLE = 'wc-cybersource-threed-secure';


	/** @var ThreeD_Secure 3D Secure handler */
	private $handler;


	/**
	 * Frontend constructor.
	 *
	 * @since 2.3.0
	 *
	 * @param ThreeD_Secure $handler 3D Secure handler
	 */
	public function __construct( ThreeD_Secure $handler ) {

		$this->handler = $handler;

		// add the action & filter hooks
		$this->add_hooks();
	}


	/**
	 * Adds the action & filter hooks.
	 *
	 * @since 2.3.0
	 */
	private function add_hooks(): void {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		add_action( 'wc_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_payment_form_end', [ $this, 'render_js' ], 5 );

		add_action( 'wc_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_payment_form_payment_method_html', [ $this, 'add_token_data' ], 10, 2 );

		add_action( 'wc_' . Plugin::CREDIT_CARD_GATEWAY_ID . '_checkout_block_payment_method_data', [ $this, 'add_block_payment_method_data' ], 10, 2 );
	}


	/**
	 * Adds the card type and card bin for saved payment methods on Payment Forms.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 */
	public function add_token_data(string $html, Framework\SV_WC_Payment_Gateway_Payment_Token $token) : string {

		$html = str_replace( 'type="radio"', 'type="radio" data-card-type="' . $token->get_card_type() . '"', $html );

		$token_data = $token->to_datastore_format();

		if ( isset( $token_data['first_six'] ) && $token_data['first_six'] ) {
			$html = str_replace( 'type="radio"', 'type="radio" data-card-bin="' . $token_data['first_six'] . '"', $html );
		}

		if ( ! empty( $token_data['exp_year'] ) && ! empty( $token_data['exp_month'] ) ) {
			$html = str_replace( 'type="radio"', 'type="radio" data-card-expiration-month="' . $token_data['exp_month'] . '" data-card-expiration-year="' . $token_data['exp_year'] . '"', $html );
		}

		return $html;
	}


	/**
	 * Adds the 3DS data to the payment method data for the checkout block.
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @param array<string, mixed> $data
	 * @param Credit_Card $gateway
	 * @return array<string, mixed>
	 */
	public function add_block_payment_method_data( array $data, Credit_Card $gateway ) : array {

		// add general 3DS data
		$data['threed_secure'] = [
			'is_enabled'              => $this->handler->is_enabled(),
			'ajax_url'                => admin_url( 'admin-ajax.php' ),
			'setup_action'            => AJAX::ACTION_SETUP,
			'setup_nonce'             => wp_create_nonce( AJAX::ACTION_SETUP ),
			'check_enrollment_action' => AJAX::ACTION_CHECK_ENROLLMENT,
			'check_enrollment_nonce'  => wp_create_nonce( AJAX::ACTION_CHECK_ENROLLMENT ),
			'enabled_card_types'      => array_map( [Helper::class, 'convert_card_type_to_code'], $this->handler->get_enabled_card_types() ),
			'enabled_card_type_names' => $this->handler->get_enabled_card_types(),
		];

		// adds the card type and card bin for saved payment methods in Checkout Block
		if ( ( $user_id = get_current_user_id() ) && ! empty( $tokens = $gateway->get_payment_tokens_handler()->get_tokens( $user_id ) ) ) {
			foreach ( $tokens as $token ) {

				if ( $core_token = $token->get_woocommerce_payment_token() ) {
					// we use the core token ID, because that's what Woo exposes on the frontend
					$data['payment_tokens'][ $core_token->get_id() ] = [
						'id'        => $token->get_id(),
						'type'      => Helper::convert_card_type_to_code( $token->get_card_type() ),
						'type_name' => $token->get_card_type(),
						'bin'       => $token->to_datastore_format()['first_six'],
					];
				}
			}
		}

		return $data;
	}


	/**
	 * Enqueues the 3D Secure assets on the Order Pay or My Payment Method pages.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 */
	public function enqueue_assets(): void {

		// only render on the pay page or Add Payment Method pages
		if ( ! is_checkout_pay_page() ) {
			return;
		}

		wp_enqueue_script( self::THREED_SECURE_SCRIPT_HANDLE );
	}


	/**
	 * Renders the JS for the Payment Form 3DS handling.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 */
	public function render_js(): void {

		// only render on the pay page or Add Payment Method pages
		if ( ! is_checkout_pay_page() ) {
			return;
		}

		wc_enqueue_js( sprintf( 'window.wc_cybersource_threed_secure = new WC_Cybersource_ThreeD_Secure_Handler( %s );', json_encode( [
			'order_id'                => $this->handler->get_gateway()->get_checkout_pay_page_order_id(),
			'ajax_url'                => admin_url( 'admin-ajax.php' ),
			'logging_enabled'         => $this->handler->get_gateway()->debug_log(),
			'setup_action'            => AJAX::ACTION_SETUP,
			'setup_nonce'             => wp_create_nonce( AJAX::ACTION_SETUP ),
			'check_enrollment_action' => AJAX::ACTION_CHECK_ENROLLMENT,
			'check_enrollment_nonce'  => wp_create_nonce( AJAX::ACTION_CHECK_ENROLLMENT ),
			'enabled_card_types'      => array_map( 'SkyVerge\WooCommerce\Cybersource\API\Helper::convert_card_type_to_code', $this->handler->get_enabled_card_types() ),
			'enabled_card_type_names' => $this->handler->get_enabled_card_types(),
			'i18n' => [
				'error_general' => __( 'An error occurred, please try again or try an alternate form of payment', 'woocommerce-gateway-cybersource' )
			],
		] ) ) );
	}


}
