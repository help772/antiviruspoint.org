<?php
/**
 * Handles the version of the different plugin components in the database.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Database;

/**
 * Class Versions.
 */
class Versions {

	/**
	 * Gets the installed version of the plugin or for the specified component.
	 *
	 * @since 1.9.0
	 *
	 * @param string $component Optional. The plugin component. Default empty.
	 * @return string
	 */
	public static function get_version( $component = '' ) {
		$option = get_option( self::get_option_key( $component ), '' );

		// Backward compatibility.
		if ( ! $option && ( ! $component || 'db' === $component ) ) {
			$option = get_option( 'woocommerce_software_version', '' );
		}

		return $option;
	}

	/**
	 * Updates the version of the plugin or the specified component.
	 *
	 * @since 1.9.0
	 *
	 * @param string $version   The new version.
	 * @param string $component Optional. The plugin component. Default empty.
	 */
	public static function update_version( $version, $component = '' ) {
		update_option( self::get_option_key( $component ), $version );
	}

	/**
	 * Gets the option key that stores the version of the plugin or the specified component.
	 *
	 * @since 1.9.0
	 *
	 * @param string $component Optional. The plugin component. Default empty.
	 * @return string
	 */
	protected static function get_option_key( $component = '' ) {
		$suffix = ( $component ? "_$component" : '' ) . '_version';

		return "wc_software_addon{$suffix}";
	}
}
