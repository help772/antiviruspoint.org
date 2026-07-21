<?php
/**
 * AdSense integration
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP;

use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * AdSense
 *
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */
class AdSense implements Integration_Interface {
	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		// Filter function for responsive ad with custom css.
		add_filter( 'advanced-ads-gadsense-responsive-output', [ $this, 'render_output' ], 10, 3 );
	}

	/**
	 * Add custom CSS to the ad output.
	 *
	 * @param string $output the ad output.
	 * @param Ad     $ad     the ad.
	 * @param string $pub_id AdSense ID.
	 *
	 * @return mixed|string
	 */
	public function render_output( $output, $ad, $pub_id ) {
		global $gadsense;

		$content = json_decode( stripslashes( $ad->get_content() ) );

		if ( isset( $content->unitType ) && 'responsive' === $content->unitType && isset( $content->resize ) ) {
			switch ( $content->resize ) {
				case 'manual':
					// The ad use custom css for resizing.
					$count    = $gadsense['adsense_count'];
					$selector = 'gadsense_slot_' . $count;
					$output  .= '<style type="text/css">' . "\n";

					// The last rule hide the ad.
					$last_rule_hidden = null;

					if ( isset( $content->defaultHidden ) && true === $content->defaultHidden ) {
						$output          .= '.' . $selector . '{display: none;}' . "\n";
						$last_rule_hidden = true;
					} else {
						$width  = $ad->get_width();
						$height = $ad->get_height();
						if ( ! empty( $width ) || ! empty( $height ) ) {
							$w       = ! empty( $width ) ? 'width: ' . $width . 'px;' : '';
							$h       = ! empty( $height ) ? 'height: ' . $height . 'px;' : '';
							$output .= '.' . $selector . '{ display: inline-block; ' . $w . ' ' . $h . '}' . "\n";
						}
					}
					if ( ! empty( $content->media ) ) {
						foreach ( $content->media as $value ) {
							$rule = explode( ':', $value );
							if ( isset( $rule[3] ) && '1' === $rule[3] ) {
								// the ad is hidden for this min-width.
								$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: none;} }' . "\n";

								// Mark this flag to true, so on the next iteration, the display attribute can be set to inline-block (if not hidden).
								$last_rule_hidden = true;
							} else {
								/**
								 * Not hidden, but firstly check if the lastly defined rule hide the ad
								 */
								if ( $last_rule_hidden ) {
									$output          .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: inline-block; width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
									$last_rule_hidden = false;
								} else {
									// Do not touch the $last_rule_hidden var, it is already FALSE or NULL.
									$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
								}
							}
						}
					}

					$output .= '</style>' . "\n";
					$output .= '<ins class="adsbygoogle ' . $selector . '" ';

					if ( null === $last_rule_hidden ) {
						/**
						 * If none of all the rules (including default sizes) has hidden the rule, this flag should be NULL
						 * So we can add the following style attribute.
						 */
						$output .= 'style="display:inline-block;" ';
					}

					$output .= 'data-ad-client="ca-' . $pub_id . '" ' . "\n";
					$output .= 'data-ad-slot="' . $content->slotId . '" ' . "\n";
					$output .= '></ins>' . "\n";
					$output .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-' . $pub_id . '" crossorigin="anonymous"></script>' . "\n";
					$output .= '<script> ' . "\n";
					$output .= '(adsbygoogle = window.adsbygoogle || []).push({}); ' . "\n";
					$output .= '</script>' . "\n";
					break;
				case 'horizontal':
				case 'rectangle':
				case 'vertical':
					$output .= '<ins class="adsbygoogle" ';
					$output .= 'style="display:block;" ' . "\n";
					$output .= 'data-ad-client="ca-' . $pub_id . '" ' . "\n";
					$output .= 'data-ad-slot="' . $content->slotId . '" ' . "\n";
					$output .= 'data-ad-format="' . $content->resize . '" ' . "\n";
					$output .= '></ins>' . "\n";
					$output .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-' . $pub_id . '" crossorigin="anonymous"></script>' . "\n";
					$output .= '<script>(adsbygoogle = window.adsbygoogle || []).push({}); </script>' . "\n";
					break;
			}
		}

		if ( isset( $content->unitType ) && 'matched-content' === $content->unitType ) {
			$settings = wp_advads_amp()->get_matched_content_settings( $content );

			if ( ! $settings['customize_enabled'] ) {
				return $output;
			}

			$output .= '<ins class="adsbygoogle" ';
			$output .= 'style="display:block;" ' . "\n";
			$output .= 'data-ad-client="ca-' . $pub_id . '" ' . "\n";
			$output .= 'data-ad-slot="' . $content->slotId . '" ' . "\n";
			$output .= 'data-ad-format="autorelaxed"' . "\n";
			$output .= sprintf( 'data-matched-content-ui-type="%s,%s"', $settings['ui_type_m'], $settings['ui_type'] ) . "\n";
			$output .= sprintf( 'data-matched-content-rows-num="%s,%s"', $settings['rows_num_m'], $settings['rows_num'] ) . "\n";
			$output .= sprintf( 'data-matched-content-columns-num="%s,%s"', $settings['columns_num_m'], $settings['columns_num'] ) . "\n";
			$output .= '></ins>' . "\n";
			$output .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-' . $pub_id . '" crossorigin="anonymous"></script>' . "\n";
			$output .= '<script>(adsbygoogle = window.adsbygoogle || []).push({}); </script>' . "\n";
		}

		return $output;
	}
}
