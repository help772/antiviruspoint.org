<?php

namespace WcPaysafe\Api_Payments\Payments\Parameters;

use WcPaysafe\Api_Payments\Config\Redirect;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Parameters_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Card_Fields;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Authorizations extends Parameters_Abstract {
	
	/**
	 * Verification parameters
	 *
	 * @param string|array $data_to_verify The data we want to verify. It can be a token string, an array of Card data or DD data.<br>
	 *                                     <b>'card'</b>(array) requires fields: [card], [cvv], [cardExpiry][month] and [cardExpiry][year]<br>
	 *                                     <b>'token'</b>(string) is a string and the token value should be passed
	 * @param string       $data_type      The Type of the data we are verifying. It can be, "card", "token", "dd"
	 *
	 * @return array|mixed
	 */
	public function get_verification_parameters( $data_to_verify, $data_type = 'card' ) {
		if ( ! in_array( $data_type, array( 'card', 'token' ) ) ) {
			$data_type = 'card';
		}
		
		/**
		 * @var \WC_Order|\WP_User $source
		 * @var Card_Fields        $fields
		 * @var Redirect           $configuration
		 */
		$fields        = $this->get_fields();
		$configuration = $this->get_configuration();
		$source        = $fields->get_source()->get_source();
		
		$params = array(
			'merchantRefNum' => uniqid( 'verify-' ),
			'profile'        => $fields->get_profile_fields(),
			'billingDetails' => $fields->get_billing_fields(),
			'description'    => __( 'Verification of a transaction', 'wc_paysafe' ),
		);
		
		if ( $configuration->send_customer_ip() ) {
			$params['customerIp'] = $configuration->get_user_ip_addr();
		}
		
		$params = apply_filters( 'wc_paysafe_verification_parameters', $params, $source, $data_to_verify, $data_type );
		
		wc_paysafe_payments_add_debug_log( 'Card verification parameters: ' . print_r( $params, true ) );
		
		if ( 'card' == $data_type ) {
			$params['card']['cardNum'] = $data_to_verify['card'];
			if ( isset( $data_to_verify['cvv'] ) ) {
				$params['card']['cvv'] = $data_to_verify['cvv'];
			}
			$params['card']['cardExpiry']['month'] = $data_to_verify['expiry_month'];
			$params['card']['cardExpiry']['year']  = $data_to_verify['expiry_year'];
		} else {
			$params['card']['paymentToken'] = $data_to_verify;
		}
		
		return $params;
	}
	
	/**
	 * Generate transaction parameters for transaction using a token
	 *
	 * @since 4.0.0
	 *
	 * @param string $token
	 * @param float  $amount
	 *
	 * @return array
	 */
	public function get_token_transaction_parameters( $token, $amount = null, $force_auth_only = false ) {
		/**
		 * @var Order_Source $source
		 * @var \WC_Order    $order
		 * @var Card_Fields  $fields
		 * @var Redirect     $configuration
		 */
		$fields        = $this->get_fields();
		$configuration = $this->get_configuration();
		$source        = $fields->get_source();
		$order         = $source->get_source();
		
		$paysafe_order = new Paysafe_Order( $order );
		
		// If no amount is provided, charge the order total
		if ( null === $amount ) {
			$amount = $order->get_total();
		}
		
		if ( $force_auth_only ) {
			$amount = 1.00;
		}
		
		$params = array(
			'merchantRefNum'     => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ). '_' . uniqid( 'payment-' ) ,
			'settleWithAuth'     => false === $force_auth_only ? false : 'sale' == $configuration->get_authorization_type(),
			'amount'             => Formatting::format_amount( $amount, $order->get_currency() ),
			'dupCheck'           => true,
			'paymentHandleToken' => $token,
			'profile'            => $fields->get_profile_fields(),
			'description'        => $fields->get_description(),
			'storedCredential'   => array(
				'type'       => 'ADHOC',
				'occurrence' => 'INITIAL',
			),
			'currencyCode'       => $order->get_currency(),
		);
		
		if ( $fields->get_customer_unique_merchant_customer_id() ) {
			$params['merchantCustomerId'] = $fields->get_customer_unique_merchant_customer_id();
		}
		
		/**
		 * storedCredential cases:
		 * 1. Subscription initial payment
		 *      type=RECURRING
		 *      occurrence=INITIAL
		 * 2. Subscription scheduled payment
		 *      type=RECURRING
		 *      occurrence=SUBSEQUENT
		 * 3. General Payment:
		 * - Using singleUseToken:
		 *      type=ADHOC,
		 *      occurrence=INITIAL
		 * - Using stored WC Token:
		 *      type=ADHOC,
		 *      occurrence=SUBSEQUENT
		 */
		
		// If we are doing a merchant initiated recurring payment,
		// we need to set the storedCredentials to recurring and subsequent
		if ( $paysafe_order->contains_subscription() ) {
			$params['storedCredential']['type']       = 'RECURRING';
			$params['storedCredential']['occurrence'] = 'INITIAL';
			
			// Not an initial payment / Merchant initiated
			if ( ! $source->get_is_initial_payment() ) {
				$params['storedCredential']['occurrence']           = 'SUBSEQUENT';
				$params['storedCredential']['initialTransactionId'] = $source->get_initial_transaction_id();
			}
		} elseif ( $source->get_using_saved_token() ) {
			$params['storedCredential']['occurrence'] = 'SUBSEQUENT';
		}
		
		// Customer initiated with a saved card, always ask for the cvv, so we can pass 3DS2
		if ( $configuration->get_gateway()->is_cvv_required() && $source->get_is_initial_payment() && $source->get_using_saved_token() ) {
			if ( '' === $source->get_cvv() ) {
				throw new \Exception( __( 'The card CVV number is required.' ) );
			}
			
			$params['card']['cvv'] = $source->get_cvv();
		}
		
		// No capture if total is 0
		if ( 0 == $amount ) {
			$params['settleWithAuth'] = false;
			$params['amount']         = 1;
		}
		
		if ( $configuration->send_customer_ip() ) {
			$params['customerIp'] = $configuration->get_user_ip_addr();
		}
		
		$params = apply_filters( 'wc_paysafe_payments_token_transaction_parameters', $params, $order, $token, 'cards', $force_auth_only );
		
		return $params;
	}
	
	/**
	 * The get Authorization parameters
	 *
	 * @param $transaction_id
	 *
	 * @return array
	 */
	public function get_auth_parameters( $transaction_id ) {
		return array( 'id' => $transaction_id );
	}
}