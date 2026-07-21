<?php
/**
 * Section 232 metal percent fields (product advanced options).
 *
 * @var array $section232_metal_rows Rows: value, unit, country.
 * @var array $material_units        slug => label.
 * @var array $country_options       code => "CODE - Name" label.
 * @var bool  $section232_initially_hidden When true, panel starts hidden (virtual/downloadable); JS reveals on toggle.
 *
 * @package WooCommerceAvaTax
 * @codeCoverageIgnore
 */
// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) || exit;


$section232_wrap_style = 'margin-bottom: 0 !important;';
if ( ! empty( $section232_initially_hidden ) ) {
	$section232_wrap_style .= ' display: none;';
}
?>
<div class="form-field _wc_avatax_section232_metal_field" style="<?php echo esc_attr( $section232_wrap_style ); ?>">
	<label id="wc_avatax_section232_heading"><?php esc_html_e( 'Section232MetalPercent', 'woocommerce-avatax' ); ?></label>
	<input type="hidden" name="_wc_avatax_section232_save" value="1" />
	<div class="wc-avatax-section232-fields" role="group" aria-labelledby="wc_avatax_section232_heading">
		<div class="wc-avatax-metal-rows" id="wc_avatax_metal_rows">
			<?php
			if ( empty( $section232_metal_rows ) ) {
				$section232_metal_rows = array(
					array(
						'value'   => '',
						'unit'    => '',
						'country' => '',
					),
				);
			}
			foreach ( $section232_metal_rows as $idx => $row ) {
				include __DIR__ . '/html-field-product-section232-metal-percent-row.php';
			}
			?>
		</div>
		<p class="wc-avatax-metal-actions">
			<button type="button" class="button" id="wc_avatax_add_metal_row"><?php esc_html_e( '+ Add Metal Percent', 'woocommerce-avatax' ); ?></button>
		</p>
		<p class="wc-avatax-metal-global-error" id="wc_avatax_metal_global_error" role="alert" hidden></p>
	</div>
</div>
<?php
// @codeCoverageIgnoreEnd
