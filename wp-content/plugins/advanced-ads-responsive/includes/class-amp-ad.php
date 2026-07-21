<?php
/**
 * The AMP ad class
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP;

use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Interfaces\Ad_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * AMP ad class
 */
class Amp_Ad extends Ad implements Ad_Interface {
	/**
	 * Prepare output for frontend.
	 *
	 * @return string
	 */
	public function prepare_frontend_output(): string {
		$options    = $this->get_prop( 'amp' ) ?? [];
		$attributes = ( ! empty( $options['attributes'] ) && is_array( $options['attributes'] ) ) ? $options['attributes'] : [];

		if ( ! empty( $this->get_width() ) ) {
			$attributes['width'] = $this->get_width();
		}

		if ( ! empty( $this->get_height() ) ) {
			$attributes['height'] = $this->get_height();
		}

		if ( ! empty( $attributes['type'] ) ) {
			$content     = '';
			$attr_string = [];
			foreach ( $attributes as $attribute => $data ) {
				$attr_string[] = sprintf( "%s='%s'", sanitize_key( $attribute ), esc_attr( $data ) );
			}
			$attr_string = implode( ' ', $attr_string );

			if ( ! empty( $options['fallback'] ) ) {
				$content = sprintf( '<div fallback>%s</div>', esc_html( $options['fallback'] ) );
			}

			return sprintf( '<%1$s %2$s>%3$s</%1$s>', 'amp-ad', $attr_string, $content );
		}

		return '';
	}
}
