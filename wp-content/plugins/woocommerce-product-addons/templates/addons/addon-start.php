<?php
/**
 * The Template for displaying start of field.
 *
 * @version 8.0.0
 * @package woocommerce-product-addons
 */

global $product;

$price_display          = '';
$title_format           = ! empty( $addon['title_format'] ) ? $addon['title_format'] : '';
$addon_type             = ! empty( $addon['type'] ) ? $addon['type'] : '';
$addon_display          = ! empty( $addon['display'] ) ? $addon['display'] : '';
$addon_price            = ! empty( $addon['price'] ) ? $addon['price'] : '';
$addon_price_type       = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$adjust_price           = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$required               = ! empty( $addon['required'] ) ? $addon['required'] : '';
$has_per_person_pricing = ( isset( $addon['wc_booking_person_qty_multiplier'] ) && 1 === $addon['wc_booking_person_qty_multiplier'] ) ? true : false;
$has_per_block_pricing  = ( ( isset( $addon['wc_booking_block_qty_multiplier'] ) && 1 === $addon['wc_booking_block_qty_multiplier'] ) || ( isset( $addon['wc_accommodation_booking_block_qty_multiplier'] ) && 1 === $addon['wc_accommodation_booking_block_qty_multiplier'] ) ) ? true : false;
$product_title          = $product->get_name();
$is_taxable             = $product->is_taxable();
$is_fieldset            = isset( $is_fieldset ) ? $is_fieldset : false;

/*
 * 'woocommerce_addons_product_page_container_classes' filter.
 *
 * Use this filter to add additional classes to the container add-on element.
 *
 * @param array      $classes
 * @param array      $addon
 * @param WC_Product $product
 */
$additional_classes = apply_filters( 'woocommerce_addons_product_page_container_classes', array(), $addon, $product );

if ( 'checkbox' !== $addon_type && 'multiple_choice' !== $addon_type && 'custom_price' !== $addon_type ) {
	$price_prefix = 0 < $addon_price ? '+' : '';
	$price_type   = $addon_price_type;
	$price_raw    = apply_filters( 'woocommerce_product_addons_price_raw', $addon_price, $addon );

	if ( 'percentage_based' === $price_type ) {
		$add_price_to_value = apply_filters( 'woocommerce_addons_add_product_price_to_value', true, $product );

		$price_display = $add_price_to_value ? apply_filters(
			'woocommerce_product_addons_price',
			$adjust_price && $price_raw ? '(' . $price_prefix . $price_raw . '%)' : '',
			$addon,
			0,
			$addon_type
		) : '';
	} else {
		$add_price_to_value = apply_filters( 'woocommerce_addons_add_product_price_to_value', true, $product );

		$price_display = $add_price_to_value ? apply_filters(
			'woocommerce_product_addons_price',
			$adjust_price && $price_raw ? '(' . $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) ) . ')' : '',
			$addon,
			0,
			$addon_type
		) : '';
	}
}

?>

<div id="wc-pao-addon-<?php echo esc_attr( sanitize_title( $addon['field_name'] ) ); ?>" class="wc-pao-addon-container <?php echo $required ? 'wc-pao-required-addon' : ''; ?> wc-pao-addon wc-pao-addon-<?php echo esc_attr( sanitize_title( $addon['field_name'] ) ); ?> <?php echo isset( $addon['id'] ) ? 'wc-pao-addon-id-' . esc_attr( sanitize_title( $addon['id'] ) ) : ''; ?> <?php echo is_array( $additional_classes ) && ! empty( $additional_classes ) ? esc_attr( implode( ' ', $additional_classes ) ) : ''; ?>" data-product-name="<?php echo esc_attr( $product_title ); ?>" data-product-tax-status="<?php echo $is_taxable ? 'taxable' : 'none'; ?>" <?php echo $is_fieldset ? ' data-fieldset' : ''; ?>>
	<?php if ( $is_fieldset ) : ?>
		<fieldset class="wc-pao-fieldset"<?php echo $display_description ? ' aria-describedby="wc-pao-description-' . esc_attr( wptexturize( $addon['field_name'] ) ) . '"' : ''; ?><?php echo $required ? ' aria-required="true"' : ''; ?>>
	<?php endif; ?>

	<?php do_action( 'wc_product_addon_start', $addon ); ?>

	<?php
	if ( $name ) {
		if ( 'heading' === $addon_type ) {
			?>
			<h2 class="wc-pao-addon-heading"><?php echo wp_kses_post( wptexturize( $name ) ); ?></h2>
			<?php
		} else {
			switch ( $title_format ) {
				case 'heading':
					?>
					<?php if ( $is_fieldset ) : ?>
						<legend class="wc-pao-legend">
							<h2 class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>"><?php echo wp_kses_post( wptexturize( $name ) ); ?> <?php echo ! empty( $price_display ) ? '<span class="wc-pao-addon-price">' . wp_kses_post( $price_display ) . '</span>' : ''; ?> <?php echo $required ? '<em class="required" aria-hidden="true">*</em>' : ''; ?></h2>
						</legend>
					<?php else : ?>
						<label for="addon-<?php echo esc_attr( wptexturize( $addon['field_name'] ) ); ?>" class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>">
							<h2 class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>"><?php echo wp_kses_post( wptexturize( $name ) ); ?> <?php echo ! empty( $price_display ) ? '<span class="wc-pao-addon-price">' . wp_kses_post( $price_display ) . '</span>' : ''; ?> <?php echo $required ? '<em class="required" aria-hidden="true">*</em>' : ''; ?></h2>
						</label>
					<?php endif; ?>
					<?php
					break;
				case 'hide':
					?>
					<?php if ( $is_fieldset ) : ?>
						<legend class="wc-pao-addon-name screen-reader-text" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>">
							<?php echo wp_kses_post( wptexturize( $name ) ); ?>
						</legend>
					<?php else : ?>
						<label for="addon-<?php echo esc_attr( wptexturize( $addon['field_name'] ) ); ?>" class="wc-pao-addon-name screen-reader-text" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>">
							<?php echo wp_kses_post( wptexturize( $name ) ); ?>
						</label>
					<?php endif; ?>
					<?php
					break;
				case 'label':
				default:
					?>
					<?php if ( $is_fieldset ) : ?>
						<legend class="wc-pao-addon-name wc-pao-legend" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>">
							<?php echo wp_kses_post( wptexturize( $name ) ); ?> <?php echo ! empty( $price_display ) ? '<span class="wc-pao-addon-price">' . wp_kses_post( $price_display ) . '</span>' : ''; ?> <?php echo $required ? '<em class="required" aria-hidden="true">*</em>' : ''; ?>
						</legend>
					<?php else : ?>
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<label for="addon-<?php echo esc_attr( wptexturize( $addon['field_name'] ) ); ?>" class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>">
							<?php echo wp_kses_post( wptexturize( $name ) ); ?>
							<?php echo ! empty( $price_display ) ? '<span class="wc-pao-addon-price">' . wp_kses_post( $price_display ) . '</span>' : ''; ?>
							<?php echo $required ? '<em class="required" aria-hidden="true">*</em>' : ''; ?>
						</label>
					<?php endif; ?>
					<?php
					break;
			}
		}
	}
	?>
	<?php
	if ( $display_description ) {
		?>
		<?php echo '<div class="wc-pao-addon-description" id="wc-pao-description-' . esc_attr( sanitize_title( $addon['field_name'] ) ) . '">' . wp_kses_post( wpautop( wptexturize( $description ) ) ) . '</div>'; ?>
	<?php } ?>

	<?php do_action( 'wc_product_addon_options', $addon ); ?>
