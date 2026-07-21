<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Abstracted_Gateway;
use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Cart_Checkout_Helpers;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe;
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
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Processes {
	
	/**
	 * @var Gateway
	 */
	private $gateway = null;
	
	/**
	 * Constructor of the class
	 *
	 * @since 2.0
	 *
	 * @param Gateway|Abstracted_Gateway $gateway
	 */
	public function __construct( Gateway $gateway ) {
		$this->set_gateway( $gateway );
	}
	
	/**
	 * Sets the gateway class to a class variable
	 *
	 * @since 2.0
	 *
	 * @param Gateway $gateway
	 */
	private function set_gateway( Gateway $gateway ) {
		$this->gateway = $gateway;
	}
	
	/**
	 * Returns the variable with the gateway class
	 *
	 * @since 2.0
	 *
	 * @return Gateway
	 * @throws \InvalidArgumentException
	 */
	public function get_gateway() {
		if ( null == $this->gateway ) {
			throw new \InvalidArgumentException( __( 'Gateway class not initiated.', 'wc_paysafe' ) );
		}
		
		return $this->gateway;
	}
	
	/**
	 * Returns the settings for this integration
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public function get_settings() {
		$obj = new Settings( $this->get_gateway() );
		
		return $obj->get_settings();
	}
	
	/**
	 * Checks to see if we have the integration set with the minimum required information for operations
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( '' == $this->get_gateway()->get_option( 'api_user_name' )
		     || '' == $this->get_gateway()->get_option( 'api_password' )
		) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the Request object based on the order contents
	 *
	 * @param \WC_Order $order
	 *
	 * @return Request_Hosted|Request_Hosted_Pre_Orders|Request_Hosted_Subscriptions
	 */
	public function get_request_object( \WC_Order $order ) {
		$paysafe_order = new Paysafe_Order( $order );
		
		if ( $paysafe_order->contains_pre_order() ) {
			$object = new Request_Hosted_Pre_Orders( $this->get_gateway() );
		} elseif ( $paysafe_order->contains_subscription() || $paysafe_order->is_subscription() ) {
			// Subscription
			$object = new Request_Hosted_Subscriptions( $this->get_gateway() );
		} else {
			$object = new Request_Hosted( $this->get_gateway() );
		}
		
		return $object;
	}
	
	/**
	 * @param string $type
	 *
	 * @return Response_Hosted|Response_Hosted_Addons
	 */
	public function get_response_object( $type = 'hosted' ) {
		if ( 'hosted_addons' == $type ) {
			$object = new Response_Hosted_Addons( $this->get_gateway() );
		} else {
			$object = new Response_Hosted( $this->get_gateway() );
		}
		
		return $object;
	}
	
	/**
	 * Process Payment of this integration
	 *
	 * @param $order_id
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( (int) $order_id );
		if ( $this->is_using_iframe() ) {
			// Redirect to the Pay page
			$url = $order->get_checkout_payment_url( true );
		} else {
			$request = $this->get_request_object( $order );
			$url     = $request->get_payment_url( $order );
		}
		
		return array(
			'result'   => 'success',
			'redirect' => $url,
		);
	}
	
	/**
	 * Receipt Page output for this integration
	 *
	 * @param $order_id
	 */
	public function receipt_page( $order_id ) {
		try {
			$order   = wc_get_order( $order_id );
			$request = $this->get_request_object( $order );
			
			echo Formatting::kses_form_html( $this->string_pay_with_form_below() );
			echo $this->load_iframe_template( $request->get_payment_url( $order ) );
		}
		catch ( \Exception $e ) {
			echo $this->iframe_error_notification_message( $e->getMessage() );
		}
	}
	
	/**
	 * @param \WC_Order $order
	 * @param null      $amount
	 * @param string    $reason
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function process_refund( $order, $amount = null, $reason = '' ) {
		$request       = new Request_Hosted( $this->get_gateway() );
		$paysafe_order = new Paysafe_Order( $order );
		
		$transaction_id = $paysafe_order->get_payment_order_id();
		if ( '' == $transaction_id ) {
			throw new \Exception( __( 'The order does not have a valid Paysafe transaction ID. Refund is not possible.', 'wc_paysafe' ) );
		}
		
		$refund = $request->process_order_refund( $transaction_id, $amount );
		
		$response = new Response_Hosted( $this->get_gateway() );
		$response->process_refund_response( $refund, $order, $amount, $transaction_id );
		
		return $refund->confirmationNumber;
	}
	
	/**
	 * @param \WC_Order $order
	 * @param float     $amount
	 *
	 * @throws \Exception
	 */
	public function process_capture( $order, $amount ) {
		$hosted_request = new Request_Hosted( $this->get_gateway() );
		$params         = $hosted_request->prepare_settlement( $order, $amount );
		$settlement     = $hosted_request->process_settlement( $params );
		
		$response = new Response_Hosted( $this->get_gateway() );
		$response->process_settlement_response( $settlement, $order, $amount );
	}
	
	/**
	 * Check the payment response and process the order
	 *
	 * @param string $type
	 *
	 * @since 2.0
	 */
	public function process_server_to_server_response( $type = 'hosted' ) {
		try {
			$response = $this->get_response_object( $type );
			$response->process_response();
			
			Cart_Checkout_Helpers::empty_cart();
		}
		catch ( \Exception $ex ) {
			// Debug log
			wc_paysafe_add_debug_log( $ex->getMessage() );
			
			$this->end_by_failed_validation();
		}
		
		// Return 204 status
		header( "HTTP/1.0 204 No Content" );
		exit;
	}
	
	/**
	 * Perform a subscription scheduled payment
	 *
	 * @since 2.0
	 *
	 * @param           $amount_to_charge
	 * @param \WC_Order $renewal_order
	 */
	public function scheduled_subscription_payment_request( $amount_to_charge, $renewal_order ) {
		try {
			wc_paysafe_add_debug_log( 'Scheduled payment: ' . print_r( WC_Compatibility::get_order_id( $renewal_order ), true ) );
			
			// Process a rebill request
			$request = $this->get_request_object( $renewal_order );
			$rebill  = $request->process_subscription_rebill( $renewal_order, $amount_to_charge );
			
			// Process the order from the response
			$response = $this->get_response_object( 'hosted_addons' );
			$response->process_order_based_on_response( $rebill, $renewal_order );;
		}
		catch ( \Exception $e ) {
			$renewal_order->update_status( 'failed', $e->getMessage() );
			
			// Debug log
			wc_paysafe_add_debug_log( $e->getMessage() );
		}
	}
	
	/**
	 * Charge the payment on order release
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	public function process_pre_order_release_payment( \WC_Order $order ) {
		try {
			$request = $this->get_request_object( $order );
			$rebill  = $request->process_pre_orders_rebill( $order );
			
			$response = $this->get_response_object( 'hosted_addons' );
			$response->process_order_based_on_response( $rebill, $order );
		}
		catch ( \Exception $e ) {
			$order->add_order_note( $e->getMessage(), 'error' );
		}
	}
	
	public function changed_failing_payment_method( $subscription, $renewal_order ) {
		$ps_subscription = new Paysafe_Order( $subscription );
		$ps_renewal      = new Paysafe_Order( $renewal_order );
		
		// Hosted is the id, checkout is the token
		$ps_subscription->save_payment_order_id( $ps_renewal->get_payment_order_id() );
	}
	
	/**
	 * Don't transfer Paysafe meta to resubscribe orders.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $resubscribe_order The order created for the customer to resubscribe to the old expired/cancelled subscription
	 *
	 * @return void
	 */
	public function remove_renewal_order_meta( $resubscribe_order ) {
		$ps_order = new Paysafe_Order( $resubscribe_order );
		$ps_order->delete_payment_order_id();
		$ps_order->delete_payment_type_detials();
	}
	
	/**
	 * The string on Pay page, prompting user to pay with the form below
	 *
	 * @return string
	 */
	public function string_pay_with_form_below() {
		return apply_filters( 'wc_paysafe_redirect_hosted_message_above_iframe', __( 'Thank you for your order, fill in the form below to pay Paysafe.', 'wc_paysafe' ), $this );
	}
	
	/**
	 * Load the iframe template
	 *
	 * @since 2.0
	 *
	 * @param $location
	 *
	 * @return string
	 */
	public function load_iframe_template( $location ) {
		ob_start();
		
		$width  = $this->get_iframe_dimension( $this->get_gateway()->get_option( 'iframe_width' ), '700' );
		$height = $this->get_iframe_dimension( $this->get_gateway()->get_option( 'iframe_height' ), '850' );
		
		wc_get_template(
			'paysafe/iframe.php',
			array(
				'location' => $location,
				'width'    => $width,
				'height'   => $height,
				'scroll'   => $this->get_gateway()->get_option( 'iframe_scroll' ),
			),
			'',
			Paysafe::plugin_path() . '/templates/'
		);
		
		return ob_get_clean();
	}
	
	/**
	 * Performs checks on the set iframe dimensions and returns the value or the default
	 *
	 * @since 2.0
	 *
	 * @param string $dimension The string dimension in pixels (111) or in percentage (55%)
	 * @param string $default   Default value in pixels only
	 *
	 * @return int|string
	 */
	public function get_iframe_dimension( $dimension, $default = '0' ) {
		if ( substr( $dimension, - 1 ) == '%' ) {
			if ( is_numeric( substr( $dimension, 0, ( strlen( $dimension ) - 1 ) ) ) ) {
				$value = $dimension;
			} else {
				$value = $default;
			}
		} elseif ( is_numeric( $dimension ) ) {
			$value = $dimension;
		} else {
			$value = $default;
		}
		
		return $value;
	}
	
	/**
	 * Adds the iframe error notification
	 *
	 * @since 2.0
	 *
	 * @param $error_message
	 *
	 * @return string
	 */
	public function iframe_error_notification_message( $error_message ) {
		// Any exception is logged and flags a notice
		wc_paysafe_add_debug_log( 'Iframe error: ' . $error_message );
		
		ob_start();
		$message = Formatting::kses_form_html( sprintf(
			__(
				'Error generating the payment form. Please refresh the page and try again.
			 		If error persists, please contact the administrator. Error message: %s ', 'wc_paysafe'
			),
			$error_message
		) );
		
		echo '<p class="paysafe-iframe-error">' . $message . '</p>';
		
		return apply_filters( 'wc_paysafe_redirect_hosted_iframe_error_message', ob_get_clean(), $error_message, $this );
	}
	
	/**
	 * Are we using iframe?
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function is_using_iframe() {
		return ( 'yes' == $this->get_gateway()->get_option( 'use_iframe', 'no' ) );
	}
	
	/**
	 * Ends execution with failed validation message.
	 *
	 * @since 2.0
	 */
	public function end_by_failed_validation() {
		die( esc_html( __( 'The request did not pass validation.', 'wc_paysafe' ) ) );
	}
}