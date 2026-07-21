<?php
/**
 * WC_Shipment_Tracking_Abilities class file.
 *
 * @package WC_Shipment_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads Shipment Tracking ability definitions.
 */
class WC_Shipment_Tracking_Abilities {

	/**
	 * Initialize ability registration hooks.
	 *
	 * @return void
	 */
	public static function init() {
		if ( interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
			self::load_ability_definition_classes();
			add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );
		}
	}

	/**
	 * Add Shipment Tracking ability definitions to WooCommerce's loader.
	 *
	 * @param array $classes Ability definition class names.
	 *
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ) {
		$classes[] = WC_Shipment_Tracking_List_Providers_Ability::class;
		$classes[] = WC_Shipment_Tracking_List_Tracking_Items_Ability::class;

		return $classes;
	}

	/**
	 * Load ability definition classes after Woo's AbilityDefinition interface exists.
	 *
	 * @return void
	 */
	private static function load_ability_definition_classes() {
		require_once WC_SHIPMENT_TRACKING_DIR . '/includes/class-wc-shipment-tracking-list-providers-ability.php';
		require_once WC_SHIPMENT_TRACKING_DIR . '/includes/class-wc-shipment-tracking-list-tracking-items-ability.php';
	}
}
