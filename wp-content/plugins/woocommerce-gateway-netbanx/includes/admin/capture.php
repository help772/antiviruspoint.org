<?php

namespace WcPaysafe\Admin;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Capture {
	
	/**
	 * Loads the capture action for the plugin
	 *
	 * This is loaded here and not in the gateway
	 * because we need it to load a little bit earlier for the action to be added to order edit screen
	 *
	 * @since 3.2.0
	 */
	public function hooks() {
		if ( WC_Compatibility::equal_or_grt( '4.0.0' ) ) {
			// The hook was moved down, so it is better to use
			add_action( 'woocommerce_admin_order_totals_after_total', array(
				$this,
				'order_meta_box_add_capture_field',
			) );
		} else {
			// The hook is only loaded when a refund is present, so we can't use it on 4.0+
			add_action( 'woocommerce_admin_order_totals_after_refunded', array(
				$this,
				'order_meta_box_add_capture_field',
			) );
		}
		
		add_action( 'woocommerce_order_item_add_action_buttons', array(
			$this,
			'order_meta_box_add_capture_payment_buttons',
		) );
	}
	
	/**
	 * Adds Capture buttons to the admin order edit screen
	 *
	 * @since 3.2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return bool
	 */
	public function order_meta_box_add_capture_payment_buttons( $order ) {
		$method = WC_Compatibility::get_prop( $order, 'payment_method' );
		if ( 'netbanx' != $method && 'paysafe_checkout_payments' != $method ) {
			return false;
		}
		
		$ps_order               = new Paysafe_Order( $order );
		$is_captured            = $ps_order->get_is_payment_captured();
		$allowed_order_statuses = self::get_capture_allowed_order_statuses();
		
		if ( $ps_order->is_subscription() || $is_captured || ! in_array( $order->get_status(), $allowed_order_statuses ) ) {
			return false;
		}
		
		?>
		<button type="button" class="button wc-paysafe-capture-payment-init">
			<?php echo esc_html( __( 'Capture', 'wc_paysafe' ) ); ?>
		</button>
		<span class="wc-paysafe-capture-payment-wrapper" style="display:none">
				<?php echo Formatting::kses_form_html( wc_help_tip( __( 'Enter the amount you want to capture above and press "Capture Payment" button.', 'wc_paysafe' ) ) ); ?>
			<button type="button" class="button button-primary wc-paysafe-capture-payment">
				<?php echo esc_html( __( 'Capture Payment', 'wc_paysafe' ) ); ?>
			</button>
			</span>
		<button type="button" class="button wc-paysafe-capture-cancel" style="display:none">
			<?php echo esc_html( __( 'Cancel Capture', 'wc_paysafe' ) ); ?>
		</button>
		<?php
	}
	
	/**
	 * Adds the capture amount field to the admin order edit screen
	 *
	 * @since 3.2.0
	 *
	 * @param $order_id
	 *
	 * @return bool
	 */
	public function order_meta_box_add_capture_field( $order_id ) {
		$order = wc_get_order( $order_id );
		
		$method = WC_Compatibility::get_prop( $order, 'payment_method' );
		if ( 'netbanx' != $method && 'paysafe_checkout_payments' != $method ) {
			return false;
		}
		
		$ps_order               = new Paysafe_Order( $order );
		$is_captured            = $ps_order->get_is_payment_captured();
		$allowed_order_statuses = self::get_capture_allowed_order_statuses();
		
		if ( $ps_order->is_subscription() || $is_captured || ! in_array( $order->get_status(), $allowed_order_statuses ) ) {
			return false;
		}
		
		$authorized_amount = wc_format_decimal( $ps_order->get_order_amount_authorized(), 2 );
		if ( empty( $authorized_amount ) ) {
			$authorized_amount = wc_format_decimal( $order->get_total(), 2 );
		}
		
		$amount_captured = wc_format_decimal( $ps_order->get_order_amount_captured(), 2 );
		$amount_allowed  = wc_format_decimal( $authorized_amount - $amount_captured, 2 );
		
		?>
		<tr class="wc-paysafe-capture-amount-wrapper" style="display: none;">
			<td class="label capture-total">
				<?php echo Formatting::kses_form_html( wc_help_tip( __( 'You can capture no more than the initially authorized amount.', 'wc_paysafe' ) ) ); ?>
				<?php echo esc_html( __( 'Capture', 'wc_paysafe' ) ); ?>:
			</td>
			<?php // There are changes in the cells, so we needed to two versions ?>
			<?php echo WC_Compatibility::is_wc_2_6() ? '<td width="1%"></td>' : ''; ?>
			<td class="total capture-total">
				<input type="text"
				       class="wc-paysafe-capture-amount wc_input_price"
				       name="wc-paysafe-capture-amount"
				       value="<?php echo esc_attr( $amount_allowed ); ?>"
				/>
			</td>
			<?php echo WC_Compatibility::is_wc_2_6() ? '' : '<td width="1%"></td>'; ?>
		</tr>
		<tr class="wc-paysafe-capture-allowed-amount-wrapper" style="display: none;">
			<td class="label capture-total">
				<small><?php echo esc_html( __( 'Total amount allowed to capture', 'wc_paysafe' ) ); ?>:</small>
			</td>
			<?php // There are changes in the cells, so we needed to two versions ?>
			<?php echo WC_Compatibility::is_wc_2_6() ? '<td width="1%"></td>' : ''; ?>
			<td class="total capture-total">
				<?php echo esc_html( $amount_allowed ); ?>
			</td>
			<?php echo WC_Compatibility::is_wc_2_6() ? '' : '<td width="1%"></td>'; ?>
		</tr>
		<?php
	}
	
	/**
	 * Returns the allowed order statuses to perform capture of a transaction.
	 * We naturally assume that the status of an order should be a paid order status, not completed and not failed payment.
	 *
	 * @since 3.2.0
	 *
	 * @return mixed
	 */
	public static function get_capture_allowed_order_statuses() {
		return apply_filters( 'wc_paysafe_capture_allowed_order_statuses', array(
			'processing',
			'on-hold',
			'active',
		) );
	}
}