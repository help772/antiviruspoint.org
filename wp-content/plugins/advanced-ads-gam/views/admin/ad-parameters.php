<?php
/**
 * Ad parameter meta box markup
 *
 * @package AdvancedAds\GAM
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   2.4.0
 *
 * @var Ad                       $ad              Ad instance.
 * @var string                   $ad_content      content of the ad.
 * @var array                    $ad_sizes_header headers for the Ad sizes table.
 * @var array                    $ad_sizes_rows   options for the Ad sizes table.
 * @var Advanced_Ads_Network_Gam $network         Ad network instance.
 */

use AdvancedAds\Abstracts\Ad;

$data           = $ad->get_data();
$has_fluid_size = false;
$update_age     = $network->get_list_update_age();

// Position left or right.
$is_floated   = false;
$ad_sizes     = Advanced_Ads_Gam_Ad::get_ad_unit_sizes( $ad );
$amp_ad_sizes = [];
$refresh      = absint( $data['gam-refresh'] ?? 0 );

if ( isset( $data['amp-ad-sizes'] ) ) {
	$amp_ad_sizes = $data['amp-ad-sizes'];
} elseif ( ! isset( $data ) || ! isset( $data['amp-has-sizes'] ) ) {
	$amp_ad_sizes = is_array( $ad_sizes ) ? array_keys( $ad_sizes ) : [];
}

if ( isset( $data ) ) {
	if ( isset( $data['ad-sizes'] ) && isset( $data['ad-sizes'][0]['sizes'] ) ) {
		$has_fluid_size = in_array( 'fluid', $data['ad-sizes'][0]['sizes'], true );
	}
	$is_floated = isset( $data['position'] ) && in_array( $data['position'], [ 'left', 'right' ], true );
}

?>
<input type="hidden" name="advanced_ad[content]" value="<?php echo esc_attr( $ad_content ); ?>" />
<?php $network->print_external_ads_list(); ?>
<script type="text/javascript">
	var AAGAM = new AdvancedAdsNetworkGam( 'gam' );
	AdvancedAdsAdmin.AdImporter.setup( AAGAM );
</script>
<div class="advads-option-list">
	<span class="label"><?php esc_html_e( 'Ad sizes', 'advanced-ads-gam' ); ?></span>
	<div id="advads-gam-ad-sizes" class="advads-ad-parameters-option-list">
		<div class="advads-gam-ad-sizes-table-container"><span class="advads-loader"></span></div>
	<?php require AA_GAM_ABSPATH . 'admin/views/ad-sizes/responsive-sizes.php'; ?>
	</div>
	<p class="advads-gam-ad-sizes-notice-missing-sizes hidden"><?php esc_html_e( 'This ad unit does not have any ad sizes', 'advanced-ads-gam' ); ?></p>
	<?php require AA_GAM_ABSPATH . 'admin/views/ad-sizes/table.php'; ?>
	<?php require AA_GAM_ABSPATH . 'admin/views/ad-sizes/table-row.php'; ?>
	<?php require AA_GAM_ABSPATH . 'admin/views/ad-sizes/table-header.php'; ?>
	<?php require AA_GAM_ABSPATH . 'admin/views/ad-sizes/table-cell.php'; ?>
	<?php require AA_GAM_ABSPATH . 'admin/views/ad-sizes/table-footer.php'; ?>
	<?php require AA_GAM_ABSPATH . 'admin/views/ad-sizes/amp.php'; ?>

	<script>
		advads_gam_stored_ad_sizes_json = <?php echo Advanced_Ads_Gam_Ad::get_ad_sizes_json_string( $ad ); // phpcs:ignore ?>;
		advads_gam_amp =
		<?php
		echo wp_json_encode(
			[
				'sizes'  => $amp_ad_sizes,
				'hasAMP' => Advanced_Ads_Checks::active_amp_plugin(),
			]
		);
		?>
		;
	</script>
	<script type="text/html" id="tmpl-gam-unit-not-in-list">
		<div id="advads-gam-adunit-not-found" data-id="{{data.unitId}}" class="card advads-notice-block advads-card-full-width advads-error">
			<p><?php esc_html_e( 'The selected ad unit is not in your ad list.', 'advanced-ads-gam' ); ?></p>
			<p>
				<strong><?php esc_html_e( 'Ad unit name', 'advanced-ads-gam' ); ?>:</strong>&nbsp;<code>{{{data.unitName}}}</code>
			</p>
		</div>
	</script>
	<hr>
	<span class="label"><?php esc_html_e( 'Auto-refresh', 'advanced-ads-gam' ); ?></span>
	<div id="advads-gam-ad-auto-refresh">
		<label>
			<input type="number" min="0" name="advanced_ad[gam-refresh]" value="<?php echo esc_attr( $refresh ); ?>" />
			<?php esc_html_e( 'seconds', 'advanced-ads-gam' ); ?>
		</label>
		<?php if ( 0 !== $refresh && 30 > $refresh ) : ?>
			<p class="advads-notice-inline advads-error"><?php esc_html_e( 'Google suggests a minimum value of 30 seconds.', 'advanced-ads-gam' ); ?></p>
		<?php endif; ?>
		<p class="description">
			<?php
			printf(
				/* translators: 1: anchor tag that links to Google's docs, 2: closing anchor tag */
				esc_html__(
					'Important: To comply with Google policy and enable your inventory to compete on Ad Exchange, %1$syou must declare which portions of your inventory refresh%2$s.',
					'advanced-ads-gam'
				),
				'<a href="https://support.google.com/admanager/answer/6286179" target="_blank">',
				'</a>'
			)
			?>
		</p>
	</div>
</div>
<?php if ( $has_fluid_size && $is_floated ) : ?>
<p class="advads-error-message clear"><?php esc_html_e( 'Fluid sizes cannot be aligned left or right. Please choose another option for Position.', 'advanced-ads-gam' ); ?></p>
<?php endif; ?>
<?php require_once AA_GAM_ABSPATH . 'admin/views/key-value.php'; ?>
<hr/>
