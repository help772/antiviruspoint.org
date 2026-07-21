<?php
/**
 * WooCommerce CyberSource
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce CyberSource to newer
 * versions in the future. If you wish to customize WooCommerce CyberSource for your
 * needs please refer to http://docs.woocommerce.com/document/cybersource-payment-gateway/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource\API;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

class Helper {


	/** @var array map of CyberSource API card type codes to framework card type strings */
	public static $card_type_map = [
		'001' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA,
		'002' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD,
		'003' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX,
		'004' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER,
		'005' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB,
		'007' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB,
		'024' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MAESTRO,
		'036' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_CARTEBLEUE,
		'042' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MAESTRO,
	];

	/** @var array map of CyberSource API bank account type codes to framework bank account type strings */
	public static $bank_account_type_map = [
		'C' => 'checking',
		'S' => 'savings',
	];


	/** Credit card methods *******************************************************************************************/


	/**
	 * Converts a CyberSource API card type code to a standard type name.
	 *
	 * @since 2.0.0
	 *
	 * @param string $code CyberSource API card type code, like 001 or 002
	 * @return string
	 */
	public static function convert_code_to_card_type( $code ) {

		return ! empty( self::$card_type_map[ $code ] ) ? self::$card_type_map[ $code ] : current( self::$card_type_map );
	}


	/**
	 * Converts a card type name to a CyberSource API code.
	 *
	 * @since 2.0.0
	 *
	 * @param string $type framework card type name, like visa
	 * @return string
	 */
	public static function convert_card_type_to_code( $type ) {

		$map = array_flip( self::$card_type_map );

		return ! empty( $map[ $type ] ) ? $map[ $type ] : current( $map );
	}


	/** eCheck methods ************************************************************************************************/


	/**
	 * Converts a CyberSource API card type code to a standard type name.
	 *
	 * @since 2.0.0
	 *
	 * @param string $code CyberSource API card type code, like 001 or 002
	 * @return string
	 */
	public static function convert_code_to_bank_account_type( $code ) {

		return ! empty( self::$bank_account_type_map[ $code ] ) ? self::$bank_account_type_map[ $code ] : current( self::$bank_account_type_map );
	}


	/**
	 * Converts a card type name to a CyberSource API code.
	 *
	 * @since 2.0.0
	 *
	 * @param string $type framework card type name, like visa
	 * @return string
	 */
	public static function convert_bank_account_type_to_code( $type ) {

		$map = array_flip( self::$bank_account_type_map );

		return ! empty( $map[ $type ] ) ? $map[ $type ] : current( $map );
	}


}
