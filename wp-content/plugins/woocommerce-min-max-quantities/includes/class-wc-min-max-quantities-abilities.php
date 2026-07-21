<?php
/**
 * WordPress Abilities API integration for WooCommerce Min/Max Quantities.
 *
 * @package  WooCommerce Min/Max Quantities
 * @since    5.2.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers read-only Min/Max Quantities abilities.
 *
 * @class    WC_Min_Max_Quantities_Abilities
 * @version  5.2.9
 */
class WC_Min_Max_Quantities_Abilities {

	/**
	 * Hooks ability registration into WooCommerce's Abilities API loader.
	 *
	 * @return void
	 */
	public static function init() {
		if ( interface_exists( '\\Automattic\\WooCommerce\\Abilities\\AbilityDefinition' ) ) {
			self::load_ability_definition_classes();
			add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );
		}
	}

	/**
	 * Adds Min/Max Quantities ability definitions to WooCommerce's ability loader.
	 *
	 * @param array $classes Ability definition class names.
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ) {
		$classes[] = WC_MMQ_Global_Rules_Ability::class;
		$classes[] = WC_MMQ_Product_Rules_Ability::class;
		$classes[] = WC_MMQ_Category_Rules_Ability::class;

		return $classes;
	}

	/**
	 * Loads WooCommerce ability definition classes.
	 *
	 * @return void
	 */
	private static function load_ability_definition_classes() {
		require_once WC_MMQ_ABSPATH . 'includes/class-wc-mmq-global-rules-ability.php';
		require_once WC_MMQ_ABSPATH . 'includes/class-wc-mmq-product-rules-ability.php';
		require_once WC_MMQ_ABSPATH . 'includes/class-wc-mmq-category-rules-ability.php';
	}
}

WC_Min_Max_Quantities_Abilities::init();
