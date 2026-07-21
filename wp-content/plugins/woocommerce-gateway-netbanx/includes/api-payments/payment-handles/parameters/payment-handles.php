<?php

namespace WcPaysafe\Api_Payments\Payment_Handles\Parameters;

use WcPaysafe\Api_Payments\Config\Redirect;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Parameters_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Card_Fields;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Customer;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Payment_Handles extends Parameters_Abstract {
	
	public function google_payment_parameters( $google_token, $amount = null ) {
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
		
		$params = array(
			'accountId'       => $configuration->get_gateway()->get_account_id( $source, 'card' ),
			'transactionType' => 'PAYMENT',
			'paymentType'     => 'CARD',
			'merchantRefNum'  => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ),
			'amount'          => Formatting::format_amount( $amount, $order->get_currency() ),
			'profile'         => $fields->get_profile_fields(),
			'billingDetails'  => $fields->get_billing_fields(),
			'description'     => $fields->get_description(),
			'currencyCode'    => $order->get_currency(),
			'returnLinks'     => [
				[
					'rel'    => 'default',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_return_default' ) ),
					'method' => 'GET',
				],
				[
					'rel'    => 'on_completed',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_return_completed' ) ),
					'method' => 'GET',
				],
				[
					'rel'    => 'on_cancelled',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_return_cancelled' ) ),
					'method' => 'GET',
				],
				[
					'rel'    => 'on_failed',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_return_failed' ) ),
					'method' => 'GET',
				],
			],
		);
		
		$google_token['paymentMethodData']['tokenizationData']['token'] = wp_unslash( $google_token['paymentMethodData']['tokenizationData']['token'] );
		
		if ( isset( $google_token['paymentMethodData'] ) && isset( $google_token['paymentMethodData']['tokenizationData'] ) ) {
			$params['googlePay']['googlePayPaymentToken'] = $google_token;
		}
		
		if ( $configuration->is_3d2_enabled() ) {
			$params['threeDs']['merchantUrl']   = home_url( '/' );
			$params['threeDs']['deviceChannel'] = 'BROWSER';
			
			// Needs to be set to "NON_PAYMENT" if we are not going to take immediate payment
			$params['threeDs']['messageCategory'] = 'PAYMENT';
			
			// Needs to be set additionally for add/update/change method verifications
			$params['threeDs']['transactionIntent'] = 'GOODS_OR_SERVICE_PURCHASE';
			
			// Set this to "RECURRING_TRANSACTION" if we are going to use it for subscriptions
			$params['threeDs']['authenticationPurpose'] = 'PAYMENT_TRANSACTION';
			if ( $paysafe_order->contains_subscription() ) {
				$params['threeDs']['authenticationPurpose'] = 'RECURRING_TRANSACTION';
				$params['threeDs']['billingCycle']          = [
					'endDate'   => '2099-09-25',
					'frequency' => 1,
				];
			}
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
		
		// No capture if total is 0
		if ( 0 == $amount ) {
			$params['amount'] = 1;
		}
		
		if ( $configuration->send_customer_ip() ) {
			$params['customerIp'] = $configuration->get_user_ip_addr();
		}
		
		$params = apply_filters( 'wc_paysafe_google_payment_parameters', $params, $order, $google_token );
		
		return $params;
	}
	
	public function single_use_handle_from_parameters( $handle_from, $amount = null, $payment_type = 'CARD' ) {
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
		
		$payment_type = strtoupper( $payment_type );
		if ( ! in_array( $payment_type, $this->allowed_payment_types() ) ) {
			$payment_type = 'CARD';
		}
		
		// If no amount is provided, charge the order total
		if ( null === $amount ) {
			$amount = $order->get_total();
		}
		
		$params = array(
			'accountId'              => $configuration->get_gateway()->get_account_id( $source, $payment_type ),
			'transactionType'        => 'PAYMENT',
			'paymentType'            => $payment_type,
			'merchantRefNum'         => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ),
			'amount'                 => Formatting::format_amount( $amount, $order->get_currency() ),
			'profile'                => $fields->get_profile_fields(),
			'billingDetails'         => $fields->get_billing_fields(),
			'description'            => $fields->get_description(),
			'currencyCode'           => $order->get_currency(),
			'paymentHandleTokenFrom' => $handle_from,
			'returnLinks'            => [
				[
					'rel'    => 'default',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_single_use_handle_return_default' ) ),
					'method' => 'GET',
				],
				[
					'rel'    => 'on_completed',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_single_use_handle_return_completed' ) ),
					'method' => 'GET',
				],
				[
					'rel'    => 'on_cancelled',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_single_use_handle_return_cancelled' ) ),
					'method' => 'GET',
				],
				[
					'rel'    => 'on_failed',
					'href'   => add_query_arg( [ 'paysafe_order' => $order->get_id() ], WC()->api_request_url( 'wc_paysafe_single_use_handle_return_failed' ) ),
					'method' => 'GET',
				],
			],
		);
		
		if ( $configuration->get_gateway()->is_cvv_required() && $source->get_is_initial_payment() && $source->get_using_saved_token() ) {
			if ( '' === $source->get_cvv() ) {
				throw new \Exception( __( 'The card CVV number is required.' ) );
			}
			
			$params['card']['cvv'] = $source->get_cvv();
		}
		
		if ( $configuration->is_3d2_enabled() ) {
			$params['options']['threeDs']['merchantUrl']   = home_url( '/' );
			$params['options']['threeDs']['deviceChannel'] = 'BROWSER';
			
			// Needs to be set to "NON_PAYMENT" if we are not going to take immediate payment
			$params['options']['threeDs']['messageCategory'] = 'PAYMENT';
			
			// Needs to be set additionally for add/update/change method verifications
			$params['options']['threeDs']['transactionIntent'] = 'GOODS_OR_SERVICE_PURCHASE';
			
			// Set this to "RECURRING_TRANSACTION" if we are going to use it for subscriptions
			$params['options']['threeDs']['authenticationPurpose']   = 'PAYMENT_TRANSACTION';
			if ( $paysafe_order->contains_subscription() ) {
				$params['options']['threeDs']['authenticationPurpose'] = 'RECURRING_TRANSACTION';
				$params['options']['threeDs']['billingCycle']          = [
					'endDate'   => '2099-09-25',
					'frequency' => 1,
				];
			}
			
			$params['options']['threeDs']['useThreeDSecureVersion2'] = true;
			
			$params['options']['threeDs']['requestorChallengePreference'] = $configuration->get_option( 'threeds2_challenge_preference', 'NO_PREFERENCE' );
			
			$params['options']['threeDs']['profile'] = array(
				'email'     => $fields->get_billing_email(),
				'phone'     => $fields->get_billing_phone(),
				'cellphone' => $fields->get_billing_phone(),
			);
			
			$paysafe_customer = new Paysafe_Customer( $order->get_user() );
			
			$params['options']['threeDS']['userAccountDetails'] = array(
				'createdDate'                 => $paysafe_customer->get_date_created()->format( 'Y-m-d' ),
				'changedDate'                 => $paysafe_customer->get_last_updated()->format( 'Y-m-d' ),
				'totalPurchasesSixMonthCount' => $paysafe_customer->get_successful_orders_last_six_months(),
			);
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
		
		// No capture if total is 0
		if ( 0 == $amount ) {
			$params['amount'] = 1;
		}
		
		if ( $configuration->send_customer_ip() ) {
			$params['customerIp'] = $configuration->get_user_ip_addr();
		}
		
		$params = apply_filters( 'wc_paysafe_google_payment_parameters', $params, $order );
		
		return $params;
	}
}