<?php
/**
 * The Template for displaying end of field.
 *
 * @version 8.0.0
 * @package woocommerce-product-addons
 */

/**
 * Action 'wc_product_addon_end'.
 *
 * @since 2.0
 * @var array $addon Information about the addon.
 */
do_action( 'wc_product_addon_end', $addon );
$addon_type    = ! empty( $addon['type'] ) ? $addon['type'] : '';
$addon_display = ! empty( $addon['display'] ) ? $addon['display'] : '';
$is_fieldset   = isset( $is_fieldset ) ? $is_fieldset : false;
?>
<?php if ( $is_fieldset ) : ?>
	</fieldset>
<?php endif; ?>
</div>

