<?php
/**
 * Plugin Types.
 *
 * @package AdvancedAds\GAM
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   2.4.0
 */

namespace AdvancedAds\GAM;

use AdvancedAds\Abstracts\Types;
use AdvancedAds\GAM\Types\GAM;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Types.
 */
class Plugin_Types implements Integration_Interface {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'advanced-ads-ad-types-manager', [ $this, 'register_ad_types' ] );
	}

	/**
	 * Register ad types
	 *
	 * @param Types $manager Ad types manager.
	 *
	 * @return void
	 */
	public function register_ad_types( $manager ): void {
		$manager->register_type( GAM::class );
	}
}
