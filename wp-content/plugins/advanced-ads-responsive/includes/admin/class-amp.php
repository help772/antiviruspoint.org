<?php
/**
 * AMP backend integration
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP\admin;

use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Utilities\Conditional;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * AMP backend
 */
class Amp implements Integration_Interface {
	/**
	 * Hooks into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ], 10 );
		add_action( 'advanced-ads-ad-pre-save', [ $this, 'save_ad_options' ], 10, 2 );
		add_filter( 'advanced-ads-ad-notices', [ $this, 'ad_notices' ], 10, 2 );
		add_action( 'advanced-ads-gadsense-extra-ad-param', [ $this, 'extra_template' ], 10, 3 );
	}

	/**
	 * Shows AMP related fields/inputs in AdSense ad param meta box.
	 *
	 * @param array     $extra_params array of extra parameters.
	 * @param \stdClass $content      ad content.
	 * @param Ad        $ad           ad object.
	 */
	public function extra_template( $extra_params, $content, $ad = null ) {
		if ( ! $ad ) {
			return;
		}

		$is_supported = \AdvancedAds\AMP\frontend\Amp::is_supported_adsense_type( $content );
		$options      = $ad->get_prop( 'amp' ) ?? [];
		$option_name  = 'advanced_ad[amp]';
		$width        = $ad->get_width();
		$height       = $ad->get_height();
		$layout       = $options['layout'] ?? 'default';
		$width        = ! empty( $options['width'] ) ? absint( $options['width'] ) : ( $width ?: 300 );
		$height       = ! empty( $options['height'] ) ? absint( $options['height'] ) : ( $height ?: 250 );
		$fixed_height = ! empty( $options['fixed_height'] ) ? absint( $options['fixed_height'] ) : ( $height ?: 250 );

		include AA_AMP_ABSPATH . 'views/adsense-size.php';
	}

	/**
	 * Show warning if a non-AMP compatible option is selected.
	 *
	 * @param array $notices Notices.
	 * @param array $box     meta box information.
	 *
	 * @return array
	 */
	public function ad_notices( $notices, $box ) {
		if ( self::has_amp_plugin() ) {
			switch ( $box['id'] ) {
				case 'ad-parameters-box':
					// Add warning if this is a non-AMP compatible AdSense ad.
					// Hidden by default and made visible with JS.
					$notices[] = [
						'text'  => __( 'This ad type is not supported on AMP pages', 'advanced-ads-responsive' ),
						'class' => 'advanced-ads-adsense-amp-warning advads-notice-inline advads-idea hidden',
					];
					break;
			}
		}

		return $notices;
	}

	/**
	 * Enqueue admin-specific JavaScript.
	 */
	public function enqueue_admin_scripts() {
		if ( ! Conditional::is_screen_advanced_ads() ) {
			return;
		}

		wp_enqueue_script( ADVADS_SLUG . '-amp-admin', AA_AMP_BASE_URL . 'assets/js/amp-admin.js', [ 'jquery' ], AAR_VERSION, true );
		wp_localize_script(
			ADVADS_SLUG . '-amp-admin',
			'advanced_ads_amp_admin',
			[
				'supported_adsense_types' => \AdvancedAds\AMP\Amp::$supported_adsense_types,
			]
		);
	}

	/**
	 * Callback to display the AMP display condition
	 *
	 * @param array  $options   options of the conditions.
	 * @param int    $index     index of the row.
	 * @param string $form_name format of the name attribute for the row.
	 *
	 * @return void
	 */
	public static function metabox_amp( $options, $index = 0, $form_name = '' ) {
		if ( empty( $options['type'] ) ) {
			return;
		}

		$type_options = \Advanced_Ads_Display_Conditions::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		$name     = \Advanced_Ads_Display_Conditions::get_form_name_with_index( $form_name, $index );
		$operator = $options['operator'] ?? 'is';

		?>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>[type]" value="<?php echo esc_attr( $options['type'] ); ?>"/>
		<select name="<?php echo esc_attr( $name ); ?>[operator]">
			<option value="is" <?php selected( 'is', $operator ); ?>><?php esc_html_e( 'is', 'advanced-ads-responsive' ); ?></option>
			<option value="is_not" <?php selected( 'is_not', $operator ); ?>><?php esc_html_e( 'is not', 'advanced-ads-responsive' ); ?></option>
		</select>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>[value]" value="1"/>
		<p class="description"><?php echo esc_html( $type_options[ $options['type'] ]['description'] ); ?></p>
		<?php
	}

	/**
	 * Check if an amp plugin is enabled.
	 *
	 * @return bool
	 */
	public static function has_amp_plugin() {
		return function_exists( 'is_amp_endpoint' ) || function_exists( 'is_wp_amp' ) || function_exists( 'ampforwp_is_amp_endpoint' );
	}

	/**
	 * Edit ad options (props) before saving
	 *
	 * @param Ad    $ad        the ad.
	 * @param array $post_data values from $_POST['advanced_ad'].
	 *
	 * @return void
	 */
	public function save_ad_options( $ad, $post_data ) {
		if ( ! $ad->is_type( [ 'amp', 'adsense' ] ) ) {
			return;
		}

		$attributes = isset( $post_data['amp']['attributes'] ) ? array_values( $post_data['amp']['attributes'] ) : [];
		$data       = isset( $post_data['amp']['data'] ) ? array_values( $post_data['amp']['data'] ) : [];
		$amp_props  = $ad->get_prop( 'amp', 'edit' ) ?? [];

		unset( $amp_props['attributes'], $amp_props['data'], $post_data['amp']['attributes'], $post_data['amp']['data'] );

		if ( count( $attributes ) === count( $data ) ) {
			foreach ( $attributes as $i => $attribute ) {
				$clear_attribute = sanitize_key( $attribute );
				$clear_data      = $data[ $i ] ?? '';

				if ( $clear_attribute && $clear_data ) {
					$amp_props['attributes'][ $clear_attribute ] = $clear_data;
				}
			}
		}

		if ( ! empty( $post_data['amp']['fallback'] ) ) {
			$amp_props['fallback'] = wp_kses_post( $post_data['amp']['fallback'] );
		}

		if ( $ad->is_type( 'adsense' ) && isset( $post_data['amp'] ) ) {
			foreach ( (array) $post_data['amp'] as $field => $_data ) {
				$amp_props[ sanitize_key( $field ) ] = sanitize_key( $_data );
			}
		}

		$ad->set_prop_temp( 'amp', $amp_props );
	}
}
