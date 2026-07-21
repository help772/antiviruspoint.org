<?php
/**
 * The Template for displaying upload field.
 *
 * @version 8.2.0
 * @package woocommerce-product-addons
 */

$field_name       = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key        = 'addon-' . sanitize_title( $field_name );
$adjust_price     = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$price            = ! empty( $addon['price'] ) ? $addon['price'] : '';
$price_type       = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$restriction_data = WC_Product_Addons_Helper::get_restriction_data( $addon );
$max_size         = size_format( wp_max_upload_size() );
$price_raw        = apply_filters( 'woocommerce_product_addons_price_raw', $adjust_price && $price ? $price : '', $addon );
$price_display    = $adjust_price && $price_raw ? WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) : '';
$value            = ! empty( $value ) ? $value : '';

if ( 'percentage_based' === $price_type ) {
	$price_display = $price_raw;
}

$addon_name = ! empty( $addon['name'] ) ? $addon['name'] : $field_name;

$file_input_label = sprintf(
	/* translators: %s: field name */
	esc_attr__( 'Choose file for %s', 'woocommerce-product-addons' ),
	esc_attr( $addon_name )
);

// Get allowed file types for accept attribute.
$accept_attr                = '';
$allowed_file_types_display = '';
if ( ! empty( $restriction_data['allowed_file_types'] ) && is_array( $restriction_data['allowed_file_types'] ) ) {
	// Build accept attribute.
	$accept_parts = array();

	foreach ( $restriction_data['allowed_file_types'] as $file_type ) {
		$file_type = trim( $file_type );
		if ( empty( $file_type ) ) {
			continue;
		}

		// Remove leading dot if present.
		$file_type = ltrim( $file_type, '.' );

		// Add extension format.
		$accept_parts[] = '.' . strtolower( $file_type );
	}

	if ( ! empty( $accept_parts ) ) {
		$accept_attr                = implode( ',', $accept_parts );
		$allowed_file_types_display = implode( ', ', $restriction_data['allowed_file_types'] );
	}
}

$helper_parts = array();

if ( ! empty( $allowed_file_types_display ) ) {
	// translators: %s comma-separated list of allowed file types.
	$helper_parts[] = sprintf( __( 'Allowed file types: %s', 'woocommerce-product-addons' ), $allowed_file_types_display );
}

if ( ! empty( $max_size ) ) {
	// translators: %s file size.
	$helper_parts[] = sprintf( __( '(max file size %s)', 'woocommerce-product-addons' ), $max_size );
}

$helper = implode( ' ', $helper_parts );

?>

<div class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>">
	<?php
	if ( ! empty( $value ) ) {
		$filename = basename( $value );
		?>
		<div class="wc-pao-addon-file-name">
			<?php
			$filelink = '<a href="' . esc_url( $value ) . '" target="_blank">' . esc_html( $filename ) . '</a>';
			// translators: %s existing filename.
			echo wp_kses_post( sprintf( __( 'Existing file: %s', 'woocommerce-product-addons' ), $filelink ) );
			?>
			<input
				type="hidden"
				name="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
				id="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>-value"
				value="<?php echo esc_attr( $value ); ?>" />
		</div>
		<?php
	}
	?>
	<input
		type="file"
		class="wc-pao-addon-file-upload input-text wc-pao-addon-field"
		data-raw-price="<?php echo esc_attr( $price_raw ); ?>"
		data-price="<?php echo esc_attr( $price_display ); ?>"
		data-price-type="<?php echo esc_attr( $price_type ); ?>"
		name="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		id="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		data-restrictions="<?php echo esc_attr( wp_json_encode( $restriction_data ) ); ?>"
		data-value="<?php echo esc_attr( $value ); ?>"
		<?php echo ! empty( $accept_attr ) ? 'accept="' . esc_attr( $accept_attr ) . '"' : ''; ?>
		aria-label="<?php echo esc_attr( $file_input_label ); ?>"
		<?php echo ! empty( $helper ) ? 'aria-describedby="addon-' . esc_attr( sanitize_title( $field_name ) ) . '-helper"' : ''; ?>
		/>
		<?php
		if ( ! empty( $helper ) ) {
			echo '<small id="addon-' . esc_attr( sanitize_title( $field_name ) ) . '-helper">';
			echo wp_kses_post( $helper );
			echo '</small>';
		}
		?>
</div>
