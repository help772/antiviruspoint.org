<?php
/**
 * Australia Post Abilities bootstrap.
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers Australia Post ability definitions with WooCommerce.
 */
class WC_Shipping_Australia_Post_Abilities {
	/**
	 * Initialize ability registration.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! interface_exists( '\Automattic\WooCommerce\Abilities\AbilityDefinition' ) ) {
			return;
		}

		require_once __DIR__ . '/abilities/class-wc-shipping-australia-post-abilities-helper.php';
		require_once __DIR__ . '/abilities/class-wc-shipping-australia-post-get-rate-configuration-ability.php';
		require_once __DIR__ . '/abilities/class-wc-shipping-australia-post-get-service-catalog-ability.php';

		add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'add_ability_definition_classes' ) );
	}

	/**
	 * Add Australia Post ability definition classes to WooCommerce's loader.
	 *
	 * @param array $classes Ability definition class names.
	 *
	 * @return array
	 */
	public static function add_ability_definition_classes( array $classes ): array {
		$classes[] = WC_Shipping_Australia_Post_Get_Rate_Configuration_Ability::class;
		$classes[] = WC_Shipping_Australia_Post_Get_Service_Catalog_Ability::class;

		return $classes;
	}
}
