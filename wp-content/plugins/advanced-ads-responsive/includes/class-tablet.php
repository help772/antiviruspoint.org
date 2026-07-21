<?php
/**
 * Modify Tablet visitor conditions into Device VC
 *
 * @package AdvancedAds\AMP
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

namespace AdvancedAds\AMP;

use AdvancedAds\Framework\Interfaces\Integration_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Transform Tablet VC into Device VC
 */
class Tablet implements Integration_Interface {
	/**
	 * Hook into WordPress
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_filter( 'advanced-ads-ad-get-visitors', [ $this, 'migrate' ] );
		add_filter( 'advanced-ads-placement-get-visitors', [ $this, 'migrate' ] );
	}

	/**
	 * Transform Tablet visitor condition into device
	 *
	 * @param array $value Visitor conditions.
	 *
	 * @return array
	 */
	public function migrate( $value ) {
		foreach ( $value as $index => $condition ) {
			if ( 'tablet' !== $condition['type'] ) {
				continue;
			}
			$new_condition   = 'is' === $condition['operator']
				? [
					'type'  => 'mobile',
					'value' => [ 'tablet' ],
				]
				: [
					'type'  => 'mobile',
					'value' => [ 'mobile', 'desktop' ],
				];
			$value[ $index ] = $new_condition + $condition;
		}

		return $value;
	}
}
