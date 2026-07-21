<?php
/**
 * The Template for displaying custom price field.
 *
 * @version 8.1.0
 * @package woocommerce-product-addons
 */

/**
 * Injections:
 *
 * @var array $addon
 * @var mixed $value
 */

$field_name       = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key        = 'addon-' . sanitize_title( $field_name );
$has_restrictions = ! empty( $addon['restrictions'] );
$min              = $addon['min'] > 0 ? $addon['min'] : 0;
$restriction_data = WC_Product_Addons_Helper::get_restriction_data( $addon );
$value            = isset( $value ) ? $value : '';
$description      = ! empty( $addon['description'] ) ? $addon['description'] : '';

?>

<div class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>">
	<input
		type="text"
		class="input-text wc-pao-addon-field wc-pao-addon-custom-price"
		name="<?php echo esc_attr( $addon_key ); ?>"
		id="<?php echo esc_attr( $addon_key ); ?>"
		data-price-type="flat_fee"
		data-restrictions="<?php echo esc_attr( wp_json_encode( $restriction_data ) ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		<?php echo WC_Product_Addons_Helper::is_addon_required( $addon ) ? 'required' : ''; ?>
		<?php echo $description ? 'aria-describedby="wc-pao-description-' . esc_attr( wptexturize( $addon['field_name'] ) ) . '"' : ''; ?>
	/>
</div>
