<?php
/**
 * AdSense ad manual CSS markup
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 *
 * @var array $extra_params   extra parameters for teh AdSense ad metabox.
 * @var bool  $is_responsive  whether the ad unit is responsive.
 * @var bool  $use_manual_css manual CSS in use.
 */

defined( 'WPINC' ) || die;

$extra_params['default_hidden'] = $extra_params['default_hidden'] ?? false;

$style = '';
if ( ! $is_responsive || ! $use_manual_css ) {
	$style = 'style="display: none;"';
}
?>
<div id="gadsense-css-div" <?php echo $style; // phpcs:ignore ?>>
	<p>
		<?php
		/* translators: %s: tutorial URL */
		echo wp_kses_post( sprintf( __( 'Need help? Take a look at the <a href="%s" target="_blank">tutorial</a>.', 'advanced-ads-responsive' ), 'http://wpadvancedads.com/adsense-responsive-custom-sizes/' ) );
		?>
	</p>
	<table class="widefat">
		<thead>
		<tr>
			<th><?php esc_html_e( 'min. browser width', 'advanced-ads-responsive' ); ?></th>
			<th><?php esc_html_e( 'ad width', 'advanced-ads-responsive' ); ?></th>
			<th><?php esc_html_e( 'ad height', 'advanced-ads-responsive' ); ?></th>
			<th colspan="2"><?php esc_html_e( 'hidden', 'advanced-ads-responsive' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><?php esc_html_e( 'Default', 'advanced-ads-responsive' ); ?></td>
			<td><input type="number" min="0" <?php if ( isset( $extra_params['default_hidden'] ) && $extra_params['default_hidden'] ) {
					echo 'disabled="disabled"';
				} ?> value="<?php echo $extra_params['default_width']; ?>" name="default-width" style="width: 5em;"></td>
			<td><input type="number" min="0" <?php if ( isset( $extra_params['default_hidden'] ) && $extra_params['default_hidden'] ) {
					echo 'disabled="disabled"';
				} ?> value="<?php echo $extra_params['default_height']; ?>" name="default-height" style="width: 5em;"></td>
			<td colspan="2"><input type="checkbox" id="default-hidden" value="1" title="<?php esc_attr_e( 'Hide for this size ?', 'advanced-ads-responsive' ); ?>" <?php checked( $extra_params['default_hidden'] ); ?> /></td>
		</tr>
		<tr class="alt">
			<td><input type="number" min="0" value="" id="new-ad-min-width" style="width: 5em;"></td>
			<td><input type="number" min="0" value="" id="new-ad-width" style="width: 5em;"></td>
			<td><input type="number" min="0" value="" id="new-ad-height" style="width: 5em;"></td>
			<td><input type="checkbox" value="1" id="new-ad-hidden" title="<?php esc_attr_e( 'Hide for this size ?', 'advanced-ads-responsive' ); ?>"/></td>
			<td>
				<button class="button button-primary" id="new-rule-btn">
					<i class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></i>&nbsp;<?php esc_html_e( 'Add rule', 'advanced-ads-responsive' ); ?>
				</button>
			</td>
		</tr>
		</tbody>
	</table>
	<table class="widefat">
		<tbody id="gadsense-css-tbody">
		<?php if ( ! empty( $extra_params['at_media'] ) ) : ?>
			<?php foreach ( $extra_params['at_media'] as $row ) : ?>
				<tr data-minwidth="<?php echo esc_attr( $row['minw'] ); ?>">
					<td><b><span class="row-minw"><?php echo $row['minw']; ?></span></b>&nbsp;px</td>
					<?php if ( $row['hidden'] ) : ?>
						<td colspan="2">
							<?php esc_html_e( 'Not displayed', 'advanced-ads-responsive' ); ?>
							<span class="row-w" style="display:none"><?php echo $row['w']; ?></span>
							<span class="row-h" style="display:none"><?php echo $row['h']; ?></span>
							<input type="hidden" class="row-hidden" value="1"/>
						</td>
					<?php else : ?>
						<td><b><span class="row-w"><?php echo $row['w']; ?></span></b>&nbsp;px</td>
						<td><b><span class="row-h"><?php echo $row['h']; ?></span></b>&nbsp;px<input type="hidden" class="row-hidden" value="0"/></td>
					<?php endif; ?>
					<td colspan="2">
						<button class="button button-secondary row-remove">
							<i class="dashicons dashicons-dismiss" title="<?php esc_attr_e( 'Remove this rule', 'advanced-ads-responsive' ); ?>" style="vertical-align: middle;"></i>
							&nbsp;<?php esc_html_e( 'remove', 'advanced-ads-responsive' ); ?>
						</button>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div><!-- #gadsense-css-div-->
