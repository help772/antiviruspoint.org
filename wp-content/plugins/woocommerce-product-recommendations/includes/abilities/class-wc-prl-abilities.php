<?php
/**
 * Product Recommendations abilities loader.
 *
 * @package  WooCommerce Product Recommendations
 * @since    4.3.4
 * @version  4.3.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Product Recommendations ability definitions through WooCommerce.
 *
 * @class    WC_PRL_Abilities
 * @version  4.3.4
 */
class WC_PRL_Abilities {

	/**
	 * WooCommerce ability definition interface.
	 */
	const ABILITY_DEFINITION_INTERFACE = '\Automattic\WooCommerce\Abilities\AbilityDefinition';

	/**
	 * Initialize ability loading when WooCommerce's ability loader is available.
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! interface_exists( self::ABILITY_DEFINITION_INTERFACE ) ) {
			return;
		}

		require_once WC_PRL_ABSPATH . 'includes/abilities/class-wc-prl-query-engines-ability.php';
		require_once WC_PRL_ABSPATH . 'includes/abilities/class-wc-prl-query-deployments-ability.php';
		require_once WC_PRL_ABSPATH . 'includes/abilities/class-wc-prl-describe-components-ability.php';

		add_filter( 'woocommerce_ability_definition_classes', array( __CLASS__, 'register_ability_definition_classes' ) );
	}

	/**
	 * Register Product Recommendations ability definition classes.
	 *
	 * @param array $classes Ability definition class names.
	 * @return array
	 */
	public static function register_ability_definition_classes( array $classes ) {
		$classes[] = WC_PRL_Query_Engines_Ability::class;
		$classes[] = WC_PRL_Query_Deployments_Ability::class;
		$classes[] = WC_PRL_Describe_Components_Ability::class;

		return $classes;
	}
}

WC_PRL_Abilities::init();
