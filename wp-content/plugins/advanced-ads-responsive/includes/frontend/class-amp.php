<?php
/**
 * Front end output on AMP pages
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP\frontend;

use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Utilities\Conditional;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * AMP frontend
 *
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */
class Amp implements Integration_Interface {
	/**
	 * Hooks into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'wp', [ $this, 'wp' ], 9 );
	}

	/**
	 * Tasks for when WP environment is set
	 *
	 * @return void
	 */
	public function wp(): void {
		add_filter( 'advanced-ads-can-display-ad', [ $this, 'can_display' ], 10, 2 );

		if ( ! Conditional::is_amp() ) {
			return;
		}

		// Disable cache-busting when AMP version of a post is being viewed.
		add_filter( 'advanced-ads-pro-cb-frontend-disable', '__return_true' );

		if ( ! defined( 'AAT_VERSION' ) || -1 === version_compare( AAT_VERSION, '2' ) ) {
			add_filter( 'advanced-ads-tracking-method', [ $this, 'set_tracking_method' ] );
		}

		add_filter( 'advanced-ads-gadsense-output', [ $this, 'prepare_gadsense_output' ], 10, 4 );

		// The is_amp_endpoint function is used for multiple plugins.
		if ( ! function_exists( 'is_amp_endpoint' ) ) {
			Amp_Compat::get();

			return;
		}

		// Adds the AdSense AMP code to the page (head) in "Reader" mode.
		add_action( 'amp_post_template_data', [ $this, 'add_component_script' ] );

		/**
		 * Load CSS for Responsive AdSense ads with custom sizes.
		 *
		 * Use `amp_post_template_css` in Reader mode.
		 * The CSS rules in Transition and Standards mode are added next to the ad in the content in prepare_gadsense_output()
		 */
		add_action( 'amp_post_template_css', [ $this, 'add_amp_css' ] );
	}

	/**
	 * Add js to the header.
	 *
	 * @param array $data AMP components.
	 */
	public function add_component_script( $data ) {
		if ( ! defined( 'ADVANCED_ADS_AMP_DISABLE_AD_SCRIPT' ) ) {
			$data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-0.1.js';
		}

		return $data;
	}

	/**
	 * Add CSS rules to header.
	 *
	 * Works with the `AMP` plugin only in Reader mode.
	 */
	public function add_amp_css() {
		echo \AdvancedAds\AMP\Amp::$css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Check if the ad can be displayed.
	 *
	 * @param bool $can_display existing value.
	 * @param Ad   $ad          Advanced_Ads_Ad object.
	 *
	 * @return bool true if the ad can be displayed, false otherwise.
	 */
	public function can_display( $can_display, $ad ) {
		if ( ! $can_display ) {
			return false;
		}

		if ( ! Conditional::is_amp() ) {
			// Disable ads with type 'amp'.
			return ! $ad->is_type( 'amp' );
		}

		return true;
	}

	/**
	 * If custom AJAX handler is used, set it to amp tracking.
	 * If frontend tracking method is used with admin-ajax.php set it to `onrequest`.
	 *
	 * @param string $method tracking method as set under Advanced Ads > Settings > Tracking.
	 *
	 * @return string
	 */
	public function set_tracking_method( $method ) {
		if ( 'frontend' !== $method ) {
			return $method;
		}

		if ( defined( 'ADVANCED_ADS_TRACKING_LEGACY_AJAX' ) && ADVANCED_ADS_TRACKING_LEGACY_AJAX ) {
			return 'onrequest';
		}

		return 'amp_pixel';
	}

	/**
	 * Check if a type of adsense ad is supported.
	 *
	 * @param \stdClass $content the ad content object.
	 *
	 * @return bool
	 */
	public static function is_supported_adsense_type( $content ) {
		if ( ! isset( $content->unitType ) ) {
			return false;
		}

		return in_array( $content->unitType, \AdvancedAds\AMP\Amp::$supported_adsense_types, true );
	}

	/**
	 * Prepare AdSense frontend output for showing on AMP page.
	 *
	 * @param string|bool $output  existing output.
	 * @param Ad          $ad      Advanced_Ads_Ad object.
	 * @param string      $pub_id  publisher ID.
	 * @param \stdClass   $content ad content.
	 *
	 * @return string new output.
	 */
	public function prepare_gadsense_output( $output, $ad, $pub_id, $content ) {
		global $gadsense;

		if ( ! self::is_supported_adsense_type( $content ) ) {
			return '';
		}

		$count       = $gadsense['adsense_count'];
		$selector    = 'gadsense_slot_' . $count;
		$width       = $ad->get_width();
		$height      = $ad->get_height();
		$options     = $ad->get_prop( 'amp' ) ?? [];
		$layout      = ! empty( $options['layout'] ) ? $options['layout'] : 'default';
		$output_part = sprintf( '<amp-ad type="adsense" data-ad-client="ca-%s" data-ad-slot="%s" ', $pub_id, $content->slotId );

		/**
		 * Go through AMP layouts
		 *
		 * `layouts` here mean the output of the ad on AMP pages.
		 * while `unitType` is the AdSense ad type.
		 */
		switch ( $layout ) {
			case 'default':
				$adsense_options = \Advanced_Ads_AdSense_Data::get_instance()->get_options();
				$fw              = ! empty( $adsense_options['fullwidth-ads'] ) ? $adsense_options['fullwidth-ads'] : 'default';
				switch ( $content->unitType ) {
					/**
					 * Normal display ads and link ad units use the fixed size given in the original ad code.
					 */
					case 'normal':
					case 'link':
						// Fixed width and height with no responsiveness supported.
						if ( $width > 0 && $height > 0 ) {
							$ad->set_wrapper_class( $selector );

							return $output_part . sprintf( 'layout="fixed" width="%s" height="%s"></amp-ad>', $width, $height );
						}
						break;
					/**
					 * Responsive link units have no specific height so we choose one as a fallback
					 * A height of 90px seems appropriate since this is the only choice the AdSense account offers for smaller fixed-sized link ads
					 */
					case 'link-responsive':
						return $output_part . ' width="auto" height="90" layout="fixed-height" ></amp-ad>';
					/**
					 * Responsive ad units with three different default behaviors
					 *
					 * 1. with manual sizes: behaves the same on AMP as on the normal page
					 * 2. fixed height of 320px and 100% of browser window width
					 * 3. fixed height of 320px and 100% of parent container width
					 *
					 * Default output is a fixed height of 320px and width of 100% of the parent container.
					 */
					case 'responsive':
						if ( isset( $content->resize ) && 'manual' === $content->resize ) {
							/**
							 * Add inline CSS in AMP Transition and Standard mode
							 * CSS for Reader mode is added in the header through amp_post_template_css()
							 */
							// Process 'advanced' resizing.
							\AdvancedAds\AMP\Amp::$css .= $this->get_adsense_manual_css( $ad, $selector, $content );
							if ( 'reader' !== $this->get_amp_template_mode() ) {
								echo '<style>' . \AdvancedAds\AMP\Amp::$css . '</style>';
							};

							/**
							 * "width" and "height" should not be needed according to https://amp.dev/documentation/guides-and-tutorials/learn/amp-html-layout/?format=websites#layout
							 * but that stopped to work in July 2020 and was only resolved by adding them
							 * the height of 250 is actually overridden by the manual sizes of the container, but "auto" is not allowed for the height
							 */
							return sprintf( '<div class="%s">%slayout="fill" width="auto" height="250"></amp-ad></div>', $selector, $output_part );
							// Responsive ad units with "auto" size.
						} else {
							// Responsive ad unit with the "full width" option being disabled.
							if ( 'disable' === $fw ) {
								return $output_part . ' width="auto" height="320" layout="fixed-height" ></amp-ad>';
							} else {
								// Responsive ad unit with the "full width" option being set to default or enabled.
								return $output_part . 'width="100vw" height="320" data-auto-format="rspv" data-full-width><div overflow></div></amp-ad>';
							}
						}
					/**
					 * In-article ad units
					 *
					 * Default output is a fixed height of 320px and width of 100% of the parent container.
					 *
					 * The "full width" option would work, technically, but we omitted it because it is not officially documented.
					 */
					case 'in-article':
						return $output_part . ' width="auto" height="320" layout="fixed-height" ></amp-ad>';
					/**
					 * Responsive Matched Content ads have no specific height so we choose one as a fallback
					 * A height of 320px seems appropriate since 300px is the default for this ad unit in the AdSense account
					 * and 320px the default for responsive AMP ads
					 *
					 * The layout output could be used for the fallback AMP output options as well if needed later.
					 */
					case 'matched-content':
						$layout_output = $this->get_adsense_matched_content_layout_settings( $content );
						if ( ! $layout_output ) {
							return $output_part . ' width="auto" height="320" layout="fixed-height" ></amp-ad>';
						} else {
							return $output_part . ' width="auto" height="320" layout="fixed-height" ' . $layout_output . '></amp-ad>';
						}
				}

				/**
				 * The "default" option no longer exists under Advanced Ads > Settings > AdSense > AMP because every ad unit should have a default behavior now.
				 * We are keeping it as a fallback for those who had it set up before.
				 */
				if ( ! empty( $adsense_options['amp']['convert'] ) ) {
					$width  = ! empty( $adsense_options['amp']['width'] ) ? absint( $adsense_options['amp']['width'] ) : 300;
					$height = ! empty( $adsense_options['amp']['height'] ) ? absint( $adsense_options['amp']['height'] ) : 250;

					return $output_part . sprintf( 'layout="responsive" width="%s" height="%s"></amp-ad>', $width, $height );
				} else {
					// This line should never be reached since we covered all ad units above but still might be a useful fallback in case we missed something.
					return $output_part . ' width="auto" height="320" layout="fixed-height" ></amp-ad>';
				}
			case 'responsive':
				$width  = ! empty( $options['width'] ) ? absint( $options['width'] ) : ( $width ? absint( $width ) : 300 );
				$height = ! empty( $options['height'] ) ? absint( $options['height'] ) : ( $height ? absint( $height ) : 250 );

				/**
				 * According to https://amp.dev/documentation/guides-and-tutorials/learn/amp-html-layout/?format=websites#layout
				 * "vw" is not needed and we would just use the raw numbers for width and height
				 * but in July 2020 this was outputting only fixed-sized ads, e.g., with 300px x 100px instead of using it as a ratio
				 * It does also not seem possible (anymore) to deliver a full width ad on mobile using this option.
				 */
				return $output_part . sprintf( 'layout="responsive" width="%svw" height="%svw"></amp-ad>', $width, $height );
			case 'fixed_height':
				$fixed_height = ! empty( $options['fixed_height'] ) ? absint( $options['fixed_height'] ) : ( $height ?: 250 );

				return $output_part . sprintf( 'layout="fixed-height" width="auto" height="%s"></amp-ad>', $fixed_height );
			case 'hide':
				return '';
		}

		// Completely disable the ad.
		return '';
	}

	/**
	 * Get mode of the official AMP plugin
	 *
	 * @return string|bool standard, transitional, reader (default)
	 */
	public function get_amp_template_mode() {
		if ( ! class_exists( 'AMP_Theme_Support' ) ) {
			return 'reader';
		}

		$exposes_support_mode = defined( 'AMP_Theme_Support::STANDARD_MODE_SLUG' )
								&& defined( 'AMP_Theme_Support::TRANSITIONAL_MODE_SLUG' )
								&& defined( 'AMP_Theme_Support::READER_MODE_SLUG' );

		if ( defined( 'AMP__VERSION' ) ) {
			$amp_plugin_version = AMP__VERSION;
			if ( strpos( $amp_plugin_version, '-' ) !== false ) {
				$amp_plugin_version = explode( '-', $amp_plugin_version )[0];
			}

			$amp_plugin_version_2_or_higher = version_compare( $amp_plugin_version, '2.0.0', '>=' );
		} else {
			$amp_plugin_version_2_or_higher = false;
		}

		if ( $amp_plugin_version_2_or_higher ) {
			$exposes_support_mode = class_exists( '\AMP_Options_Manager' )
									&& method_exists( '\AMP_Options_Manager', 'get_option' )
									&& $exposes_support_mode;
		} else {
			$exposes_support_mode = class_exists( '\AMP_Theme_Support' )
									&& method_exists( '\AMP_Theme_Support', 'get_support_mode' )
									&& $exposes_support_mode;
		}

		if ( $exposes_support_mode ) {
			// If recent version, we can properly detect the mode.
			if ( $amp_plugin_version_2_or_higher ) {
				$mode = \AMP_Options_Manager::get_option( 'theme_support' );
			} else {
				$mode = \AMP_Theme_Support::get_support_mode();
			}

			if ( \AMP_Theme_Support::STANDARD_MODE_SLUG === $mode ) {
				return $mode;
			}

			if ( in_array( $mode, [ \AMP_Theme_Support::TRANSITIONAL_MODE_SLUG, \AMP_Theme_Support::READER_MODE_SLUG ], true ) ) {
				return $mode;
			}
		}

		return 'reader';
	}

	/**
	 * Get layout settings for Matched Content ad units
	 *
	 * @param object $content Ad unit content (which includes options).
	 *
	 * @return bool|string false if layout settings are not enabled or the string to include in AMP ad units.
	 */
	private function get_adsense_matched_content_layout_settings( $content ) {
		if ( empty( $content ) ) {
			return false;
		}

		$layout_settings = wp_advads_amp()->get_matched_content_settings( $content );

		if ( ! $layout_settings['customize_enabled'] ) {
			return false;
		}

		$layout_output  = sprintf( 'data-matched-content-ui-type="%s,%s"', $layout_settings['ui_type_m'], $layout_settings['ui_type'] ) . "\n";
		$layout_output .= sprintf( 'data-matched-content-rows-num="%s,%s"', $layout_settings['rows_num_m'], $layout_settings['rows_num'] ) . "\n";
		$layout_output .= sprintf( 'data-matched-content-columns-num="%s,%s"', $layout_settings['columns_num_m'], $layout_settings['columns_num'] ) . "\n";

		return $layout_output;
	}

	/**
	 * Get css used in manual (advanced) resizing
	 *
	 * @param Ad        $ad       Advanced_Ads_Ad object.
	 * @param string    $selector css selector.
	 * @param \stdClass $content  ad content.
	 *
	 * @return string
	 */
	private function get_adsense_manual_css( $ad, $selector, $content ) {
		$output = '.' . $selector . '{ position: relative; }' . "\n";
		// The last rule hide the ad.
		$last_rule_hidden = null;

		$ad_width  = $ad->get_width();
		$ad_height = $ad->get_height();

		if ( isset( $content->defaultHidden ) && true === $content->defaultHidden ) {
			$output          .= '.' . $selector . '{display: none;}' . "\n";
			$last_rule_hidden = true;
		} elseif ( ! empty( $ad_width ) || ! empty( $ad_height ) ) {
			$w       = ! empty( $ad_width ) ? 'width: ' . $ad_width . 'px;' : '';
			$h       = ! empty( $ad_height ) ? 'height: ' . $ad_height . 'px;' : '';
			$output .= '.' . $selector . '{ display: inline-block; ' . $w . ' ' . $h . '}' . "\n";
		}

		if ( ! empty( $content->media ) ) {
			foreach ( $content->media as $value ) {

				$rule   = explode( ':', $value );
				$hidden = isset( $rule[3] ) && '1' == $rule[3];

				if ( $hidden ) {
					// The ad is hidden for this min-width.
					$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: none;} }' . "\n";

					// Mark this flag to true, so on the next iteration, the display attribute can be set to inline-block (if not hidden).
					$last_rule_hidden = true;
				} elseif ( $last_rule_hidden ) {
					/**
					 * Not hidden, but firstly check if the lastly defined rule hide the ad
					 */
					$output          .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: inline-block; width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
					$last_rule_hidden = false;
				} else {
					// Do not touch the $last_rule_hidden var, it is already FALSE or NULL.
					$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
				}
			}
		}

		return $output;
	}
}
