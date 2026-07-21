<?php
/**
 * WordPress frontend integration.
 *
 * @package AdvancedAds\GAM
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   2.4.0
 */

namespace AdvancedAds\GAM;

use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Frontend.
 */
class Frontend implements Integration_Interface {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'wp_enqueue_scripts', [ $this, 'late_enqueue_script' ], 100 );
		add_action( 'advanced_ads_inline_header_scripts', [ $this, 'inline_script' ] );
	}

	/**
	 * Close the ad wrapper if a GAM ad did not get filled.
	 * Sticky and Layer handle that on their end.
	 *
	 * @param string $prefix prefix used for frontend ID and class attributes.
	 *
	 * @return void
	 */
	public function inline_script( $prefix ) {
		?>
		<script>
			document.addEventListener( 'aagam_empty_slot', function ( ev ) {
				const div = document.getElementById( ev.detail );
				if ( ! div ) {
					return;
				}
				const prefix = '<?php echo esc_js( $prefix ); ?>', wrapper = div.closest( '[id^="' + prefix + '"]' );
				if ( ! wrapper ) {
					return;
				}
				if ( wrapper.classList.contains( prefix + 'sticky' ) || wrapper.classList.contains( prefix + 'layer' ) ) {
					return;
				}
				wrapper.style.display = 'none';
			} );
		</script>
		<?php
	}

	/**
	 * Append inline JS to cache busting base JS file
	 *
	 * @return void
	 */
	public function late_enqueue_script() {
		if ( ! defined( 'AAP_VERSION' ) ) {
			return;
		}
		wp_add_inline_script(
			'advanced-ads-pro/cache_busting',
			"document.addEventListener( 'advads_ajax_ad_select', function(ev){ ev.detail.gam = " . wp_json_encode( \Advanced_Ads_Gam_Ad::get_front_end_variables() ) . '; } );',
			'before'
		);
	}
}
