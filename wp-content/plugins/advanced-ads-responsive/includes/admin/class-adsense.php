<?php
/**
 * Backend AdSense integration
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP\admin;

use AdvancedAds\Constants;
use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Framework\Utilities\Params;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * AdSense ad editing
 */
class AdSense implements Integration_Interface {
	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_filter( 'advanced-ads-gadsense-ad-param-data', [ $this, 'ad_param_data' ], 10, 3 );
		add_filter( 'advanced-ads-gadsense-responsive-sizing', [ $this, 'enable_manual_css' ], 10, 1 );
		add_action( 'advanced-ads-gadsense-extra-ad-param', [ $this, 'extra_template' ], 10, 2 );
		add_filter( 'advanced-ads-gadsense-ad-param-script', [ $this, 'ad_param_script' ] );
		add_action( 'admin_print_scripts', [ $this, 'print_scripts' ] );
		add_filter( 'advanced-ads-ad-pre-save', [ $this, 'save_post' ], 20, 2 );
	}

	/**
	 * Pass the default width and height to be saved in ad options.
	 *
	 * @param Ad $ad the ad.
	 *
	 * @return void
	 */
	public function save_post( $ad ) {
		if ( 'manual' === Params::post( 'ad-resize-type' ) ) {
			$ad->set_width( (int) Params::post( 'default-width' ) );
			$ad->set_height( (int) Params::post( 'default-height' ) );
		}
	}

	/**
	 * Enqueue additional script (.js) files when on the new/edit ad page
	 *
	 * @param array $scripts array of scripts file to enqueue.
	 */
	public function ad_param_script( $scripts ) {
		wp_enqueue_style( 'gadsense-responsive-manual-css', AA_AMP_BASE_URL . 'assets/css/adsense.css', [], AAR_VERSION );

		$scripts['gadsense-respad-js'] = [
			'path'    => AA_AMP_BASE_URL . 'assets/js/adsense-new-ad.js',
			'dep'     => [ 'jquery' ],
			'version' => AAR_VERSION,
		];

		return $scripts;
	}

	/**
	 * Print inline script
	 *
	 * @return void
	 */
	public function print_scripts() {
		global $pagenow, $post_type, $post;
		if (
			( 'post-new.php' === $pagenow && Constants::POST_TYPE_AD )
			|| (
				'post.php' === $pagenow
				&& Constants::POST_TYPE_AD === $post_type
				&& 'edit' === Params::get( 'action' )
			)
		) {

			//  Fix for #108.
			//  NEVER create a json object inside of JS from a string/obj without encoding it!!!
			$json_object = json_decode( $post->post_content );
			?>
			<script type="text/javascript">
				var respAdsAdsense =
				<?php
				echo wp_json_encode(
					[
						'msg'       => [
							'removeRule'   => esc_attr__( 'Remove this rule', 'advanced-ads-responsive' ),
							'remove'       => esc_attr__( 'remove', 'advanced-ads-responsive' ),
							'notDisplayed' => esc_attr__( 'Not Displayed', 'advanced-ads-responsive' ),
						],
						'currentAd' => empty( $json_object ) ? false : $json_object,
					]
				);
				?>;
			</script>
			<?php
		}
	}

	/**
	 * Prepare data before rendering ad parameters
	 *
	 * @param array     $extra_params extra ad parameters for AdSense ads.
	 * @param \stdClass $content      ad data from the post content.
	 * @param Ad        $ad           the current ad.
	 *
	 * @return array
	 */
	public function ad_param_data( $extra_params, $content, $ad ) {
		if ( 'manual' === $content->resize ) {
			$extra_params['default_width']  = $ad->get_width();
			$extra_params['default_height'] = $ad->get_height();
			$extra_params['default_hidden'] = isset( $content->defaultHidden ) && $content->defaultHidden;

			if ( isset( $content->media ) ) {
				foreach ( $content->media as $rule ) {
					$exploded                   = explode( ':', $rule );
					$new_rule                   = [
						'minw' => $exploded[0],
						'w'    => $exploded[1],
						'h'    => $exploded[2],
					];
					$hidden                     = isset( $exploded[3] ) && '1' === $exploded[3];
					$new_rule['hidden']         = $hidden;
					$extra_params['at_media'][] = $new_rule;
				}
			}
		}

		return $extra_params;
	}

	/**
	 * Adds the manual css option to the list of available resizing mode.
	 *
	 * @param array $resize associative array with the mode's slug as key and the displayed text (within a <select />) as value.
	 *
	 * @return array the modified list.
	 */
	public function enable_manual_css( $resize ) {
		$resize['horizontal'] = __( 'horizontal', 'advanced-ads-responsive' );
		$resize['rectangle']  = __( 'rectangle', 'advanced-ads-responsive' );
		$resize['vertical']   = __( 'vertical', 'advanced-ads-responsive' );
		$resize['manual']     = __( 'advanced', 'advanced-ads-responsive' );

		return $resize;
	}

	/**
	 * Draws manual css fields/inputs on adsense ad parameters meta box
	 *
	 * @param array     $extra_params extra parameters added to the ad parameters meta box.
	 * @param \stdClass $content      the ad content object parsed from the post content value.
	 *
	 * @return void
	 */
	public function extra_template( $extra_params, $content ) {
		$is_responsive  = isset( $content->unitType ) && 'responsive' === $content->unitType;
		$use_manual_css = isset( $content->resize ) && 'manual' === $content->resize;

		include AA_AMP_ABSPATH . 'views/ad-params-manual-css.php';

		$unit_type    = $content->unitType ?? 'normal';
		$is_supported = 'matched-content' === $unit_type;
		$settings     = wp_advads_amp()->get_matched_content_settings( $content );
		$types        = [ 'image_sidebyside', 'image_card_sidebyside', 'image_stacked', 'image_card_stacked', 'text', 'text_card' ];

		include AA_AMP_ABSPATH . 'views/ad-params-responsive-matched-content.php';
	}
}
