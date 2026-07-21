<?php
/**
 * Deprecated functions.
 *
 * This file is autoloaded via composer.json.
 *
 * @since 1.9.0
 */

/**
 * WooCommerce fallback notice.
 *
 * @since 1.7.13
 * @deprecated 1.9.0
 */
function woocommerce_software_add_on_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Software Add-on requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-software-add-on' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * Runs various functions when the plugin first activates.
 *
 * @since 1.0
 * @deprecated 1.9.0
 */
function woocommerce_software_add_on_activation() {
	\Themesquad\WC_Software_Addon\Database\Installer::install();
}
