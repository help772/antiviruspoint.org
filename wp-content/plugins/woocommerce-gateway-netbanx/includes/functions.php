<?php
/**
 * Functions file to define some commonly used functionality into functions
 *
 * @since   3.3.0
 * @version 3.3.0
 * @author  VanboDevelops
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A function to add logs for the plugin processes
 *
 * Note: Moved the log process initiation to a function because it will be easier to scale the process if needed.
 * It will keep the process with a single point of entry
 *
 * @since 3.3.0
 *
 * @param        $message
 * @param string $handle
 * @param string $level
 */
function wc_paysafe_add_debug_log( $message, $handle = 'paysafe', $level = 'debug' ) {
	if ( '' === $message ) {
		return;
	}
	
	\WcPaysafe\Debug::add_debug_log( $message, $handle, $level );
}

function wc_paysafe_payments_add_debug_log( $message, $handle = 'paysafe_checkout_payments', $level = 'debug' ) {
	if ( '' === $message ) {
		return;
	}
	
	\WcPaysafe\Debug::add_debug_log( $message, $handle, $level );
}

/**
 * Is the customer on the update payment method page
 * @since 3.3.0
 * @return bool
 */
function wc_paysafe_is_update_payment_method_page() {
	global $wp;
	
	$page_id = wc_get_page_id( 'myaccount' );
	
	return $page_id && is_page( $page_id ) && isset( $wp->query_vars['update-payment-method'] );
}

/**
 * Is the customer on the payment methods page
 * @since 3.3.0
 * @return bool
 */
function wc_paysafe_is_payment_methods_page() {
	global $wp;
	
	$page_id = wc_get_page_id( 'myaccount' );
	
	return $page_id && is_page( $page_id ) && isset( $wp->query_vars['payment-methods'] );
}

/**
 * Is this the change method page
 * @since 3.3.0
 * @return bool
 */
function wc_paysafe_is_change_method_page() {
	
	if ( ! class_exists( 'WC_Subscriptions_Change_Payment_Gateway' ) ) {
		return false;
	}
	
	if ( ! isset( $_GET['pay_for_order'] ) ) {
		return false;
	}
	
	return \WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment;
}

/**
 * Is this the pay for order page.
 * Definition:  The page the customers go to when they want to pay for an existing order.
 *              Usually, goes through "My Account > Orders > Pay button"
 *
 * @since 3.7.0
 * @return bool
 */
function wc_paysafe_is_pay_for_order_page() {
	
	if ( is_checkout_pay_page() && 'true' === wc_clean( wp_unslash( \WcPaysafe\Paysafe::get_field( 'pay_for_order', $_GET, '' ) ) ) ) {
		return true;
	}
	
	return false;
}

/**
 * Is this the pay for order page.
 * Definition:  The page the customers go to when they want to pay for an existing order.
 *              Usually, goes through "My Account > Orders > Pay button"
 *
 * @since 3.7.0
 * @return bool
 */
function wc_paysafe_is_checkout_pay_page() {
	
	if ( is_checkout_pay_page() && 'true' !== wc_clean( wp_unslash( \WcPaysafe\Paysafe::get_field( 'pay_for_order', $_GET, '' ) ) ) ) {
		return true;
	}
	
	return false;
}
