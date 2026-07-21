<?php

namespace WcPaysafe\Api_Payments\Config;

use WcPaysafe\Api_Payments\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api_Payments\Data_Sources\Order_Source;
use WcPaysafe\Paysafe_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implementation of the Redirect Gateway class
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Redirect extends Configuration_Abstract {
	
	/**
	 * @param null|Data_Source_Interface $data_source
	 * @param string                     $payment_type cards|directdebit|interac|card|applePay
	 *
	 * @return int
	 */
	public function get_account_id( $data_source = null, $payment_type = null ) {
		return $this->get_gateway()->get_account_id( $data_source, $payment_type );
	}
	
	public function get_available_payment_methods() {
		return $this->get_gateway()->get_available_payment_methods();
	}
	
	public function is_3d2_enabled() {
		return 'yes' === $this->get_gateway()->get_option( 'use_layover_3ds2', 'no' );
	}
	
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
		
		return apply_filters( 'wc_paysafe_redirect_user_ip_address', $ip, $this );
	}
	
	public function send_customer_ip() {
		return 'yes' == $this->get_gateway()->get_option( 'send_ip_address', '' );
	}
	
	public function is_testmode() {
		return 'yes' == $this->get_gateway()->get_option( 'testmode', 'yes' );
	}
	
	public function get_authorization_type() {
		return $this->get_gateway()->get_option( 'authorization_type', 'sale' );
	}
	
	public function get_layover_image_url() {
		return $this->get_gateway()->get_option( 'layover_image_url', '' );
	}
	
	public function get_layover_button_color() {
		$value = str_replace( '#', '', $this->get_gateway()->get_option( 'layover_button_color', '' ) );
		
		if ( $value && 0 !== strpos( '#', trim( $value ) ) ) {
			$value = '#' . $value;
		}
		
		return $value;
	}
	
	public function get_layover_preferred_payment_method() {
		return $this->get_gateway()->get_option( 'layover_preferred_payment_method', '' );
	}
	
	/**
	 * @param \WcPaysafe\Api_Payments\Data_Sources\User_Source|\WcPaysafe\Api_Payments\Data_Sources\Order_Source $data_source
	 *
	 * @return bool
	 */
	public function show_save_cards_checkbox( $data_source ) {
		if ( ! $data_source instanceof Order_Source ) {
			return $this->get_gateway()->saved_cards;
		}
		
		/**
		 * We don't support tokenization
		 * ||
		 * Subscription is active and order contains subscription
		 * ||
		 * Pre_order is active and order contains pre-order
		 */
		$paysafe_order        = new Paysafe_Order( $data_source->get_source() );
		$display_tokenization = $this->get_gateway()->supports( 'tokenization' ) // Tokenize
		                        && $this->get_gateway()->saved_cards // Merchant chose to allow token payments
		                        && ( is_checkout() || is_checkout_pay_page() )
		                        && ! wc_paysafe_is_change_method_page(); // We are on Checkout or Pay page
		$save_to_account      = true;
		if (
			! is_user_logged_in()
			|| apply_filters( 'wc_paysafe_show_save_payment_method_checkbox', ! $display_tokenization )
			|| $paysafe_order->contains_subscription()
			|| $paysafe_order->is_pre_order_with_tokenization()
		) {
			$save_to_account = false;
		}
		
		return $save_to_account;
	}
	
	public function get_company_name() {
		$name = '' != $this->get_gateway()->get_option( 'layover_merchant_name', '' ) ? $this->get_gateway()->get_option( 'layover_merchant_name', '' ) : get_bloginfo( 'name' );
		
		return apply_filters( 'wc_paysafe_redirect_company_name', $name, $this->get_gateway() );
	}
	
	public function get_locale() {
		return $this->get_gateway()->get_option( 'locale', 'en_US' );
	}
	
	public function get_user_prefix() {
		return $this->get_gateway()->get_option( 'user_prefix' );
	}
}