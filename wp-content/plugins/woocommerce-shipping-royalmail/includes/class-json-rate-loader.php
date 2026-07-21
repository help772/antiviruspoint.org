<?php
/**
 * JSON Rate Loader Service.
 *
 * @since 4.0.0
 * @package WC_Shipping_Royalmail
 */

namespace WooCommerce\RoyalMail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JSON Rate Loader class.
 *
 * Handles loading and caching of Royal Mail rate data from local JSON files.
 * Provides methods to fetch, validate, and cache rate information for different
 * Royal Mail services and rate types.
 */
class JSON_Rate_Loader {
	/**
	 * Local path for rate files directory relative to plugin root.
	 *
	 * @var string
	 */
	const LOCAL_RATES_PATH = 'rate-files-json/';

	/**
	 * Local rate paths configuration for different rate types.
	 * Populated dynamically from local configuration file.
	 *
	 * @var array
	 */
	private static array $local_paths = array();

	/**
	 * Current rate date being used.
	 *
	 * @var string
	 */
	private static string $current_rate_date = '';

	/**
	 * Database table name for storing rates.
	 *
	 * @var string
	 */
	public const TABLE_NAME = 'wc_royalmail_rates';

	/**
	 * Logger instance, initialized on first use.
	 *
	 * @var \WC_Logger_Interface|null
	 */
	private static ?\WC_Logger_Interface $logger = null;

	/**
	 * Cache populated by preload_rates(); maps service_code => array|false.
	 * false  = confirmed not in DB for current date; use file fallback.
	 * absent = never looked up via preload; individual query may still run.
	 *
	 * @var array<string, array|false>
	 */
	private static array $bulk_loaded_rates = array();

	/**
	 * Cache for get_all_rate_dates(); null means unpopulated.
	 *
	 * @var string[]|null
	 */
	private static ?array $rate_dates_cache = null;

	/**
	 * Log a message at the given level, always tagged with the plugin source.
	 *
	 * @param string $message Log message.
	 * @param string $level   Log level ('error', 'warning', etc.).
	 * @param array  $context Additional context merged into the log context array.
	 */
	public static function log( string $message, string $level = 'error', array $context = array() ): void {
		if ( null === self::$logger ) {
			self::$logger = \wc_get_logger();
		}
		self::$logger->{$level}( $message, array_merge( array( 'source' => 'wc-royalmail' ), $context ) );
	}

	/**
	 * Get plugin rates directory path.
	 *
	 * @return string Full path to the rates directory.
	 */
	private static function get_plugin_rates_path(): string {
		return plugin_dir_path( WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE ) . self::LOCAL_RATES_PATH;
	}

	/**
	 * Scan the rates directory and return all valid date directories (YYYY-MM-DD format).
	 *
	 * @return string[]|false All date directory names, unsorted, or false if the directory is unreadable.
	 */
	private static function get_all_rate_dates() {
		if ( null !== self::$rate_dates_cache ) {
			return self::$rate_dates_cache;
		}

		$rates_path  = self::get_plugin_rates_path();
		$directories = is_dir( $rates_path ) ? scandir( $rates_path ) : false;

		if ( false === $directories ) {
			return false;
		}

		$dates = array();
		foreach ( $directories as $dir ) {
			if ( '.' === $dir || '..' === $dir ) {
				continue;
			}
			if ( is_dir( $rates_path . $dir ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $dir ) ) {
				$dates[] = $dir;
			}
		}

		self::$rate_dates_cache = $dates;
		return self::$rate_dates_cache;
	}

	/**
	 * Get current rate date by scanning available directories.
	 *
	 * Scans the rate-files-json directory for date folders and returns
	 * the most recent date that is not in the future.
	 *
	 * @return string|false Current rate date (YYYY-MM-DD) or false if none found.
	 */
	private static function get_current_rate_date() {
		$all_dates    = self::get_all_rate_dates();
		$current_date = gmdate( 'Y-m-d' );

		if ( false === $all_dates ) {
			return false;
		}

		$available_dates = array();
		foreach ( $all_dates as $date ) {
			if ( $date <= $current_date ) {
				$available_dates[] = $date;
			}
		}

		if ( empty( $available_dates ) ) {
			return false;
		}

		rsort( $available_dates );
		return $available_dates[0];
	}

	/**
	 * Get upcoming rate dates by scanning available directories.
	 *
	 * Returns all date directories that are in the future (scheduled but not yet active).
	 *
	 * @return string[] Upcoming rate dates (YYYY-MM-DD), sorted ascending.
	 */
	public static function get_upcoming_rate_dates(): array {
		$all_dates    = self::get_all_rate_dates();
		$current_date = gmdate( 'Y-m-d' );

		if ( false === $all_dates ) {
			return array();
		}

		$upcoming_dates = array();
		foreach ( $all_dates as $date ) {
			if ( $date > $current_date ) {
				$upcoming_dates[] = $date;
			}
		}

		sort( $upcoming_dates );
		return $upcoming_dates;
	}

	/**
	 * Initialize local rate paths by scanning directories.
	 *
	 * @return bool True if paths loaded successfully, false otherwise.
	 */
	private static function init_local_paths(): bool {
		if ( ! empty( self::$local_paths ) ) {
			return true;
		}

		$current_rate_date = self::get_current_rate_date();
		if ( false === $current_rate_date ) {
			return false;
		}

		// Verify that the rate directories exist.
		$base_path = self::get_plugin_rates_path() . $current_rate_date . '/';
		if ( ! is_dir( $base_path ) ) {
			return false;
		}

		// Check that both online and regular directories exist.
		$online_path  = $base_path . 'online/';
		$regular_path = $base_path . 'regular/';

		if ( ! is_dir( $online_path ) || ! is_dir( $regular_path ) ) {
			return false;
		}

		// Set up local paths structure.
		self::$local_paths       = array(
			'online'  => $online_path,
			'regular' => $regular_path,
		);
		self::$current_rate_date = $current_rate_date;

		return true;
	}

	/**
	 * Load rate data for a specific service.
	 *
	 * Attempts to load rate data from database first, then from local files if not available.
	 * Saves successful file reads to database for future use.
	 *
	 * @param string $service_slug Service slug (e.g., 'international-economy').
	 * @param string $rate_type    Rate type ('online' or 'regular').
	 *
	 * @return array|false Rate data array or false on failure.
	 */
	public static function load_rate_data( string $service_slug, string $rate_type ) {
		// Sanitize and validate input parameters.
		$service_slug = sanitize_key( $service_slug );
		$rate_type    = sanitize_key( $rate_type );

		// Create service code by combining service slug and rate type.
		$service_code = $service_slug . '_' . $rate_type;

		// Try to get rate data from database first.
		$rate_data = self::get_rate_from_database( $service_code );

		if ( false !== $rate_data ) {
			return $rate_data;
		}

		// If not in database, fetch from local file.
		$rate_data = self::fetch_from_local_file( $service_slug, $rate_type );

		if ( false !== $rate_data ) {
			// Save to database for future use.
			self::save_rate_to_database( $service_code, $rate_data, self::$current_rate_date );

			// Update the bulk-loaded cache so subsequent calls in the same request use the cached value.
			self::$bulk_loaded_rates[ $service_code ] = $rate_data;
		} else {
			self::log( sprintf( 'WooCommerce Royal Mail: Could not load rate data for service "%s" (type: %s).', $service_slug, $rate_type ) );
		}

		return $rate_data;
	}

	/**
	 * Fetch rate data from local file.
	 *
	 * Reads rate data from the local JSON file for the specified service and rate type.
	 *
	 * @param string $service_slug Service slug.
	 * @param string $rate_type    Rate type.
	 *
	 * @return array|false Rate data or false on failure.
	 */
	private static function fetch_from_local_file( string $service_slug, string $rate_type ) {
		if ( ! self::init_local_paths() ) {
			return false;
		}

		// Construct the file path.
		$file_path = self::get_plugin_rates_path() .
					self::$current_rate_date . '/' .
					$rate_type . '/' .
					$service_slug . '.json';

		// Check if file exists.
		if ( ! file_exists( $file_path ) ) {
			self::log( sprintf( 'WooCommerce Royal Mail: Rate file not found: %s', $file_path ), 'warning' );
			return false;
		}

		// Read file contents.
		$content = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $content ) {
			return false;
		}

		// Decode JSON data.
		$data = json_decode( $content, true );
		if ( ! is_array( $data ) ) {
			return false;
		}

		return self::validate_rate_data( $data );
	}

	/**
	 * Validate rate data structure.
	 *
	 * Ensures the rate data contains required fields and at least one
	 * valid rate structure (packages, zones, or compensation levels).
	 *
	 * @param array $data Rate data to validate.
	 *
	 * @return array|false Validated data or false on failure.
	 */
	private static function validate_rate_data( array $data ) {
		// Validate that we have at least one rate structure.
		$has_rate_data = false;
		if ( ! empty( $data['packages'] ) && is_array( $data['packages'] ) ) {
			$has_rate_data = true;
		} elseif ( ! empty( $data['zones'] ) && is_array( $data['zones'] ) ) {
			$has_rate_data = true;
		} elseif ( ! empty( $data['compensation'] ) && is_array( $data['compensation'] ) ) {
			$has_rate_data = true;
		}

		if ( ! $has_rate_data ) {
			return false;
		}

		return $data;
	}

	/**
	 * Bulk-load rate data for multiple services in a single DB query.
	 *
	 * Call this before iterating over services to reduce N queries to 1.
	 * Results are cached in $bulk_loaded_rates; individual load_rate_data()
	 * calls for preloaded codes will hit the cache without touching the DB.
	 *
	 * @param string[] $service_codes Service codes, e.g. ['first-class_online'].
	 */
	public static function preload_rates( array $service_codes ): void {
		if ( empty( $service_codes ) ) {
			return;
		}

		// Skip codes already in cache.
		$uncached = array_values(
			array_filter(
				$service_codes,
				fn( $code ) => ! array_key_exists( $code, self::$bulk_loaded_rates )
			)
		);

		if ( empty( $uncached ) ) {
			return;
		}

		// If no effective date can be determined, seed every uncached code as absent so
		// load_rate_data() falls through to the file path (which will also fail cleanly).
		$current_rate_date = self::get_current_rate_date();
		if ( false === $current_rate_date ) {
			foreach ( $uncached as $code ) {
				self::$bulk_loaded_rates[ $code ] = false;
			}
			return;
		}

		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		// Build IN-list placeholders safe for $wpdb->prepare().
		$placeholders = implode( ', ', array_fill( 0, count( $uncached ), '%s' ) );
		$args         = array_merge( $uncached, array( $current_rate_date ) );

		// Only rows from the current effective cycle are eligible; older snapshots
		// (e.g. pre-4.0.x January rows left over) are ignored.
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT service_code, start_date, rates_json
				 FROM {$table_name}
				 WHERE service_code IN ({$placeholders})
				 AND start_date = %s",
				...$args
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		foreach ( (array) $rows as $row ) {
			$rate_data                                     = json_decode( $row->rates_json, true );
			self::$bulk_loaded_rates[ $row->service_code ] = is_array( $rate_data )
				? self::validate_rate_data( $rate_data )
				: false;
		}

		// Store false sentinel for codes absent from DB so load_rate_data()
		// skips the individual SELECT and goes straight to file fallback.
		foreach ( $uncached as $code ) {
			if ( ! array_key_exists( $code, self::$bulk_loaded_rates ) ) {
				self::$bulk_loaded_rates[ $code ] = false;
			}
		}
	}

	/**
	 * Get rate data from database for a specific service.
	 *
	 * @param string $service_code Service code (e.g., 'international-economy_regular').
	 *
	 * @return array|false Rate data array or false if not found.
	 */
	private static function get_rate_from_database( string $service_code ) {
		// Phase 1: bulk preload cache (array_key_exists detects stored `false`).
		if ( array_key_exists( $service_code, self::$bulk_loaded_rates ) ) {
			return self::$bulk_loaded_rates[ $service_code ];
		}

		// Phase 2: individual query - fallback.
		$current_rate_date = self::get_current_rate_date();
		if ( false === $current_rate_date ) {
			return false;
		}

		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$result = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT rates_json FROM {$table_name}
				WHERE service_code = %s
				AND start_date = %s
				LIMIT 1",
				$service_code,
				$current_rate_date
			)
		);

		if ( null === $result ) {
			return false;
		}

		$rate_data = json_decode( $result->rates_json, true );
		if ( ! is_array( $rate_data ) ) {
			return false;
		}

		return self::validate_rate_data( $rate_data );
	}

	/**
	 * Save rate data to database.
	 *
	 * @param string $service_code Service code (e.g., 'international-economy_regular').
	 * @param array  $rate_data    Rate data to save.
	 * @param string $start_date   Start date for the rates (defaults to current date).
	 *
	 * @return bool True on success, false on failure.
	 */
	private static function save_rate_to_database( string $service_code, array $rate_data, string $start_date = '' ): bool {
		global $wpdb;

		if ( empty( $start_date ) ) {
			$start_date = gmdate( 'Y-m-d' );
		}

		$table_name = $wpdb->prefix . self::TABLE_NAME;

		$rates_json = wp_json_encode( $rate_data );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->query(
			$wpdb->prepare(
				'INSERT INTO %i (service_code, start_date, rates_json)
				VALUES (%s, %s, %s)
				ON DUPLICATE KEY UPDATE rates_json = VALUES(rates_json)',
				$table_name,
				$service_code,
				$start_date,
				$rates_json
			)
		);

		if ( false === $result ) {
			self::log( sprintf( 'WooCommerce Royal Mail: Failed to save rate data for service "%s" to database.', $service_code ) );
		}

		return false !== $result;
	}

	/**
	 * Delete DB rate rows whose start_date no longer corresponds to a directory in the rate-files-json folder.
	 *
	 * Skips deletion entirely when $valid_dates is empty to prevent accidental data loss.
	 *
	 * @param string[] $valid_dates All date directory names currently on disk (YYYY-MM-DD).
	 * @return int|false Number of rows deleted, 0 if guard fired or nothing stale, false on DB failure.
	 */
	private static function delete_stale_db_rates( array $valid_dates ) {
		if ( empty( $valid_dates ) ) {
			return 0;
		}

		global $wpdb;

		$table_name   = $wpdb->prefix . self::TABLE_NAME;
		$placeholders = implode( ', ', array_fill( 0, count( $valid_dates ), '%s' ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
		$deleted = $wpdb->query(
			$wpdb->prepare(
				'DELETE FROM %i WHERE start_date NOT IN (' . $placeholders . ')',
				$table_name,
				...$valid_dates
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber

		if ( false === $deleted ) {
			return false;
		}

		return (int) $deleted;
	}

	/**
	 * Sync all available rates from local files to database.
	 *
	 * @return array Results array with success/failure counts.
	 */
	public static function sync_rates_to_database(): array {
		$results = array(
			'success' => 0,
			'failed'  => 0,
			'errors'  => array(),
			'deleted' => 0,
		);

		// Collect the current rate date and all upcoming future dates.
		$current_rate_date = self::get_current_rate_date();
		if ( false === $current_rate_date ) {
			++$results['failed'];
			$results['errors'][] = 'No valid rate directories found.';
			return $results;
		}

		$dates_to_sync = array_merge( array( $current_rate_date ), self::get_upcoming_rate_dates() );
		$rate_types    = array( 'regular', 'online' );

		foreach ( $dates_to_sync as $rate_date ) {
			foreach ( $rate_types as $rate_type ) {
				$dir_path   = self::get_plugin_rates_path() . $rate_date . '/' . $rate_type . '/';
				$json_files = is_dir( $dir_path ) ? ( glob( $dir_path . '*.json' ) ? glob( $dir_path . '*.json' ) : array() ) : array();

				foreach ( $json_files as $file_path ) {
					$service_slug = basename( $file_path, '.json' );
					$service_code = $service_slug . '_' . $rate_type;

					// Read and decode directly from the specific date path.
					$content   = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					$data      = is_string( $content ) ? json_decode( $content, true ) : false;
					$rate_data = is_array( $data ) ? self::validate_rate_data( $data ) : false;

					if ( false !== $rate_data ) {
						$saved = self::save_rate_to_database( $service_code, $rate_data, $rate_date );
						if ( $saved ) {
							++$results['success'];
						} else {
							++$results['failed'];
							$results['errors'][] = 'Failed to save ' . $service_code . ' to database for ' . $rate_date;
						}
					} else {
						++$results['failed'];
						$results['errors'][] = 'Failed to parse JSON data from ' . $service_slug . '.json (' . $rate_type . ') for ' . $rate_date;
					}
				}
			}
		}

		// Remove DB rows whose start_date no longer has a matching directory on rate-files-json.
		$all_disk_dates = self::get_all_rate_dates();
		$deleted        = self::delete_stale_db_rates( is_array( $all_disk_dates ) ? $all_disk_dates : array() );

		if ( false === $deleted ) {
			$results['errors'][] = 'Failed to delete stale rate entries from database.';
		} else {
			$results['deleted'] = $deleted;
		}

		return $results;
	}
}
