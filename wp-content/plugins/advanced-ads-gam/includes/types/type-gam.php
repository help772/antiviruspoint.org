<?php
/**
 * This class represents the "GAM" ad type.
 *
 * @package AdvancedAds\GAM
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   2.4.0
 */

namespace AdvancedAds\GAM\Types;

use Advanced_Ads_Gam_Ad;
use Advanced_Ads_Network_Gam;
use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Interfaces\Ad_Type;

defined( 'ABSPATH' ) || exit;

/**
 * Type GAM.
 */
class GAM implements Ad_Type {
	/**
	 * Get the unique identifier (ID) of the ad type.
	 *
	 * @return string The unique ID of the ad type.
	 */
	public function get_id(): string {
		return 'gam';
	}

	/**
	 * Get the class name of the object as a string.
	 *
	 * @return string
	 */
	public function get_classname(): string {
		return Advanced_Ads_Gam_Ad::class;
	}

	/**
	 * Get the title or name of the ad type.
	 *
	 * @return string The title of the ad type.
	 */
	public function get_title(): string {
		return __( 'Google Ad Manager', 'advanced-ads-gam' );
	}

	/**
	 * Get a description of the ad type.
	 *
	 * @return string The description of the ad type.
	 */
	public function get_description(): string {
		return __( 'Use ad units from your Google Ad Manager account', 'advanced-ads-gam' );
	}

	/**
	 * Check if this ad type requires premium.
	 *
	 * @return bool True if premium is required; otherwise, false.
	 */
	public function is_premium(): bool {
		return false;
	}

	/**
	 * Get the URL for upgrading to this ad type.
	 *
	 * @return string The upgrade URL for the ad type.
	 */
	public function get_upgrade_url(): string {
		return '';
	}

	/**
	 * Get the URL for upgrading to this ad type.
	 *
	 * @return string The upgrade URL for the ad type.
	 */
	public function get_image(): string {
		return ADVADS_BASE_URL . 'assets/img/ad-types/gam.svg';
	}

	/**
	 * Check if this ad type has size parameters.
	 *
	 * @return bool True if has size parameters; otherwise, false.
	 */
	public function has_size(): bool {
		return true;
	}

	/**
	 * Output for the ad parameters metabox
	 *
	 * @param Ad $ad Ad instance.
	 *
	 * @return void
	 */
	public function render_parameters( $ad ): void {
		$ad_content       = $ad->get_content() ? trim( $ad->get_content() ) : '';
		$network          = Advanced_Ads_Network_Gam::get_instance();
		$responsive_sizes = ! empty( $ad->get_prop( 'responsive-sizes' ) );

		require_once AA_GAM_ABSPATH . 'views/admin/ad-parameters.php';
	}
}
