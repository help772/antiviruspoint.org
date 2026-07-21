<?php
/**
 * WooCommerce USPS Abilities API integration.
 *
 * @package WC_Shipping_USPS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads WooCommerce USPS ability definitions.
 */
class WC_Shipping_USPS_Abilities {

	/**
	 * Whether ability hooks have been initialized.
	 *
	 * @var bool
	 */
	private static $initialized = false;

	/**
	 * Initialize ability registration hooks.
	 *
	 * @return void
	 */
	public static function init() {
		if ( self::$initialized ) {
			return;
		}

		if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
			return;
		}

		self::load_ability_definition_classes();
		add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );

		self::$initialized = true;
	}

	/**
	 * Adds USPS ability definitions to WooCommerce's ability loader.
	 *
	 * @param array $classes Ability definition class names.
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ) {
		$classes[] = WC_Shipping_USPS_Get_Settings_Ability::class;
		$classes[] = WC_Shipping_USPS_List_Services_Ability::class;
		$classes[] = WC_Shipping_USPS_List_Packages_Ability::class;

		return $classes;
	}

	/**
	 * Loads USPS ability definition classes.
	 *
	 * @return void
	 */
	private static function load_ability_definition_classes() {
		require_once WC_USPS_ABSPATH . 'includes/abilities/class-wc-shipping-usps-ability-output.php';
		require_once WC_USPS_ABSPATH . 'includes/abilities/class-wc-shipping-usps-get-settings-ability.php';
		require_once WC_USPS_ABSPATH . 'includes/abilities/class-wc-shipping-usps-list-services-ability.php';
		require_once WC_USPS_ABSPATH . 'includes/abilities/class-wc-shipping-usps-list-packages-ability.php';
	}
}
