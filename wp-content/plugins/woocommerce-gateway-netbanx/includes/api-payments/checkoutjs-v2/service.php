<?php

namespace WcPaysafe\Api_Payments\Checkoutjs_V2;

use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Abstract;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Api_Payments\Data_Sources\User_Source;
use WcPaysafe\Api_Payments\Request_Fields\Checkoutjs_Fields;
use WcPaysafe\Api_Payments\Service_Abstract;
use WcPaysafe\Api_Payments\Service_Interface;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Gateways\Redirect\Payments\Processes;
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
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Service extends Service_Abstract implements Service_Interface {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Abstract $source
	 *
	 * @return Checkoutjs_Fields
	 */
	public function get_fields( $source ) {
		return new Checkoutjs_Fields( $source );
	}
	
	/**
	 * Returns parameters to load the payment iframe
	 *
	 * @since 3.3.0
	 *
	 * @param \WC_Order $order
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function get_iframe_order_parameters( $order ) {
		/**
		 * @var Checkoutjs_Fields $fields
		 */
		$fields = $this->get_fields( new Order_Source( $order ) );
		$config = $this->get_configuration();
		
		$amount = Formatting::format_amount( $order->get_total(), $order->get_currency() );
		if ( wc_paysafe_is_change_method_page() || 0 == $amount ) {
			$amount = Formatting::format_amount( 1.00, $order->get_currency() );
		}
		
		$paysafe_order = new Paysafe_Order( $order );
		
		$parameters = array(
			'options' => array(
				'holderName'     => $fields->get_billing_first_name() . ' ' . $fields->get_billing_last_name(),
				'environment'    => $config->is_testmode() ? 'TEST' : 'LIVE',
				'orderId'        => WC_Compatibility::get_order_id( $order ),
				'orderNumber'    => $order->get_order_number(),
				'amount'         => $amount,
				'currency'       => WC_Compatibility::get_order_currency( $order ),
				'imageUrl'       => \WC_HTTPS::force_https_url( $config->get_layover_image_url() ),
				'companyName'    => $config->get_company_name(),
				'locale'         => $config->get_locale(),
				'merchantRefNum' => $paysafe_order->get_order_number() . '_' . $paysafe_order->get_attempts_suffix( 'order' ),
				'canEditAmount'  => false,
				'payout'         => false,
			),
			'urls'    => array(
				'successRedirectPage' => esc_url_raw( $order->get_checkout_order_received_url() ),
			),
		);
		
		$methods = $config->get_available_payment_methods();
		
		$parameters['options']['displayPaymentMethods'] = $methods;
		foreach ( $methods as $method ) {
			$parameters['options']['paymentMethodDetails'][ $method ] = [
				'accountId' => $config->get_account_id( $fields->get_source(), $method ),
			];
		}
		
		// Add some applePay config
		if ( isset( $parameters['options']['paymentMethodDetails']['applePay'] ) ) {
			$apple_pay_config = apply_filters( 'wc_paysafe_payments_apple_pay_config', [
				'label'                  => __( "Pay With Apple", 'wc_paysafe' ),
				'requestShippingAddress' => false,
				'requestBillingAddress'  => false,
				'type'                   => "buy",
				'color'                  => "white-outline",
			], $order );
			
			$parameters['options']['paymentMethodDetails']['applePay'] = array_merge( $apple_pay_config, $parameters['options']['paymentMethodDetails']['applePay'] );
		}
		
		// Change payment method will accept 'card' only for now
		if ( wc_paysafe_is_change_method_page() || $paysafe_order->contains_subscription() ) {
			unset( $parameters['options']['applePay'] );
		}
		
		if ( $config->is_3d2_enabled() ) {
			$parameters['options']['threeDs']['merchantUrl']   = home_url( '/' );
			$parameters['options']['threeDs']['deviceChannel'] = 'BROWSER';
			
			// Needs to be set to "NON_PAYMENT" if we are not going to take immediate payment
			$parameters['options']['threeDs']['messageCategory'] = 'PAYMENT';
			
			// Needs to be set additionally for add/update/change method verifications
			$parameters['options']['threeDs']['transactionIntent'] = 'GOODS_OR_SERVICE_PURCHASE';
			
			// Set this to "RECURRING_TRANSACTION" if we are going to use it for subscriptions
			$parameters['options']['threeDs']['authenticationPurpose'] = 'PAYMENT_TRANSACTION';
			if ( $paysafe_order->contains_subscription() ) {
				$parameters['options']['threeDs']['authenticationPurpose'] = 'RECURRING_TRANSACTION';
				$parameters['options']['threeDs']['billingCycle']          = [
					'endDate'   => '2099-09-25',
					'frequency' => 1,
				];
			}
			
			$parameters['options']['threeDs']['useThreeDSecureVersion2'] = true;
			
			$parameters['options']['threeDs']['requestorChallengePreference'] = $config->get_option( 'threeds2_challenge_preference', 'NO_PREFERENCE' );
			
			$parameters['options']['threeDs']['profile'] = array(
				'email'     => $fields->get_billing_email(),
				'phone'     => $fields->get_billing_phone(),
				'cellphone' => $fields->get_billing_phone(),
			);
			
			if ( 0 < $order->get_user_id() ) {
				$user = get_user_by( 'id', $order->get_user_id() );
				if ( $user ) {
					$paysafe_customer = new Paysafe_Customer( $user );
					
					// Is the customer ordering this item for a first time? "REORDER" or "FIRST_TIME_ORDER"
					$parameters['options']['threeDs']['reorderItemsIndicator'] = $paysafe_customer->did_customer_ordered_before( $order );
					
					$parameters['options']['threeDs']['userAccountDetails'] = array(
						'createdDate'                 => $paysafe_customer->get_date_created()->format( 'Y-m-d' ),
						'changedDate'                 => $paysafe_customer->get_last_updated()->format( 'Y-m-d' ),
						'totalPurchasesSixMonthCount' => $paysafe_customer->get_successful_orders_last_six_months(),
						'shippingDetailsUsage'        => array(
							'initialUsageDate' => $paysafe_customer->get_shipping_address_first_use( $order )->format( 'Y-m-d' ),
						),
					);
				}
			}
		}
		
		if ( 0 < $order->get_user_id() ) {
			// The customer object should get the customer_id
			$paysafe_customer = new Paysafe_Customer( $order->get_user() );
			$api_customer     = false;
			
			try {
				$processes         = new Processes( $config->get_gateway() );
				$api_client        = $processes->get_api_client( $order, 'card' );
				$customers_service = $api_client->get_customers_service();
				
				// 1. Check with the Payments API if the user has an account on record
				$customers_request = $customers_service->customers_request();
				
				// If the customer has a customer ID, try to get the account
				if ( $paysafe_customer->get_payments_merchant_customer_id() ) {
					$api_customer = $customers_request->get_customer_by_merchant_customer_id( $paysafe_customer->get_payments_merchant_customer_id() );
				}
			}
			catch ( \Exception $e ) {
				// Nothing to do, it would mean that we did not get an account
			}
			
			try {
				$single_use_token_response = false;
				
				// 2. If an account is present, make a single-use token for the account
				if ( $api_customer && 'ACTIVE' == $api_customer->get_status() ) {
					$single_use_token_request = $customers_service->single_use_tokens_request();
					$customer_id              = $paysafe_customer->get_payments_customer_id();
					
					$single_use_token_response = $single_use_token_request->create( $customer_id,
						$single_use_token_request->get_request_builder( new Order_Source( $order ) )->get_single_use_token_parameters()
					);
				}
			}
			catch ( \Exception $e ) {
				// Nothing to do, it would mean that we did not get a single-use token
			}
			
			// 3. Add the single-use token to the request
			if ( $single_use_token_response && $single_use_token_response->get_id() ) {
				$parameters['options']['singleUseCustomerToken'] = $single_use_token_response->get_single_use_token();
			} else {
				// 4. If account is not present, add a customer profile to the request
				$parameters['options']['customer'] = $fields->get_profile_fields();
			}
		}
		
		if ( true === apply_filters( 'wwc_paysafe_payments_checkoutjs_add_billing_address', true, $order, $this ) ) {
			$parameters['options']['billingAddress'] = $fields->get_billing_fields();
		}
		
		// Filter the fields, so 3rd party can modify
		$parameters = apply_filters( 'wc_paysafe_payments_checkoutjs_order_iframe_props', $parameters, $order, $this );
		
		// Remove keys with empty values
		$parameters = Formatting::array_filter_recursive( $parameters );
		
		return $parameters;
	}
}