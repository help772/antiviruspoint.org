<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase --- Ignore the class file name
/**
 * Base class for RoyalMail International rates.
 *
 * @package WC_RoyalMail
 */

namespace WooCommerce\RoyalMail\Rates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Packaging;

/**
 * International_Rate class.
 */
abstract class International_Rate extends RoyalMail_Rate {
	/**
	 * Boxes that must remain in $this->boxes when merchants configure custom
	 * international parcel boxes, so BoxPacker can classify packages the user's
	 * boxes cannot rate-model. Subclasses whose rate bands include box types
	 * beyond PACKET should override this and return the relevant default box
	 * keys from $this->international_default_box.
	 *
	 * @return array<int, string>
	 */
	protected function get_custom_box_additions(): array {
		return array();
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $country_code Address to ship to.
	 * @param  array  $boxes User-defined boxes.
	 * @param  int    $instance_id Instance ID.
	 *
	 * @return array<string, float>
	 */
	public function get_quotes( array $items, string $packing_method, string $country_code, array $boxes = array(), int $instance_id = 0 ): array {
		$supported_countries = $this->get_supported_countries();
		if ( ! empty( $supported_countries ) && ! in_array( $country_code, $this->get_supported_countries(), true ) ) {
			return array();
		}

		$this->apply_additional_rates();
		$bands       = $this->bands;
		$class_quote = 0;

		if ( ! empty( $boxes ) ) {
			$this->boxes = array();

			foreach ( $boxes as $key => $box ) {
				$this->boxes[ $key ] = array(
					'length'     => $box['inner_length'],
					'width'      => $box['inner_width'],
					'height'     => $box['inner_height'],
					'box_weight' => $box['box_weight'],
					'weight'     => 2000,
				);
			}

			// Preserve default boxes that subclasses declare as mandatory so BoxPacker
			// can classify packages into rate bands the user's boxes cannot model
			// (e.g. MEDIUM_PARCEL for International Tracked above 2 kg).
			foreach ( $this->get_custom_box_additions() as $box_key ) {
				if ( ! isset( $this->boxes[ $box_key ] ) && isset( $this->international_default_box[ $box_key ] ) ) {
					$this->boxes[ $box_key ] = $this->international_default_box[ $box_key ];
				}
			}
		} else {
			$this->boxes = $this->international_default_box;
		}

		if ( isset( $bands[ $country_code ] ) ) {
			$zone = $country_code;
		} else {
			$zone = $this->get_zone( $country_code );
		}

		// Detect flat packages structure (e.g. international-economy) vs zone-based structure.
		$is_flat_structure = ! isset( $bands[ $zone ] ) && (
			isset( $bands[ Packaging::LETTER ] ) ||
			isset( $bands[ Packaging::LARGE_LETTER ] ) ||
			isset( $bands[ Packaging::PACKET ] ) ||
			isset( $bands[ Packaging::PRINTED_PAPERS ] )
		);

		$rate_slug_for_filter = str_replace( 'international-', '', $this->get_rate_slug() );

		/**
		 * Allow third party to enable/disable printed papers rate.
		 *
		 * @param boolean $rate_enabled Flag for enabling/disabling the rate.
		 * @param int $instance_id Instance ID.
		 * @param string $rate_slug Name of the rate.
		 * @param string $country_code Destination.
		 * @param string $packing_method Packing method.
		 *
		 * @since 3.0.0
		 */
		$printed_paper_packages         = apply_filters( 'woocommerce_shipping_royal_mail_printed_papers_enabled', true, $instance_id, $rate_slug_for_filter, $country_code, $packing_method ) ? $this->get_printed_papers_packages( $items, $country_code, $packing_method ) : array();
		$regular_packages               = $this->get_packages( $items, $packing_method );
		$packages                       = array_merge( $regular_packages, $printed_paper_packages );
		$options                        = $this->get_instance_options( $instance_id );
		$ignore_max_compensation        = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );
		$enable_additional_compensation = ( ! empty( $options['enable_addit_compensation'] ) && 'yes' === $options['enable_addit_compensation'] );
		$max_compensation               = ( true === $enable_additional_compensation ) ? $this->get_compensation_up_to_value() : $this->get_compensation_included_value();

		if ( empty( $packages ) ) {
			return array();
		}

		foreach ( $packages as $package ) {
			$this->validate_package( $package );

			$package_bands = $is_flat_structure
				? ( $bands[ $package->id ] ?? null )
				: ( $bands[ $zone ][ $package->id ] ?? null );

			if ( null === $package_bands ) {
				return array(); // Unpacked item.
			}

			if ( in_array( $package->id, array( Packaging::PACKET, Packaging::PRINTED_PAPERS ), true ) && 900 < ( $package->length + $package->width + $package->height ) ) {
				return array(); // Exceeding parcels requirement, unpacked.
			}

			if ( $package->value > $max_compensation && ! $ignore_max_compensation ) {
				return array(); // Exceeding maximum compensation.
			}

			$matched = false;

			foreach ( $package_bands as $weight => $value ) {
				if ( $package->weight <= $weight ) {
					$class_quote += $value;
					$matched      = true;
					break;
				}
			}

			if ( ! $matched ) {
				return array();
			}

			if ( $enable_additional_compensation && $package->value > $this->get_compensation_included_value() ) {
				// Fee buys the additional cover band above the included compensation tier.
				$class_quote += $this->get_compensation_fees();
			}
		}

		// If taxed, rate include 20% VAT.
		if ( $this->is_taxed ) {
			$class_quote = $class_quote / 1.2;
		}

		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $class_quote / 100;

		return $quotes;
	}
}
