<?php

namespace WcPaysafe\Admin;

use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Vault\Responses\Ach;
use WcPaysafe\Api\Vault\Responses\Bacs;
use WcPaysafe\Api\Vault\Responses\Eft;
use WcPaysafe\Api\Vault\Responses\Sepa;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Customer;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modifying the Admin display of the refund process.
 * We need more information to refund DD payments and we want to display that to the merchant before the refund.
 *
 * Note: The actual refund process happens in the Gateway classes
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Refund {
	
	/**
	 * Loads the hooks to manipulate the refunds process.
	 *
	 * @since 3.3.0
	 */
	public function hooks() {
		add_action( 'woocommerce_order_item_add_action_buttons', array( $this, 'maybe_disable_refunds' ), 10 );
		add_action( 'woocommerce_order_item_add_line_buttons', array(
			$this,
			'display_direct_debit_account',
		), 10 );
		
		add_action( 'woocommerce_order_item_add_line_buttons', array(
			$this,
			'display_card_available_to_refund',
		), 10 );
	}
	
	/**
	 * Disables the refunds support if customer paid with DirectDebit and did not save a token to the Vault.
	 *
	 * @since 3.3.0
	 *
	 * @param $order
	 */
	public function maybe_disable_refunds( $order ) {
		$ps_order     = new Paysafe_Order( $order );
		$token        = $ps_order->get_order_profile_token();
		$payment_type = $ps_order->get_payment_type();
		
		if ( 'card' != $payment_type && ! $token ) {
			if ( 'netbanx' == $order->get_payment_method() || 'paysafe_checkout_payments' == $order->get_payment_method() ) {
				$gateway     = Factories::get_gateway( $order->get_payment_method() );
				$refunds_key = array_search( 'refunds', $gateway->supports );
				
				if ( false !== $refunds_key ) {
					unset( $gateway->supports[ $refunds_key ] );
				}
			}
		} elseif ( 'card' == $payment_type ) {
			// TODO: Disable the Refund button, if availableToRefund is 0
		}
	}
	
	/**
	 * Display the bank account information to the Admin before they process a stand alone credit to the customer
	 *
	 * @since 3.3.0
	 *
	 * @param $order_id
	 *
	 * @throws \Exception
	 */
	public function display_direct_debit_account( $order_id ) {
		$order         = wc_get_order( $order_id );
		$paysafe_order = new Paysafe_Order( $order );
		
		// Info is for DD only
		if ( 'card' == $paysafe_order->get_payment_type() ) {
			return;
		}
		
		try {
			$paysafe_customer = new Paysafe_Customer( $order->get_user() );
			$profile          = $paysafe_customer->get_vault_sources( 'netbanx', array( $paysafe_order->get_payment_type() => true ) );
			$token            = $paysafe_order->get_order_profile_token();
			
			// We need this token
			if ( ! $profile->{'get_' . $paysafe_order->get_payment_type()}() ) {
				return;
			}
			
			/**
			 * @var Eft|Sepa|Bacs|Ach $bank
			 * @var Eft|Sepa|Bacs|Ach $item
			 */
			$banks = $profile->{'get_' . $paysafe_order->get_payment_type()}();
			$bank  = false;
			foreach ( $banks as $item ) {
				if ( $item->get_payment_token() == $token ) {
					$bank = $item;
					break;
				}
			}
			
			if ( ! $bank ) {
				return;
			}
			?>
			<div class="clear"></div>
			<div class="wc-order-refund-items wc-order-data-row-toggle wc-paysafe-refund-info" style="display: block;">
				<div class="clear"></div>
				<table class="wc-order-totals">
					<tbody>
					<tr>
						<td class="label" colspan="2">
							<label for=""><?php echo esc_html( __( 'Important: You are about to perform a Standalone Credit to the following bank account.', 'wc_paysafe' ) ); ?></label>
						</td>
					</tr>
					<tr>
						<td class="label" colspan="2">
							<label for=""><?php echo esc_html( __( 'Receivers bank information', 'wc_paysafe' ) ); ?></label>
						</td>
					</tr>
					<tr>
						<td class="label"><?php echo esc_html( __( 'Account Holder Name', 'wc_paysafe' ) ); ?>:</td>
						<td class="total"><?php echo esc_attr( $bank->get_account_holder_name() ); ?></td>
					</tr>
					<tr>
						<td class="label"><?php echo esc_html( __( 'Account Digits', 'wc_paysafe' ) ); ?>:</td>
						<td class="total"><?php echo esc_attr( $bank->get_last_digits() ); ?>                                                </td>
					</tr>
					<tr>
						<td class="label"><?php echo esc_html( __( 'Routing Number', 'wc_paysafe' ) ); ?>:</td>
						<td class="total"><?php echo esc_attr( $bank->get_routing_number() ); ?></td>
					</tr>

					<tr>
					</tbody>
				</table>
				<div class="clear"></div>
			</div>
			<?php
		}
		catch ( \Exception $e ) {
			// We are not displaying anything if the process above fails
		}
	}
	
	public function display_card_available_to_refund( $order_id ) {
		$order         = wc_get_order( $order_id );
		$paysafe_order = new Paysafe_Order( $order );
		
		// Info is for DD only
		if ( 'card' != $paysafe_order->get_payment_type() ) {
			return;
		}
		
		try {
			$gateway = Factories::get_gateway( $order->get_payment_method() );
			
			// Check gateways
			if ( ! in_array( $gateway->id, [ 'netbanx', 'paysafe_checkout_payments' ] ) ) {
				return;
			}
			
			$type = 'card';
			if ( 'netbanx' == $gateway->id ) {
				$type = 'cards';
			}
			
			// Make sure the order is paid and there is an ID to check against
			if ( ! $paysafe_order->get_authorization_id() ) {
				return;
			}
			
			$client_api = Factories::get_api_client( $gateway, new Order_Source( $order ), $type );
			
			$authorization = $client_api->get_cards_service()->authorizations_request()->get( array( 'id' => $paysafe_order->get_authorization_id() ) );
			
			$total_auth_amount         = $authorization->get_amount();
			$total_available_to_settle = $authorization->get_available_to_settle();
			
			$available_to_refund = $total_auth_amount - $total_available_to_settle;
			
			if ( 0 == $available_to_refund ) {
				return;
			}
			
			?>
			<div class="clear"></div>
			<div class="wc-order-refund-items wc-order-data-row-toggle wc-paysafe-refund-info" style="display: block;">
				<div class="clear"></div>
				<table class="wc-order-totals">
					<tbody>
					<tr>
						<td class="label" colspan="2">
							<label for=""><?php echo esc_html( $gateway->get_method_title() . ' - ' . __( 'Available to refund: ', 'wc_paysafe' ) ); ?></label>
						</td>

						<td class="total">
							<?php echo esc_attr( get_woocommerce_currency_symbol() . wc_format_decimal( Formatting::format_amount_from_cent( $available_to_refund ), 2 ) ); ?>
						</td>
					</tr>
					</tbody>
				</table>
				<div class="clear"></div>
			</div>
			<?php
		}
		catch ( \Exception $e ) {
			// We are not displaying anything if the process above fails
		}
	}
}