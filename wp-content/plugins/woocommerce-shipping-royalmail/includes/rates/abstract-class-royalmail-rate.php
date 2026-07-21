<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase --- Ignore the class file name
/**
 * Base class for RoyalMail rate.
 *
 * @package WC_RoyalMail
 */

namespace WooCommerce\RoyalMail\Rates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Packaging;
use WooCommerce\RoyalMail\Shipping_Zones;
use WooCommerce\RoyalMail\JSON_Rate_Loader;
use WooCommerce\RoyalMail\Rate_Data_Manager;
use WooCommerce\BoxPacker;
use WooCommerce\BoxPacker\WC_Boxpack;
use WooCommerce\BoxPacker\Package;

/**
 * RoyalMail Rate class
 */
abstract class RoyalMail_Rate {

	const MAX_LARGE_LETTER_WEIGHT = 750;

	const MAX_LETTER_WEIGHT = 100;

	/**
	 * List of country codes under EUR Zone 1.
	 *
	 * See https://www.royalmail.com/sites/royalmail.com/files/2023-09/our-prices-october-2023-ta.pdf
	 * Page 10.
	 *
	 * Excluding Corsica as it's part of France.
	 *
	 * @var array EUR Zone 1 country codes
	 */
	protected array $europe_zone_1 = array(
		'IE',
		'DE',
		'DK',
		'FR',
		'MC',
	);

	/**
	 * List of country codes under EUR Zone 2.
	 *
	 * See https://www.royalmail.com/sites/royalmail.com/files/2023-09/our-prices-october-2023-ta.pdf
	 * Page 10.
	 *
	 * Excluding Azores, Madeira as it's part of Portugal.
	 * Excluding Balaeric Island, and Canary Island as it's part of Spain.
	 *
	 * @var array EUR Zone 2 country codes
	 */
	protected array $europe_zone_2 = array(
		'AT',
		'BE',
		'BG',
		'HR',
		'CY',
		'CZ',
		'EE',
		'FI',
		'GR',
		'HU',
		'IT',
		'LV',
		'LT',
		'LU',
		'MT',
		'NL',
		'PL',
		'PT',
		'RO',
		'SK',
		'SI',
		'ES',
		'SE',
	);

	/**
	 * List of country codes under EUR Zone 3.
	 *
	 * See https://www.royalmail.com/sites/royalmail.com/files/2023-09/our-prices-october-2023-ta.pdf
	 * Page 10.
	 *
	 * Excluding Kosovo as it's not part of ISO Countries.
	 *
	 * @var array EUR Zone 3 country codes
	 */
	protected array $europe_zone_3 = array(
		'AL',
		'AD',
		'AM',
		'AZ',
		'BY',
		'BA',
		'FO',
		'GE',
		'GI',
		'GL',
		'IS',
		'KZ',
		'KG',
		'LI',
		'MD',
		'ME',
		'MK',
		'NO',
		'RU',
		'SM',
		'RS',
		'CH',
		'TJ',
		'TR',
		'TM',
		'UA',
		'UZ',
		'VA',
	);

	/**
	 * List of country codes under World Zone 2.
	 *
	 * @var array World Zone country codes
	 */
	protected array $world_zone_2 = array(
		'AU',
		'PW',
		'IO',
		'CX',
		'CC',
		'CK',
		'FJ',
		'PF',
		'TF',
		'KI',
		'MO',
		'NR',
		'NC',
		'NZ',
		'PG',
		'NU',
		'NF',
		'LA',
		'PN',
		'TO',
		'TV',
		'WS',
		'AS',
		'SG',
		'SB',
		'TK',
	);

	/**
	 * List of country codes under World Zone 3.
	 *
	 * @var array World Zone country codes
	 */
	protected array $world_zone_3 = array(
		'US',
	);

	/**
	 * List of country codes where Printed Papers max weight
	 * is limited to 2kg instead of 5kg.
	 *
	 * @var array Country codes
	 */
	protected array $printed_papers_2kg_limited_countries = array(
		'CA',
		'KH',
	);

	/**
	 * Name of the rate (e.g. 'special_delivery_1pm').
	 *
	 * @var string ID/Name of rate
	 */
	protected string $rate_id = '';

	/**
	 * Rate price type.
	 *
	 * @var string
	 */
	public string $rate_type;

	/**
	 * Bands is an array of pricing bands where key is coverage / compensation
	 * for loss or damange (this will numeric value), or size (e.g. 'letter');
	 * value is an key-value array where key is package weight and value is the
	 * price. Data is nested within top-level array where key is the is the
	 * calendar year during which the pricing bands became active.
	 *
	 * @var array Pricing bands
	 */
	protected $bands = array();

	/**
	 * Shipping boxes.
	 *
	 * @var array Shipping boxes
	 */
	protected $boxes = array();

	/**
	 * Refer to https://www.royalmail.com/sites/royalmail.com/files/2021-03/royal-mail-our-prices-april-2021.pdf page 9.
	 *
	 * @var array
	 */
	protected array $international_default_box = array(
		Packaging::LETTER        => array(
			'length' => 240, // Max L in mm.
			'width'  => 165, // Max W in mm.
			'height' => 5,   // Max H in mm.
			'weight' => self::MAX_LETTER_WEIGHT, // Max Weight in grams.
		),
		Packaging::LARGE_LETTER  => array(
			'length' => 353,
			'width'  => 250,
			'height' => 25,
			'weight' => self::MAX_LARGE_LETTER_WEIGHT,
		),
		'long-parcel'            => array(
			'length' => 600,
			'width'  => 150,
			'height' => 150,
			'weight' => 2000,
		),
		'square-parcel'          => array(
			'length' => 300,
			'width'  => 300,
			'height' => 300,
			'weight' => 2000,
		),
		'parcel'                 => array(
			'length' => 450,
			'width'  => 225,
			'height' => 225,
			'weight' => 2000,
		),
		Packaging::MEDIUM_PARCEL => array(
			'length' => 610,
			'width'  => 460,
			'height' => 460,
			'weight' => 20000,
		),
	);

	/**
	 * Additional rates for printed papers.
	 *
	 * @var array
	 */
	protected array $additional_rates;

	/**
	 * Box packer library to use.
	 *
	 * @var string
	 */
	protected string $box_packer_library;

	/**
	 * Maximum compensation value a customer can claim, in pounds.
	 *
	 * @var float
	 */
	private float $compensation_up_to_value = 0;

	/**
	 * Maximum inclusive compensation threshold before extra fees apply, in pounds.
	 *
	 * @var float
	 */
	private float $maximum_inclusive_compensation = 0;

	/**
	 * Maximum total cover available for a service, in pounds.
	 *
	 * @var float
	 */
	private float $maximum_total_cover = 0;

	/**
	 * Compensation value included by default with the service, in pounds.
	 *
	 * @var float
	 */
	private float $compensation_included_value = 0;

	/**
	 * Additional compensation fees, in pence.
	 *
	 * @var int
	 */
	private int $compensation_fees = 0;

	/**
	 * Whether the service rate includes tax.
	 *
	 * @var bool
	 */
	protected bool $is_taxed = false;

	/**
	 * List of country codes supported by the service.
	 *
	 * @var array
	 */
	protected array $supported_countries = array();

	/**
	 * Class constructor.
	 *
	 * @param string $rate_type          Rate type.
	 * @param string $box_packer_library Box packer library.
	 */
	public function __construct( string $rate_type, string $box_packer_library ) {
		$this->rate_id            = $this->get_rate_id();
		$this->rate_type          = $rate_type;
		$this->box_packer_library = $box_packer_library;

		// Load JSON rate loader and data manager if not already loaded.
		$this->load_json_dependencies();

		$this->setup();
	}

	/**
	 * Set up the rate.
	 */
	public function setup() {
		$json_data = $this->get_json_rate_data();

		$this->bands = ( false !== $json_data )
			? Rate_Data_Manager::transform_to_bands( $json_data )
			: array();

		if ( false !== $json_data && isset( $json_data['boxes'] ) ) {
			$this->boxes = $json_data['boxes'];
		}

		$this->load_service_limits_from_json( $json_data );
	}

	/**
	 * Name of the rate (e.g. 'special_delivery_1pm').
	 *
	 * @return string
	 */
	public function get_rate_id(): string {
		$rate_slug = $this->get_rate_slug();

		return str_replace( '-', '_', $rate_slug );
	}

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	abstract public function get_rate_slug(): string;

	/**
	 * Get this rates pricing Bands.
	 *
	 * @param mixed $band Coverage or compensation for loss / damage, or size.
	 *                    If not specified all prices are returned.
	 *
	 * @return array Returns false if no rate data available
	 */
	public function get_rate_bands( $band = false ): array {
		/**
		 * Allow third party to modify the boxes.
		 *
		 * @param array $bands Current bands.
		 *
		 * @since 2.2.2
		 */
		$bands = apply_filters( 'woocommerce_shipping_royalmail_' . $this->rate_id . '_rate_bands', $this->bands );

		if ( $band ) {
			return $bands[ $band ] ?? array();
		} else {
			return is_array( $bands ) ? $bands : array();
		}
	}

	/**
	 * Apply additional rates for any package type if needed.
	 */
	public function apply_additional_rates() {
		if ( empty( $this->additional_rates ) ) {
			return;
		}

		// Check if bands are already loaded.
		if ( empty( $this->bands ) ) {
			return;
		}

		// Apply additional rates for each package type.
		foreach ( $this->additional_rates as $package_type => $zone_rates ) {

			// $zone_rates can be either an array of per-zone rates or a single integer applied to all zones.
			if ( ! is_array( $zone_rates ) && ! is_numeric( $zone_rates ) ) {
				continue;
			}

			$this->apply_additional_rates_to_package_type( $package_type, $zone_rates );
		}
	}

	/**
	 * Apply additional rates to a specific package type across all zones.
	 *
	 * @param string    $package_type Package type key (e.g., 'printed-papers').
	 * @param array|int $zone_rates   Either an associative array of zone => rate or a single integer rate to apply to all zones.
	 *
	 * @return void
	 */
	private function apply_additional_rates_to_package_type( string $package_type, $zone_rates ) {

		// Normalize $zone_rates to a per-zone array.
		$normalized_rates = array();

		if ( is_array( $zone_rates ) ) {
			$normalized_rates = $zone_rates;
		} elseif ( is_numeric( $zone_rates ) ) {

			// Single rate applies to all zones that have this package type.
			foreach ( $this->bands as $zone => $packages ) {
				if ( isset( $packages[ $package_type ] ) ) {
					$normalized_rates[ $zone ] = (int) $zone_rates;
				}
			}
		} else {
			return; // Unsupported data type, do nothing.
		}

		// Loop through all zones that have this package type.
		foreach ( $this->bands as $zone => $packages ) {
			if ( ! isset( $packages[ $package_type ] ) ) {
				continue;
			}

			// Get additional rate for this zone.
			$additional_rate = isset( $normalized_rates[ $zone ] ) ? (int) $normalized_rates[ $zone ] : 0;

			if ( 0 === $additional_rate ) {
				continue;
			}

			// Each additional 250g (or part thereof) up to 5kg.
			// The chain anchors on the 2000g band — if absent, log a warning.
			if ( ! isset( $this->bands[ $zone ][ $package_type ][2000] ) ) {
				JSON_Rate_Loader::log(
					sprintf(
						'WooCommerce Royal Mail: Zone "%s" package type "%s" has no 2000g band — additional rates cannot be applied.',
						$zone,
						$package_type
					)
				);
				continue;
			}

			for ( $weight = 2250; $weight <= 5000; $weight += 250 ) {
				$previous_weight = $weight - 250;

				if ( isset( $this->bands[ $zone ][ $package_type ][ $previous_weight ] ) ) {
					$previous_rate                                    = $this->bands[ $zone ][ $package_type ][ $previous_weight ];
					$this->bands[ $zone ][ $package_type ][ $weight ] = $previous_rate + $additional_rate;
				}
			}
		}
	}

	/**
	 * Get this rates boxes.
	 *
	 * @return array
	 */
	public function get_rate_boxes(): array {
		/**
		 * Allow third party to modify the boxes.
		 *
		 * @param array $boxes Boxes.
		 *
		 * @since 2.2.2
		 */
		return apply_filters( 'woocommerce_shipping_royalmail_' . $this->rate_id . '_rate_boxes', $this->boxes );
	}

	/**
	 * Get all European countries. Combination of Europe zone 1, 2, and 3.
	 *
	 * @return array
	 */
	public function get_all_european_countries(): array {
		return array_merge( $this->europe_zone_1, $this->europe_zone_2, $this->europe_zone_3 );
	}

	/**
	 * Get the zone for the package.
	 *
	 * @param string $country_code $country_code Two-letter country code.
	 *
	 * @return string Zone.
	 */
	public function get_zone( string $country_code ): string {
		if ( 'GB' === $country_code ) {
			return Shipping_Zones::ZONE_UK;
		} elseif ( in_array( $country_code, $this->europe_zone_1, true ) ) {
			return Shipping_Zones::ZONE_EUR_1;
		} elseif ( in_array( $country_code, $this->europe_zone_2, true ) ) {
			return Shipping_Zones::ZONE_EUR_2;
		} elseif ( in_array( $country_code, $this->europe_zone_3, true ) ) {
			return Shipping_Zones::ZONE_EUR_3;
		} elseif ( in_array( $country_code, WC()->countries->get_european_union_countries(), true ) ) {
			return Shipping_Zones::ZONE_EU;
		} elseif ( in_array( $country_code, $this->world_zone_2, true ) ) {
			return Shipping_Zones::ZONE_2;
		} elseif ( in_array( $country_code, $this->world_zone_3, true ) ) {
			return Shipping_Zones::ZONE_3;
		} else {
			return Shipping_Zones::ZONE_1;
		}
	}

	/**
	 * See if box could be a letter.
	 *
	 * @param  object $box Box.
	 * @return bool
	 */
	public function box_is_letter( object $box ): bool {
		if ( $box->get_weight() > self::MAX_LETTER_WEIGHT ) {
			return false;
		}
		if ( $box->get_length() > 240 ) {
			return false;
		}
		if ( $box->get_width() > 165 ) {
			return false;
		}
		if ( $box->get_height() > 5 ) {
			return false;
		}
		return true;
	}

	/**
	 * See if box could be a letter.
	 *
	 * @param  object $box Box.
	 * @return bool
	 */
	public function box_is_large_letter( object $box ): bool {
		if ( $box->get_weight() > self::MAX_LARGE_LETTER_WEIGHT ) {
			return false;
		}
		if ( $box->get_length() > 353 ) {
			return false;
		}
		if ( $box->get_width() > 250 ) {
			return false;
		}
		if ( $box->get_height() > 25 ) {
			return false;
		}
		return true;
	}

	/**
	 * Pack items into boxes and return results.
	 *
	 * @param array  $items Items to pack.
	 * @param string $method Method to pack items (e.g. 'Pack items individually').
	 * @param string $country_code The two-letter country code of the destination.
	 * @param bool   $printed_papers If this is for Printed Papers rates.
	 * @param bool   $books If this is for Printed Papers rates only for books.
	 * @param bool   $tube If this is for tube rates.
	 *
	 * @return array Packed items.
	 * @since 1.0.0
	 * @version 2.5.3
	 */
	public function get_packages( array $items, string $method, string $country_code = '', bool $printed_papers = false, bool $books = false, bool $tube = false ): array {
		$packages  = array();
		$boxpacker = $this->get_boxpack( $country_code, $printed_papers, $books, $tube );

		if ( empty( $items ) ) {
			return $packages;
		}

		if ( 'per_item' === $method ) {
			$packages = $this->get_packages_using_per_item_method( $items, $boxpacker, $method );
		} else {
			$packages = $this->get_packages_using_box_packing_method( $items, $boxpacker, $method );
		}

		/**
		 * Filter the packages array before rates are calculated.
		 *
		 * @param array          $packages     Array of packed packages.
		 * @param array          $items        The items being shipped.
		 * @param string         $method       The packing method in use.
		 * @param string         $country_code The destination country code.
		 * @param RoyalMail_Rate $rate         The rate instance.
		 *
		 * @since 4.0.5
		 */
		$packages = apply_filters( 'woocommerce_shipping_royalmail_packages', $packages, $items, $method, $country_code, $this );

		return $packages;
	}

	/**
	 * Get box packer instance populated with defined boxes.
	 *
	 * @param string $country_code The two-letter country code of the destination.
	 * @param bool   $printed_papers If this is for Printed Papers rates.
	 * @param bool   $books If this is for Printed Papers rates only for books.
	 * @param bool   $tube If this is for tube rates.
	 *
	 * @return BoxPacker\Abstract_Packer Box packer.
	 * @since 2.5.3
	 * @version 3.5.0
	 */
	protected function get_boxpack( string $country_code = '', bool $printed_papers = false, bool $books = false, bool $tube = false ): BoxPacker\Abstract_Packer {
		$boxpack = ( new WC_Boxpack( 'mm', 'g', $this->box_packer_library ) )->get_packer();

		// Define boxes.
		foreach ( $this->get_rate_boxes() as $box_id => $box ) {

			// Medium parcel does not apply for printed papers.
			if ( true === $printed_papers && Packaging::MEDIUM_PARCEL === $box_id ) {
				continue;
			}

			if ( true !== $tube && Packaging::TUBE === $box_id ) {
				continue;
			}

			$newbox = $boxpack->add_box(
				$box['length'],
				$box['width'],
				$box['height'],
				isset( $box['box_weight'] ) ? $box['box_weight'] : 0
			);

			if ( is_numeric( $box_id ) && $this->box_is_letter( $newbox ) ) {
				$box_id = Packaging::LETTER;
				$newbox->set_type( 'envelope' );
			} elseif ( is_numeric( $box_id ) && $this->box_is_large_letter( $newbox ) ) {
				$box_id = Packaging::LARGE_LETTER;
				$newbox->set_type( 'envelope' );
			} elseif ( strstr( $box_id, Packaging::PACKET ) || strstr( $box_id, 'parcel' ) ) {
				$newbox->set_type( Packaging::PACKET );
			} elseif ( strstr( $box_id, Packaging::TUBE ) ) {
				$newbox->set_type( Packaging::TUBE );
			}

			$newbox->set_id( $box_id );

			if ( ! empty( $box['weight'] ) ) {
				$newbox->set_max_weight( $box['weight'] );
			}

			// Printed Papers max weight adjustments.
			if ( $printed_papers ) {
				if ( in_array( $country_code, $this->printed_papers_2kg_limited_countries, true ) ) {
					$newbox->set_max_weight( 2000 );
				} elseif ( 'IE' === $country_code ) {
					$max_weight = ( $books ) ? 5000 : 2000;
					$newbox->set_max_weight( $max_weight );
				} else {
					$newbox->set_max_weight( 5000 );
				}
			}
		}

		return $boxpack;
	}

	/**
	 * Get packages using per item method.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array                     $items   Items to pack.
	 * @param BoxPacker\Abstract_Packer $boxpack Box packer instance.
	 * @param string                    $method  Method to pack items (e.g. 'per_item').
	 *
	 * @return array Packages.
	 */
	protected function get_packages_using_per_item_method( array $items, BoxPacker\Abstract_Packer $boxpack, string $method = '' ): array {
		$packages = array();
		foreach ( $items as $item ) {
			$boxpack->clear_items();

			/**
			 * Allow skipping individual products from box packing.
			 *
			 * @param bool           $skip    Whether to skip this item. Default false.
			 * @param array          $item    The item array being considered for packing.
			 * @param string         $method  The packing method in use.
			 * @param string         $rate_id The rate ID being calculated.
			 * @param RoyalMail_Rate $rate    The rate instance.
			 *
			 * @since 4.0.5
			 */
			if ( apply_filters( 'woocommerce_shipping_royalmail_skip_item_box_packing', false, $item, $method, $this->rate_id, $this ) ) {
				continue;
			}

			$boxpack->add_item(
				$item->length,
				$item->width,
				$item->height,
				$item->weight,
				$item->value
			);

			$boxpack->pack();
			$item_packages = $boxpack->get_packages();

			for ( $i = 0; $i < $item->qty; $i++ ) {
				$packages = array_merge( $packages, $item_packages );
			}
		}

		return $packages;
	}

	/**
	 * Get packages using box packing method.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array                     $items   Items to pack.
	 * @param BoxPacker\Abstract_Packer $boxpack Box packer instance.
	 * @param string                    $method  Method to pack items (e.g. 'Pack items individually').
	 *
	 * @return array Packages.
	 */
	protected function get_packages_using_box_packing_method( array $items, BoxPacker\Abstract_Packer $boxpack, string $method = '' ): array {
		foreach ( $items as $item ) {

			/**
			 * Allow skipping individual products from box packing.
			 *
			 * @param bool           $skip    Whether to skip this item. Default false.
			 * @param array          $item    The item array being considered for packing.
			 * @param string         $method  The packing method in use.
			 * @param string         $rate_id The rate ID being calculated.
			 * @param RoyalMail_Rate $rate    The rate instance.
			 *
			 * @since 4.0.5
			 */
			if ( apply_filters( 'woocommerce_shipping_royalmail_skip_item_box_packing', false, $item, $method, $this->rate_id, $this ) ) {
				continue;
			}

			$boxpack->add_item(
				$item->length,
				$item->width,
				$item->height,
				$item->weight,
				$item->value,
				array(),
				$item->qty
			);
		}

		// Pack it.
		$boxpack->pack();

		return $boxpack->get_packages();
	}

	/**
	 * Gets the instance options.
	 *
	 * @since 2.5.7
	 * @param  int $instance_id The ID of the shipping instance.
	 * @return array $option_value
	 */
	public function get_instance_options( int $instance_id = 0 ): array {
		if ( empty( $instance_id ) ) {
			return array();
		}

		return get_option( 'woocommerce_royal_mail_' . $instance_id . '_settings', array() );
	}

	/**
	 * Validates package id and weights
	 *
	 * @param Package $package Package object.
	 */
	protected function validate_package( Package $package ) {

		if ( Packaging::LETTER !== $package->id && Packaging::LARGE_LETTER !== $package->id && Packaging::PRINTED_PAPERS !== $package->id && Packaging::MEDIUM_PARCEL !== $package->id ) {
			$package->id = Packaging::PACKET;
		}

		if ( Packaging::LARGE_LETTER === $package->id && $package->weight > self::MAX_LARGE_LETTER_WEIGHT ) {
			$package->id = Packaging::PACKET;
		}

		if ( Packaging::LETTER === $package->id && $package->weight > self::MAX_LETTER_WEIGHT ) {
			$package->id = Packaging::PACKET;
		}
	}

	/**
	 * Separate all qualifying Printed Papers items, get them packed
	 * according to type and weight, and return the packages for
	 * quoting.
	 *
	 * @param array  $items $items Items to pack.
	 * @param string $country_code Two-letter country code.
	 * @param string $packing_method Method to pack items (e.g. 'Pack items individually').
	 *
	 * @return array Printed Papers packages.
	 */
	protected function get_printed_papers_packages( array &$items, string $country_code, string $packing_method ): array {

		/**
		 * Separate printed papers and standard items
		 * before sending to the box packer.
		 */
		$printed_papers = $this->get_qualifying_printed_papers_items( $items, $country_code );

		/**
		 * If there are printed papers items and the destination is
		 * the Republic of Ireland, separate the books from the rest
		 * of the Printed Papers items.
		 */
		$printed_paper_books_packages = array();
		if ( ! empty( $printed_papers ) && 'IE' === $country_code ) {
			$printed_papers_books         = $this->get_printed_papers_books( $printed_papers );
			$printed_paper_books_packages = $this->get_packages( $printed_papers_books, $packing_method, $country_code, true, true );
		}

		// Get Printed Papers packages.
		$printed_paper_packages = $this->get_packages( $printed_papers, $packing_method, $country_code, true );

		/**
		 * If books were separated, combine them now with the rest
		 * of the printed papers items.
		 */
		if ( ! empty( $printed_paper_books_packages ) ) {
			$printed_paper_packages = array_merge( $printed_paper_packages, $printed_paper_books_packages );
		}

		// Set each printed papers package ID to 'printed-papers'.
		foreach ( $printed_paper_packages as $package ) {
			$package->id = Packaging::PRINTED_PAPERS;
		}

		return $printed_paper_packages;
	}

	/**
	 * Separate all qualifying Tube items, get them packed
	 * according to type and weight, and return the packages for
	 * quoting.
	 *
	 * @param array  $items $items Items to pack.
	 * @param string $country_code Two-letter country code.
	 * @param string $packing_method Method to pack items (e.g. 'Pack items individually').
	 *
	 * @return array Tube packages.
	 */
	protected function get_tube_packages( array &$items, string $country_code, string $packing_method ): array {

		/**
		 * Separate tubes and standard items
		 * before sending to the box packer.
		 */
		$tubes = $this->get_qualifying_tube_items( $items );

		if ( empty( $tubes ) ) {
			return array();
		}

		$tubes_packages = $this->get_packages( $tubes, $packing_method, $country_code, false, false, true );

		// Set each tube package ID to 'tube'.
		foreach ( $tubes_packages as $package ) {
			$package->id = Packaging::TUBE;
		}

		return $tubes_packages;
	}

	/**
	 * Find and separate printed papers from the standard items
	 * so we can handle differently.
	 *
	 * Any qualifying Printed Papers items will be returned in an
	 * array and will also be unset from the $items array that is
	 * passed by reference.
	 *
	 * @see https://www.royalmail.com/price-finder/printed-papers
	 *
	 * @param array  $items Items to pack.
	 * @param string $country_code Two-letter country code.
	 *
	 * @return array Printed Papers items.
	 */
	protected function get_qualifying_printed_papers_items( array &$items, string $country_code ): array {
		$printed_papers = array();
		foreach ( $items as $key => $item ) {
			if ( $item->printed_papers ) {
				// Items over 5kg don't qualify for Printed Papers.
				if ( $item->weight > 5000 ) {
					continue;
				}

				/**
				 * If the destination country limits Printed Papers to 2kg max
				 * and this item is over 2kg, don't ship it with Printed Papers
				 * rates.
				 */
				if ( $item->weight > 2000 && in_array( $country_code, $this->printed_papers_2kg_limited_countries, true ) ) {
					continue;
				}

				/**
				 * Only books can be over 2kg and be shipped with the Printed Papers
				 * service to the Republic of Ireland. Other bound papers/pamphlets
				 * cannot exceed 2kg or they don't qualify for Printed Papers.
				 */
				if ( $item->weight > 2000 && ! $item->book && 'IE' === $country_code ) {
					continue;
				}

				$printed_papers[] = $item;
				unset( $items[ $key ] );
			}
		}

		return $printed_papers;
	}

	/**
	 * Find and separate printed papers books from other
	 * printed papers items for separate calculation if necessary.
	 * Example: Republic of Ireland only allows books to be over
	 * 2kg and up to 5kg. Other printed papers items must be 2kg
	 * or less. So we need to split books from other printed papers
	 * items for separate handling/rules in that case.
	 *
	 * Any qualifying Printed Papers book items will be returned in an
	 * array and will also be unset from the $items array that is
	 * passed by reference.
	 *
	 * @param array $printed_papers_items Printed Papers items.
	 *
	 * @return array Printed Papers books.
	 */
	protected function get_printed_papers_books( array &$printed_papers_items ): array {
		$books = array();
		foreach ( $printed_papers_items as $key => $item ) {
			if ( $item->book ) {

				$books[] = $item;
				unset( $printed_papers_items[ $key ] );
			}
		}

		return $books;
	}

	/**
	 * Find and separate tubes from the standard items
	 * so we can handle differently.
	 *
	 * Any qualifying Tube items will be returned in an
	 * array and will also be unset from the $items array that is
	 * passed by reference.
	 *
	 * @param array $items Items to pack.
	 *
	 * @return array Tube items.
	 */
	protected function get_qualifying_tube_items( array &$items ): array {
		$tubes = array();
		foreach ( $items as $key => $item ) {
			if ( ! $item->tube ) {
				continue;
			}

			// Item's width and height should be the same.
			if ( $item->width !== $item->height ) {
				continue;
			}

			// Tube item's length should not exceed 90cm.
			if ( $item->length >= 900 ) {
				continue;
			}

			// Item's length and 2xdiameter should not exceed 104cm.
			if ( ( $item->length + $item->width + $item->height ) >= 1040 ) {
				continue;
			}

			$tubes[] = $item;
			unset( $items[ $key ] );
		}

		return $tubes;
	}

	/**
	 * Load JSON dependencies
	 */
	private function load_json_dependencies() {
		if ( ! class_exists( 'WooCommerce\RoyalMail\JSON_Rate_Loader' ) ) {
			include_once plugin_dir_path( __FILE__ ) . '../class-json-rate-loader.php';
		}

		if ( ! class_exists( 'WooCommerce\RoyalMail\Rate_Data_Manager' ) ) {
			include_once plugin_dir_path( __FILE__ ) . '../class-rate-data-manager.php';
		}
	}

	/**
	 * Get JSON rate data for this service
	 *
	 * @return array|false JSON rate data or false on failure.
	 */
	protected function get_json_rate_data() {
		$service_slug = $this->get_rate_slug();
		$rate_type    = $this->rate_type;

		return JSON_Rate_Loader::load_rate_data( $service_slug, $rate_type );
	}

	/**
	 * Load service limits from JSON data.
	 *
	 * @param array|false $json_data JSON rate data.
	 */
	protected function load_service_limits_from_json( $json_data ) {
		if ( false === $json_data ) {
			return;
		}

		$this->compensation_up_to_value    = Rate_Data_Manager::get_compensation_up_to_value( $json_data ) / 100;
		$this->compensation_included_value = Rate_Data_Manager::get_compensation_included_value( $json_data ) / 100;
		// Intentionally NOT divided by 100 — fees are added directly to a pence-denominated price total.
		$this->compensation_fees = Rate_Data_Manager::get_compensation_fees( $json_data );

		$this->maximum_inclusive_compensation = Rate_Data_Manager::get_maximum_inclusive_compensation( $json_data ) / 100;
		$this->maximum_total_cover            = Rate_Data_Manager::get_maximum_total_cover( $json_data ) / 100;

		$this->supported_countries = Rate_Data_Manager::get_supported_countries( $json_data );
		$this->is_taxed            = Rate_Data_Manager::is_taxed( $json_data );

		$this->additional_rates = Rate_Data_Manager::get_additional_rates( $json_data );
	}

	/**
	 * Get compensation up to value
	 *
	 * @return float Compensation up to value in pounds.
	 */
	protected function get_compensation_up_to_value(): float {
		return $this->compensation_up_to_value;
	}

	/**
	 * Get compensation included value
	 *
	 * @return float Compensation included value in pounds.
	 */
	protected function get_compensation_included_value(): float {
		return $this->compensation_included_value;
	}

	/**
	 * Get supported countries.
	 *
	 * @return array Supported countries.
	 */
	protected function get_supported_countries(): array {
		return $this->supported_countries;
	}

	/**
	 * Get additional compensation fees
	 *
	 * @return int Additional compensation fees in pence.
	 */
	protected function get_compensation_fees(): int {
		return $this->compensation_fees;
	}

	/**
	 * Get maximum total cover.
	 *
	 * @return float Maximum total cover value in pounds.
	 */
	protected function get_maximum_total_cover(): float {
		return $this->maximum_total_cover;
	}

	/**
	 * Get maximum inclusive compensation.
	 *
	 * @return float Maximum inclusive compensation value in pounds.
	 */
	protected function get_maximum_inclusive_compensation(): float {
		return $this->maximum_inclusive_compensation;
	}

	/**
	 * Get zone bands.
	 *
	 * @param string $country_code Destination country code.
	 *
	 * @return mixed
	 */
	public function get_zone_bands( string $country_code ) {
		$bands = $this->get_rate_bands();

		if ( isset( $bands[ $country_code ] ) ) {
			$zone = $country_code;
		} else {
			$zone = $this->get_zone( $country_code );
		}

		return $bands[ $zone ] ?? array();
	}
}
