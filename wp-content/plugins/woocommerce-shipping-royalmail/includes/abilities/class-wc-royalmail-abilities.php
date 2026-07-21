<?php
/**
 * Ability loader for WooCommerce Royal Mail.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Royal Mail ability definition classes with WooCommerce.
 */
class WC_RoyalMail_Abilities {
	/**
	 * Initialize ability registration.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
			return;
		}

		self::load_definition_classes();
		add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'register_ability_definition_classes' ) );
	}

	/**
	 * Register ability definition classes with WooCommerce's loader.
	 *
	 * @param array $classes Ability definition class names.
	 * @return array
	 */
	public static function register_ability_definition_classes( array $classes ): array {
		$classes[] = WC_RoyalMail_List_Services_Ability::class;
		$classes[] = WC_RoyalMail_Get_Settings_Ability::class;

		return $classes;
	}

	/**
	 * Load concrete ability definition classes.
	 *
	 * @return void
	 */
	private static function load_definition_classes(): void {
		require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/abilities/class-wc-royalmail-ability-helper.php';
		require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/abilities/class-wc-royalmail-list-services-ability.php';
		require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/abilities/class-wc-royalmail-get-settings-ability.php';
	}
}
