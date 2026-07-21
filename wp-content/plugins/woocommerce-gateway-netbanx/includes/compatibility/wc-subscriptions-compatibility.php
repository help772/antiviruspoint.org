<?php

namespace WcPaysafe\Compatibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Subscriptions_Compatibility {
	
	public static $is_subs_2_0;
	/**
	 * Is WC 2.2+
	 * @var bool
	 */
	public static $is_subs_2_2;
	
	public static function is_active() {
		return class_exists( 'WC_Subscriptions' );
	}
	
	public static function get_subs_version() {
		return class_exists( 'WC_Subscriptions' ) ? \WC_Subscriptions::$version : '1.0.0';
	}
	
	public static function is_equal_or_gtr( $version ) {
		return version_compare( self::get_subs_version(), $version, '>=' );
	}
	
	/**
	 * Detect, if we are using WC 2.4+
	 *
	 * @since 3.3.0
	 * @return bool
	 */
	public static function is_2_0() {
		if ( is_bool( self::$is_subs_2_0 ) ) {
			return self::$is_subs_2_0;
		}
		
		return self::$is_subs_2_0 = self::is_equal_or_gtr( '2.0.0' );
	}
	
	/**
	 * Detect, if we are using WC 2.4+
	 *
	 * @since 3.3.0
	 * @return bool
	 */
	public static function is_2_2() {
		if ( is_bool( self::$is_subs_2_2 ) ) {
			return self::$is_subs_2_2;
		}
		
		return self::$is_subs_2_2 = self::is_equal_or_gtr( '2.2.0' );
	}
	
	/**
	 * @param \WC_Subscription
	 *
	 * @return \WC_Order
	 */
	public static function get_parent( $subscription ) {
		if ( self::is_2_2() ) {
			return $subscription->get_parent();
		}
		
		return $subscription->order;
	}
}