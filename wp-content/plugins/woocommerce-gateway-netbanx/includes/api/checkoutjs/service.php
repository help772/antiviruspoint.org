<?php

namespace WcPaysafe\Api\Checkoutjs;

use WcPaysafe\Api\Data_Sources\Data_Source_Abstract;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Request_Fields\Checkoutjs_Fields;
use WcPaysafe\Api\Service_Abstract;
use WcPaysafe\Api\Service_Interface;
use WcPaysafe\Compatibility\WC_Compatibility;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Customer;
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
	 * @param \WP_User $user
	 *
	 * @throws \Exception
	 * @return array
	 */
	public function get_iframe_user_parameters( $user ) {
		/**
		 * @var Checkoutjs_Fields $fields
		 */
		$fields = $this->get_fields( new User_Source( $user ) );
		$config = $this->get_configuration();
		
		$parameters = array(
			'options' => array(
				'holderName'             => $fields->get_billing_first_name() . ' ' . $fields->get_billing_last_name(),
				'environment'            => $config->is_testmode() ? 'TEST' : 'LIVE',
				'amount'                 => 1,
				'userId'                 => $user->ID,
				'currency'               => get_woocommerce_currency(),
				'imageUrl'               => \WC_HTTPS::force_https_url( $config->get_layover_image_url() ),
				'companyName'            => $config->get_company_name(),
				'locale'                 => $config->get_locale(),
				'buttonColor'            => $config->get_layover_button_color(),
				'preferredPaymentMethod' => $config->get_layover_preferred_payment_method(),
			),
		);
		
		// Add 3D secure account if available
		$three_d_secure_account = $config->get_account_id( $fields->get_source(), 'iframe' );
		if ( '' != $three_d_secure_account ) {
			// Make sure the CC account is an int
			$parameters['options']['accounts']['CC'] = (int) $three_d_secure_account;
			
			if ( $config->is_3d2_enabled() ) {
				$parameters['options']['threeDS']['useThreeDSecureVersion2'] = true;
				
				// Needs to be set to "NON_PAYMENT" if we are not going to take immediate payment
				$parameters['options']['threeDS']['messageCategory'] = 'PAYMENT';
				
				// Needs to be set additionally for add/update/change method verifications
				$parameters['options']['threeDS']['transactionIntent']            = 'GOODS_OR_SERVICE_PURCHASE';
				$parameters['options']['threeDS']['requestorChallengePreference'] = $config->get_option( 'threeds2_challenge_preference', 'NO_PREFERENCE' );
				
				$parameters['options']['threeDS']['profile'] = array(
					'email'     => $fields->get_billing_email(),
					'phone'     => $fields->get_billing_phone(),
					'cellphone' => $fields->get_billing_phone(),
				);
				
				$paysafe_customer = new Paysafe_Customer( $user );
				
				$parameters['options']['threeDS']['userAccountDetails'] = array(
					'createdDate'                 => $paysafe_customer->get_date_created()->format( 'Y-m-d' ),
					'changedDate'                 => $paysafe_customer->get_last_updated()->format( 'Y-m-d' ),
					'totalPurchasesSixMonthCount' => $paysafe_customer->get_successful_orders_last_six_months(),
				);
			}
		}
		
		if ( true == apply_filters( 'wc_paysafe_checkoutjs_add_billing_address', true, null, $this ) ) {
			$parameters['options']['billingAddress'] = $fields->get_billing_fields();
		}
		
		// Make the return URL the payment methods if we are on the update-token page
		if ( wc_paysafe_is_update_payment_method_page() || is_add_payment_method_page() ) {
			$parameters['urls'] = array(
				'successRedirectPage' => wc_get_account_endpoint_url( 'payment-methods' ),
			);
		}
		
		// Filter the fields, so 3rd party can modify
		$parameters = apply_filters( 'wc_paysafe_checkoutjs_user_iframe_props', $parameters, $user, $this );
		
		// Remove keys with empty values
		$parameters = Formatting::array_filter_recursive( $parameters );
		
		return $parameters;
	}
	
	/**
	 * Returns parameters to load the payment iframe
	 *
	 * TODO: Format fields to their character limit
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
			$amount = 1;
		}
		
		$parameters = array(
			'options' => array(
				'holderName'             => $fields->get_billing_first_name() . ' ' . $fields->get_billing_last_name(),
				'environment'            => $config->is_testmode() ? 'TEST' : 'LIVE',
				'orderId'                => WC_Compatibility::get_order_id( $order ),
				'orderNumber'            => $order->get_order_number(),
				'amount'                 => $amount,
				'currency'               => WC_Compatibility::get_order_currency( $order ),
				'imageUrl'               => \WC_HTTPS::force_https_url( $config->get_layover_image_url() ),
				'companyName'            => $config->get_company_name(),
				'locale'                 => $config->get_locale(),
				'buttonColor'            => $config->get_layover_button_color(),
				'preferredPaymentMethod' => $config->get_layover_preferred_payment_method(),
				'showSaveCardCheckboxes' => $config->show_save_cards_checkbox( $fields->get_source() ),
			),
			'urls'    => array(
				'successRedirectPage' => esc_url_raw( $order->get_checkout_order_received_url() ),
			),
		);
		
		$paysafe_order = new Paysafe_Order( $order );
		
		// Add 3D secure account if available
		$three_d_secure_account = $config->get_account_id( $fields->get_source(), 'iframe' );
		if ( '' != $three_d_secure_account ) {
			// Make sure the CC account is an int
			$parameters['options']['accounts']['CC'] = (int) $three_d_secure_account;
			
			if ( $config->is_3d2_enabled() ) {
				$parameters['options']['threeDS']['useThreeDSecureVersion2'] = true;
				
				// Set this to "RECURRING_TRANSACTION" if we are going to use it for subscriptions
				$parameters['options']['threeDS']['authenticationPurpose'] = 'PAYMENT_TRANSACTION';
				if ( $paysafe_order->contains_subscription() ) {
					$parameters['options']['threeDS']['authenticationPurpose'] = 'RECURRING_TRANSACTION';
					$parameters['options']['threeDS']['billingCycle']          = [
						'endDate'   => '2099-09-25',
						'frequency' => 1,
					];
				}
				
				// Needs to be set to "NON_PAYMENT" if we are not going to take immediate payment
				$parameters['options']['threeDS']['messageCategory'] = 'PAYMENT';
				
				// Needs to be set additionally for add/update/change method verifications
				$parameters['options']['threeDS']['transactionIntent']            = 'GOODS_OR_SERVICE_PURCHASE';
				$parameters['options']['threeDS']['requestorChallengePreference'] = $config->get_option( 'threeds2_challenge_preference', 'NO_PREFERENCE' );
				
				$parameters['options']['threeDS']['profile'] = array(
					'email'     => $fields->get_billing_email(),
					'phone'     => $fields->get_billing_phone(),
					'cellphone' => $fields->get_billing_phone(),
				);
				
				if ( 0 < $order->get_user_id() ) {
					$user = get_user_by( 'id', $order->get_user_id() );
					if ( $user ) {
						$paysafe_customer = new Paysafe_Customer( $user );
						
						// Is the customer ordering this item for a first time? "REORDER" or "FIRST_TIME_ORDER"
						$parameters['options']['threeDS']['reorderItemsIndicator'] = $paysafe_customer->did_customer_ordered_before( $order );
						
						$parameters['options']['threeDS']['userAccountDetails'] = array(
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
		}
		
		if ( true == apply_filters( 'wc_paysafe_checkoutjs_add_billing_address', true, $order, $this ) ) {
			$parameters['options']['billingAddress'] = $fields->get_billing_fields();
		}
		
		// Filter the fields, so 3rd party can modify
		$parameters = apply_filters( 'wc_paysafe_checkoutjs_order_iframe_props', $parameters, $order, $this );
		
		// Remove keys with empty values
		$parameters = Formatting::array_filter_recursive( $parameters );
		
		return $parameters;
	}
}