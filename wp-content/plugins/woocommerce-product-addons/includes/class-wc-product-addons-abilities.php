<?php
/**
 * Product Add-ons Abilities API integration.
 *
 * @package WooCommerce Product Add-Ons
 * @version 8.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads Product Add-Ons ability definitions.
 *
 * @version 8.3.1
 */
class WC_Product_Addons_Abilities {

	/**
	 * Initialize ability registration hooks.
	 *
	 * @version 8.3.1
	 */
	public static function init() {
		if ( interface_exists( '\\Automattic\\WooCommerce\\Abilities\\AbilityDefinition' ) ) {
			self::load_ability_definition_classes();
			add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );
		}
	}

	/**
	 * Adds Product Add-Ons ability definitions to WooCommerce's ability loader.
	 *
	 * @param array $classes Ability definition class names.
	 * @version 8.3.1
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ) {
		$classes[] = WC_Product_Addons_Global_Add_On_Groups_Ability::class;
		$classes[] = WC_Product_Addons_Product_Add_Ons_Ability::class;

		return $classes;
	}

	/**
	 * Loads WooCommerce ability definition classes.
	 *
	 * @version 8.3.1
	 * @return void
	 */
	private static function load_ability_definition_classes() {
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/class-wc-product-addons-ability-output.php';
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/class-wc-product-addons-global-add-on-groups-ability.php';
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/class-wc-product-addons-product-add-ons-ability.php';
	}
}
