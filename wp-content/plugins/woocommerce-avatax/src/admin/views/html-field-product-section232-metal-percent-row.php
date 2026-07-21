<?php
/**
 * Single Section 232 metal row (value, unit, country).
 *
 * @var array        $row
 * @var int|string   $idx Row index.
 * @var array        $material_units
 * @var array        $country_options
 *
 * @package WooCommerceAvaTax
 * @codeCoverageIgnore
 */
// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) || exit;

// @codeCoverageIgnoreStart
$row = wp_parse_args(
	isset( $row ) ? $row : array(),
	array(
		'value'   => '',
		'unit'    => '',
		'country' => '',
	)
);
?>
<div class="wc-avatax-metal-row" data-row-index="<?php echo esc_attr( (string) $idx ); ?>">
	<div class="wc-avatax-metal-row-inner">
		<div class="wc-avatax-metal-field wc-avatax-metal-field-value">
			<label class="wc-avatax-metal-label">
				<span class="wc-avatax-metal-label-text"><?php esc_html_e( 'Value (%)', 'woocommerce-avatax' ); ?></span>
				<input
					type="text"
					class="wc-avatax-metal-value"
					name="_wc_avatax_section232_value[]"
					value="<?php echo esc_attr( $row['value'] ); ?>"
					placeholder="<?php esc_attr_e( 'e.g., 0.25', 'woocommerce-avatax' ); ?>"
					inputmode="decimal"
					autocomplete="off"
				/>
				<span class="wc-avatax-metal-field-error" role="alert"></span>
			</label>
		</div>
		<div class="wc-avatax-metal-field wc-avatax-metal-field-unit">
			<label class="wc-avatax-metal-label">
				<span class="wc-avatax-metal-label-text"><?php esc_html_e( 'Material Unit', 'woocommerce-avatax' ); ?></span>
				<select class="wc-avatax-metal-unit" name="_wc_avatax_section232_unit[]">
					<option value=""><?php esc_html_e( 'Select material', 'woocommerce-avatax' ); ?></option>
					<?php foreach ( $material_units as $slug => $label ) : ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $row['unit'], $slug ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="wc-avatax-metal-field-error" role="alert"></span>
			</label>
		</div>
		<div class="wc-avatax-metal-field wc-avatax-metal-field-country">
			<label class="wc-avatax-metal-label">
				<span class="wc-avatax-metal-label-text"><?php esc_html_e( 'Material Country of Origin', 'woocommerce-avatax' ); ?></span>
				<select class="wc-avatax-metal-country" name="_wc_avatax_section232_country[]">
					<option value=""><?php esc_html_e( 'Select country', 'woocommerce-avatax' ); ?></option>
					<?php foreach ( $country_options as $code => $clabel ) : ?>
						<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $row['country'], $code ); ?>><?php echo esc_html( $clabel ); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="wc-avatax-metal-field-error" role="alert"></span>
			</label>
		</div>
		<div class="wc-avatax-metal-field wc-avatax-metal-field-actions">
			<span class="wc-avatax-metal-actions-label screen-reader-text"><?php esc_html_e( 'Row actions', 'woocommerce-avatax' ); ?></span>
			<button type="button" class="button wc-avatax-metal-remove" aria-label="<?php esc_attr_e( 'Remove row', 'woocommerce-avatax' ); ?>">&times;</button>
		</div>
	</div>
</div>
<?php
// @codeCoverageIgnoreEnd
