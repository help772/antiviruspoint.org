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
class Currency {
	
	/**
	 * Is the currency allowed for direct debit processing
	 *
	 * @param $currency
	 *
	 * @return bool
	 */
	public static function is_allowed_direct_debit_currency( $currency ) {
		if ( ! in_array( $currency, self::sepa_currencies() )
		     && ! in_array( $currency, self::bacs_currencies() )
		     && ! in_array( $currency, self::eft_currencies() )
		     && ! in_array( $currency, self::ach_currencies() ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param $currency
	 *
	 * @return bool
	 */
	public static function is_sepa_currency( $currency ) {
		return in_array(
			$currency,
			self::sepa_currencies()
		);
	}
	
	/**
	 * Currencies for bacs processing
	 * @return array
	 */
	public static function sepa_currencies() {
		return apply_filters( 'wc_paysafe_direct_debit_sepa_currencies', array(
			'EUR'
		) );
	}
	
	/**
	 * @param $currency
	 *
	 * @return bool
	 */
	public static function is_bacs_currency( $currency ) {
		return in_array(
			$currency,
			self::bacs_currencies()
		);
	}
	
	/**
	 * Currencies for bacs processing
	 * @return array
	 */
	public static function bacs_currencies() {
		return apply_filters( 'wc_paysafe_direct_debit_bacs_currencies', array(
			'GBP'
		) );
	}
	
	/**
	 * @param $currency
	 *
	 * @return bool
	 */
	public static function is_eft_currency( $currency ) {
		return in_array(
			$currency,
			self::eft_currencies()
		);
	}
	
	/**
	 * Currencies for eft processing
	 * @return array
	 */
	public static function eft_currencies() {
		return apply_filters( 'wc_paysafe_direct_debit_eft_currencies', array(
			'CAD'
		) );
	}
	
	/**
	 * @param $currency
	 *
	 * @return bool
	 */
	public static function is_ach_currency( $currency ) {
		return in_array(
			$currency,
			self::ach_currencies()
		);
	}
	
	/**
	 * Currencies for ach processing
	 * @return array
	 */
	public static function ach_currencies() {
		return apply_filters( 'wc_paysafe_direct_debit_ach_currencies', array(
			'USD'
		) );
	}
}