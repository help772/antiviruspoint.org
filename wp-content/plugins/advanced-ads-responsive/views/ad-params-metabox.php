<?php
/**
 * AMP Ad parameters metabox markup
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 *
 * @var array  $attributes AMP attributes.
 * @var string $fallback  fallback text.
 */

defined( 'WPINC' ) || die;

?>
<label class="label"><?php esc_html_e( 'Attributes', 'advanced-ads-responsive' ); ?></label>
<div style="float:none; overflow:auto;">
	<table id="advads-amp-props" class="widefat">
		<thead>
		<tr>
			<th><?php esc_html_e( 'Name', 'advanced-ads-responsive' ); ?></th>
			<th><?php esc_html_e( 'Value', 'advanced-ads-responsive' ); ?></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $attributes as $attribute => $data ) : ?>
			<tr class="advads-amp-prop-row">
				<td><input class="large-text" type="text" name="advanced_ad[amp][attributes][]" value="<?php echo esc_attr( $attribute ); ?>"/></td>
				<td><textarea class="large-text" name="advanced_ad[amp][data][]"><?php echo esc_textarea( $data ); ?></textarea></td>
				<td>
					<button type="button" class="advads-amp-delete-prop button">x</button>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="3">
				<button type="button" class="button button-primary" id="advads-amp-add-prop">
					<i class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></i>&nbsp;<?php esc_html_e( 'Add attribute', 'advanced-ads-responsive' ); ?>
				</button>
		</tr>
		</tbody>
	</table>
</div>
<hr/>

<label class="label"><?php esc_html_e( 'Fallback', 'advanced-ads-responsive' ); ?></label>
<div style="float:none; overflow:auto;">
	<textarea class="large-text" name="advanced_ad[amp][fallback]"><?php echo esc_textarea( $fallback ); ?></textarea>
	<p class="description"><?php esc_html_e( ' If supported by the ad network, this text is shown if no ad is available for the ad slot', 'advanced-ads-responsive' ); ?></p>
</div>
<hr/>
