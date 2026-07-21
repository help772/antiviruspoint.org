<?php
/**
 * Compatibility utilities.
 *
 * @since 1.8.0
 */

namespace Themesquad\WC_Software_Addon\Utilities;

/**
 * Class Compat_Utils.
 */
class Compat_Utils {

	/**
	 * Gets whether the custom order tables are enabled or not.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public static function is_custom_order_tables_enabled() {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
			return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}

		return false;
	}

	/**
	 * Gets the screen name of orders page in wp-admin.
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public static function get_order_admin_screen() {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
			return \Automattic\WooCommerce\Utilities\OrderUtil::get_order_admin_screen();
		}

		return 'shop_order';
	}
}
