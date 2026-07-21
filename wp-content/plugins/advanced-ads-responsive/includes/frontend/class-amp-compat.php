<?php
/**
 * Handle known compatibility issues
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP\frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Plugins ccmpatibility
 */
class Amp_Compat {
	/**
	 * Private constructor
	 */
	private function __construct() {
		// WP AMP — Accelerated Mobile Pages for WordPress and WooCommerce (https://codecanyon.net/item/wp-amp-accelerated-mobile-pages-for-wordpress-and-woocommerce/16278608).
		if ( function_exists( 'is_wp_amp' ) ) {
			add_filter( 'amphtml_the_content', [ $this, 'amphtml_add_the_content' ] );
			add_filter( 'advanced-ads-output-wrapper-options', [ $this, 'gather_css' ], 10, 1 );
			add_action( 'amphtml_template_css', [ $this, 'add_amp_css' ] );
		}
	}

	/**
	 * `WP AMP`: update list of allowed the_content filters.
	 *
	 * @param array $hooks undocumented ?.
	 *
	 * @return array $hooks
	 */
	public function amphtml_add_the_content( $hooks ) {
		$priority = \Advanced_Ads::get_instance()->get_content_injection_priority();
		$hooks[ $priority ][] = 'inject_content';

		return $hooks;
	}

	/**
	 * `WP AMP`: gather css rules, since `WP AMP` does not allow inline css
	 *
	 * @param array $wrapper_options ad wrapper options.
	 *
	 * @return array
	 */
	public function gather_css( $wrapper_options ) {
		if ( ! isset( $wrapper_options['id'] ) ) {
			return $wrapper_options;
		}

		if ( ! isset( $wrapper_options['style'] ) ) {
			return $wrapper_options;
		}

		$_style_values_string = '';

		foreach ( $wrapper_options['style'] as $_style_attr => $_style_values ) {
			if ( is_array( $_style_values ) ) {
				$_style_values_string .= $_style_attr . ': ' . implode( ' ', $_style_values ) . '; ';
			} else {
				$_style_values_string .= $_style_attr . ': ' . $_style_values . '; ';
			}
		}
		\AdvancedAds\AMP\Amp::$css .= sprintf( '#%s{ %s }', $wrapper_options['id'], $_style_values_string );

		return $wrapper_options;
	}

	/**
	 * Add css rules to header.
	 *
	 * @return void
	 */
	public function add_amp_css() {
		echo \AdvancedAds\AMP\Amp::$css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Return the singleton
	 *
	 * @return Amp_Compat
	 */
	public static function get() {
		static $instance;

		if ( null === $instance ) {
			$instance = new Amp_Compat();
		}

		return $instance;
	}
}
