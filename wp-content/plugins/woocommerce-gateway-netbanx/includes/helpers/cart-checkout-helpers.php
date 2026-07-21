<?php

namespace WcPaysafe\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Cart_Checkout_Helpers {
	
	public static function empty_cart( $clear_persistent_cart = true ) {
		if ( ! isset( WC()->cart ) || '' === WC()->cart ) {
			WC()->cart = new \WC_Cart();
		}
		WC()->cart->empty_cart( $clear_persistent_cart );
	}
	
	public static function get_shipping_options( $shipping_address, $itemized_display_items = false ) {
		try {
			// Set the shipping options.
			$data = [];
			
			// Remember current shipping method before resetting.
			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods', [] );
			self::calculate_shipping( apply_filters( 'wc_paysafe_payment_request_shipping_posted_values', $shipping_address ) );
			
			$packages          = WC()->shipping->get_packages();
			$shipping_rate_ids = [];
			
			if ( ! empty( $packages ) && WC()->customer->has_calculated_shipping() ) {
				foreach ( $packages as $package_key => $package ) {
					if ( empty( $package['rates'] ) ) {
						throw new \Exception( __( 'Unable to find shipping method for address.', 'wc_paysafe' ) );
					}
					
					foreach ( $package['rates'] as $key => $rate ) {
						if ( in_array( $rate->id, $shipping_rate_ids, true ) ) {
							// The Payment Requests will try to load indefinitely if there are duplicate shipping
							// option IDs.
							throw new \Exception( __( 'Unable to provide shipping options for Payment Requests.', 'wc_paysafe' ) );
						}
						$shipping_rate_ids[]        = $rate->id;
						$data['shipping_options'][] = [
							'id'      => $rate->id,
							'label'   => $rate->label,
							'detail'  => '',
							'amount'  => wc_format_decimal( $rate->cost ),
							'amount1' => Formatting::get_pay_amount( $rate->cost ),
							'amount2' => Formatting::format_amount( $rate->cost ),
						];
					}
				}
			} else {
				throw new \Exception( __( 'Unable to find shipping method for address.', 'wc_paysafe' ) );
			}
			
			// The first shipping option is automatically applied on the client.
			// Keep chosen shipping method by sorting shipping options if the method still available for new address.
			// Fallback to the first available shipping method.
			if ( isset( $data['shipping_options'][0] ) ) {
				if ( isset( $chosen_shipping_methods[0] ) ) {
					$chosen_method_id         = $chosen_shipping_methods[0];
					$compare_shipping_options = function ( $a, $b ) use ( $chosen_method_id ) {
						if ( $a['id'] === $chosen_method_id ) {
							return - 1;
						}
						
						if ( $b['id'] === $chosen_method_id ) {
							return 1;
						}
						
						return 0;
					};
					usort( $data['shipping_options'], $compare_shipping_options );
				}
				
				$first_shipping_method_id = $data['shipping_options'][0]['id'];
				self::update_shipping_method( [ $first_shipping_method_id ] );
				$data['chosen_shipping_option'] = $first_shipping_method_id;
			}
			
			WC()->cart->calculate_totals();
			
			self::maybe_restore_recurring_chosen_shipping_methods( $chosen_shipping_methods );
			
			$data           += self::build_display_items( $itemized_display_items );
			$data['result'] = 'success';
		}
		catch ( \Exception $e ) {
			$data           += self::build_display_items( $itemized_display_items );
			$data['result'] = 'invalid_shipping_address';
		}
		
		return $data;
	}
	
	public static function update_shipping_method( $shipping_methods ) {
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		
		if ( is_array( $shipping_methods ) ) {
			foreach ( $shipping_methods as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}
		}
		
		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
	}
	
	public static function maybe_restore_recurring_chosen_shipping_methods( $previous_chosen_methods = [] ) {
		if ( empty( WC()->cart->recurring_carts ) || ! method_exists( 'WC_Subscriptions_Cart', 'get_recurring_shipping_package_key' ) ) {
			return;
		}
		
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods', [] );
		
		foreach ( WC()->cart->recurring_carts as $recurring_cart_key => $recurring_cart ) {
			foreach ( $recurring_cart->get_shipping_packages() as $recurring_cart_package_index => $recurring_cart_package ) {
				$package_key = \WC_Subscriptions_Cart::get_recurring_shipping_package_key( $recurring_cart_key, $recurring_cart_package_index );
				
				// If the recurring cart package key is found in the previous chosen methods, but not in the current chosen methods, restore it.
				if ( isset( $previous_chosen_methods[ $package_key ] ) && ! isset( $chosen_shipping_methods[ $package_key ] ) ) {
					$chosen_shipping_methods[ $package_key ] = $previous_chosen_methods[ $package_key ];
				}
			}
		}
		
		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
	}
	
	public static function calculate_shipping( $address = [] ) {
		$country   = $address['country'];
		$state     = $address['state'];
		$postcode  = $address['postcode'];
		$city      = $address['city'];
		$address_1 = $address['address'];
		$address_2 = $address['address_2'];
		
		// Normalizes state to calculate shipping zones.
		$state = self::get_normalized_state( $state, $country );
		
		// Normalizes postal code in case of redacted data from Apple Pay.
		$postcode = self::get_normalized_postal_code( $postcode, $country );
		
		WC()->shipping->reset_shipping();
		
		if ( $postcode && \WC_Validation::is_postcode( $postcode, $country ) ) {
			$postcode = wc_format_postcode( $postcode, $country );
		}
		
		if ( $country ) {
			WC()->customer->set_location( $country, $state, $postcode, $city );
			WC()->customer->set_shipping_location( $country, $state, $postcode, $city );
		} else {
			WC()->customer->set_billing_address_to_base();
			WC()->customer->set_shipping_address_to_base();
		}
		
		WC()->customer->set_calculated_shipping( true );
		WC()->customer->save();
		
		$packages = [];
		
		$packages[0]['contents']                 = WC()->cart->get_cart();
		$packages[0]['contents_cost']            = 0;
		$packages[0]['applied_coupons']          = WC()->cart->applied_coupons;
		$packages[0]['user']['ID']               = get_current_user_id();
		$packages[0]['destination']['country']   = $country;
		$packages[0]['destination']['state']     = $state;
		$packages[0]['destination']['postcode']  = $postcode;
		$packages[0]['destination']['city']      = $city;
		$packages[0]['destination']['address']   = $address_1;
		$packages[0]['destination']['address_2'] = $address_2;
		
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( $item['data']->needs_shipping() ) {
				if ( isset( $item['line_total'] ) ) {
					$packages[0]['contents_cost'] += $item['line_total'];
				}
			}
		}
		
		$packages = apply_filters( 'woocommerce_cart_shipping_packages', $packages );
		
		WC()->shipping->calculate_shipping( $packages );
	}
	
	/**
	 * @param \WC_Order $order
	 *
	 * @return array
	 */
	public static function build_order_display_items( $order ) {
		
		$discounts   = $order->get_discount_total();
		$tax         = $order->get_total_tax();
		$shipping    = $order->get_shipping_total();
		$order_total = $order->get_total( false );
		
		$items[] = [
			'label' => esc_html( __( 'Subtotal', 'wc_paysafe' ) ),
			'type'  => 'SUBTOTAL',
			'price' => Formatting::get_pay_amount( $order->get_subtotal() ),
		];
		
		if ( wc_tax_enabled() ) {
			$items[] = [
				'label' => esc_html( __( 'Tax', 'wc_paysafe' ) ),
				'type'  => 'TAX',
				'price' => Formatting::get_pay_amount( $tax ),
			];
		}
		
		if ( 0 < $shipping ) {
			$items[] = [
				'label' => esc_html( __( 'Shipping', 'woocommerce' ) ),
				'type'  => 'LINE_ITEM',
				'price' => Formatting::get_pay_amount( $shipping ),
			];
		}
		
		if ( 0 < $discounts ) {
			$items[] = [
				'label' => esc_html( __( 'Discount', 'wc_paysafe' ) ),
				'type'  => 'LINE_ITEM',
				'price' => Formatting::get_pay_amount( $discounts ),
			];
		}
		
		return [
			'displayItems' => $items,
			'total'        => [
				'label'   => __( 'Payment for order', 'wc_paysafe' ),
				'price'   => max( 0, apply_filters( 'wc_paysafe_calculated_total', Formatting::get_pay_amount( $order_total ), $order_total, WC()->cart ) ),
				'pending' => false,
			],
		];
	}
	
	public static function build_display_items( $itemized_display_items = false ) {
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		
		$items         = [];
		$lines         = [];
		$subtotal      = 0;
		$discounts     = 0;
		$display_items = ! apply_filters( 'wc_paysafe_payment_request_hide_itemization', true ) || $itemized_display_items;
		
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$subtotal       += $cart_item['line_subtotal'];
			$amount         = $cart_item['line_subtotal'];
			$quantity_label = 1 < $cart_item['quantity'] ? ' (x' . $cart_item['quantity'] . ')' : '';
			$product_name   = $cart_item['data']->get_name();
			
			$lines[] = [
				'label' => $product_name . $quantity_label,
				'type'  => 'LINE_ITEM',
				'price' => Formatting::get_pay_amount( $amount ),
			];
		}
		
		if ( $display_items ) {
			$items = array_merge( $items, $lines );
		} else {
			// Default show only subtotal instead of itemization.
			
			$items[] = [
				'label' => 'Subtotal',
				'type'  => 'SUBTOTAL',
				'price' => Formatting::get_pay_amount( $subtotal ),
			];
		}
		
		if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
			$discounts = wc_format_decimal( WC()->cart->get_cart_discount_total(), WC()->cart->dp );
		} else {
			$applied_coupons = array_values( WC()->cart->get_coupon_discount_totals() );
			
			foreach ( $applied_coupons as $amount ) {
				$discounts += (float) $amount;
			}
		}
		
		$discounts   = wc_format_decimal( $discounts, WC()->cart->dp );
		$tax         = wc_format_decimal( WC()->cart->tax_total + WC()->cart->shipping_tax_total, WC()->cart->dp );
		$shipping    = wc_format_decimal( WC()->cart->shipping_total, WC()->cart->dp );
		$items_total = wc_format_decimal( WC()->cart->cart_contents_total, WC()->cart->dp ) + $discounts;
		$order_total = version_compare( WC_VERSION, '3.2', '<' ) ? wc_format_decimal( $items_total + $tax + $shipping - $discounts, WC()->cart->dp ) : WC()->cart->get_total( false );
		
		if ( wc_tax_enabled() ) {
			$items[] = [
				'label' => esc_html( __( 'Tax', 'wc_paysafe' ) ),
				'type'  => 'TAX',
				'price' => Formatting::get_pay_amount( $tax ),
			];
		}
		
		if ( WC()->cart->needs_shipping() ) {
			$items[] = [
				'label' => esc_html( __( 'Shipping', 'woocommerce' ) ),
				'type'  => 'LINE_ITEM',
				'price' => Formatting::get_pay_amount( $shipping ),
			];
		}
		
		if ( WC()->cart->has_discount() ) {
			$items[] = [
				'label' => esc_html( __( 'Discount', 'wc_paysafe' ) ),
				'type'  => 'LINE_ITEM',
				'price' => Formatting::get_pay_amount( $discounts ),
			];
		}
		
		if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
			$cart_fees = WC()->cart->fees;
		} else {
			$cart_fees = WC()->cart->get_fees();
		}
		
		// Include fees and taxes as display items.
		foreach ( $cart_fees as $key => $fee ) {
			$items[] = [
				'label' => $fee->name,
				'type'  => 'LINE_ITEM',
				'price' => Formatting::get_pay_amount( $fee->amount ),
			];
		}
		
		return [
			'displayItems' => $items,
			'total'        => [
				'label'   => __( 'Payment for order', 'wc_paysafe' ),
				'price'   => max( 0, apply_filters( 'wc_paysafe_calculated_total', Formatting::get_pay_amount( $order_total ), $order_total, WC()->cart ) ),
				'pending' => false,
			],
		];
	}
	
	/**
	 * Gets the normalized state/county field because in some
	 * cases, the state/county field is formatted differently from
	 * what WC is expecting and throws an error. An example
	 * for Ireland, the county dropdown in Chrome shows "Co. Clare" format.
	 *
	 * @since   5.0.0
	 * @version 5.1.0
	 *
	 * @param string $state   Full state name or an already normalized abbreviation.
	 * @param string $country Two-letter country code.
	 *
	 * @return string Normalized state abbreviation.
	 */
	public static function get_normalized_state( $state, $country ) {
		// If it's empty or already normalized, skip.
		if ( ! $state || self::is_normalized_state( $state, $country ) ) {
			return $state;
		}
		
		// If it's normalized, return.
		if ( self::is_normalized_state( $state, $country ) ) {
			return $state;
		}
		
		// If the above doesn't work, fallback to matching against the list of translated
		// states from WooCommerce.
		return self::get_normalized_state_from_wc_states( $state, $country );
	}
	
	public static function is_normalized_state( $state, $country ) {
		$wc_states = WC()->countries->get_states( $country );
		
		return (
			is_array( $wc_states ) &&
			in_array( $state, array_keys( $wc_states ), true )
		);
	}
	
	/**
	 * Sanitize string for comparison.
	 *
	 * @since 5.1.0
	 *
	 * @param string $string String to be sanitized.
	 *
	 * @return string The sanitized string.
	 */
	public static function sanitize_string( $string ) {
		return trim( wc_strtolower( remove_accents( $string ) ) );
	}
	
	/**
	 * Get normalized state from WooCommerce list of translated states.
	 *
	 * @since 5.1.0
	 *
	 * @param string $state   Full state name or state code.
	 * @param string $country Two-letter country code.
	 *
	 * @return string Normalized state or original state input value.
	 */
	public static function get_normalized_state_from_wc_states( $state, $country ) {
		$wc_states = WC()->countries->get_states( $country );
		
		if ( is_array( $wc_states ) ) {
			foreach ( $wc_states as $wc_state_abbr => $wc_state_value ) {
				if ( preg_match( '/' . preg_quote( $wc_state_value, '/' ) . '/i', $state ) ) {
					return $wc_state_abbr;
				}
			}
		}
		
		return $state;
	}
	
	public static function get_normalized_postal_code( $postcode, $country ) {
		/**
		 * Currently, Apple Pay truncates the UK and Canadian postal codes to the first 4 and 3 characters respectively
		 * when passing it back from the shippingcontactselected object. This causes WC to invalidate
		 * the postal code and not calculate shipping zones correctly.
		 */
		if ( 'GB' === $country ) {
			// Replaces a redacted string with something like LN10***.
			return str_pad( preg_replace( '/\s+/', '', $postcode ), 7, '*' );
		}
		if ( 'CA' === $country ) {
			// Replaces a redacted string with something like L4Y***.
			return str_pad( preg_replace( '/\s+/', '', $postcode ), 6, '*' );
		}
		
		return $postcode;
	}
	
	public static function normalize_state() {
		$billing_country  = ! empty( $_POST['billing_country'] ) ? wc_clean( wp_unslash( $_POST['billing_country'] ) ) : '';
		$shipping_country = ! empty( $_POST['shipping_country'] ) ? wc_clean( wp_unslash( $_POST['shipping_country'] ) ) : '';
		$billing_state    = ! empty( $_POST['billing_state'] ) ? wc_clean( wp_unslash( $_POST['billing_state'] ) ) : '';
		$shipping_state   = ! empty( $_POST['shipping_state'] ) ? wc_clean( wp_unslash( $_POST['shipping_state'] ) ) : '';
		
		// Finally we normalize the state value we want to process.
		if ( $billing_state && $billing_country ) {
			$_POST['billing_state'] = Cart_Checkout_Helpers::get_normalized_state( $billing_state, $billing_country );
		}
		
		if ( $shipping_state && $shipping_country ) {
			$_POST['shipping_state'] = Cart_Checkout_Helpers::get_normalized_state( $shipping_state, $shipping_country );
		}
	}
	
	public static function validate_state() {
		$wc_checkout     = \WC_Checkout::instance();
		$posted_data     = $wc_checkout->get_posted_data();
		$checkout_fields = $wc_checkout->get_checkout_fields();
		$countries       = WC()->countries->get_countries();
		
		$is_supported = true;
		// Checks if billing state is missing and is required.
		if ( ! empty( $checkout_fields['billing']['billing_state']['required'] ) && '' === $posted_data['billing_state'] ) {
			$is_supported = false;
		}
		
		// Checks if shipping state is missing and is required.
		if ( WC()->cart->needs_shipping_address() && ! empty( $checkout_fields['shipping']['shipping_state']['required'] ) && '' === $posted_data['shipping_state'] ) {
			$is_supported = false;
		}
		
		if ( ! $is_supported ) {
			wc_add_notice(
				sprintf(
				/* translators: 1) country. */
					__( 'The Payment Request button is not supported in %1$s because some required fields couldn\'t be verified. Please proceed to the checkout page and try again.', 'woocommerce-gateway-stripe' ),
					isset( $countries[ $posted_data['billing_country'] ] ) ? $countries[ $posted_data['billing_country'] ] : $posted_data['billing_country']
				),
				'error'
			);
		}
	}
}