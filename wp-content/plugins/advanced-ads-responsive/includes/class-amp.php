<?php
/**
 * AMP integration
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP;

use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Abstracts\Types;
use AdvancedAds\Utilities\Conditional;
use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * AMP features
 */
class Amp implements Integration_Interface {
	/**
	 * Css rules in header.
	 *
	 * @var string
	 */
	public static $css = '';

	/**
	 * AdSense ad types that work on AMP.
	 *
	 * @var array
	 */
	public static $supported_adsense_types = [
		'normal',
		'responsive',
		'matched-content',
		'link',
		'link-responsive',
		'in-article',
	];

	/**
	 * Hooks into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_filter( 'advanced-ads-display-conditions', [ $this, 'add_amp_display_condition' ] );
		add_action( 'advanced-ads-ad-types-manager', [ $this, 'register_ad_type' ] );
	}

	/**
	 * Register ad type
	 *
	 * @param Types $manager Ad types manager.
	 *
	 * @return void
	 */
	public function register_ad_type( Types $manager ) {
		$manager->register_type( Amp_Type::class );
	}

	/**
	 * Add AMP display condition.
	 *
	 * @param array $conditions display conditions of the main plugin.
	 *
	 * @return array new display conditions.
	 */
	public function add_amp_display_condition( $conditions ): array {
		$conditions['amp'] = [
			'label'       => __( 'Accelerated Mobile Pages', 'advanced-ads-responsive' ),
			'description' => __( 'Display ads on Accelerated Mobile Pages', 'advanced-ads-responsive' ),
			'metabox'     => [ '\AdvancedAds\AMP\admin\Amp', 'metabox_amp' ], // Callback to generate the meta box.
			'check'       => [ $this, 'check_amp_display_condition' ], // Callback for frontend check.
		];

		return $conditions;
	}

	/**
	 * Check if ad can be displayed by AMP display condition in frontend.
	 *
	 * @param array $options Options of the condition.
	 * @param Ad    $ad      The ad object.
	 *
	 * @return bool
	 */
	public static function check_amp_display_condition( $options, $ad ): bool {
		if ( ! isset( $options['operator'] ) ) {
			return true;
		}

		switch ( $options['operator'] ) {
			case 'is':
				if ( ! Conditional::is_amp() ) {
					return false;
				}
				break;
			case 'is_not':
				if ( Conditional::is_amp() ) {
					return false;
				}
				break;
		}

		return true;
	}
}
