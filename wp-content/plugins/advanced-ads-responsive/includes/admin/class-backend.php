<?php
/**
 * The add-on backend main class
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP\admin;

use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin backend
 */
class Backend implements Integration_Interface {
	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'wp_loaded', [ $this, 'wp_loaded' ] );
		add_filter( 'advanced-ads-notices', [ $this, 'add_notices' ] );
	}

	/**
	 * Dashboard initialization
	 *
	 * @return void
	 */
	public function wp_loaded(): void {
		if ( ! defined( 'AAP_VERSION' ) || 1 !== version_compare( AAP_VERSION, '2.24.2' ) ) {
			$notices = get_option( 'advanced-ads-notices' );
			if ( ! array_key_exists( 'pro_responsive_migration', $notices['closed'] ?? [] ) ) {
				\Advanced_Ads_Admin_Notices::get_instance()->add_to_queue( 'pro_responsive_migration' );
			}
		}
	}

	/**
	 * Add potential warning to global array of notices.
	 *
	 * @param array $notices existing notices.
	 *
	 * @return mixed
	 */
	public function add_notices( $notices ) {
		$message = wp_kses(
			sprintf(
			/* translators: 1 is the opening link to the Advanced Ads pge, 2 the closing link */
				__(
					'We have renamed the Responsive Ads add-on to ‘Advanced Ads AMP Ads’. With this change, the Browser Width visitor condition moved from that add-on into Advanced Ads Pro. You can deactivate ‘Advanced Ads AMP Ads’ if you don’t utilize AMP ads or the custom sizes feature for responsive AdSense ad units. %1$sRead more%2$s.',
					'advanced-ads-responsive'
				),
				'<a href="https://wpadvancedads.com/responsive-ads-add-on-becomes-amp-ads" target="_blank" class="advads-manual-link">',
				'</a>'
			),
			[
				'a' => [
					'href'   => true,
					'target' => true,
					'class'  => true,
				],
			]
		);

		$notices['pro_responsive_migration'] = [
			'type'   => 'info',
			'text'   => $message,
			'global' => true,
		];

		return $notices;
	}
}
