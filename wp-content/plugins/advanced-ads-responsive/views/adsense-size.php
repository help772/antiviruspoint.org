<?php
/**
 * AMP related fields on AdSense ads
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 *
 * @var bool   $is_supported whether AMP is supported for the AdSense.
 * @var string $option_name  name attribute of form inputs.
 * @var string $layout       AdSense layout.
 * @var int    $width        ad width.
 * @var int    $height       ad height.
 * @var int    $fixed_height ad fixed height if set.
 */

use AdvancedAds\AMP\admin\Amp;

defined( 'WPINC' ) || die;

?>
<label class="label" <?php echo $is_supported ? 'style="display: none;"' : ''; ?>>AMP</label>
<div id="advads-adsense-responsive-amp-inputs" style="overflow:hidden; <?php echo $is_supported ? 'display: none;' : ''; ?>">
	<ul <?php echo ! Amp::has_amp_plugin() ? 'style="display: none;"' : ''; ?>>
		<li>
			<label>
				<input type="radio" name="<?php echo esc_attr( $option_name ); ?>[layout]" value="default" <?php checked( $layout, 'default' ); ?> />
				<?php esc_html_e( 'automatically convert to AMP', 'advanced-ads-responsive' ); ?>
			</label>
			<a class="advads-manual-icon" href="<?php echo 'https://wpadvancedads.com/manual/amp-adsense-wordpress/#utm_source=advanced-ads&utm_medium=link&utm_campaign=settings-adsense-amp'; ?>" target="_blank">
				<span class="dashicons dashicons-welcome-learn-more"></span>
			</a>
		</li>
		<li>
			<label><input type="radio" name="<?php echo esc_attr( $option_name ); ?>[layout]" value="responsive" <?php checked( $layout, 'responsive' ); ?>/>
				<?php
				echo wp_kses(
					sprintf(
					// translators: 1 width number input, 2 height number input.
						__( 'use dynamic size with ratio %1$s x %2$s', 'advanced-ads-responsive' ),
						'</label><label><input type="number" min="1" max="99999" name="' . esc_attr( $option_name ) . '[width]" value="' . esc_attr( $width ) . '"/>',
						'</label><label><input type="number" min="1" max="99999" name="' . esc_attr( $option_name ) . '[height]" value="' . esc_attr( $height ) . '"/>'
					),
					[
						'label' => [],
						'input' => [
							'type'  => [],
							'min'   => [],
							'max'   => [],
							'name'  => [],
							'value' => [],
						],
					]
				);
				?>
			</label>
		</li>
		<li>
			<label><input type="radio" name="<?php echo esc_attr( $option_name ); ?>[layout]" value="fixed_height" <?php checked( $layout, 'fixed_height' ); ?>/>
				<?php
				echo wp_kses(
					sprintf(
					// translators: number input.
						__( 'use responsive width and static height of %s px', 'advanced-ads-responsive' ),
						'</label><label><input type="number" min="1" max="99999" name="' . esc_attr( $option_name ) . '[fixed_height]" value="' . esc_attr( $fixed_height ) . '"/>'
					),
					[
						'label' => [],
						'input' => [
							'type'  => [],
							'min'   => [],
							'max'   => [],
							'name'  => [],
							'value' => [],
						],
					]
				);
				?>
			</label>
		</li>
		<li>
			<label><input type="radio" name="<?php echo esc_attr( $option_name ); ?>[layout]" value="hide" <?php checked( $layout, 'hide' ); ?> />
				<?php esc_html_e( 'hide', 'advanced-ads-responsive' ); ?>
			</label>
		</li>
	</ul>
	<?php echo ! Amp::has_amp_plugin() ? esc_html__( 'no AMP plugin found', 'advanced-ads-responsive' ) : ''; ?>
</div>
<hr/>
