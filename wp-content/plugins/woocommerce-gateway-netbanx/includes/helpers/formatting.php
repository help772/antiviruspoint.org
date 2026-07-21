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
class Formatting {
	
	public static function get_pay_amount( $total, $currency = '' ) {
		if ( ! $currency ) {
			$currency = get_woocommerce_currency();
		}
		
		$currency = strtolower( $currency );
		
		if ( in_array( $currency, self::no_decimal_currencies(), true ) ) {
			return $total;
		} elseif ( in_array( $currency, self::three_decimal_currencies(), true ) ) {
			$price_decimals = wc_get_price_decimals();
			$amount         = wc_format_decimal( ( (float) $total ), $price_decimals );
			
			return $amount - ( $amount % 10 );
		} else {
			return wc_format_decimal( ( (float) $total ), wc_get_price_decimals() ); // In cents.
		}
	}
	
	public static function no_decimal_currencies() {
		return [
			'JPY',
		];
	}
	
	public static function three_decimal_currencies() {
		return [
			'TND',
		];
	}
	
	/**
	 * Format amount for requests. Amount should be with no decimals and no leading 0.
	 *
	 * @since 2.0
	 *
	 * @param $amount
	 *
	 * @return string
	 */
	public static function format_amount( $amount, $currency = '' ) {
		if ( in_array( $currency, self::no_decimal_currencies(), true ) ) {
			$formatted = wc_format_decimal( $amount, 0 );
		} elseif ( in_array( $currency, self::three_decimal_currencies(), true ) ) {
			$formatted = ltrim( number_format( $amount, 3, '', '' ), '0' );
		} else {
			$formatted = ltrim( number_format( $amount, 2, '', '' ), '0' );
		}
		
		// Since we are trimming 0 we can end up with an empty string on free orders
		// so in this case make sure amount is 0.
		if ( '' == $formatted ) {
			$formatted = 0;
		}
		
		return $formatted;
	}
	
	public static function format_amount_from_cent( $amount, $currency = '' ) {
		if ( 'JPY' == $currency ) {
			$formatted = $amount;
		} elseif ( 'TND' == $currency ) {
			$formatted = $amount / 1000;
		} else {
			$formatted = $amount / 100;
		}
		
		if ( '' === $formatted ) {
			$formatted = 0;
		}
		
		return $formatted;
	}
	
	/**
	 * Convert string to UTF-8
	 *
	 * @since 2.0
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function convert_to_utf( $str ) {
		if ( ! function_exists( 'mb_convert_encoding' ) ) {
			return wp_check_invalid_utf8( $str, true );
		}
		
		return mb_convert_encoding( $str, 'utf-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS,windows-1251' );
	}
	
	/**
	 * Formats and returns a the passed string
	 *
	 * @since 2.0
	 *
	 * @param string $string            String to be formatted
	 * @param int    $limit             Limit characters of the string
	 * @param bool   $remove_restricted Whether to remove restricted characters
	 * @param string $suffix            Add to the end of the string
	 *
	 * @return string
	 */
	public static function format_string( $string, $limit, $remove_restricted = true, $suffix = '' ) {
		if ( function_exists( 'wc_trim_string' ) ) {
			$string = wc_trim_string( $string, $limit, $suffix );
		} else {
			if ( strlen( $string ) > $limit ) {
				$string = substr( $string, 0, ( $limit - 3 ) ) . $suffix;
			}
		}
		
		if ( $remove_restricted ) {
			$string = self::remove_restricted_characters( $string );
		}
		
		return html_entity_decode( self::convert_to_utf( $string ), ENT_NOQUOTES, 'UTF-8' );
	}
	
	/**
	 * Removes Paysafe request restricted characters from a string.
	 *
	 * 'paysafe_restricted_characters' - can be used to add to the restricted characters
	 *
	 * @since 2.1
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function remove_restricted_characters( $string ) {
		/**
		 * @deprecated netbanx_restricted_characters is deprecated use the filter below
		 */
		$restricted_characters = apply_filters(
			'netbanx_restricted_characters',
			array( '"', ';', '^', '*', '<', '>', '/', '[', ']', "\\", PHP_EOL )
		);
		
		$restricted_characters = apply_filters(
			'paysafe_restricted_characters',
			$restricted_characters
		);
		
		return str_replace( $restricted_characters, '', $string );
	}
	
	/**
	 * Returns the allowed order statuses, in which we can save the customer Paysafe profile to the order.
	 * We don't want to save the profiles too early in the order process.
	 * We want to make sure that the order is at least in a status that will not get overwritten by the WC order generation process.
	 *
	 * @since. 2.3
	 *
	 * @return mixed
	 */
	public static function allowed_order_status_to_save_profile() {
		/**
		 * @deprecated wc_netbanx_allowed_order_status_to_save_profile is deprecated use the action below
		 */
		$status = apply_filters(
			'wc_netbanx_allowed_order_status_to_save_profile',
			array(
				'processing',
				'on-hold',
				'completed',
			)
		);
		
		$status = apply_filters(
			'wc_paysafe_allowed_order_status_to_save_profile',
			$status
		);
		
		return $status;
	}
	
	/**
	 * Remove empty array elements from the array, recursively.
	 *
	 * @since 3.3.0
	 *
	 * @param array    $input
	 * @param callable $callback Additional callback to apply to the array_filter
	 *
	 * @return array
	 */
	public static function array_filter_recursive( array $input, callable $callback = null ) {
		foreach ( $input as &$value ) {
			if ( is_array( $value ) ) {
				$value = self::array_filter_recursive( $value );
			}
		}
		
		if ( null === $callback ) {
			$callback = array( __CLASS__, 'is_not_empty' );
		}
		
		return array_filter( $input, $callback );
	}
	
	/**
	 * Looks into the passed variable and returns true if it is not empty.
	 *
	 * 0 - is not an empty value
	 *
	 * @since 3.7.0
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public static function is_not_empty( $value ) {
		if ( 0 === $value ) {
			return true;
		}
		
		return ! empty( $value );
	}
	
	public static function kses_form_html( $content ) {
		$allowed = apply_filters( 'wc_paysafe_allowed_kses_input', [
			'img' => [
				'alt'              => 1,
				'align'            => 1,
				'border'           => 1,
				'height'           => 1,
				'hspace'           => 1,
				'loading'          => 1,
				'longdesc'         => 1,
				'vspace'           => 1,
				'src'              => 1,
				'usemap'           => 1,
				'width'            => 1,
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
			],
			
			'i' => [
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
				'xml:lang'         => 1,
			],
			
			'label' => [
				'for'              => 1,
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
				'xml:lang'         => 1,
			],
			
			'a' => [
				'href'     => 1,
				'rel'      => 1,
				'rev'      => 1,
				'name'     => 1,
				'target'   => 1,
				'download' => [
					'valueless' => 'y',
				],
				
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
				'xml:lang'         => 1,
			],
			
			'div' => [
				'align'            => 1,
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
				'xml:lang'         => 1,
			],
			
			'span' => [
				'align'            => 1,
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
				'xml:lang'         => 1,
			],
			
			'p' => [
				'align'            => 1,
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
			],
			
			'input' => [
				'align'            => 1,
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
				'type'             => 1,
				'autocomplete'     => 1,
				'inputmode'        => 1,
				'autocorrect'      => 1,
				'autocapitalize'   => 1,
				'spellcheck'       => 1,
				'placeholder'      => 1,
				'name'             => array(),
			],
			
			'fieldset' => [
				'aria-describedby' => 1,
				'aria-details'     => 1,
				'aria-label'       => 1,
				'aria-labelledby'  => 1,
				'aria-hidden'      => 1,
				'class'            => 1,
				'data-*'           => 1,
				'dir'              => 1,
				'id'               => 1,
				'lang'             => 1,
				'style'            => 1,
				'title'            => 1,
				'role'             => 1,
				'xml:lang'         => 1,
			],
		
		], $content );
		
		return wp_kses( $content, $allowed );
	}
	
	public static function get_log_id( $gateway_id ) {
		if ( is_object( $gateway_id ) ) {
			$gateway_id = $gateway_id->id;
		}
		
		$log_id = 'paysafe';
		if ( 'paysafe_checkout_payments' === $gateway_id ) {
			$log_id = $gateway_id;
		}
		
		return $log_id;
	}
}