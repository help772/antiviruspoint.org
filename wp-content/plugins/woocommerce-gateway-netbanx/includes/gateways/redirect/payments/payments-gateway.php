<?php

namespace WcPaysafe\Gateways\Redirect\Payments;

use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Abstracted_Gateway;
use WcPaysafe\Gateways\Redirect\Payments\Google_Pay\Google_Pay_Button;
use WcPaysafe\Helpers\Exceptions;
use WcPaysafe\Payment_Form;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Payments_Gateway extends Abstracted_Gateway {
	
	public static $api;
	/**
	 * @var array
	 */
	public $available_cc;
	public $card_options;
	public $testmode;
	public $locale;
	public $user_prefix;
	public $debug;
	public $synchro;
	public $send_order_details;
	public $send_ip_address;
	public $authorization_type;
	public $integration;
	public $saved_cards;
	public $save_card_text;
	public $save_card_checked_by_default;
	public $use_layover_3ds;
	public $use_layover_3ds2;
	protected $integration_object;
	public static $loaded;
	
	public function __construct() {
		
		$this->id   = 'paysafe_checkout_payments';
		$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', $this->icon );
		
		$this->has_fields   = false;
		$this->card_options = apply_filters(
			$this->id . '_card_options', array(
				'visa'       => __( 'Visa', 'wc_paysafe' ),
				'mastercard' => __( 'Master Card', 'wc_paysafe' ),
				'maestro'    => __( 'Maestro', 'wc_paysafe' ),
				'amex'       => __( 'American Express', 'wc_paysafe' ),
				'discover'   => __( 'Discover', 'wc_paysafe' ),
				'jcb'        => __( 'JCB', 'wc_paysafe' ),
				'diners'     => __( 'Diners', 'wc_paysafe' ),
			)
		);
		
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		$this->title            = $this->get_option( 'title' );
		$this->description      = $this->get_option( 'description' );
		$this->enabled          = $this->get_option( 'enabled', 'no' );
		$this->testmode         = $this->get_option( 'testmode', 'yes' );
		$this->locale           = $this->get_option( 'locale', 'en_GB' );
		$this->user_prefix      = $this->get_option( 'user_prefix', uniqid() . '-' );
		$this->debug            = $this->get_option( 'debug', 'no' );
		$this->synchro          = $this->get_option( 'synchro', 'no' );
		$this->send_ip_address  = $this->get_option( 'send_ip_address', 'yes' );
		$this->use_layover_3ds  = 'yes' === $this->get_option( 'use_layover_3ds', 'no' );
		$this->use_layover_3ds2 = 'yes' === $this->get_option( 'use_layover_3ds2', 'no' );
		$this->available_cc     = $this->get_option( 'available_cc', array() );
		
		// Vault
		$this->saved_cards                  = 'yes' == $this->get_option( 'saved_cards', 'no' );
		$this->save_card_text               = $this->get_option( 'save_card_text', __( 'Save to Account', 'wc_paysafe' ) );
		$this->save_card_checked_by_default = $this->get_option( 'save_card_checked_by_default', 'no' );
		
		$this->supports = array(
			'subscriptions',
			'products',
			'subscription_cancellation',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_amount_changes',
			'subscription_payment_method_change', // Subs 1.n compatibility
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'subscription_date_changes',
			'multiple_subscriptions',
			'refunds',
			'pre-orders',
			'tokenization',
		);
		
		$this->authorization_type = $this->get_option( 'authorization_type', 'sale' );
		
		$this->method_title       = __( 'Paysafe Checkout (Payments API)', 'wc_paysafe' );
		$this->method_description = __( 'Paysafe Checkout (Payments API) is hosted payments service currently supporting Cards, Google Pay and Apple Pay payments.', 'wc_paysafe' );
		
		$this->has_fields = true;
		
		// Run hooks
		$this->hooks();
	}
	
	/**
	 * Loads the hooks of the gateway
	 *
	 * @since 2.0
	 */
	public function hooks() {
		if ( true === self::$loaded ) {
			return;
		}
		
		// Actions
		add_action( 'woocommerce_api_wc_gateway_paysafe_response', array(
			$this,
			'process_server_to_server_response',
		) );
		
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		
		// Save options
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);
		
		// This is a custom filter that will run the capture_payment process and return its response
		add_filter( 'wc_paysafe_capture_payment_for_order', array( $this, 'capture_payment' ), 10, 2 );
		
		add_filter( 'woocommerce_checkout_customer_id', array( $this, 'add_user_cookie_action' ), 999 );
		
		do_action( 'wc_paysafe_plugin_loaded', $this );
		
		add_filter( 'woocommerce_order_actions', array( $this, 'add_order_capture_action' ) );
		add_action( 'woocommerce_order_action_paysafe_capture_payment', array(
			$this,
			'capture_payment',
		) );
		
		$google_pay_button = new Google_Pay_Button();
		$google_pay_button->hooks();
		
		$return_links = new Return_Links();
		$return_links->hooks();
		
		add_filter( 'woocommerce_update_order_review_fragments', [
			$this,
			'filter_updated_checkout_fragments',
		] );
		
		self::$loaded = true;
	}
	
	public function filter_updated_checkout_fragments( $fragments ) {
		$fragments['hasSubscription'] = false;
		if ( Paysafe::is_subscriptions_active() && \WC_Subscriptions_Cart::cart_contains_subscription() ) {
			$fragments['hasSubscription'] = true;
		}
		
		return $fragments;
	}
	
	/**
	 * Returns the integration object
	 *
	 * @return \WcPaysafe\Gateways\Redirect\Payments\Processes
	 */
	public function get_integration_object() {
		do_action( $this->id . '_main_integration_object', $this );
		
		if ( null === $this->integration_object ) {
			$this->set_integration_object( new Processes( $this ) );
		}
		
		return $this->integration_object;
	}
	
	/**
	 * Filters the user ID at checkout.
	 * If we don't have an ID, we will add an action to set the registered and logged in user cookie,
	 * so we can generate correct nonce
	 *
	 * @since 3.7.1
	 *
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public function add_user_cookie_action( $user_id ) {
		// No user, we require registration or we will be registering the user
		if ( 0 == $user_id && ( WC()->checkout()->is_registration_required() || (int) ! empty( $_POST['createaccount'] ) ) ) {
			add_action( 'set_logged_in_cookie', array( $this, 'add_logged_in_cookie_in_global' ) );
		}
		
		return $user_id;
	}
	
	/**
	 * Add the logged in user cookie to the globals, so we can generate a nonce
	 *
	 * @since 3.7.1
	 *
	 * @param $logged_in_cookie
	 */
	public function add_logged_in_cookie_in_global( $logged_in_cookie ) {
		if ( ! isset( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
			$_COOKIE[ LOGGED_IN_COOKIE ] = $logged_in_cookie;
		}
	}
	
	/**
	 * Admin Panel Options
	 */
	public function admin_options() {
		wp_enqueue_script( 'paysafe-admin' );
		?>
		<style>
					.paysafe_warning {
						display: none;
						color: #fff;
						margin: 0;
						background: #dc3232;
						box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);
						padding: 15px 10px;
					}
		</style>
		<?php
		parent::admin_options();
	} // End admin_options()
	
	/**
	 * @throws \Exception
	 */
	public function payment_fields() {
		parent::payment_fields();
		
		if ( $this->supports( 'tokenization' ) ) {
			if ( wc_paysafe_is_change_method_page() ) {
				// Change payment method iframe
				$subscription_id  = absint( $_GET['change_payment_method'] );
				$subscription     = wcs_get_subscription( $subscription_id );
				$subscription_key = wc_clean( wp_unslash( Paysafe::get_field( 'key', $_GET, '' ) ) ); // WPCS: input var ok, CSRF ok.
				
				// TODO: Do we need more checks here?
				if ( $subscription && ! hash_equals( $subscription->get_order_key(), $subscription_key ) ) {
					return false;
				}
				
				$integration        = $this->get_integration_object();
				$api_client         = $integration->get_api_client( new Order_Source( $subscription ), 'card' );
				$checkoutjs_service = $api_client->get_checkoutjs_service();
				$paysafe_args       = $checkoutjs_service->get_iframe_order_parameters( $subscription );
				
				$paysafe_args['processAction']               = 'change_payment_method';
				$paysafe_args['urls']['successRedirectPage'] = $subscription->get_view_order_url();
				$paysafe_args['options']['hideAmount']       = true;
				
				// If the label does not exist, add it
				$paysafe_args['options']['buttonLabel'] = _x( 'Submit', 'layover change method button label', 'wc_paysafe' );
				
				if ( $this->use_layover_3ds2 ) {
					$paysafe_args['options']['threeDs']['requestorChallengePreference'] = $this->get_option( 'threeds2_challenge_preference', 'NO_PREFERENCE' );
					
					if ( $paysafe_args['options']['threeDs']['transactionIntent'] ) {
						// Needs to be set additionally for add/update/change method verifications
						$paysafe_args['options']['threeDs']['transactionIntent'] = 'CHECK_ACCEPTANCE';
					}
				}
				
				$paysafe_args = apply_filters( 'wc_paysafe_checkoutjs_change_payment_method_props', $paysafe_args, $subscription );
				
				wp_localize_script( 'paysafe-checkout-v2', 'paysafe_layover_params', $paysafe_args );
				
				$payment_form = new Payment_Form( $this );
				$payment_form->output_payment_fields();
			} elseif ( wc_paysafe_is_pay_for_order_page() ) {
				global $wp;
				// The check for the page verifies that the 'order-pay' exists and is not empty
				$order_id  = absint( $wp->query_vars['order-pay'] );
				$order_key = wc_clean( wp_unslash( Paysafe::get_field( 'key', $_GET, '' ) ) ); // WPCS: input var ok, CSRF ok.
				$order     = wc_get_order( $order_id );
				
				// Order or payment link is invalid.
				if ( ! $order || $order->get_id() !== $order_id || ! hash_equals( $order->get_order_key(), $order_key ) ) {
					return false;
				}
				$paysafe_order                 = new Paysafe_Order( $order );
				$integration                   = $this->get_integration_object();
				$api_client                    = $integration->get_api_client( new Order_Source( $order ), 'card' );
				$checkoutjs_service            = $api_client->get_checkoutjs_service();
				$paysafe_args                  = $checkoutjs_service->get_iframe_order_parameters( $order );
				$paysafe_args['processAction'] = 'process_payment';
				
				// If the label does not exist, add it
				$paysafe_args['options']['buttonLabel'] = _x( 'Pay for order', 'layover pay for order button label', 'wc_paysafe' );
				
				if ( $this->use_layover_3ds2 && $paysafe_order->is_pre_order_with_tokenization() ) {
					// For a pre-ordered purchase, this is the date that the merchandise is expected to be available.
					// The ISO 8601 date format is expected, i.e., YYYY-MM-DD.
					$release_date = \WC_Pre_Orders_Product::get_localized_availability_date( \WC_Pre_Orders_Order::get_pre_order_product( $order ) );
					
					$paysafe_args['options']['threeDs']['orderItemDetails']['preOrderItemAvailabilityDate'] = date( 'Y-m-d', strtotime( $release_date ) );
					$paysafe_args['options']['threeDs']['orderItemDetails']['preOrderPurchaseIndicator']    = 'FUTURE_AVAILABILITY';
					$paysafe_args['options']['threeDs']['requestorChallengePreference']                     = $this->get_option( 'threeds2_challenge_preference', 'NO_PREFERENCE' );
				}
				
				$paysafe_args = apply_filters( 'wc_paysafe_checkoutjs_pay_for_order_props', $paysafe_args, $order );
				
				wp_localize_script( 'paysafe-checkout-v2', 'paysafe_layover_params', $paysafe_args );
				
				$payment_form = new Payment_Form( $this );
				$payment_form->output_payment_fields();
			} else {
				$payment_form = new Payment_Form( $this );
				$payment_form->output_payment_fields();
			}
		}
	}
	
	/**
	 * Process the payment and return the result
	 *
	 * @since 1.1
	 *
	 * @param int $order_id
	 * @throw \Exception
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		try {
			if ( true === apply_filters( 'wc_paysafe_clean_buffer_before_redirect', false ) ) {
				ob_clean();
			}
			
			$integration = $this->get_integration_object();
			
			return $integration->process_payment( $order_id );
		}
		catch ( \Exception $e ) {
			
			$exception = new Exceptions( $e );
			
			wc_paysafe_payments_add_debug_log( 'Payment response errors: ' . $exception->get_errors() );
			wc_paysafe_payments_add_debug_log( 'Payment response details: ' . $exception->get_details() );
			
			wc_add_notice( $exception->get_errors(), 'error' );
		}
	}
	
	/**
	 * Call the iframe generation method
	 *
	 * @param int $order_id
	 *
	 * @return void
	 */
	public function receipt_page( $order_id ) {
		$this->get_integration_object()->receipt_page( $order_id );
	}
	
	/**
	 * Check the payment response and process the order
	 */
	public function process_server_to_server_response() {
		$integration = $this->get_integration_object();
		$integration->process_server_to_server_response( 'hosted' );
	}
	
	/**
	 * Process automatic refunds
	 *
	 * @since 2.0
	 *
	 * @param int    $order_id
	 * @param null   $amount
	 * @param string $reason
	 *
	 * @throws \Exception
	 *
	 * @return bool|\WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		try {
			$order = wc_get_order( $order_id );
			
			$integration = $this->get_integration_object();
			
			$refund = $integration->process_refund( $order, $amount, $reason );
			
			// Debug log
			wc_paysafe_payments_add_debug_log( 'Refund completed.' );
			
			// Add order note
			$order->add_order_note(
				sprintf(
					__(
						'Refunded %s. Refund ID: %s. Reconciliation ID: %s. %s',
						'wc_paysafe'
					),
					$amount,
					$refund->get_id(),
					$refund->get_gateway_reconciliation_id(),
					( '' != $reason ) ? sprintf(
						__(
							'Credit Note: %s.', 'wc_paysafe'
						), $reason
					) : ''
				)
			);
			
			return true;
		}
		catch ( \Exception $ex ) {
			return new \WP_Error( 'paysafe-error', $ex->getMessage() );
		}
	}
	
	/**
	 * Adds capture payment action to the order actions
	 *
	 * @since 3.2.0
	 *
	 * @param $actions
	 *
	 * @return mixed
	 */
	public function add_order_capture_action( $actions ) {
		
		/**
		 * @var \WC_Order $theorder
		 */
		global $theorder;
		
		$method = WC_Compatibility::get_prop( $theorder, 'payment_method' );
		if ( $this->id != $method ) {
			return $actions;
		}
		
		$ps_order = new Paysafe_Order( $theorder );
		
		$is_captured            = $ps_order->get_is_payment_captured();
		$allowed_order_statuses = \WcPaysafe\Admin\Capture::get_capture_allowed_order_statuses();
		
		if ( $ps_order->is_subscription() || false != $is_captured || ! in_array( $theorder->get_status(), $allowed_order_statuses ) ) {
			return $actions;
		}
		
		$authorized_amount = $ps_order->get_order_amount_authorized();
		if ( empty( $authorized_amount ) ) {
			$authorized_amount = $theorder->get_total();
		}
		
		$amount_captured   = $ps_order->get_order_amount_captured();
		$authorized_amount = wc_format_decimal( $authorized_amount );
		$amount_captured   = wc_format_decimal( $amount_captured );
		
		$amount_allowed = wc_format_decimal( $authorized_amount - $amount_captured );
		
		$actions['paysafe_capture_payment'] = __( 'Capture Payment (' . get_woocommerce_currency_symbol() . $amount_allowed . ')', 'wc_paysafe' );
		
		return $actions;
	}
	
	/**
	 * Capture payment
	 *
	 * @since 3.2.0
	 *
	 * @param \WC_Order  $order
	 * @param float|null $amount
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	public function capture_payment( $order, $amount = null ) {
		
		if ( ! $order instanceof \WC_Order ) {
			return $order;
		}
		
		$ps_order = new Paysafe_Order( $order );
		
		if ( $order->get_payment_method() != $this->id ) {
			return $order;
		}
		
		$is_captured = $ps_order->get_is_payment_captured();
		
		// Bail, if we captured the amount already
		if ( false != $is_captured ) {
			return __( 'Order amount is already captured', 'wc_paysafe' );
		}
		
		try {
			// We need to have at lease one settlement
			$transaction_id = $ps_order->get_authorization_id();
			
			// Bail, if there is no reference|settlement ID
			if ( '' == $transaction_id ) {
				throw new \Exception( __( 'Missing Transaction ID. The order does not have all required information to process process a capture.', 'wc_paysafe' ) );
			}
			
			$authorized_amount = wc_format_decimal( $ps_order->get_order_amount_authorized(), 2 );
			if ( empty( $authorized_amount ) ) {
				$authorized_amount = wc_format_decimal( $order->get_total(), 2 );
			}
			
			$amount                = wc_format_decimal( $amount, 2 );
			$amount_captured       = wc_format_decimal( $ps_order->get_order_amount_captured(), 2 );
			$amount_captured_total = wc_format_decimal( $amount + $amount_captured, 2 );
			// Can't capture more than the initially authorized amount
			if ( $authorized_amount < $amount_captured_total ) {
				throw new \Exception( sprintf( __( "You can't capture more than the initially authorized amount: %s.", 'wc_paysafe' ), $authorized_amount ) );
			}
			
			if ( 0 == $amount ) {
				$amount = wc_format_decimal( $authorized_amount - $amount_captured, 2 );
			}
			
			$integration = $this->get_integration_object();
			$integration->process_capture( $order, $amount );
			
			return true;
		}
		catch ( \Exception $e ) {
			$message = sprintf( __( 'Capture failed. Message: %s', 'wc_paysafe' ), $e->getMessage() );
			$order->add_order_note( $message );
			
			return $message;
		}
	}
	
	/**
	 * Delete a customer profile from both PayTrace and store systems.
	 *
	 * @param \WC_Payment_Token $wc_token The ID of the save, in the database, profile
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	function delete_profile_token( $wc_token ) {
		return $this->get_integration_object()->delete_profile_token( $wc_token );
	}
	
	/**
	 * Returns the account ID used for the request
	 *
	 * Note : The $payment_type values explained:
	 *      1. card - Cards API transaction
	 *      2. applePay - Direct Debit API transaction
	 *      4. iframe - Setup for the Paysafe Checkout JS iframe. Default the "card" account ID is used
	 *
	 * @param null|Data_Source_Interface $data_source
	 * @param string                     $payment_type cards|applePay|iframe
	 *
	 * @return int
	 */
	public function get_account_id( $data_source = null, $payment_type = 'card' ) {
		$payment_type = strtolower( $payment_type );
		$currency     = $data_source ? $data_source->get_currency() : get_woocommerce_currency();
		
		/**
		 * @var array $accounts
		 */
		$accounts   = $this->get_option( $payment_type . '_accounts', array() );
		$account_id = '';
		
		foreach ( $accounts as $account ) {
			if ( $currency == $account['account_currency'] ) {
				$account_id = $account['account_id'];
				break;
			}
		}
		
		// The order is not guaranteed, so make sure you check for it
		return apply_filters( 'wc_paysafe_checkoutjs_v2_account_id', $account_id, $data_source, $payment_type );
	}
	
	public function get_available_payment_methods() {
		$methods = $this->get_option( 'available_payment_options', [ 'card' ] );
		
		if ( ! $methods ) {
			return [ 'card' ];
		}
		
		return $methods;
	}
	
	/**
	 * Is Available for the plugin.
	 *
	 * @return boolean
	 */
	public function is_available() {
		if ( 'yes' != $this->enabled ) {
			return false;
		}
		
		return $this->get_integration_object()->is_available();
	}
	
	/**
	 * Should we send the customer IP to Paysafe
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	public function send_customer_ip() {
		return 'yes' == $this->send_ip_address;
	}
	
	/**
	 * Returns the set request locale
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return mixed
	 */
	public function get_locale( \WC_Order $order ) {
		/**
		 * @deprecated 'wc_netbanx_locale' Will be removed soon, use 'wc_paysafe_locale'
		 */
		$locale = apply_filters( 'wc_netbanx_locale', $this->locale, $order );
		$locale = apply_filters( 'wc_paysafe_locale', $locale, $order );
		
		return $locale;
	}
	
	/**
	 * Do we send the order details to paysafe
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function maybe_send_order_details() {
		return 'yes' == $this->send_order_details;
	}
	
	/**
	 * Returns the customer IP address
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_user_ip_addr() {
		
		$ip = '127.0.0.1';
		
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = wc_clean( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = wc_clean( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = wc_clean( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		
		$ip = wc_clean( wp_unslash( $ip ) );
		
		if ( false !== strpos( $ip, ',' ) ) {
			$split = explode( ',', $ip );
			$ip    = trim( $split[0] );
		}
		
		return $ip;
	}
	
	/**
	 * Returns true, if order contains Subscription
	 *
	 * @since      2.0
	 *
	 * @deprecated 3.3.0 Use the Paysafe_Order::order_contains_subscription instead
	 *
	 * @param \WC_Order $order
	 *
	 * @return bool
	 */
	public function order_contains_subscription( \WC_Order $order ) {
		// This class does not support Subscriptions, so overwrite to support it.
		return false;
	}
	
	/**
	 * Returns true, if order contains Pre-Order
	 *
	 * @since      2.0
	 *
	 * @deprecated 3.3.0 Use the Paysafe_Order::contains_pre_order instead
	 *
	 * @param \WC_Order $order
	 *
	 * @return bool
	 */
	public function order_contains_pre_order( \WC_Order $order ) {
		// This class does not support Pre-Orders, so overwrite to support it.
		return false;
	}
	
	/**
	 * Generates the HTML for the account IDs fields
	 *
	 * @since 3.4.0
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return false|string
	 */
	public function generate_account_ids_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);
		
		$data = wp_parse_args( $data, $defaults );
		
		$currencies = get_woocommerce_currencies();
		$pairs      = $this->get_option( $key, $data['default'] );
		
		ob_start();
		?>
		<tr valign="top" class="<?php echo esc_attr( $key ); ?>-repeater-field-wrap repeater-field-wrap" data-id="<?php echo esc_attr( $key ); ?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp">
				<style>
									.repeater-field-wrap .paysafe-remove-account-id,
									.repeater-field-wrap .paysafe-remove-account-id:hover {
										background-color: #ED5E68;
										color: #F5D9DB;
										box-shadow: 0 1px 0 #F5D9DB;
										border-color: #ed5e68;
									}
				</style>
				<?php foreach ( $pairs as $pair_key => $account_data ) {
					?>
					<fieldset class="repeater-field" data-field-key="<?php echo esc_attr( $pair_key ); ?>">
						<legend class="screen-reader-text">
							<span><?php echo wp_kses_post( $data['title'] ); ?></span>
						</legend>

						<select name="<?php echo esc_attr( $key ); ?>_account_currency[<?php echo esc_attr( $pair_key ); ?>]" class="<?php echo esc_attr( $data['class'] ); ?> wc-enhanced-select">
							<option value="" <?php selected( true, false ) ?>><?php echo esc_html( __( 'Choose Currency', 'wc_paysafe' ) ) ?></option>
							<?php foreach ( $currencies as $currency_code => $currency_name ) { ?>
								<option value="<?php echo esc_attr( $currency_code ); ?>" <?php selected( $currency_code, $account_data['account_currency'] ) ?>><?php echo esc_html( $currency_name ); ?></option>
							<?php } ?>
						</select>

						<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( 'text' ); ?>" name="<?php echo esc_attr( $key ); ?>_account_id[<?php echo esc_attr( $pair_key ); ?>]" id="<?php echo esc_attr( $field_key ); ?>-<?php echo esc_attr( $pair_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $account_data['account_id'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> />
						<input type="button" class="paysafe-remove-account-id button-secondary" value="<?php echo esc_attr( __( 'Remove', 'wc_paysafe' ) ); ?>" />
						
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
					</fieldset>
				<?php } ?>
				<p>
					<input type="button" class="paysafe-add-account-id button-secondary" value="<?php echo esc_attr( __( 'Add Account ID', 'wc_paysafe' ) ); ?>" />
				</p>

				<fieldset class="repeater-field template-fieldset" data-field-key="{number}" style="display: none;">
					<legend class="screen-reader-text">
						<span><?php echo wp_kses_post( $data['title'] ); ?></span>
					</legend>

					<select name="{<?php echo esc_attr( $key ); ?>_account_currency}[{number}]" class="<?php echo esc_attr( $data['class'] ); ?> enhanced wc-enhanced-select">
						<option value="" <?php selected( true, false ) ?>><?php echo esc_html( __( 'Choose Currency', 'wc_paysafe' ) ); ?></option>
						<?php foreach ( $currencies as $currency_code => $currency_name ) { ?>
							<option value="<?php echo esc_attr( $currency_code ); ?>"><?php echo esc_html( $currency_name ); ?></option>
						<?php } ?>
					</select>

					<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( 'text' ); ?>" name="{<?php echo esc_attr( $key ); ?>_account_id}[{number}]" id="<?php echo esc_attr( $field_key ); ?>-{number}" style="<?php echo esc_attr( $data['css'] ); ?>" value="" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> />
					<input type="button" class="paysafe-remove-account-id button-secondary" value="<?php echo esc_attr( __( 'Remove', 'wc_paysafe' ) ); ?>" />
					
					<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
				</fieldset>
			</td>
		</tr>
		<?php
		
		return ob_get_clean();
	}
	
	/**
	 * Validates the account ids fields and returns the formatted data
	 *
	 * @since 3.4.0
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return array
	 */
	public function validate_account_ids_field( $key, $value ) {
		$pairs = array();
		if ( isset( $_POST[ $key . '_account_id' ] ) && isset( $_POST[ $key . '_account_currency' ] ) ) {
			$count       = 0;
			$account_ids = wc_clean( wp_unslash( $_POST[ $key . '_account_id' ] ) );
			foreach ( $account_ids as $i => $id ) {
				
				$currency_code = isset( $_POST[ $key . '_account_currency' ][ $i ] ) ? wc_clean( wp_unslash( $_POST[ $key . '_account_currency' ][ $i ] ) ) : '';
				
				if ( ! $id || ! $currency_code ) {
					continue;
				}
				
				$pairs[ $count ]['account_id']       = wc_clean( wp_unslash( trim( stripslashes( $id ) ) ) );
				$pairs[ $count ]['account_currency'] = wc_clean( wp_unslash( trim( stripslashes( $currency_code ) ) ) );
				$count ++;
			}
		}
		
		// Keep at least one row of pairs
		if ( empty( $pairs ) ) {
			$pairs[] = array(
				'account_currency' => get_woocommerce_currency(),
				'account_id'       => '',
			);
		}
		
		return $pairs;
	}
	
	public function is_cvv_required() {
		if ( wc_paysafe_is_change_method_page()
		     || wc_paysafe_is_update_payment_method_page()
		     || is_add_payment_method_page()
		) {
			return false;
		}
		
		return ( $this->use_layover_3ds || $this->use_layover_3ds2 ) && 'yes' == $this->get_option( 'is_cvv_required_with_tokens', 'yes' );
	}
}