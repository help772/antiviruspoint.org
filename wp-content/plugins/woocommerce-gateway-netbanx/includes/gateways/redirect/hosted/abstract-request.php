<?php

namespace WcPaysafe\Gateways\Redirect\Hosted;

use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Paysafe;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract class handling requests to the Paysafe servers.
 *
 *
 * @since  2.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Abstract_Request extends Abstract_Hosted {
	
	private $profile_id = '';
	private $profile_token = '';
	
	/**
	 * Setup the Paysafe payment request
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function build_payment_request( \WC_Order $order ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Generating payment form for order #' . WC_Compatibility::get_order_id( $order ) );
		
		$amount        = $this->format_amount( $order->get_total() );
		$paysafe_order = new Paysafe_Order( $order );
		
		$request_params = array(
			'merchantRefNum'            => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ),
			'totalAmount'               => $amount,
			'currencyCode'              => WC_Compatibility::get_order_currency( $order ),
			'customerNotificationEmail' => WC_Compatibility::get_order_billing_email( $order ),
			'locale'                    => $this->get_locale( $order ),
			'billingDetails'            => $this->get_billing_fields( $order ),
			'callback'                  => $this->get_callback_fields(),
			'redirect'                  => $this->get_redirect_fields(),
			'addendumData'              => $this->get_addendum_data_fields( $order ),
			'link'                      => $this->get_link_fields( $order ),
		);
		
		// Add the merchant notifications to the request
		if ( '' != $this->get_gateway()->get_option( 'merchant_email_address' ) ) {
			$request_params['merchantNotificationEmail'] = $this->get_gateway()->get_option( 'merchant_email_address' );
		}
		
		// Add shipping fields
		if ( '' !== WC_Compatibility::get_order_shipping_address_1( $order ) ) {
			$request_params['shippingDetails'] = $this->get_shipping_fields( $order );
		}
		
		// Add order details
		if ( $this->maybe_send_order_details() ) {
			// If prices include tax send the order as one item with all products in the name
			if ( 'yes' == WC_Compatibility::get_prop( $order, 'prices_include_tax' ) ) {
				$request_params['shoppingCart'] = $this->get_shopping_cart_fields( $order, 'including' );
			} else {
				$request_params['shoppingCart'] = $this->get_shopping_cart_fields( $order, 'excluding' );
				
				// Add fees only, if needed.
				if ( 0 < $order->get_total_discount()
				     || 0 < $order->get_total_tax()
				     || 0 < WC_Compatibility::get_total_shipping( $order )
				     || 0 < count( $order->get_items( 'fee' ) )
				) {
					$request_params['ancillaryFees'] = $this->get_ancillary_fees_fields( $order );
				}
			}
		}
		
		// If we don't have to charge a payment. Total 0
		if ( 0 == $amount || 'auth' == $this->get_gateway()->authorization_type ) {
			$request_params['extendedOptions'][] = array(
				'key'   => 'authType',
				'value' => 'auth',
			);
		}
		
		// Add the customer profile node
		$request_params['profile'] = $this->add_customer_profile_fields( $order );
		
		// Are we sending the customer IP with the request
		if ( $this->send_customer_ip() ) {
			$request_params['customerIp'] = $this->get_user_ip_addr();
		}
		
		return $request_params;
	}
	
	/**
	 * Process an order payment request.
	 * The request sets up a payment and returns an object with the payment URL link.
	 *
	 * @since 2.0
	 *
	 * @param          $params
	 *
	 * @throws \Exception
	 * @return \Paysafe\HostedPayment\Order
	 */
	public function process_order( $params ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Payment request: ' . print_r( $params, true ) );
		
		// Send request to setup the payment token
		$response = $this->get_client()->hostedPaymentService()->processOrder(
			new \Paysafe\HostedPayment\Order( $params )
		);
		
		// Debug log
		wc_paysafe_add_debug_log( 'Payment response: ' . print_r( $response, true ) );
		
		return $response;
	}
	
	/**
	 * Process an order payment refund.
	 *
	 * @since 2.0
	 *
	 * @param          $params
	 *
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Refund
	 */
	public function process_refund( $params ) {
		// Debug log
		wc_paysafe_add_debug_log( 'Refund request: ' . print_r( $params, true ) );
		
		// Send request to setup the payment token
		$refund = $this->get_client()->hostedPaymentService()->refund(
			new \Paysafe\HostedPayment\Refund( $params )
		);
		
		// Debug log
		wc_paysafe_add_debug_log( 'Refund response: ' . print_r( $refund, true ) );
		
		return $refund;
	}
	
	/**
	 * Process order look up.
	 *
	 * @since 2.0
	 *
	 * @param $id
	 *
	 * @throws \Exception
	 * @return \Paysafe\HostedPayment\Order
	 */
	public function process_order_lookup( $id ) {
		$params = array(
			'id' => $id,
		);
		
		// Send request to setup the payment token
		$lookup = $this->get_client()->hostedPaymentService()->getOrder(
			new \Paysafe\HostedPayment\Order( $params )
		);
		
		return $lookup;
	}
	
	/**
	 * Process a rebill payment
	 *
	 * @since 2.0
	 *
	 * @param array $params
	 *
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Order
	 */
	public function process_rebill( $params ) {
		
		wc_paysafe_add_debug_log( 'Rebill request: ' . print_r( $params, true ) );
		
		// Send request to setup the payment token
		$rebill = $this->get_client()->hostedPaymentService()->rebillOrder(
			new \Paysafe\HostedPayment\Order( $params )
		);
		
		wc_paysafe_add_debug_log( 'Rebill response: ' . print_r( $rebill, true ) );
		
		return $rebill;
	}
	
	/**
	 * Process a settlement payment
	 *
	 * @since 3.2.0
	 *
	 * @param array $params
	 *
	 * @throws \Exception
	 *
	 * @return \Paysafe\HostedPayment\Settlement
	 */
	public function process_settlement( $params ) {
		
		wc_paysafe_add_debug_log( 'Settle request: ' . print_r( $params, true ) );
		
		// Send request to setup the payment token
		$result = $this->get_client()->hostedPaymentService()->settlement(
			new \Paysafe\HostedPayment\Settlement( $params )
		);
		
		wc_paysafe_add_debug_log( 'Settle response: ' . print_r( $result, true ) );
		
		return $result;
	}
	
	/**
	 * Attempt to process an order.
	 * It will run two attempts to process an order.
	 * If the first attempt fails because of a already used profile merchantCustomerId,
	 * it will increment the merchantCustomerId suffix and attempt the order again.
	 * This is done because of instances where a first attempt on payment fails,
	 * but even though a profile was successful it was not saved to the customer.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param array     $params The Request fields
	 *
	 * @return \Paysafe\HostedPayment\Order
	 */
	public function attempt_to_process_order( \WC_Order $order, $params ) {
		$response = '';
		for ( $i = 1; $i <= 2; $i ++ ) {
			try {
				$response = $this->process_order( $params );
				
				// Save the payment profile after the payment request.
				$this->save_customer_payment_profile( $order, $response );
				
				break;
			}
			catch ( \Exception $e ) {
				wc_paysafe_add_debug_log( 'Exception is: Code: ' . $e->getCode() );
				wc_paysafe_add_debug_log( 'Exception is: Message: ' . $e->getMessage() );
				
				if ( '7505' == $e->getCode() && 2 !== $i ) {
					
					wc_paysafe_add_debug_log( 'Re-running: ' . $e->getCode() );
					
					// Increment the user profile suffix and go on to the next attempt
					$this->increment_user_profile_id_suffix( $order->get_user_id() );
					
					continue;
				} else {
					// Throw any other exceptions the same method they came in.
					$exception = get_class( $e );
					throw new $exception( $e->getMessage(), $e->getCode() );
				}
			}
		}
		
		return $response;
	}
	
	/**
	 * Increment the user profile suffix.
	 * Suffix is mostly used when there where problems with creation of user profile.
	 *
	 * @since 2.0
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function increment_user_profile_id_suffix( $user_id ) {
		// We only increment registered users.
		// Guests are unique ids, so we can just retry
		if ( 0 === (int) $user_id ) {
			return;
		}
		
		$current_suffix = $this->get_merchant_profile_id_suffix( $user_id );
		$increment      = absint( $current_suffix ) + 1;
		
		return $this->update_merchant_profile_id_suffix( $user_id, $increment );
	}
	
	/**
	 * Does the user have a Paysafe profile on file
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function user_has_profile() {
		if ( '' != $this->get_profile_id() ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns order suffix, to prevent duplicate order reference numbers
	 *
	 * @since      2.0
	 * @deprecated Use the Paysafe_Order::get_attempts_suffix instead
	 *
	 * @param \WC_Order $order
	 * @param string    $type
	 *
	 * @return string
	 */
	public function get_attempts_suffix( \WC_Order $order, $type = 'order' ) {
		// Add a retry count sufix to the orderID.
		$ps_order = new Paysafe_Order( $order );
		
		return $ps_order->get_attempts_suffix( $type );
	}
	
	/**
	 * Returns if the callback should be synchronous or not
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function is_synchro() {
		return ( 'yes' == $this->get_gateway()->get_option( 'synchro' ) );
	}
	
	/**
	 * Should we send the customer IP to Paysafe
	 *
	 * @since 3.0.3
	 *
	 * @return bool
	 */
	public function send_customer_ip() {
		return 'yes' == $this->get_gateway()->get_option( 'send_ip_address', 'yes' );
	}
	
	/**
	 * Returns the customer IP address
	 *
	 * @since 2.0
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
		$locale = apply_filters( 'wc_netbanx_locale', $this->get_gateway()->get_option( 'locale' ), $order );
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
		return ( 'yes' == $this->get_gateway()->get_option( 'send_order_details' ) );
	}
	
	/**
	 * Sets the Paysafe profile ID to class variable
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	private function set_profile_id( \WC_Order $order ) {
		$user_profile_id = $this->get_user_profile_id( $order->get_user_id() );
		
		// Check the user for profile ID
		if ( '' != $user_profile_id ) {
			$this->profile_id = $user_profile_id;
		}
	}
	
	/**
	 * Sets the Paysafe profile token to class variable
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 */
	private function set_profile_token( \WC_Order $order ) {
		$user_profile_token = $this->get_user_profile_token( $order->get_user_id() );
		
		// Check the user for profile Token
		if ( '' != $user_profile_token ) {
			$this->profile_token = $user_profile_token;
		}
	}
	
	/**
	 * Returns Paysafe profile ID
	 *
	 * @since 2.0
	 *
	 * @return null
	 */
	public function get_profile_id() {
		return $this->profile_id;
	}
	
	/**
	 * Returns Paysafe profile token
	 *
	 * @since 2.0
	 *
	 * @return null
	 */
	public function get_profile_token() {
		return $this->profile_token;
	}
	
	/**
	 * Add a customer profile to the payment request
	 *
	 * Profile should be two types:
	 *
	 * I - Saved to the user itself
	 *        1. We can use it to process future payments for that user
	 * II - Saved to the order itself
	 *        1. In cases where the user is a guest, we will save the profile and token to the order
	 *        2. We can process that profile and move it from order to order or Subs to Subs
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 *
	 * @return array Profile element fields
	 */
	public function add_customer_profile_fields( \WC_Order $order ) {
		$this->set_profile_id( $order );
		$this->set_profile_token( $order );
		
		wc_paysafe_add_debug_log( 'Profile ID after initial request setup: ' . $this->get_profile_id() );
		wc_paysafe_add_debug_log( 'Profile Token after initial request setup: ' . $this->get_profile_token() );
		
		// We can find a profile ID to use for this order
		if ( $this->user_has_profile() ) {
			$profile['id'] = $this->get_profile_id();
			
			if ( '' != $this->get_profile_token() ) {
				$profile['paymentToken'] = $this->get_profile_token();
			} else {
				$profile['firstName'] = WC_Compatibility::get_order_billing_first_name( $order );
				$profile['lastName']  = WC_Compatibility::get_order_billing_last_name( $order );
			}
		} else {
			// No Profile ID, so lets create one for the user/guest
			$id = $this->get_merchant_customer_id( $order->get_user_id() );
			
			$profile = array(
				'merchantCustomerId' => $id,
				'firstName'          => WC_Compatibility::get_order_billing_first_name( $order ),
				'lastName'           => WC_Compatibility::get_order_billing_last_name( $order ),
			);
		}
		
		return $profile;
	}
	
	/**
	 * Returns the payment request billing fields
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public function get_billing_fields( \WC_Order $order ) {
		$billing = array(
			'city'    => WC_Compatibility::get_order_billing_city( $order ),
			'country' => WC_Compatibility::get_order_billing_country( $order ),
			'street'  => WC_Compatibility::get_order_billing_address_1( $order ),
			'street2' => WC_Compatibility::get_order_billing_address_2( $order ),
			'zip'     => WC_Compatibility::get_order_billing_postcode( $order ),
			'state'   => '' == WC_Compatibility::get_order_billing_state( $order ) ? WC_Compatibility::get_order_billing_city( $order ) : WC_Compatibility::get_order_billing_state( $order ),
			'phone'   => WC_Compatibility::get_order_billing_phone( $order ),
		);
		
		// Remove empty elements
		$billing = array_filter( $billing );
		
		return $billing;
	}
	
	/**
	 * Returns the payment request callback fields
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_callback_fields() {
		
		$api_url = WC()->api_request_url( 'wc_gateway_paysafe_response' );
		
		return array(
			array(
				'format'      => 'get',
				'rel'         => 'on_success',
				'retries'     => 3,
				'synchronous' => $this->is_synchro(),
				'uri'         => $api_url,
				'returnKeys'  => array(
					'id',
				),
			),
			array(
				'format'      => 'get',
				'rel'         => 'on_pending',
				'retries'     => 3,
				'synchronous' => $this->is_synchro(),
				'uri'         => $api_url,
				'returnKeys'  => array(
					'id',
				),
			),
			array(
				'format'      => 'get',
				'rel'         => 'on_hold',
				'retries'     => 3,
				'synchronous' => $this->is_synchro(),
				'uri'         => $api_url,
				'returnKeys'  => array(
					'id',
				),
			),
			array(
				'format'      => 'get',
				'rel'         => 'on_decline',
				'retries'     => 3,
				'synchronous' => $this->is_synchro(),
				'uri'         => $api_url,
				'returnKeys'  => array(
					'id',
				),
			),
		);
	}
	
	/**
	 * Returns the payment request callback fields
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_redirect_fields() {
		
		$url = WC()->api_request_url( 'wc_gateway_paysafe_response' );
		
		return array(
			array(
				'rel'        => 'on_success',
				'uri'        => add_query_arg( 'paysafe-redirect', 'success', $url ),
				'returnKeys' => array(
					'id',
				),
			),
			array(
				'rel'        => 'on_error',
				'uri'        => add_query_arg( 'paysafe-redirect', 'error', $url ),
				'returnKeys' => array(
					'id',
				),
			),
			array(
				'rel'        => 'on_hold',
				'uri'        => add_query_arg( 'paysafe-redirect', 'hold', $url ),
				'returnKeys' => array(
					'id',
				),
			),
			array(
				'rel'        => 'on_timeout',
				'uri'        => add_query_arg( 'paysafe-redirect', 'timeout', $url ),
				'returnKeys' => array(
					'id',
				),
			),
			array(
				'rel'        => 'on_decline',
				'uri'        => add_query_arg( 'paysafe-redirect', 'decline', $url ),
				'returnKeys' => array(
					'id',
				),
			),
		);
	}
	
	/**
	 * Returns the payment request addendum data fields
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public function get_addendum_data_fields( \WC_Order $order ) {
		return array(
			array(
				'key'   => 'order_id',
				'value' => WC_Compatibility::get_order_id( $order ),
			),
		);
	}
	
	/**
	 * Returns the payment request link fields.
	 * Link fields are used for cancel button url and location to return the customer to.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_link_fields( \WC_Order $order ) {
		$return = $this->get_gateway()->get_return_url( $order );
		$cancel = $order->get_cancel_order_url_raw();
		
		if ( 'yes' == $this->get_gateway()->get_option( 'use_iframe', 'no' ) ) {
			$return = add_query_arg( 'paysafe-hosted-return', WC_Compatibility::get_order_id( $order ), $return );
			$cancel = add_query_arg( 'paysafe-hosted-return-cancel', WC_Compatibility::get_order_id( $order ), $cancel );
		}
		
		return array(
			array(
				'rel'        => 'return_url',
				'uri'        => $return,
				'returnKeys' => array(
					'transaction.status',
				),
			),
			array(
				'rel' => 'cancel_url',
				'uri' => $cancel,
			),
		);
	}
	
	/**
	 * Returns the payment request shipping fields
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public function get_shipping_fields( \WC_Order $order ) {
		$shipping = array(
			'recipientName' => WC_Compatibility::get_order_shipping_first_name( $order ) . ' ' . WC_Compatibility::get_order_shipping_last_name( $order ),
			'city'          => WC_Compatibility::get_order_shipping_city( $order ),
			'country'       => WC_Compatibility::get_order_shipping_country( $order ),
			'street'        => WC_Compatibility::get_order_shipping_address_1( $order ),
			'street2'       => WC_Compatibility::get_order_shipping_address_2( $order ),
			'zip'           => WC_Compatibility::get_order_shipping_postcode( $order ),
			'state'         => '' == WC_Compatibility::get_order_shipping_state( $order ) ? WC_Compatibility::get_order_shipping_city( $order ) : WC_Compatibility::get_order_shipping_state( $order ),
		);
		
		// Remove empty elements
		$shipping = array_filter( $shipping );
		
		return $shipping;
	}
	
	/**
	 * Returns the payment request shopping cart details fields.
	 * All fields, total amounts + ancillary fees, should match the total amount of payment sent.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 * @param string    $price_tax_calculation
	 *
	 * @return mixed
	 */
	public function get_shopping_cart_fields( \WC_Order $order, $price_tax_calculation = 'including' ) {
		$shopping_cart = array();
		if ( 'including' == $price_tax_calculation ) {
			$desc = '';
			if ( 0 < count( $order->get_items() ) ) {
				foreach ( $order->get_items() as $item ) {
					if ( WC_Compatibility::get_item_quantity( $item ) ) {
						$item_meta = WC_Compatibility::wc_display_item_meta( $item );
						
						$item_name = WC_Compatibility::get_item_name( $item );
						if ( $item_meta ) {
							$item_name .= ' (' . $item_meta . ')';
						}
						
						$desc .= $item['qty'] . ' x ' . $item_name . ', ';
					}
				}
				// Add the description
				$desc = substr( $desc, 0, - 2 );
				$desc = $this->format_string( $desc, 50 );
			}
			
			$shopping_cart[] = array(
				'amount'      => $this->format_amount( $order->get_total() ),
				'description' => $desc,
				'quantity'    => 1,
			);
		} else {
			// Cart Contents
			if ( 0 < count( $order->get_items() ) ) {
				foreach ( $order->get_items() as $item ) {
					if ( WC_Compatibility::get_item_quantity( $item ) ) {
						$product   = WC_Compatibility::get_product_from_item( $item, $order );
						$item_meta = WC_Compatibility::wc_display_item_meta( $item );
						
						$item_name = WC_Compatibility::get_item_name( $item );
						if ( $item_meta ) {
							$item_name .= ' (' . $item_meta . ')';
						}
						
						// Cart items
						$shopping_cart[] = array(
							'amount'      => $this->format_amount( $order->get_item_subtotal( $item, false ) ),
							'description' => $this->format_string( $item_name, 50 ),
							'quantity'    => WC_Compatibility::get_item_quantity( $item ),
							'sku'         => $this->format_string( ( $product->get_sku() ) ? $product->get_sku() : WC_Compatibility::get_prop( $product, 'id' ), 60 ),
						);
					}
				}
			}
		}
		
		return $shopping_cart;
	}
	
	/**
	 * Returns the payment request ancillary fees fields.
	 * Fees are Tax, Discount, Shipping amount.
	 * All fields, ancillary fees + total amount of shopping cart, should match the total amount of payment sent.
	 *
	 * @since 2.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public function get_ancillary_fees_fields( \WC_Order $order ) {
		$fees = array();
		
		// Add the order tax
		if ( 0 < $order->get_total_tax() ) {
			$fees[] = array(
				'amount'      => $this->format_amount( $order->get_total_tax() ),
				'description' => $this->format_string( __( 'Tax', 'wc_paysafe' ), 50 ),
			);
		}
		
		// Add the order discount
		if ( 0 < $order->get_total_discount() ) {
			$fees[] = array(
				'amount'      => - $this->format_amount( $order->get_total_discount() ),
				'description' => $this->format_string( __( 'Discount', 'wc_paysafe' ), 50 ),
			);
		}
		
		// Add the shipping parameter
		if ( 0 < WC_Compatibility::get_total_shipping( $order ) ) {
			$fees[] = array(
				'amount'      => $this->format_amount( WC_Compatibility::get_total_shipping( $order ) ),
				'description' => $this->format_string( __( 'Shipping', 'wc_paysafe' ), 50 ),
			);
		}
		
		$item_fees = $order->get_items( 'fee' );
		if ( 0 < count( $item_fees ) ) {
			foreach ( $item_fees as $fee ) {
				$fees[] = array(
					'amount'      => $this->format_amount( $order->get_item_subtotal( $fee, false ) ),
					'description' => $this->format_string( WC_Compatibility::get_item_name( $fee ), 50 ),
				);
			}
		}
		
		return $fees;
	}
	
	/**
	 * Returns the payment request profile -> merchantCustomerId.
	 *
	 * @since 2.0
	 *
	 * @param int $user_id
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function get_merchant_customer_id( $user_id ) {
		if ( ! empty( $user_id ) ) {
			$suffix_value = $this->get_merchant_profile_id_suffix( $user_id );
			
			// Increment the ID from the start.
			$increment_suffix = (int) $suffix_value + 1;
			$this->update_merchant_profile_id_suffix( $user_id, $increment_suffix );
			
			$suffix = '-' . $increment_suffix;
			$id     = $this->get_gateway()->get_option( 'user_prefix' ) . $user_id . $suffix;
		} else {
			$id = uniqid( $this->get_gateway()->get_option( 'user_prefix' ), true );
		}
		
		return $id;
	}
	
	/**
	 * Returns the merchant customer id suffix
	 *
	 * @since 2.0
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function get_merchant_profile_id_suffix( $user_id ) {
		if ( 0 === (int) $user_id ) {
			return false;
		}
		
		return get_user_meta( $user_id, '_netbanx_hosted_merchant_customer_id_suffix', true );
	}
	
	/**
	 * Updates the merchant customer id suffix
	 *
	 * @since 2.0
	 *
	 * @param int   $user_id
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function update_merchant_profile_id_suffix( $user_id, $value ) {
		if ( 0 === (int) $user_id ) {
			return false;
		}
		
		return update_user_meta( $user_id, '_netbanx_hosted_merchant_customer_id_suffix', $value );
	}
}