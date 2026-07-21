<?php
/**
 * Multiplex ads (matched content) layout customization
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 *
 * @var bool  $is_supported whether the ad unit is type is matched content.
 * @var array $settings     matched content settings.
 * @var array $types        matched content ui types.
 */

defined( 'WPINC' ) || die;

?>
<label id="advads-adsense-matched-content" class="label" style="<?php echo ! $is_supported ? 'display: none;' : ''; ?>">
	<?php esc_html_e( 'Layout', 'advanced-ads-responsive' ); ?>
</label>
<div id="advads-adsense-matched-content-controls" style="overflow:hidden; <?php echo ! $is_supported ? 'display: none;' : ''; ?>">
	<p>
		<label>
			<input id="matched-content-customize-switcher" type="checkbox" onchange="advads_toggle_box( this, '#matched-content-customize' );" <?php checked( $settings['customize_enabled'], 1 ); ?> value="1">
			<?php esc_html_e( 'Customize', 'advanced-ads-responsive' ); ?>
		</label>
		<a href="https://support.google.com/adsense/answer/7533385" target="_blank"><?php esc_html_e( 'manual', 'advanced-ads-responsive' ); ?></a>
	</p>
	<table id="matched-content-customize" <?php echo ! $settings['customize_enabled'] ? 'style="display:none;"' : ''; ?>>
		<tr>
			<td><?php esc_html_e( 'on desktop', 'advanced-ads-responsive' ); ?></td>
			<td><?php esc_html_e( 'Rows', 'advanced-ads-responsive' ); ?></td>
			<td><?php esc_html_e( 'Columns', 'advanced-ads-responsive' ); ?></td>
		</tr>
		<tr>
			<td>
				<select id="matched-content-ui-type">
					<?php foreach ( $types as $type ): ?>
						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $settings['ui_type'] ); ?>><?php echo esc_html( $type ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<td>
				<input type="number" min="1" max="99999" id="matched-content-rows-num" value="<?php echo esc_attr( $settings['rows_num'] ); ?>"/>
			</td>
			<td>
				<input type="number" min="1" max="99999" id="matched-content-columns-num" value="<?php echo esc_attr( $settings['columns_num'] ); ?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="3"><?php esc_html_e( 'on mobile', 'advanced-ads-responsive' ); ?>
			</td>
		<tr>
			<td>
				<select id="matched-content-ui-type-m">
					<?php foreach ( $types as $type ): ?>
						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $settings['ui_type_m'] ); ?>><?php echo esc_html( $type ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<td>
				<input type="number" min="1" max="99999" id="matched-content-rows-num-m" value="<?php echo esc_attr( $settings['rows_num_m'] ); ?>"/>
			</td>
			<td>
				<input type="number" min="1" max="99999" id="matched-content-columns-num-m" value="<?php echo esc_attr( $settings['columns_num_m'] ); ?>"/>
			</td>
		</tr>
	</table>
</div>
<hr/>
