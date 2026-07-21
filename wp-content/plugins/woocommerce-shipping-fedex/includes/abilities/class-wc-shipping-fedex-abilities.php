<?php
/**
 * WordPress Abilities API integration for WooCommerce FedEx Shipping.
 *
 * @package WC_Shipping_Fedex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers read-only FedEx abilities.
 */
class WC_Shipping_Fedex_Abilities {

	/**
	 * Hooks ability registration into WooCommerce's Abilities API loader.
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
			return;
		}

		self::load_ability_definition_classes();
		add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );
	}

	/**
	 * Adds FedEx ability definitions to WooCommerce's ability loader.
	 *
	 * @param array $classes Ability definition class names.
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ) {
		$classes[] = WC_Shipping_Fedex_Get_Connection_Status_Ability::class;
		$classes[] = WC_Shipping_Fedex_Get_Rate_Configuration_Ability::class;

		return $classes;
	}

	/**
	 * Loads FedEx ability definition classes and shared helpers.
	 *
	 * @return void
	 */
	private static function load_ability_definition_classes() {
		require_once WC_SHIPPING_FEDEX_ABSPATH . 'includes/abilities/class-wc-shipping-fedex-ability-data.php';
		require_once WC_SHIPPING_FEDEX_ABSPATH . 'includes/abilities/class-wc-shipping-fedex-get-connection-status-ability.php';
		require_once WC_SHIPPING_FEDEX_ABSPATH . 'includes/abilities/class-wc-shipping-fedex-get-rate-configuration-ability.php';
	}
}
