<?php
/**
 * WordPress Abilities API integration for WooCommerce Canada Post Shipping.
 *
 * @package woocommerce-shipping-canada-post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads Canada Post ability definitions.
 */
class WC_Shipping_Canada_Post_Abilities {

	/**
	 * Whether ability registration hooks have been initialized.
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

		if ( interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
			self::load_ability_definition_classes();
			add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );
			self::$initialized = true;
		}
	}

	/**
	 * Add Canada Post ability definitions to WooCommerce's loader.
	 *
	 * @param array $classes Ability definition class names.
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ) {
		$classes[] = WC_Shipping_Canada_Post_Get_Settings_Ability::class;
		$classes[] = WC_Shipping_Canada_Post_List_Services_Ability::class;
		$classes[] = WC_Shipping_Canada_Post_List_Package_Options_Ability::class;

		return $classes;
	}

	/**
	 * Load ability definition classes after Woo's AbilityDefinition interface exists.
	 *
	 * @return void
	 */
	private static function load_ability_definition_classes() {
		require_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post.php';
		require_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post-ability-helper.php';
		require_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post-ability-projection.php';
		require_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post-get-settings-ability.php';
		require_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post-list-services-ability.php';
		require_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post-list-package-options-ability.php';
	}
}
